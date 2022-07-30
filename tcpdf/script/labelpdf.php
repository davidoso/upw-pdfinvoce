<?php
// --------------------------------------------------------------------------------------
// Author:				David Osorio, jdavidosori96@gmail.com
// Upwork profile:		https://www.upwork.com/freelancers/~010be696c9ded003b5
// Date:				July 2022
// PHP version:			7.2.3
// --------------------------------------------------------------------------------------

// SETUP 1/2: Change path to include main TCPDF library and customize defined constants on config/tcpdf_config.php
require_once('../tcpdf_include.php');

// Not needed
// Extend TCPDF with custom functions, eg. Header() Footer() and MultiRow()
// require_once('../tcpdf_custom.php');

// Keep some constants from config/tcpdf_config.php
// and "PDF_LABEL_CONSTANT" used for Dymo LabelWriter 450 label 30334 custom page size: 2-1/8" x 1-1/8"
require_once('../config/tcpdf_config_label.php');

// SETUP 2/2: Customize input parameters
/**
 * @param config			Associative array, contains 8 keys:
	* @param jsonfilepath	JSON filepath to fetch data from (required)
 	* @param outputName		Output PDF filename
 	* @param outputMode		Choose "I" or "D". I: view on browser. D: download directly
	* @param showTopBorder	Top separator line below "The Scarab" title. true / false. Default true
	* @param showLOPCBottom		"LOPC" label above price tag and barcode. true / false. Default true
	* @param showBarcodeBorder	Rectangle border around barcode: true / false. Default false
	* @param alignBarcode		Align barcode at the bottom: "L", "C" or "R". Default "C"
	* @param alignPrice			Align price label above barcode: "L", "C" or "R". Default "R"
**/
// Sample 1.pdf
$config = array(
	"jsonfilepath" => "jsonSamples/test2_label.json",
	"outputName" => "label.pdf",
	"outputMode" => "I",
	"showTopBorder" => true,
	"showLOPCBottom" => true,
	"showBarcodeBorder" => false,
	"alignBarcode" => "C",
	"alignPrice" => "R",
);

// Sample 2.pdf
// $config = array(
// 	"jsonfilepath" => "jsonSamples/test2_label.json",
// 	"outputName" => "label.pdf",
// 	"outputMode" => "I",
// 	"showTopBorder" => false,
// 	"showLOPCBottom" => false,
// 	"showBarcodeBorder" => true,
// 	"alignBarcode" => "L",
// 	"alignPrice" => "C",
// );


/**
 *
 * The following code has been tested successfully with JSON samples
 * Please do not modify it unless you understand what you are doing
 *
**/


// Output: PDF file created with TCPDF
createPDF($config);


function createPDF($config) {
	if(isset($config['jsonfilepath'])) {
		// JSON parameters
		$jsonfilepath = $config['jsonfilepath'];
		// Config parameters
		$outputName = isset($config['outputName']) ? $config['outputName'] : 'label.pdf';
		$outputMode = isset($config['outputMode']) ? $config['outputMode'] : 'I';
		$showTopBorder = isset($config['showTopBorder']) ? $config['showTopBorder'] : true;
		$showLOPCBottom = isset($config['showLOPCBottom']) ? $config['showLOPCBottom'] : true;
		$showBarcodeBorder = isset($config['showBarcodeBorder']) ? $config['showBarcodeBorder'] : false;
		$alignBarcode = isset($config['alignBarcode']) ? $config['alignBarcode'] : 'C';
		$alignPrice = isset($config['alignPrice']) ? $config['alignPrice'] : 'R';

		// Retrieve JSON data
		$jsonfile = file_get_contents($jsonfilepath);
		$data = json_decode($jsonfile);

		// Create new CUSTOM PDF document
		// $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Create new DEFAULT PDF document
		$pdf = new TCPDF(PDF_LABEL_PAGE_ORIENTATION, PDF_LABEL_UNIT, PDF_LABEL_PAGE_FORMAT, true, 'UTF-8', false);

		// Set document information
		$pdf->SetCreator(PDF_LABEL_CREATOR);
		$pdf->SetAuthor(PDF_LABEL_AUTHOR);
		$pdf->SetTitle(PDF_LABEL_TITLE);

		// Hide default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// Set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_LABEL_FONT_NAME_MAIN, '', PDF_LABEL_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_LABEL_FONT_NAME_DATA, '', PDF_LABEL_FONT_SIZE_DATA));

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins (default)
		$pdf->SetMargins(PDF_LABEL_MARGIN_LEFT, PDF_LABEL_MARGIN_TOP, PDF_LABEL_MARGIN_RIGHT, true);
		$pdf->SetHeaderMargin(PDF_LABEL_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_LABEL_MARGIN_FOOTER);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_LABEL_MARGIN_BOTTOM);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Add a page
		$pdf->AddPage();

		//
		// GET DATA AND DEFAULT VALUES
		//
		$rugnumber = isset($data->labeldata->rugnumber) ? $data->labeldata->rugnumber : '';
		$quality = isset($data->labeldata->quality) ? $data->labeldata->quality : '';
		$design = isset($data->labeldata->design) ? $data->labeldata->design : '';
		$content = isset($data->labeldata->content) ? $data->labeldata->content : '';
		$country = isset($data->labeldata->country) ? $data->labeldata->country : '';
		$size = isset($data->labeldata->size) ? $data->labeldata->size : '';
		$color = isset($data->labeldata->color) ? $data->labeldata->color : '';
		$lopctop = isset($data->labeldata->lopctop) ? $data->labeldata->lopctop : '';
		$lopcbottom = isset($data->labeldata->lopcbottom) ? $data->labeldata->lopcbottom : '';
		$pricetag = isset($data->labeldata->pricetag) ? $data->labeldata->pricetag : '';

		//
		// SET LABEL TITLE
		//
		// Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='',
		// $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		//
		$titleWidth = $pdf->getPageWidth() - PDF_LABEL_MARGIN_LEFT - PDF_LABEL_MARGIN_RIGHT;
		$labelCellWidth = 10;
		$valueCellWidth = $labelCellWidth * 2.75;
		$cellHeight = 1;

		$pdf->SetX(PDF_LABEL_MARGIN_LEFT);
		$pdf->SetFont('helvetica', 'B', PDF_LABEL_FONT_SIZE_MAIN);
		$pdf->setCellPaddings(0, 0, 0, 0);
		$pdf->SetLineStyle( array('width' => 0.1, 'color' => array(80, 80, 80)) );
		if($showTopBorder)
			$pdf->Cell($titleWidth, 0, 'The Scarab', 'B', 1, 'L', 0, '', 0);
		else
			$pdf->Cell($titleWidth, 0, 'The Scarab', 0, 1, 'L', 0, '', 0);

		//
		// SET LABEL DATA (LEFT AND RIGHT COLUMNS)
		//
		// Each column contains label/value
		$pdf->SetFont('helvetica', 'R', PDF_LABEL_FONT_SIZE_DATA);
		// Set left padding after :
		$pdf->setCellPaddings(1, 0, 0, 0);
		$x_index = $pdf->GetX();
		$x_index_col2 = $x_index + $valueCellWidth + $labelCellWidth;
		$y_index = $pdf->GetY();

		// Print Rug #. Row 1. Left column
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Rug #:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $rugnumber, 0, 1, 'L', 0, '', 0);

		// Print LOPC top. Row 1. Right column
		$pdf->SetXY($x_index_col2, $y_index);
		$pdf->Cell($valueCellWidth - $labelCellWidth, $cellHeight, $lopctop, 0, 1, 'R', 0, '', 0);

		// Print Quality. Row 2. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Quality:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $quality, 0, 1, 'L', 0, '', 0);

		// Print Design. Row 3. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Design:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $design, 0, 1, 'L', 0, '', 0);

		// Print Content. Row 4. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Content:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $content, 0, 1, 'L', 0, '', 0);

		// Print Country. Row 5. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Country:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, strtoupper($country), 0, 1, 'L', 0, '', 0);

		// Print Size. Row 6. Left column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Size:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $size, 0, 1, 'L', 0, '', 0);

		// Print LOPC bottom. Row 6. Right column
		if($showLOPCBottom) {
			$pdf->SetXY($x_index_col2, $y_index);
			$pdf->Cell($valueCellWidth - $labelCellWidth, $cellHeight, $lopcbottom, 0, 1, 'R', 0, '', 0);
		}

		// Print Color. Row 7. Left column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($labelCellWidth, $cellHeight, 'Color:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $labelCellWidth, $y_index);
		$pdf->Cell($valueCellWidth, $cellHeight, $color, 0, 1, 'L', 0, '', 0);

		//
		// SET PRICE
		//
		// Print Price tag. Row 7. Right column
		$pdf->SetXY($x_index_col2, $y_index);
		$pdf->SetFont('helvetica', 'B', PDF_LABEL_FONT_SIZE_MAIN);
		// Choose align: "L", "C" or "R"
		// Choose border: 0: none; 1: all; or T: Top / B: Bottom / L: Left / R: Right
		$pdf->Cell($valueCellWidth - $labelCellWidth, $cellHeight,
			formatCurrency($pricetag, '$0.00'), 0, 1, $alignPrice, 0, '', 0);

		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index_col2, $y_index);

		//
		// SET BARCODE (CODE 39)
		//
		// Examples
		// How to use $pdf->write1DBarcode() and style parameters
		// https://hooks.wbcomdesigns.com/reference/classes/tcpdf/write1dbarcode
		//
		// Add barcode support: tcpdf_barcodes_1d.php
		// https://tcpdf.org/examples/example_027
		//
		// Add QR support: tcpdf_barcodes_2d.php
		// https://tcpdf.org/examples/example_050
		//
		if($rugnumber != '') {
			// Define barcode styles and position
			$style = array(
				'position' => '',
				'align' => 'C',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'hpadding' => 'auto',
				'vpadding' => 'auto',
				'fgcolor' => array(0, 0, 0),		// Default black RGB array(0, 0, 0)
				'bgcolor' => array(255, 255, 255),	// Default white RGB array(255, 255, 255)
				'text' => false,					// Show text
				'border' => $showBarcodeBorder,		// Show border
				'font' => 'helvetica',
				'fontsize' => 8,
				'stretchtext' => 4
			);
			$barcode_y = $pdf->GetY();
			$barcode_width = $labelCellWidth * 3;
			switch($alignBarcode) {
                case 'L': // Bottom Left
					$barcode_x = PDF_LABEL_MARGIN_LEFT;
					break;
                case 'R': // Bottom Right
					$barcode_x = $pdf->getPageWidth() - PDF_LABEL_MARGIN_RIGHT - $barcode_width;
					break;
                default: // Bottom Center
					$barcode_x = ($pdf->getPageWidth() - $barcode_width) / 2;
              }
			// Print barcode
			$pdf->SetFont('helvetica', 'R', 8);
			$pdf->write1DBarcode($rugnumber, 'C39', $barcode_x, $barcode_y, $barcode_width, 6, 0.4, $style, 'N');
		}

		// Close and output PDF document
		$pdf->Output($outputName, $outputMode);
	}
}

function formatCurrency($val, $zeroValue) {
	if($val == $zeroValue)
		return $zeroValue;
	if(intval($val) == 0)
		return $zeroValue;
	else
		return '$' . number_format($val, 2);
}
?>