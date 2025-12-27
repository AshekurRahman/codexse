<?php

namespace App\Filament\Admin\Resources\ChatbotFaqResource\Pages;

use App\Filament\Admin\Resources\ChatbotFaqResource;
use App\Models\ChatbotFaq;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListChatbotFaqs extends ListRecords
{
    protected static string $resource = ChatbotFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('CSV File')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->helperText('Upload a CSV file with columns: question, answer, keywords, category, sort_order, is_active'),

                    Forms\Components\Toggle::make('skip_first_row')
                        ->label('Skip header row')
                        ->default(true)
                        ->helperText('Enable if your CSV has a header row'),

                    Forms\Components\Toggle::make('update_existing')
                        ->label('Update existing FAQs')
                        ->default(false)
                        ->helperText('If enabled, FAQs with matching questions will be updated'),
                ])
                ->action(function (array $data): void {
                    $this->importCsv($data);
                }),

            Actions\Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    Forms\Components\Radio::make('format')
                        ->label('Export Format')
                        ->options([
                            'all' => 'All FAQs',
                            'active' => 'Active FAQs only',
                        ])
                        ->default('all')
                        ->required(),
                ])
                ->action(function (array $data): StreamedResponse {
                    return $this->exportCsv($data);
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function importCsv(array $data): void
    {
        $file = Storage::disk('local')->path($data['file']);
        $skipFirst = $data['skip_first_row'];
        $updateExisting = $data['update_existing'];

        if (!file_exists($file)) {
            Notification::make()
                ->danger()
                ->title('Import Failed')
                ->body('Could not read the uploaded file.')
                ->send();
            return;
        }

        $handle = fopen($file, 'r');
        $rowNumber = 0;
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip header row
            if ($skipFirst && $rowNumber === 1) {
                continue;
            }

            // Validate row has enough columns
            if (count($row) < 2) {
                $errors[] = "Row {$rowNumber}: Not enough columns";
                $skipped++;
                continue;
            }

            $question = trim($row[0] ?? '');
            $answer = trim($row[1] ?? '');
            $keywords = trim($row[2] ?? '');
            $category = trim($row[3] ?? 'General');
            $sortOrder = (int) ($row[4] ?? 0);
            $isActive = isset($row[5]) ? filter_var(trim($row[5]), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true : true;

            if (empty($question) || empty($answer)) {
                $errors[] = "Row {$rowNumber}: Missing question or answer";
                $skipped++;
                continue;
            }

            // Check for existing FAQ
            $existing = ChatbotFaq::where('question', $question)->first();

            if ($existing) {
                if ($updateExisting) {
                    $existing->update([
                        'answer' => $answer,
                        'keywords' => $keywords ?: null,
                        'category' => $category ?: null,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                ChatbotFaq::create([
                    'question' => $question,
                    'answer' => $answer,
                    'keywords' => $keywords ?: null,
                    'category' => $category ?: null,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                ]);
                $imported++;
            }
        }

        fclose($handle);

        // Clean up temp file
        Storage::disk('local')->delete($data['file']);

        // Build notification message
        $message = [];
        if ($imported > 0) {
            $message[] = "{$imported} imported";
        }
        if ($updated > 0) {
            $message[] = "{$updated} updated";
        }
        if ($skipped > 0) {
            $message[] = "{$skipped} skipped";
        }

        Notification::make()
            ->success()
            ->title('Import Complete')
            ->body(implode(', ', $message))
            ->send();
    }

    protected function exportCsv(array $data): StreamedResponse
    {
        $query = ChatbotFaq::query()->ordered();

        if ($data['format'] === 'active') {
            $query->active();
        }

        $faqs = $query->get();

        return response()->streamDownload(function () use ($faqs) {
            $handle = fopen('php://output', 'w');

            // Write header
            fputcsv($handle, [
                'question',
                'answer',
                'keywords',
                'category',
                'sort_order',
                'is_active',
                'hit_count',
            ]);

            // Write data
            foreach ($faqs as $faq) {
                fputcsv($handle, [
                    $faq->question,
                    $faq->answer,
                    $faq->keywords,
                    $faq->category,
                    $faq->sort_order,
                    $faq->is_active ? 'true' : 'false',
                    $faq->hit_count,
                ]);
            }

            fclose($handle);
        }, 'chatbot-faqs-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
