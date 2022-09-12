<?php
    use setasign\Fpdi\Fpdi;
    use setasign\Fpdi\PdfReader;

    require_once('../fpdf184/fpdf.php');
    require_once('../fpdf184/src/autoload.php');

    // Use FPDI instead of FPDF to import empty pdf template
    $pdf = new FPDI('P', 'cm', array(21.6, 27.9));
    $pageCount = $pdf->setSourceFile('template/template.pdf');

    // Retrieve JSON data and set default values
    $jsonfilepath = 'sampledata.json';
    $jsonfile = file_get_contents($jsonfilepath);
    $data = json_decode($jsonfile);

    $beneficiary = isset($data->beneficiary) ? $data->beneficiary : '';
    $accountcode = isset($data->accountcode) ? $data->accountcode : '';
    $authorizedform = isset($data->authorizedform) ? $data->authorizedform : '';
    $amount = isset($data->amount) ? $data->amount : 0;
    $payableto = isset($data->payableto) ? $data->payableto : '';
    $accountnumber = isset($data->accountnumber) ? $data->accountnumber : '';
    $signature = isset($data->signature) ? $data->signature : '';
    $ckboxIdxApplication = isset($data->ckboxIdxApplication) ? $data->ckboxIdxApplication : 0;
    $ckboxIdxPayment = isset($data->ckboxIdxPayment) ? $data->ckboxIdxPayment : 0;
    $mailingaddress = isset($data->mailingaddress) ? $data->mailingaddress : 0;
    $mailingday = isset($data->mailingday) ? $data->mailingday : date('j'); // w/out leading zeros
    $effectiveDateDay = isset($data->effectiveDateDay) ? $data->effectiveDateDay : '';
    $effectiveDateMonth = isset($data->effectiveDateMonth) ? $data->effectiveDateMonth : '';
    $effectiveDateYear = isset($data->effectiveDateYear) ? $data->effectiveDateYear : '';
    $monthlyDeposit = isset($data->monthlyDeposit) ? $data->monthlyDeposit : 0;
    $nysarcFee = isset($data->nysarcFee) ? $data->nysarcFee : 0;
    $auditFee = isset($data->auditFee) ? $data->auditFee : 0;

    // Cast checkbox index as int
    // ckboxIdxApplication from 0 to 2. index 0 = NEW. 1 = CHANGE. 2 = STOP
    $ckboxIdxApplication = intval($ckboxIdxApplication);
    // ckboxIdxPayment from 0 to 5. index 0 = Rent / Mortgage / Condo. 1 = Car Loan / Lease. [...] 5 = PSEGLI
    $ckboxIdxPayment = intval($ckboxIdxPayment);

    // Format days and months
    if(intval($mailingday) < 10) {
        $mailingday = '0' . $mailingday;
    }
    if(intval($effectiveDateDay) < 10) {
        $effectiveDateDay = '0' . $effectiveDateDay;
    }
    if(intval($effectiveDateMonth) < 10) {
        $effectiveDateMonth = '0' . $effectiveDateMonth;
    }

    // Set total deposit
    $totalDeposit = intval($monthlyDeposit - $nysarcFee - $auditFee);

    // Format as currency
    $zeroValue = '$0.00';
    $amount = intval($amount) == 0 ? $zeroValue : '$' . number_format($amount, 2);
    $monthlyDeposit = intval($monthlyDeposit) == 0 ? $zeroValue : '$' . number_format($monthlyDeposit, 2);
    $nysarcFee = intval($nysarcFee) == 0 ? $zeroValue : '$' . number_format($nysarcFee, 2);
    $auditFee = intval($auditFee) == 0 ? $zeroValue : '$' . number_format($auditFee, 2);
    $totalDeposit = $totalDeposit == 0 ? $zeroValue : '$' . number_format($totalDeposit, 2);

    // Import page 1 and 2 from template
    for($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        // Add a page
        $tplIdx = $pdf->importPage($pageNo);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);

        //
        // Custom settings
        //
        $maxWidth = $pdf->GetPageWidth() - 2.3; // Right margin (.docx) is 1.3
        $mainFontSize = '12'; // Default font size 12
        $startX = 1.2; // Start every row at X (sheet 1)
        $startX2 = 14; // Start every row at X (sheet 2)
        $lh = 0.5; // Line height
        $chkSize = 0.4; // Checkbox width and height

        //
        // PRINT FORM VALUES
        // $pdf->Cell(w, h, text, border, ln, align)
        // Use 'B' for bottom border; or 0 no border
        //
        switch ($pageNo) {
            case 1:
                // Set beneficiary and account code
                $pdf->SetXY($startX, 4);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(3.6, $lh, 'Beneficiary Name: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell(8.5, $lh, strtoupper($beneficiary), 'B', 0, 'L');

                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(2.9, $lh, 'Account Code: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell($maxWidth - $pdf->GetX(), $lh, strtoupper($accountcode), 'B', 0, 'L');

                // Set submitting form
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 1.5);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(9.85, $lh, 'Authorized individual submitting form (please print): ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell($maxWidth - $pdf->GetX(), $lh, strtoupper($authorizedform), 0, 0, 'L');

                // Set checkbox 1
                $pdf->SetXY($startX, 6.9);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(6.75, $lh, 'Automatic Payment Application: ', 0, 0, 'L');
                $pdf->SetFont('Times', 'B', 10);
                $startXchk1 = [8.25, 10.8, 14.15];
                for ($i = 0; $i < 3; $i++) {
                    $pdf->SetXY($startXchk1[$i], 6.9);
                    // Enable flag 1 to show all borders to create box
                    if($i == $ckboxIdxApplication)
                        $pdf->Cell($chkSize, $chkSize, 'X', 1, 0, 'C');
                    else
                        $pdf->Cell($chkSize, $chkSize, '', 1, 0, 'C');
                }

                // Set checkbox 2
                $pdf->SetXY($startX, 8.75);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(4, $lh, 'Type of payment: ', 0, 0, 'L');
                $pdf->SetFont('Times', 'B', 10);
                $startYchk2 = $pdf->GetY() + 0.05;
                for ($i = 0; $i < 6; $i++) {
                    $pdf->SetXY(5.5, $startYchk2);
                    // Enable flag 1 to show all borders to create box
                    if($i == $ckboxIdxPayment)
                        $pdf->Cell($chkSize, $chkSize, 'X', 1, 0, 'C');
                    else
                        $pdf->Cell($chkSize, $chkSize, '', 1, 0, 'C');
                    $startYchk2 = $pdf->GetY() + $chkSize + 0.14;
                }

                // Set payment amount
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 2);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(9.25, $lh, 'Requested monthly Automatic Payment amount: ', 0, 0, 'L');
                // Comment out to keep bold text
                $pdf->SetFont('Times');
                // $pdf->Cell($maxWidth - $pdf->GetX(), $lh, $amount, 'B', 0, 'L');
                $pdf->Cell(4, $lh, $amount, 'B', 0, 'L');

                // Set mailing date day
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 3.75);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(4.6, $lh, 'Requested mailing date: ', 0, 0, 'L');
                // Comment out to keep bold text
                $pdf->SetFont('Times');
                $pdf->Cell(0.8, $lh, $mailingday, 'B', 0, 'C');
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(4, $lh, 'day of each month.', 0, 0, 'L');

                // Set Effective Date
                $pdf->Cell(6, $lh, 'Effective Date: ', 0, 0, 'R');
                // Comment out to keep bold text
                $pdf->SetFont('Times');
                $pdf->Cell(0.6, $lh, $effectiveDateDay, 'B', 0, 'C');
                $pdf->Cell(0.3, $lh, ' / ', 0, 0, 'C'); // Separator
                $pdf->Cell(0.6, $lh, $effectiveDateMonth, 'B', 0, 'C');
                $pdf->Cell(0.3, $lh, ' / ', 0, 0, 'C'); // Separator
                $pdf->Cell(0.6, $lh, $effectiveDateYear, 'B', 0, 'C');

                // Set payable to
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 1.5);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(4.5, $lh, 'Make check payable to: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell($maxWidth - $pdf->GetX(), $lh, strtoupper($payableto), 'B', 0, 'L');

                // Set account number
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 1.5);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(2.5, $lh, 'Account #: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell(6, $lh, strtoupper($accountnumber), 'B', 0, 'L');

                // Set mailing address
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 1.5);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(3.4, $lh, 'Mailing Address: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell($maxWidth - $pdf->GetX(), $lh, $mailingaddress, 'B', 0, 'L');

                // Set signature
                $pdf->SetXY($startX, $pdf->GetY() + $lh * 1.5);
                $pdf->SetFont('Times', 'B', $mainFontSize);
                $pdf->Cell(2.5, $lh, 'Signature*: ', 0, 0, 'L');
                $pdf->SetFont('Times');
                $pdf->Cell(6, $lh, $signature, 'B', 0, 'L');
                break;
            case 2:
                $pdf->SetFont('Times');
                $pdf->SetXY($startX2, 6.7);
                $pdf->Cell(2.5, $lh, $monthlyDeposit, 'B', 0, 'L');

                $pdf->SetXY($startX2 - 0.3, $pdf->GetY() + $lh);
                $pdf->Cell(0.3, $lh, '-', 0, 0, 'L');
                $pdf->Cell(2.5, $lh, $nysarcFee, 'B', 0, 'L');

                $pdf->SetXY($startX2 - 0.3, $pdf->GetY() + $lh);
                $pdf->Cell(0.3, $lh, '-', 0, 0, 'L');
                $pdf->Cell(2.5, $lh, $auditFee, 'B', 0, 'L');

                $pdf->SetXY($startX2, $pdf->GetY() + $lh * 2);
                $pdf->Cell(2.5, $lh, $totalDeposit, 'B', 0, 'L');
                $pdf->SetXY($startX2, $pdf->GetY() + 0.05); // Double bottom line
                $pdf->Cell(2.5, $lh, '', 'B', 0, 'L');
                break;
        } // switch
    } // for

	// Close and output PDF document
    // Choose "I" or "D". I: view on browser. D: download directly
    $pdf->Output('payment_application.pdf', 'I');