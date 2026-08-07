<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Load PDF language for client
 * This is used eq if client have different language the system default language so in this case the PDF document
 * will be on client language not on system language
 * @param  mixed $clientid client id
 * @return null
 */
function print_pdf_dh_v2($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf('L', 'mm', ['27.94', '50.8'], true, 'UTF-8', false, false, 'data', $hide);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($optionPrint == 'orders') {
        $font_size = 8.5;
    }
    $font_size = 15;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', array(45, 20));
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/template_print_barcode.php');
    return $pdf;
}
function print_pdf_dh($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $hide);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($optionPrint == 'orders') {
        $font_size = 8.5;
    }

    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], array(45, 14));
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/template_print_barcode.php');
    return $pdf;
}
function load_pdf_language($clientid)
{
    $CI = &get_instance();

    $language = get_option('active_language');

    $clientLanguage = get_client_default_language($clientid);

    // When cron or email sending pdf document the pdfs need to be on the client language
    if (is_data_for_customer() || DEFINED('CRON')) {
        if (!empty($clientLanguage)) {
            $language = $clientLanguage;
        }
    } else {
        if (get_option('output_client_pdfs_from_admin_area_in_client_language') == 1) {
            if (!empty($clientLanguage)) {
                $language = $clientLanguage;
            }
        }
    }

    if (file_exists(APPPATH . 'language/' . $language)) {
        $CI->lang->load($language . '_lang', $language);
    }

    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    hooks()->do_action('load_pdf_language', ['language' => $language, 'client_id' => $clientid]);
}

/**
 * Fetches custom pdf logo url for pdf or use the default logo uploaded for the company
 * Additional statements applied because this function wont work on all servers. All depends how the server is configured.
 * @return string
 */
function print_pdf_P_ch($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf('P', 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 45, -1);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('P', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    if (!empty($data->qrCode)) {
        $pdf->write2DBarcode($data->qrCode['code'], $data->qrCode['type'], $data->qrCode['x'], $data->qrCode['y'], $data->qrCode['width'], $data->qrCode['height'], $data->qrCode['style'], $data->qrCode['align']);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="5">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_L_ch.php');
    return $pdf;
}

function pdf_logo_url()
{
    $custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
    $width                     = get_option('pdf_logo_width');
    $logoUrl                   = '';

    if ($width == '') {
        $width = 120;
    }
    if ($custom_pdf_logo_image_url != '') {
        $logoUrl = $custom_pdf_logo_image_url;
    } else {
        if (get_option('company_logo_dark') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo_dark'))) {
            $logoUrl = get_upload_path_by_type('company') . get_option('company_logo_dark');
        } elseif (get_option('company_logo') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo'))) {
            $logoUrl = get_upload_path_by_type('company') . get_option('company_logo');
        }
    }

    $logoImage = '';
    if ($logoUrl != '') {
        $logoImage = '<img width="' . $width . 'px" src="' . $logoUrl . '">';
    }

    return hooks()->apply_filters('pdf_logo_url', $logoImage);
}

/**
 * Get available fonts for PDF
 * @return mixed
 */
function get_pdf_fonts_list()
{
    static $fontlist = null;
    if (!$fontlist) {
        $fontlist = [];
        if (($fontsdir = opendir(TCPDF_FONTS::_getfontpath())) !== false) {
            while (($file = readdir($fontsdir)) !== false) {
                if (substr($file, -4) == '.php') {
                    $name = strtolower(basename($file, '.php'));
                    // Exclude ITALIC Fonts because are causing issue when they are set directly.
                    // Not sure if they work fine if it's set manually.
                    if (!endsWith($name, 'i')) {
                        array_push($fontlist, $name);
                    }
                }
            }
            closedir($fontsdir);
        }
    }

    return hooks()->apply_filters('pdf_fonts_list', $fontlist);
}
/**
 * Set constant for sending mail template
 * Used to identify if the custom fields should be shown and loading the PDF language
 */
function set_mailing_constant()
{
    if (!defined('SEND_MAIL_TEMPLATE')) {
        define('SEND_MAIL_TEMPLATE', true);
    }
}
/**
 * Get PDF format page
 * Based on the options will return the formatted string that will be used in the PDF library
 * @param  string $option_name
 * @return array
 */
function get_pdf_format($option_name)
{
    $oFormat = strtoupper(get_option($option_name));
    $data    = [
        'orientation' => '',
        'format'      => '',
    ];

    if ($oFormat == 'A4-PORTRAIT') {
        $data['orientation'] = 'P';
        $data['format']      = 'A4';
    } elseif ($oFormat == 'A4-LANDSCAPE') {
        $data['orientation'] = 'L';
        $data['format']      = 'A4';
    } elseif ($oFormat == 'LETTER-PORTRAIT') {
        $data['orientation'] = 'P';
        $data['format']      = 'LETTER';
    } elseif ($oFormat == 'LETTER-LANDSCAPE') {
        $data['orientation'] = 'L';
        $data['format']      = 'LETTER';
    }

    return hooks()->apply_filters('pdf_format_array', $data);
}

/**
 * Prepare general invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @param  string $tag     tag for bulk pdf exporter
 * @return mixed object
 */
function invoice_pdf($invoice, $tag = '')
{
    return app_pdf('invoice', LIBSPATH . 'pdf/Invoice_pdf', $invoice, $tag);
}
/**
 * Prepare general credit note pdf
 * @param  object $credit_note Credit note as object with all necessary fields
 * @param  string $tag tag for bulk pdf exported
 * @return mixed object
 */
function credit_note_pdf($credit_note, $tag = '')
{
    return app_pdf('credit_note', LIBSPATH . 'pdf/Credit_note_pdf', $credit_note, $tag);
}

/**
 * Prepare general estimate pdf
 * @since  Version 1.0.2
 * @param  object $estimate estimate as object with all necessary fields
 * @param  string $tag tag for bulk pdf exporter
 * @return mixed object
 */
function estimate_pdf($estimate, $tag = '')
{
    return app_pdf('estimate', LIBSPATH . 'pdf/Estimate_pdf', $estimate, $tag);
}

/**
 * Function that generates proposal pdf for admin and clients area
 * @param  object $proposal
 * @param  string $tag      tag for bulk pdf exporter
 * @return object
 */
function proposal_pdf($proposal, $tag = '')
{
    return app_pdf('proposal', LIBSPATH . 'pdf/Proposal_pdf', $proposal, $tag);
}

/**
 * Generate contract pdf
 * @param  object $contract object db
 * @return mixed object
 */
function contract_pdf($contract)
{
    return app_pdf('contract', LIBSPATH . 'pdf/Contract_pdf', $contract);
}
/**
 * Generate payment pdf
 * @param  mixed $payment payment from database
 * @param  string $tag     tag for bulk pdf exporter
 * @return object
 */
function payment_pdf($payment, $tag = '')
{
    return app_pdf('payment', LIBSPATH . 'pdf/Payment_pdf', $payment, $tag);
}

/**
 * Prepare customer statement pdf
 * @param  object $statement statement
 * @return mixed
 */
function statement_pdf($statement)
{
    return app_pdf('statement', LIBSPATH . 'pdf/Statement_pdf', $statement);
}

/**
 * General function for PDF documents logic
 * @param  string $type   document type e.q. payment, statement, invoice
 * @param  string $class  full class path
 * @param  mixed $params  params to pass in class constructor
 * @return object
 */
function app_pdf($type, $path, ...$params)
{
    $basename = ucfirst(basename(strbefore($path, EXT)));

    if (!endsWith($path, EXT)) {
        $path .= EXT;
    }

    $path = hooks()->apply_filters("{$type}_pdf_class_path", $path, ...$params);

    include_once($path);

    return (new $basename(...$params))->prepare();
}
/**
 * This will add tag to PDF at the top right side
 * Only used when bulk pdf exporter feature is used from admin area
 * @param  string $tag  tag to check
 * @param  object &$pdf pdf instance
 * @return null
 */
function _bulk_pdf_export_maybe_tag($tag, &$pdf)
{
    // Tag - used in BULK pdf exporter
    if ($tag != '') {
        $font_name = get_option('pdf_font');
        $font_size = get_option('pdf_font_size');

        if ($font_size == '') {
            $font_size = 10;
        }
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetDrawColor(245, 245, 245);
        $pdf->SetXY(0, 0);
        $pdf->SetFont($font_name, 'B', 15);
        $pdf->SetTextColor(0);
        $pdf->SetLineWidth(0.75);
        $pdf->StartTransform();
        $pdf->Rotate(-35, 109, 235);
        $pdf->Cell(100, 1, mb_strtoupper($tag, 'UTF-8'), 'TB', 0, 'C', '1');
        $pdf->StopTransform();
        $pdf->SetFont($font_name, '', $font_size);
        $pdf->setX(10);
        $pdf->setY(10);
    }
}

/**
 * Helper function for PDF multi row
 * @param  string  $left       the left row
 * @param  string  $right      the right row
 * @param  object  $pdf        the PDF class object
 * @param  integer $left_width left row width
 * @return null
 */
function pdf_multi_row($left, $right, $pdf, $left_width = 40)
{
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)

    $page_start = $pdf->getPage();
    $y_start    = $pdf->GetY();

    // write the left cell
    $pdf->MultiCell($left_width, 0, $left, 0, 'L', 0, 2, '', '', true, 0, true);

    $page_end_1 = $pdf->getPage();
    $y_end_1    = $pdf->GetY();

    $pdf->setPage($page_start);

    // write the right cell
    $pdf->MultiCell(0, 0, $right, 0, 'R', 0, 1, $pdf->GetX(), $y_start, true, 0, true);

    $page_end_2 = $pdf->getPage();
    $y_end_2    = $pdf->GetY();

    // set the new row position by case
    if (max($page_end_1, $page_end_2) == $page_start) {
        $ynew = max($y_end_1, $y_end_2);
    } elseif ($page_end_1 == $page_end_2) {
        $ynew = max($y_end_1, $y_end_2);
    } elseif ($page_end_1 > $page_end_2) {
        $ynew = $y_end_1;
    } else {
        $ynew = $y_end_2;
    }

    $pdf->setPage(max($page_end_1, $page_end_2));
    $pdf->SetXY($pdf->GetX(), $ynew);
}
function print_pdf_return($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 45, -1);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_return.php');
    return $pdf;
}
function print_pdf_fix($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', true);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    if (!empty($data->code_vouchers)) {
        $pdf->SetTitle($data->code_vouchers);
    }
    $pdf->SetMargins(10, 45, -1);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1.53);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    $pdf->writeHTML('' . $data->content, true, 0, true, true);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf.php');
    return $pdf;
}
function print_pdf($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    if (!empty($data->title)) {
        $pdf->SetTitle($data->title);
    }
    if (!empty($data->code_vouchers)) {
        $pdf->SetTitle($data->code_vouchers);
    }
    $pdf->SetMargins(10, 45, -1);
    // $pdf->SetAutoPageBreak(TRUE, 0);
    // $pdf->setPrintFooter(false);
    // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    if (!empty($data->qrCode)) {
        $pdf->write2DBarcode($data->qrCode['code'], $data->qrCode['type'], $data->qrCode['x'], $data->qrCode['y'], $data->qrCode['width'], $data->qrCode['height'], $data->qrCode['style'], $data->qrCode['align']);
        // $pdf->write1DBarcode($data->qrCode['code'], 'C128', 110, 35, '', 13, 0.22, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="5">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    // $pdf->writeHTML('' . $data->content, true, 0, true, true);
    $dimensions = $pdf->getPageDimensions();
    $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data->content, 0, 'J', 0, 1, '', 55, true, 0, true, true, 0);

    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf.php');
    return $pdf;
}
function print_pdf_L_ch($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf('L', 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 45, -1);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="5">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_L_ch.php');
    return $pdf;
}
function print_pdf_L($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf('L', 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 0, -1);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_L.php');
    return $pdf;
}
function print_pdf_tnh_order($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->SetFont('gugi', '', $font_size);

    //	$fontname = TCPDF_FONTS::addTTFfont('third_partythird_party/tcpdf/fonts/Gugi-Regular.ttf', 'TrueTypeUnicode', '', 96);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    if (!empty($data['qrCode'])) {
        $pdf->write2DBarcode($data['qrCode']['code'], $data['qrCode']['type'], $data['qrCode']['x'], $data['qrCode']['y'], $data['qrCode']['width'], $data['qrCode']['height'], $data['qrCode']['style'], $data['qrCode']['align']);
        // $pdf->write1DBarcode($data['qrCode']['code'], 'C128', 230, 65, '', 13, 0.35, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode']) && empty($data['is_c'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();

    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    if (!empty($data['number_print'])) {
        $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
    }

    if (!empty($data['js'])) {
        $pdf->IncludeJS($data['js']);
    }

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_tnh($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->SetFont('gugi', '', $font_size);

    //	$fontname = TCPDF_FONTS::addTTFfont('third_partythird_party/tcpdf/fonts/Gugi-Regular.ttf', 'TrueTypeUnicode', '', 96);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    if (!empty($data['qrCode'])) {
        $pdf->write2DBarcode($data['qrCode']['code'], $data['qrCode']['type'], $data['qrCode']['x'], $data['qrCode']['y'], $data['qrCode']['width'], $data['qrCode']['height'], $data['qrCode']['style'], $data['qrCode']['align']);
        // $pdf->write1DBarcode($data['qrCode']['code'], 'C128', 230, 65, '', 13, 0.35, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode']) && empty($data['is_c'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();

    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    if (!empty($data['number_print'])) {
        $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
    }

    if (!empty($data['js'])) {
        $pdf->IncludeJS($data['js']);
    }

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_tnh_reports($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->SetFont('gugi', '', $font_size);

    //	$fontname = TCPDF_FONTS::addTTFfont('third_partythird_party/tcpdf/fonts/Gugi-Regular.ttf', 'TrueTypeUnicode', '', 96);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    if (!empty($data['qrCode'])) {
        $pdf->write2DBarcode($data['qrCode']['code'], $data['qrCode']['type'], $data['qrCode']['x'], $data['qrCode']['y'], $data['qrCode']['width'], $data['qrCode']['height'], $data['qrCode']['style'], $data['qrCode']['align']);
        // $pdf->write1DBarcode($data['qrCode']['code'], 'C128', 112, 40, '', 13, 0.2, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode']) && empty($data['is_c'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();

    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    if (!empty($data['number_print'])) {
        $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
    }

    if (!empty($data['js'])) {
        $pdf->IncludeJS($data['js']);
    }

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_tnh_productions($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->SetFont('gugi', '', $font_size);

    //	$fontname = TCPDF_FONTS::addTTFfont('third_partythird_party/tcpdf/fonts/Gugi-Regular.ttf', 'TrueTypeUnicode', '', 96);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    if (!empty($data['qrCode'])) {
        $pdf->write2DBarcode($data['qrCode']['code'], $data['qrCode']['type'], $data['qrCode']['x'], $data['qrCode']['y'], $data['qrCode']['width'], $data['qrCode']['height'], $data['qrCode']['style'], $data['qrCode']['align']);
        // $pdf->write1DBarcode($data['qrCode']['code'], 'C128', 8, 31, '', 13, 0.2, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode']) && empty($data['is_c'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();

    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    if (!empty($data['number_print'])) {
        $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
    }

    if (!empty($data['js'])) {
        $pdf->IncludeJS($data['js']);
    }

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_tnh_new($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}

function print_pdf_brandcode_tnh($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);
    // $pdf->SetHeaderData('logo', 'width', 'title 001', 'head string', array(0,64,255), array(0,64,128));
    // $pdf->SetHeaderData('PDF_HEADER_LOGO', 'PDF_HEADER_LOGO_WIDTH', 'PDF_HEADER_TITLE'.' 001', 'PDF_HEADER_STRING', array(0,64,255), array(0,64,128));
    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }
    // $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // $pdf->setImageScale(1.53);
    // $pdf->SetAutoPageBreak(TRUE,20);
    // $pdf->setPrintFooter(false);
    // $pdf->setImageScale(1.53);

    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    // // Multicell test
    // $pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 0, 0, '', '', true);
    // $pdf->MultiCell(55, 5, '[RIGHT] '.$txt, 1, 'R', 0, 1, '', '', true);
    // $pdf->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, '', '', true);
    // $pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 0, 2, '' ,'', true);
    // $pdf->MultiCell(55, 5, '[DEFAULT] '.$txt, 1, '', 0, 1, '', '', true);

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 15, true, 0, true, true, 0);

    if (!empty($data['items'])) {
        foreach ($data['items'] as $key => $value) {
            // $pdf->MultiCell(50, 15, $value['barcode'], 1, $align = 'C', $fill = 0, $ln = 1, $x = '', $y = 15, $reseth = true, $stretch = 0, $ishtml = true, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = false );
            $xBegin = 10;
            $yBegin = 20;
            for ($j = 1; $j <= 13; $j++) {
                if ($j == 1) {
                    $yUse = 13;
                } else if ($j == 2) {
                    $yUse = 34;
                } else if ($j == 3) {
                    $yUse = 55;
                } else if ($j == 4) {
                    $yUse = 76;
                } else if ($j == 5) {
                    $yUse = 97;
                } else if ($j == 6) {
                    $yUse = 118;
                } else if ($j == 7) {
                    $yUse = 139;
                } else if ($j == 8) {
                    $yUse = 160;
                } else if ($j == 9) {
                    $yUse = 181;
                } else if ($j == 10) {
                    $yUse = 202;
                } else if ($j == 11) {
                    $yUse = 223;
                } else if ($j == 12) {
                    $yUse = 244;
                } else if ($j == 13) {
                    $yUse = 265;
                }

                for ($i = 1; $i <= 5; $i++) {
                    if ($i == 1) {
                        $xUse = 11;
                    } else if ($i == 2) {
                        $xUse = 49;
                    } else if ($i == 3) {
                        $xUse = 87;
                    } else if ($i == 4) {
                        $xUse = 125;
                    } else if ($i == 5) {
                        $xUse = 163;
                    }

                    $pdf->MultiCell(38, 21, $value['barcode'], 0, $align = 'C', $fill = 0, $ln = 1, $x = $xUse, $y = $yUse, $reseth = true, $stretch = 0, $ishtml = true, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = false);
                }
            }
        }
    }

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}

function quote_detail_pdf($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf('P', 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    $pdf->SetMargins(10, 45, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('P', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);

    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    // echo $data->content;
    // die;
    // $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data->content, 0, 'J', 0, 1, '', 42, true, 0, true, true, 0);
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/quote_detail_pdf.php');

    return $pdf;
}

function print_pdf_ch($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 0, -1);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_ch.php');
    return $pdf;
}

function print_pdf_dt($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    //    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'data');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', 'A5', true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 0, -1);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    $pdf->AddPage('L', 'A5');
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);


    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_ch.php');
    return $pdf;
}


function print_pdf_orders_qcode($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf('L', 'mm', ['50', '75'], true, 'UTF-8', false, false, 'data', $hide);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($optionPrint == 'orders') {
        $font_size = 8.5;
    }
    $font_size = 10;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', array(80, 53));
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_qr_code_orders.php');

    return $pdf;
}
function print_pdf_dt_L($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    $pageCustome = '';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }
    $pdf = new Pdf(
        'L',
        'mm',
        $formatArray['format'],
        true,
        'UTF-8',
        false,
        false,
        'data',
        $barcode,
        $showHeader,
        $pageCustome
    );

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }


    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 30, true, 0, true, true, 0);

    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_tnhs($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $type_page = !empty($data['type_page']) ? $data['type_page'] : '';
    $data_header = !empty($data['data_header']) ? $data['data_header'] : [];
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $data_header, $type_page);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    $pdf->SetTitle($data['title']);
    // $pdf->SetHeaderData('logo', 'width', 'title 001', 'head string', array(0,64,255), array(0,64,128));
    // $pdf->SetHeaderData('PDF_HEADER_LOGO', 'PDF_HEADER_LOGO_WIDTH', 'PDF_HEADER_TITLE'.' 001', 'PDF_HEADER_STRING', array(0,64,255), array(0,64,128));
    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    if ($type_page == "deliveries") {
        $pdf->SetMargins(15, 10, -1);
    }
    // $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // $pdf->setImageScale(1.53);
    // $pdf->SetAutoPageBreak(TRUE,20);
    // $pdf->setPrintFooter(false);
    // $pdf->setImageScale(1.53);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    $dimensions = $pdf->getPageDimensions();
    if ($data['number_print']) {
        if ($data['type'] == 'L') {
            $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 250, 32, true, 0, true, true, 0);
        } else {
            $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
        }
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    // $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data['content']);
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    // // Multicell test
    // $pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 0, 0, '', '', true);
    // $pdf->MultiCell(55, 5, '[RIGHT] '.$txt, 1, 'R', 0, 1, '', '', true);
    // $pdf->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, '', '', true);
    // $pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 0, 2, '' ,'', true);
    // $pdf->MultiCell(55, 5, '[DEFAULT] '.$txt, 1, '', 0, 1, '', '', true);

    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    if ($type_page == "deliveries") {
        $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 45, true, 0, true, true, 0);
    } else if ($type_page == "quotes") {
        $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    } else {
        //        $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 30, true, 0, true, true, 0);
        $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    }

    $pdf->setPageMark();
    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}

function print_pdf_tem($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    $font_size = 8;



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }
    // $pdf->SetMargins(10, 50, -1);

    // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 7, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}

function print_pdf_dt_delivery($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }

    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }



    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(14, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    //    $pdf->SetFont('gugi', '', $font_size);

    //	$fontname = TCPDF_FONTS::addTTFfont('third_partythird_party/tcpdf/fonts/Gugi-Regular.ttf', 'TrueTypeUnicode', '', 96);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);

    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');

    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    if ($showHeader == 'hide') {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else if ($data['type_print'] == "quotes") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    if ($data['number_print']) {
        $pdf->MultiCell($dimensions['wk'], 0, $data['number_print'], 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
    }
    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}
function print_pdf_dt_new($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }
    $branch = '';
    if (!empty($data['branch'])) {
        $branch = $data['branch'];
    }
    $pageCustome = '';
    if (!empty($data['pageCustome'])) {
        $pageCustome = $data['pageCustome'];
    }
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome, $branch);
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    //    $pdf->SetTitle($data['title']);
    //    if ($showHeader == 'show') {
    //        $pdf->SetMargins(10, 10, -1);
    //    }
    //    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //    $pdf->setPrintFooter(false);
    //    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetTitle($data['title']);
    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 10, -1);
    }
    $pdf->SetMargins(10, 35, -1);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $tagvs = array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
    $pdf->setHtmlVSpace($tagvs);

    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($data['type'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    // // Add cellpadding to all tables inside the html
    if (empty($data['size_brandcode'])) {
        // $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $data['content']);
    }
    // // Remove white spaces cased by the html editor ex. <td>  item</td>
    // $data['content'] = preg_replace('/[\t\n\r\0\x0B]/', '', $data['content']);
    // $data['content'] = preg_replace('/([\s])\1+/', ' ', $data['content']);
    // // Tcpdf does not support float css we need to adjust this here
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    // $data['content'] = str_replace('<strong>', '', $data['content']);
    // $data['content'] = str_replace('</strong>', '', $data['content']);

    // $data['content'] = preg_replace('/(<tr[^>]+>(?:<\/p>)?)/i', '<span>$1</span>', $data['content']);

    // Image center
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    // print_arrays($data['content']);
    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf_tnh.php');
    // $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
    $pdf->setPageMark();
    if ($data['showHeader'] == "hide") {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 5, true, 0, true, true, 0);
    } else {
        $pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 30, true, 0, true, true, 0);
    }
    // $pdf->writeHTML($data['content'], true, 0, true, true);
    return $pdf;
}

function print_pdf_staff_dt($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf($formatArray['orientation'], 'mm', 'A4', true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($optionPrint == 'orders') {
        $font_size = 8.5;
    }
    $font_size = 10;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 20, 0);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('P', 'A4');
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/template_print_barcode_v2.php');

    return $pdf;
}

function print_pdf_page_a5($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $formatArray['format'] = 'A5';
    $barcode = false;
    if (!empty($data['barcode'])) {
        $barcode = $data['barcode'];
    }
    $showHeader = 'show';
    if (!empty($data['showHeader'])) {
        $showHeader = $data['showHeader'];
    }

    $pageCustome = 'A5';
    $pdf = new Pdf($data['type'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data', $barcode, $showHeader, $pageCustome);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetTitle($data['title']);

    if ($showHeader == 'show') {
        $pdf->SetMargins(10, 35, -1);
    }

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    if (!empty($data['size_brandcode'])) {
        $font_size = $data['size_brandcode'];
        $pdf->SetMargins(10, 15, -1);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(1.53);
    }

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);

    foreach ($data['content'] as $key => $value) {
        $pdf->AddPage($data['type'], $formatArray['format']);
        if ($CI->input->get('print') == 'true') {
            $js = 'print(true);';
            $pdf->IncludeJS($js);
        }

        $value = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $value);
        if (empty($data['size_brandcode'])) {
            $value = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="2">', $value);
        }

        $value = str_replace('float: right', 'text-align: center', $value);
        $value = str_replace('float: left', 'text-align: center', $value);
        $value = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $value);

        $dimensions = $pdf->getPageDimensions();
        // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
        // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data['content'], 0, 'J', 0, 1, '', 40, true, 0, true, true, 0);
        //		$pdf->setPageMark();
        //		if ($showHeader == 'hide') {
        //			$pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
        //		} else if ($data['type_print'] == "quotes") {
        //			$pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 3, true, 0, true, true, 0);
        //		} else {
        //			$pdf->MultiCell(0, 0, $data['content'], 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
        //		}
        //		if(!empty($data['number_print'])) {
        //			$pdf->MultiCell($dimensions['wk'], 0, $value, 0, 'J', 0, 1, 165, 32, true, 0, true, true, 0);
        //		}
        $pdf->MultiCell(0, 0, $value, 0, 'J', 0, 1, '', 33, true, 0, true, true, 0);
    }
    return $pdf;
}

function print_pdf_item_qrcode($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    // $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    // if ($hide == "hide") {
    //     $formatArray['format'] = ['100', '9'];
    // }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf('L', 'mm', ['50', '75'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($optionPrint == 'orders') {
        $font_size = 8.5;
    }
    $font_size = 10;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('L', array(80, 53));
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_qr_code_orders.php');

    return $pdf;
}

function print_pdf_qr_dt($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf('L', 'mm', ['27.94', '50.8'], true, 'UTF-8', false, false, 'data', $hide);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($data['object'] == "products") {
        $font_size = 8.5;
    }

    // $font_size = 5;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    // $pdf->AddPage('L',array(45, 20));
    $pdf->AddPage('L', array(80, 53));
    $pdf->setSpacesRE($re = '/[^\S\xa0]/');

    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    $dimensions = $pdf->getPageDimensions();
    $i = 1;
    if ($data['object'] == "materials") {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {
                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = 'materials||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';

                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    } else if ($data['object'] == "products") {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {

                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = 'products||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';


                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    } else {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {

                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = $data['object'] . '||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';

                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    }


    $pdf->Output();

    return $pdf;
}
function print_pdf_qr_dtmv($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');

    $hide = !empty($data['hide']) ? $data['hide'] : 'show';
    if ($hide == "hide") {
        $formatArray['format'] = ['100', '9'];
    }

    $optionPrint = !empty($data['optionPrint']) ? $data['optionPrint'] : '';
    if ($optionPrint == 'orders') {
        $formatArray['format'] = 'A5';
    }
    $pdf = new Pdf('L', 'mm', ['27.94', '50.8'], true, 'UTF-8', false, false, 'data', $hide);

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    if ($data['object'] == "products") {
        $font_size = 8.5;
    }

    // $font_size = 5;
    $pdf->SetTitle($data['title']);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->setImageScale(1);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    // $pdf->AddPage('L',array(45, 20));
    $pdf->AddPage('L', array(80, 53));
    $pdf->setSpacesRE($re = '/[^\S\xa0]/');

    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    $data['content'] = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data['content']);
    $data['content'] = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="1">', $data['content']);
    $data['content'] = str_replace('float: right', 'text-align: center', $data['content']);
    $data['content'] = str_replace('float: left', 'text-align: center', $data['content']);
    $data['content'] = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data['content']);

    $dimensions = $pdf->getPageDimensions();
    $i = 1;
    if ($data['object'] == "materials") {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {
                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = 'materials||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';

                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                // $pdf->write1DBarcode($code, 'C128', 5, 36, '', 13, 0.35, $style = array('position' => 'center', 'align' => 'center'), 'N');
                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    } else if ($data['object'] == "products") {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {

                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = 'products||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';


                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                // $pdf->write1DBarcode($code, 'C128', 5, 36, '', 13, 0.35, $style = array('position' => 'center', 'align' => 'center'), 'N');

                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    } else {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $value) {

                $style = array(
                    'border' => 0,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $code = $data['object'] . '||' . $value['id'];
                $pdf->write2DBarcode($code, 'QRCODE,Q', 2, -2, 25, 80, $style, 'N');

                // QRCODE,L : QR-CODE Low error correction
                // write RAW 2D Barcode

                $htmlBody = '<span>Mã:' . $value['code'] . '</span><br><span>Tên:' . $value['name'] . '</span>';

                $pdf->MultiCell(50, 0, $htmlBody, 0, 'J', 0, 1, 25, 15, true, 0, true, true, 0);
                if ($key < (count($data['items']) - 1)) {
                    $pdf->AddPage('L', array(80, 53));
                }
            }
        }
    }


    $pdf->Output();

    return $pdf;
}
function propose_offer_pdf($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf('P', 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data','','','','');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }

    $pdf->SetMargins(10, 5, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->setPrintFooter(false);
    $pdf->setImageScale(1);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage('P', $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="3">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', ' ', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);

    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    // echo $data->content;
    // die;
    // $dimensions = $pdf->getPageDimensions();
    // $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data->content, 0, 'J', 0, 1, '', 42, true, 0, true, true, 0);
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/quote_detail_pdf.php');

    return $pdf;
}
function print_pdf_audit($data)
{
    $CI = &get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    if (!empty($data->title)) {
        $pdf->SetTitle($data->title);
    }
    if (!empty($data->code_vouchers)) {
        $pdf->SetTitle($data->code_vouchers);
    }
    $pdf->SetMargins(10, 45, -1);
    // $pdf->SetAutoPageBreak(TRUE, 0);
    // $pdf->setPrintFooter(false);
    // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    if (!empty($data->qrCode)) {
        $pdf->write2DBarcode($data->qrCode['code'], $data->qrCode['type'], $data->qrCode['x'], $data->qrCode['y'], $data->qrCode['width'], $data->qrCode['height'], $data->qrCode['style'], $data->qrCode['align']);
        // $pdf->write1DBarcode($data->qrCode['code'], 'C128', 110, 35, '', 13, 0.22, $style = array('position' => 'center', 'align' => 'center'), 'N');
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="5">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    // $pdf->writeHTML('' . $data->content, true, 0, true, true);
    $dimensions = $pdf->getPageDimensions();
    $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data->content, 0, 'J', 0, 1, '', 25, true, 0, true, true, 0);

    // include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/print_pdf.php');
    return $pdf;
}