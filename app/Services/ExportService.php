<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Collection;

class ExportService
{
    protected Spreadsheet $spreadsheet;
    protected array $data = [];
    protected array $headers = [];
    protected array $styles = [];
    protected ?string $fallbackRedirectUrl = null;
    protected array $documentProperties = [];

    /**
     * Create a new export instance.
     *
     * @param array $headers
     * @param Collection|array $data
     */
    public function __construct(array $headers = [], $data = [])
    {
        $this->spreadsheet = new Spreadsheet();
        $this->headers = $headers;
        $this->data = $data instanceof Collection ? $data->toArray() : $data;
        
        // Set Excel-specific properties for better compatibility
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $this->spreadsheet->getDefaultStyle()->getFont()->setSize(11);
        
        $this->setupDefaultStyles();
    }

    /**
     * Set the headers for the export.
     *
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set the data for the export.
     *
     * @param Collection|array $data
     * @return self
     */
    public function setData($data): self
    {
        $this->data = $data instanceof Collection ? $data->toArray() : $data;
        return $this;
    }

    /**
     * Add data row to the export.
     *
     * @param array $row
     * @return self
     */
    public function addRow(array $row): self
    {
        $this->data[] = $row;
        return $this;
    }

    /**
     * Set custom styles for the export.
     *
     * @param array $styles
     * @return self
     */
    public function setStyles(array $styles): self
    {
        $this->styles = array_merge($this->styles, $styles);
        return $this;
    }

    /**
     * Set fallback redirect URL for JavaScript downloads.
     *
     * @param string $url
     * @return self
     */
    public function setFallbackRedirectUrl(string $url): self
    {
        $this->fallbackRedirectUrl = $url;
        return $this;
    }

    /**
     * Set document properties for Excel files.
     *
     * @param array $properties ['title' => 'My Export', 'subject' => 'Data', ...]
     * @return self
     */
    public function setDocumentProperties(array $properties): self
    {
        $this->documentProperties = array_merge($this->documentProperties, $properties);
        return $this;
    }

    /**
     * Export as Excel (.xlsx) file.
     *
     * @param string|null $filename
     * @return string The file path
     */
    public function toExcel(?string $filename = null): string
    {
        $filename = $filename ?: 'export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filepath = sys_get_temp_dir() . '/' . $filename;

        $this->buildSpreadsheet();
        $this->applyStyles();

        // Create Xlsx writer with proper settings
        $writer = new Xlsx($this->spreadsheet);
        
        // Set document properties for better Excel compatibility
        $properties = $this->spreadsheet->getProperties();
        $properties->setCreator('FMR System');
        $properties->setLastModifiedBy('FMR System');
        
        // Apply custom properties if set
        if (!empty($this->documentProperties)) {
            foreach ($this->documentProperties as $key => $value) {
                switch ($key) {
                    case 'title':
                        $properties->setTitle($value);
                        break;
                    case 'subject':
                        $properties->setSubject($value);
                        break;
                    case 'description':
                        $properties->setDescription($value);
                        break;
                    case 'keywords':
                        $properties->setKeywords($value);
                        break;
                    case 'category':
                        $properties->setCategory($value);
                        break;
                }
            }
        } else {
            // Default properties
            $properties->setTitle('Data Export');
            $properties->setDescription('Exported data from FMR System');
            $properties->setSubject('Export');
            $properties->setKeywords('export, fmr, data');
            $properties->setCategory('Report');
        }

        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Export as CSV file.
     *
     * @param string|null $filename
     * @return string The file path
     */
    public function toCsv(?string $filename = null): string
    {
        $filename = $filename ?: 'export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = sys_get_temp_dir() . '/' . $filename;

        $this->buildSpreadsheet();
        $this->applyStyles();

        $writer = new Csv($this->spreadsheet);
        $writer->setEnclosure('"');
        $writer->setDelimiter(';'); // Use semicolon as delimiter
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true);
        $writer->setOutputEncoding('UTF-8');
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Download the file directly to browser.
     *
     * @param string $format 'excel' or 'csv'
     * @param string|null $filename
     * @return void
     */
    public function download(string $format, ?string $filename = null): void
    {
        $filepath = $format === 'excel' ? $this->toExcel($filename) : $this->toCsv($filename);
        
        $this->sendDownloadResponse($filepath, $format);
    }

    /**
     * Build the spreadsheet with headers and data.
     *
     * @return void
     */
    protected function buildSpreadsheet(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        
        // Set headers
        if (!empty($this->headers)) {
            $col = 1;
            foreach ($this->headers as $header) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($col) . '1';
                // Ensure UTF-8 encoding for headers
                $encodedHeader = mb_convert_encoding($header, 'UTF-8', 'auto');
                $sheet->setCellValue($cellCoordinate, $encodedHeader);
                $col++;
            }
        }

        // Set data
        $row = 2;
        foreach ($this->data as $dataRow) {
            $col = 1;
            foreach ($dataRow as $cellValue) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;
                // Ensure UTF-8 encoding for data
                $encodedValue = mb_convert_encoding($cellValue, 'UTF-8', 'auto');
                $sheet->setCellValue($cellCoordinate, $encodedValue);
                $col++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Apply styles to the spreadsheet.
     *
     * @return void
     */
    protected function applyStyles(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        
        // Default header styles
        $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '236151'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Apply custom styles
        if (!empty($this->styles)) {
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
                  ->applyFromArray($this->styles);
        }

        // Style person names (rows where only first column has content and others are empty)
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $firstCell = $sheet->getCell('A' . $row)->getValue();
            $secondCell = $sheet->getCell('B' . $row)->getValue();
            
            // If first cell has content but second cell is empty, it's a person name
            if (!empty($firstCell) && empty($secondCell)) {
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '236151'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'e8f5e8'],
                    ],
                ]);
            }
        }

        // Data row styles
        $dataRange = 'A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow();
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Setup default styles for the export.
     *
     * @return void
     */
    protected function setupDefaultStyles(): void
    {
        $this->styles = [
            'font' => [
                'name' => 'Arial',
                'size' => 11,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
    }

    /**
     * Send download response to browser (WordPress compatible).
     *
     * @param string $filepath
     * @param string $format
     * @return void
     */
    protected function sendDownloadResponse(string $filepath, string $format): void
    {
        $mimeType = $format === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv';
        $filename = basename($filepath);
        $content = file_get_contents($filepath);

        // Clean up temporary file first
        unlink($filepath);

        // Try WordPress-compatible headers first
        if ($this->tryWordPressHeaders($content, $filename, $mimeType)) {
            return;
        }

        // Fallback to JavaScript download
        $this->triggerJavaScriptDownload($content, $filename, $mimeType);
    }

    /**
     * Try to use proper WordPress headers for download.
     *
     * @param string $content
     * @param string $filename
     * @param string $mimeType
     * @return bool Success status
     */
    private function tryWordPressHeaders(string $content, string $filename, string $mimeType): bool
    {
        // Check if headers have already been sent
        if (headers_sent()) {
            return false;
        }

        try {
            // Clear any existing output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Set proper headers
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Output content and exit
            echo $content;
            exit;

        } catch (Exception $e) {
            // If headers fail, return false to use JavaScript fallback
            return false;
        }
    }

    /**
     * Trigger download using JavaScript (WordPress-compatible).
     *
     * @param string $content
     * @param string $filename
     * @param string $mimeType
     * @return void
     */
    private function triggerJavaScriptDownload(string $content, string $filename, string $mimeType): void
    {
        // For Excel files, don't convert encoding as they are binary
        // For CSV files, ensure UTF-8 encoding
        if ($mimeType === 'text/csv' && !mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }
        
        // Encode content for JavaScript
        $encodedContent = base64_encode($content);
        
        // Get the referer URL to redirect back to
        $redirectUrl = $this->getRedirectUrl();
        
        // Simple JavaScript download with redirect back
        ?>
        <script>
            (function() {
                const content = '<?php echo $encodedContent; ?>';
                const filename = '<?php echo addslashes($filename); ?>';
                const mimeType = '<?php echo $mimeType; ?>';
                
                // Decode base64 and create blob with proper encoding
                const binaryString = atob(content);
                const bytes = new Uint8Array(binaryString.length);
                for (let i = 0; i < binaryString.length; i++) {
                    bytes[i] = binaryString.charCodeAt(i);
                }
                
                // For Excel files, don't add charset to MIME type
                const blobType = mimeType.includes('excel') ? mimeType : mimeType + ";charset=UTF-8";
                const blob = new Blob([bytes], { type: blobType });
                const url = window.URL.createObjectURL(blob);
                
                const link = document.createElement("a");
                link.href = url;
                link.download = filename;
                link.style.display = "none";
                document.body.appendChild(link);
                link.click();
                
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                // Redirect back to the anniversaries page
                setTimeout(function() {
                    window.location.href = '<?php echo $redirectUrl; ?>';
                }, 100);
            })();
        </script>
        <?php
        exit;
    }

    /**
     * Get the redirect URL after download.
     *
     * @return string
     */
    private function getRedirectUrl(): string
    {
        // Try to get the referer URL first
        $referer = wp_get_referer();
        
        // If referer exists and is from the same domain, use it
        if ($referer && strpos($referer, home_url()) === 0) {
            return $referer;
        }
        
        // Use custom fallback URL if set
        if ($this->fallbackRedirectUrl) {
            return $this->fallbackRedirectUrl;
        }
        
        // Fallback to admin dashboard
        return admin_url();
    }

    /**
     * Create a new export instance (fluent interface).
     *
     * @param array $headers
     * @param Collection|array $data
     * @return self
     */
    public static function make(array $headers = [], $data = []): self
    {
        return new self($headers, $data);
    }

    /**
     * Create export from collection with specified columns.
     *
     * @param Collection $collection
     * @param array $columns ['header' => 'property.path', ...]
     * @return self
     */
    public static function fromCollection(Collection $collection, array $columns): self
    {
        $headers = array_keys($columns);
        $data = [];

        foreach ($collection as $item) {
            $row = [];
            foreach ($columns as $column) {
                $row[] = data_get($item, $column, '');
            }
            $data[] = $row;
        }

        return new self($headers, $data);
    }
}
