<?php
    use setasign\Fpdi\Fpdi;
    use setasign\Fpdi\PdfReader;

    require_once('../fpdf184/fpdf.php');
    require_once('../fpdf184/src/autoload.php');

    // Use FPDI instead of FPDF to import empty pdf template
    // Page size: letter (landscape)
    $pdf = new FPDI('l', 'cm', array(21.6, 27.9));
    $pageCount = $pdf->setSourceFile('certificate_template.pdf');
    // Use imported template as new page
    $tpl = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tpl);
    // Georgia font is not provided. Times New Roman is used by default
    $pdf->SetFont('Times');
    // Keep same size as Template.docx
    $nameFontSize = '15';
    $dateFontSize = '11';

    // Retrieve JSON data and set default values
    $jsonfilepath = 'certificate_sampledata.json';
    $jsonfile = file_get_contents($jsonfilepath);
    $data = json_decode($jsonfile);

    $name = isset($data->name) ? $data->name : '';
    $year = isset($data->year) ? $data->year : date('Y');
    // 'n' and 'j' = months and days without leading zeros
    $month = isset($data->month) ? $data->month : date('n');
    $day = isset($data->day) ? $data->day : date('j');

    // Set date labels based on month and day numbers
    // Ordinal x position
    $day = intval($day);
    $month = intval($month);
    $months = array(
        array("name"=>"JANUARY",    "ordinal"=>12.08),
        array("name"=>"FEBRUARY",   "ordinal"=>11.95),
        array("name"=>"MARCH",      "ordinal"=>12.28),
        array("name"=>"APRIL",      "ordinal"=>12.42),
        array("name"=>"MAY",        "ordinal"=>12.54),
        array("name"=>"JUNE",       "ordinal"=>12.48),
        array("name"=>"JULY",       "ordinal"=>12.48),
        array("name"=>"AUGUST",     "ordinal"=>12.18),
        array("name"=>"SEPTEMBER",  "ordinal"=>11.85),
        array("name"=>"OCTOBER",    "ordinal"=>12.08),
        array("name"=>"NOVEMBER",   "ordinal"=>11.9),
        array("name"=>"DECEMBER",   "ordinal"=>11.95),
    );
    $lblMonth = $months[$month - 1]["name"];
    $lblOrdinal = 'TH';
    switch ($day) {
        case 1:
        case 21:
        case 31:
            $lblOrdinal = 'ST';
            break;
        case 2:
        case 22:
            $lblOrdinal = 'ND';
            break;
        case 3:
        case 23:
            $lblOrdinal = 'RD';
            break;
    }

    //
    // PRINT VALUES
    // $pdf->Cell(w, h, text, border, ln, align)
    //
    $pdf->SetFontSize($nameFontSize);
    $pdf->SetXY(1.1, 8.1);
    $pdf->Cell($pdf->GetPageWidth() - 2.2, 1, strtoupper($name), 0, 1, 'C');

    $lblDate1 = 'THIS ' . $day . '     DAY OF ' . $lblMonth . ' ' . $year;
    $lblDate2 = '9.0 CONTINUING EDUCATION HOURS';

    $pdf->SetFontSize($dateFontSize);
    $pdf->SetXY(1.1, 11.75);
    $pdf->Cell($pdf->GetPageWidth() - 2.2, 0.5, $lblDate1, 0, 1, 'C');
    $pdf->SetXY(1.1, 12.25);
    $pdf->Cell($pdf->GetPageWidth() - 2.2, 0.5, $lblDate2, 0, 0, 'C');
    $pdf->SetFontSize($dateFontSize - 3);

    $x = strlen($lblDate1);
    $xOrdinal = $day < 10 ? $months[$month - 1]["ordinal"] : $months[$month - 1]["ordinal"] + 0.1;
    $pdf->SetXY($xOrdinal, 11.7);
    $pdf->Cell(0.5, 0.5, $lblOrdinal, 0, 1, 'L');

	// Close and output PDF document
    // Choose "I" or "D". I: view on browser. D: download directly
    $pdf->Output('certificate.pdf', 'I');