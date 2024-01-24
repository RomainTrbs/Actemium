<?php

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcelController extends AbstractController
{
    #[Route('/excel', name: 'app_excel')]
    public function generateExcel(): Response
    {

        $htmlContent = '<table>
                            <tr>
                            <th>Header 1</th>
                            <th>Header 2</th>
                            </tr>
                            <tr>
                            <td>Data 1</td>
                            <td>Data 2</td>
                            </tr>
                        </table>';

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();        
        
        $htmlReader = IOFactory::createReader('Html');
        
        $spreadsheet = $htmlReader->loadFromString($htmlContent);



        // Add data to the spreadsheet
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Header 1');
        // $sheet->setCellValue('B1', 'Header 2');
        // $sheet->setCellValue('A2', 'Data 1');
        // $sheet->setCellValue('B2', 'Data 2');

        // Create a new Excel writer
        $writer = new Xlsx($spreadsheet);

        // Save the Excel file to a temporary location
        $tempFilePath = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFilePath);

        // Return the Excel file as a response
        $response = new Response(file_get_contents($tempFilePath));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="example.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Delete the temporary file
        unlink($tempFilePath);

        return $response;
    }
}