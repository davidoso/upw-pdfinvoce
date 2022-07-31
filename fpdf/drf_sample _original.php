<?php

    $b = $me->getBeneficiaryCurrentlyViewing();
    
    $pdf = new PDF();
    
    $pdf->AddPage();
    $pdf->SetDrawColor(0,0,0);

    // logo
    if($b->isEddy()) {
        // no logo for Eddy beneficiaries
        $pdf->Ln(17);
    } else {        
        $pdf->Image('assets/img/logo-2x.png',80,null,50,0);
        $pdf->Ln(5);
    }

    // instructions box
    $pdf->Line(20,$pdf->GetY(),185,$pdf->GetY());
    $pdf->Ln(2);
    $pdf->SetFont('Times','I',10);
    $pdf->SetX(20);
    $pdf->MultiCell(165,4,"Directions: Complete Section 1 below and sign. Attach documentation: lease, invoice, receipt, or price quote. Incomplete or unsigned requests will not be processed. Submit requests to NYSARC Trust Services. Allow 2 business days after receipt to update your account records. Please allow adequate time for processing and mailing. Please plan accordingly.",0,'J');
    $pdf->Ln(2);
    $pdf->Line(20,$pdf->GetY(),185,$pdf->GetY());

    $pdf->Ln(2);
    $pdf->SetFont('Times','B',16);
    $pdf->MultiCell(190,10,$b->getTrustType() . " - Disbursement Request Form",0,'C');

    // bar code
    $pdf->Code39(140,$pdf->GetY()+5,"12345",1,20);

    $pdf->SetFont('','',18);
    $pdf->text(150,$pdf->GetY()+32,"12345");        


    $pdf->SetFont('','',18);

    $pdf->Ln(25);
    $pdf->text(20,$pdf->GetY(),"Today's Date:");
    $pdf->Line(60,$pdf->GetY(),100,$pdf->GetY());

    $pdf->Ln(8);
    //$pdf->text(20,$pdf->GetY(),$b->getAccountingCode());

    $pdf->Ln(8);
    $pdf->text(20,$pdf->GetY(),"Beneficiary Name:");
    $pdf->SetFont('','');
    $pdf->text(70,$pdf->GetY(),"Jane Smith");
    
    $pdf->Ln(8);
    $pdf->SetFont('','',12);
    $pdf->text(20,$pdf->GetY(),"Please complete the following information regarding the disbursement:");

    $pdf->Ln(8);
    $pdf->SetFont('','',18);
    $pdf->text(20,$pdf->GetY(),"Requested By:");
    $pdf->Line(60,$pdf->GetY(),150,$pdf->GetY());

    $pdf->Ln(6);
    $pdf->SetFont('','I',14);
    $pdf->text(20,$pdf->GetY(),"(If other than Beneficiary, must be authorized on the Joinder Agreement)");

    $pdf->Ln(8);
    $pdf->SetFont('','',18);
    $pdf->text(20,$pdf->GetY(),"Amount of Request:   $");
    $pdf->Line(80,$pdf->GetY(),130,$pdf->GetY());

    $pdf->Ln(8);
    $pdf->text(20,$pdf->GetY(),"Purpose of Request:");
    $pdf->Line(80,$pdf->GetY(),180,$pdf->GetY());

    $pdf->Ln(8);
    $pdf->text(20,$pdf->GetY(),"Make check payable to:");
    $pdf->Line(80,$pdf->GetY(),180,$pdf->GetY());

    $pdf->Ln(8);
    $pdf->text(20,$pdf->GetY(),"Address:");
    $pdf->Line(45,$pdf->GetY(),180,$pdf->GetY());
    $pdf->Ln(8);
    $pdf->Line(45,$pdf->GetY(),180,$pdf->GetY());
    $pdf->Ln(8);
    $pdf->Line(45,$pdf->GetY(),180,$pdf->GetY());
    
    $pdf->Ln(8);
    $pdf->text(20,$pdf->GetY(),"Signature:");
    $pdf->Line(48,$pdf->GetY(),130,$pdf->GetY());

    $pdf->Ln(10);
    $pdf->SetFont('','',10);
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
        $pdf->text(20,$pdf->GetY(),$line);
        $pdf->Ln(5);
    }
    $pdf->SetY( $pdf->GetY() - 2 );
    $pdf->SetLineWidth(0.75);
    $pdf->Line(20,$pdf->GetY(),180,$pdf->GetY());
    
    $pdf->Ln(10);
    
    $topOfBottom = $pdf->GetY();
    
    $pdf->Ln(3);
    
    // bottom left
    $pdf->SetFont('','B',11);
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
    $boxTexts = [];
    $boxTexts[] = "Mail to:";
    foreach($b->getDisbursementRequestAddressParts() AS $addressLine) {
        $boxTexts[] = $addressLine;
    }
    $boxTexts[] = "";
    $boxTexts[] = "Phone: " . $b->getDepositPhone();
    $boxTexts[] = "Fax: " . $b->getDepositFax();
    
    foreach($boxTexts AS $boxText) {
        $pdf->text(13,$pdf->GetY(),$boxText);
        $pdf->Ln(5);
    }
    
    $pdf->SetLineWidth(0.25);
    $pdf->Rect(10,$topOfBottom-5,47,45);
    
    // middle
    $pdf->SetY($topOfBottom);
    $pdf->SetFont('','B',14);
    $pdf->text(60,$pdf->GetY(),"TRUST SERVICES USE ONLY:");

    $pdf->SetFont('','',9);
    $pdf->Ln(10);        
    
    $smallTexts = [
        "Employee ID No.: ________________",
        "Lease on file? Circle One (Yes) (No)",
        "Available Balance: \$______________",
        "Date Calculated: ______/_____/____",
        "Date Complete: ______/_____/____",
    ];
    foreach($smallTexts AS $smallText) {
        $pdf->text(60,$pdf->GetY(),$smallText);
        $pdf->Ln(6);        
    }
    
    $pdf->SetY($topOfBottom+10);
    $pdf->SetFont('','I',12);
    $pdf->text(149,$pdf->GetY(),"Circle One");
    
    $pdf->SetFont('','',12);
    $boxTexts = [
        "Special handling?                    (Yes) (No)",
        "Type of handling: ______________________",
        "NOTES: _____________________________",
        "_____________________________________",
        "_____________________________________",
    ];
    foreach($boxTexts AS $boxText) {
        $pdf->text(118,$pdf->GetY(),$boxText);
        $pdf->Ln(7);        
    }
    $pdf->Rect(115,$topOfBottom+3,88,40);

    
    $pdf->Output();
