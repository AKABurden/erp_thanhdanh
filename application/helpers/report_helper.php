<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Check if client have transactions recorded
 * @param  mixed $id clientid
 * @return boolean
 */
function ch_EditColumSelectInputDate_po($value = "", $id = '', $name = "", $ValShow = "", $urlGetData = '', $urlFrom = '', $indexAddfrom = '', $name_data_input = 'data_input')
{
    $html = '<div class="lableScript">' . $ValShow . ' 
                <a class="editDataTable_ch" data-type="select" data-href="' . $urlGetData . '" data-id="' . $id . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            </div>
            <div style="width:100%" class="inputScript hide">
                ' . form_open($urlFrom, $indexAddfrom) . '
                    <input style="width:100%" name="' . $name_data_input . '" data-hidden="' . $value . '" class="ChangeDataTable" value="' . $value . '"/>
                    <input style="width:100%" name="name_input"  type="hidden" value="' . $name . '"/>
                    <input style="width:100%" name="id" id="id_ch" type="hidden" value="' . $id . '"/>
                    <div style="width:100%" class="clearfix mtop10"></div>
                    <button type="submit" class="btn btn-icon"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-icon text-danger closeEditData"><i class="fa fa-times" aria-hidden="true"></i></i></button>
                ' . form_close() . '
            </div>';
    return $html;
}
function ch_EditColumSelectInput_po($value = "", $id = '', $name = "", $ValShow = "", $urlGetData = '', $urlFrom = '', $indexAddfrom = '', $name_data_input = 'data_input')
{
    $html = '<div class="lableScript">' . $ValShow . ' 
                <a class="editDataTable_ch" data-type="select" data-href="' . $urlGetData . '" data-id="' . $id . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            </div>
            <div style="width:100%" class="inputScript hide">
                ' . form_open($urlFrom, $indexAddfrom) . '
                    <input style="width:100%" onkeyup="formatNumBerKeyUp(this)" name="' . $name_data_input . '" data-hidden="' . $value . '" class="H_input align_right ChangeDataTable" value="' . $value . '"/>
                    <input style="width:100%" name="name_input"  type="hidden" value="' . $name . '"/>
                    <input style="width:100%" name="id" id="id_ch" type="hidden" value="' . $id . '"/>
                    <div style="width:100%" class="clearfix mtop10"></div>
                    <button type="submit" class="btn btn-icon"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-icon text-danger closeEditData"><i class="fa fa-times" aria-hidden="true"></i></i></button>
                ' . form_close() . '
            </div>';
    return $html;
}
function ch_EditColumSelectInput($value = "", $id = '', $name = "", $ValShow = "", $urlGetData = '', $urlFrom = '', $indexAddfrom = '', $name_data_input = 'data_input')
{
    $html = '<div class="lableScript">' . $ValShow . ' 
                    <a class="editDataTable_ch" data-type="select" data-href="' . $urlGetData . '" data-id="' . $id . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                </div>
                <div style="width:100px" class="inputScript hide">
                    ' . form_open($urlFrom, $indexAddfrom) . '
                        <input style="width:100px" onkeyup="formatNumBerKeyUp(this)" name="' . $name_data_input . '" data-hidden="' . $value . '" class="H_input align_right ChangeDataTable" value="' . $value . '"/>
                        <input style="width:100px" name="name_input"  type="hidden" value="' . $name . '"/>
                        <input style="width:100px" name="id" id="id_ch" type="hidden" value="' . $id . '"/>
                        <div style="width:100px" class="clearfix mtop10"></div>
                        <button type="submit" class="btn btn-icon"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-icon text-danger closeEditData"><i class="fa fa-times" aria-hidden="true"></i></i></button>
                    ' . form_close() . '
                </div>';
    return $html;
}
function cover_month($id = '')
{
    $month = '';
    switch ($id) {
        case '1':
            $month = 'January';
            break;
        case '2':
            $month = 'February';
            break;
        case '3':
            $month = 'March';
            break;
        case '4':
            $month = 'April';
            break;
        case '5':
            $month = 'May';
            break;
        case '6':
            $month = 'June';
            break;
        case '7':
            $month = 'July';
            break;
        case '8':
            $month = 'August';
            break;
        case '9':
            $month = 'September';
            break;
        case '10':
            $month = 'October';
            break;
        case '11':
            $month = 'November';
            break;
        case '12':
            $month = 'December';
            break;
    }
    return $month;
}
function sum_fin($id = '', $nam, $total, $thangs)
{
    $thang = cover_month($thangs);
    $fin = get_table_where('tblcosts', array('costs_parent' => $id));
    foreach ($fin as $key => $value) {
        $financial_control_detail = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $value['id'], 'nam' => $nam), '', 'row');
        if (!empty($financial_control_detail)) {
            $total += $financial_control_detail->$thang;
        }
        $ktr = get_table_where('tblcosts', array('costs_parent' => $value['id']), '', 'row');
        if (!empty($ktr)) {
            $total = sum_fin($value['id'], $nam, $total, $thangs);
        }
    }
    return $total;
}
function sum_fin_other_operations($id = '', $nam, $total, $thangs)
{
    if ($id > 0) {
        $fin = get_table_where('tblcosts', array('costs_parent' => $id));
        foreach ($fin as $key => $value) {
            $thu = get_table_where_sum('tblother_payslips_coupon', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $value['id']), 'total');
            $chi = get_table_where_sum('tblother_payslips', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $value['id']), 'total');
            $chi_c = get_table_where_sum('tblpay_slip', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $value['id']), 'total');
            $tu = get_table_where_sum('tbladvance_payment', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $value['id']), 'total');
            $total += $thu + $chi + $chi_c + $tu;


            $ktr = get_table_where('tblcosts', array('costs_parent' => $value['id']), '', 'row');
            if (!empty($ktr)) {
                $total = sum_fin_other_operations($value['id'], $nam, $total, $thangs);
            }
        }
        return $total;
    } else {
        // $tienmat = get_table_where('tblinvoicepaymentsmodes',array('type'=>'Tiền mặt'));

        // $thutm = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PTK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Tiền mặt")'),'total');
        // $chitm = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PCK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Tiền mặt")'),'total');
        // $thunh = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PTK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Ngân hàng")'),'total');
        // $chinh = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PCK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Ngân hàng")'),'total');
        // $chi = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PCK','financial_control'=>$value['id']),'total');
        $total = 0;
        return $total;
    }
}

function get_tale_fin_retport($id = '', $nam, $id_new = array(), $dem, $thuchis, $thangs)
{
    $aColumns     = array(
        'tblcosts.id',
        'tblcosts.costs_parent',
        'tblcosts.code',
        'tblcosts.name',
        '11',
        '1',
        '2',
        '12',
    );
    $rows = array();
    $join = array();
    $sIndexColumn = "id";
    $sTable       = 'tblcosts';
    $where = array('AND tblcosts.costs_parent =' . $id);
    if (!empty($id_new)) {
        if (!empty($id_new[$dem])) {
            array_push($where, 'AND tblcosts.id = "' . $id_new[$dem] . '"');
        } else {
            array_push($where, 'AND tblcosts.id = -1');
        }
    }
    $group_by = "GROUP BY tblcosts.id";
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblcosts.id'), '', $group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $thang = cover_month($thangs);
    foreach ($rResult as $key => $aRow) {
        $ktr = get_table_where('tblcosts', array('costs_parent' => $aRow['id']), '', 'row');
        $row = array();
        $financial_control_detail = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $aRow['id'], 'nam' => $nam), '', 'row');
        for ($i = 0; $i < count($aColumns); $i++) {

            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == '11') {
                if (!empty($ktr)) {
                    $total = sum_fin($aRow['id'], $nam, 0, $thangs);
                    $_data = text_align(number_format($total), 'right');
                } else {
                    if (!empty($financial_control_detail)) {
                        $_data = text_align(number_format($financial_control_detail->$thang), 'right');
                    } else {
                        $_data = text_align(number_format(0), 'right');
                    }
                }
            }
            if ($aColumns[$i] == 'tblcosts.costs_parent') {
                $_data = get_table_where('tblcosts', array('id' => $aRow['tblcosts.costs_parent']), '', 'row')->code;
            }
            if ($aColumns[$i] == 'tblcosts.lever') {
                $_data = 'Cấp: ' . $aRow[$aColumns[$i]];
            }
            if ($aColumns[$i] == '1') {
                if (!empty($ktr)) {
                    $thuchi = sum_fin_other_operations($aRow['id'], $nam, 0, $thangs);
                    $_data = text_align(number_format($thuchi), 'right');
                } else {
                    if ($aRow['id'] == -2) {
                        // $thutm = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PTK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Tiền mặt")'),'total');
                        // $chitm = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PCK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Tiền mặt")'),'total');    
                        $thuchi = 0;
                    } elseif ($aRow['id'] == -3) {
                        // $thunh = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PTK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Ngân hàng")'),'total');
                        // $chinh = get_table_where_sum('tblother_operations',array('month(date)'=>$thangs,'year(date)'=>$nam,'prefix'=>'PCK','payment_modes IN(select tblinvoicepaymentsmodes.id from tblinvoicepaymentsmodes where tblinvoicepaymentsmodes.type = "Ngân hàng")'),'total');    
                        $thuchi = 0;
                    } else {
                        $thu = get_table_where_sum('tblother_payslips_coupon', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $aRow['id']), 'total');

                        $chi = get_table_where_sum('tblother_payslips', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $aRow['id']), 'total');
                        $chi_c = get_table_where_sum('tblpay_slip', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $aRow['id']), 'total');

                        $tu = get_table_where_sum('tbladvance_payment', array('month(date)' => $thangs, 'year(date)' => $nam, 'id_costs' => $aRow['id']), 'total');

                        $thuchi = $thu + $chi + $chi_c + $tu;
                    }
                    $_data = text_align(number_format($thuchi), 'right');
                }
            }
            if ($aColumns[$i] == '2') {
                if (!empty($ktr)) {
                    $avg = $thuchi - $total;
                    if ($avg <= 0) {
                        $_data = text_align(number_format(abs($avg)) . ' <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:#379a31;"></i>', 'right');
                    } else {
                        $_data = text_align(number_format(abs($avg)) . ' <i style="color:red;" class="fa fa-long-arrow-down" aria-hidden="true"></i>', 'right');
                    }
                } else {
                    if (!empty($financial_control_detail)) {
                        $avg = $thuchi - $financial_control_detail->$thang;
                    } else {
                        $avg = $thuchi - 0;
                    }
                    if ($avg <= 0) {
                        $_data = text_align(number_format(abs($avg)) . ' <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:#379a31;"></i>', 'right');
                    } else {
                        $_data = text_align(number_format(abs($avg)) . ' <i style="color:red;" class="fa fa-long-arrow-down" aria-hidden="true"></i>', 'right');
                    }
                }
            }
            if ($aColumns[$i] == '12') {
                if ($thuchis == 0) {
                    $_data = '0%';
                } else {
                    $_data = round((($thuchi / $thuchis) * 100), 2, PHP_ROUND_HALF_UP) . '%';
                }
            }
            $row[] = $_data;
            if (!empty($ktr)) {
                $row['DT_RowClass'] = 'alert-header bold warning';
            }
        }
        $rows[] = $row;
        if (!empty($ktr)) {
            $rows = array_merge($rows, get_tale_fin_retport($aRow['id'], $nam, $id_new, ($dem + 1), $thuchi, $thangs));
        }
    }
    return $rows;
}
function  get_months()
{
    return array(
        'January' => _l('January'),
        'February' => _l('February'),
        'March' => _l('March'),
        'April' => _l('April'),
        'May' => _l('May'),
        'June' => _l('June'),
        'July' => _l('July'),
        'August' => _l('August'),
        'September' => _l('September'),
        'October' => _l('October'),
        'November' => _l('November'),
        'December' => _l('December')
    );
}
function get_tale_fin($id = '', $nam, $id_new = array(), $dem)
{
    $aColumns     = array(
        'tblcosts.id',
        'tblcosts.costs_parent',
        'tblcosts.code',
        'tblcosts.name',
        'tblcosts.lever',
        '1',
    );
    $months = get_months();
    $rows = array();
    foreach ($months as $key => $month) {
        array_push($aColumns, $key);
    }
    $join = array();
    array_push($join, 'LEFT JOIN tblfinancial_control_detail ON tblfinancial_control_detail.id_financial_control=tblcosts.id');
    $sIndexColumn = "id";
    $sTable       = 'tblcosts';
    $where = array('AND tblcosts.costs_parent =' . $id);
    if ($id_new) {
        array_push($where, 'AND tblcosts.id = "' . $id_new[$dem] . '"');
    }
    // if($nam) {
    // array_push($where, 'AND tblfinancial_control_detail.nam = "'.$nam.'"');
    // }
    $group_by = "GROUP BY tblcosts.id";
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblcosts.id'), '', $group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    foreach ($rResult as $key => $aRow) {
        $row = array();
        $ktr = get_table_where('tblcosts', array('costs_parent' => $aRow['id']), '', 'row');

        $financial_control_detail = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $aRow['id'], 'nam' => $nam), '', 'row');
        for ($i = 0; $i < count($aColumns); $i++) {

            if ($i > 5 && $i < count($aColumns)) {
                if (!empty($ktr)) {
                    if (!empty($financial_control_detail)) {
                        $_data = text_align(number_format($financial_control_detail->$aColumns[$i]), 'right');
                    } else {
                        $_data = text_align(number_format(0), 'right');
                    }
                } else {
                    if (!empty($financial_control_detail)) {
                        $text = $aColumns[$i];
                        $_data = '<div class="type_v1">' . ch_EditColumSelectInput(number_format($financial_control_detail->$text), $aRow['tblcosts.id'], '', '<a class="pointer" id="check_' . $aRow['tblcosts.id'] . '" target="_blank" >' . number_format($financial_control_detail->$text) . '</a>', '', admin_url('costs/price_items/' . $nam . '/' . $aColumns[$i]), 'class="formUpdateDataTable"') . '</div><div class="type_v2 hide" data-id="' . $aRow['tblcosts.id'] . '" class="price_items_input"><input onkeyup="formatNumBerKeyUp(this)" type="text" name="price_items" id="price_items" class="height_auto  price_items H_input align_right" value="' . number_format($financial_control_detail->$text) . '"></div>';
                    } else {
                        $_data = '<div class="type_v1">' . ch_EditColumSelectInput(number_format(0), $aRow['tblcosts.id'], '', '<a class="pointer" id="check_' . $aRow['tblcosts.id'] . '" target="_blank" >' . number_format(0) . '</a>', '', admin_url('costs/price_items/' . $nam . '/' . $aColumns[$i]), 'class="formUpdateDataTable"') . '</div><div class="type_v2 hide" data-id="' . $aRow['tblcosts.id'] . '" class="price_items_input"><input onkeyup="formatNumBerKeyUp(this)" type="text" name="price_items" id="price_items" class="height_auto  price_items H_input align_right" value="' . number_format(0) . '"></div>';
                    }
                }
            }
            if ($aColumns[$i] == 'tblcosts.costs_parent') {
                $_data = get_table_where('tblcosts', array('id' => $aRow['tblcosts.costs_parent']), '', 'row')->code;
            }
            if ($aColumns[$i] == 'tblcosts.lever') {
                $_data = 'Cấp: ' . $aRow[$aColumns[$i]];
            }
            if ($aColumns[$i] == 'tblcosts.name') {
                $_data = $aRow[$aColumns[$i]];
            }
            if ($aColumns[$i] == '1') {
                $_data = $nam;
            }
            if ($aColumns[$i] == 'tblcosts.id') {
                $_data = $key + 1;
            }
            $row[] = $_data;
        }
        $rows[] = $row;
        if (!empty($ktr)) {
            $rows = array_merge($rows, get_tale_fin($aRow['id'], $nam, $id_new, ($dem + 1)));
        }
    }
    return $rows;
}

function getStart_debt_supplierts_v2($id_suppliert = NULL, $startDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert) && !empty($startDate)) {
        $CI->db->select_sum('total');
        $CI->db->where('tblimport.date <', $startDate);
        $total = $CI->db->get_where('tblimport', array('tblimport.suppliers_id' => $id_suppliert))->row()->total;

        $CI->db->select_sum('subtotal');
        $CI->db->where('tbl_services.date <', $startDate);
        $CI->db->where('tbl_services.status', 1);
        $total += $CI->db->get_where('tbl_services', array('tbl_services.suppliers_id' => $id_suppliert))->row()->subtotal;

        $CI->db->select_sum('tblreturn_suppliers.total');
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.date >=', $startDate);
        }
        $CI->db->where('tblreturn_suppliers.treatment_methods ', 2);
        $CI->db->where('tblreturn_suppliers.status ', 2);
        $total -= $CI->db->get_where(
            'tblreturn_suppliers',
            array('tblreturn_suppliers.suppliers_id' => $id_suppliert)
        )->row()->total;
    }
    return $total;
}

function debt_supplierts_v2($id_suppliert = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert)) {
        $CI->db->select_sum('total');
        if ($startDate) {
            $CI->db->where('tblimport.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblimport.date <=', $startEnd);
        }
        $total = $CI->db->get_where('tblimport', array('tblimport.suppliers_id' => $id_suppliert))->row()->total;

        $CI->db->select_sum('subtotal');
        if ($startDate) {
            $CI->db->where('tbl_services.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_services.date <=', $startEnd);
        }
        $CI->db->where('tbl_services.status', 1);
        $total += $CI->db->get_where('tbl_services', array('tbl_services.suppliers_id' => $id_suppliert))->row()->subtotal;

        $CI->db->select_sum('tblreturn_suppliers.total');
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblreturn_suppliers.date <=', $startEnd);
        }
        $CI->db->where('tblreturn_suppliers.treatment_methods ', 2);
        $CI->db->where('tblreturn_suppliers.status ', 2);
        $total -= $CI->db->get_where(
            'tblreturn_suppliers',
            array('tblreturn_suppliers.suppliers_id' => $id_suppliert)
        )->row()->total;
    }
    return $total;
}
function debt_supplierts_v3($id_suppliert = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert)) {
        $CI->db->select_sum('total');
        if ($startDate) {
            $CI->db->where('tblimport.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblimport.date <', $startEnd);
        }
        $total = $CI->db->get_where('tblimport', array('tblimport.suppliers_id' => $id_suppliert))->row()->total;
    }
    return $total;
}
function pay_slip_ch_v3($id_suppliert = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert)) {
        $CI->db->select_sum('payment');
        if ($startDate) {
            $CI->db->where('tblpay_slip.date >=', $startDate . ' 00:00:00');
        }
        if ($startEnd) {
            $CI->db->where('tblpay_slip.date <', $startEnd . ' 23:59:59');
        }
        $pay_slip = $CI->db->get_where('tblpay_slip', array('tblpay_slip.id_supplierss' => $id_suppliert))->row()->payment;

        $CI->db->select_sum('total');
        if ($startDate) {
            $CI->db->where('tblother_payslips.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblother_payslips.date <', $startEnd);
        }
        $CI->db->where('tblother_payslips.objects ', 2);
        $other_payslips = $CI->db->get_where('tblother_payslips', array('tblother_payslips.objects_id' => $id_suppliert))->row()->total;
        $total = $pay_slip + $other_payslips;
    }
    return $total;
}
