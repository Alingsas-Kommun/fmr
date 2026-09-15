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
        $filters = $this->resolveFilters($request);
        $export = $request->get('export');

        if ($export && $filters !== []) {
            return $this->handleExport($export, $filters);
        }

        $results = $filters !== []
            ? $this->anniversaryService->getPersonsByServiceYears($filters)
            : collect();

        return view('admin.anniversaries.index', [
            'results' => $results,
            'filters' => $filters,
            'boards' => app(BoardController::class)->getAll(),
            'roles' => app(RoleController::class)->getAll(),
            'hasFilters' => $filters !== [],
        ]);
    }

    /**
     * Normalize request input into the filters the service expects.
     *
     * @param Request $request
     * @return array
     */
    private function resolveFilters(Request $request): array
    {
        $filters = [];

        foreach (['min_years' => 'min-years', 'max_years' => 'max-years'] as $key => $input) {
            $value = $request->get($input);

            if ($value !== null && $value !== '') {
                $filters[$key] = (float) $value;
            }
        }

        $boardId = $request->get('board');
        if ($boardId !== null && $boardId !== '') {
            $filters['board_id'] = (int) $boardId;
        }

        $roleIds = array_values(array_filter(array_map('intval', (array) $request->get('roles', []))));
        if ($roleIds !== []) {
            $filters['role_ids'] = $roleIds;
        }

        return $filters;
    }

    /**
     * Handle export requests for Excel and CSV formats.
     *
     * @param string $format
     * @param array $filters
     * @return void
     */
    private function handleExport(string $format, array $filters): void
    {
        $results = $this->anniversaryService->getPersonsByServiceYears($filters);
        $filename = $this->generateExportFilename($format, $filters);
        $exportData = $this->prepareExportData($results);

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
            ->setFallbackRedirectUrl(remove_query_arg('export'))
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
            __('Board', 'fmr'),
            __('Start Date', 'fmr'),
            __('End Date', 'fmr'),
            __('Service Time', 'fmr'),
        ];

        $emptyRow = array_pad([], count($headers), '');
        $rows = [];

        foreach ($results as $result) {
            $personDisplayName = $result['party_name']
                ? $result['full_name'] . ' (' . $result['party_name'] . ')'
                : $result['full_name'];

            $rows[] = array_pad([$personDisplayName], count($headers), '');

            foreach ($result['assignments'] as $assignment) {
                $rows[] = [
                    $assignment->roleTerm->name ?? __('Unknown Role', 'fmr'),
                    $assignment->board?->post_title ?? '',
                    $assignment->period_start->format('Y-m-d H:i:s'),
                    $assignment->period_end->format('Y-m-d H:i:s'),
                    '',
                ];
            }

            $resultRow = $emptyRow;
            $resultRow[0] = __('Total time (overlapping periods counted once)', 'fmr');
            $resultRow[array_key_last($resultRow)] = $result['service_display'];
            $rows[] = $resultRow;
            $rows[] = $emptyRow;
        }

        return compact('headers', 'rows');
    }

    /**
     * Generate export filename with timestamp.
     *
     * @param string $format
     * @param array $filters
     * @return string
     */
    private function generateExportFilename(string $format, array $filters): string
    {
        $filterString = collect($filters)
            ->reject(fn ($value) => $value === null || $value === [])
            ->map(fn ($value, $key) => str_replace('_', '', $key) . (is_array($value) ? implode('-', $value) : $value))
            ->implode('_');

        $extension = $format === 'excel' ? 'xlsx' : 'csv';

        return sprintf(
            'anniversaries%s_%s.%s',
            $filterString !== '' ? '_' . $filterString : '',
            now()->format('Y-m-d_H-i-s'),
            $extension
        );
    }
}
