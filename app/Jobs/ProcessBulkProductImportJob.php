<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;

class ProcessBulkProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 hour
    public int $tries = 1;

    public function __construct(
        public ProductImport $import
    ) {}

    public function handle(): void
    {
        $this->import->markAsProcessing();

        try {
            $filePath = Storage::disk('public')->path($this->import->file_path);
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $options = $this->import->options;

            foreach ($csv->getRecords() as $index => $record) {
                $rowNumber = $index + 2;

                try {
                    $this->processRow($record, $options, $rowNumber);
                    $this->import->increment('success_rows');
                } catch (\Exception $e) {
                    $this->import->increment('failed_rows');
                    $errors = $this->import->errors ?? [];
                    $errors["row_{$rowNumber}"] = $e->getMessage();
                    $this->import->update(['errors' => $errors]);
                }

                $this->import->increment('processed_rows');
            }

            $this->import->markAsCompleted();

        } catch (\Exception $e) {
            $this->import->markAsFailed($e->getMessage());
        }
    }

    protected function processRow(array $record, array $options, int $rowNumber): void
    {
        // Get or validate name
        $name = trim($record['name'] ?? '');
        if (empty($name)) {
            throw new \Exception('Name is required');
        }

        $slug = !empty($record['slug']) ? Str::slug($record['slug']) : Str::slug($name);
        $price = floatval($record['price'] ?? 0);

        if ($price <= 0) {
            throw new \Exception('Valid price is required');
        }

        // Check for existing product
        $existingProduct = Product::where('slug', $slug)->first();

        if ($existingProduct) {
            if ($options['update_existing'] ?? false) {
                $this->updateProduct($existingProduct, $record, $options);
                return;
            } elseif ($options['skip_duplicates'] ?? true) {
                throw new \Exception('Product already exists (skipped)');
            }
            // Make slug unique
            $slug = $slug . '-' . uniqid();
        }

        // Resolve seller
        $sellerId = $options['default_seller_id'] ?? null;
        if (!empty($record['seller_email'])) {
            $user = User::where('email', $record['seller_email'])->first();
            if ($user && $user->seller) {
                $sellerId = $user->seller->id;
            }
        }

        if (!$sellerId) {
            throw new \Exception('No seller specified');
        }

        // Resolve category
        $categoryId = $options['default_category_id'] ?? null;
        if (!empty($record['category'])) {
            $category = Category::where('name', $record['category'])
                ->orWhere('slug', $record['category'])
                ->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Create product
        Product::create([
            'seller_id' => $sellerId,
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'short_description' => $record['short_description'] ?? null,
            'description' => $record['description'] ?? null,
            'price' => $price,
            'sale_price' => !empty($record['sale_price']) ? floatval($record['sale_price']) : null,
            'version' => $record['version'] ?? '1.0.0',
            'demo_url' => $record['demo_url'] ?? null,
            'preview_url' => $record['preview_url'] ?? null,
            'video_url' => $record['video_url'] ?? null,
            'software_compatibility' => !empty($record['software_compatibility'])
                ? array_map('trim', explode(',', $record['software_compatibility']))
                : [],
            'is_featured' => ($record['is_featured'] ?? '0') === '1',
            'status' => $options['default_status'] ?? 'draft',
        ]);
    }

    protected function updateProduct(Product $product, array $record, array $options): void
    {
        $data = [];

        if (!empty($record['short_description'])) $data['short_description'] = $record['short_description'];
        if (!empty($record['description'])) $data['description'] = $record['description'];
        if (!empty($record['price'])) $data['price'] = floatval($record['price']);
        if (!empty($record['sale_price'])) $data['sale_price'] = floatval($record['sale_price']);
        if (!empty($record['version'])) $data['version'] = $record['version'];
        if (!empty($record['demo_url'])) $data['demo_url'] = $record['demo_url'];
        if (!empty($record['preview_url'])) $data['preview_url'] = $record['preview_url'];

        if (!empty($data)) {
            $product->update($data);
        }
    }
}
