<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
	public $_fonts_list = array();
	protected $last_page_flag = false;
	private $pdf_type = '';

	function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false,$pdf_type = '', $barcode = false, $showHeader = 'show',$pageCustome = '')
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->pdf_type = $pdf_type;
		$lg = array();
		$lg['a_meta_charset'] = 'UTF-8';
		// set some language-dependent strings (optional)
		$this->setLanguageArray($lg);
		$this->_fonts_list = $this->fontlist;
		$this->barcode = $barcode;
		$this->showHeader = $showHeader;
		$this->pageCustome = $pageCustome;
		$this->orientation = $orientation;
	}

	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}

	// public function Header() {
	// 	$this->SetFont('helvetica', 'B', 20);
	// 	$y = $this->getY();
	// 	$logo = base_url('uploads/company/'.get_option('company_logo'));
	// 	// Image( $file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array() )
	// 	// $this->Image($logo, $x = 10, $y = 10, $w = 35, $h = '', $type = '', $link = '', $aligin = 'T', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, false, false);
	// 	// Title
	// 	// Cell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' )
	// 	// $title = '
	// 	// 	<span>'.get_option('invoice_company_name').'</span><br>
	// 	// 	<span>Office: '.get_option('invoice_company_address').'</span>
	// 	// ';
	// 	// $this->Cell(0, 15, $title, 0, false, 'L', 0, '', 0, false, 'T', 'M');
	// 	// $this->writeHTML($title, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '' );

	// 	$this->writeHTMLCell('', '', '', $y+7, '<img src="'.base_url('uploads/logo-header.png').'">', 0, 0, false, true, 'J', true);
	// 	// $this->Cell(0, 15, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	// }

	public function Header() {
		$this->SetFont(get_option('pdf_font'));
		if ($this->showHeader == 'show') {
			// $company_logo = get_option('company_logo');
			// $img = base_url('uploads/company/'.$company_logo);
			// $this->MultiCell(40, 0, '<img width="150" height="60" src="'.$img.'">', 0, 'J', 0, 0, '', 10, 0, 0, true, true, 0);

			// $html = '';
			// $html .= '<span style="font-weight: bold; font-size: 14px;">'.get_option('invoice_company_name').'</span><br>';
			// $html .= '<span style="font-size: 12px;">Địa chỉ: '.get_option('invoice_company_address').'</span><br>';
			// $html .= '<span style="font-size: 12px;">Điện thoại: '.get_option('invoice_company_phonenumber').'</span><br>';
			// // $html .= '<span style="font-size: 12px;"> Fax: '.get_option('fax_company').'</span><br>';
			// $html .= '<span style="font-size: 12px;">Email: '.get_option('email_company').'</span>';
			// $this->MultiCell(112, 0, $html, 0, 'J', 0, 1, 50, 5, true, 0, true, true, 0);
			if($this->pageCustome == 'check_quality'){
				$company_logo = get_option('company_logo');
				$img = base_url('uploads/company/'.$company_logo);
				$this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

				$html = '<div style="text-align: right">';
				$html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
				$html .='</div>';
				$this->MultiCell(210, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
				$html1 ='<hr>';
				$this->MultiCell(275, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
			} else if($this->pageCustome == 'quotes'){
				$company_logo = get_option('company_logo');
				$img = base_url('uploads/company/'.$company_logo);
				$this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

				$html = '<div style="text-align: right">';
				$html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
				$html .='</div>';
				$this->MultiCell(210, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
				$html1 ='<hr>';
				$this->MultiCell(275, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
			} else if($this->pageCustome == 'productions_orders'){
				$company_logo = get_option('company_logo');
				$img = base_url('uploads/company/'.$company_logo);
				$this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

				$html = '<div style="text-align: right">';
				$html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
				$html .='</div>';
				$this->MultiCell(210, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
				$html1 ='<hr>';
				$this->MultiCell(275, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
			} elseif($this->pageCustome == 'orders_detail'){
                $company_logo = get_option('company_logo');
                $img = base_url('uploads/company/'.$company_logo);
                $this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

                $html = '<div style="text-align: right">';
                $html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
                $html .='</div>';
                $this->MultiCell(215, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
                $html1 ='<hr>';
                $this->MultiCell(278, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
            } elseif($this->pageCustome == 'orders_detail_1'){
                $company_logo = get_option('company_logo');
                $img = base_url('uploads/company/'.$company_logo);
                $this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

                $html = '<div style="text-align: right">';
                $html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
                $html .='</div>';
                $this->MultiCell(190, 0, $html, 0, 'J', 0, 1, 10, 5, true, 0, true, true, 0);
                $html1 ='<hr>';
                $this->MultiCell(190, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
            } elseif($this->pageCustome == 'delivery'){
                $company_logo = get_option('company_logo');
                $img = base_url('uploads/company/'.$company_logo);

                $html = '<div style="text-align: right">';
                $html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
                $html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';

                $html .='</div>';
                $this->MultiCell(121, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
                $html1 ='<hr>';
                $this->MultiCell(182, 0, $html1, 0, 'J', 0, 0, 14, 30, 0, 0, true, true, 0);

                if (!empty($this->barcode))
                {
                    $htmlBarcode = '<span>'.$this->barcode.'</span>';
                    $this->MultiCell(100, 0, $htmlBarcode, 0, 'J', 0, 1, 163, 14, true, 0, true, true, 0);
                    // MultiCell( $w, $h, $txt, $border = 0, $align = 'J', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false )
                }
            } else if($this->pageCustome == 'A5') {

				$company_logo = get_option('company_logo');
				$img = base_url('uploads/company/'.$company_logo);
//                $this->MultiCell(40, 0, '<img width="100" height="55" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

				$html = '<div style="text-align: right">';
				$html .= '<span style="font-weight: bold; font-size: 11px; color: red;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 7px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 7px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 7px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
                $html .= '<span style="font-size: 7px;">' . _l('Email') . ' : ' . get_option('email_company') . '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 7px;">' . _l('tnh_website') . ' : ' . get_option('company_website') . '</span>';
                $html .='</div>';
				$htmlTable = '<table width="100%">
							<tr>
								<td width="25%"><img width="100" height="55" src="'.$img.'"></td>
								<td width="75%">'.$html.'</td>
							</tr>
						</table><hr>';
				$this->MultiCell(135, 0, $htmlTable, 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

			}
			else {
				$company_logo = get_option('company_logo');
				$img = base_url('uploads/company/'.$company_logo);
                $this->MultiCell(40, 0, '<img width="200" height="110" src="'.$img.'">', 0, 'J', 0, 0, 8, 5, 0, 0, true, true, 0);

				$html = '<div style="text-align: right">';
				$html .= '<span style="font-weight: bold; font-size: 12px; color: red;">'.get_option('invoice_company_name').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
				$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
                $html .= '<span style="font-size: 10px;">' . _l('Email') . ' : ' . get_option('email_company') . '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">' . _l('tnh_website') . ' : ' . get_option('company_website') . '</span><br>';
                $html .='</div>';
				$html1 ='<hr>';
				if ($this->orientation == 'L') {
					$this->MultiCell(120, 0, $html, 0, 'J', 0, 1, 165, 5, true, 0, true, true, 0);
					$this->MultiCell(277, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
				} else {
					$this->MultiCell(125, 0, $html, 0, 'J', 0, 1, 73, 5, true, 0, true, true, 0);
					$this->MultiCell(190, 0, $html1, 0, 'J', 0, 0, 10, 30, 0, 0, true, true, 0);
				}

				if (!empty($this->barcode))
				{
					$htmlBarcode = '<span>'.$this->barcode.'</span>';
					$this->MultiCell(100, 0, $htmlBarcode, 0, 'J', 0, 1, 163, 14, true, 0, true, true, 0);
					// MultiCell( $w, $h, $txt, $border = 0, $align = 'J', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false )
				}
			}
		}
	}

	public function Footer() {
        // Position at 15 mm from bottom
		$this->SetY(-15);

		$font_name = get_option('pdf_font');
	    $font_size = get_option('pdf_font_size');

	    if ($font_size == '') {
	        $font_size = 10;
	    }

		$this->SetFont($font_name, '', $font_size);

		do_action('pdf_footer',array('pdf_instance'=>$this,'type'=>$this->pdf_type));
        // Set font
		$this->SetFont('helvetica', 'I', 8);
		if(get_option('show_page_number_on_pdf') == 1){
			$this->SetTextColor(142,142,142);
			$this->Cell(0, 15, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}

	public function get_fonts_list(){
		return $this->_fonts_list;
	}

}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */