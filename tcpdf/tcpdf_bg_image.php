<?php
class MYPDF extends TCPDF {
    // To add the same image on all pages, uses Header() function instead and harcode $img_file variable
    // public function Header() {
    public function AddBackgroundImage($image) {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();
        // Get current auto page break mode
        $auto_page_break = $this->AutoPageBreak;
        // Disable auto page break
        $this->SetAutoPageBreak(false, 0);
        // Set background image
        $img_file = K_PATH_IMAGES . $image;
        $this->Image($img_file, 0, 0, 69.85, 95.25, '', '', '', false, 300, '', false, false, 0);
        // Restore auto page break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // Set the starting point for the page content
        $this->setPageMark();
    }
}
?>