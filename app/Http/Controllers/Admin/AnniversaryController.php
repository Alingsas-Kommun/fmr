<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnniversaryService;
use App\Services\ExportService;
use Illuminate\Http\Request;

class AnniversaryController extends Controller
{
    /**
     * The anniversary service instance.
     *
     * @var AnniversaryService
     */
    protected $anniversaryService;

    /**
     * Constructor.
     *
     * @param AnniversaryService $anniversaryService
     */
    public function __construct(AnniversaryService $anniversaryService)
    {
        $this->anniversaryService = $anniversaryService;
    }

    /**
     * Display the anniversaries page with optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $minYears = $request->get('min-years') !== null ? (float) $request->get('min-years') : null;
        $maxYears = $request->get('max-years') !== null ? (float) $request->get('max-years') : null;
        $export = $request->get('export');

        // Handle export requests
        if ($export && ($minYears !== null || $maxYears !== null)) {
            return $this->handleExport($export, $minYears, $maxYears);
        }

        $results = collect();

        // Only perform the expensive calculation if filters are applied
        if ($minYears !== null || $maxYears !== null) {
            $results = $this->anniversaryService->getPersonsByServiceYears($minYears, $maxYears);
        }

        return view('admin.anniversaries.index', [
            'results' => $results,
            'minYears' => $minYears,
            'maxYears' => $maxYears,
            'hasFilters' => $minYears !== null || $maxYears !== null
        ]);
    }

    /**
     * Handle export requests for Excel and CSV formats.
     *
     * @param string $format
     * @param float|null $minYears
     * @param float|null $maxYears
     * @return void
     */
    private function handleExport(string $format, ?float $minYears, ?float $maxYears): void
    {
        $results = $this->anniversaryService->getPersonsByServiceYears($minYears, $maxYears);
        $filename = $this->generateExportFilename($format, $minYears, $maxYears);

        // Prepare export data
        $exportData = $this->prepareExportData($results);

        // Create and download export with custom styling
        ExportService::make($exportData['headers'], $exportData['rows'])
            ->setStyles([
                'font' => ['name' => 'Arial', 'size' => 11],
            ])
            ->setDocumentProperties([
                'title' => 'Anniversaries Export',
                'subject' => 'Anniversaries',
                'description' => 'Exported anniversaries data with service years',
                'keywords' => 'anniversaries, export, fmr, service years',
                'category' => 'Anniversary Report'
            ])
            ->setFallbackRedirectUrl(admin_url('admin.php?page=anniversaries'))
            ->download($format, $filename);
    }

    /**
     * Prepare data for export.
     *
     * @param \Illuminate\Support\Collection $results
     * @return array
     */
    private function prepareExportData($results): array
    {
        $headers = [
            __('Position', 'fmr'),
            __('Start Date', 'fmr'),
            __('End Date', 'fmr'),
            __('Service Time', 'fmr')
        ];

        $rows = [];
        foreach ($results as $result) {
            // Add person name as a separate row
            $rows[] = [
                $result['person']->post_title, // Person name in first column
                '', // Empty columns for other headers
                '',
                ''
            ];
            
            // Add assignment rows
            foreach ($result['assignments'] as $assignment) {
                $rows[] = [
                    $assignment->roleTerm->name ?? __('Unknown Role', 'fmr'),
                    $assignment->period_start->format('Y-m-d H:i:s'),
                    $assignment->period_end->format('Y-m-d H:i:s'),
                    ''
                ];
            }
            
            // Add service time result row
            $rows[] = [
                __('Result:', 'fmr'),
                '',
                '',
                $result['service_display']
            ];
            
            // Add blank row between persons
            $rows[] = ['', '', '', ''];
        }

        return compact('headers', 'rows');
    }

    /**
     * Generate export filename with timestamp.
     *
     * @param string $format
     * @param float|null $minYears
     * @param float|null $maxYears
     * @return string
     */
    private function generateExportFilename(string $format, ?float $minYears, ?float $maxYears): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filters = [];
        
        if ($minYears !== null) {
            $filters[] = "min{$minYears}";
        }
        if ($maxYears !== null) {
            $filters[] = "max{$maxYears}";
        }
        
        $filterString = !empty($filters) ? '_' . implode('_', $filters) : '';
        $extension = $format === 'excel' ? 'xlsx' : 'csv';
        
        return "anniversaries{$filterString}_{$timestamp}.{$extension}";
    }
}
