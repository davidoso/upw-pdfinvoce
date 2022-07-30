<?php
//============================================================+
// File name   : tcpdf_config.php
// Begin       : 2004-06-11
// Last Update : 2014-12-11
//
// Description : Configuration file for TCPDF.
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2004-2014  Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
//============================================================+

/**
 * Configuration file for TCPDF.
 * @author Nicola Asuni
 * @package com.tecnick.tcpdf
 * @version 4.9.005
 * @since 2004-10-27
 */

// IMPORTANT:
// If you define the constant K_TCPDF_EXTERNAL_CONFIG, all the following settings will be ignored.
// If you use the tcpdf_autoconfig.php, then you can overwrite some values here.


/**
 * Installation path (/var/www/tcpdf/).
 * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
 */
//define ('K_PATH_MAIN', '');

/**
 * URL path to tcpdf installation folder (http://localhost/tcpdf/).
 * By default it is automatically set but you can also set it as a fixed string to improve performances.
 */
//define ('K_PATH_URL', '');

/**
 * Path for PDF fonts.
 * By default it is automatically set but you can also set it as a fixed string to improve performances.
 */
//define ('K_PATH_FONTS', K_PATH_MAIN.'fonts/');

/**
 * Default images directory.
 * By default it is automatically set but you can also set it as a fixed string to improve performances.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('K_PATH_IMAGES', dirname(__FILE__).'/../images/');

/**
 * Deafult image logo used be the default Header() method.
 * Please set here your own logo or an empty string to disable it.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('PDF_HEADER_LOGO', 'logo.jpg');

/**
 * Header logo image width in user units.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('PDF_HEADER_LOGO_WIDTH', 30);

/**
 * Cache directory for temporary files (full path).
 */
//define ('K_PATH_CACHE', '/tmp/');

/**
 * Generic name for a blank image.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('K_BLANK_IMAGE', '_blank.png');

/**
 * Page format. Dymo LabelWriter 450 label 30334 custom page size: 2-1/8" x 1-1/8"
 */
// v01: Page size is the same as label size "in theory"
// define ('PDF_LABEL_PAGE_FORMAT', array(53.97, 28.57));
// v02: Increase page size * 1.2 to it fits real printed size
define ('PDF_LABEL_PAGE_FORMAT', array(64, 34));

/**
 * Page orientation (P=portrait, L=landscape).
 */
define ('PDF_LABEL_PAGE_ORIENTATION', 'L');

/**
 * Document creator.
 */
define ('PDF_LABEL_CREATOR', 'The Scarab');

/**
 * Document author.
 */
define ('PDF_LABEL_AUTHOR', 'The Scarab');

/**
 * Document title.
 */
define ('PDF_LABEL_TITLE', 'Label');

/**
 * Header title.
 */
//define ('PDF_HEADER_TITLE', 'The Scarab');

/**
 * Header description string.
 */
define ('PDF_LABEL_HEADER_STRING', "Rug label");

/**
 * Document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch].
 */
define ('PDF_LABEL_UNIT', 'mm');

/**
 * Header margin.
 */
define ('PDF_LABEL_MARGIN_HEADER', 1);

/**
 * Footer margin.
 */
define ('PDF_LABEL_MARGIN_FOOTER', 1);

/**
 * Top margin.
 */
define ('PDF_LABEL_MARGIN_TOP', 1);

/**
 * Bottom margin.
 */
define ('PDF_LABEL_MARGIN_BOTTOM', 1);

/**
 * Left margin.
 */
define ('PDF_LABEL_MARGIN_LEFT', 2);

/**
 * Right margin.
 */
define ('PDF_LABEL_MARGIN_RIGHT', 2);

/**
 * Default main font name.
 */
define ('PDF_LABEL_FONT_NAME_MAIN', 'helvetica');

/**
 * Default main font size.
 */
define ('PDF_LABEL_FONT_SIZE_MAIN', 7);

/**
 * Default data font name.
 */
define ('PDF_LABEL_FONT_NAME_DATA', 'helvetica');

/**
 * Default data font size.
 */
define ('PDF_LABEL_FONT_SIZE_DATA', 7);

/**
 * Default monospaced font name.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('PDF_FONT_MONOSPACED', 'courier');

/**
 * Ratio used to adjust the conversion of pixels to user units.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define ('PDF_IMAGE_SCALE_RATIO', 1.25);

/**
 * Magnification factor for titles.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('HEAD_MAGNIFICATION', 1.1);

/**
 * Height of cell respect font height.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_CELL_HEIGHT_RATIO', 1.25);

/**
 * Title magnification respect main font size.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_TITLE_MAGNIFICATION', 1.3);

/**
 * Reduction factor for small font.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_SMALL_RATIO', 2/3);

/**
 * Set to true to enable the special procedure used to avoid the overlappind of symbols on Thai language.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_THAI_TOPCHARS', true);

/**
 * If true allows to call TCPDF methods using HTML syntax
 * IMPORTANT: For security reason, disable this feature if you are printing user HTML content.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_TCPDF_CALLS_IN_HTML', false);

/**
 * If true and PHP version is greater than 5, then the Error() method throw new exception instead of terminating the execution.
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_TCPDF_THROW_EXCEPTION_ERROR', false);

/**
 * Default timezone for datetime functions
 */
// NOTE: Constant already declared in tcpdf_config.php
// define('K_TIMEZONE', 'UTC');

//============================================================+
// END OF FILE
//============================================================+
