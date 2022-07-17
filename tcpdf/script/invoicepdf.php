<?php
// --------------------------------------------------------------------------------------
// Author:				David Osorio, jdavidosori96@gmail.com
// Upwork profile:		https://www.upwork.com/freelancers/~010be696c9ded003b5
// Date:				July 2022
// PHP version:			7.2.3
// --------------------------------------------------------------------------------------
// SETUP 1/2: Change path to include main TCPDF library and customize defined constants on config/tcpdf_config.php
require_once('../tcpdf_include.php');
// Extend TCPDF with custom functions, eg. Header() Footer() and MultiRow()
require_once('../tcpdf_custom.php');
// SETUP 2/2: Customize input parameters
/**
 * @param config			Associative array, contains 16 keys:
 	* @param outputName		Output PDF filename
 	* @param outputMode		I: view on browser. D: download directly
 	* @param titleWidth		Title column width. Default: 50. Recommended range value: 30-90. Default unit: mm
	* @param titleColor		Title column background color (RGB array)
	* @param subheaderColor	Subheader row background color (RGB array)
	* @param imageWidth		Image width. Default: 35
	* @param imageHeight	Image height. Default: 25
	* @param lineWidth		Line width. Default: 1
	* @param borderWidth	Border width. Default: 0.1
	* @param borderColor	Border color (RGB array)
	* @param font			Title and body font
	* @param titleFontStyle	Title font style. Default: bold, italic
	* @param titleFontSize	Title font size. Default: 20
	* @param bodyFontStyle	Body font style. Default: normal
	* @param bodyFontSize	Body font size. Default: 10
	* @param headerLogo		The Scarab logo on "images" folder
 * @param json				Associative array, contains 3 keys:
	* @param filepath		JSON filepath (required)
	* @param headerTitle	Header title (appears on every page)
	* @param formTitle		Form title (appears only at the beginning, before printing JSON data)
**/
$config = array(
	"jsonfilepath" => "jsonSamples/test1.json",
	"outputName" => "Invoice.pdf",
	"outputMode" => "I",
	"headerLogo" => "logo.jpg",
	"invoiceRowColorRGB" => array(233, 236, 239),
	"itemRowOddColorHEX" => "#fff",
	"itemRowEvenColorHEX" => "#E9ECEF"
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
		$outputName = isset($config['outputName']) ? $config['outputName'] : 'Invoice.pdf';
		$outputMode = isset($config['outputMode']) ? $config['outputMode'] : 'I';
		$headerLogo = $config['headerLogo'];
		$invoiceRowColorRGB = $config['invoiceRowColorRGB'];
		$itemRowOddColorHEX = $config['itemRowOddColorHEX'];
		$itemRowEvenColorHEX = $config['itemRowEvenColorHEX'];

		// Retrieve JSON data
		$jsonfile = file_get_contents($jsonfilepath);
		$data = json_decode($jsonfile);

		// Create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle(PDF_TITLE);

		// Set default header data
		// $pdf->SetHeaderData($headerLogo, PDF_HEADER_LOGO_WIDTH, $headerTitle, PDF_HEADER_STRING);

		// Set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins (default)
		// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
		// Set margins (custom rounded rect)
		$pdf->SetMargins(PDF_MARGIN_LEFT + 2, PDF_MARGIN_TOP + PDF_MARGIN_FOOTER, PDF_MARGIN_RIGHT, true);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM + PDF_MARGIN_FOOTER);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Add a page
		$pdf->AddPage();

		//
		// SET CLIENT SECTION
		//
		$pdf->Ln(4);
		$y_start = $pdf->GetY();
		$pdf->SetX(PDF_MARGIN_LEFT * 3);

		$company = isset($data->customerdata->company) ? $data->customerdata->company : '';
		$pdf->setCellPaddings(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT * 2, 1);
		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->MultiCell(130, 5, $company, 0, 'L', 0, 1);

		$pdf->SetFont('helvetica', 'R', 10);
		foreach($data->customerdata as $k => $v) {
			// Do nothing. Cliente company is added above
			if($k != 'company') {
				$pdf->SetX(PDF_MARGIN_LEFT * 3);
				$pdf->MultiCell(130, 5, $v, 0, 'L', 0, 1);
			}
		}

		//
		// SET INVOICE SECTION
		//
		$pdf->SetFont('helvetica', 'R', 9);
		$x_start = PDF_MARGIN_LEFT * 3 + 130;
		$y_index = $y_start;

		$pdf->SetLineStyle( array( 'width' => 0.5, 'color' => array(80, 80, 80)));
		foreach($data->invoicedata as $item) {
			$pdf->SetXY($x_start, $y_index);

			$pdf->SetFillColor($invoiceRowColorRGB[0], $invoiceRowColorRGB[1], $invoiceRowColorRGB[2]);
			$pdf->setCellPaddings(2, 2, 2, 2);
			$pdf->MultiCell(25, 5, $item->label, 'B', 'L', 1, 0);

			$pdf->SetFillColor(255, 255, 255);
			$pdf->setCellPaddings(2, 2, 2, 2);

			if(strpos(strtolower($item->label), 'amount') > 1) {
				$pdf->MultiCell(35, 5, formatCurrency($item->value, '$0.00'), 'B', 'R', 1, 1);
			}
			else {
				$pdf->MultiCell(35, 5, $item->value, 'B', 'R', 1, 1);
			}

			$y_index = $pdf->GetY() + 0.5;
		}

		//
		// SET ITEMS TABLE SECTION
		//
		$tbl = getTable($data->items, $itemRowEvenColorHEX, $itemRowOddColorHEX);
		// echo $tbl;

		$pdf->Ln(4);
		$pdf->SetX(PDF_MARGIN_RIGHT + 2);
		$pdf->writeHTML($tbl, true, false, false, false, '');

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

function getTable($items, $itemRowEvenColorHEX, $itemRowOddColorHEX) {
	$datatotal = $items->total;
	$datalist = $items->list;

	// Add thead
	$headerStyle = 'border-top: 1px solid #212529;';
	$tbl = '<table cellpadding="5">' .
	'<thead>' .
		'<tr style="color: #fff; background-color: #212529; font-weight: bold; font-size: 12px;">' .
			'<td width="100" align="center" style="border-left: 1px solid #212529; ' . $headerStyle . '"><b>Image</b></td>' .
			'<td width="100" align="center" style="' . $headerStyle . '"><b>Item</b></td>' .
			'<td width="160" align="center" style="' . $headerStyle . '"><b>Description</b></td>' .
			'<td width="80" align="center" style="' . $headerStyle . '"><b>Unit cost</b></td>' .
			'<td width="40" align="center" style="' . $headerStyle . '"><b>Qty</b></td>' .
			'<td width="80" align="center" style="' . $headerStyle . '"><b>Discount</b></td>' .
			'<td width="80" align="center" style="' . $headerStyle . '"><b>Tax</b></td>' .
			'<td width="80" align="right" style="border-right: 1px solid #212529; ' . $headerStyle . '"><b>Price</b></td>' .
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

		// DAVID PENDIENTE: imageurl null
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

	$totalStyle = 'text-align: right; border: 1px solid #505050;';
	$balanceStyle = 'text-align: right; border: 1px solid #505050; color: #fff; background-color: #212529;';

	// Add totals
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

	// Add balance
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