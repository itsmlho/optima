<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export Service
 * Centralized service for data export operations (Excel, CSV, PDF)
 * Removes business logic from views - proper MVC separation
 */
class ExportService
{
    protected $defaultHeaderStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    protected $defaultCellStyle = [
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ];

    /**
     * Export data to Excel file
     * 
     * @param array $data Data to export
     * @param array $headers Column headers ['Key' => 'Display Name']
     * @param string $title Sheet title
     * @param array $options Additional options (filename, author, company)
     * @return \CodeIgniter\HTTP\DownloadResponse
     */
    public function exportToExcel(array $data, array $headers, string $title = 'Export', array $options = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title, 0, 31)); // Excel limit

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator($options['author'] ?? 'OPTIMA System')
            ->setCompany($options['company'] ?? 'Yohanes Chandra')
            ->setTitle($title)
            ->setSubject($options['subject'] ?? 'Data Export')
            ->setDescription($options['description'] ?? 'Exported from OPTIMA')
            ->setCreated(time());

        // Write headers
        $col = 'A';
        $headerKeys = [];
        foreach ($headers as $key => $displayName) {
            $sheet->setCellValue($col . '1', $displayName);
            $headerKeys[] = $key;
            $col++;
        }

        // Apply header style
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($this->defaultHeaderStyle);

        // Write data rows
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($headerKeys as $key) {
                $value = $item[$key] ?? '';
                
                // Handle special data types
                if (is_numeric($value) && strlen($value) > 10) {
                    // Treat long numbers as strings (e.g., phone numbers, IDs)
                    $sheet->setCellValueExplicit($col . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } elseif ($value instanceof \DateTime) {
                    $sheet->setCellValue($col . $row, $value->format('Y-m-d H:i:s'));
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                
                $col++;
            }
            $row++;
        }

        // Apply cell style to data range
        if ($row > 2) {
            $sheet->getStyle('A2:' . $lastCol . ($row - 1))->applyFromArray($this->defaultCellStyle);
        }

        // Auto-size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Generate filename
        $filename = $options['filename'] ?? ($title . '_' . date('YmdHis') . '.xlsx');
        if (!str_ends_with($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        // Write to output
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        $response = service('response');
        return $response->download($tempFile, null, true)
            ->setFileName($filename)
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Export data to CSV file
     * 
     * @param array $data Data to export
     * @param array $headers Column headers ['Key' => 'Display Name']
     * @param string $title File title
     * @param array $options Additional options (delimiter, enclosure, encoding)
     * @return \CodeIgniter\HTTP\DownloadResponse
     */
    public function exportToCSV(array $data, array $headers, string $title = 'Export', array $options = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write headers
        $col = 'A';
        $headerKeys = [];
        foreach ($headers as $key => $displayName) {
            $sheet->setCellValue($col . '1', $displayName);
            $headerKeys[] = $key;
            $col++;
        }

        // Write data rows
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($headerKeys as $key) {
                $value = $item[$key] ?? '';
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Generate filename
        $filename = $options['filename'] ?? ($title . '_' . date('YmdHis') . '.csv');
        if (!str_ends_with($filename, '.csv')) {
            $filename .= '.csv';
        }

        // Write to CSV
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter($options['delimiter'] ?? ',');
        $writer->setEnclosure($options['enclosure'] ?? '"');
        
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        $response = service('response');
        return $response->download($tempFile, null, true)
            ->setFileName($filename)
            ->setContentType('text/csv');
    }

    /**
     * Export data to formatted Excel with custom styling
     * 
     * @param array $data Data to export
     * @param array $config Export configuration
     * @return \CodeIgniter\HTTP\DownloadResponse
     * 
     * Config structure:
     * [
     *     'title' => 'Report Title',
     *     'filename' => 'report.xlsx',
     *     'headers' => ['key' => 'Display Name'],
     *     'summary' => ['Total' => 100, 'Count' => 50], // Optional summary row
     *     'groupBy' => 'category', // Optional grouping
     *     'styles' => ['header' => [...], 'cell' => [...]] // Optional custom styles
     * ]
     */
    public function exportFormattedExcel(array $data, array $config)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $title = $config['title'] ?? 'Export';
        $headers = $config['headers'] ?? [];
        $summary = $config['summary'] ?? null;
        $groupBy = $config['groupBy'] ?? null;
        
        $sheet->setTitle(substr($title, 0, 31));

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('OPTIMA System')
            ->setTitle($title)
            ->setCreated(time());

        $currentRow = 1;

        // Add title row if specified
        if (isset($config['showTitle']) && $config['showTitle']) {
            $sheet->setCellValue('A' . $currentRow, $title);
            $sheet->getStyle('A' . $currentRow)->getFont()->setSize(14)->setBold(true);
            $currentRow += 2;
        }

        // Write headers
        $col = 'A';
        $headerKeys = [];
        foreach ($headers as $key => $displayName) {
            $sheet->setCellValue($col . $currentRow, $displayName);
            $headerKeys[] = $key;
            $col++;
        }

        $lastCol = chr(ord('A') + count($headers) - 1);
        $headerStyle = $config['styles']['header'] ?? $this->defaultHeaderStyle;
        $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)->applyFromArray($headerStyle);
        $currentRow++;

        // Write data with optional grouping
        if ($groupBy && count($data) > 0) {
            $grouped = $this->groupData($data, $groupBy);
            foreach ($grouped as $group => $items) {
                // Group header
                $sheet->setCellValue('A' . $currentRow, $group);
                $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7E6E6');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                // Group items
                foreach ($items as $item) {
                    $col = 'A';
                    foreach ($headerKeys as $key) {
                        $sheet->setCellValue($col . $currentRow, $item[$key] ?? '');
                        $col++;
                    }
                    $currentRow++;
                }
            }
        } else {
            // Standard row writing
            foreach ($data as $item) {
                $col = 'A';
                foreach ($headerKeys as $key) {
                    $sheet->setCellValue($col . $currentRow, $item[$key] ?? '');
                    $col++;
                }
                $currentRow++;
            }
        }

        // Apply cell styles
        $cellStyle = $config['styles']['cell'] ?? $this->defaultCellStyle;
        $dataStartRow = (isset($config['showTitle']) && $config['showTitle']) ? 4 : 2;
        $sheet->getStyle('A' . $dataStartRow . ':' . $lastCol . ($currentRow - 1))->applyFromArray($cellStyle);

        // Add summary row if specified
        if ($summary) {
            $currentRow++;
            $col = 'A';
            foreach ($headers as $key => $displayName) {
                $value = $summary[$key] ?? '';
                $sheet->setCellValue($col . $currentRow, $value);
                $col++;
            }
            $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)->getFont()->setBold(true);
        }

        // Auto-size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze panes
        $freezeRow = (isset($config['showTitle']) && $config['showTitle']) ? 4 : 2;
        $sheet->freezePane('A' . $freezeRow);

        // Generate and download
        $filename = $config['filename'] ?? ($title . '_' . date('YmdHis') . '.xlsx');
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        $response = service('response');
        return $response->download($tempFile, null, true)
            ->setFileName($filename)
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Group data by a specific key
     * 
     * @param array $data Data array
     * @param string $key Grouping key
     * @return array Grouped data
     */
    protected function groupData(array $data, string $key): array
    {
        $grouped = [];
        foreach ($data as $item) {
            $groupValue = $item[$key] ?? 'Ungrouped';
            if (!isset($grouped[$groupValue])) {
                $grouped[$groupValue] = [];
            }
            $grouped[$groupValue][] = $item;
        }
        return $grouped;
    }

    /**
     * Get formatted download response for direct output
     * Useful for simple exports without file generation
     * 
     * @param string $content File content
     * @param string $filename Download filename
     * @param string $contentType MIME type
     * @return \CodeIgniter\HTTP\DownloadResponse
     */
    public function downloadResponse(string $content, string $filename, string $contentType = 'text/plain')
    {
        $response = service('response');
        return $response->setBody($content)
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'must-revalidate')
            ->setHeader('Pragma', 'public')
            ->setHeader('Expires', '0');
    }
}
