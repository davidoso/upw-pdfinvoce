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
	"jsonfilepath" => "jsonSamples/test1_invoice.json",
	"outputName" => "Invoice.pdf",
	"outputMode" => "I",
	"headerLogo" => "logo.jpg",
	"invoiceRowColorRGB" => array(233, 236, 239),
	"itemRowOddColorHEX" => "#FFF",
	"itemRowEvenColorHEX" => "#E9ECEF",
	"tableHeaderColorHEX" => "#212529",
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
		$tableHeaderColorHEX = $config['tableHeaderColorHEX'];

		// Retrieve JSON data
		$jsonfile = file_get_contents($jsonfilepath);
		$data = json_decode($jsonfile);

		// Create new custom PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle(PDF_TITLE);

		// NOTE: tcpdf_custom.php overrides default TCPDF header / footer
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
			// Do nothing. Client company is added above
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

		$pdf->SetLineStyle( array('width' => 0.5, 'color' => array(80, 80, 80)) );
		foreach($data->invoicedata as $item) {
			$pdf->SetXY($x_start, $y_index);

			$pdf->SetFillColor($invoiceRowColorRGB[0], $invoiceRowColorRGB[1], $invoiceRowColorRGB[2]);
			$pdf->setCellPaddings(2, 2, 2, 2);
			$pdf->MultiCell(25, 5, $item->label, 'B', 'L', 1, 0);

			$pdf->SetFillColor(255, 255, 255);
			$pdf->setCellPaddings(2, 2, 2, 2);

			if(strpos(strtolower($item->label), 'amount') > -1) {
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
		$tbl = getTable($data->items, $itemRowEvenColorHEX, $itemRowOddColorHEX, $tableHeaderColorHEX);

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
		'<td width="80" style="' . $balanceStyle . '"><b>' .
			formatCurrency($datatotal->balance, '$0.00') . '</b></td>' .
	'</tr>';

	// Close tags
	$tbl = $tbl .
	'</tbody> ' .
	'</table> ' ;

	return $tbl;
}
?>