<?php
    // $b = $me->getBeneficiaryCurrentlyViewing();
    // $pdf = new PDF();

    // Logo
    // if($b->isEddy()) {
    //     // No logo for Eddy beneficiaries
    //     $pdf->Ln(17);
    // } else {
    //     $pdf->Image('assets/img/logo-2x.png', 80, null, 50, 0);
    //     $pdf->Ln(5);
    // }

    // NOTE: Use FPDF instead of custom PDF class
    // getBeneficiaryCurrentlyViewing() not provided
    require_once('../fpdf184/fpdf.php');
    $pdf = new FPDF();

	// Add a page
    $pdf->AddPage();
    $pdf->SetDrawColor(0, 0, 0);

    // Retrieve JSON data and set default values
    $jsonfilepath = 'drf_sampledata.json';
    $jsonfile = file_get_contents($jsonfilepath);
    $data = json_decode($jsonfile);

    $todaydate = isset($data->drfdata->todaydate) ? $data->drfdata->todaydate : '';
    $beneficiary = isset($data->drfdata->beneficiary) ? $data->drfdata->beneficiary : '';
    $requestedby = isset($data->drfdata->requestedby) ? $data->drfdata->requestedby : '';
    $amount = isset($data->drfdata->amount) ? $data->drfdata->amount : '';
    $purpose = isset($data->drfdata->purpose) ? $data->drfdata->purpose : '';
    $checkpayableto = isset($data->drfdata->checkpayableto) ? $data->drfdata->checkpayableto : '';
    $address = isset($data->drfdata->address) ? $data->drfdata->address : '';
    $signature = isset($data->drfdata->signature) ? $data->drfdata->signature : '';

    // Format amount as currency
    $zeroValue = '$0.00';
    if($amount == $zeroValue)
        $amount = $zeroValue;
    if(intval($amount) == 0)
        $amount = $zeroValue;
    else
        $amount = '$' . number_format($amount, 2);

    // Instructions box
    $pdf->Line(20, $pdf->GetY(), 185, $pdf->GetY());
    $pdf->Ln(2);
    $pdf->SetFont('Times', 'I', 10);
    $pdf->SetX(20);
    $pdf->MultiCell(165, 4, 'Directions: Complete Section 1 below and sign. Attach documentation: lease, invoice, receipt, or price quote. Incomplete or unsigned requests will not be processed. Submit requests to NYSARC Trust Services. Allow 2 business days after receipt to update your account records. Please allow adequate time for processing and mailing. Please plan accordingly.', 0, 'J');
    $pdf->Ln(2);
    $pdf->Line(20, $pdf->GetY(), 185, $pdf->GetY());

    $pdf->Ln(2);
    $pdf->SetFont('Times', 'B', 16);
    // $pdf->MultiCell(190, 10, $b->getTrustType() . ' - Disbursement Request Form', 0, 'C');
    $pdf->MultiCell(190, 10, 'Community II - Disbursement Request Form', 0, 'C');

    // NOTE: Skip barcode for this sample
    // $pdf->Code39(140, $pdf->GetY() + 5, '12345', 1, 20);
    // $pdf->SetFont('', '', 18);
    // $pdf->text(150, $pdf->GetY() + 32, '12345');


    //
    // PRINT FORM VALUES
    // $pdf->Cell(w, h, text, border, ln, align)
    // Use 'B' for bottom border; or 0 no border
    //
    $lineHeight = 10;
    $labelStartX = 20;

    $pdf->Ln(25);
    $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(40, $lineHeight, "Today's Date:", 0, 0, 'L');
    $pdf->Cell(60, $lineHeight, $todaydate, 0, 1, 'L');

    $pdf->Ln(8);
    $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(50, $lineHeight, "Beneficiary Name:", 0, 0, 'L');
    $pdf->Cell($pdf->GetPageWidth() - $pdf->GetX() - $labelStartX * 1.5, $lineHeight, $beneficiary, 0, 1, 'L');

    $pdf->Ln(8);
    $pdf->SetFont('', '', 12);
    $pdf->text(20, $pdf->GetY(), "Please complete the following information regarding the disbursement:");

    $pdf->Ln(1);
    $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(40, $lineHeight, "Requested By:", 0, 0, 'L');
    $pdf->Cell($pdf->GetPageWidth() - $pdf->GetX() - $labelStartX * 1.5, $lineHeight, $requestedby, 'B', 1, 'L');

    $pdf->Ln(6);
    $pdf->SetFont('', 'I', 14);
    $pdf->text(20, $pdf->GetY(), "(If other than Beneficiary, must be authorized on the Joinder Agreement)");

    $pdf->Ln(1);
    $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(65, $lineHeight, "Amount of Request:", 0, 0, 'L');
    $pdf->Cell(50, $lineHeight, $amount, 'B', 1, 'L');

    // $pdf->Ln(1);
    // $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(65, $lineHeight, "Purpose of Request:", 0, 0, 'L');
    $pdf->MultiCell($pdf->GetPageWidth() - $pdf->GetX() - $labelStartX * 1.5, $lineHeight, $purpose, 'B','L');

    // $pdf->Ln(1);
    // $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(65, $lineHeight, "Make check payable to:", 0, 0, 'L');
    $pdf->Cell($pdf->GetPageWidth() - $pdf->GetX() - $labelStartX * 1.5, $lineHeight, $checkpayableto, 'B', 1, 'L');

    // $pdf->Ln(1);
    // $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(30, $lineHeight, "Address:", 0, 0, 'L');
    $pdf->MultiCell($pdf->GetPageWidth() - $pdf->GetX() - $labelStartX * 1.5, $lineHeight, $address, 'B','L');

    // $pdf->Ln(1);
    // $pdf->SetFont('', '', 18);
    $pdf->SetX($labelStartX - 1);
    $pdf->Cell(30, $lineHeight, "Signature:", 0, 0, 'L');
    $pdf->Cell(85, $lineHeight, $signature, 'B', 1, 'L');


    //
    // PRINT DISCLAIMER AND FOOTER
    //
    $pdf->Ln(10);
    $pdf->SetFont('', '', 10);
    $disclaimers = [
        "SIGNOR AGREES TO THE FOLLOWING",
        "1) I am the Beneficiary and/or a contact authorized to request disbursements for this account.",
        "2) Proof of payment is required prior to reimbursement.",
        "3) Requested disbursement is an actual expense for the sole benefit of this Beneficiary.",
        "4) It is the sole responsibility of the Beneficiary or their representative to determine the impact of this disbursement on",
        "continuing eligibility for governmental benefits.",
        "5) Repayment will be sought for duplicate disbursements or disbursements issued after the death of the Beneficiary.",
        "6) Requests and supporting documentation must be received prior to the death of the beneficiary.",
    ];
    foreach($disclaimers AS $line) {
        $pdf->text(20, $pdf->GetY(), $line);
        $pdf->Ln(5);
    }
    $pdf->SetY($pdf->GetY() - 2);
    $pdf->SetLineWidth(0.75);
    $pdf->Line(20, $pdf->GetY(), 180, $pdf->GetY());

    $pdf->Ln(10);
    $topOfBottom = $pdf->GetY();
    $pdf->Ln(3);

    // Bottom left
    $pdf->SetFont('', 'B', 11);
    /**
    $boxTexts = [
        "Mail to:",
        COMPANY_NAME,
        COMPANY_ADDRESS,
        COMPANY_CSZ,
        "",
        "Phone: (518) 439-8323",
        "Fax: (518) 439-2670"
    ];
    **/
    $boxTexts = [
        "Mail to:",
        "NYSARC Trust Services",
        "P.O. Box 1531",
        "Latham, NY 12110",
        "",
        "Phone: (518) 439-8323",
        "Fax: (518) 439-2670"
    ];

    // $boxTexts = [];
    // $boxTexts[] = "Mail to: ";
    // foreach($b->getDisbursementRequestAddressParts() AS $addressLine) {
    //     $boxTexts[] = $addressLine;
    // }
    // $boxTexts[] = "NYSARC Trust Services";
    // $boxTexts[] = "P.O. Box 1531";
    // $boxTexts[] = "Latham, NY 12110";
    // $boxTexts[] = "";
    // $boxTexts[] = "Phone:  (518) 439-8323";
    // $boxTexts[] = "Fax: (518) 439-2670";
    // $boxTexts[] = "Phone: " . $b->getDepositPhone();
    // $boxTexts[] = "Fax: " . $b->getDepositFax();

    foreach($boxTexts AS $boxText) {
        $pdf->text(13, $pdf->GetY(), $boxText);
        $pdf->Ln(5);
    }
    $pdf->SetLineWidth(0.25);
    $pdf->Rect(10, $topOfBottom - 5, 47, 45);

    // Middle
    $pdf->SetY($topOfBottom);
    $pdf->SetFont('', 'B', 14);
    $pdf->text(60, $pdf->GetY(), "TRUST SERVICES USE ONLY:");
    $pdf->SetFont('', '', 9);
    $pdf->Ln(10);

    $smallTexts = [
        "Employee ID No.: ________________",
        "Lease on file? Circle One (Yes) (No)",
        "Available Balance: \$______________",
        "Date Calculated: ______/_____/____",
        "Date Complete: ______/_____/____",
    ];
    foreach($smallTexts AS $smallText) {
        $pdf->text(60, $pdf->GetY(), $smallText);
        $pdf->Ln(6);
    }

    $pdf->SetY($topOfBottom + 10);
    $pdf->SetFont('', 'I', 12);
    $pdf->text(149, $pdf->GetY(), "Circle One");
    $pdf->SetFont('', '', 12);

    $boxTexts = [
        "Special handling?                    (Yes) (No)",
        "Type of handling: ______________________",
        "NOTES: _____________________________",
        "_____________________________________",
        "_____________________________________",
    ];
    foreach($boxTexts AS $boxText) {
        $pdf->text(118, $pdf->GetY(), $boxText);
        $pdf->Ln(7);
    }
    $pdf->Rect(115, $topOfBottom + 3, 88, 40);

	// Close and output PDF document
    // Choose "I" or "D". I: view on browser. D: download directly
    $pdf->Output('form.pdf', 'I');