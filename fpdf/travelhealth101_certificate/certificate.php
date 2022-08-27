<?php
    use setasign\Fpdi\Fpdi;
    use setasign\Fpdi\PdfReader;

    require_once('../fpdf184/fpdf.php');
    require_once('../fpdf184/src/autoload.php');

    // Use FPDI instead of FPDF to import empty pdf template
    // Page size: letter (landscape)
    $pdf = new FPDI('l', 'cm', array(21.6, 27.9));
    $pagecount = $pdf->setSourceFile('certificate_template.pdf');
    // Use imported template as new page
    $tpl = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tpl);


    // Retrieve JSON data and set default values
    $jsonfilepath = 'certificate_sampledata.json';
    $jsonfile = file_get_contents($jsonfilepath);
    $data = json_decode($jsonfile);

    $name = isset($data->name) ? $data->name : '';
    $year = isset($data->year) ? $data->year : date('Y');
    // 'n' and 'j' = without leading zeros
    $month = isset($data->month) ? $data->month : date('n');
    $day = isset($data->day) ? $data->day : date('j');
    
    // 
    $day = intval($day);
    $month = intval($month);

    $months = array(
        'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL',
        'MAY', 'JUNE', 'JULY', 'AUGUST',
        'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
    );
    $lblMonth = $months[$month - 1];
    // $lblDay = '2nd';
    $lblDay = '2nd';


    // Georgia font is not provided. Times New Roman is used by default
    $pdf->SetFont('Times'); 
    // Same size as Template.docx
    $pdf->SetFontSize('15'); 

    $pdf->SetXY(1.1, 8.1); // set the position of the box
    $pdf->Cell($pdf->GetPageWidth() - 2.2, 1, $name, 0, 1, 'C');

    $lblDate1 = 'THIS ' . $day . '   DAY OF ' . $lblMonth . ' ' . $year;
    $lblDate2 = '9.0 CONTINUING EDUCATION HOURS 22';

// aday
$pdf->SetFontSize('11'); // same size as template.docx
$pdf->SetXY(1.1, 11.75);
$pdf->Cell($pdf->GetPageWidth() - 2.2, 0.5, $lblDate1, 0, 1, 'C');
$pdf->SetXY(1.1, 12.25);
$pdf->Cell($pdf->GetPageWidth() - 2.2, 0.5, $lblDate2, 0, 1, 'C');


    
		// Close and output PDF document
    $pdf->Output();