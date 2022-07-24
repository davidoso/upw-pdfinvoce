<?php
// --------------------------------------------------------------------------------------
// Author:				David Osorio, jdavidosori96@gmail.com
// Upwork profile:		https://www.upwork.com/freelancers/~010be696c9ded003b5
// Date:				July 2022
// PHP version:			7.2.3
// --------------------------------------------------------------------------------------

// SETUP 1/2: Change path to include main TCPDF library and customize defined constants on config/tcpdf_config.php
require_once('../config/tcpdf_config_label.php');
require_once('../tcpdf_include.php');
// Extend TCPDF with custom functions, eg. Header() Footer() and MultiRow()
// require_once('../tcpdf_custom.php');

// SETUP 2/2: Customize input parameters
/**
 * @param config			Associative array, contains 8 keys:
	* @param jsonfilepath	JSON filepath to fetch data from (required)
 	* @param outputName		Output PDF filename
 	* @param outputMode		Choose "I" or "D". I: view on browser. D: download directly
	* @param headerLogo		The Scarab logo on "images" folder
	* @param invoiceRowColorRGB		RGB color array for section: invoice
	* @param itemRowOddColorHEX		HEX color array for each odd row on table. Default #FFF white
	* @param itemRowEvenColorHEX	HEX color array for each even row on table. Default #E9ECEF light gray
	* @param tableHeaderColorHEX	HEX color array for table headers. Default #212529 dark
**/
$config = array(
	"jsonfilepath" => "jsonSamples/test2_label.json",
	"outputName" => "label.pdf",
	"outputMode" => "I",
);


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

		// Retrieve JSON data
		$jsonfile = file_get_contents($jsonfilepath);
		$data = json_decode($jsonfile);

		// Create new custom PDF document
		// $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Create new default PDF document
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

		// Set margins (custom rounded rect)
		// $pdf->SetMargins(PDF_MARGIN_LEFT + 2, PDF_LABEL_MARGIN_TOP + PDF_LABEL_MARGIN_FOOTER, PDF_LABEL_MARGIN_RIGHT, true);
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
		$pricetag = isset($data->labeldata->pricetag) ? $data->labeldata->pricetag : '-';
		
		//
		// SET LABEL TITLE
		//

		$titleWidth = $pdf->getPageWidth() - PDF_LABEL_MARGIN_LEFT - PDF_LABEL_MARGIN_RIGHT;
		$leftCellWidth = 8;
		$cellHeight = 1;

		$pdf->SetX(PDF_LABEL_MARGIN_LEFT);
        $pdf->SetFont('helvetica', 'B', PDF_LABEL_FONT_SIZE_MAIN);
        $pdf->setCellPaddings(0, 0, 0, 0);
		$pdf->SetLineStyle( array('width' => 0.1, 'color' => array(80, 80, 80)) );
        $pdf->Cell($titleWidth, 0, 'The Scarab', 'B', 1, 'L', 0, '', 0);

		//
		// SET LABEL DATA (LEFT AND RIGHT COLUMNS)
		//
		$pdf->SetFont('helvetica', 'R', PDF_LABEL_FONT_SIZE_DATA);
		$pdf->setCellPaddings(0, 0.5, 0.5, 0);
		$x_index = $pdf->GetX();
		$x_index_col2 = $x_index + $leftCellWidth * 3;
		$y_index = $pdf->GetY();

		// Print Rug #. Row 1. Left column
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Rug #:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $rugnumber, 0, 1, 'L', 0, '', 0);

		// Print LOPC top. Row 1. Right column
		$pdf->SetXY($x_index_col2, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, '', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index_col2 + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $lopctop, 0, 1, 'L', 0, '', 0);

		// Print Quality. Row 2. Left column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Quality:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $quality, 0, 1, 'L', 0, '', 0);

		// Print Color. Row 2. Right column
		$pdf->SetXY($x_index_col2, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Color:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index_col2 + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $color, 0, 1, 'L', 0, '', 0);

		// Print Design. Row 3. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Design:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $design, 0, 1, 'L', 0, '', 0);

		// Print Content. Row 4. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Content:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $content, 0, 1, 'L', 0, '', 0);

		// Print Country. Row 5. Left column. Skip right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Country:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, strtoupper($country), 0, 1, 'L', 0, '', 0);

		// Print Size. Row 6. Left column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, 'Size:', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $size, 0, 1, 'L', 0, '', 0);

		// Print LOPC bottom. Row 6. Right column
		$pdf->SetXY($x_index_col2, $y_index);
		$pdf->Cell($leftCellWidth, $cellHeight, '', 0, 1, 'R', 0, '', 0);
		$pdf->SetXY($x_index_col2 + $leftCellWidth, $y_index);
		$pdf->Cell($leftCellWidth * 2, $cellHeight, $lopcbottom, 0, 1, 'L', 0, '', 0);

		// Print Price tag. Row 7. Right column
		$y_index = $pdf->GetY();
		$pdf->SetXY($x_index_col2, $y_index);
		// $pdf->Cell($leftCellWidth, $cellHeight, '', 0, 1, 'R', 0, '', 0);
		// $pdf->SetXY($x_index_col2 + $leftCellWidth, $y_index);
        $pdf->SetFont('helvetica', 'B', PDF_LABEL_FONT_SIZE_MAIN);
		// Choose align: 'L', 'C' or 'R'
		// Choose border: 0: none; 1: all; or T: Top / B: Bottom / L: Left / R: Right
		// Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='',
		// $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$pdf->Cell($leftCellWidth * 3, $cellHeight, formatCurrency($pricetag, '-'), 0, 1, 'R', 0, '', 0);


		$y_index = $pdf->GetY();

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

function getTable($items, $itemRowEvenColorHEX, $itemRowOddColorHEX, $headerColor) {
	$datatotal = $items->total;
	$datalist = $items->list;

	// Add thead
	$headerStyle = 'border-top: 1px solid ' . $headerColor . ';';
	$firstHeaderStyle = 'border-left: 1px solid ' . $headerColor . '; border-top: 1px solid ' . $headerColor . ';';
	$lastHeaderStyle = 'border-right: 1px solid ' . $headerColor . '; border-top: 1px solid ' . $headerColor . ';';

	$tbl = '<table cellpadding="5">' .
	'<thead>' .
		'<tr style="color: #FFF; background-color: ' . $headerColor . '; font-weight: bold; font-size: 12px;">' .
			'<td width="100" align="center"	style="' . $firstHeaderStyle . '"><b>Image</b></td>' .
			'<td width="100" align="center"	style="' . $headerStyle . '"><b>Item</b></td>' .
			'<td width="160" align="center"	style="' . $headerStyle . '"><b>Description</b></td>' .
			'<td width="80" align="center"	style="' . $headerStyle . '"><b>Unit cost</b></td>' .
			'<td width="40" align="center"	style="' . $headerStyle . '"><b>Qty</b></td>' .
			'<td width="80" align="center"	style="' . $headerStyle . '"><b>Discount</b></td>' .
			'<td width="80" align="center"	style="' . $headerStyle . '"><b>Tax</b></td>' .
			'<td width="80" align="right"	style="' . $lastHeaderStyle . '"><b>Price</b></td>' .
		'</tr>' .
	'</thead>' .
	'<tbody style="font-size: 10px;">';

	// Add tbody
	$rowIndex = 0;
	foreach($datalist as $item) {
		$rowBg = (++$rowIndex % 2 == 0)
			? 'border: 1px solid #505050; background-color: ' . $itemRowEvenColorHEX . ';'
			: 'border: 1px solid #505050; background-color: ' . $itemRowOddColorHEX . ';';

		$imageurl = isset($item->imageurl) ? $item->imageurl : '';
		$rugid = isset($item->rugid) ? $item->rugid : '-';
		$description = isset($item->description) ? $item->description : '-';
		$qty = isset($item->qty) ? $item->qty : '0';
		$unitcost = isset($item->unitcost) ? $item->unitcost : '$0.00';
		$discount = isset($item->discount) ? $item->discount : '-';
		$tax = isset($item->tax) ? $item->tax : '$0.00';
		$price = isset($item->price) ? $item->price : '$0.00';

		$unitcost = formatCurrency($unitcost, '$0.00');
		$discount = formatCurrency($discount, '-');
		$tax = formatCurrency($tax, '$0.00');
		$price = formatCurrency($price, '$0.00');

		$tbl = $tbl .
		'<tr style="color: black;">' .
			($imageurl == ''
				// No image
				? '<td width="100" style="text-align: left; ' . $rowBg . '"></td>'
				// Show image
				: ('<td width="100" height="80" style="text-align: center; ' . $rowBg . '">' .
						'<img src="' . $imageurl . '" alt="" width="80"/>
					</td>'
				)
			) .
			'<td width="100" style="text-align: left; ' . $rowBg . '">' . $rugid . '</td>' .
			'<td width="160" style="text-align: left; ' . $rowBg . '">' . $description . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">' . $unitcost . '</td>' .
			'<td width="40" style="text-align: center; ' . $rowBg . '">' . $qty . '</td>' .
			// '<td width="80" style="text-align: ' .
			// 	($discount == '-' ? 'center' : 'right') . $rowBg . '">' . $discount . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">' . $discount . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">' . $tax . '</td>' .
			'<td width="80" style="text-align: right; ' . $rowBg . '">' . $price . '</td>' .
		'</tr> ';
	}

	// Add totals
	$totalStyle = 'text-align: right; border: 1px solid #505050;';
	$balanceStyle = $totalStyle . ' color: #FFF; background-color: ' . $headerColor .';';

	foreach($datatotal as $k => $v) {
		if($k != 'balance') {
			$tbl = $tbl .
			'<tr style="color: black;">' .
				'<td width="100"></td>' .
				'<td width="100"></td>' .
				'<td width="160"></td>' .
				'<td width="80"></td>' .
				'<td width="40"></td>' .
				// '<td width="80"></td>' .
				'<td width="160" style="' . $totalStyle . '">' . ucfirst($k) . '</td>' .
				'<td width="80" style="' . $totalStyle . '">' . formatCurrency($v, '$0.00') . '</td>' .
			'</tr>';
		}
	}

	// Add balance due
	$tbl = $tbl .
	'<tr style="color: black;">' .
		'<td width="100"></td>' .
		'<td width="100"></td>' .
		'<td width="160"></td>' .
		'<td width="80"></td>' .
		'<td width="40"></td>' .
		// '<td width="80"></td>' .
		'<td width="160" style="' . $balanceStyle . '"><b>Balance Due</b></td>' .
		'<td width="80" style="' . $balanceStyle . '"><b>' . formatCurrency($datatotal->balance, '$0.00') . '</b></td>' .
	'</tr>';

	// Close tags
	$tbl = $tbl .
	'</tbody> ' .
	'</table> ' ;

	return $tbl;
}
?>