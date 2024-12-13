<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;

class StatsExport
{
    /**
     * Generate the Excel report with the provided questions data
     *
     * @param array $questionsData
     * @return void
     */
public function generateExcelReport($stats)
{
    Log::info('Starting the Excel report generation process. Stats received: ' . json_encode($stats));

    // Initialize a new spreadsheet
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Log::info('Spreadsheet initialized successfully.');
    } catch (\Exception $e) {
        Log::error('Error initializing spreadsheet: ' . $e->getMessage());
        throw $e; // Re-throw to allow higher-level error handling
    }

    // Set headers
    try {
        $sheet->setCellValue('A1', 'السؤال');
        $sheet->setCellValue('B1', 'ضعيف');
        $sheet->setCellValue('C1', 'مقبول');
        $sheet->setCellValue('D1', 'جيد');
        $sheet->setCellValue('E1', 'جيد جدا');
        $sheet->setCellValue('F1', 'ممتاز');
        Log::info('Headers set in the first row.');
    } catch (\Exception $e) {
        Log::error('Error setting headers: ' . $e->getMessage());
        throw $e;
    }

    // Initialize variables for overall average calculation
    $totalQuestionAverage = 0;
    $questionCount = 0;
    
    // Set data for each question
    $row = 2; // Starting from the second row to insert data
    foreach ($stats['questions'] as $questionStats) {
        // Log the current question being processed
        Log::info('Processing question: ' . $questionStats['text']);

        // Check if the 'percentages' data exists and is an array
        if (isset($questionStats['stats']['percentages']) && is_array($questionStats['stats']['percentages'])) {
            try {
                // Write question text to the spreadsheet
                $sheet->setCellValue('A' . $row, $questionStats['text']);
                
                // Get percentages for each option
                $percentages = $questionStats['stats']['percentages'];
                Log::info('Percentages for question: ' . $questionStats['text'] . ': ' . json_encode($percentages));

                // Set data for each percentage column (with default 0 if not set)
                $sheet->setCellValue('B' . $row, $percentages['ضعيف'] ?? 0);
                $sheet->setCellValue('C' . $row, $percentages['مقبول'] ?? 0);
                $sheet->setCellValue('D' . $row, $percentages['جيد'] ?? 0);
                $sheet->setCellValue('E' . $row, $percentages['جيد جدا'] ?? 0);
                $sheet->setCellValue('F' . $row, $percentages['ممتاز'] ?? 0);

             

            

                // Move to next row
                $row++;
                Log::info('Data set for row ' . $row);
            } catch (\Exception $e) {
                Log::error('Error setting data for question: ' . $questionStats['text'] . '. ' . $e->getMessage());
                throw $e;
            }
        } else {
            Log::info("Skipping question: " . $questionStats['text'] . " (no percentages data).");
        }
    }

    

    // Set headers for file download
    $fileName = 'questionnaire_report.xlsx';
    try {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        Log::info('Headers set for file download.');
    } catch (\Exception $e) {
        Log::error('Error setting headers for file download: ' . $e->getMessage());
        throw $e;
    }

    // Write the file to output (send it to the browser)
    try {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        Log::info('Excel file generated and output sent to browser.');
    } catch (\Exception $e) {
        Log::error('Error writing file to output: ' . $e->getMessage());
        throw $e;
    }

    // End the script execution
    exit;
}

}
