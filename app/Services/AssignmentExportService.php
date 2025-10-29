<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Term;
use App\Services\ExportService;
use App\Http\Controllers\Admin\AssignmentController;
use Illuminate\Support\Collection;

class AssignmentExportService
{
    protected AssignmentController $assignmentController;

    public function __construct(AssignmentController $assignmentController)
    {
        $this->assignmentController = $assignmentController;
    }

    /**
     * Handle export requests for Excel and CSV formats.
     * Optimized for large datasets with memory management.
     *
     * @param string $format
     * @param array $filters
     * @return void
     */
    public function handleExport(string $format, array $filters = [])
    {
        // Increase memory limit for large exports
        ini_set('memory_limit', '512M');
        
        // Set longer execution time for large datasets
        set_time_limit(300); // 5 minutes
        
        $assignments = $this->getAssignmentsForExport($filters);
        $filename = $this->generateExportFilename($format, $filters);

        // Prepare export data with memory optimization
        $exportData = $this->prepareExportData($assignments);

        // Create and download export with custom styling
        ExportService::make($exportData['headers'], $exportData['rows'])
            ->setStyles([
                'font' => ['name' => 'Arial', 'size' => 12],
            ])
            ->setDocumentProperties([
                'title' => 'Assignments Export',
                'subject' => 'Assignments',
                'description' => 'Exported assignments data with person details',
                'keywords' => 'assignments, export, fmr, persons, roles',
                'category' => 'Assignment Report'
            ])
            ->setFallbackRedirectUrl(admin_url('admin.php?page=assignments'))
            ->download($format, $filename);
    }

    /**
     * Get assignments for export with all necessary relationships.
     * Uses chunking for large datasets to avoid memory issues.
     * Applies the same filters and sorting as the list view.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAssignmentsForExport(array $filters = []): Collection
    {
        // Use the shared filtering and sorting logic from the controller
        $query = $this->assignmentController->buildFilteredQuery($filters);

        // For large datasets, we'll use chunking to avoid memory issues
        $totalCount = $query->count();
        
        if ($totalCount > 1000) {
            // For large datasets, stream results with lazy() and merge into a single collection
            return $this->assignmentController
                ->buildFilteredQuery($filters)
                ->lazy(500)
                ->collect()
                ->values();
        } else {
            // For smaller datasets, get all at once
            return $query->get();
        }
    }

    /**
     * Prepare data for export.
     * Optimized for large datasets with batch processing.
     *
     * @param Collection $assignments
     * @return array
     */
    private function prepareExportData(Collection $assignments): array
    {
        $headers = [
            __('Firstname', 'fmr'),
            __('Lastname', 'fmr'),
            __('SSN', 'fmr'),
            __('Role', 'fmr'),
            __('Decision Authority', 'fmr'),
            __('Period', 'fmr'),
            __('Email (work)', 'fmr'),
            __('Mobile (work)', 'fmr'),
            __('Phone (work)', 'fmr'),
            __('Address (work)', 'fmr'),
            __('Zip Code (work)', 'fmr'),
            __('City (work)', 'fmr'),
            __('Email (home)', 'fmr'),
            __('Mobile (home)', 'fmr'),
            __('Phone (home)', 'fmr'),
            __('Address (home)', 'fmr'),
            __('Zip Code (home)', 'fmr'),
            __('City (home)', 'fmr'),
        ];

        $rows = [];
        
        // For large datasets, process in smaller batches to avoid memory issues
        $batchSize = 100;
        $totalCount = $assignments->count();
        
        if ($totalCount > 500) {
            // Process in batches for large datasets
            foreach ($assignments->chunk($batchSize) as $batch) {
                foreach ($batch as $assignment) {
                    $rows[] = $this->prepareAssignmentRow($assignment);
                }
            }
        } else {
            // Process all at once for smaller datasets
            foreach ($assignments as $assignment) {
                $rows[] = $this->prepareAssignmentRow($assignment);
            }
        }

        return compact('headers', 'rows');
    }

    /**
     * Prepare a single assignment row for export.
     *
     * @param Assignment $assignment
     * @return array
     */
    private function prepareAssignmentRow(Assignment $assignment): array
    {
        $person = $assignment->person;
        
        // Get person meta data efficiently
        $personMeta = $person ? $person->getMetaValues([
            'person_firstname',
            'person_lastname',
            'person_ssn',
            'person_work_email',
            'person_work_mobile',
            'person_work_phone',
            'person_work_address',
            'person_work_zip',
            'person_work_city',
            'person_home_email',
            'person_home_mobile',
            'person_home_phone',
            'person_home_address',
            'person_home_zip',
            'person_home_city',
        ]) : [];

        return [
            $personMeta['person_firstname'] ?? '',
            $personMeta['person_lastname'] ?? '',
            $personMeta['person_ssn'] ?? '',
            $assignment->roleTerm->name ?? __('Unknown Role', 'fmr'),
            $assignment->decisionAuthority->title ?? __('Unknown Authority', 'fmr'),
            $this->formatPeriod($assignment->period_start, $assignment->period_end),
            $personMeta['person_work_email'] ?? '',
            $personMeta['person_work_mobile'] ?? '',
            $personMeta['person_work_phone'] ?? '',
            $personMeta['person_work_address'] ?? '',
            $personMeta['person_work_zip'] ?? '',
            $personMeta['person_work_city'] ?? '',
            $personMeta['person_home_email'] ?? '',
            $personMeta['person_home_mobile'] ?? '',
            $personMeta['person_home_phone'] ?? '',
            $personMeta['person_home_address'] ?? '',
            $personMeta['person_home_zip'] ?? '',
            $personMeta['person_home_city'] ?? '',
        ];
    }

    /**
     * Format period for display.
     *
     * @param string|null $start
     * @param string|null $end
     * @return string
     */
    private function formatPeriod($start, $end): string
    {
        $startFormatted = $start ? wp_date('Y-m-d', strtotime($start)) : '—';
        $endFormatted = $end ? wp_date('Y-m-d', strtotime($end)) : '—';
        
        return $startFormatted . ' – ' . $endFormatted;
    }

    /**
     * Generate export filename with timestamp and filters.
     *
     * @param string $format
     * @param array $filters
     * @return string
     */
    private function generateExportFilename(string $format, array $filters): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filterParts = [];
        
        if (!empty($filters['period_status']) && $filters['period_status'] !== 'all') {
            $filterParts[] = $filters['period_status'];
        }
        
        if (!empty($filters['role_filter'])) {
            $role = Term::find($filters['role_filter']);
            if ($role) {
                $filterParts[] = 'role_' . sanitize_title($role->name);
            }
        }
        
        if (!empty($filters['board_filter'])) {
            $filterParts[] = 'board_' . $filters['board_filter'];
        }
        
        if (!empty($filters['person_filter'])) {
            $filterParts[] = 'person_' . $filters['person_filter'];
        }
        
        if (!empty($filters['party_filter'])) {
            $filterParts[] = 'party_' . $filters['party_filter'];
        }
        
        if (!empty($filters['search'])) {
            $filterParts[] = 'search_' . sanitize_title(substr($filters['search'], 0, 20));
        }
        
        $filterString = !empty($filterParts) ? '_' . implode('_', $filterParts) : '';
        $extension = $format === 'excel' ? 'xlsx' : 'csv';
        
        return "assignments{$filterString}_{$timestamp}.{$extension}";
    }
}
