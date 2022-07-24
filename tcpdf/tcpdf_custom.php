<?php
class MYPDF extends TCPDF {

    // Override default header
    public function Header() {
        // Set header font, position and style
        $this->SetFont('helvetica', 'B', PDF_FONT_SIZE_MAIN);
        $this->setCellPaddings(PDF_MARGIN_LEFT, 0, 0, 0);
        $this->Cell(0, 0, 'The Scarab', 0, 1, 'L', 0, '', 0);
        // $this->Ln(1);

        $this->SetTextColor(66,66,66);
        $this->SetFont('helvetica', 'R', PDF_FONT_SIZE_MAIN);
        $this->Cell(0, 0, '201 Main Street | P.O. Box 579', 0, 1, 'L', 0, '', 0);
        $this->Cell(0, 0, 'Minturn, CO 81647', 0, 1, 'L', 0, '', 0);
        $this->Cell(0, 0, '970-949-1730', 0, 1, 'L', 0, '', 0);
        $this->Cell(0, 0, 'info@thescarab.com', 0, 1, 'L', 0, '', 0);
        $this->setCellPaddings(PDF_MARGIN_LEFT, 0, 0, 2);
        // $this->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $this->Ln(1);

        $image_file = K_PATH_IMAGES.'logo.jpg';

        // Add logo
        // This keeps contact info inside rectangle
        // $this->Image($image_file, 165, 5, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetLineStyle( array('width' => 0.3, 'color' => array(80, 80, 80)) );
        // $this->Rect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM);
        $this->RoundedRect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM, 3.50, '1111'/*, 'DF'*/);

        // Add logo
        $this->Image($image_file, 165, 5, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }

    // Override default footer
    public function Footer() {
        // Set footer font, position and style
        $this->SetFont('helvetica', 'BI', PDF_FONT_SIZE_DATA);
        $this->SetTextColor(66, 66, 66);
        $this->SetY(264.5);

        // Set page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), '', false, 'R', 0, '', 0, false, 'T', 'M');
        $this->Ln(2);

        // Set disclaimer
        $this->SetFont('helvetica', 'R', PDF_FONT_SIZE_DATA);
        $this->setCellPaddings(2, 0, 2, 1);
        $this->Cell(0, 0, 'Terms', 0, 1, 'L', 0, '', 0);
        $this->Cell(0, 0, 'All sale items are final. No returns after 10 days. Balance due on the date of sale.', 0, 1, 'L', 0, '', 0);
    }

    // public function MultiRow($leftWidth, $left, $right) {
    // }
}
?>