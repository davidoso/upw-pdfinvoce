<?php
class MYPDF extends TCPDF {

        //Page header
        public function Header() {

            //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

            // Set font
            // $this->SetFont('helvetica', 'B', 20);
            $this->SetFont('helvetica', 'B', 12);
            $this->setCellPaddings(2, 0, 2, 1);
            $this->Cell(0, 0, 'he Scarab', 0, 1, 'L', 0, '', 0);
            // $this->Cell(120, 5, 'The Scarab', 1, 'L', 0, 1);
            // $this->setCellPaddings(2, 2, 0, 0);
            // $this->Ln(2);

            $this->SetFont('helvetica', 'R', 10);
            // $this->Cell(120, 5, 'The Scarab', 1, 'L', 0, 1);

            // Title
            // $this->Cell(0, 30, 'Adrress here', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Cell(0, 0, 'Adrress here', 0, 1, 'L', 0, '', 0);
            $this->Cell(0, 0, 'Adrress here 2', 0, 1, 'L', 0, '', 0);
            // Logo
            $image_file = K_PATH_IMAGES.'logo.jpg';
            $this->Image($image_file, 180, 5, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $this->SetLineStyle( array( 'width' => 0.3, 'color' => array(80, 80, 80)));
// $this->Rect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM);
        $this->RoundedRect(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, $this->getPageHeight() - $this->GetY() - PDF_MARGIN_BOTTOM, 3.50, '1111'/*, 'DF'*/);
            
        }

    public function MultiRow($leftWidth, $left, $right) {
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
    }

    public function Footer() {
        $image_file = K_PATH_IMAGES . 'logo.jpg';
        // Set Sitefotos image
        // $this->Image($image_file, 20, 268, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            

        // Set footer font, position and style
        $this->SetFont('helvetica', 'BI', 8);
        $this->SetY(264.5);
        // Dont show line
		// $this->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(80, 80, 80)));
        // Set page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), '', false, 'R', 0, '', 0, false, 'T', 'M');
            $this->Ln(2);
        
        $this->SetFont('helvetica', 'R', 8);
$this->setCellPaddings(2, 0, 2, 1);
$this->Cell(0, 0, 'Terms', 0, 1, 'L', 0, '', 0);
$this->Cell(0, 0, 'All sales items are final. No returns after 10 days. Balance due on the date of sale', 0, 1, 'L', 0, '', 0);
            //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

    }
}
?>
