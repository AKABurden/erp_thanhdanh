<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\messages\Message;
use app\services\messages\PopupMessage;

if (!function_exists('js')) {
    function js($link = '')
    {
        return base_url('assets/js/tnh/') . $link;
    }
}

if (!function_exists('css')) {
    function css($link = '')
    {
        return base_url('assets/css/') . $link;
    }
}

if (!function_exists('pathMaterial')) {
    function pathMaterial($file = '')
    {
        return base_url('uploads/materials/') . $file;
    }
}

if (!function_exists('pathProduct')) {
    function pathProduct($file = '')
    {
        return base_url('uploads/products/') . $file;
    }
}

if (!function_exists('lang')) {
    /**
     * Lang
     *
     * Fetches a language variable and optionally outputs a form label
     *
     * @param string $line The language line
     * @param string $for The "for" value (id of the form element)
     * @param array $attributes Any additional HTML attributes
     * @return    string
     */
    function lang($line, $for = '', $attributes = array())
    {
        $temp = $line;
        $line = get_instance()->lang->line($line);
        if (empty($line)) $line = $temp;
        if ($for !== '') {
            $line = '<label for="' . $for . '"' . _stringify_attributes($attributes) . '>' . $line . '</label>';
        }
        return $line;
    }
}

if (!function_exists('print_arrays')) {
    function print_arrays()
    {
        $args = func_get_args();
        echo "<pre>";
        foreach ($args as $arg) {
            print_r($arg);
        }
        echo "</pre>";
        die();
    }
}

if (!function_exists('type_products')) {
    function type_products()
    {
        $option['products'] = lang('products');
        $option['semi_products'] = lang('semi_products');
        $option['semi_products_outside'] = lang('semi_products_outside');
        return $option;
    }
}

if (!function_exists('type_design_bom')) {
    function type_design_bom($type = 'all')
    {
        $option['materials'] = lang('materials');
        if ($type != 'not_all') {
            $option['semi_products'] = lang('semi_products');
            $option['semi_products_outside'] = lang('semi_products_outside');
        }
        return $option;
    }
}

if (!function_exists('status_machine')) {
    function status_machine()
    {
        $option['not_produced'] = lang('tnh_not_produced');
        $option['producing'] = lang('tnh_producing');
        $option['maintenance'] = lang('tnh_maintenance');
        $option['damaged'] = lang('tnh_damaged');
        return $option;
    }
}

if (!function_exists('type_tools_supplies')) {
    function type_tools_supplies()
    {
        $option['tools'] = lang('tools');
        $option['supplies'] = lang('supplies');
        $option['packaging'] = lang('packaging');
        return $option;
    }
}

if (!function_exists('typeProductionsOrders')) {
    function typeProductionsOrders()
    {
        // $option[1] = lang('tnh_not_produced');
        // $option[2] = lang('tnh_producing');
        // $option[6] = lang('qc');
        // $option[3] = lang('tnh_st_purchase_ws');
        // $option[4] = lang('tnh_delay_progress');
        $option[5] = lang('tnh_st_finished');
        return $option;
    }
}

if (!function_exists('normalize')) {
    function normalize($string)
    {
        $table = array(
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
        );
        return strtr($string, $table);
    }
}
if (!function_exists('tnh_vn_to_str')) {
    function tnh_vn_to_str($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(' ', '_', $str);
        $str = str_replace('.', '_', $str);
        $str = str_replace('\\', '_', $str);
        $str = str_replace('/', '_', $str);
        $str = str_replace(':', '_', $str);
        $str = str_replace('*', '_', $str);
        $str = str_replace('?', '_', $str);
        $str = str_replace('"', '_', $str);
        $str = str_replace('<', '_', $str);
        $str = str_replace('>', '_', $str);
        $str = str_replace('|', '_', $str);
        return $str;
    }
}

if (!function_exists('get_fields_export')) {
    function get_fields_export($table, $arr_diff = false, $arr_more = false)
    {
        $CI = &get_instance();
        $fields = $CI->db->list_fields($table);
        if (!empty($arr_diff)) {
            $fields = array_diff($fields, $arr_diff);
        }
        if (!empty($arr_more)) {
            $fields = array_merge($fields, $arr_more);
        }
        return $fields;
    }
}

if (!function_exists('style_excel')) {
    function style_excel()
    {
        $data = [];
        $data['BStyle_center'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['BStyle_left'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['BStyle'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
            )
        );

        $data['title'] = [
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 16,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $data['bold'] = [
            'font' => array(
                'bold' => true,
            )
        ];

        $data['center'] = [
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $data['border'] = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        ];

        $data['font'] = [
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            )
        ];

        $data['Background_header'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '14b8e9'),
                'size' => 14,
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        );



        $data['c_th'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '92d050'),
                'size' => 12,
                'bold' => true
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['c_td_center'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'size' => 12,
                'bold' => false
            ),
            'font' => array(
                'bold' => false,
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['c_td_left'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'size' => 12,
                'bold' => false
            ),
            'font' => array(
                'bold' => false,
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['c_td_right'] = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'size' => 12,
                'bold' => false
            ),
            'font' => array(
                'bold' => false,
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $data['c_head'] = array(
            'font' => array(
                'bold' => false,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        return $data;
    }
}

if (!function_exists('statusOrders')) {
    function statusOrders()
    {
        $CI = &get_instance();
        $CI->db->select('tblprocedure_client_detail.*');
        $CI->db->from('tblprocedure_client');
        $CI->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id_detail = tblprocedure_client.id');
        $CI->db->where('tblprocedure_client.id', 4);
        $CI->db->where('tblprocedure_client.type', 'orders');
        $CI->db->order_by('tblprocedure_client_detail.orders ASC');
        $result = $CI->db->get()->result_array();
        if (empty($result)) return false;

        foreach ($result as $key => $value) {
            $option[$value['default_id']] = [
                'index' => $value['default_id'],
                'text' => $value['name'],
                'color' => $value['default_color'],
            ];
        }
        return $option;
    }
}

if (!function_exists('workflowOrders')) {
    function workflowOrders()
    {
        $option['lkhsx'] = lang('tnh_lkhsx');
        $option['dsxtcty'] = lang('tnh_dsxtcty');
        $option['sxx'] = lang('tnh_sxx');
        // $option['xgcn'] = lang('tnh_xgcn');
        // $option['ngcn'] = lang('tnh_ngcn');
        $option['gh'] = lang('tnh_gh');
        $option['ghc'] = lang('Giữ trên chuyền');
        // $option['xkgh'] = lang('tnh_xkgh');
        return $option;
    }
}

if (!function_exists('statusOrdersOld')) {
    function statusOrdersOld()
    {
        // $option[0] = lang('tnh_order_new');
        // $option[1] = lang('tnh_order_check');
        // $option[2] = lang('tnh_order_delivery');
        // $option[3] = lang('tnh_order_realize');
        // $option[4] = lang('tnh_order_finised');

        $option[0] = [
            'index' => 0,
            'text' => lang('tnh_order_new'),
            'color' => '#f44336',
        ];
        $option[1] = [
            'index' => 1,
            'text' => lang('tnh_step2'),
            'color' => '#e91e63',
        ];
        $option[2] = [
            'index' => 2,
            'text' => lang('tnh_step3'),
            'color' => '#9c27b0',
        ];
        $option[3] = [
            'index' => 3,
            'text' => lang('tnh_step4'),
            'color' => '#673ab7',
        ];
        $option[4] = [
            'index' => 4,
            'text' => lang('tnh_step5'),
            'color' => '#3f51b5',
        ];
        $option[5] = [
            'index' => 5,
            'text' => lang('tnh_step6'),
            'color' => '#2196f3',
        ];
        $option[6] = [
            'index' => 6,
            'text' => lang('tnh_step7'),
            'color' => '#03a9f4',
        ];
        $option[7] = [
            'index' => 7,
            'text' => lang('tnh_step8'),
            'color' => '#00bcd4',
        ];
        $option[8] = [
            'index' => 8,
            'text' => lang('tnh_step9'),
            'color' => '#009688',
        ];
        $option[9] = [
            'index' => 9,
            'text' => lang('tnh_step10'),
            'color' => '#4caf50',
        ];
        $option[10] = [
            'index' => 10,
            'text' => lang('tnh_step11'),
            'color' => '#84c529',
        ];
        $option[11] = [
            'index' => 11,
            'text' => lang('tnh_step12'),
            'color' => '#795548',
        ];
        $option[12] = [
            'index' => 12,
            'text' => lang('tnh_step13'),
            'color' => '#0a4e0d',
        ];
        $option[13] = [
            'index' => 13,
            'text' => lang('tnh_step14'),
            'color' => '#4cae4c',
        ];
        // $option[1] = lang('tnh_step2');
        // $option[2] = lang('tnh_step3');
        // $option[3] = lang('tnh_step4');
        // $option[4] = lang('tnh_step5');
        // $option[5] = lang('tnh_step6');
        // $option[6] = lang('tnh_step7');
        // $option[7] = lang('tnh_step8');
        // $option[8] = lang('tnh_step9');
        // $option[9] = lang('tnh_step10');
        // $option[10] = lang('tnh_step11');
        // $option[11] = lang('tnh_step12');
        return $option;
    }
}

if (!function_exists('cloumns_excel')) {
    function cloumns_excel()
    {
        return [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ',
            'EA',
            'EB',
            'EC',
            'ED',
            'EE',
            'EF',
            'EG',
            'EH',
            'EI',
            'EJ',
            'EK',
            'EL',
            'EM',
            'EN',
            'EO',
            'EP',
            'EQ',
            'ER',
            'ES',
            'ET',
            'EU',
            'EV',
            'EW',
            'EX',
            'EY',
            'EZ',
            'FA',
            'FB',
            'FC',
            'FD',
            'FE',
            'FF',
            'FG',
            'FH',
            'FI',
            'FJ',
            'FK',
            'FL',
            'FM',
            'FN',
            'FO',
            'FP',
            'FQ',
            'FR',
            'FS',
            'FT',
            'FU',
            'FV',
            'FW',
            'FX',
            'FY',
            'FZ',
            'GA',
            'GB',
            'GC',
            'GD',
            'GE',
            'GF',
            'GG',
            'GH',
            'GI',
            'GJ',
            'GK',
            'GL',
            'GM',
            'GN',
            'GO',
            'GP',
            'GQ',
            'GR',
            'GS',
            'GT',
            'GU',
            'GV',
            'GW',
            'GX',
            'GY',
            'GZ',
            'HA',
            'HB',
            'HC',
            'HD',
            'HE',
            'HF',
            'HG',
            'HH',
            'HI',
            'HJ',
            'HK',
            'HL',
            'HM',
            'HN',
            'HO',
            'HP',
            'HQ',
            'HR',
            'HS',
            'HT',
            'HU',
            'HV',
            'HW',
            'HX',
            'HY',
            'HZ',
            'IA',
            'IB',
            'IC',
            'ID',
            'IE',
            'IF',
            'IG',
            'IH',
            'II',
            'IJ',
            'IK',
            'IL',
            'IM',
            'IN',
            'IO',
            'IP',
            'IQ',
            'IR',
            'IS',
            'IT',
            'IU',
            'IV',
            'IW',
            'IX',
            'IY',
            'IZ',
            'JA',
            'JB',
            'JC',
            'JD',
            'JE',
            'JF',
            'JG',
            'JH',
            'JI',
            'JJ',
            'JK',
            'JL',
            'JM',
            'JN',
            'JO',
            'JP',
            'JQ',
            'JR',
            'JS',
            'JT',
            'JU',
            'JV',
            'JW',
            'JX',
            'JY',
            'JZ',
            'KA',
            'KB',
            'KC',
            'KD',
            'KE',
            'KF',
            'KG',
            'KH',
            'KI',
            'KJ',
            'KK',
            'KL',
            'KM',
            'KN',
            'KO',
            'KP',
            'KQ',
            'KR',
            'KS',
            'KT',
            'KU',
            'KV',
            'KW',
            'KX',
            'KY',
            'KZ',
        ];
    }
}

if (!function_exists('getReference')) {
    function getReference($field)
    {
        $CI = &get_instance();
        $q = $CI->db->get_where('tbl_order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'productions_plan':
                    $prefix = get_option('prefix_productions_plan');
                    break;
                case 'business_plan':
                    $prefix = get_option('prefix_business_plan');
                    break;
                case 'productions_capacity':
                    $prefix = get_option('prefix_productions_capacity');
                    break;
                case 'productions_orders':
                    $prefix = get_option('prefix_productions_orders');
                    break;
                case 'productions_orders_details':
                    $prefix = get_option('prefix_productions_orders_details');
                    break;
                case 'quotes':
                    $prefix = get_option('prefix_quotes');
                    break;
                case 'suggest_exporting':
                    $prefix = get_option('prefix_suggest_exporting');
                    break;
                case 'stock':
                    $prefix = get_option('prefix_stock');
                    break;
                case 'orders':
                    $prefix = get_option('prefix_orders');
                    break;
                case 'deliveries':
                    $prefix = get_option('prefix_deliveries');
                    break;
                case 'export_warehouses':
                    $prefix = get_option('prefix_export_warehouses');
                    break;
                case 'purchase_products':
                    $prefix = get_option('prefix_purchase_products');
                    break;
                case 'warehousing':
                    $prefix = get_option('prefix_warehousing');
                    break;
                case 'purchase_internal':
                    $prefix = get_option('prefix_purchase_internal');
                    break;
                case 'qc':
                    $prefix = get_option('prefix_qc');
                    break;
                case 'returned_goods':
                    $prefix = get_option('prefix_returned_goods');
                    break;
                case 'outsource':
                    $prefix = get_option('prefix_outsource');
                    break;
                case 'import_outsource':
                    $prefix = get_option('prefix_import_outsource');
                    break;
                case 'personnel':
                    $prefix = get_option('prefix_personnel');
                    break;
                case 'kpi':
                    $prefix = get_option('prefix_kpi');
                    break;
                case 'checkQuality':
                    $prefix = 'QC';
                    break;
                case 'manufacture':
                    $prefix = get_option('prefix_manufacture');
                    break;
                case 'tranfer_tp':
                    $prefix = get_option('prefix_tranfer_tp');
                    break;
                case 'production_lists':
                    $prefix = get_option('prefix_production_lists');
                    break;
                case 'suggest_ticket_purchase_product':
                    $prefix = 'YCNKTP';
                    break;
                case 'suggest_replace':
                    $prefix = 'YCVTTT';
                    break;
                case 'quotation_request':
                    $prefix = 'RFQ';
                    break;
                case 'purchase_order_request':
                    $prefix = 'POR';
                    break;
                case 'production_order_request':
                    $prefix = 'PROR';
                    break;
                case 'suggest_paid_holiday':
                    $prefix = 'YCNP';
                    break;
                case 'suggest_rating_system':
                    $prefix = 'YCĐGHT';
                    break;
                case 'suggest_rating_process':
                    $prefix = 'YCĐGQT';
                    break;
                case 'suggest_rating_machines':
                    $prefix = 'YCĐGTB';
                    break;
                case 'purchase_plan_purchase_npl':
                    $prefix = 'YCMNPL';
                    break;
                case 'purchase_plan_purchase_vt':
                    $prefix = 'YCMVT';
                    break;
                case 'purchase_plan_purchase_machines':
                    $prefix = 'YCMTB';
                    break;
                case 'suggest_plan_evaluate':
                    $prefix = 'YCKHĐG';
                    break;
                case 'suggest_plan_overtime':
                    $prefix = 'YCKHTC';
                    break;
                case 'stock_out_request':
                    $prefix = 'SOR';
                    break;
                case 'suggest_plan_outsource':
                    $prefix = 'YCKHGC';
                    break;
                case 'suggest_plan_recruitment':
                    $prefix = 'YCKHTD';
                    break;
                case 'purchase_request_material':
                    $prefix = 'YCMNPL';
                    break;
                case 'purchase_request_zinc':
                    $prefix = 'YCMGK';
                    break;
                case 'suggest_plan_educate':
                    $prefix = 'YCKHĐT';
                    break;
                case 'suggest_maintenance':
                    $prefix = 'YCBD';
                    break;
                case 'suggest_check':
                    $prefix = 'YCKT';
                    break;
                case 'suggest_task':
                    $prefix = 'YCCV';
                    break;
                case 'request_template':
                    $prefix = 'YCPTM';
                    break;
                case 'ptm':
                    $prefix = 'YCPTM';
                    break;
                case 'request_printed_page_layout':
                    $prefix = 'YCPDTI';
                    break;
                case 'request_graft_size':
                    $prefix = 'YCGS';
                    break;
                case 'request_export_products':
                    $prefix = 'YCXKTPT';
                    break;
                case 'request_calibration':
                    $prefix = 'YCHC';
                    break;
                case 'suggest_recruitment':
                    $prefix = 'YCTD';
                    break;
                case 'suggest_control_vehicle':
                    $prefix = 'YCĐX';
                    break;
                case 'suggest_educate':
                    $prefix = 'YCĐT';
                    break;
                case 'suggest_evaluate':
                    $prefix = 'YCĐG';
                    break;
                case 'request_repair':
                    $prefix = 'YCSC';
                    break;
                case 'request_improve':
                    $prefix = 'YCCT';
                    break;
                case 'request_bussiness':
                    $prefix = 'PYCCT';
                    break;
                case 'suggest_outsource':
                    $prefix = 'YCGC';
                    break;
                case 'request_client_complaints':
                    $prefix = 'YCXLKNKH';
                    break;
                case 'request_system_control':
                    $prefix = 'ĐGHT';
                    break;
                case 'request_overtime':
                    $prefix = 'YCTC';
                    break;
                case 'request_place_the_tank_mold':
                    $prefix = 'YCDKB';
                    break;
                case 'suggest_bonus_disciplines':
                    $prefix = 'YCKTKL';
                    break;
                case 'decision_bonus_discipline':
                    $prefix = 'QĐKTKL';
                    break;
                case 'suggest_kpi':
                    $prefix = 'YCĐGKPI';
                    break;
                case 'request_control_vehicle_bussiness':
                    $prefix = 'YCĐXCT';
                    break;
                case 'request_plan_calibration':
                    $prefix = 'YCKHHC';
                    break;
                case 'suggest_purchase_npl':
                    $prefix = 'YCNKNPL';
                    break;
                case 'suggest_additional_personnel':
                    $prefix = 'YCBSNS';
                    break;
                case 'repair_plan':
                    $prefix = 'YCKHSC';
                    break;
                case 'production_report':
                    $prefix = get_option('prefix_production_report');
                    break;
                case 'suggest_payslips':
                    $prefix = 'YCC';
                    break;
                case 'suggest_probationary_evaluate':
                    $prefix = 'YCĐGTV';
                    break;
                case 'suggest_employee_evaluate':
                    $prefix = 'YCĐGNV';
                    break;
                case 'suggest_skill_evaluate':
                    $prefix = 'YCĐGTN';
                    break;
                case 'probationary_evaluate':
                    $prefix = 'ĐGTV';
                    break;
                case 'suggest_pccc':
                    $prefix = 'YCPCCC';
                    break;
                case 'suggest_accreditation':
                    $prefix = 'YCKD';
                    break;
                case 'suggest_overcome_product':
                    $prefix = 'YCNKTPV';
                    break;
                case 'in_and_out_of_work':
                    $prefix = 'PRC';
                    break;
                case 'suggest_social_insurance':
                    $prefix = 'YCBH';
                    break;
                case 'suggest_social_welfare':
                    $prefix = 'YCPLXH';
                    break;
                case 'suggest_union':
                    $prefix = 'YCCĐ';
                    break;
                case 'suggest_personal_income_tax':
                    $prefix = 'YCTTNCN';
                    break;
                case 'suggest_plan_deparment':
                    $prefix = 'YCKHPB';
                    break;
                case 'probationary_assessment':
                    $prefix = 'DGTV';
                    break;
                case 'probationary_assessment_ct':
                    $prefix = 'DGCT';
                    break;
                case 'entrance_ticket':
                    $prefix = 'RVC';
                    break;
                default:
                    $prefix = '';
            }

            $separator = get_option('separator');
            $format_date_prefix = get_option('format_date_prefix');
            $ref_no = (!empty($prefix)) ? $prefix . "$separator" : '';
            $ref_no .= date("$format_date_prefix") . sprintf("%02s", $ref->{$field});
            // if ($this->Settings->reference_format == 1) {
            //     $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            // } elseif ($this->Settings->reference_format == 2) {
            //     $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            // } elseif ($this->Settings->reference_format == 3) {
            //     $ref_no .= sprintf("%04s", $ref->{$field});
            // } else {
            //     $ref_no .= $this->getRandomReference();
            // }

            return $ref_no;
        }
        return FALSE;
    }
}

if (!function_exists('countReferenceMinus')) {
    function countReferenceMinus($field)
    {
        $ct = 0;
        switch ($field) {
            case 'productions_plan':
                $ct += strlen(get_option('prefix_productions_plan'));
                break;
            case 'business_plan':
                $ct += strlen(get_option('prefix_business_plan'));
                break;
            case 'productions_capacity':
                $ct += strlen(get_option('prefix_productions_capacity'));
                break;
            case 'productions_orders':
                $ct += strlen(get_option('prefix_productions_orders'));
                break;
            case 'productions_orders_details':
                $ct += strlen(get_option('prefix_productions_orders_details'));
                break;
            case 'quotes':
                $ct += strlen(get_option('prefix_quotes'));
                break;
            case 'suggest_exporting':
                $ct += strlen(get_option('prefix_suggest_exporting'));
                break;
            case 'stock':
                $ct += strlen(get_option('prefix_stock'));
                break;
            case 'orders':
                $ct += strlen(get_option('prefix_orders'));
                break;
            case 'deliveries':
                $ct += strlen(get_option('prefix_deliveries'));
                break;
            case 'purchase_products':
                $ct += strlen(get_option('prefix_purchase_products'));
                break;
            case 'purchase_internal':
                $ct = strlen(get_option('prefix_purchase_internal'));
                break;
            case 'qc':
                $ct = strlen(get_option('prefix_qc'));
                break;
            case 'returned_goods':
                $ct = strlen(get_option('prefix_returned_goods'));
                break;
            case 'outsource':
                $ct = strlen(get_option('prefix_outsource'));
                break;
            default:
                $ct += 0;
        }
        // if ($field == "productions_orders") {
        // 	$ct+= strlen(get_option('prefix_productions_orders'));
        // }
        $format_date_prefix = get_option('format_date_prefix');
        if ($format_date_prefix == "dmY") {
            $ct = $ct + 8;
        }
        $separator = get_option('separator');
        $ct += strlen($separator);
        return $ct;
    }
}

if (!function_exists('subReference')) {
    function subReference($str)
    {
        $max = preg_replace('/[^0-9]/', '', $str);
        if (get_option('format_date_prefix') == "dmY") {
            $max = substr($max, 8);
        }
        $max = ceil($max) + 1;
        return $max;
    }
}

if (!function_exists('updateReferenceNormal')) {
    function updateReferenceNormal($field, $number)
    {
        $CI = &get_instance();
        return $CI->db->update('tbl_order_ref', [$field => $number], array('ref_id' => '1'));
    }
}

if (!function_exists('updateReference')) {
    function updateReference($field)
    {
        $CI = &get_instance();
        $q = $CI->db->get_where('tbl_order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            $CI->db->update('tbl_order_ref', array($field => $ref->{$field} + 1), array('ref_id' => '1'));
            return TRUE;
        }
        return FALSE;
    }
}

if (!function_exists('formatSAC')) {
    function formatSAC($num)
    {
        $pos = strpos((string)$num, ".");
        if ($pos === false) {
            $decimalpart = "00";
        } else {
            $decimalpart = substr($num, $pos + 1, 2);
            $num = substr($num, 0, $pos);
        }

        if (strlen($num) > 3 & strlen($num) <= 12) {
            $last3digits = substr($num, -3);
            $numexceptlastdigits = substr($num, 0, -3);
            $formatted = $this->makecomma($numexceptlastdigits);
            $stringtoreturn = $formatted . "," . $last3digits . "." . $decimalpart;
        } elseif (strlen($num) <= 3) {
            $stringtoreturn = $num . "." . $decimalpart;
        } elseif (strlen($num) > 12) {
            $stringtoreturn = number_format($num, 2);
        }

        if (substr($stringtoreturn, 0, 2) == "-,") {
            $stringtoreturn = "-" . substr($stringtoreturn, 2);
        }

        return $stringtoreturn;
    }
}

if (!function_exists('formatDecimalMoney')) {
    function formatDecimalMoney($number, $decimals = NULL)
    {
        if (!is_numeric($number)) {
            return NULL;
        }
        if (!$decimals) {
            $decimals = get_option('decimals_money');
        }
        return number_format($number, $decimals, '.', '');
    }
}


if (!function_exists('formatNumber')) {
    function formatNumber($number, $decimals = NULL)
    {
        // if (!$decimals) {
        if ($decimals === NULL) {
            $decimals = get_option('decimals_number');
        }

        if (!is_decimal($number)) {
            $decimals = 0;
        }

        $ts = get_option('thousands_sep') == '0' ? ' ' : get_option('thousands_sep');
        $ds = get_option('decimals_sep');
        return number_format($number, $decimals, $ds, $ts);
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($number, $decimals = NULL)
    {
        if (get_option('sac')) {
            return formatSAC(formatDecimalMoney($number));
        }
        if ($decimals === NULL) {
            $decimals = get_option('decimals_money');
        }

        if (!is_decimal($number)) {
            $decimals = 0;
        }

        $ts = get_option('thousands_sep') == '0' ? ' ' : get_option('thousands_sep');
        $ds = get_option('decimals_sep');
        return number_format($number, $decimals, $ds, $ts);
    }
}

if (!function_exists('formatBox')) {
    function formatBox($number, $decimals = NULL)
    {
        $decimals = 0;

        $ts = get_option('thousands_sep') == '0' ? ' ' : get_option('thousands_sep');
        $ds = get_option('decimals_sep');
        return number_format($number, $decimals, $ds, $ts);
    }
}

if (!function_exists('status_productions_plan')) {
    function status_productions_plan()
    {
        // $option['keep'] = lang('tnh_keep_stock_material');
        $option['not_keep'] = lang('c_note_keep_stock_material');
        $option['keep_apart_full'] = lang('Giữ kho 1 phần (đủ NPL giữ tiếp)');
        $option['keep_apart_not_full'] = lang('Giữ kho 1 phần (chưa đủ NPL)');
        $option['keep_full'] = lang('Đã giữ đủ');
        // $option['ycmh'] = lang('tnh_ycmh');
        // $option['capacity'] = lang('capacity');
        return $option;
    }
}

if (!function_exists('recursive_stages')) {
    function recursive_stages(&$output = null, $parent_id = 0, $indent = null, $is_selected = 0)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_stages');
        $CI->db->where('tbl_stages.parent_id', $parent_id);
        $CI->db->where('tbl_stages.type !=', 7);
        if (defined('TYPE_USE') && TYPE_USE == 1) {
            $CI->db->where('tbl_stages.type_use', 0);
        }
        $CI->db->order_by('tbl_stages.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($parent_id == 0) {
                    // $disabled = 'disabled';
                }
                // data-icon="fa fa-ellipsis-h"

                $CI->db->select('tbl_stage_criteria.*');
                $CI->db->from('tbl_stage_criteria');
                $CI->db->where('tbl_stage_criteria.stage_id', $item['id']);
                $stage_criteria = $CI->db->get()->result_array();
                $json_stage_criteria = '';
                if (!empty($stage_criteria)) {
                    $json_stage_criteria = htmlentities(json_encode($stage_criteria, JSON_UNESCAPED_UNICODE));
                }

                $selected = '';
                if ($is_selected) {
                    if ($item['id'] == STAGES_MATERIAL) {
                        $selected = 'selected';
                    }
                }

                $output .= '<option data-subtext="' . $item['code'] . '" ' . $selected . ' ' . $disabled . ' data-json_stage_criteria="' . $json_stage_criteria . '" value="' . $item['id'] . '">' . $indent . $item['name'] . "</option>";
                recursive_stages($output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;", $is_selected);
            }
        }

        return $output;
    }
}

if (!function_exists('recursive_stages_array')) {
    function recursive_stages_array(&$output = [], $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_stages');
        $CI->db->where('tbl_stages.parent_id', $parent_id);
        if (defined('TYPE_USE') && TYPE_USE == 1) {
            $CI->db->where('tbl_stages.type_use', 0);
        }
        $CI->db->order_by('tbl_stages.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($parent_id == 0) {
                    // $disabled = 'disabled';
                }
                // data-icon="fa fa-ellipsis-h"
                $output[] = $item;
                recursive_stages_array($output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }
}

if (!function_exists('recursiveCategoryItems')) {
    function recursiveCategoryItems($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_category_items');
        $CI->db->where('tbl_category_items.parent_id', $parent_id);
        $CI->db->order_by('tbl_category_items.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }
                // if ($parent_id == 0) {
                // 	$disabled = 'disabled';
                // }
                // data-icon="fa fa-ellipsis-h"
                $output .= '<option data-code="' . $item['code'] . '" ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['name'] . '(' . $item['code'] . ')' . "</option>";
                recursiveCategoryItems($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }
}

if (!function_exists('recursiveCategoryProducts')) {
    function recursiveCategoryProducts($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_category_products');
        $CI->db->where('tbl_category_products.parent_id', $parent_id);
        $CI->db->order_by('tbl_category_products.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }

                $item['code'] = str_replace('\'', '', $item['code']);
                $item['code'] = str_replace('"', '', $item['code']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('/n', '', $item['name']);
                $item['name'] = str_replace('\n', '', $item['name']);
                $item['code'] = str_replace('\n', '', $item['code']);
                $item['code'] = str_replace('/n', '', $item['code']);
                $output .= '<option data-code="' . $item['code'] . '" ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['name'] . '(' . $item['code'] . ')' . "</option>";
                recursiveCategoryProducts($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }
}

if (!function_exists('status_productions_capacity')) {
    function status_productions_capacity()
    {
        $option['un_approved'] = lang('un_approved');
        $option['approved'] = lang('approved');
        $option['purchases'] = lang('tnh_st_purchases');
        $option['un_purchases'] = lang('tnh_st_un_purchases');
        return $option;
    }
}

if (!function_exists('tnh_html_entity_decode')) {
    function tnh_html_entity_decode($string)
    {
        if (empty($string)) $string = '';
        return html_entity_decode($string);
        // return $string;
    }
}

if (!function_exists('tnh_htmlentities')) {
    function tnh_htmlentities($string)
    {
        return htmlentities($string, ENT_QUOTES);
        // return $string;
    }
}

if (!function_exists('checkModule')) {
    function checkModule($module)
    {
        $CI = &get_instance();
        $CI->db->from('tblmodules');
        $CI->db->where('module_name', $module);
        $CI->db->where('active', 1);
        return $CI->db->get()->num_rows();
    }
}

if (!function_exists('refererModel')) {
    function refererModel($message)
    {
        set_alert('danger', $message);
        die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('admin')) . "'; }, 10);</script>");
    }
}

if (!function_exists('recursiveLocations')) {
    function recursiveLocations($id_parent, &$output = null, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbllocaltion_warehouses');
        $CI->db->where('tbllocaltion_warehouses.id', $id_parent);
        $query = $CI->db->get()->row_array();
        $name = $query['name'];
        $id_parent = $query['id_parent'];

        $output = $name . '->' . $output;
        if ($id_parent != 0) {
            recursiveLocations($id_parent, $output);
        }
        return substr($output, 0, -2);
    }
}

if (!function_exists('minusDate')) {
    function minusDate($dateStart, $dateEnd)
    {
        $dateStart = new DateTime(to_sql_date(trim($dateStart)));
        $dateEnd = new DateTime(to_sql_date(trim($dateEnd)));
        $diff = $dateStart->diff($dateEnd);
        return $diff->days;
    }
}

if (!function_exists('minusDateFormat')) {
    function minusDateFormat($dateStart, $dateEnd)
    {
        $dateStart = new DateTime($dateStart);
        $dateEnd = new DateTime($dateEnd);
        $diff = $dateStart->diff($dateEnd);
        return $diff->days;
    }
}

if (!function_exists('minusDateNotFormat')) {
    function minusDateNotFormat($dateStart, $dateEnd)
    {
        $dateStart = date_create($dateStart);
        $dateEnd = date_create($dateEnd);
        $diff = date_diff($dateEnd, $dateStart);
        return $diff->format("%R%a");
        // $dateStart = new DateTime($dateStart);
        // $dateEnd = new DateTime($dateEnd);
        // $diff = $dateStart->diff($dateEnd);
        // return $diff->days;
    }
}

if (!function_exists('convertCustomerLeadToCustomer')) {
    function convertCustomerLeadToCustomer($customer_id, $address_delivery_id = 0)
    {
        $data = false;
        if (!empty($customer_id)) {
            $CI = &get_instance();
            //check exist leads in customer
            $CI->db->select('*');
            $CI->db->from('tblclients');
            $CI->db->where('tblclients.leadid', $customer_id);
            $CI->db->limit(1);
            $customer = $CI->db->get()->row_array();
            if (!empty($customer)) {
                $data['customer_id'] = $customer['userid'];
                $data['customer_name'] = $customer['company'];
                $data['address_delivery_id'] = 0;
            } else {
                $CI->db->where('id', $customer_id);
                $lead = $CI->db->get('tblleads')->row();
                if (!empty($lead)) {
                    $CI->db->where('leadid', $lead->id);
                    $ktClient = $CI->db->get('tblclients')->row();
                    if (empty($ktClient)) {
                        $first_date = strtotime(date('Y-m-d'));
                        $second_date = strtotime($lead->date_contact);
                        $datediff = abs($first_date - $second_date);
                        $leadtime = floor($datediff / (60 * 60 * 24));

                        $arrayAdd = [
                            'email_client' => $lead->email,
                            'birtday' => $lead->birtday,
                            'note' => $lead->description,
                            'code_system' => $lead->code_system,
                            'company' => $lead->company,
                            'fullname' => $lead->name,
                            'phonenumber' => $lead->phonenumber,
                            'id_facebook' => $lead->id_facebook,
                            'leadid' => $lead->id,
                            'zcode' => $lead->zcode,
                            'datecreated' => date('Y-m-d H:i:s'),
                            'addedfrom' => get_staff_user_id(),
                            'dt' => $lead->dt,
                            'kt' => $lead->kt,
                            'religion' => $lead->religion,
                            'marriage' => $lead->marriage,
                            'city' => $lead->city,
                            'district' => $lead->district,
                            'ward' => $lead->ward,
                            'date_contact' => $lead->date_contact,
                            'name_facebook' => $lead->name_facebook,
                            'link_facebook' => $lead->link_facebook,
                            'leadtime' => $leadtime
                        ];
                        $CI->db->insert('tblclients', $arrayAdd);
                        if ($CI->db->insert_id()) {
                            $idClient = $CI->db->insert_id();
                            CreateCode('client', $idClient);
                            $data['customer_id'] = $idClient;
                            $data['customer_name'] = $lead->company;
                        }
                        $CI->db->where('lead', $lead->id);
                        $lead->info_group = $CI->db->get('tbllead_value')->result_array();
                        if (!empty($lead->info_group)) {
                            foreach ($lead->info_group as $kInfo => $vInfo) {
                                $arrayInfo = [
                                    'id_detail' => $vInfo['id_detail'],
                                    'value' => $vInfo['value'],
                                    'client' => $idClient,
                                ];
                                $CI->db->insert('tblclient_value', $arrayInfo);
                            }
                        }

                        $img_lead = get_upload_path_by_type('lead') . $lead->id . '/';
                        $img_client = get_upload_path_by_type('customer') . $idClient . '/';
                        _maybe_create_upload_path($img_client);
                        @copy($img_lead . 'small_' . $lead->lead_image, $img_client . 'small_' . $lead->lead_image);
                        @copy($img_lead . 'thumb_' . $lead->lead_image, $img_client . 'thumb_' . $lead->lead_image);

                        $arrayUpdateClient = [
                            'client_image' => $lead->lead_image
                        ];

                        $CI->db->where('userid', $idClient);
                        $CI->db->update('tblclients', $arrayUpdateClient);

                        $CI->db->where('lead_id', $lead->id);
                        $shipping = $CI->db->get('tblshipping_lead')->result_array();

                        $data['address_delivery_id'] = 0;
                        foreach ($shipping as $key => $value) {
                            $id_shipping = $value['id'];
                            unset($value['id']);
                            unset($value['lead_id']);
                            $value['client'] = $idClient;

                            $CI->db->insert('tblshipping_client', $value);
                            $id_new_shipping = $CI->db->insert_id();
                            if ($address_delivery_id == $id_shipping) {
                                $data['address_delivery_id'] = $id_new_shipping;
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}

if (!function_exists('recursiveLocationWarehouses')) {
    function recursiveLocationWarehouses($id, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbllocaltion_warehouses');
        $CI->db->where('tbllocaltion_warehouses.id_parent', $parent_id);
        $CI->db->where('tbllocaltion_warehouses.warehouse', $id);
        $CI->db->where('tbllocaltion_warehouses.pod_id', 0);
        $CI->db->where('tbllocaltion_warehouses.stage_id', 0);
        $CI->db->where('tbllocaltion_warehouses.productions_plan_id', 0);
        $CI->db->order_by('tbllocaltion_warehouses.id_parent');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['id_parent'] == $parent_id) {
                $disabled = '';
                $CI->db->from('tbllocaltion_warehouses');
                $CI->db->where('tbllocaltion_warehouses.id_parent', $item['id']);
                $CI->db->limit(1);
                $q = $CI->db->get()->num_rows();
                if ($q) {
                    $disabled = 'disabled';
                }
                $output .= '<option ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['name'] . "</option>";
                recursiveLocationWarehouses($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }
        return $output;
    }
}

if (!function_exists('stylePdf')) {
    function stylePdf()
    {
        echo '
			<style>
				body {
				}
	            .text-center {
	                text-align: center;
	            }
                .text-left {
	                text-align: left;
	            }
	            .text-right {
	                text-align: right;
	            }
	            .uppercase {
	            	text-transform: uppercase;
	            }
	            .row {
	            }
	            .mtop10 {
					margin-top: 10px;
	            }
	            .table {
	            	width: 100%;
	            }
	            .italic {
	            	font-style: italic;
	            }
	            div {
	            	margin: 0;
	            	padding: 0;
	            }
	            .bold {
	            	font-weight: bold;
	            }
	            .table-items {
	            	border-collapse: collapse;
	            	width: 100%;
	            }
	            .table-items tr th, .table-items tr td {
	            	border: 1px solid black;
	            	vertical-align: middle;
	            }
                .font-small {
                    font-size: 12px;
                }
                .font-company {
                    font-size: 15px;
                }
            </style>';
    }
}

if (!function_exists('checkView')) {
    function checkView($fields, $list_users, $id)
    {
        $CI = &get_instance();
        $flag = false;
        if ($fields == "orders") {
            if (!empty($list_users)) {
                $staff_id = get_staff_user_id();
                $list_users = explode(',', $list_users);
                if (($key = array_search($staff_id, $list_users)) !== false) {
                    unset($list_users[$key]);
                    $CI->db->where('id', $id);
                    $CI->db->update('tbl_orders', ['list_users' => implode(',', $list_users)]);
                    $flag = true;
                }
            }
        } else if ($fields == "deliveries") {
            if (!empty($list_users)) {
                $staff_id = get_staff_user_id();
                $list_users = explode(',', $list_users);
                if (($key = array_search($staff_id, $list_users)) !== false) {
                    unset($list_users[$key]);
                    $CI->db->where('id', $id);
                    $CI->db->update('tbl_deliveries', ['list_users' => implode(',', $list_users)]);
                    $flag = true;
                }
            }
        }
        return $flag;
    }
}

if (!function_exists('sendEmail')) {
    function sendEmail($data = [])
    {
        if (empty($data)) {
            return false;
        }
        $CI = &get_instance();
        $CI->load->config('email');

        $email = $data['email'];
        $message = $data['message'];
        $subject = $data['subject'];
        // Simulate fake template to be parsed
        $template = new StdClass();
        $template->message = get_option('email_header') . $message . get_option('email_footer');
        $template->fromname = get_option('companyname') != '' ? get_option('companyname') : 'TEST';
        $template->subject = $subject;

        $template = parse_email_template($template);

        hooks()->do_action('before_send_test_smtp_email');
        $CI->email->initialize();
        if (get_option('mail_engine') == 'phpmailer') {
            $CI->email->set_debug_output(function ($err) {
                if (!isset($GLOBALS['debug'])) {
                    $GLOBALS['debug'] = '';
                }
                $GLOBALS['debug'] .= $err . '<br />';

                return $err;
            });
            $CI->email->set_smtp_debug(3);
        }

        $CI->email->set_newline(config_item('newline'));
        $CI->email->set_crlf(config_item('crlf'));

        $CI->email->from(get_option('smtp_email'), $template->fromname);
        $CI->email->to($email);

        $systemBCC = get_option('bcc_emails');

        if ($systemBCC != '') {
            $CI->email->bcc($systemBCC);
        }

        $CI->email->subject($template->subject);
        $CI->email->message($template->message);
        if (!empty($data['attachment'])) {
            $CI->email->attach($data['attachment']['base64'], $data['attachment']['type'], $data['attachment']['name'], $data['attachment']['type_file']);
        }
        if ($CI->email->send(true)) {
            return true;
            // set_alert('success', 'Seems like your SMTP settings is set correctly. Check your email now.');
            // hooks()->do_action('smtp_test_email_success');
            // echo 'Seems like your SMTP settings is set correctly. Check your email now.';
        } else {
            return false;
            // set_debug_alert('<h1>Your SMTP settings are not set correctly here is the debug log.</h1><br />' . $CI->email->print_debugger() . (isset($GLOBALS['debug']) ? $GLOBALS['debug'] : ''));
            // hooks()->do_action('smtp_test_email_failed');
        }
    }
}

if (!function_exists('accessDenied')) {
    function accessDenied($js = false)
    {
        $CI = &get_instance();
        set_alert('danger', lang('access_denied'));
        if ($js) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('admin')) . "'; }, 10);</script>");
        } else {
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin');
        }
    }
}

if (!function_exists('checkMyData')) {
    function checkMyData($check_id, $js = NULL)
    {
        return TRUE;
        if (!is_admin()) {
            if ($check_id != get_staff_user_id()) {
                set_alert('danger', lang('access_denied'));
                if ($js) {
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('admin')) . "'; }, 10);</script>");
                } else {
                    redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin');
                }
            }
        }
        return TRUE;
    }
}

if (!function_exists('checkMyDataTF')) {
    function checkMyDataTF($check_id)
    {
        return true;
        if (!is_admin()) {
            if ($check_id != get_staff_user_id()) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('typeChargeParty')) {
    function typeChargeParty($type = 'all')
    {
        $option['company'] = lang('tnh_company');
        $option['customer'] = lang('tnh_customer');
        return $option;
    }
}

if (!function_exists('typeCheckQuality')) {
    function typeCheckQuality($type = 'all')
    {
        $option['allowed_to_stock'] = lang('tnh_allowed_to_stock');
        $option['un_allowed_to_stock'] = lang('tnh_un_allowed_to_stock');
        return $option;
    }
}

if (!function_exists('recursiveCategoryErrors')) {
    function recursiveCategoryErrors($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_category_errors');
        $CI->db->where('tbl_category_errors.parent_id', $parent_id);
        $CI->db->order_by('tbl_category_errors.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }
                $output .= '<option ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['name'] . '(' . $item['code'] . ')' . "</option>";
                recursiveCategoryErrors($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }
}

if (!function_exists('pusherTNHNotfication')) {
    function pusherTNHNotfication()
    {
        if (get_option('pusher_realtime_notifications') == 0) {
            return false;
        }

        $channels = ['tnh-notification'];
        $channels = array_unique($channels);

        $CI = &get_instance();

        $CI->load->library('app_pusher');

        $CI->app_pusher->trigger($channels, 'tnh-notification', []);
    }
}

if (!function_exists('typeHandlingSolution')) {
    function typeHandlingSolution()
    {
        $option['debt_reduction'] = lang('tnh_debt_reduction');
        $option['pay_down'] = lang('tnh_pay_down');
        return $option;
    }
}

if (!function_exists('typeNotificationForm')) {
    function typeNotificationForm()
    {
        $option['noti_phone'] = lang('noti_phone');
        $option['noti_email'] = lang('noti_email');
        $option['noti_zalo'] = lang('noti_zalo');
        $option['noti_note_other'] = lang('noti_note_other');
        return $option;
    }
}

if (!function_exists('genBarcode')) {
    function genBarcode($code = NULL, $bcs = 'code128', $height = 30, $text = 1)
    {
        $CI = &get_instance();
        $drawText = ($text != 1) ? FALSE : TRUE;
        $CI->load->library('zend');
        $CI->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $code, 'barHeight' => $height, 'drawText' => $drawText);
        $rendererOptions = array('horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        //Zend_Barcode::render('code128', 'image', $barcodeOptions, $rendererOptions);
        $renderer = Zend_Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions);
        $file = $renderer->draw();
        $pathName = 'file/barcode/barcode_po.png';
        $store_image = @imagepng($file, $pathName);
        return $pathName;
    }
}

if (!function_exists('insertActivityLog')) {
    function insertActivityLog($data = [], $staff_id = 0)
    {
        $staff_id = empty($staff_id) ? get_staff_user_id() : $staff_id;
        $date_created = date('Y-m-d H:i:s');

        $field = [
            'type_parent_obj' => $data['type_parent_obj'],
            'table_obj' => $data['table_obj'],
            'id_obj' => $data['id_obj'],
            'name_obj' => $data['name_obj'],
            'content' => $data['content'],
            'staff_id' => $staff_id,
            'date' => $date_created,
            'actions' => $data['actions']
        ];

        $CI = &get_instance();
        $CI->db->insert('tblactivity_log_v2', $field);
        return $CI->db->insert_id();
    }
}

if (!function_exists('getActivityLogByObjId')) {
    function getActivityLogByObjId($id_obj, $module_history)
    {
        $CI = &get_instance();

        $CI->db->select('tblactivity_log_v2.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name');
        $CI->db->from('tblactivity_log_v2');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblactivity_log_v2.staff_id', 'inner');
        $CI->db->where_in('tblactivity_log_v2.id_obj', $id_obj);
        $CI->db->where_in('tblactivity_log_v2.type_parent_obj', $module_history);
        $CI->db->order_by('tblactivity_log_v2.date DESC');
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('getRelationship')) {
    function getRelationship($op = false)
    {
        $option['father'] = lang('tnh_father');
        $option['mother'] = lang('tnh_mother');
        $option['brother'] = lang('tnh_brother');
        $option['sister'] = lang('tnh_sister');
        $option['younger'] = lang('tnh_younger');
        $option['wife'] = lang('tnh_wife');
        $option['child'] = lang('tnh_child');
        $option['husband'] = lang('tnh_husband');
        $option['other'] = lang('tnh_other');
        if (!empty($op)) {
            return $option[$op];
        }
        return $option;
    }
}

if (!function_exists('getLiteracy')) {
    function getLiteracy($op = false)
    {
        $option['primary'] = lang('tnh_primary');
        $option['intermediate'] = lang('tnh_intermediate');
        $option['colleges'] = lang('tnh_colleges');
        $option['vocational'] = lang('tnh_vocational');
        $option['vocational_colleges'] = lang('tnh_vocational_colleges');
        $option['elementary_occupations'] = lang('tnh_elementary_occupations');
        $option['university'] = lang('tnh_university');
        $option['master'] = lang('tnh_master');
        $option['phd'] = lang('tnh_phd');
        $option['other'] = lang('tnh_other');
        if (!empty($op)) {
            return $option[$op];
        }
        return $option;
    }
}

if (!function_exists('getClassification')) {
    function getClassification($op = false)
    {
        $option['great'] = lang('tnh_great');
        $option['rather'] = lang('tnh_rather');
        $option['medium'] = lang('tnh_medium');
        $option['other'] = lang('tnh_other');
        if (!empty($op)) {
            return $option[$op];
        }
        return $option;
    }
}

if (!function_exists('getReceivePersonnel')) {
    function getReceivePersonnel()
    {
        $option[1] = lang('tnh_cmnd_cc_hc');
        $option[2] = lang('tnh_ttke');
        $option[3] = lang('tnh_syll');
        $option[4] = lang('tnh_bc');
        $option[5] = lang('tnh_gksk');
        $option[6] = lang('tnh_acn');
        $option[7] = lang('tnh_bsgks');
        $option[8] = lang('tnh_bsshk');
        return $option;
    }
}

if (!function_exists('getFormInsurrance')) {
    function getFormInsurrance($op = false)
    {
        $option[1] = lang('tnh_bt');
        $option[2] = lang('tnh_bg');
        if (!empty($op)) {
            return $option[$op];
        }
        return $option;
    }
}

if (!function_exists('createdPopupNotification')) {
    function createdPopupNotification($field, $id, $title)
    {
        $CI = &get_instance();
        $link = '';
        $date = '';
        if ($field == "orders") {
            $CI->db->select('tbl_orders.date, tbl_orders.reference_no');
            $CI->db->from('tbl_orders');
            $CI->db->where('tbl_orders.id', $id);
            $result = $CI->db->get()->row_array();
            if (!empty($result)) {
                $link = '<a data-tnh="modal" class="tnh-modal" onClick="clickCloseClassify(this)" href="' . base_url('admin/orders/view_order/' . $id) . '" data-toggle="modal" data-target="#myModal">' . $result['reference_no'] . '</a>';
                $date = _d($result['date']);
            }
        } else if ($field == "purchases") {
            $CI->db->select('tblpurchases.date');
            $CI->db->from('tblpurchases');
            $CI->db->where('tblpurchases.id', $id);
            $result = $CI->db->get()->row_array();
            if (!empty($result)) {
                $link = '';
                $date = _d($result['date']);
            }
        }

        $html = '
        <div class="notifi-classify" style="top: 0px;" data-top="0">
            <div class="notifi-classify-img">
                <img width="80" src="https://i.gifer.com/XlQO.gif">
            </div>
            <div class="notifi-classify-content">
                <div class="bold uppercase text-center">' . $title . '</div>
                <div class="notifi-classify-title bold uppercase">' . $link . '</div>
                <div class="text-center">
                    <p style="background: #e69a07; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;"></p>
                </div>
                <div class="text-center">' . $date . '</div>
            </div>
            <div class="notifi-close-classify" onClick="clickCloseClassify(this)"><i class="fa fa-times"></i></div>
        </div>';
        return $html;
    }
}

if (!function_exists('getMonth')) {
    function getMonth()
    {
        $option[''] = '';
        $option['01'] = lang('01');
        $option['02'] = lang('02');
        $option['03'] = lang('03');
        $option['04'] = lang('04');
        $option['05'] = lang('05');
        $option['06'] = lang('06');
        $option['07'] = lang('07');
        $option['08'] = lang('08');
        $option['09'] = lang('09');
        $option['10'] = lang('10');
        $option['11'] = lang('11');
        $option['12'] = lang('12');
        return $option;
    }
}

if (!function_exists('getDateReportSales')) {
    function getDateReportSales($year, $precious = false, $month = false)
    {
        $data = [];
        if (!empty($precious)) {
            if ($precious == 1) {
                $start_date = "$year-01-01";
                $end_date = "$year-03-31";
            } else if ($precious == 2) {
                $start_date = "$year-04-01";
                $end_date = "$year-06-30";
            } else if ($precious == 3) {
                $start_date = "$year-07-01";
                $end_date = "$year-09-30";
            } else if ($precious == 4) {
                $start_date = "$year-10-01";
                $end_date = "$year-12-31";
            }
        } else if (!empty($month)) {
            $d = new DateTime("$year-$month-01");
            $d->modify('first day of this month');
            $start_date = $d->format('Y-m-d');

            $dLast = new DateTime("$year-$month-01");
            $dLast->modify('last day of this month');
            // $end_date = date('Y-m-d', strtotime("last day of this month"));
            $end_date = $dLast->format('Y-m-d');
        } else {
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        return $data;
    }
}

if (!function_exists('formatDecimalNumber')) {
    function formatDecimalNumber($number, $decimals = NULL)
    {
        if (!is_numeric($number)) {
            return NULL;
        }
        if (!$decimals) {
            $decimals = get_option('decimals_number');
        }
        return number_format($number, $decimals, '.', '');
    }
}

if (!function_exists('formatRound')) {
    function formatRound($number, $decimals = 0)
    {
        if (!$decimals) {
            $decimals = get_option('decimals_money');
        }
        return round($number, $decimals);
    }
}

if (!function_exists('formatNumberExcel')) {
    function formatNumberExcel($number, $decimals = NULL)
    {
        if (!$decimals) {
            $decimals = get_option('decimals_number');
        }

        if (!is_decimal($number)) {
            $decimals = 0;
        }

        return '#,##0' . ($decimals > 0 ? '.' . sprintf("%0" . $decimals . "s", 0) : '');
    }
}

if (!function_exists('formatMoneyExcel')) {
    function formatMoneyExcel($number, $decimals = NULL)
    {
        if (get_option('sac')) {
            return formatSAC(formatDecimalMoney($number));
        }
        if (!$decimals) {
            $decimals = get_option('decimals_money');
        }

        if (!is_decimal($number)) {
            $decimals = 0;
        }

        return '#,##0' . ($decimals > 0 ? '.' . sprintf("%0" . $decimals . "s", 0) : '');
    }
}

if (!function_exists('pr_chat_pusher_options')) {
    function pr_chat_pusher_options() {}
}

if (!function_exists('formatRoundNumber')) {
    function formatRoundNumber($number, $decimals = 0)
    {
        if (!$decimals) {
            $decimals = get_option('decimals_number');
        }
        return round($number, $decimals);
    }
}

if (!function_exists('handlingQC')) {
    function handlingQC($id_stage, $pod_id, $arr_id = [], $arr_id_not = [], $type, $pois_id, $cView = false)
    {
        $data = [];
        $qcReturn = [];
        $CI = &get_instance();
        // $CI->db->select('
        //     tbl_check_quality.id as id,
        //     tbl_check_quality.reference_no as reference_no,
        //     tbl_check_quality_items.id as cqi_id,
        //     tbl_check_quality_items.pod_id as pod_id,
        //     tbl_check_quality_items.quantity_qc as quantity_qc,
        //     tbl_check_quality_items.quantity_recycling as quantity_recycling,
        // ');
        // $CI->db->from('tbl_check_quality_items');
        // $CI->db->join('tbl_check_quality', 'tbl_check_quality.id = tbl_check_quality_items.check_quality_id');
        // $CI->db->where('tbl_check_quality_items.pod_id', $pod_id);
        // $CI->db->where('tbl_check_quality_items.id_stage_again', $id_stage);
        // $CI->db->where('tbl_check_quality_items.result', 2);
        // if (!empty($arr_id)) {
        //     $CI->db->where_in('tbl_check_quality_items.id', $arr_id);
        // }
        // if (!empty($arr_id_not)) {
        //     $CI->db->where_not_in('tbl_check_quality_items.id', $arr_id_not);
        // }
        // $check_quality_items_returns = $CI->db->get()->result_array();

        $CI->db->select('
            tbl_check_quality.id as id,
            tbl_check_quality.reference_no as reference_no,
            tbl_check_quality_items_stage.check_quality_items_id as cqi_id,
            tbl_check_quality_items_stage.pod_id as pod_id,
            tbl_check_quality_items.quantity_qc as quantity_qc,
            tbl_check_quality_items.quantity_recycling as quantity_recycling,
            tbl_check_quality_items_stage.active as active,
            tbl_check_quality_items_stage.staff_active as staff_active,
            tbl_check_quality_items_stage.date_active as date_active,
            tbl_check_quality_items_stage.final_stage as final_stage,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
            tblstaff.profile_image as profile_image,
            tbl_check_quality_items_stage.id as cqis_id,
            tbl_check_quality_items_stage.status_result as status_result,
            tbl_stages.status_qc as status_qc
        ');
        $CI->db->from('tbl_check_quality_items_stage');
        $CI->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
        $CI->db->join('tbl_check_quality', 'tbl_check_quality.id = tbl_check_quality_items_stage.check_quality_id');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tbl_check_quality_items_stage.staff_active', 'left');
        $CI->db->join('tbl_stages', 'tbl_stages.id = tbl_check_quality_items_stage.stage_id', 'left');
        $CI->db->where('tbl_check_quality_items_stage.pod_id', $pod_id);
        $CI->db->where('tbl_check_quality_items_stage.stage_id', $id_stage);
        if (!empty($arr_id)) {
            $CI->db->where_in('tbl_check_quality_items_stage.check_quality_items_id', $arr_id);
        }
        if (!empty($arr_id_not)) {
            $CI->db->where_not_in('tbl_check_quality_items_stage.check_quality_items_id', $arr_id_not);
        }
        $check_quality_items_returns = $CI->db->get()->result_array();
        $vQcReturn = '';
        $isStatusRemake = false;
        if (!empty($check_quality_items_returns)) {
            foreach ($check_quality_items_returns as $key => $vQC) {
                $cqi_id = $vQC['cqi_id'];
                $cqis_id = $vQC['cqis_id'];
                $status_qc = $vQC['status_qc'];
                $status_result = $vQC['status_result'];
                $tagRemake = '<div class="c-box c-box--arrow-right c-box-red">
                    ' . lang('tnh_remake') . '
                </div>';
                $qcRemake = null;
                if (!empty($vQC['active'])) {
                    $qcRemake = [
                        'status' => $vQC['active'],
                        'status_date' => $vQC['date_active'],
                        'type' => $type,
                        'staff_name' => $vQC['staff_name'],
                        'profile_image' => $vQC['profile_image'],
                    ];
                }
                // $qcRemake = $CI->manufactures_model->getQCRemakeCQIId($cqi_id);
                $agreeProcessRemake = '';
                $type = 0;
                if ($type == 0) {
                    if ($qcRemake) {
                        $agreeProcessRemake = '<span style="position: absolute;" onclick="agreeProcessRemake(this, \'' . $cqi_id . '\', 0, \'' . $type . '\', \'' . $pois_id . '\', 0, \'' . $cqis_id . '\')" data-toggle="tooltip" title="' . lang('tnh_un_finished') . '" class="fa system-checkmark-delete pointer"></span>';
                        $isStatusRemake = true;
                    } else {
                        $agreeProcessRemake = '<span style="position: absolute;" onclick="agreeProcessRemake(this, \'' . $cqi_id . '\', 1, \'' . $type . '\', \'' . $pois_id . '\', 0, \'' . $cqis_id . '\')" data-toggle="tooltip" title="' . lang('finished') . '" class="fa system-checkmark-checkbox pointer"></span>';
                    }
                } else if ($type == 1 || $type == 2 || $type == 3) {
                    if ($qcRemake) {
                        $agreeProcessRemake = '<span style="position: absolute;" onclick="agreeProcessRemake(this, \'' . $cqi_id . '\', 0, \'' . $type . '\', \'' . $pois_id . '\', 0, \'' . $cqis_id . '\')" data-toggle="tooltip" title="' . lang('tnh_un_finished') . '" class="fa system-checkmark-delete pointer"></span>';
                        $isStatusRemake = true;
                    } else {
                        $agreeProcessRemake = '<span style="position: absolute;" onclick="agreeProcessRemake(this, \'' . $cqi_id . '\', 1, \'' . $type . '\', \'' . $pois_id . '\', \'' . $vQC['quantity_recycling'] . '\', \'' . $cqis_id . '\')" data-toggle="tooltip" title="' . lang('finished') . '" class="fa system-checkmark-checkbox pointer"></span>';
                    }
                }

                if ($cView) {
                    $agreeProcessRemake = '';
                }

                $imgStaff = '';
                $staff_name = '';
                $dateActive = '';
                if (!empty($qcRemake)) {
                    $staff_name = $qcRemake['staff_name'];
                    $staff_image = base_url('assets/images/user-placeholder.jpg');
                    if (!empty($qcRemake['profile_image'])) {
                        $staff_image = base_url('uploads/staff_profile_images/' . $qcRemake['staff_status'] . '/small_' . $qcRemake['profile_image']);
                        $qcRemake['staff_image'] = $staff_image;
                        $dataProcess[$key]['staff_image'] = $staff_image;
                    }
                    if (!empty($staff_name)) {
                        $imgStaff = '<img src="' . $staff_image . '" data-toggle="tooltip" data-title="' . $staff_name . '" class="staff-profile-image-small mright5 staff-image-cs" alt="' . $staff_name . '" data-original-title="" title="' . $staff_name . '">';
                    }
                    $dateActive = _d($qcRemake['status_date']);
                }

                $qc = '';
                if ($status_qc == 1) {
                    if (empty($status_result)) {
                        $qc = '<span style="width: 60px;" class="font-size-11 label btn-warning mright5 border-radius-cs">' . lang('tnh_chua_qc') . '</span>';
                    } else {
                        $qc = '<span style="width: 60px;" class="font-size-11 label btn-success mright5 border-radius-cs">' . lang('tnh_da_qc') . '</span>';
                    }
                }

                //purchase products
                $divSemiProduct = '';
                $CI->db->select('
                    tbl_products.id as id,
                    tbl_products.code as code,
                    tbl_products.name as name,
                    SUM(tbl_purchase_product_items.quantity) as quantity,
                    tbl_products.images as images
                ', false);
                $CI->db->from('tbl_purchase_products');
                $CI->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                $CI->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id');
                $CI->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
                $CI->db->where('tbl_purchase_products.pois_id', $pois_id);
                $CI->db->where('tbl_purchase_products.type >=', 10);
                $CI->db->group_by('tbl_products.id');
                $purchase_products_items = $CI->db->get()->result_array();
                if (!empty($purchase_products_items)) {
                    $divSemiProduct = '';
                    $count = count($purchase_products_items);
                    foreach ($purchase_products_items as $k => $v) {
                        $tempK = 0;
                        if ($k % 2 == 0) {
                            $tempK = $k;
                            if (empty($divSemiProduct)) {
                                $divSemiProduct .= '<div class="mtop10">';
                            } else {
                                $divSemiProduct .= '<div class="mtop20">';
                            }
                        }
                        $images =  base_url('assets/images/tnh/no_image.png');
                        if ($v['images']) {
                            $images = base_url('uploads/products/' . $v['images']);
                            $purchase_products_items[$k]['images'] = $images;
                        }

                        $divSemiProduct .= '<a style="margin-top: 3px; margin-right: 3px;" href="javascript:void(0)">
                            <span class="font-size-11 mright5 border-radius-cs tag-semi-product"><img style="width: 18px;" src="' . $images . '"> ' . $v['name'] . ' - SL: ' . formatNumber($v['quantity']) . '</span>
                        </a>';

                        if (($tempK + 1) == $k || ($count - 1) == $k) {
                            $divSemiProduct .= '</div>';
                        }
                    }
                    $divSemiProduct = '<div style="margin-top: 17px;">' . $divSemiProduct . '</div>';
                }

                $rs = '(' . lang('sản xuất lại') . ' - SL: ' . formatNumber($vQC['quantity_recycling']) . ')';
                $vQcReturn .= '<div class="mtop5" style="position: relative;">
                    <a style="margin-top: 3px; margin-right: 3px;" href="' . base_url('admin/quality_control/viewQualityControl/' . $vQC['id']) . '" class="tnh-modal" data-tnh="modal" data-target="#myModal" data-toggle="modal">
                        <span class="font-size-11 label btn-danger mright5 border-radius-cs bg-red">' . $vQC['reference_no'] . ' - ' . $rs . '</span>
                    </a>
                    ' . $tagRemake . '
                    ' . $agreeProcessRemake . '
                    <div class="mtop5">' . $imgStaff . '' . $staff_name . '</div>
                    <div class="time">' . $dateActive . '</div>
                    <div class="mtop10" style="display: grid;">' . $qc . '</div>
                    ' . $divSemiProduct . '
                </div>';
            }

            $check_quality_items_returns[$key]['qcRemake'] = $qcRemake;
            $check_quality_items_returns[$key]['purchase_products_items'] = $purchase_products_items;
            $qcReturn = $check_quality_items_returns;
        }
        $data['vQcReturn'] = $vQcReturn;
        $data['isStatusRemake'] = $isStatusRemake;
        $data['qcReturn'] = $qcReturn;
        return $data;
    }
}

if (!function_exists('showProcessDetailProductions')) {
    function showProcessDetailProductions($productions_orders_items_id, $object_type = null, $production_plan_item_id = 0, $isData = false)
    {
        $CI = &get_instance();
        if ($object_type == "orders") {
        } else if ($object_type == "business_plan") {
        }

        $isFinished = [];
        $dataProcess = [];
        // tbl_stages.type as type,
        $CI->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.name as stage_name, 
            tbl_productions_orders_items_stages.active as active, 
            tbl_productions_orders_items_stages.date_active as date_active,
            tbl_productions_orders_items_stages.staff_active as staff_active,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
            tblstaff.profile_image as profile_image,
            tbl_stages.status_qc as status_qc,
            tbl_stages.id as stage_id,
            tbl_productions_orders_items_stages.final_stage as final_stage,
            tbl_productions_orders_items_stages.type as type,
            tbl_productions_orders_items_stages.begin_productions as begin_productions,
            tbl_productions_orders_items_stages.date_productions as date_productions,
            tbl_productions_orders_items_stages.staff_productions as staff_productions,
            CONCAT(staff1.firstname, " ", staff1.lastname) as staff_name_productions,
            staff1.profile_image as profile_image_productions,
        ', false);
        $CI->db->from('tbl_productions_orders_items_stages');
        $CI->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
        $CI->db->join('tblstaff staff1', 'staff1.staffid = tbl_productions_orders_items_stages.staff_productions', 'left');
        $CI->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
        $CI->db->order_by('tbl_productions_orders_items_stages.number ASC');
        $productions_orders_items_stages = $CI->db->get()->result_array();
        $data = [];
        $process = '';
        if (!empty($productions_orders_items_stages)) {
            $liProcess = '';
            $paddlingLeft = '';
            $dataProcess = $productions_orders_items_stages;
            $type_group = 0;
            $arr_id_not_qc = [];
            foreach ($productions_orders_items_stages as $key => $value) {
                $pois_id = $value['id'];
                $type = $value['type'];
                $staff_productions = $value['staff_productions'];
                $begin_productions = $value['begin_productions'];
                $dateActive = !empty($value['date_active']) ? _d($value['date_active']) : '';
                $active = $value['active'] ? "active" : '';
                $staff_name = $value['staff_name'];
                $staff_image = base_url('assets/images/user-placeholder.jpg');

                $dataProcess[$key]['staff_image'] = '';
                if (!empty($value['profile_image'])) {
                    $staff_image = base_url('uploads/staff_profile_images/' . $value['staff_active'] . '/small_' . $value['profile_image']);
                    $dataProcess[$key]['staff_image'] = $staff_image;
                }

                $active_warning = '';
                $dataProcess[$key]['staff_image_productions'] = '';
                if ($begin_productions && !$active) {
                    $staff_name = $value['staff_name_productions'];
                    $active_warning = "active-warning";
                    $dateActive = !empty($value['date_productions']) ? _d($value['date_productions']) : '';
                    if (!empty($value['profile_image_productions'])) {
                        $staff_image = base_url('uploads/staff_profile_images/' . $value['staff_productions'] . '/small_' . $value['profile_image_productions']);
                    }
                    $dataProcess[$key]['staff_image_productions'] = $staff_image;
                }

                $imgStaff = '';
                if (!empty($staff_name)) {
                    $imgStaff = '<img src="' . $staff_image . '" data-toggle="tooltip" data-title="' . $staff_name . '" class="staff-profile-image-small mright5 staff-image-cs" alt="' . $staff_name . '" data-original-title="" title="' . $staff_name . '">';
                }

                $Check = '';
                $tagBeginProductions = '';

                $key_pre = $key;
                if ($key != 0) {
                    $key_pre = $key - 1;
                }
                $type_pre = $productions_orders_items_stages[$key_pre]['type'];
                // if ($type_pre == 2 && $type == 0) {
                if ($type_pre == 2) {
                    $type = 3;
                    $type_group = 3;
                }

                if ($type_group == 3) {
                    $type = 3;
                }

                // if (($type == 3) && empty($active)) {
                //     $Check = '
                //         <span onclick="agreeProcess(this, \''.$pois_id.'\', 1, \''.$type.'\')" data-toggle="tooltip" title="'.lang('finished').'" class="fa system-checkmark-checkbox pointer"></span>
                //     ';
                // } else 

                if ($value['type'] == 6) {
                    $Check = '';
                } else if ($begin_productions == 1 && empty($active)) {
                    $Check = '
                        <span onclick="agreeProcess(this, \'' . $pois_id . '\', 1, \'' . $type . '\', \'' . ($type == 1 ? 'enter_semi_products' : 'products') . '\')" class="btn-primary-cs btn-sm-custom pull-right "><span class="fa fa-plus"></span> ' . ($type == 1 ? lang('tnh_enter_semi_products') : lang('tnh_enter_products')) . '</span>
                    ';
                    $tagBeginProductions = '<div class="c-box c-box--arrow-right">
                        ' . lang('tnh_producing') . '
                    </div>';
                    $paddlingLeft = 'padding-left: 100px;';
                } else if (($type == 1 || $type == 2 || $type == 3) && empty($active)) {
                    $Check = '
                        <span onclick="agreeProcess(this, \'' . $pois_id . '\', 1, \'' . 'begins_production' . '\')" data-toggle="tooltip" title="' . lang('tnh_begin_productions') . '" class="fa system-checkmark-checkbox-step pointer"></span>
                        <span onclick="agreeProcess(this, \'' . $pois_id . '\', 1, \'' . $type . '\')" data-toggle="tooltip" title="' . lang('finished') . '" class="fa system-checkmark-checkbox pointer"></span>
                    ';
                    // } else if ($key != 0 && empty($active)) {
                } else if (empty($active)) {
                    $Check = '<span onclick="agreeProcess(this, \'' . $pois_id . '\', 1, \'' . $type . '\')" data-toggle="tooltip" title="' . lang('finished') . '" class="fa system-checkmark-checkbox pointer"></span>';
                    // } else if ($key != 0 && !empty($active)){
                } else if (!empty($active)) {
                    $Check = '<span onclick="agreeProcess(this, \'' . $pois_id . '\', 0)" data-toggle="tooltip" title="' . lang('task_unmark_as_complete') . '" class="fa system-checkmark-delete pointer"></span>';
                }

                $pod = get_table_where('tbl_productions_orders_details', ['productions_orders_item_id' => $productions_orders_items_id], '', 'row_array', '', 'tbl_productions_orders_details.id');
                $pod_id = $pod['id'];
                $stage_id = $value['stage_id'];
                $qc = '';
                $status_qc = $value['status_qc'];
                $txtOutsource = '';
                $isQC = 0;
                if (!empty($active)) {
                    if ($status_qc == 1) {
                        $queryQC = "(
                            SELECT
                                tbl_check_quality.id as id,
                                tbl_check_quality.date as date,
                                tbl_check_quality.reference_no as reference_no,
                                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                                tbl_check_quality_items.id as cqi_id,
                                tbl_check_quality_items.quantity_qc as quantity_qc,
                                tbl_check_quality_items.quantity_recycling as quantity_recycling,
                                tbl_check_quality_items.result as result
                            FROM tbl_check_quality_items
                            INNER JOIN tbl_check_quality ON tbl_check_quality.id = tbl_check_quality_items.check_quality_id
                            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_check_quality.created_by
                            WHERE tbl_check_quality_items.pod_id = '$pod_id' AND tbl_check_quality_items.id_stage = '$stage_id'
                        )";
                        $dbQC = $CI->db->query($queryQC)->result_array();
                        if ($dbQC) {
                            $isQC = true;
                            foreach ($dbQC as $kQC => $vQC) {
                                $dateQC = _d($vQC['date']);

                                $rs = $vQC['result'] == 1 ? '(' . lang('tnh_achieved') . ')' : ($vQC['result'] == 2 ? '(' . lang('tnh_not_achieved') . ')' : '');

                                $bg_qc = $vQC['result'] == 1 ? 'bg-green' : ($vQC['result'] == 2 ? 'bg-warning' : '');

                                $staff_created_name = $vQC['staff_created_name'];
                                $qc .= '<a style="margin-top: 3px; margin-right: 3px;" href="' . base_url('admin/quality_control/viewQualityControl/' . $vQC['id']) . '" class="tnh-modal" data-tnh="modal" data-target="#myModal" data-toggle="modal"><span class="font-size-11 label btn-success mright5 border-radius-cs ' . $bg_qc . '">' . lang('tnh_da_qc') . ': ' . $dateQC . '(' . $staff_created_name . ')(' . $vQC['reference_no'] . ') - SL: ' . formatNumber($vQC['quantity_qc']) . ' <span class="">' . $rs . '</span></span></a>';

                                // $qcReturn = handlingQC($stage_id, $pod_id, [$vQC['cqi_id']], $arr_id_not_qc, $type, $pois_id);
                                // $qc.= $qcReturn['vQcReturn'];
                                // if ($qcReturn['vQcReturn'] && !$qcReturn['isStatusRemake']) {
                                // if ($qcReturn['vQcReturn']) {
                                //     $paddlingLeft = 'padding-left: 100px;';
                                // }
                                // $dbQC[$kQC]['qc_return'] = $qcReturn['qcReturn'];
                                // $arr_id_not_qc[] = $vQC['cqi_id'];
                            }
                        } else {
                            $qc = '<span style="width: 60px;" class="font-size-11 label btn-warning mright5 border-radius-cs pointer" onclick="createQcByProductionDetail(' . $pod_id . ',' . $stage_id . ');return false;">' . lang('tnh_chua_qc') . '</span>';
                        }
                        $dataProcess[$key]['qc'] = $dbQC;
                    }

                    $qcReturn = handlingQC($stage_id, $pod_id, false, $arr_id_not_qc, $type, $pois_id);
                    $qc .= $qcReturn['vQcReturn'];
                    // if ($qcReturn['vQcReturn'] && !$qcReturn['isStatusRemake']) {
                    if ($qcReturn['vQcReturn']) {
                        $paddlingLeft = 'padding-left: 100px;';
                    }
                    $dataProcess[$key]['qc_return'] = $qcReturn['qcReturn'];

                    $queryOutsource = "(
                        SELECT
                            tbl_outsource.id as id,
                            tbl_outsource.reference_no as reference_no,
                            tbl_outsource.date as date,
                            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                            tbl_outsource_items.quantity as quantity,
                            tblsuppliers.company as suppliers_company
                        FROM tbl_outsource_items
                        INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                        INNER JOIN tblsuppliers ON tbl_outsource.supplier_id = tblsuppliers.id
                        LEFT JOIN tblstaff ON tblstaff.staffid = tbl_outsource.created_by
                        WHERE tbl_outsource_items.pod_id = '$pod_id' AND tbl_outsource_items.id_stage = '$stage_id'
                    )";
                    $dbOutsource = $CI->db->query($queryOutsource)->result_array();
                    if ($dbOutsource) {
                        foreach ($dbOutsource as $kO => $vO) {
                            $dateO = _d($vO['date']);
                            $staff_created_name = $vO['staff_created_name'];
                            $txtOutsource .= '<a style="margin-top: 3px; margin-right: 3px;" href="' . base_url('admin/outsource/view_outsource/' . $vO['id']) . '" class="tnh-modal" data-tnh="modal" data-target="#myModal" data-toggle="modal"><span class="font-size-11 label btn-primary mright5 border-radius-cs">' . lang('tnh_outsource') . ': NCC: ' . $vO['suppliers_company'] . ' (' . $staff_created_name . ') - SL: ' . formatNumber($vO['quantity']) . '</span></a>';
                        }
                    }
                    $dataProcess[$key]['outsource'] = $dbOutsource;

                    //import outsourcing
                    $queryImportOutsource = "(
                        SELECT
                            tbl_import_outsource.id as id,
                            tbl_import_outsource.reference_no as reference_no,
                            tbl_import_outsource.date as date,
                            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                            tbl_import_outsource_items.quantity as quantity,
                            tblsuppliers.company as suppliers_company
                        FROM  tbl_import_outsource_items
                        INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                        INNER JOIN tblsuppliers ON tbl_import_outsource.supplier_id = tblsuppliers.id
                        LEFT JOIN tblstaff ON tblstaff.staffid = tbl_import_outsource.created_by
                        WHERE tbl_import_outsource_items.pod_id = '$pod_id' AND tbl_import_outsource_items.stage_id_default = '$stage_id'
                    )";
                    $dbImportOutsource = $CI->db->query($queryImportOutsource)->result_array();
                    if ($dbImportOutsource) {
                        foreach ($dbImportOutsource as $kO => $vO) {
                            $dateO = _d($vO['date']);
                            $staff_created_name = $vO['staff_created_name'];
                            $txtOutsource .= '<a style="margin-top: 3px; margin-right: 3px;" href="' . base_url('admin/outsource/view_import_outsource/' . $vO['id']) . '" class="tnh-modal" data-tnh="modal" data-target="#myModal" data-toggle="modal"><span class="font-size-11 label btn-primary mright5 border-radius-cs">' . lang('tnh_import_outsource') . ': NCC: ' . $vO['suppliers_company'] . ' (' . $staff_created_name . ') - SL: ' . formatNumber($vO['quantity']) . '</span></a>';
                        }
                    }
                    $dataProcess[$key]['import_outsource'] = $dbImportOutsource;
                }

                //violation records
                $CI->db->select('tblviolation_records.*');
                $CI->db->from('tblviolation_records');
                $CI->db->where('tblviolation_records.object_type', 'productions_orders_detail');
                $CI->db->where('tblviolation_records.object_id', $pod_id);
                $CI->db->where('tblviolation_records.stages', $stage_id);
                $violation_records = $CI->db->get()->result_array();
                $txtViolation = '';
                if (!empty($violation_records)) {
                    foreach ($violation_records as $kV => $vV) {
                        // $txtViolation.= '<a style="margin-top: 3px; margin-right: 3px;" href="'.base_url('admin/violation_records/view/'.$vV['id']).'" class="tnh-modal" data-tnh="modal" data-target="#myModal" data-toggle="modal"><span class="font-size-11 label btn-danger mright5 border-radius-cs">'.lang('Vị phạm').': '.$vV['code'].'('._d($vV['date']).')</span></a>';
                        $txtViolation .= '<a style="margin-top: 3px; margin-right: 3px;" href="' . base_url('admin/violation_records/print_pdf/' . $vV['id']) . '" target="_blank" class=""><span class="font-size-11 label btn-danger mright5 border-radius-cs">' . lang('Vị phạm') . ': ' . $vV['code'] . '(' . _d($vV['date']) . ')</span></a>';
                    }
                    $dataProcess[$key]['violation_records'] = $violation_records;
                }


                $CI->db->select('tblproduction_report.*');
                $CI->db->from('tblproduction_report');
                $CI->db->where('tblproduction_report.production_stage', $value['id']);
                $production_report = $CI->db->get()->result_array();

                $dataProcess[$key]['production_report'] = $production_report;
                $spanReport = '';
                foreach ($production_report as $k => $v) {
                    $spanReport .= '<div class="mtop10" style="display: grid;">
										<a style="margin-top: 3px; margin-right: 3px;" href="' . admin_url('production_report/modal/' . $v['id']) . '" target="_blank" class="c_modal">
											<span class="font-size-11 label btn-warning mright5 border-radius-cs">Phiếu báo cáo: ' . _dt($v['date']) . '</span>
										</a>
									</div>';
                }




                if ($isQC) {
                    $Check = '';
                }
                $final_stage = $value['final_stage'];
                $textFinished = '';
                $strFinishedFinalStage = '';
                if ($final_stage && $active) {
                    $strFinishedFinalStage = '<div class="mtop5 panel-finished">
                        <div class="">' . lang('tnh_finished_production') . '</div>
                        <div class="">' . $dateActive . '</div>
                    </div>';
                    $dateActive = '';
                    $isFinished = [
                        'is_finished' => true
                    ];
                } else if ($final_stage) {
                    $textFinished = 'color: #337ab7; !important';
                }

                //purchase products
                $divSemiProduct = '';
                $CI->db->select('
                    tbl_products.id as id,
                    tbl_products.code as code,
                    tbl_products.name as name,
                    SUM(IF (tbl_purchase_products.is_errors = 0, tbl_purchase_product_items.quantity, 0)) as quantity,
                    SUM(IF (tbl_purchase_products.is_errors = 1, tbl_purchase_product_items.quantity, 0)) as quantity_errors,
                    tbl_products.images as images,
                    tbl_purchase_products.sp_type as sp_type,
                    tbl_purchase_product_items.price as price
                ', false);
                $CI->db->from('tbl_purchase_products');
                $CI->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                $CI->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id');
                $CI->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
                $CI->db->where('tbl_purchase_products.pois_id', $pois_id);
                $CI->db->where('tbl_purchase_products.type <', 10);
                $CI->db->group_by('tbl_products.id');
                $purchase_products_items = $CI->db->get()->result_array();
                if (!empty($purchase_products_items)) {
                    $divSemiProduct = '';
                    $count = count($purchase_products_items);
                    foreach ($purchase_products_items as $k => $v) {
                        $tempK = 0;
                        if ($k % 2 == 0) {
                            $tempK = $k;
                            if (empty($divSemiProduct)) {
                                $divSemiProduct .= '<div class="mtop10">';
                            } else {
                                $divSemiProduct .= '<div class="mtop20">';
                            }
                        }
                        $images =  base_url('assets/images/tnh/no_image.png');
                        if ($v['images']) {
                            $images = base_url('uploads/products/' . $v['images']);
                            $purchase_products_items[$k]['images'] = $images;
                        }

                        $sp_type = $v['sp_type'];
                        $strPrice = '';
                        if ($sp_type == 1) {
                            $price = $v['price'];
                            $strPrice = ' - <span class="text-danger">Giá vốn: ' . formatMoney($price) . '</span>';
                        }

                        $strQuantityErrors = '';
                        if (!empty($v['quantity_errors'])) {
                            $strQuantityErrors = ' - <span class="text-danger">SL lỗi: ' . $v['quantity_errors'] . '</span>';
                        }

                        $divSemiProduct .= '<a style="margin-top: 3px; margin-right: 3px;" href="javascript:void(0)">
                            <span class="font-size-11 mright5 border-radius-cs tag-semi-product"><img style="width: 18px;" src="' . $images . '"> ' . $v['name'] . ' - SL: ' . formatNumber($v['quantity']) . '' . $strPrice . ' ' . $strQuantityErrors . '</span>
                        </a>';

                        if (($tempK + 1) == $k || ($count - 1) == $k) {
                            $divSemiProduct .= '</div>';
                        }
                    }
                }
                $dataProcess[$key]['purchase_products_items'] = $purchase_products_items;
                $liProcess .= '<li class="' . $active . ' ' . $active_warning . '">
                    ' . $tagBeginProductions . '
                    <p style="' . $textFinished . '">' . $value['stage_name'] . ' ' . $Check . '</p>
                    <div>' . $imgStaff . '' . $staff_name . '</div>
                    <div class="time">' . $dateActive . '</div>
                    ' . $divSemiProduct . '
                    ' . $spanReport . '
                    <div class="mtop10" style="display: grid;">' . $qc . '' . $txtOutsource . '' . $txtViolation . '</div>
                    ' . $strFinishedFinalStage . '
                </li>';
            }

            $process = '<div class="timeline-vertical">
                <div class="wrapper" style="' . $paddlingLeft . '">
                    <ul class="sessions">
                        ' . $liProcess . '
                    </ul>
                </div>
            </div>';
        }
        $data['process'] = $process;
        $data['isFinished'] = $isFinished;
        if ($isData) {
            $data['dataProcess'] = $dataProcess;
        }
        return $data;
    }
}

if (!function_exists('isQCDeleteEdit')) {
    function isQCDeleteEdit($id, $arr_id_not = [])
    {
        $CI = &get_instance();
        $CI->db->select('tbl_check_quality_items.id as cqi_id', false);
        $CI->db->from('tbl_check_quality');
        $CI->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id');
        $CI->db->where('tbl_check_quality.id', $id);
        if (!empty($arr_id_not)) {
            $CI->db->where_not_in('tbl_check_quality_items.id', $arr_id_not);
        }
        $items = $CI->db->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $cqi_id = $value['cqi_id'];
                $CI->db->from('tbl_suggest_exporting');
                $CI->db->where('tbl_suggest_exporting.cqi_id', $cqi_id);
                $q = $CI->db->count_all_results();
                if (!empty($q)) {
                    return true;
                }

                $CI->db->from('tbl_purchase_products');
                $CI->db->where('tbl_purchase_products.cqi_id', $cqi_id);
                $q = $CI->db->count_all_results();
                if (!empty($q)) {
                    return true;
                }
            }
        }
        return false;
    }
}

if (!function_exists('reSizeCS')) {
    function reSizeCS($filename, $width = 100, $height = 100)
    {
        // File type
        header('Content-Type: image/jpg');

        // // Maximum width and height
        // $width = 100;
        // $height = 100;

        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($filename);

        $ratio_orig = $width_orig / $height_orig;

        if ($width / $height > $ratio_orig) {
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }

        // Resampling the image 
        $image_p = imagecreatetruecolor($width, $height);
        $image = imagecreatefromjpeg($filename);

        imagecopyresampled(
            $image_p,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $width_orig,
            $height_orig
        );

        // Display of output image
        ob_start();
        imagejpeg($image_p, null, 100);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}

if (!function_exists('handlingCodeMaterial')) {
    function handlingCodeMaterial($category_id, $species = 0, $paper = '', $quantitative = '', $material_code_supplier = '', $suppliers = '')
    {

        $dtCategory = get_table_where('tbl_category_items', ['id' => $category_id], '', 'row_array');
        $codeCategory = '';
        if (!empty($dtCategory)) {
            $codeCategory = $dtCategory['code'];
        }

        $codeSpecies = '';
        if (!empty($species)) {
            $dtSpecies = get_table_where('tbl_species', ['id' => $species], '', 'row_array');
            $codeSpecies = $dtSpecies['code'];
        }

        if (empty($paper)) $paper = '';
        if (empty($quantitative)) $quantitative = '';

        $strPaperQuantitative = $paper . $quantitative;
        if ($paper && $quantitative) {
            $strPaperQuantitative = $paper . 'x' . $quantitative;
        }

        $strSuppliers = '';
        if (!empty($suppliers)) {
            $dtSuppliers = get_table_where('tblsuppliers', ['id' => $suppliers], '', 'row_array');
            // $strSuppliers = trim($dtSuppliers['prefix']."-".$dtSuppliers['code']);
            $strSuppliers = trim($dtSuppliers['code']);
        }

        $material_code_supplier = trim($material_code_supplier);
        $aCode =  $codeCategory . '-' . $codeSpecies . '-' . $strPaperQuantitative . '-' . $strSuppliers;
        return $aCode;
    }
}

if (!function_exists('handlingCodeProduct')) {
    function handlingCodeProduct($category_id, $species = 0, $mode_product = '',  $longs = '',  $wide = '')
    {

        $dtCategory = get_table_where('tbl_category_products', ['id' => $category_id], '', 'row_array');
        $codeCategory = '';
        if (!empty($dtCategory)) {
            $codeCategory = $dtCategory['code'];
        }

        $codeSpecies = '';
        if (!empty($species)) {
            $dtSpecies = get_table_where('tbl_species', ['id' => $species], '', 'row_array');
            $codeSpecies = $dtSpecies['code'];
        }

        if (empty($mode_product)) $mode_product = '';

        $long_wide = '';
        if ($longs != '' || $wide != '') {
            $long_wide = $longs . 'x' . $wide;
        }

        $mode_product = trim($mode_product);
        $aCode =  $codeCategory . '-' . $codeSpecies . '-' . $long_wide . '-' . $mode_product;
        return $aCode;
    }
}

if (!function_exists('plusMonth')) {
    function plusMonth($date, $plus)
    {
        return date('Y-m-d', strtotime("+" . $plus . " months", strtotime($date)));
    }
}

if (!function_exists('minusMonth')) {
    function minusMonth($date, $plus)
    {
        return date('Y-m-d', strtotime("-" . $plus . " months", strtotime($date)));
    }
}

if (!function_exists('checkOrder')) {
    function checkOrder($order_id)
    {
        return true;
    }
}

if (!function_exists('date_sort')) {
    function date_sort($a, $b)
    {
        return strtotime($a) - strtotime($b);
    }
}

if (!function_exists('calCostingFinishedProduct')) {
    function calCostingFinishedProduct($pod_id)
    {
        $CI = &get_instance();
        $CI->load->model('manufactures_model');

        $priceMaterial = "(
            SELECT
                SUM(tbl_suggest_exporting.grand_total) as total_material
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.reference_stock IS NOT NULL AND tbl_suggest_exporting.grand_total > 0 AND tbl_suggest_exporting.productions_orders_details_id = $pod_id
        )";
        $totalMaterial = $CI->db->query($priceMaterial)->row_array();
        $totalMaterial = !empty($totalMaterial) ? $totalMaterial['total_material'] : 0;

        $purchaseInternal = "(
            SELECT
                SUM(tbl_purchase_internal.grand_total) as total_internal
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.grand_total > 0 AND tbl_purchase_internal.pod_id = $pod_id
        )";
        $totalInternal = $CI->db->query($purchaseInternal)->row_array();
        $totalInternal = !empty($totalInternal) ? $totalInternal['total_internal'] : 0;

        $payslips = "(
            SELECT
                SUM(tblother_payslips.total) as total_payslips
            FROM tblother_payslips
            WHERE tblother_payslips.type_vouchers = 9 AND tblother_payslips.vouchers_id = $pod_id
        )";
        $totalPayslips = $CI->db->query($payslips)->row_array();
        $totalPayslips = !empty($totalPayslips) ? $totalPayslips['total_payslips'] : 0;

        $totalMa = $totalMaterial - $totalInternal;
        $totalCost = $totalMa + $totalPayslips;

        $price = 0;

        $CI->db->select('tbl_productions_orders_details.quantity_warehoused, tbl_productions_orders_items.items_id', false);
        $CI->db->from('tbl_productions_orders_details');
        $CI->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $CI->db->where('tbl_productions_orders_details.id =', $pod_id);
        $productions_orders_details = $CI->db->get()->row_array();
        if (!empty($productions_orders_details)) {
            $quantity_warehoused = $productions_orders_details['quantity_warehoused'];
            if ($quantity_warehoused > 0) {
                $price =  $totalCost / $quantity_warehoused;
            }

            $items_id = $productions_orders_details['items_id'];

            $CI->db->select('
                tblwarehouse_product.*
            ', false);
            $CI->db->from('tblwarehouse_product');
            $CI->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion');
            $CI->db->where('tblwarehouse_product.type_export', 18);
            $CI->db->where('tblwarehouse_product.type_items', 'product');
            $CI->db->where('tblwarehouse_product.product_id', $items_id);
            $CI->db->where('tbllocaltion_warehouses.pod_id', $pod_id);
            $CI->db->where('tbllocaltion_warehouses.stage_id', 0);
            $warehouse_product = $CI->db->get()->result_array();
            $arr_warehouse_product = [];
            if (!empty($warehouse_product)) {
                foreach ($warehouse_product as $k => $val) {
                    $arr_warehouse_product[] = [
                        'id' => $val['id'],
                        'price' => $price,
                    ];
                }
            }

            if (!empty($arr_warehouse_product)) {
                $CI->db->update_batch('tblwarehouse_product', $arr_warehouse_product, 'id');
            }

            $CI->db->where('id', $pod_id);
            $CI->db->update('tbl_productions_orders_details', ['price_costing' => $price]);

            //update purchase products
            $CI->db->select('
                tbl_purchase_product_items.*
            ', false);
            $CI->db->from('tbl_purchase_products');
            $CI->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
            $CI->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
            $CI->db->where('tbl_purchase_products.final_stage', 1);
            $purchase_product_items = $CI->db->get()->result_array();
            $arrPurchaseProductItems = [];
            $arrPurchaseProducts = [];
            if (!empty($purchase_product_items)) {
                foreach ($purchase_product_items as $k => $v) {
                    $quantity = $v['quantity'];
                    $amount = $price * $quantity;

                    $arrPurchaseProducts[] = $v['purchase_product_id'];
                    $arrPurchaseProductItems[] = [
                        'id' => $v['id'],
                        'price' => $price,
                        'amount' => $amount,
                    ];
                }

                if (!empty($arrPurchaseProductItems)) {
                    $CI->db->update_batch('tbl_purchase_product_items', $arrPurchaseProductItems, 'id');
                }

                if (!empty($arrPurchaseProducts)) {
                    $arrPurchaseP = [];
                    $arrPurchaseProducts = array_unique($arrPurchaseProducts);
                    foreach ($arrPurchaseProducts as $k => $v) {
                        $CI->db->select('SUM(tbl_purchase_product_items.amount) as amount');
                        $CI->db->from('tbl_purchase_product_items');
                        $CI->db->where('tbl_purchase_product_items.purchase_product_id', $v);
                        $dtSum = $CI->db->get()->row_array();
                        if (!empty($dtSum)) {
                            $arrPurchaseP[] = [
                                'id' => $v,
                                'grand_total' => $dtSum['amount'],
                            ];
                        }
                    }

                    if (!empty($arrPurchaseP)) {
                        $CI->db->update_batch('tbl_purchase_products', $arrPurchaseP, 'id');
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('calCostingSuggProduct')) {
    function calCostingSuggProduct($purchase_products_id)
    {
        $CI = &get_instance();
        $CI->load->model('manufactures_model');
        $CI->load->model('stock_model');

        $CI->db->select('
            tbl_purchase_product_items.*,
            tbl_purchase_products.warehouseman_id
        ', false);
        $CI->db->from('tbl_purchase_products');
        $CI->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $CI->db->where('tbl_purchase_products.id', $purchase_products_id);
        $CI->db->where('tbl_purchase_products.final_stage', 0);
        $CI->db->where('tbl_purchase_products.pois_id !=', 0);
        $CI->db->where('tbl_purchase_products.sp_type', 1);
        $purchase_product_items = $CI->db->get()->result_array();
        if (!empty($purchase_product_items)) {
            $arrPurchaseProductItems = [];
            $grand_total = 0;
            foreach ($purchase_product_items as $key => $value) {
                $purchase_product_items_id = $value['id'];

                $CI->db->select('
                    SUM(tbl_suggest_exporting_items.amount) as total_amount
                ', false);
                $CI->db->from('tbl_suggest_exporting_items');
                $CI->db->where('tbl_suggest_exporting_items.purchase_product_items_id', $purchase_product_items_id);
                $total_material = (float)$CI->db->get()->row_array()['total_amount'];

                $price = 0;
                $quantity_purchase = $value['quantity'];
                if ($quantity_purchase > 0) {
                    $price = $total_material / $quantity_purchase;
                }

                if (empty($value['warehouseman_id'])) {
                    $price = 0;
                }

                $amount = $price * $quantity_purchase;

                $CI->db->where('tblwarehouse_product.type_export', 18);
                $CI->db->where('tblwarehouse_product.import_id', $purchase_products_id);
                $CI->db->where('tblwarehouse_product.type_items', 'product');
                $CI->db->where('tblwarehouse_product.product_id', $value['item_id']);
                $CI->db->update('tblwarehouse_product', ['price' => $price]);


                $arrPurchaseProductItems[] = [
                    'id' => $purchase_product_items_id,
                    'price' => $price,
                    'amount' => $amount,
                ];

                $grand_total += $amount;
            }

            $CI->db->update_batch('tbl_purchase_product_items', $arrPurchaseProductItems, 'id');
            $CI->db->where('tbl_purchase_products.id', $purchase_products_id);
            $CI->db->update('tbl_purchase_products', ['grand_total' => $grand_total]);
            return true;
        }

        return false;
    }
}

if (!function_exists('calRecipe')) {
    function calRecipe($id = 0)
    {
        $option[1] = lang('>');
        $option[2] = lang('<');
        $option[3] = lang('=');
        $option[4] = lang('tnh_between');
        if ($id > 0) {
            return $option[$id];
        }
        return $option;
    }
}

if (!function_exists('calResult')) {
    function calResult($id = 0)
    {
        $option[1] = lang('tnh_not_reached');
        $option[2] = lang('tnh_need_keep_trying');
        $option[3] = lang('tnh_obtain');
        $option[4] = lang('tnh_pass');
        if ($id > 0) {
            return $option[$id];
        }
        return $option;
    }
}

if (!function_exists('roundNumberFormat')) {
    function roundNumberFormat($number, $decimals = null)
    {
        if ($decimals === NULL) {
            $decimals = get_option('decimals_number');
        }

        if (!is_decimal($number)) {
            $decimals = 0;
        }

        return round($number, $decimals);
    }
}

if (!function_exists('handlingCommuneStages')) {
    function handlingCommuneStages($manufactures_id, $status)
    {
        $CI = &get_instance();
        $data = [];

        $CI->db->select('
            tbl_manufactures.id,
            tbl_productions_orders_details.productions_orders_item_id as poi_id,
            tbl_productions_orders_details.id as pod_id,
            tbl_manufactures.warehouseman_id as warehouseman_id,
            tbl_manufactures.warehouseman_date as warehouseman_date,
            tbl_manufactures.status_manufactures as status_manufactures,
        ', false);
        $CI->db->from('tbl_manufactures');
        $CI->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_manufactures.id_production_detail');
        $CI->db->where('tbl_manufactures.id', $manufactures_id);
        $manufactures = $CI->db->get()->row_array();
        if (!empty($manufactures)) {
            $pod_id = $manufactures['pod_id'];
            $poi_id = $manufactures['poi_id'];
            $warehouseman_id = $manufactures['warehouseman_id'];
            $status_manufactures = $manufactures['status_manufactures'];
            if ($status_manufactures == $status) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái phiếu xã khổ này đã thay đổi');
                return $data;
            }

            if (empty($warehouseman_id)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng duyệt kho phiếu xã khổ');
                return $data;
            }

            $date = date('Y-m-d H:i:s');
            $staff_id = get_staff_user_id();

            if ($status == 1) {
                $CI->db->select('
                    tbl_productions_orders_items_stages.id
                ', false);
                $CI->db->from('tbl_productions_orders_items_stages');
                $CI->db->where('tbl_productions_orders_items_stages.stage_id', STAGES_COMMUNE);
                $CI->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                $productions_orders_items_stages = $CI->db->get()->row_array();
                if (empty($productions_orders_items_stages)) {
                    $CI->db->select('tbl_productions_orders_items_stages.*');
                    $CI->db->from('tbl_productions_orders_items_stages');
                    $CI->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                    $CI->db->order_by('tbl_productions_orders_items_stages.number ASC');
                    $arr_productions_orders_items_stages = $CI->db->get()->result_array();
                    if (!empty($arr_productions_orders_items_stages)) {

                        $arrStagesCommune = $arr_productions_orders_items_stages[0];
                        $arrStagesCommune['active'] = 1;
                        $arrStagesCommune['stage_id'] = STAGES_COMMUNE;
                        $arrStagesCommune['staff_active'] = $staff_id;
                        $arrStagesCommune['date_active'] = $date;
                        $arrStagesCommune['machines_id'] = 0;
                        $arrStagesCommune['id'] = 0;
                        $arrStagesCommune['type'] = 8;

                        array_splice($arr_productions_orders_items_stages, 1, 0, [$arrStagesCommune]);
                        $number = 0;
                        foreach ($arr_productions_orders_items_stages as $key => $value) {
                            $number++;
                            $arr_productions_orders_items_stages[$key]['number'] = $number;
                        }

                        $CI->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                        $CI->db->delete('tbl_productions_orders_items_stages');
                        $CI->db->insert_batch('tbl_productions_orders_items_stages', $arr_productions_orders_items_stages);
                    }
                }

                //productions orders items sub
                $arrDelete = [];
                $arrDeleteId = [];
                $arrInsert = [];
                $CI->db->select('tbl_manufactures_items.*, tbl_materials.unit_id, tbl_materials.code as item_code, tbl_materials.name as item_name');
                $CI->db->from('tbl_manufactures_items');
                $CI->db->join('tbl_materials', 'tbl_materials.id = tbl_manufactures_items.item_id');
                $CI->db->where('tbl_manufactures_items.manufactures_id', $manufactures_id);
                $CI->db->where('tbl_manufactures_items.type_items', 'materials');
                $manufactures_items = $CI->db->get()->result_array();
                if (!empty($manufactures_items)) {
                    foreach ($manufactures_items as $k => $v) {
                        $manufactures_items_id = $v['id'];

                        $parent_id = 0;
                        $productions_orders_id = 0;
                        $productions_orders_items_id = 0;
                        $quantity_element = 0;
                        $stage_item_id = 0;
                        $quantity_order = 0;
                        $type_element_item = 0;

                        $CI->db->select('tbl_manufactures_items_bom.*');
                        $CI->db->from('tbl_manufactures_items_bom');
                        $CI->db->where('tbl_manufactures_items_bom.manufactures_items_id', $manufactures_items_id);
                        $manufactures_items_bom = $CI->db->get()->result_array();
                        if (!empty($manufactures_items_bom)) {
                            if (!empty($manufactures_items_bom)) {
                                foreach ($manufactures_items_bom as $kB => $vB) {
                                    $type_items = $vB['type_items'];
                                    $item_id = $vB['item_id'];

                                    $CI->db->select('
                                        tbl_productions_orders_items_sub.*
                                    ', false);
                                    $CI->db->from('tbl_productions_orders_items_sub');
                                    $CI->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $poi_id);
                                    $CI->db->where('tbl_productions_orders_items_sub.type', $type_items);
                                    $CI->db->where('tbl_productions_orders_items_sub.item_id', $item_id);
                                    $productions_orders_items_sub = $CI->db->get()->result_array();

                                    if (!empty($productions_orders_items_sub)) {
                                        foreach ($productions_orders_items_sub as $kPOIS => $vPOIS) {
                                            $arrDelete[$kPOIS] = $vPOIS;
                                            $arrDelete[$kPOIS]['manufactures_id'] = $manufactures_id;
                                            $arrDelete[$kPOIS]['manufactures_item_id'] = $manufactures_items_id;

                                            $arrDeleteId[] = $vPOIS['id'];

                                            if ($kPOIS == 0) {
                                                $parent_id = $vPOIS['parent_id'];
                                                $productions_orders_id = $vPOIS['productions_orders_id'];
                                                $productions_orders_items_id = $vPOIS['productions_orders_items_id'];
                                                $quantity_element = $vPOIS['quantity_element'];
                                                $stage_item_id = $vPOIS['stage_item_id'];
                                                $quantity_order = $vPOIS['quantity_order'];
                                                $type_element_item = $vPOIS['type_element_item'];
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $arrInsert[] = [
                            'id' => 0,
                            'parent_id' => $parent_id,
                            'productions_orders_id' => $productions_orders_id,
                            'productions_orders_items_id' => $productions_orders_items_id,
                            'type' => 'materials',
                            'item_id' => $v['item_id'],
                            'item_code' => $v['item_code'],
                            'item_name' => $v['item_name'],
                            'unit_id' => $v['unit_id'],
                            'quantity_single' => $v['quantity_unit'] / $quantity_order,
                            'quantity' => $v['quantity_unit'],
                            'unit_parent_id' => $v['unit_id'],
                            'quantity_exchange' => 1,
                            'quantity_primary' => $v['quantity_unit'],
                            'leadtime' => 0,
                            'stage_item_id' => $stage_item_id,
                            'quantity_order' => $quantity_order,
                            'type_element_item' => $type_element_item,
                            'quantity_element' => $quantity_element,
                            'manufactures_id' => $manufactures_id,
                            'manufactures_item_id' => $manufactures_items_id,
                            'quantity_cs' => $v['quantity_unit'] / $quantity_order
                        ];
                    }
                }

                if (!empty($arrInsert)) {
                    $CI->db->insert_batch('tbl_productions_orders_items_sub', $arrInsert);
                }

                if (!empty($arrDelete)) {
                    $CI->db->insert_batch('tbl_productions_orders_items_sub_temp', $arrDelete);
                }

                if (!empty($arrDeleteId)) {
                    $CI->db->where_in('tbl_productions_orders_items_sub.id', $arrDeleteId);
                    $CI->db->delete('tbl_productions_orders_items_sub');
                }
            } else if ($status == 0) {

                $CI->db->from('tbl_manufactures_items');
                $CI->db->join('tbl_manufactures', 'tbl_manufactures.id = tbl_manufactures_items.manufactures_id');
                $CI->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.item_id = tbl_manufactures_items.item_id');
                $CI->db->join('tbl_suggest_exporting', 'tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id');
                $CI->db->where('tbl_manufactures.id', $manufactures_id);
                $CI->db->where('tbl_suggest_exporting_items.type_item', 'materials');
                $CI->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
                $is_manufactures_items = $CI->db->count_all_results();
                if (!empty($is_manufactures_items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Nguyên phụ liệu tăng đã có xuất kho sản xuất không thể bỏ duyệt');
                    return $data;
                }

                $CI->db->select('
                    tbl_productions_orders_items_stages.id
                ', false);
                $CI->db->from('tbl_productions_orders_items_stages');
                $CI->db->where('tbl_productions_orders_items_stages.stage_id', STAGES_COMMUNE);
                $CI->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $poi_id);
                $productions_orders_items_stages = $CI->db->get()->row_array();
                if (!empty($productions_orders_items_stages)) {
                    $pois_id = $productions_orders_items_stages['id'];
                    $CI->db->from('tbl_suggest_exporting');
                    $CI->db->where('tbl_suggest_exporting.pois_id', $pois_id);
                    $is_suggest_exporting = $CI->db->count_all_results();
                    if ($is_suggest_exporting) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đã có phiếu xuất sản xuất giai đoạn này không thể xóa');
                        return $data;
                    }

                    //
                    $CI->db->from('tbl_purchase_products');
                    $CI->db->where('tbl_purchase_products.pois_id', $pois_id);
                    $is_purchase_products = $CI->db->count_all_results();
                    if ($is_purchase_products) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đã có nhập kho giai đoạn này không thể xóa');
                        return $data;
                    }

                    $CI->db->where('tbl_productions_orders_items_stages.id', $pois_id);
                    $CI->db->delete('tbl_productions_orders_items_stages');
                }

                //
                $CI->db->select('tbl_productions_orders_items_sub_temp.*');
                $CI->db->from('tbl_productions_orders_items_sub_temp');
                $CI->db->where('tbl_productions_orders_items_sub_temp.manufactures_id', $manufactures_id);
                $productions_orders_items_sub_temp = $CI->db->get()->result_array();
                $arrInsert = [];
                foreach ($productions_orders_items_sub_temp as $k => $val) {
                    $arrInsert[$k] = $val;
                    unset($arrInsert[$k]['manufactures_id']);
                    unset($arrInsert[$k]['manufactures_item_id']);
                }

                $CI->db->where('tbl_productions_orders_items_sub_temp.manufactures_id', $manufactures_id);
                $CI->db->delete('tbl_productions_orders_items_sub_temp');

                $CI->db->where('tbl_productions_orders_items_sub.manufactures_id', $manufactures_id);
                $CI->db->delete('tbl_productions_orders_items_sub');
                if (!empty($arrInsert)) {
                    $CI->db->insert_batch('tbl_productions_orders_items_sub', $arrInsert);
                }
            }

            $CI->db->where('id', $manufactures_id);
            $CI->db->update('tbl_manufactures', [
                'status_manufactures' => $status,
                'date_manufactures' => $date,
                'user_manufactures' => $staff_id,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy dữ liệu');
        }

        return $data;
    }
}

if (!function_exists('searchDuplicate')) {
    function searchDuplicate($arr, $obj)
    {
        foreach ($arr as $key => $value) {
            // if ($value['type_price'] == $obj['type_price'] && $value['item_id_price'] == $obj['item_id_price']) {
            //     return true; //duplicate
            // }

            if ($value['type_price'] == $obj['type_price'] && $value['item_id_price'] == $obj['item_id_price'] && $value['face'] == $obj['face']) {
                return [
                    'key' => $key,
                    'result' => true,
                    'face_after' => 0
                ]; //duplicate
            } else if ($value['type_price'] == $obj['type_price'] && $value['item_id_price'] == $obj['item_id_price'] && $value['face'] != $obj['face']) {
                return [
                    'key' => $key,
                    'result' => true,
                    'face_after' => 2
                ]; //duplicate
            }
        }
        return false;
    }
}

if (!function_exists('tnh_vn_to_str_cs')) {
    function tnh_vn_to_str_cs($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }
}

if (!function_exists('getParentStaff')) {
    function getParentStaff($staff_id = 0)
    {
        $CI = &get_instance();
        $arrID = array();
        $staff_id = !empty($staff_id) ? $staff_id : get_staff_user_id();
        if (is_admin($staff_id)) {
            $arrID[] = $staff_id;

            return $arrID;
        } else {
            $CI->db->select('tbl_employee_manage_staff.*');
            $CI->db->from('tbl_employee_manage_staff');
            // $CI->db->where('tbl_employee_manage_staff.staff_id', $staff_id);
            $CI->db->where('tbl_employee_manage_staff.employee_id', $staff_id);
            $employee_manage_staff = $CI->db->get()->result_array();
            if (!empty($employee_manage_staff)) {
                foreach ($employee_manage_staff as $key => $value) {
                    // get_manager($value['employee_id'], $arrID);
                    // array_push($arrID, $value['employee_id']);
                    get_manager($value['staff_id'], $arrID);
                    array_push($arrID, $value['staff_id']);
                }

                if ($arrID) {
                    $arrID = array_unique($arrID);
                }
            }
        }

        return $arrID;
    }
}

if (!function_exists('get_manager')) {
    function get_manager($parent_id = '', &$result = array(), $key = 0)
    {
        if ($key == 50) {
            return true;
        }
        $CI = &get_instance();
        // $CI->db->where('staff_id', $parent_id);
        $CI->db->where('employee_id', $parent_id);
        $items = $CI->db->get('tbl_employee_manage_staff')->result();

        foreach ($items as $value) {
            // array_push($result, $value->employee_id);
            // get_manager($value->employee_id, $result, ++$key);

            array_push($result, $value->staff_id);
            get_manager($value->staff_id, $result, ++$key);
        }
    }
}

if (!function_exists('handlingPricesCustomerGroupOld')) {
    function handlingPricesCustomerGroupOld($quote_id)
    {
        $CI = &get_instance();
        $CI->db->select('
            tbl_quotes.id as id,
            tbl_quotes.status as status,
            tbl_quotes.customer_id as customer_id,
            tbl_quotes.date as date,
            tbl_quotes.reference_no as reference_no
        ', false);
        $CI->db->from('tbl_quotes');
        $CI->db->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id');
        $CI->db->where('tbl_quotes.id', $quote_id);
        $quote = $CI->db->get()->row_array();
        if (!empty($quote)) {
            if ($quote['status'] == 'approved') {
                $year = date_format(date_create($quote['date']), 'Y');

                $customer_id = $quote['customer_id'];
                $CI->db->select('tblcustomer_groups.groupid as groupid');
                $CI->db->from('tblcustomer_groups');
                $CI->db->where('tblcustomer_groups.customer_id', $customer_id);
                $customer_groups = $CI->db->get()->result_array();

                if (!empty($customer_groups)) {
                    $CI->db->select('
                        tbl_quote_items.type_item as type_item,
                        tbl_quote_items.item_id as item_id,
                        tbl_quote_items.moq as moq,
                        tbl_quote_items.moq_to as moq_to,
                        tbl_quote_items.unit_price as unit_price,
                    ', false);
                    $CI->db->from('tbl_quote_items');
                    $CI->db->where('tbl_quote_items.quote_id', $quote_id);
                    $quote_items = $CI->db->get()->result_array();

                    $arrPrice = [];
                    if (!empty($customer_groups) && !empty($quote_items)) {
                        foreach ($customer_groups as $k => $val) {
                            $arrPrice[$k]['group_id'] = $val['groupid'];
                            foreach ($quote_items as $kQ => $vQ) {
                                $arrPrice[$k]['items'][] = [
                                    'price' => $vQ['unit_price'],
                                    'product_id' => $vQ['item_id'],
                                    'product_type' => 'product',
                                    'group_price_id' => 0,
                                    'money_start' => $vQ['moq'],
                                    'money_end' => $vQ['moq_to'],
                                    'quotes_id' => $quote_id,
                                ];
                            }
                        }
                    }

                    if (!empty($arrPrice)) {
                        foreach ($arrPrice as $key => $value) {
                            $group_id = $value['group_id'];
                            $items = $value['items'];

                            $CI->db->select('
                                tblgroup_price.id as id
                            ', false);
                            $CI->db->from('tblgroup_price');
                            $CI->db->where('tblgroup_price.group_id', $group_id);
                            $CI->db->where('tblgroup_price.year', $year);
                            $group_price = $CI->db->get()->row_array();
                            if (!empty($group_price)) {
                                $group_price_id = $group_price['id'];
                                $itemsPriceDetail = [];
                                if ($items) {
                                    foreach ($items as $k => $val) {
                                        $itemsPriceDetail[$k] = $val;
                                        $itemsPriceDetail[$k]['group_price_id'] = $group_price_id;
                                    }
                                }

                                $CI->db->insert_batch('tblgroup_price_detail', $itemsPriceDetail);
                            } else {
                                $arrGroupPrice = [
                                    'date_start' => '0000-00-00',
                                    'date_end' => '0000-00-00',
                                    'name_price' => 'Bảng giá: ' . $quote['reference_no'],
                                    'group_id' => $group_id,
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'staff_create' => get_staff_user_id(),
                                    'year' => $year,
                                ];
                                $CI->db->insert('tblgroup_price', $arrGroupPrice);
                                $group_price_id = $CI->db->insert_id();
                                if (!empty($group_price_id)) {
                                    $itemsPriceDetail = [];
                                    if ($items) {
                                        foreach ($items as $k => $val) {
                                            $itemsPriceDetail[$k] = $val;
                                            $itemsPriceDetail[$k]['group_price_id'] = $group_price_id;
                                        }
                                    }
                                    $CI->db->insert_batch('tblgroup_price_detail', $itemsPriceDetail);
                                }
                            }
                        }

                        return true;
                    }
                }
            } else if ($quote['status'] == 'un_approved') {
                $CI->db->where('tblgroup_price_detail.quotes_id', $quote_id);
                $CI->db->delete('tblgroup_price_detail');
                return true;
            }
        } else {
            return false;
        }

        return false;
    }
}

if (!function_exists('handlingPricesCustomerGroup')) {
    function handlingPricesCustomerGroup($quote_id)
    {
        $CI = &get_instance();
        $CI->db->select('
            tbl_quotes.id as id,
            tbl_quotes.status as status,
            tbl_quotes.customer_id as customer_id,
            tbl_quotes.date as date,
            tbl_quotes.reference_no as reference_no
        ', false);
        $CI->db->from('tbl_quotes');
        $CI->db->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id');
        $CI->db->where('tbl_quotes.id', $quote_id);
        $quote = $CI->db->get()->row_array();
        if (!empty($quote)) {
            if ($quote['status'] == 'approved') {
                $year = date_format(date_create($quote['date']), 'Y');

                $customer_id = $quote['customer_id'];
                $CI->db->select('tblcustomer_groups.groupid as groupid');
                $CI->db->from('tblcustomer_groups');
                $CI->db->where('tblcustomer_groups.customer_id', $customer_id);
                $customer_groups = $CI->db->get()->result_array();

                if (!empty($customer_groups)) {
                    $CI->db->select('
                        tbl_quote_items.type_item as type_item,
                        tbl_quote_items.item_id as item_id,
                        tbl_quote_items.moq as moq,
                        tbl_quote_items.moq_to as moq_to,
                        tbl_quote_items.unit_price as unit_price,
                    ', false);
                    $CI->db->from('tbl_quote_items');
                    $CI->db->where('tbl_quote_items.quote_id', $quote_id);
                    $quote_items = $CI->db->get()->result_array();

                    $arrPrice = [];
                    if (!empty($customer_groups) && !empty($quote_items)) {
                        foreach ($customer_groups as $k => $val) {
                            $arrPrice[$k]['group_id'] = $val['groupid'];
                            foreach ($quote_items as $kQ => $vQ) {
                                $arrPrice[$k]['items'][] = [
                                    'price' => $vQ['unit_price'],
                                    'product_id' => $vQ['item_id'],
                                    'product_type' => 'product',
                                    'group_price_id' => 0,
                                    'money_start' => $vQ['moq'],
                                    'money_end' => $vQ['moq_to'],
                                    'quotes_id' => $quote_id,
                                ];
                            }
                        }
                    }

                    if (!empty($arrPrice)) {
                        foreach ($arrPrice as $key => $value) {
                            $group_id = $value['group_id'];
                            $items = $value['items'];

                            $CI->db->select('
                                tblgroup_price.id as id
                            ', false);
                            $CI->db->from('tblgroup_price');
                            // $CI->db->where('tblgroup_price.group_id', $group_id);
                            $CI->db->where('tblgroup_price.client', $customer_id);
                            // $CI->db->where('tblgroup_price.year', $year);
                            $group_price = $CI->db->get()->row_array();
                            if (!empty($group_price)) {
                                $group_price_id = $group_price['id'];
                                $itemsPriceDetail = [];
                                if ($items) {
                                    foreach ($items as $k => $val) {
                                        $itemsPriceDetail[$k] = $val;
                                        $itemsPriceDetail[$k]['group_price_id'] = $group_price_id;
                                    }
                                }

                                $CI->db->insert_batch('tblgroup_price_detail', $itemsPriceDetail);
                            } else {
                                $arrGroupPrice = [
                                    'date_start' => '0000-00-00',
                                    'date_end' => '0000-00-00',
                                    'name_price' => 'Bảng giá: ' . $quote['reference_no'],
                                    'group_id' => 0,
                                    'client' => $customer_id,
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'staff_create' => get_staff_user_id(),
                                    'year' => $year,
                                ];
                                $CI->db->insert('tblgroup_price', $arrGroupPrice);
                                $group_price_id = $CI->db->insert_id();
                                if (!empty($group_price_id)) {
                                    $itemsPriceDetail = [];
                                    if ($items) {
                                        foreach ($items as $k => $val) {
                                            $itemsPriceDetail[$k] = $val;
                                            $itemsPriceDetail[$k]['group_price_id'] = $group_price_id;
                                        }
                                    }
                                    $CI->db->insert_batch('tblgroup_price_detail', $itemsPriceDetail);
                                }
                            }
                        }

                        return true;
                    }
                }
            } else if ($quote['status'] == 'un_approved') {
                $CI->db->where('tblgroup_price_detail.quotes_id', $quote_id);
                $CI->db->delete('tblgroup_price_detail');
                return true;
            }
        } else {
            return false;
        }

        return false;
    }
}

if (!function_exists('status_machine_new')) {
    function status_machine_new()
    {
        //		$option['not_produced'] = lang('tnh_not_produced');
        $option['producing'] = lang('Đang Sử Dụng');
        $option['maintenance'] = lang('Ngừng Sửa Chữa');
        $option['damaged'] = lang('Tạm Ngừng Sửa Chữa');
        return $option;
    }
}

if (!function_exists('ceildNumberFormat')) {
    function ceildNumberFormat($number, $decimals = null)
    {
        // if ($decimals === NULL) {
        //     $decimals = get_option('decimals_number');
        // }

        // if (!is_decimal($number)) {
        $decimals = 0;
        // }

        return round($number, $decimals);
    }
}

function numInWords($num)
{
    $nwords = array(
        0                   => 'không',
        1                   => 'một',
        2                   => 'hai',
        3                   => 'ba',
        4                   => 'bốn',
        5                   => 'năm',
        6                   => 'sáu',
        7                   => 'bảy',
        8                   => 'tám',
        9                   => 'chín',
        10                  => 'mười',
        11                  => 'mười một',
        12                  => 'mười hai',
        13                  => 'mười ba',
        14                  => 'mười bốn',
        15                  => 'mười lăm',
        16                  => 'mười sáu',
        17                  => 'mười bảy',
        18                  => 'mười tám',
        19                  => 'mười chín',
        20                  => 'hai mươi',
        30                  => 'ba mươi',
        40                  => 'bốn mươi',
        50                  => 'năm mươi',
        60                  => 'sáu mươi',
        70                  => 'bảy mươi',
        80                  => 'tám mươi',
        90                  => 'chín mươi',
        100                 => 'trăm',
        1000                => 'nghìn',
        1000000             => 'triệu',
        1000000000          => 'tỷ',
        1000000000000       => 'nghìn tỷ',
        1000000000000000    => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ',
    );
    $separate = ' ';
    $negative = ' âm ';
    $rltTen   = ' lẻ ';
    $decimal  = ' phẩy ';
    if (!is_numeric($num)) {
        $w = '#';
    } else if ($num < 0) {
        $w = $negative . numInWords(abs($num));
    } else {
        if (fmod($num, 1) != 0) {
            $numInstr    = strval($num);
            $numInstrArr = explode(".", $numInstr);
            $w           = numInWords(intval($numInstrArr[0])) . $decimal . numInWords(intval($numInstrArr[1]));
        } else {
            $w = '';
            if ($num < 21) // 0 to 20
            {
                $w .= $nwords[$num];
            } else if ($num < 100) {
                // 21 to 99
                $w .= $nwords[10 * floor($num / 10)];
                $r = fmod($num, 10);
                if ($r > 0) {
                    $w .= $separate . $nwords[$r];
                }
            } else if ($num < 1000) {
                // 100 to 999
                $w .= $nwords[floor($num / 100)] . $separate . $nwords[100];
                $r = fmod($num, 100);
                if ($r > 0) {
                    if ($r < 10) {
                        $w .= $rltTen . $separate . numInWords($r);
                    } else {
                        $w .= $separate . numInWords($r);
                    }
                }
            } else {
                $baseUnit     = pow(1000, floor(log($num, 1000)));
                $numBaseUnits = (int) ($num / $baseUnit);
                $r            = fmod($num, $baseUnit);
                if ($r == 0) {
                    $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit];
                } else {
                    if ($r < 100) {
                        if ($r >= 10) {
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
                        } else {
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm lẻ ' . numInWords($r);
                        }
                    } else {
                        $baseUnitInstr      = strval($baseUnit);
                        $rInstr             = strval($r);
                        $lenOfBaseUnitInstr = strlen($baseUnitInstr);
                        $lenOfRInstr        = strlen($rInstr);
                        if (($lenOfBaseUnitInstr - 1) != $lenOfRInstr) {
                            $numberOfZero = $lenOfBaseUnitInstr - $lenOfRInstr - 1;
                            if ($numberOfZero == 2) {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm lẻ ' . numInWords($r);
                            } else if ($numberOfZero == 1) {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
                            } else {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
                            }
                        } else {
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
                        }
                    }
                }
            }
        }
    }
    return $w;
}

function numberInVietnameseWords($num)
{
    return str_replace("mươi năm", "mươi lăm", str_replace("mươi một", "mươi mốt", numInWords($num)));
}

function numberInVietnameseCurrency($num)
{
    $rs    = numberInVietnameseWords($num);
    $rs[0] = strtoupper($rs[0]);
    return $rs . ' đồng';
}

if (!function_exists('handlingColumns')) {
    function handlingColumns($aColumns)
    {
        $_aColumns = [];
        $needle   = ' as ';
        foreach ($aColumns as $column) {
            if (strpos($column, $needle) !== false) {
                $arrColumns = explode($needle, $column);
                $_column = trim($arrColumns[1]);
            } else {
                $arrColumns = explode('.', $column);
                $_column = trim($arrColumns[1]);
            }
            array_push($_aColumns, $_column);
        }

        return $_aColumns;
    }
}

if (!function_exists('getTypeCategoryTasks')) {
    function getTypeCategoryTasks($option_id = 0)
    {
        $option[1] = lang('Ngày');
        $option[2] = lang('Tháng');
        $option[3] = lang('Năm');
        if (!empty($option_id)) {
            return $option[$option_id];
        }
        return $option;
    }
}

if (!function_exists('stringWhere')) {
    function stringWhere($where = [])
    {
        $where = implode(' ', $where);
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
        }
        return $where;
    }
}

if (!function_exists('getWhereQuotes')) {
    function getWhereQuotes($where = [], $stringSql = false)
    {
        $CI = &get_instance();
        $staff_id = get_staff_user_id();
        $is_admin = is_admin();
        $perViewQuotes = has_permission('quotes', $staff_id, 'view');
        if (!$perViewQuotes) {
            array_push($where, ' AND (tbl_quotes.created_by = ' . $staff_id . ')');
        }
        if (!$is_admin) {
            $list_branch = get_list_branch_staff($staff_id);
            if (!empty($list_branch)) {
                array_push($where, ' AND tbl_quotes.id_branch IN (' . $list_branch . ')');
            } else {
                array_push($where, ' AND tbl_quotes.id_branch = 0');
            }
        }

        if ($stringSql) {
            $where = stringWhere($where);
        }

        return $where;
    }
}

if (!function_exists('getProcessWorkPlan')) {
    function getProcessWorkPlan($op = false)
    {
        $option[0] = [
            'name' => lang('Chưa bắt đầu'),
            'color' => '#ff9800'
        ];
        $option[1] = [
            'name' => lang('Đang làm dở'),
            'color' => '#b7b71d'
        ];
        $option[2] = [
            'name' => lang('Trễ tiến độ'),
            'color' => '#f44336'
        ];
        $option[3] = [
            'name' => lang('Đã hoàn thành'),
            'color' => '#4caf50'
        ];
        if (!empty($op)) {
            return $option[$op];
        }
        return $option;
    }
}

if (!function_exists('array_unique_multidimensional')) {
    function array_unique_multidimensional($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = array_unique_multidimensional($value);
            }
        }
        return $result;
    }
}

if (!function_exists('_string')) {
    function _string($data)
    {
        return trim($data);
    }
}

if (!function_exists('_strSingleQuote')) {
    function _strSingleQuote($str)
    {
        return str_replace("'", "\'", $str);
    }
}

if (!function_exists('stringWhere')) {
    function stringWhere($where = [])
    {
        $where = implode(' ', $where);
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
        }
        return $where;
    }
}

if (!function_exists('convertToRoman')) {
    function convertToRoman($number)
    {
        $map = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );

        $result = '';
        foreach ($map as $roman => $value) {
            $matches = intval($number / $value);
            $result .= str_repeat($roman, $matches);
            $number = $number % $value;
        }

        return $result;
    }
}

if (!function_exists('plusDate')) {
    function plusDate($date, $plusDate)
    {
        if (empty($date)) return null;
        $current_date = strtotime($date);
        $new_date = strtotime('+' . $plusDate . ' day', $current_date);
        return date('Y-m-d', $new_date);
    }
}

if (!function_exists('abbreviateNumber')) {
    function abbreviateNumber($number, $format = NULL)
    {
        if ($number >= 1000000000) {
            return formatNumber($number / 1000000000, $format) . 'B'; // B: tỷ
        } elseif ($number >= 1000000) {
            return formatNumber($number / 1000000, $format) . 'M'; // M: triệu
        } elseif ($number >= 1000) {
            return formatNumber($number / 1000, $format) . 'K'; // K: nghìn
        }

        return formatNumber($number);
    }
}

if (!function_exists('handlingTitleExcel')) {
    function handlingTitleExcel()
    {
        $arrTitleExcel = [];
        $args = func_get_args();
        if ($args) {
            foreach ($args as $arg) {
                if ($arg) {
                    $arrTitleExcel[] = $arg;
                }
            }
        }

        $data = [];
        $CI = &get_instance();
        $allPost = $CI->input->post();
        if ($allPost) {
            foreach ($allPost as $key => $value) {
                if (strpos($key, '_text') !== false && $value) {
                    $arrTitleExcel[] = $value;
                } else if (strpos($key, '_date') !== false && $value) {
                    $arrTitleExcel[] = $value;
                }
            }
        }

        $data['title'] = 'Giai Đoạn : ' . (implode(' - ', $arrTitleExcel));
        return $data;
    }
}

if (!function_exists('optionStatusProductionList')) {
    function optionStatusProductionList()
    {

        $option['CHT'] = [
            'id' => 'CHT',
            'name' => lang('Chưa hoàn thành'),
        ];

        $option['HT'] = [
            'id' => 'HT',
            'name' => lang('Hoàn thành'),
        ];

        if (!empty($option_id)) {
            return $option[$option_id];
        }
        return $option;
    }
}

if (!function_exists('insertCompanyInfo')) {
    function insertCompanyInfo($objPHPExcel, $merge = 'C1:T2', $cell_logo = 'A1', $type = '')
    {
        // Lấy logo và tên công ty
        $company_logo = get_option('company_logo');
        $img = 'uploads/company/' . $company_logo;
        $company_name = "CÔNG TY TRÁCH NHIỆM HỮU HẠN IN 3D THÀNH DANH\nTHANH DANH 3D PRINTING CO.,LTD";

        // Tạo đối tượng RichText
        $objRichText = new PHPExcel_RichText();

        // Thêm văn bản với định dạng
        $fi = $objRichText->createTextRun('CÔNG TY TRÁCH NHIỆM HỮU HẠN ');
        $fi->getFont()->setBold(true);
        $fi->getFont()->setSize(16);

        $boldPart = $objRichText->createTextRun('IN 3D THÀNH DANH');
        $boldPart->getFont()->setBold(true);
        $boldPart->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));
        $boldPart->getFont()->setSize(15);

        $objRichText->createText("\n");

        $coloredPart = $objRichText->createTextRun('THANH DANH 3D PRINTING CO.,LTD');
        $coloredPart->getFont()->setBold(true);
        $coloredPart->getFont()->setColor(new PHPExcel_Style_Color('FF800080')); // Màu xanh
        $coloredPart->getFont()->setSize(15);

        // Gán nội dung Rich Text vào một ô
        $sheet = $objPHPExcel->getActiveSheet();
        $cellCoordinate = 'C1'; // Chọn ô bạn muốn chèn
        $sheet->getCell($cellCoordinate)->setValue($objRichText);

        // Định dạng ô để nội dung xuống dòng tự động
        $sheet->getStyle($cellCoordinate)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->mergeCells($merge);
        $objPHPExcel->getActiveSheet()->getStyle($cellCoordinate)->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        // Tùy chỉnh chiều cao của hàng để phù hợp nội dung
        $sheet->getRowDimension('1')->setRowHeight(20);

        // Thêm logo vào sheet nếu tồn tại
        if (file_exists($img)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            // $objDrawing->setName($company_logo);
            // $objDrawing->setDescription('Image');
            // $objDrawing->setPath($img);
            // $objDrawing->setCoordinates('A1');
            // $objDrawing->setWidth(80);
            // $objDrawing->setHeight(80);
            // $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            // $objPHPExcel->getActiveSheet()->mergeCells('A1:B4');

            $objDrawing->setName($company_logo);
            $objDrawing->setDescription('Image');
            $objDrawing->setPath($img);
            list($originalWidth, $originalHeight) = getimagesize($img);
            $maxWidth = 90;  // Chiều rộng tối đa của ô
            $maxHeight = 90; // Chiều cao tối đa của ô
            $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $scaledWidth = $originalWidth * $scale;
            $scaledHeight = $originalHeight * $scale;
            // $objDrawing->setWidth($scaledWidth);
            // $objDrawing->setHeight($scaledHeight);
            $offsetX = ($maxWidth - $scaledWidth) / 2;
            $offsetY = ($maxHeight - $scaledHeight) / 2;
            // $objDrawing->setResizeProportional(false);
            // $objDrawing->setOffsetX($offsetX + 2);
            // $objDrawing->setOffsetY($offsetY + 2);

            // $objDrawing->setOffsetX(5);
            // $objDrawing->setOffsetY(5);
            // $objDrawing->setHeight(80); // Chiều cao 0.83 inch = 80 pixel
            // $objDrawing->setWidth(114); // Chiều rộng 1.19 inch = 114 pixel

            $scale = 0.5;
            $scaledWidth = $originalWidth * $scale;
            $scaledHeight = $originalHeight * $scale;
            $objDrawing->setWidth($scaledWidth);
            $objDrawing->setHeight($scaledHeight);
            // $objDrawing->setWidth(80);
            // $objDrawing->setHeight(120);
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);


            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objDrawing->setCoordinates('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            // if ($cell_logo == 'B1') {
            //     if ($type == 'orders') {
            //         // $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            //         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            //     } else {
            //         // $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            //         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            //     }
            // }
            $objPHPExcel->getActiveSheet()->mergeCells('A1:B4');
            // $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
            // $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(-1);
            // $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(-1);
            // $objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(-1);
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(20);
            // // $objPHPExcel->getActiveSheet()->getRowDimension('A1')->setRowHeight(100);
        }
    }
}

if (!function_exists('totalBusinessPlan')) {
    function totalBusinessPlan($product_id)
    {
        $CI = &get_instance();
        $CI->db->select('
            tbl_business_plan_items.items_id as items_id,
            SUM(tbl_business_plan_items.quantity) as total_quantity
        ', false);
        $CI->db->from('tbl_business_plan');
        $CI->db->join('tbl_business_plan_items', 'tbl_business_plan_items.business_plan_id = tbl_business_plan.id');
        if (!empty($product_id)) {
            $product_id = array_unique($product_id);
            $CI->db->where_in('tbl_business_plan_items.items_id', $product_id, false);
        }
        $CI->db->group_by('tbl_business_plan_items.items_id');
        $items = $CI->db->get()->result_array();

        if (!empty($product_id)) {
            $CI->db->where_in('id', $product_id);
            $CI->db->set('total_business_plan', 0);
            $CI->db->update('tbl_products');
        }

        if (!empty($items)) {
            $arrUpdate = [];
            foreach ($items as $key => $value) {
                $arrUpdate[] = [
                    'id' => $value['items_id'],
                    'total_business_plan' => $value['total_quantity'],
                ];
            }

            if (!empty($arrUpdate)) {
                $CI->db->update_batch('tbl_products', $arrUpdate, 'id');
            }
        }

        return true;
    }
}

if (!function_exists('totalTransferBusinessItem')) {
    function totalTransferBusinessItem($product_id)
    {
        $CI = &get_instance();

        $CI->db->select('
            tbl_tranfer_business_item.item_id as item_id,
            SUM(tbl_tranfer_business_item.quantity) as total_quantity
        ', false);
        $CI->db->from('tbl_tranfer_business_item');
        if (!empty($product_id)) {
            $product_id = array_unique($product_id);
            $CI->db->where_in('tbl_tranfer_business_item.item_id', $product_id, false);
        }
        $CI->db->group_by('tbl_tranfer_business_item.item_id');
        $items = $CI->db->get()->result_array();

        if (!empty($product_id)) {
            $CI->db->where_in('id', $product_id);
            $CI->db->set('total_transfer_business', 0);
            $CI->db->update('tbl_products');
        }

        if (!empty($items)) {
            $arrUpdate = [];
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $arrUpdate[] = [
                        'id' => $value['item_id'],
                        'total_transfer_business' => $value['total_quantity'],
                    ];
                }

                if (!empty($arrUpdate)) {
                    $CI->db->update_batch('tbl_products', $arrUpdate, 'id');
                }
            }
        }

        return true;
    }
}

if (!function_exists('listStockAvailable')) {
    function listStockAvailable($arrProductId)
    {
        if (empty($arrProductId)) return null;

        $CI = &get_instance();
        $arrProductId = array_unique($arrProductId);
        $queryStockAvailable = "
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_HOLD . ", " . WAREHOUSES_ERRORS . ", " . WAREHOUSES_CAPACITY . ") AND tbllocaltion_warehouses.stage_id = 0 AND EXISTS (
                SELECT 1
                FROM tbl_productions_orders_details
                WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'business_plan'
            )
            AND tblwarehouse_items.id_items IN (" . implode(',', $arrProductId) . ")
            GROUP BY tblwarehouse_items.id_items
        ";
        $listStockAvailable = $CI->db->query($queryStockAvailable)->result_array();
        if (!empty($listStockAvailable)) {
            $listStockAvailable = array_reduce($listStockAvailable, function ($carry, $item) {
                $carry[$item['id_items']] = $item;
                return $carry;
            });
        }

        return $listStockAvailable;
    }
}

if (!function_exists('listApprovedStock')) {
    function listApprovedStock($arrProductId)
    {
        if (empty($arrProductId)) return null;

        $CI = &get_instance();
        $arrProductId = array_unique($arrProductId);
        $CI->db->select('
            tbl_purchase_product_items.item_id as item_id,
            SUM(tbl_purchase_product_items.quantity) as quantity
        ', false);
        $CI->db->from('tbl_purchase_products');
        $CI->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $CI->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id');
        $CI->db->where_in('tbl_purchase_product_items.item_id', $arrProductId, false);
        $CI->db->where('tbl_purchase_products.type_business_plan', 0);
        $CI->db->group_start();
        $CI->db->where('tbl_purchase_products.final_stage', 1);
        $CI->db->or_where('tbl_purchase_products.is_errors', 0);
        $CI->db->group_end();
        $CI->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $CI->db->where('tbl_productions_orders_details.object_type', 'business_plan');
        $CI->db->group_by('tbl_purchase_product_items.item_id');
        $listApprovedStock = $CI->db->get()->result_array();
        // print_arrays($CI->db->last_query());
        if (!empty($listApprovedStock)) {
            $listApprovedStock = array_reduce($listApprovedStock, function ($carry, $item) {
                $carry[$item['item_id']] = $item;
                return $carry;
            });
        }

        return $listApprovedStock;
    }
}

if (!function_exists('toZeroIfSmall')) {
    function toZeroIfSmall($number, $epsilon = 1e-4)
    {
        return abs($number) < $epsilon ? 0 : $number;
    }
}
