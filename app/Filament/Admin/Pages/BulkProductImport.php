<?php

namespace App\Filament\Admin\Pages;

use App\Jobs\ProcessBulkProductImportJob;
use App\Models\Category;
use App\Models\ProductImport;
use App\Models\Seller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class BulkProductImport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Bulk Import';
    protected static ?string $navigationGroup = 'Products';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.admin.pages.bulk-product-import';

    public ?array $data = [];
    public ?string $uploadedFile = null;
    public ?array $previewData = null;
    public ?array $validationErrors = null;
    public int $totalRows = 0;
    public int $validRows = 0;
    public int $errorRows = 0;
    public bool $isProcessing = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload CSV File')
                    ->description('Upload a CSV file containing product data. Download the template below for the correct format.')
                    ->schema([
                        Forms\Components\FileUpload::make('csv_file')
                            ->label('CSV File')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain'])
                            ->maxSize(10240) // 10MB
                            ->directory('imports')
                            ->visibility('private')
                            ->required()
                            ->helperText('Maximum file size: 10MB. Accepted formats: CSV'),

                        Forms\Components\Select::make('default_seller_id')
                            ->label('Default Seller')
                            ->options(Seller::whereHas('user')->with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->helperText('Assign products to this seller if not specified in CSV'),

                        Forms\Components\Select::make('default_category_id')
                            ->label('Default Category')
                            ->options(Category::pluck('name', 'id'))
                            ->searchable()
                            ->helperText('Assign products to this category if not specified in CSV'),

                        Forms\Components\Select::make('default_status')
                            ->label('Default Status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending Review',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\Toggle::make('skip_duplicates')
                            ->label('Skip Duplicate Products')
                            ->helperText('Skip rows where product name or slug already exists')
                            ->default(true),

                        Forms\Components\Toggle::make('update_existing')
                            ->label('Update Existing Products')
                            ->helperText('Update products if they already exist (matched by slug)')
                            ->default(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function preview(): void
    {
        $data = $this->form->getState();

        if (empty($data['csv_file'])) {
            Notification::make()
                ->title('No file uploaded')
                ->body('Please upload a CSV file first.')
                ->warning()
                ->send();
            return;
        }

        try {
            $filePath = Storage::disk('public')->path($data['csv_file']);
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $headers = $csv->getHeader();
            $requiredHeaders = ['name', 'price'];
            $missingHeaders = array_diff($requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                Notification::make()
                    ->title('Invalid CSV format')
                    ->body('Missing required columns: ' . implode(', ', $missingHeaders))
                    ->danger()
                    ->send();
                return;
            }

            $stmt = Statement::create()->limit(10);
            $records = $stmt->process($csv);

            $this->previewData = [
                'headers' => $headers,
                'rows' => iterator_to_array($records),
            ];

            $this->totalRows = count($csv);
            $this->uploadedFile = $data['csv_file'];

            // Validate all rows
            $this->validateCsv($csv, $data);

            Notification::make()
                ->title('Preview loaded')
                ->body("Found {$this->totalRows} rows. {$this->validRows} valid, {$this->errorRows} with errors.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error reading CSV')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function validateCsv($csv, array $data): void
    {
        $this->validationErrors = [];
        $this->validRows = 0;
        $this->errorRows = 0;

        $existingSlugs = [];
        if ($data['skip_duplicates'] ?? true) {
            $existingSlugs = \App\Models\Product::pluck('slug')->toArray();
        }

        foreach ($csv->getRecords() as $index => $record) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we have header row
            $errors = [];

            // Validate required fields
            if (empty($record['name'])) {
                $errors[] = 'Name is required';
            }

            if (empty($record['price']) || !is_numeric($record['price'])) {
                $errors[] = 'Valid price is required';
            }

            if (!empty($record['sale_price']) && !is_numeric($record['sale_price'])) {
                $errors[] = 'Sale price must be numeric';
            }

            // Check for duplicates
            $slug = \Illuminate\Support\Str::slug($record['name'] ?? '');
            if (in_array($slug, $existingSlugs) && !($data['update_existing'] ?? false)) {
                $errors[] = 'Product with this name already exists';
            }

            // Validate category if provided
            if (!empty($record['category']) && !Category::where('name', $record['category'])->orWhere('slug', $record['category'])->exists()) {
                if (empty($data['default_category_id'])) {
                    $errors[] = "Category '{$record['category']}' not found";
                }
            }

            if (!empty($errors)) {
                $this->validationErrors[$rowNumber] = $errors;
                $this->errorRows++;
            } else {
                $this->validRows++;
            }
        }
    }

    public function import(): void
    {
        $data = $this->form->getState();

        if (empty($this->uploadedFile)) {
            Notification::make()
                ->title('No file to import')
                ->body('Please upload and preview a CSV file first.')
                ->warning()
                ->send();
            return;
        }

        if ($this->validRows === 0) {
            Notification::make()
                ->title('No valid rows')
                ->body('There are no valid rows to import. Please fix the errors and try again.')
                ->danger()
                ->send();
            return;
        }

        try {
            // Create import record
            $import = ProductImport::create([
                'user_id' => auth()->id(),
                'file_path' => $this->uploadedFile,
                'total_rows' => $this->totalRows,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'status' => 'pending',
                'options' => $data,
                'errors' => [],
            ]);

            // Dispatch background job
            dispatch(new ProcessBulkProductImportJob($import));

            $this->isProcessing = true;

            Notification::make()
                ->title('Import started')
                ->body("Processing {$this->validRows} products in the background. You'll be notified when complete.")
                ->success()
                ->send();

            // Reset form
            $this->form->fill();
            $this->previewData = null;
            $this->uploadedFile = null;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'name',
            'slug',
            'short_description',
            'description',
            'price',
            'sale_price',
            'category',
            'seller_email',
            'version',
            'demo_url',
            'preview_url',
            'video_url',
            'software_compatibility',
            'tags',
            'is_featured',
            'status',
        ];

        $exampleRow = [
            'Premium Dashboard Template',
            'premium-dashboard-template',
            'A beautiful admin dashboard template',
            'Full description with features and details...',
            '49.99',
            '39.99',
            'templates',
            'seller@example.com',
            '1.0.0',
            'https://demo.example.com',
            'https://preview.example.com',
            'https://youtube.com/watch?v=xxx',
            'Laravel 10, PHP 8.1, Tailwind CSS',
            'dashboard, admin, template',
            '0',
            'draft',
        ];

        return response()->streamDownload(function () use ($headers, $exampleRow) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            fputcsv($output, $exampleRow);
            fclose($output);
        }, 'product-import-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function getRecentImports(): \Illuminate\Database\Eloquent\Collection
    {
        return ProductImport::with('user')
            ->latest()
            ->limit(10)
            ->get();
    }
}
