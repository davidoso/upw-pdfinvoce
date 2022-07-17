<?php
// --------------------------------------------------------------------------------------
// Author:				David Osorio, jdavidosori96@gmail.com
// Upwork profile:		https://www.upwork.com/freelancers/~010be696c9ded003b5
// Date:				November 2019
// PHP version:			7.2.3
// --------------------------------------------------------------------------------------
// SETUP: Change path to include main TCPDF library and customize defined constants on config/tcpdf_config.php
require_once('../tcpdf_include.php');
// Extend TCPDF with a custom function. MultiRow() allows to add field title and value in a single line
require_once('../tcpdf_multirow.php');

// NOTE: Example how to call script and customize input parameters
// $json = array("filepath" => "jsonSamples/test1.json");
$config = array(
	"jsonfilepath" => "jsonSamples/test1.json",
	"headerLogo" => "logo.jpg",
	"invoiceRowColorRGB" => array(233, 236, 239),
	"itemRowOddColorHEX" => "#fff",
	"itemRowEvenColorHEX" => "#E9ECEF",
	"rowOddColor" => "logo.jpg"
);
parseJSONPDF($config);


/**
 *
 * The following code has been tested successfully with provided JSON samples
 * Please do not modify it unless you understand what you are doing
 * Recursion reference:
 * https://stackoverflow.com/questions/5524227/php-foreach-with-arrays-within-arrays
 *
**/


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
	* @param headerLogo		Sitefotos logo on "images" folder
 * @param json				Associative array, contains 3 keys:
	* @param filepath		JSON filepath (required)
	* @param headerTitle	Header title (appears on every page)
	* @param formTitle		Form title (appears only at the beginning, before printing JSON data)
**/
function parseJSONPDF($config/*, $json*/) {
	if(isset($config['jsonfilepath'])) {
		// JSON parameters
		$jsonfilepath = $config['jsonfilepath'];
		// $headerTitle = isset($json['headerTitle']) ? $json['headerTitle'] : 'Default header title';
		// $formTitle = isset($json['formTitle']) ? $json['formTitle'] : 'Default form title';

		// Config parameters
		$outputName = isset($config['outputName']) ? $config['outputName'] : 'Invoice.pdf';
		$outputMode = isset($config['outputMode']) ? $config['outputMode'] : 'I';
		$titleWidth = isset($config['titleWidth']) ? $config['titleWidth'] : 50;
		$titleColor = isset($config['titleColor']) ? $config['titleColor'] : array(233, 236, 239);
		$subheaderColor = isset($config['subheaderColor']) ? $config['subheaderColor'] : array(255, 217, 102);
		$imageWidth = isset($config['imageWidth']) ? $config['imageWidth'] : 35;
		$imageHeight = isset($config['imageHeight']) ? $config['imageHeight'] : 25;
		$lineWidth = isset($config['lineWidth']) ? $config['lineWidth'] : 1;
		$borderWidth = isset($config['borderWidth']) ? $config['borderWidth'] : 0.1;
		$borderColor = isset($config['borderColor']) ? $config['borderColor'] : array(80, 80, 80);
		$font = isset($config['font']) ? $config['font'] : 'helvetica';
		$titleFontStyle = isset($config['titleFontStyle']) ? $config['titleFontStyle'] : 'BI';
		$titleFontSize = isset($config['titleFontSize']) ? $config['titleFontSize'] : 20;
		$bodyFontStyle = isset($config['bodyFontStyle']) ? $config['bodyFontStyle'] : '';
		$bodyFontSize = isset($config['bodyFontSize']) ? $config['bodyFontSize'] : 10;
		$headerLogo = $config['headerLogo'];
		$invoiceRowColorRGB = $config['invoiceRowColorRGB'];
		$itemRowOddColorHEX = $config['itemRowOddColorHEX'];
		$itemRowEvenColorHEX = $config['itemRowEvenColorHEX'];

		// Fixed constants (non parameters)
		// Switch on printField() only works with these types, unless the object contains a "value" key also
		$knownTypes = array('text', 'comment', 'radiogroup', 'checkbox', 'dropdown', 'dropdownmultiple', 'file', 'signaturepad', 'sketch', 'service', 'material', 'geo', 'url', 'issues', 'segmentInput');
		// Images are created with Image()
		// Field titles are printed with MultiCell()
		// Optional captions with Cell()
		$imageTypes = array('file', 'signaturepad', 'sketch', 'issues');
		// These types contain "title" and "value" keys but are not intended to be visible
		$ignoreTypes = array('crew');

		// Retrieve JSON data
		// $json_filepath = $filepath;
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
		// $pdf->SetHeaderData($headerLogo, PDF_HEADER_LOGO_WIDTH, 'asd<br>asd2', PDF_HEADER_STRING);

		// Set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins
		// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
		$pdf->SetMargins(PDF_MARGIN_LEFT + 2, PDF_MARGIN_TOP + PDF_MARGIN_FOOTER, PDF_MARGIN_RIGHT, true);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM + PDF_MARGIN_FOOTER);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Add a page
		$pdf->AddPage();

		// padding
		// Left=2, Top=4, Right=6, Bottom=8\
		

		$pdf->Ln(4);
        $y_start = $pdf->GetY();

        $pdf->SetX(PDF_MARGIN_LEFT * 3);
		$pdf->setCellPaddings(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT * 2, 1);

		// Client data
		// First name
		// $pdf->SetFillColor(233, 236, 239);
        $pdf->SetFont('helvetica', 'B', 10);
		
		$company = isset($data->customerdata->company) ? $data->customerdata->company : '';

		$pdf->MultiCell(130, 5, $company, 0, 'L', 0, 1);

        $pdf->SetFont('helvetica', 'R', 10);

		// foreach($jsonfile->elements as $e)
		// 	printField($e, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, 
		foreach($data->customerdata as $k => $v) {
			if($k != 'company') {
				$pdf->SetX(PDF_MARGIN_LEFT * 3);
				$pdf->MultiCell(130, 5, $v, 0, 'L', 0, 1);
			}
		}

		//
		// invoice data
		//

		// $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));


        $pdf->SetFont('helvetica', 'R', 9);
		// padding 10
		// 60 width
        // $x_start = $pdf->getPageWidth() - PDF_MARGIN_LEFT - 30;
        $x_start = PDF_MARGIN_LEFT * 3 + 130;

		$y_index = $y_start;

		$pdf->SetLineStyle( array( 'width' => 0.5, 'color' => array(80, 80, 80)));
		foreach($data->invoicedata as $item) {
			$pdf->SetXY($x_start, $y_index);
			$pdf->SetFillColor($invoiceRowColorRGB[0], $invoiceRowColorRGB[1], $invoiceRowColorRGB[2]);
			$pdf->setCellPaddings(2, 2, 2, 2);
			$pdf->MultiCell(25, 5, $item->label, 'B', 'L', 1, 0);
			$pdf->setCellPaddings(2, 2, 8, 0);

			$pdf->SetFillColor(255, 255, 255);
			$pdf->setCellPaddings(2, 2, 2, 2);
			$pdf->MultiCell(35, 5, $item->value, 'B', 'R', 1, 1);
		// $pdf->Ln(1);

			$y_index = $pdf->GetY() + 0.5;
		}


		$tbl = getTable($data->items, $itemRowEvenColorHEX, $itemRowOddColorHEX);

        // $pdf->SetX(2 + PDF_MARGIN_RIGHT);
		//
		// items table
		//

		
		// este es el bueno
		// CONFIG MARGIN TABLE
        // $pdf->SetX(PDF_MARGIN_RIGHT + 2);
        
		// TEST END PAGE
		// $pdf->SetXY(PDF_MARGIN_RIGHT + 2, 200);

		$pdf->SetX(PDF_MARGIN_RIGHT + 2);

		$pdf->Ln(4);


		$pdf->writeHTML($tbl, true, false, false, false, '');

		// Close and output PDF document
		$pdf->Output($outputName, $outputMode);
		// echo $tbl;
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

	$tbl =
	'<table cellpadding="5">' .
	'<thead>' .
		'<tr  style="color: #fff; background-color: #212529; font-weight: bold; font-size: 12px;">' .
	'<td width="100" align="center" style="border-left: 1px solid #212529; border-top: 1px solid #212529;">	<b>Image</b></td>' .
	'<td width="100"   align="center" style="border-top: 1px solid #212529;">	<b>Item</b></td>' .
	'<td width="160"   align="center" style="border-top: 1px solid #212529;">	<b>Description</b></td>' .
	'<td width="80"   align="center" style="border-top: 1px solid #212529;">		<b>Unit cost</b></td>' .
	'<td width="40"   align="center" style="border-top: 1px solid #212529;">		<b>Qty</b></td>' .
	'<td width="80"   align="center" style="border-top: 1px solid #212529;">		<b>Discount</b></td>' .
	'<td width="80"   align="center" style="border-top: 1px solid #212529;">		<b>Tax</b></td>' .
	'<td width="80"   align="right" style="border-right: 1px solid #212529; border-top: 1px solid #212529;">		<b>Price</b></td>' .
		'</tr>' .
	'</thead>' .
	'<tbody style="font-size: 10px;">';

	$rowIndex = 0;
	foreach($datalist as $item) {
		$rowBg =  (++$rowIndex % 2 == 0)
			? 'vertical-align:bottom; border: 1px solid #505050; background-color: ' . $itemRowEvenColorHEX . ';'
			: 'vertical-align:bottom; border: 1px solid #505050; background-color: ' . $itemRowOddColorHEX . ';';

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
		$tbl = $tbl . '<tr  style="color: black;">' .
			'<td width="100" height="80" style="text-align: center; ' . $rowBg . '">' .
				'<img src="' . $imageurl . '" alt="" width="80"/></td>' .
			'<td width="100" style="padding: 15px; text-align: left; ' . $rowBg . '">'  . $rugid . '</td>' .
			'<td width="160" style="text-align: left; ' . $rowBg . '">'  . $description . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">'  . $unitcost . '</td>' .
			'<td width="40" style="text-align: center; ' . $rowBg . '">'  . $qty . '</td>' .
			// '<td width="80" style="text-align: ' .
			// 	($discount == '-' ? 'center' : 'right') . $rowBg . '">'  . $discount . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">'  . $discount . '</td>' .
			'<td width="80" style="text-align: center; ' . $rowBg . '">'  . $tax . '</td>' .
			'<td width="80" style="text-align: right; ' . $rowBg . '">'  . $price . '</td>' .
		'</tr> ';
	}

	$totalStyle = 'text-align: right; border: 1px solid #505050;';
	$balanceStyle = 'text-align: right; border: 1px solid #505050; color: #fff; background-color: #212529;';

	// Add totals
	foreach($datatotal as $k => $v) {
		if($k != 'balance') {
			$tbl = $tbl . '<tr style="color: black;">' .
			'<td width="100"></td>' .
			'<td width="100"></td>' .
			'<td width="160"></td>' .
			'<td width="80"></td>' .
			'<td width="40"></td>' .
			// '<td width="80"></td>' .
			'<td width="160" style="' . $totalStyle . '">' . ucfirst($k) . '</td>' .
			'<td width="80" style="' . $totalStyle . '">' . formatCurrency($v, '$0.00') . '</td>' .
			'</tr> ';
		}
	}

	// Add balance
	$tbl = $tbl . '<tr style="color: black;">' .
	'<td width="100"></td>' .
	'<td width="100"></td>' .
	'<td width="160"></td>' .
	'<td width="80"></td>' .
	'<td width="40"></td>' .
	// '<td width="80"></td>' .
	'<td width="160" style="' . $balanceStyle . '"><b>Balance Due</b></td>' .
	'<td width="80" style="' . $balanceStyle . '"><b>' . formatCurrency($datatotal->balance, '$0.00') . '</b></td>' .
	'</tr> ';

	$tbl = $tbl .
	'</tbody> ' .
	'</table> ' ;
	return $tbl;
}



/*function printField($e, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize) {
	if(isset($e->title) && isset($e->type)) {
		$title = $e->title;
		$type = $e->type;
		if((isset($e->value) || in_array($type, $knownTypes)) && !in_array($type, $ignoreTypes)) {
			$value = '';
			// Get field title and value based on type
			switch($type) {
				// There could be +1 image URLs witn NO captions
				case 'file':
					$imageURL = array();
					foreach($e->value as $photo) {
						array_push($imageURL, $photo->lrImageURL);
					}
					break;
				// There could be +1 image URLs with captions
				case 'issues':
					$imageURL = array();
					$imageName = array();
					foreach($e->value as $photo) {
						if(gettype($photo->value) == 'array')
							array_push($imageURL, $photo->value[0]->lrImageURL);	// Add image URL
						else
							array_push($imageURL, $photo->value->lrImageURL);		// Add image URL
						array_push($imageName, $photo->title);						// Add caption
					}
					break;
				// There is only one image URL
				case 'signaturepad':
				case 'sketch':
					$imageURL = array();
					if(gettype($e->value) == 'array')
						array_push($imageURL, $e->value[0]->lrImageURL);
					else
						array_push($imageURL, $e->value->lrImageURL);
					break;
				case 'service':
					$title = $e->serviceType;
					$value = '';
					switch($title) {
						case 'AsTask':
							$value = $e->ServiceName . '. Options: ' . implode(", ", $e->Options);
							break;
						case 'AsDetail':
							$info = '';
							if(isset($e->People) && isset($e->Hour)) {
								$info = $e->People . ' People, ' . $e->Hour . ' Hour(s). ';
							}
							$value = $e->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e->Options);
							break;
						case 'AsTimer':
							$info = '';
							if(isset($e->StartTime) && isset($e->StopTime)) {
								$timediff = $e->StopTime - $e->StartTime;
								$info = intdiv($timediff, 60) . ' Minutes. ';
							}
							$value = $e->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e->Options);
							break;
					}
					$title = 'Service (' . $title . ')';
					break;
				case 'geo':
					if(isset($e->buildingName) && isset($e->lat) && isset($e->lng))
						$value = 'Lat: ' . substr($e->lat, 0, 10) .
							'. Lng: ' . substr($e->lng, 0, 10) .
							'. Building name: ' . $e->buildingName;
					break;
				case 'url':
					if(isset($e->value))
						$value = $e->value;
					if(isset($e->url))
						$value = $e->url;
					break;
				case 'material':
					$title = 'Material';
					$value = $e->title . ': ' . $e->quantity . ' ' . $e->unit;
					break;
				// Most field values are simple strings or one-dimensional arrays
				default:
					if(gettype($e->value) == 'array')
						$value = implode(", ", $e->value);
					else
						$value = $e->value;
			}
			$pdf->setCellPaddings(0, 0, 2, 0);					// Set field title right padding
			// Add image first, then caption if exists, then field title
			if(in_array($type, $imageTypes)) {
				$startX = $titleWidth + 16;						// Print image starting from this X value
				for($i = 0; $i < count($imageURL); $i++) {
					// Disable auto page break if cell size can't fit on current page
					if($pdf->GetY() + $imageHeight > 253) {
						$pdf->SetXY($startX - 1, $pdf->GetY());
						$pdf->SetFillColor(255, 255, 255);		// Add a white cell for border below image
						$pdf->Cell($imageWidth + 4, 0.1, '', 'T', 0, 'L', 1);
						$pdf->AddPage();
					}
					// Image
					$pdf->Image($imageURL[$i], $startX + 2, $pdf->GetY() + 2.5, $imageWidth, $imageHeight);
					// Caption
					$caption = isset($imageName[$i]) ? '   ' . ltrim($imageName[$i]) : '';
					$pdf->SetX($startX + $imageWidth + 2);		// If exists, print caption from this X value
					$pdf->SetFillColor(255, 255, 255);			// Add a white cell for borders next to image
					$pdf->Cell(133 - $imageWidth, $imageHeight + 5, $caption, 'TBR', 0, 'L', 1);
					$y = $pdf->GetY();
					// Top border: empty cell to fill in small left and right spaces above image
					$pdf->SetXY($startX - 1, $pdf->GetY());
					$pdf->SetFillColor(255, 255, 255);			// Add a white cell for border above image
					$pdf->SetFont($font, $bodyFontStyle, $bodyFontSize);
					$pdf->Cell($imageWidth + 4, 0.1, '', 'LT', 0, 'L', 1);
					// Field title
					$pdf->SetXY(PDF_MARGIN_LEFT, $y);			// Print field title from this XY value
					$pdf->SetFillColor($titleColor[0], $titleColor[1], $titleColor[2]);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->setCellPaddings(0, 2, 2, 0);			// Set field title top and right padding
					$pdf->MultiCell($titleWidth, $imageHeight + 5, ltrim($title), 1, 'R', 1);
				}
			}
			// Add normal field values (string or stringied array)
			else {
				$pdf->SetFillColor($titleColor[0], $titleColor[1], $titleColor[2]);
				$pdf->MultiRow($titleWidth, ltrim($title), ltrim($value));
			}
		}
		if($e->type == 'subHeader') {
			$pdf->setCellPaddings(0, 2, 0, 0);					// Set subheader top padding
			$pdf->SetFillColor($subheaderColor[0], $subheaderColor[1], $subheaderColor[2]);
			$pdf->MultiCell(186, 8, 'SECTION: ' . ltrim(strtoupper($e->title)), 1, 'C', 1);
		}
	}
	// Recursion if "elements" are nested
	if(isset($e->elements)) {
		foreach($e->elements as $e2)
			printField($e2, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
	}
	// Recursion if "elements" are nested in "choices" array and "choiceValue" matchs witch selected "value"
	// When "value" is an array e.g. checkboxes, all "elements" ares printed regardless "choiceValue"
	if(isset($e->value) && isset($e->choices) && gettype($e->choices) == 'array') {
		$choiceValue = $e->value;
		foreach($e->choices as $c) {
			if(isset($c->choiceValue) && isset($c->elements)) {
				if(gettype($e->value) == 'array')
					foreach($c->elements as $e3)
						printField($e3, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
				elseif($choiceValue == $c->choiceValue)
					foreach($c->elements as $e3)
						printField($e3, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
			}
		}
	}
}*/
?>