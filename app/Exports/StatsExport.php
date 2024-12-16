<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Log;
use App\Models\QuestionnaireTarget;

class StatsExport
{
    /**
     * Generate the Excel report with the provided stats data.
     *
     * @param array $stats
     * @return void
     */
    public function generateExcelReport($stats)
    {
        try {
            // Initialize Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Populate Headers
            $this->populateHeaders($sheet, $stats);

            // Populate Data
            $this->populateData($sheet, $stats);

            // Export the Excel file
            $this->exportFile($spreadsheet);

        } catch (\Exception $e) {
            Log::error('Failed to generate Excel report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Populate the header information in the Excel sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param array $stats
     * @return void
     */
    private function populateHeaders($sheet, $stats)
    {
        try {
            $questionnaireTarget = QuestionnaireTarget::find($stats['questionnaire']['id']);

            if (!$questionnaireTarget) {
                throw new \Exception('QuestionnaireTarget not found.');
            }

            $sheet->setCellValue('A1', 'Questionnaire Title');
            $sheet->setCellValue('B1', 'Course Name');
            $sheet->setCellValue('C1', 'Average Score');

            $sheet->setCellValue('A2', $questionnaireTarget->Questionnaire->title ?? 'N/A');
            $sheet->setCellValue('B2', $questionnaireTarget->CourseDetail->Course->name ?? 'N/A');
            $sheet->setCellValue('C2', $stats['stats']['overall_average'] ?? 'N/A');

            // Question Headers
            $headers = ['السؤال', 'ضعيف', 'مقبول', 'جيد', 'جيد جدا', 'ممتاز', 'المتوسط'];
            $this->setRowValues($sheet, 3, $headers);

            // Apply header styling
            $this->styleHeaders($sheet);

            Log::info('Headers set successfully.');
        } catch (\Exception $e) {
            Log::error('Error setting headers: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Apply styling to the header cells.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    private function styleHeaders($sheet)
    {
        $headerRange = 'A1:G1';  // Range for headers
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00'); // Yellow background

        // Set borders for the header
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Populate the data rows in the Excel sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param array $stats
     * @return void
     */
    private function populateData($sheet, $stats)
    {
        try {
            $row = 4; // Data starts at row 4 (after headers)
            foreach ($stats['stats']['questions'] as $questionStats) {
                if (isset($questionStats['stats']['percentages']) && is_array($questionStats['stats']['percentages'])) {
                    $percentages = $questionStats['stats']['percentages'];
                    $data = [
                        $questionStats['text'] ?? 'N/A',
                        $percentages['ضعيف']. '%' ?? 0,
                        $percentages['مقبول']. '%' ?? 0,
                        $percentages['جيد']. '%' ?? 0,
                        $percentages['جيد جدا']. '%' ?? 0,
                        $percentages['ممتاز']. '%' ?? 0,
                        $questionStats['stats']['average'] ?? 0,
                    ];

                    $this->setRowValues($sheet, $row, $data);
                    $row++;
                    Log::info("Data populated for row {$row}.");
                } else {
                    Log::info("Skipping question: " . $questionStats['text'] . " (no percentages data).");
                }
            }

            // Apply data styling
            $this->styleData($sheet, 4, $row);
        } catch (\Exception $e) {
            Log::error('Error populating data: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Apply styling to the data cells.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $startRow
     * @param int $endRow
     * @return void
     */
    private function styleData($sheet, $startRow, $endRow)
    {
        $dataRange = 'A' . $startRow . ':G' . $endRow;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($dataRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Export the file as an Excel download.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return void
     */
    private function exportFile($spreadsheet)
    {
        try {
            $fileName = 'questionnaire_report.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            Log::info('Excel file generated and sent to browser.');
        } catch (\Exception $e) {
            Log::error('Error exporting file: ' . $e->getMessage());
            throw $e;
        }

        exit;
    }

    /**
     * Set multiple values in a single row.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $row
     * @param array $values
     * @return void
     */
    private function setRowValues($sheet, $row, $values)
    {
        $column = 'A';
        foreach ($values as $value) {
            $sheet->setCellValue($column . $row, $value);
            $column++;
        }
    }
}
