<?php
class MYPDF extends TCPDF {

        //Page header
        public function Header() {

            //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

            // Set font
            // $this->SetFont('helvetica', 'B', 20);
            $this->SetFont('helvetica', 'B', PDF_FONT_SIZE_MAIN);
            $this->setCellPaddings(PDF_MARGIN_LEFT, 0, 0, 0);
            $this->Cell(0, 0, 'The Scarab', 0, 1, 'L', 0, '', 0);
            // $this->Cell(120, 5, 'The Scarab', 1, 'L', 0, 1);
            // $this->setCellPaddings(2, 2, 0, 0);
            // $this->Ln(2);

            $this->SetTextColor(66,66,66);
            // $this->SetTextColor(255,152,0);
            $this->SetFont('helvetica', 'R', PDF_FONT_SIZE_MAIN);
            // $this->setCellPaddings(2, 0, 0, 0);
            // $this->Cell(120, 5, 'The Scarab', 1, 'L', 0, 1);

            // Title
            // $this->Cell(0, 30, 'Adrress here', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        // $this->SetX(PDF_MARGIN_LEFT * 2);

            $this->Cell(0, 0, '201 Main Street | P.O. Box 579', 0, 1, 'L', 0, '', 0);
            $this->Cell(0, 0, 'Minturn, CO 81647', 0, 1, 'L', 0, '', 0);
            $this->Cell(0, 0, '970-949-1730', 0, 1, 'L', 0, '', 0);
            $this->Cell(0, 0, 'info@thescarab.com', 0, 1, 'L', 0, '', 0);
            $this->setCellPaddings(PDF_MARGIN_LEFT, 0, 0, 2);
            // $this->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
            $this->Ln(1);

            // This keeps contact info inside rectangle
            // Logo
            // $image_file = K_PATH_IMAGES.'logo.jpg';
            // $this->Image($image_file, 170, 5, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $this->SetLineStyle( array( 'width' => 0.3, 'color' => array(80, 80, 80)));
// $this->Rect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM);
        $this->RoundedRect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM, 3.50, '1111'/*, 'DF'*/);
            

        // Logo
        $image_file = K_PATH_IMAGES.'logo.jpg';
        $this->Image($image_file, 165, 5, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // $this->SetY($this->GetY() + 10);

        // $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
		// $this->SetHeaderMargin(PDF_MARGIN_HEADER);
		// $this->SetFooterMargin(PDF_MARGIN_FOOTER);

		// // Set auto page breaks
		// $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        }

    /*public function MultiRow($leftWidth, $left, $right) {
        // Disable auto page break if cell size can't fit on current page
        if($this->GetY() > 253) {
            $this->AddPage();
        }
        $page_start = $this->getPage();
        $y_start = $this->GetY();
		// Write the left cell
		$this->setCellPaddings(0, 2, 2, 0);
		$this->MultiCell($leftWidth, 10, $left, 1, 'R', 1, 2, '', '', true, 0);
        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();
        $this->setPage($page_start);
		// Write the right cell
		$this->setCellPaddings(2, 2, 0, 0);
		$this->MultiCell(0, $y_end_1 - $y_start, $right, 'TBR', 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();
        // Increase field title cell (left) in case value cell (right) is longer
        if($y_end_2 > $y_end_1 && $page_end_1 == $page_end_2) {
            // Write the left cell
            $this->SetXY(15, $y_start);
            $this->setCellPaddings(0, 2, 2, 0);
            $this->MultiCell($leftWidth, $y_end_2 - $y_start, '', 1, 'R', 1, 2, '', '', true, 0);
            $page_end_1 = $this->getPage();
            $y_end_1 = $this->GetY();
            $this->SetX(15);
        }
        // Set the new row position by case
        if(max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }
        $this->setPage(max($page_end_1, $page_end_2));
        $this->SetXY($this->GetX(), $ynew);
    }*/

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
}
?>
