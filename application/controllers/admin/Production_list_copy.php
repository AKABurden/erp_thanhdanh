<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Production_list_copy extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('production_list_model');
        $this->tnh = true;

        $this->perViewProductionList = has_permission('production_list', '', 'view');
        $this->perAddProductionList = has_permission('production_list', '', 'create');
        $this->perEditProductionList = has_permission('production_list', '', 'edit');
        $this->perDeleteProductionList = has_permission('production_list', '', 'delete');
    }

    public function index()
    {
        if (!$this->perViewProductionList) {
            accessDenied();
        }

        $data['type_productionlist'] = $this->production_list_model->getTypeProductionList();
        $data['title'] = lang('tnh_production_list');
        $this->load->view('admin/manufactures/production_list', $data);
    }

    public function handling($production_list_id = 0)
    {

        if (!$this->perAddProductionList && empty($production_list_id)) {
            accessDenied();
        } else if (!$this->perEditProductionList && !empty($production_list_id)) {
            accessDenied();
        }

        $data = [];
        if ($this->input->post('add')) {
            $data = [];

            $production_list_id_edit = $this->input->post('production_list_id');
            if ($production_list_id_edit) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_production_list"), 'trim|required');
            } else {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_production_list"), 'trim|required|is_unique[tbl_production_lists.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true) {
                if ($production_list_id_edit) {
                    $reference_no = $this->input->post('reference_no');
                } else {
                    $reference_no = getReference('production_lists');
                }

                $dataPOST = $this->input->post();

                $start_date = $dataPOST['start_date'];
                $end_date = $dataPOST['end_date'];
                if (empty($start_date) || empty($end_date)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn ngày bắt đầu và kết thúc');
                    echo json_encode($data);
                    die;
                }
                $start_date = to_sql_date($start_date);
                $end_date = to_sql_date($end_date);

                $date = !empty($dataPOST['date']) ? to_sql_date($dataPOST['date'], true) : null;

                $so_luong_may = !empty($dataPOST['so_luong_may']) ? number_unformat($dataPOST['so_luong_may']) : 0;
                $nhom_tho = !empty($dataPOST['nhom_tho']) ? number_unformat($dataPOST['nhom_tho']) : 0;
                $nang_suat_may = !empty($dataPOST['nang_suat_may']) ? number_unformat($dataPOST['nang_suat_may']) : 0;
                $_thoi_gian_canh_bai = !empty($dataPOST['_thoi_gian_canh_bai']) ? number_unformat($dataPOST['_thoi_gian_canh_bai']) : 0;
                $thoi_gian_lam_viec_chuan = !empty($dataPOST['thoi_gian_lam_viec_chuan']) ? number_unformat($dataPOST['thoi_gian_lam_viec_chuan']) : 0;
                $thoi_gian_lam_viec_ot = !empty($dataPOST['thoi_gian_lam_viec_ot']) ? number_unformat($dataPOST['thoi_gian_lam_viec_ot']) : 0;
                $thoi_gian_cho_kho = !empty($dataPOST['thoi_gian_cho_kho']) ? ($dataPOST['thoi_gian_cho_kho']) : '';
                $bong_os_nhung = !empty($dataPOST['bong_os_nhung']) ? number_unformat($dataPOST['bong_os_nhung']) : 0;
                $capacity_1 = !empty($dataPOST['capacity_1']) ? number_unformat($dataPOST['capacity_1']) : 0;
                $capacity_2 = !empty($dataPOST['capacity_2']) ? number_unformat($dataPOST['capacity_2']) : 0;
                $capacity_3 = !empty($dataPOST['capacity_3']) ? number_unformat($dataPOST['capacity_3']) : 0;

                $so_luong_tho =  !empty($dataPOST['so_luong_tho']) ? number_unformat($dataPOST['so_luong_tho']) : 0;

                $nang_suat_may_in_300 =  !empty($dataPOST['nang_suat_may_in_300']) ? number_unformat($dataPOST['nang_suat_may_in_300']) : 0;
                $nang_suat_may_in_600 =  !empty($dataPOST['nang_suat_may_in_600']) ? number_unformat($dataPOST['nang_suat_may_in_600']) : 0;

                $nang_suat_dau_in_trang_den =  !empty($dataPOST['nang_suat_dau_in_trang_den']) ? number_unformat($dataPOST['nang_suat_dau_in_trang_den']) : 0;
                $nang_suat_dau_in_mau =  !empty($dataPOST['nang_suat_dau_in_mau']) ? number_unformat($dataPOST['nang_suat_dau_in_mau']) : 0;
                $thoi_gian_canh_bai_in_trang_den =  !empty($dataPOST['thoi_gian_canh_bai_in_trang_den']) ? number_unformat($dataPOST['thoi_gian_canh_bai_in_trang_den']) : 0;
                $thoi_gian_canh_bai_in_mau =  !empty($dataPOST['thoi_gian_canh_bai_in_mau']) ? number_unformat($dataPOST['thoi_gian_canh_bai_in_mau']) : 0;

                $nang_suat_keo_tay =  !empty($dataPOST['nang_suat_keo_tay']) ? number_unformat($dataPOST['nang_suat_keo_tay']) : 0;

                $nang_suat_may_boi_mot_mat =  !empty($dataPOST['nang_suat_may_boi_mot_mat']) ? number_unformat($dataPOST['nang_suat_may_boi_mot_mat']) : 0;
                $nang_suat_may_boi_hai_mat =  !empty($dataPOST['nang_suat_may_boi_hai_mat']) ? number_unformat($dataPOST['nang_suat_may_boi_hai_mat']) : 0;

                $nang_suat_may_be_giay_thuong =  !empty($dataPOST['nang_suat_may_be_giay_thuong']) ? number_unformat($dataPOST['nang_suat_may_be_giay_thuong']) : 0;
                $nang_suat_may_demi_be_giay_boi_pet =  !empty($dataPOST['nang_suat_may_demi_be_giay_boi_pet']) ? number_unformat($dataPOST['nang_suat_may_demi_be_giay_boi_pet']) : 0;

                $items = !empty($dataPOST['items']) ? $dataPOST['items'] : null;

                $type_productionlist_id = !empty($dataPOST['type_productionlist_id']) ? $dataPOST['type_productionlist_id'] : 0;
                if (empty($type_productionlist_id)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn loại công đoạn');
                    echo json_encode($data);
                    die;
                }

                if ($type_productionlist_id == 1) {
                    $capacity_2 = $nhom_tho * $nang_suat_may * $thoi_gian_lam_viec_chuan;
                    $capacity_3 = $nhom_tho * $nang_suat_may * $thoi_gian_lam_viec_ot;
                } else if ($type_productionlist_id == 2) {
                    $capacity_2 = $so_luong_tho * $nang_suat_may * $thoi_gian_lam_viec_chuan;
                    $capacity_3 = $so_luong_tho * $nang_suat_may * $thoi_gian_lam_viec_ot;
                }


                $production_lists_total = [
                    'type_productionlist_id' => $type_productionlist_id,
                    'so_luong_may' => $so_luong_may,
                    'nhom_tho' => $nhom_tho,
                    'nang_suat_may' => $nang_suat_may,
                    '_thoi_gian_canh_bai' => $_thoi_gian_canh_bai,
                    'thoi_gian_lam_viec_chuan' => $thoi_gian_lam_viec_chuan,
                    'thoi_gian_lam_viec_ot' => $thoi_gian_lam_viec_ot,
                    'thoi_gian_cho_kho' => $thoi_gian_cho_kho,
                    'bong_os_nhung' => $bong_os_nhung,
                    'capacity_1' => $capacity_1,
                    'capacity_2' => $capacity_2,
                    'capacity_3' => $capacity_3,
                    'so_luong_tho' => $so_luong_tho,

                    'nang_suat_may_in_300' => $nang_suat_may_in_300,
                    'nang_suat_may_in_600' => $nang_suat_may_in_600,

                    'nang_suat_dau_in_trang_den' => $nang_suat_dau_in_trang_den,
                    'nang_suat_dau_in_mau' => $nang_suat_dau_in_mau,
                    'thoi_gian_canh_bai_in_trang_den' => $thoi_gian_canh_bai_in_trang_den,
                    'thoi_gian_canh_bai_in_mau' => $thoi_gian_canh_bai_in_mau,

                    'nang_suat_keo_tay' => $nang_suat_keo_tay,

                    'nang_suat_may_boi_mot_mat' => $nang_suat_may_boi_mot_mat,
                    'nang_suat_may_boi_hai_mat' => $nang_suat_may_boi_hai_mat,

                    'nang_suat_may_be_giay_thuong' => $nang_suat_may_be_giay_thuong,
                    'nang_suat_may_demi_be_giay_boi_pet' => $nang_suat_may_demi_be_giay_boi_pet,
                ];

                $arrItems = [];
                $arrProductionsListsDate = [];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $po_id = $value['po_id'];
                        $item_id = $value['item_id'];

                        $to_in = !empty($value['to_in']) ? number_unformat($value['to_in']) : 0;

                        $ngay_mo_lsx = !empty($value['ngay_mo_lsx']) ? to_sql_date($value['ngay_mo_lsx']) : null;
                        $ngay_giao_hang_he_thong = !empty($value['ngay_mo_lsx']) ? to_sql_date($value['ngay_giao_hang_he_thong']) : null;
                        $stage_name = !empty($value['stage_name']) ? $value['stage_name'] : '';
                        $str_stage_id = !empty($value['str_stage_id']) ? $value['str_stage_id'] : '';
                        $so_con_tren_to_in = !empty($value['so_con_tren_to_in']) ? number_unformat($value['so_con_tren_to_in']) : 0;
                        $so_con_tren_kb_offset = !empty($value['so_con_tren_kb_offset']) ? number_unformat($value['so_con_tren_kb_offset']) : 0;
                        $so_mat_in = !empty($value['so_mat_in']) ? number_unformat($value['so_mat_in']) : 0;
                        $thoi_gian_canh_bai = !empty($value['thoi_gian_canh_bai']) ? number_unformat($value['thoi_gian_canh_bai']) : 0;
                        $ngay_giao_hang = !empty($value['ngay_giao_hang']) ? to_sql_date($value['ngay_giao_hang']) : null;
                        $ngay_ve_nvl_du_kien = !empty($value['ngay_ve_nvl_du_kien']) ? to_sql_date($value['ngay_ve_nvl_du_kien']) : null;
                        $ngay_ban_giao_san_xuat = !empty($value['ngay_ban_giao_san_xuat']) ? to_sql_date($value['ngay_ban_giao_san_xuat']) : null;
                        $ngay_bat_dau_du_kien = !empty($value['ngay_bat_dau_du_kien']) ? to_sql_date($value['ngay_bat_dau_du_kien']) : null;
                        $ngay_hoan_thanh_in = !empty($value['ngay_hoan_thanh_in']) ? to_sql_date($value['ngay_hoan_thanh_in']) : null;
                        $tinh_trang = !empty($value['tinh_trang']) ? $value['tinh_trang'] : '';
                        $may_in = !empty($value['may_in']) ? $value['may_in'] : '';
                        $ghi_chu = !empty($value['ghi_chu']) ? $value['ghi_chu'] : '';
                        $bong_mang = !empty($value['bong_mang']) ? $value['bong_mang'] : '';
                        $hoan_thanh = !empty($value['hoan_thanh']) ? $value['hoan_thanh'] : '';
                        $thoi_gian_con_lai = !empty($value['thoi_gian_con_lai']) ? $value['thoi_gian_con_lai'] : 0;
                        $ngay_hoan_thanh_mang = !empty($value['ngay_hoan_thanh_mang']) ? to_sql_date($value['ngay_hoan_thanh_mang']) : null;

                        $tong_tua = 0;
                        $thoi_gian_in = 0;
                        $tua_sau_in = 0;
                        $thoi_gian_xu_ly = 0;
                        if ($type_productionlist_id == 1) {
                            $tong_tua = $so_mat_in * $to_in;
                            if ($nang_suat_may > 0) {
                                $thoi_gian_in = $tong_tua / $nang_suat_may;
                            }
                            $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;
                            $tua_sau_in = ($so_con_tren_kb_offset > 0 ? ($so_con_tren_to_in / $so_con_tren_kb_offset * $to_in) : $to_in);
                        }

                        //type_productionlist_id = 2
                        $so_luong_san_xuat = !empty($value['so_luong_san_xuat']) ? number_unformat($value['so_luong_san_xuat']) : 0;
                        $so_con_tren_kb_flexo = !empty($value['so_con_tren_kb_flexo']) ? number_unformat($value['so_con_tren_kb_flexo']) : 0;
                        $so_tua_in_flexo = 0;
                        if ($type_productionlist_id == 2) {
                            $tong_tua = $so_mat_in * $to_in;
                            $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;

                            $so_tua_in_flexo = 0;
                            if ($so_con_tren_kb_flexo > 0) {
                                $so_tua_in_flexo = $so_luong_san_xuat / $so_con_tren_kb_flexo;
                            }

                            $thoi_gian_in = 0;
                            if ($nang_suat_may > 0) {
                                $thoi_gian_in = $so_tua_in_flexo / $nang_suat_may;
                            }
                        }

                        //type_productionlist_id = 3
                        $nang_suat = 0;
                        $dau_in = !empty($value['dau_in']) ? number_unformat($value['dau_in']) : 0;
                        $so_tua_in = 0;
                        if ($type_productionlist_id == 3) {
                            $nang_suat = ($dau_in == 300) ? $nang_suat_may_in_300 : $nang_suat_may_in_600;
                            $so_tua_in = $so_luong_san_xuat;

                            $thoi_gian_in = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_in = $so_tua_in / $nang_suat;
                            }

                            $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                            $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 4
                        $loai = !empty($value['loai']) ? $value['loai'] : '';
                        $thoi_gian_canh_bai = !empty($value['thoi_gian_canh_bai']) ? number_unformat($value['thoi_gian_canh_bai']) : 0;
                        $ghi_chu_2 = !empty($value['ghi_chu_2']) ? $value['ghi_chu_2'] : '';
                        if ($type_productionlist_id == 4) {
                            $nang_suat = (strtoupper($loai) == "T/D") ? $nang_suat_dau_in_trang_den : $nang_suat_dau_in_mau;
                            $so_tua_in = $to_in * $so_mat_in;

                            $thoi_gian_in = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_in = $so_tua_in / $nang_suat;
                            }

                            $thoi_gian_canh_bai = (strtoupper($loai) == "T/D") ? $thoi_gian_canh_bai_in_trang_den : $thoi_gian_canh_bai_in_mau;
                            $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 5 - Lụa
                        $so_mau_in = !empty($value['so_mau_in']) ? number_unformat($value['so_mau_in']) : 0;
                        $be_xa_cat = !empty($value['be_xa_cat']) ? $value['be_xa_cat'] : '';
                        if ($type_productionlist_id == 5) {
                            $so_tua_in = $to_in * $so_mat_in * $so_mau_in;

                            $nang_suat = $nang_suat_keo_tay;
                            $thoi_gian_in = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_in = $so_tua_in / $nang_suat;
                            }
                            $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                            $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 6 - Cán màng
                        $be_xa_khoan = !empty($value['be_xa_khoan']) ? $value['be_xa_khoan'] : '';
                        $tong_thoi_gian = 0;
                        if ($type_productionlist_id == 6) {
                            $so_tua_in = $to_in * $so_mat_in;
                            $nang_suat = $nang_suat_may;

                            $thoi_gian_xu_ly = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_xu_ly = $so_tua_in / $nang_suat;
                            }

                            $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                            $tong_thoi_gian = $thoi_gian_xu_ly + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 7 - Phun bóng
                        $boi = !empty($value['boi']) ? $value['boi'] : '';
                        $be_xa_khoan_lo_2 = !empty($value['be_xa_khoan_lo_2']) ? $value['be_xa_khoan_lo_2'] : '';
                        $so_mat_phun_bong = !empty($value['so_mat_phun_bong']) ? number_unformat($value['so_mat_phun_bong']) : 0;
                        if ($type_productionlist_id == 7) {
                            $so_tua_in = $to_in * $so_mat_phun_bong;
                            $nang_suat = $nang_suat_may;
                            $thoi_gian_xu_ly = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_xu_ly = $so_tua_in / $nang_suat;
                            }

                            $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                            $tong_thoi_gian = $thoi_gian_xu_ly + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 8 - Bồi
                        $so_con_tren_kb = !empty($value['so_con_tren_kb']) ? number_unformat($value['so_con_tren_kb']) : 0;
                        $loai_boi = !empty($value['loai_boi']) ? number_unformat($value['loai_boi']) : 0;
                        if ($type_productionlist_id == 8) {
                            $nang_suat = 0;
                            $so_tua_in = $to_in;
                            if ($loai_boi == 2 || $loai_boi == '2') {
                                $nang_suat = $nang_suat_may_boi_hai_mat;
                            } else {
                                $nang_suat = $nang_suat_may_boi_mot_mat;
                            }

                            $thoi_gian_xu_ly = 0;
                            if ($nang_suat > 0) {
                                $thoi_gian_xu_ly = $so_tua_in / $nang_suat;
                            }

                            $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                            $tong_thoi_gian = $thoi_gian_xu_ly + $thoi_gian_canh_bai;
                        }

                        //type_productionlist_id = 9 - Bế
                        $loai_giay = !empty($value['loai_giay']) ? ($value['loai_giay']) : '';
                        $ngay_hoan_thanh_in = !empty($value['ngay_hoan_thanh_in']) ? to_sql_date($value['ngay_hoan_thanh_in']) : null;
                        $ngay_hoan_thanh_bong = !empty($value['ngay_hoan_thanh_bong']) ? to_sql_date($value['ngay_hoan_thanh_bong']) : null;
                        $ngay_hoan_thanh_can_mang = !empty($value['ngay_hoan_thanh_can_mang']) ? to_sql_date($value['ngay_hoan_thanh_can_mang']) : null;
                        $ngay_hoan_thanh_boi = !empty($value['ngay_hoan_thanh_boi']) ? to_sql_date($value['ngay_hoan_thanh_boi']) : null;
                        $ngay_hoan_thanh_lua = !empty($value['ngay_hoan_thanh_lua']) ? to_sql_date($value['ngay_hoan_thanh_lua']) : null;
                        $ngay_hoan_thanh_flexo = !empty($value['ngay_hoan_thanh_flexo']) ? to_sql_date($value['ngay_hoan_thanh_flexo']) : null;
                        $ngay_hoan_thanh_hp = !empty($value['ngay_hoan_thanh_hp']) ? to_sql_date($value['ngay_hoan_thanh_hp']) : null;

                        if ($type_productionlist_id == 9) {
                            $tong_tua = 0;
                            if ($so_con_tren_kb > 0) {
                                $tong_tua = $so_con_tren_to_in / $so_con_tren_kb * $to_in;
                                $nang_suat = 0;
                                if (strtoupper($loai_giay) == 'THƯỜNG' || $loai_giay == 'thường' || $loai_giay == 'Thường') {
                                    $nang_suat = $nang_suat_may_be_giay_thuong;
                                } else {
                                    $nang_suat = $nang_suat_may_demi_be_giay_boi_pet;
                                }

                                $thoi_gian_xu_ly = 0;
                                if ($nang_suat > 0) {
                                    $thoi_gian_xu_ly = $tong_tua / $nang_suat;
                                }

                                $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                                $tong_thoi_gian = $thoi_gian_xu_ly + $thoi_gian_canh_bai;
                            }
                        }

                        $stage_id = !empty($value['stage_id']) ? ($value['stage_id']) : 0;

                        if ($production_list_id_edit) {
                            $queryCheck = '(SELECT count(tbl_production_lists_total.id) as ct
                            FROM tbl_production_lists_total
                            INNER JOIN tbl_production_lists_items ON tbl_production_lists_items.production_list_total_id = tbl_production_lists_total.id
                            WHERE tbl_production_lists_total.type_productionlist_id = ' . $type_productionlist_id . ' AND tbl_production_lists_items.stage_id = ' . $stage_id . ' AND tbl_production_lists_total.production_list_id != ' . $production_list_id_edit . ')';
                            $isQueryCheck = $this->db->query($queryCheck)->row_array();
                            if (!empty($isQueryCheck['ct'])) {
                                continue;
                            }
                        }

                        $ngay_ket_thuc = !empty($value['ngay_ket_thuc']) ? to_sql_date($value['ngay_ket_thuc']) : null;

                        $dtListItem = $this->production_list_model->getProductionListsItemsByUp($po_id, $production_list_id_edit, $stage_id);

                        $arrItems[] = [
                            'po_id' => $po_id,
                            'type_item' => 'products',
                            'item_id' => $item_id,
                            'to_in' => $to_in,
                            'so_mat_in' => $so_mat_in,
                            'tong_tua' => $tong_tua,
                            'thoi_gian_in' => $thoi_gian_in,
                            'ngay_mo_lsx' => $ngay_mo_lsx,
                            'ngay_giao_hang_he_thong' => $ngay_giao_hang_he_thong,
                            'ngay_ve_nvl_du_kien' => $ngay_ve_nvl_du_kien,
                            'ngay_ban_giao_san_xuat' => $ngay_ban_giao_san_xuat,
                            'ngay_bat_dau_du_kien' => $ngay_bat_dau_du_kien,
                            'ngay_hoan_thanh_in' => $ngay_hoan_thanh_in,
                            'tinh_trang' => $tinh_trang,
                            'may_in' => $may_in,
                            'thoi_gian_con_lai' => $thoi_gian_con_lai,
                            'ghi_chu' => $ghi_chu,
                            'so_con_tren_to_in' => $so_con_tren_to_in,
                            'so_con_tren_kb_offset' => $so_con_tren_kb_offset,
                            'tua_sau_in' => $tua_sau_in,
                            'bong_mang' => $bong_mang,
                            'hoan_thanh' => $hoan_thanh,
                            'stage_name' => $stage_name,
                            'str_stage_id' => $str_stage_id,
                            'thoi_gian_xu_ly' => $thoi_gian_xu_ly,

                            'so_luong_san_xuat' => $so_luong_san_xuat,
                            'so_con_tren_kb_flexo' => $so_con_tren_kb_flexo,
                            'so_tua_in_flexo' => $so_tua_in_flexo,

                            'nang_suat' => $nang_suat,
                            'dau_in' => $dau_in,
                            'so_tua_in' => $so_tua_in,

                            'loai' => $loai,
                            'thoi_gian_canh_bai' => $thoi_gian_canh_bai,
                            'ghi_chu_2' => $ghi_chu_2,

                            'ngay_giao_hang' => $ngay_giao_hang,
                            'so_mau_in' => $so_mau_in,
                            'be_xa_cat' => $be_xa_cat,

                            'be_xa_khoan' => $be_xa_khoan,
                            'tong_thoi_gian' => $tong_thoi_gian,

                            'boi' => $boi,
                            'be_xa_khoan_lo_2' => $be_xa_khoan_lo_2,
                            'so_mat_phun_bong' => $so_mat_phun_bong,

                            'so_con_tren_kb' => $so_con_tren_kb,
                            'loai_boi' => $loai_boi,

                            'loai_giay' => $loai_giay,
                            'ngay_hoan_thanh_bong' => $ngay_hoan_thanh_bong,
                            'ngay_hoan_thanh_can_mang' => $ngay_hoan_thanh_can_mang,
                            'ngay_hoan_thanh_boi' => $ngay_hoan_thanh_boi,
                            'ngay_hoan_thanh_lua' => $ngay_hoan_thanh_lua,
                            'ngay_hoan_thanh_flexo' => $ngay_hoan_thanh_flexo,
                            'ngay_hoan_thanh_hp' => $ngay_hoan_thanh_hp,
                            'stage_id' => $stage_id,
                            'ngay_ket_thuc' => $ngay_ket_thuc,
                            'ngay_hoan_thanh_mang' => $ngay_hoan_thanh_mang,

                            'ngay_bat_dau_thuc_te' => !empty($dtListItem['ngay_bat_dau_thuc_te']) ? $dtListItem['ngay_bat_dau_thuc_te'] : null,
                            'ngay_ket_thuc_thuc_te' => !empty($dtListItem['ngay_ket_thuc_thuc_te']) ? $dtListItem['ngay_ket_thuc_thuc_te'] : null,
                            'so_luong_thuc_te' => !empty($dtListItem['so_luong_thuc_te']) ? $dtListItem['so_luong_thuc_te'] : 0,
                            'ngay_bat_dau_ke_hoach' => !empty($dtListItem['ngay_bat_dau_ke_hoach']) ? $dtListItem['ngay_bat_dau_ke_hoach'] : null,
                            'ngay_ket_thuc_ke_hoach' => !empty($dtListItem['ngay_ket_thuc_ke_hoach']) ? $dtListItem['ngay_ket_thuc_ke_hoach'] : null,
                            'status' => !empty($dtListItem['status']) ? $dtListItem['status'] : 1,
                        ];

                        if (!empty($arrProductionsListsDate[$ngay_bat_dau_du_kien])) {
                            $arrProductionsListsDate[$ngay_bat_dau_du_kien]['thoi_gian_xu_ly'] = $arrProductionsListsDate[$ngay_bat_dau_du_kien]['thoi_gian_xu_ly'] + $thoi_gian_xu_ly;
                        } else if (!empty($ngay_bat_dau_du_kien)) {
                            $arrProductionsListsDate[$ngay_bat_dau_du_kien] = [
                                'date_handling' => $ngay_bat_dau_du_kien,
                                'thoi_gian_xu_ly' => $thoi_gian_xu_ly,
                            ];
                        }
                    }
                }

                if (empty($arrItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có mặt hàng để lưu');
                    echo json_encode($data);
                    die;
                }

                if ($production_list_id_edit) {
                    $production_lists = [
                        'date' => $date,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'date_updated' => date('Y-m-d H:i:s'),
                        'updated_by' => get_staff_user_id(),
                    ];
                    $up = $this->production_list_model->updateProductionLists($production_list_id_edit, $production_lists);
                    if ($up) {
                        $production_list_id = $production_list_id_edit;
                    }
                } else {
                    //add
                    $production_lists = [
                        'date' => $date,
                        'reference_no' => $reference_no,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                    ];
                    $production_list_id = $this->production_list_model->insertProductionLists($production_lists);
                }

                if ($production_list_id) {
                    if (!$production_list_id_edit) {
                        updateReference('production_lists');
                    } else {
                        $this->production_list_model->deleteProductionListsItemsTypeProductionlistId($production_list_id, $type_productionlist_id);
                        $this->production_list_model->deleteProductionListsDateTypeProductionlistId($production_list_id, $type_productionlist_id);
                        $this->production_list_model->deleteProductionListsTotalTypeProductionlistId($production_list_id, $type_productionlist_id);
                    }

                    $production_lists_total['production_list_id'] = $production_list_id;
                    $production_list_total_id = $this->production_list_model->insertProductionListsTotal($production_lists_total);
                    if (!empty($arrItems)) {
                        foreach ($arrItems as $key => $value) {
                            $arrItems[$key]['production_list_id'] = $production_list_id;
                            $arrItems[$key]['production_list_total_id'] = $production_list_total_id;
                        }
                        $this->production_list_model->insertBatchProductionListsItems($arrItems);
                    }

                    if (!empty($arrProductionsListsDate)) {
                        $arrListsDate = [];
                        $i = 0;
                        foreach ($arrProductionsListsDate as $key => $value) {
                            $arrListsDate[$i] = $value;
                            $arrListsDate[$i]['production_list_id'] = $production_list_id;
                            $arrListsDate[$i]['production_list_total_id'] = $production_list_total_id;
                            $i++;
                        }

                        $this->production_list_model->insertBatchProductionListsDate($arrListsDate);
                    }

                    $data['production_list_id'] = $production_list_id;
                    $data['reference_no'] = $reference_no;
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        if (!empty($production_list_id)) {
            $production_lists = $this->production_list_model->getProductionListsById($production_list_id);
            $data['production_lists'] = $production_lists;
        }

        $data['production_list_id'] = $production_list_id;


        $data['title'] = lang('tnh_add_edit_production_list');
        $data['breadcrumb'] = [array('link' => base_url('admin/production_list'), 'page' => lang('tnh_production_list')), array('link' => '#', 'page' => lang('tnh_add_edit_production_list'))];
        $this->load->view('admin/manufactures/handling_production_list', $data);
    }

    public function loadDataProductionList()
    {
        if (!$this->perAddProductionList || !$this->perEditProductionList) {
            echo lang('access_denied');
            die;
        }

        $data = [];
        $this->load->view('admin/manufactures/load_data_production_list', $data);
    }

    public function loadDataTableProductionList()
    {
        if (!$this->perAddProductionList || !$this->perEditProductionList) {
            echo lang('access_denied');
            die;
        }

        $data = [];
        $this->load->view('admin/manufactures/load_data_table_production_list', $data);
    }

    public function getProductionLists()
    {
        if (!$this->perViewProductionList) {
            accessDenied($js = true);
        }

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');

        $tbProductionsLists = "(
            SELECT
                tbl_production_lists_total.production_list_id as production_list_id,
                GROUP_CONCAT(distinct tbl_type_productionlist.code SEPARATOR ', ') as code_type_productionlist
            FROM tbl_production_lists_total
            INNER JOIN tbl_type_productionlist ON tbl_type_productionlist.id = tbl_production_lists_total.type_productionlist_id
            GROUP BY tbl_production_lists_total.production_list_id
        ) tb_productions_lists";

        $aColumns = [
            'tbl_production_lists.id as id',
            'tbl_production_lists.date as date',
            'tbl_production_lists.start_date as start_date',
            'tbl_production_lists.end_date as end_date',
            'tbl_production_lists.reference_no as reference_no',
            'tb_productions_lists.code_type_productionlist as type_stages',
            '"" as printer',
            '"" as status',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as full_name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_production_lists';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_production_lists.created_by',
            'LEFT JOIN ' . $tbProductionsLists . ' ON tb_productions_lists.production_list_id = tbl_production_lists.id'
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, ' AND tbl_production_lists.date >= "' . $start_date_search . '"');
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, ' AND tbl_production_lists.date <= "' . $end_date_search . '"');
        }

        if (!empty($status_table) && $status_table != 'all') {
            array_push($where, ' AND exists (
                SELECT tbl_production_lists_total.id
                FROM tbl_production_lists_total
                WHERE tbl_production_lists_total.type_productionlist_id = ' . $status_table . ' AND tbl_production_lists.id = tbl_production_lists_total.production_list_id
            )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;
            $production_list_id = $aRow['id'];


            $edit = '<a href="' . base_url('admin/production_list/handling/' . $production_list_id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_production_list') . '</a>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/production_list/view_production_list/' . $production_list_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view_production_list') . '</a>';

            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/production_list/delete/' . $production_list_id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_production_list') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 195px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            //
            $this->db->select('
                tbl_machines.id as id,
                tbl_machines.name as name_machine,
                SUM(tbl_production_lists_items.thoi_gian_xu_ly) as thoi_gian_xu_ly
            ', false);
            $this->db->from('tbl_production_lists_items');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_production_lists_items.may_in');
            $this->db->where('tbl_production_lists_items.may_in >', 0);
            $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
            $this->db->group_by('tbl_machines.id');
            $machines = $this->db->get()->result_array();

            $strMachines = '';
            if (!empty($machines)) {
                $strMachines .= '<table class="table-bordered">';
                foreach ($machines as $kM => $vM) {
                    $strMachines .= '<tr>
                        <td class="text-center" style="padding: 5px;">' . $vM['name_machine'] . '</td>
                        <td class="text-center" style="padding: 5px;">' . formatNumber($vM['thoi_gian_xu_ly']) . '</td>
                    </tr>';
                }
                $strMachines .= '</table>';
            }

            // $this->db->select('
            //     count(tbl_production_lists_items.id) as ct_not_active
            // ', false);
            // $this->db->from('tbl_production_lists_items');
            // $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_production_lists_items.po_id');
            // $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id');
            // $this->db->where('tbl_production_lists_items.production_list_id', $production_list_id);
            // $this->db->where('tbl_productions_orders_items_stages.active', 0);
            // $this->db->limit(1);
            // $dtCheck = $this->db->get()->row_array();

            // $strStatus = '';
            // if (!empty($dtCheck)) {
            //     $strStatus.= '<div class="label label-warning">Đang sản xuất</div>';
            // } else {
            //     $strStatus.= '<div class="text-danger">Đang sản xuất</div>';
            // }

            foreach ($aColumns as $k => $v) {
                if ($v == 'actions') {
                    $row[] = $actions;
                } else if ($v == 'date' || $v == 'end_date' || $v == 'start_date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'reference_no') {
                    $row[] = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/production_list/view_production_list/' . $production_list_id) . '" data-toggle="modal" data-target="#myModal">' . ($aRow[$v]) . '</a>';
                } else if ($v == 'printer') {
                    $row[] = $strMachines;
                } else if ($v == 'status') {
                    $row[] = '';
                } else {
                    $row[] = $aRow[$v];
                }
            }

            // $print = $this->perPrintOrders ? '<a href="' . base_url('admin/orders/print_orders/' . $order_id) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . '</a>' : '';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function delete($id)
    {
        $data = [];
        if (!$this->perDeleteProductionList) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $isDelete = $this->production_list_model->deleteProductionLists($id);
        if ($isDelete) {
            $this->production_list_model->deleteProductionListsTotal($id);
            $this->production_list_model->deleteProductionListsItems($id);
            $this->production_list_model->deleteProductionListsDate($id);

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function view_production_list($id)
    {
        if (!$this->perViewProductionList) {
            accessDenied($js = true);
        }

        $data = [];
        $data['type_productions_list_total'] = $this->production_list_model->getTypeProductionsListsTotal($id);
        $data['production_lists'] = $this->production_list_model->getProductionListsById($id);
        $this->load->view('admin/manufactures/view_production_list', $data);
    }

    //
    public function moderation_plan()
    {
        if (!$this->perViewProductionList) {
            accessDenied();
        }

        $data['category_stages'] = $this->production_list_model->getCategoryStages();
        $data['type_productionlist'] = $this->production_list_model->getTypeProductionList();
        $data['title'] = lang('tnh_moderation_plan');
        $this->load->view('admin/production_list/moderation_plan', $data);
    }

    public function getModerationPlan()
    {
        if (!$this->perViewProductionList) {
            accessDenied($js = true);
        }

        $date_search = $this->input->post('date_search');
        $status_table = $this->input->post('status_table');

        $aColumns = [
            'tbl_production_lists_items.id as id',
            'tbl_production_lists_items.ngay_giao_hang_he_thong as ngay_giao_hang_he_thong',
            'tbl_productions_orders.reference_no as reference_no_po',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'coalesce(tbl_production_lists_items.so_luong_san_xuat, 0) as so_luong_san_xuat',
            'coalesce(tbl_production_lists_items.so_con_tren_to_in, 0) as so_con_tren_to_in',
            'coalesce(tbl_production_lists_items.to_in, 0) as to_in',
            'IF (tbl_production_lists_items.tong_thoi_gian > 0, tbl_production_lists_items.tong_thoi_gian, tbl_production_lists_items.thoi_gian_xu_ly) as tong_thoi_gian',
            'tbl_production_lists_items.ngay_bat_dau_ke_hoach as ngay_bat_dau_ke_hoach',
            'tbl_production_lists_items.ngay_ket_thuc_ke_hoach as ngay_ket_thuc_ke_hoach',
            'tbl_production_lists_items.ngay_bat_dau_thuc_te as ngay_bat_dau_thuc_te',
            'tbl_production_lists_items.ngay_ket_thuc_thuc_te as ngay_ket_thuc_thuc_te',
            'tbl_production_lists_items.so_luong_thuc_te as so_luong_thuc_te',
            'tbl_production_lists_items.status as status',
            // '"" as a14',
            // '"" as a15',
            // '"" as a16',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_production_lists';
        $where        = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_production_lists_items ON tbl_production_lists_items.production_list_id = tbl_production_lists.id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_production_lists_items.po_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_production_lists_items.item_id',
        ];

        if (!empty($date_search)) {
            $date_search = to_sql_date($date_search);
            array_push($where, ' AND tbl_production_lists.start_date <= "' . $date_search . '" AND tbl_production_lists.end_date >= "' . $date_search . '"');
        } else {
            array_push($where, ' AND tbl_production_lists.id = 0');
        }

        array_push($where, ' AND exists (
            SELECT 1
            FROM tbl_stages
            INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
            WHERE tbl_stages.id = tbl_production_lists_items.stage_id AND tbl_category_stages.type_productionlist_id = ' . $status_table . '
        )');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $dtStatusProductionsLists = $this->production_list_model->getStatusProductionsLists();

        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;

            foreach ($aColumns as $k => $v) {
                if ($v == 'id') {
                    $row[] = '<div class="text-center">' . $start . '</div>';
                } else if ($v == 'ngay_giao_hang_he_thong') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? _d($aRow[$v]) : '') . '</div>';
                } else if ($v == 'so_luong_san_xuat' || $v == 'so_con_tren_to_in' || $v == 'to_in' || $v == 'tong_thoi_gian') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? formatNumber($aRow[$v]) : '') . '</div>';
                } else if ($v == 'ngay_bat_dau_ke_hoach' || $v == 'ngay_ket_thuc_ke_hoach' || $v == 'ngay_bat_dau_thuc_te' || $v == 'ngay_ket_thuc_thuc_te') {
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updatePlan(this, \'' . $v . '\', ' . $aRow['id'] . ')" name="' . $v . '" class="form-control datetimepicker ' . $v . '" value="' . (!empty($aRow[$v]) ? date_format(date_create($aRow[$v]), 'd/m/Y H:i') : '') . '">
                    </div>';
                } else if ($v == 'so_luong_thuc_te') {
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updatePlan(this, \'' . $v . '\', ' . $aRow['id'] . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
                    </div>';
                } else if ($v == 'status') {
                    // $opStatus = '<option></option>';
                    $opStatus = '';
                    if (!empty($dtStatusProductionsLists)) {
                        foreach ($dtStatusProductionsLists as $kS => $vS) {
                            $opStatus .= '<option ' . ($vS['id'] == $aRow['status'] ? 'selected' : '') . ' value="' . $vS['id'] . '">' . $vS['code'] . '</option>';
                        }
                    }

                    $row[] = '
                        <select name="status" onchange="updatePlan(this, \'' . $v . '\', ' . $aRow['id'] . ')" class="form-control status">
                            ' . $opStatus . '
                        </select>
                    ';
                } else {
                    $row[] = $aRow[$v];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updatePlan()
    {
        $data = [];

        $data['result'] = 0;
        $data['message'] = lang('Chức năng này hiện không khả dụng');
        echo json_encode($data);
        die;

        if (!$this->perEditProductionList) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $_name = $this->input->post('_name');
        $_value = $this->input->post('_value');
        $_id = $this->input->post('_id');

        if ($_name == 'ngay_bat_dau_ke_hoach' || $_name == 'ngay_ket_thuc_ke_hoach' || $_name == 'ngay_bat_dau_thuc_te' || $_name == 'ngay_ket_thuc_thuc_te') {
            if (!empty($_value)) {
                $_value = to_sql_date($_value, true);
            } else {
                $_value = NULL;
            }
        } else if ($_name == 'so_luong_thuc_te') {
            if (!empty($_value)) {
                $_value = number_unformat($_value);
            } else {
                $_value = 0;
            }
        }

        $options = [
            $_name => $_value
        ];

        $rs = $this->production_list_model->updateProductionListsItems($_id, $options);
        if ($rs) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
    public function getmachine()
    {
        $category_stages = $this->input->post('category_stages');
        $dtMachines = $this->production_list_model->getMachines($category_stages);
        $string_option = "<option></option>";

        foreach ($dtMachines as $key => $value) {
            $string_option .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        echo $string_option;
        die;
    }
    public function getModerationPlanNew()
    {
        if (!$this->perViewProductionList) {
            accessDenied($js = true);
        }

        $date_search = $this->input->post('date_search');
        $status_table = $this->input->post('status_table');
        $date_start = $this->input->post('date_start') ? to_sql_date($this->input->post('date_start')) . ' 00:00:00' : null;
        $date_end = $this->input->post('date_end') ? to_sql_date($this->input->post('date_end')) . ' 23:59:59' : null;
        // $status_table_stages = $this->input->post('status_table_stages');
        $status_table_stages = $this->input->post('category_stages');
        $machine_id_new = $this->input->post('machine_id_new');
        $start_date_delivery = $this->input->post('start_date_delivery') ? to_sql_date($this->input->post('start_date_delivery')) : null;
        $end_date_delivery = $this->input->post('end_date_delivery') ? to_sql_date($this->input->post('end_date_delivery')) : null;

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";

        $tbPurchasesErrors = "(
            SELECT
                tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            WHERE tbl_purchase_products.is_errors = 1
            GROUP BY tbl_purchase_products.productions_orders_details_id
        ) tb_purchases_errors";

        $tbDateDelivery = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                MIN(tbl_productions_plan_details.date) as date_shipping
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
            WHERE tbl_productions_plan_items.is_preventive = 0
            GROUP BY tbl_productions_orders_items.productions_orders_id
        ) tb_date_delivery";

        $tbDateExport = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.date_active as date_active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.stage_id = '" . STAGES_MATERIAL . "' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
         ) tb_date_export";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tb_date_delivery.date_shipping as date_shipping,
                tb_date_export.date_active as date_export,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                tbl_products.quantity_child_molds as quantity_child_molds,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
                SUM(tb_purchases_errors.quantity_errors) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
            LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
            LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";
        // print_arrays($tbProductionsOrderItems);

        // $aColumns = [
        //     'tbl_production_lists_items.id as id',
        //     'tbl_production_lists_items.ngay_giao_hang_he_thong as ngay_giao_hang_he_thong',
        //     'tbl_productions_orders.reference_no as reference_no_po',
        //     'tbl_products.code as item_code',
        //     'tbl_products.name as item_name',
        //     'coalesce(tbl_production_lists_items.so_luong_san_xuat, 0) as so_luong_san_xuat',
        //     'coalesce(tbl_production_lists_items.so_con_tren_to_in, 0) as so_con_tren_to_in',
        //     'coalesce(tbl_production_lists_items.to_in, 0) as to_in',
        //     'IF (tbl_production_lists_items.tong_thoi_gian > 0, tbl_production_lists_items.tong_thoi_gian, tbl_production_lists_items.thoi_gian_xu_ly) as tong_thoi_gian',
        //     'tbl_production_lists_items.ngay_bat_dau_ke_hoach as ngay_bat_dau_ke_hoach',
        //     'tbl_production_lists_items.ngay_ket_thuc_ke_hoach as ngay_ket_thuc_ke_hoach',
        //     'tbl_production_lists_items.ngay_bat_dau_thuc_te as ngay_bat_dau_thuc_te',
        //     'tbl_production_lists_items.ngay_ket_thuc_thuc_te as ngay_ket_thuc_thuc_te',
        //     'tbl_production_lists_items.so_luong_thuc_te as so_luong_thuc_te',
        //     'tbl_production_lists_items.status as status',
        //     '"" as actions',
        // ];

        $aColumns = [
            'tbl_productions_orders.id as id',
            // 'tb_production_order_item.date_shipping as ngay_giao_hang_he_thong',
            'tbl_productions_orders.date as ngay_giao_hang_he_thong',
            'tbl_productions_orders.reference_no as reference_no_po',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tb_production_order_item.quantity as so_luong_san_xuat',
            'GROUP_CONCAT(
                DISTINCT IF(
                    tbl_category_stages.is_in = 1,
                    tbl_stages.name,
                    ""
                )
                SEPARATOR "<br>"
            ) as stage',
            'tb_production_order_item.date_shipping as date_delivery',
            '0 as so_con_tren_to_in',
            '0 as to_in',
            '0 as mat_in',
            '0 as machine_id',
            '0 as tong_thoi_gian',
            '"" as ngay_bat_dau_ke_hoach',
            '"" as ngay_ket_thuc_ke_hoach',
            '"" as ngay_bat_dau_thuc_te',
            '"" as ngay_ket_thuc_thuc_te',
            '0 as so_luong_thuc_te',
            '0 as so_gio_thuc_te',
            '"" as status',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $where        = [];
        $filter = [];

        // $join = [
        //     'INNER JOIN tbl_production_lists_items ON tbl_production_lists_items.production_list_id = tbl_production_lists.id',
        //     'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_production_lists_items.po_id',
        //     'INNER JOIN tbl_products ON tbl_products.id = tbl_production_lists_items.item_id',
        // ];

        $join = [
            'INNER JOIN ' . $tbProductionsOrderItems . ' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
            'LEFT JOIN tbl_type_print ON tbl_type_print.id = tbl_products.type_print',
            'LEFT JOIN ' . $tbProductionsPlanOrdersByOrders . ' ON tb_orders.productions_order_id = tbl_productions_orders.id',
            'LEFT JOIN ' . $tbProductionsPlanOrdersByBusinessPlan . ' ON tb_business_plan.productions_order_id = tbl_productions_orders.id',
        ];

        if (empty($date_start) || empty($date_end)) {
            array_push($where, ' AND tbl_productions_orders.id = 0');
        } else {
            array_push($where, ' AND tbl_productions_orders.date >= "' . $date_start . '" AND tbl_productions_orders.date <= "' . $date_end . '"');
        }
        // array_push($where, ' AND tbl_category_stages.type_productionlist_id = '.$status_table.'');
        array_push($where, ' AND tbl_category_stages.id = ' . $status_table_stages . ' AND tbl_category_stages.type_productionlist_id > 0');

        if (!empty($start_date_delivery)) {
            array_push($where, ' AND tb_production_order_item.date_shipping >= "' . $start_date_delivery . '"');
        }

        if (!empty($end_date_delivery)) {
            array_push($where, ' AND tb_production_order_item.date_shipping <= "' . $end_date_delivery . '"');
        }

        // if (!empty($date_search)) {
        //     $date_search = to_sql_date($date_search);
        //     array_push($where, ' AND tbl_production_lists.start_date <= "'.$date_search.'" AND tbl_production_lists.end_date >= "'.$date_search.'"');
        // } else {
        //     array_push($where, ' AND tbl_production_lists.id = 0');
        // }

        // array_push($where, ' AND exists (
        //     SELECT 1
        //     FROM tbl_stages
        //     INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
        //     WHERE tbl_stages.id = tbl_production_lists_items.stage_id AND tbl_category_stages.type_productionlist_id = '.$status_table.'
        // )');
        if (!empty($machine_id_new)) {
            $join[] = 'LEFT JOIN tbl_moderation_plan ON tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id   AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id';
            array_push($where, ' AND tbl_moderation_plan.machine_id = ' . $machine_id_new);
        }
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_production_order_item.items_id as items_id',
            'GROUP_CONCAT(DISTINCT tb_production_order_item.items_id) as items_id',
            'tb_production_order_item.plan_id as plan_id',
            'tbl_category_stages.is_in as is_in',
            'tbl_productions_orders_items_stages.stage_id as stage_id',
            'GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face) as face',
            'GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face_after) as face_after',
            'tbl_category_stages.type_productionlist_id as type_productionlist_id',
            'tb_production_order_item.versions_stage as versions_stage'
        ], 'GROUP BY tb_production_order_item.date_shipping asc, tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id', []);

        $aColumns = handlingColumns($aColumns);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $dtStatusProductionsLists = $this->production_list_model->getStatusProductionsLists();
        $dtMachines = $this->production_list_model->getMachines($status_table_stages);
        $group_id = 0;
        // print_arrays($rResult);
		$total = [
			'tong_thoi_gian' => 0,
			'tong_so_luong' => 0,
		];
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $start++;

            $productions_orders_id = $aRow['id'];
            $items_id = $aRow['items_id'];
            $type_productionlist_id = $aRow['type_productionlist_id'];
            $versions_stage = $aRow['versions_stage'];

            $flagGroup = false;
            if ($group_id != $productions_orders_id && $aRow['is_in']) {
                $group_id = $productions_orders_id;
                $flagGroup = true;
            }

            $this->db->select('
                tbl_products.code as product_code,
                tbl_products.name as product_name,
                GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "<br>") as landscape_print_size,
                GROUP_CONCAT(DISTINCT ppb_materials.number_children_size SEPARATOR "<br>") as number_children_size,
                SUM(ppb_materials.paper_exchange) as paper_exchange,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);
            $dtQuantityNew = $this->db->get()->row_array();

            $plan_id = $aRow['plan_id'];
            if ($aRow['is_in'] == 1) {
                $this->db->select('
                    (ppb_materials.item_type) as type, 
                    (ppb_materials.item_id), 
                    (ppb_materials.landscape_print_size), 
                    (ppb_materials.number_children_size), 
                    (ppb_materials.unit_parent_id), 
                    (ppb_materials.quantity_single),
                    SUM(ppb_materials.quantity) as quantity,
                    (ppb_materials.quantity_single) as quantity_single,
                ', false);
            } else {
                $this->db->select('
                    (ppb_materials.item_type) as type, 
                    (ppb_materials.item_id), 
                    (ppb_materials.landscape_print_size), 
                    (ppb_materials.number_children_size), 
                    (ppb_materials.unit_parent_id), 
                    (ppb_materials.quantity_single),
                    SUM(ppb_materials.quantity) as quantity,
                    (ppb_materials.quantity_single) as quantity_single,
                ', false);
            }
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            $this->db->where('(tbl_productions_orders_items.items_id IN (' . $items_id . '))');

            $this->db->where('(
                ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                OR exists (
                    SELECT
                        tbl_materials.id
                    FROM tbl_materials
                    INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                    WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                )
            )', false, false);

            $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
            $bom = $this->db->get()->result_array();

            $total_paper_exchange = 0;
            $total_quantity_compensation = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                    $quantity = ceil($vB['quantity']);
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                    $total_quantity_compensation += $quantity_compensation;
                }
            }
            $quantityNew = $total_paper_exchange;

            $so_con_tren_to_in = $dtQuantityNew['number_children_size'];
            $aRow['so_con_tren_to_in'] = $so_con_tren_to_in;

            $so_to_in = $quantityNew;
            $aRow['to_in'] = $so_to_in;

            $_po_id = $productions_orders_id;
            $_item_id = $items_id;
            $_type_productionlist_id = $type_productionlist_id;
            $_stage_id = $aRow['stage_id'];

            $face = array_unique(explode(',', $aRow['face']));
            $face_after = array_unique(explode(',', $aRow['face_after']));
            $countFace = 0;
            if (in_array(1, $face)) {
                $countFace++;
            }

            if (in_array(2, $face_after)) {
                $countFace++;
            }

            // $aRow['mat_in'] = $countFace;
            // $aRow['mat_in'] = $countFace;

            $this->db->select('SUM(quantity) as quantity');
            $this->db->from('tbl_productions_orders_items');
            $this->db->where('productions_orders_id', $productions_orders_id);
            $this->db->where('items_id IN (' . $items_id . ')');
            $this->db->where('object_item_type', 'business_plan');
            $quantityDp = $this->db->get()->row_array()['quantity'];


            $this->db->select('SUM(quantity) as quantity,SUM(quantity_warehoused) as quantity_warehoused');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('tbl_productions_orders_items.items_id IN (' . $items_id . ')');
            $this->db->where('object_item_type', 'orders');
            $quantityAll = $this->db->get()->row_array();

            $aRow['so_luong_san_xuat'] = (float)$quantityAll['quantity'] + (float)$quantityDp;
            $temp_item_id = explode(',', $_item_id);
            if ($_item_id) {
                $_item_id = explode(',', $_item_id);
                $_item_id = $_item_id[0];
            }

            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($moderationPlan)) {
                $aRow['so_luong_thuc_te'] = $moderationPlan['so_luong_thuc_te'];
                $aRow['tong_thoi_gian'] = $moderationPlan['tong_thoi_gian'];
                $aRow['ngay_bat_dau_ke_hoach'] = $moderationPlan['ngay_bat_dau_ke_hoach'];
                $aRow['ngay_ket_thuc_ke_hoach'] = $moderationPlan['ngay_ket_thuc_ke_hoach'];
                $aRow['ngay_bat_dau_thuc_te'] = $moderationPlan['ngay_bat_dau_thuc_te'];
                $aRow['ngay_ket_thuc_thuc_te'] = $moderationPlan['ngay_ket_thuc_thuc_te'];
                $aRow['status'] = $moderationPlan['status'];
                $aRow['machine_id'] = $moderationPlan['machine_id'];
                $aRow['mat_in'] = $moderationPlan['mat_in'];
                $aRow['so_gio_thuc_te'] = $moderationPlan['so_gio_thuc_te'];
            }

            if (empty($aRow['machine_id'])) {
                if (!empty($versions_stage)) {
                    $this->db->select('
                        tbl_product_stages_versions.machines as machines
                    ', false);
                    $this->db->from('tbl_product_stages');
                    $this->db->join('tbl_product_stages_versions', 'tbl_product_stages_versions.version_id = tbl_product_stages.id');
                    $this->db->where_in('tbl_product_stages.versions', explode(',', $versions_stage));
                    $this->db->where_in('tbl_product_stages.product_id', $temp_item_id);
                    $this->db->where('tbl_product_stages_versions.machines >', 0);
                    $this->db->where('tbl_product_stages_versions.stage_id', $_stage_id);
                    $product_versions = $this->db->get()->row_array();
                    // print_arrays($this->db->last_query());
                    if (!empty($product_versions['machines'])) {
                        $_tong_so_to_in = $aRow['to_in'];
                        $machine_id = $product_versions['machines'];
                        $machine = $this->production_list_model->getMachinesById($machine_id);
                        $tong_thoi_gian = 0;
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                        ];

                        if (!empty($machine)) {
                            // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                            $thoi_gian_canh_bai = $machine['preparation_time'];
                            $nang_suat_may = $machine['quota_productivity'];
                            $so_mat = $moderationPlan['mat_in'];
                            if ($nang_suat_may > 0) {
                                $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                            }

                            $options['tong_so_to_in'] = $_tong_so_to_in;
                            $options['tong_thoi_gian'] = $tong_thoi_gian;
                            $options['machine_id'] = $machine_id;

                            if (!empty($moderationPlan)) {
                                $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
                            } else {
                                $rs = $this->production_list_model->insertModerationPlan($options);
                            }

                            if ($rs) {
                                $aRow['machine_id'] = $machine_id;
                                $aRow['tong_thoi_gian'] = $tong_thoi_gian;
                            }
                        }
                    }
                }
            }
            //
            if (empty($aRow['mat_in'])) {
                $this->db->select('
                    MAX(tbl_product_stages_versions.face) as face,
                    MAX(tbl_product_stages_versions.face_after) as face_after
                ', false);
                $this->db->from('tbl_product_stages');
                $this->db->join('tbl_product_stages_versions', 'tbl_product_stages_versions.version_id = tbl_product_stages.id');
                $this->db->where_in('tbl_product_stages.versions', explode(',', $versions_stage));
                $this->db->where_in('tbl_product_stages.product_id', $temp_item_id);
                // $this->db->where('tbl_product_stages_versions.machines >', 0);
                $this->db->where('tbl_product_stages_versions.stage_id', $_stage_id);
                $product_versions = $this->db->get()->row_array();
                if (!empty($product_versions)) {
                    $countFace = 0;
                    if ($product_versions['face'] > 0) {
                        $countFace++;
                    }

                    if ($product_versions['face_after'] > 0) {
                        $countFace++;
                    }

                    if ($countFace > 0) {
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                        ];
                        $options['mat_in'] = $countFace;
                        

                        if (!empty($moderationPlan)) {
                            $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
                        } else {
                            $rs = $this->production_list_model->insertModerationPlan($options);
                        }
                    }
                    $aRow['mat_in'] = $countFace;
                }
            }
			
            foreach ($aColumns as $k => $v) {
                if ($v == 'id') {
                    $row[] = '<div class="text-center">' . $start . '</div>';
                } else if ($v == 'ngay_giao_hang_he_thong') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? _d($aRow[$v]) : '') . '</div>';
                } else if ($v == 'so_luong_san_xuat' || $v == 'so_con_tren_to_in' || $v == 'to_in') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? (is_numeric($aRow[$v]) ? formatNumber($aRow[$v]) : $aRow[$v]) : '') . '</div>';
                    // $row[] = '<div class="text-center">'.($aRow[$v] ? ($aRow[$v]) : '').'</div>';
                } else if ($v == 'tong_thoi_gian') {
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ')" name="' . $v . '" class="form-control number-format ' . $v . '" value="' . (!empty($aRow[$v]) ? formatNumber($aRow[$v]) : '') . '">
                    </div>';
					$total['tong_thoi_gian'] += (!empty($aRow[$v]) ? ($aRow[$v]) : '0');
                } else if ($v == 'ngay_bat_dau_ke_hoach' || $v == 'ngay_ket_thuc_ke_hoach' || $v == 'ngay_bat_dau_thuc_te' || $v == 'ngay_ket_thuc_thuc_te') {
                    // $row[] = '<div>
                    //     <input type="text" style="width: 150px;" onchange="updatePlan(this, \''.$v.'\', '.$aRow['id'].')" name="'.$v.'" class="form-control datetimepicker '.$v.'" value="'.(!empty($aRow[$v]) ? date_format(date_create($aRow[$v]), 'd/m/Y H:i') : '').'">
                    // </div>';

                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ')" name="' . $v . '" class="form-control datetimepicker ' . $v . '" value="' . (!empty($aRow[$v]) ? date_format(date_create($aRow[$v]), 'd/m/Y H:i') : '') . '">
                    </div>';
                } else if ($v == 'so_luong_thuc_te') {
                    // $row[] = '<div>
                    //     <input type="text" style="width: 150px;" onchange="updatePlan(this, \''.$v.'\', '.$aRow['id'].')" name="'.$v.'" class="form-control '.$v.'" value="'.formatNumber($aRow[$v]).'">
                    // </div>';
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
                    </div>';
					$total['tong_so_luong'] += (!empty($aRow[$v]) ? ($aRow[$v]) : '0');
                } else if ($v == 'status') {
                    // $opStatus = '<option></option>';
                    $opStatus = '';
                    if (!empty($dtStatusProductionsLists)) {
                        foreach ($dtStatusProductionsLists as $kS => $vS) {
                            $opStatus .= '<option ' . ($vS['id'] == $aRow['status'] ? 'selected' : '') . ' value="' . $vS['id'] . '">' . $vS['code'] . '</option>';
                        }
                    }

                    // $row[] = '
                    //     <select name="status" onchange="updatePlan(this, \''.$v.'\', '.$aRow['id'].')" class="form-control status">
                    //         '.$opStatus.'
                    //     </select>
                    // ';
                    $row[] = '
                        <select name="status" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ')" class="form-control status">
                            ' . $opStatus . '
                        </select>
                    ';
                } else if ($v == 'machine_id') {

                    $opMachines = '<option value=""></option>';
                    if (!empty($dtMachines)) {
                        foreach ($dtMachines as $kM => $vM) {
                            $opMachines .= '<option data-preparation_time="' . $vM['preparation_time'] . '" ' . ($vM['id'] == $aRow['machine_id'] ? 'selected' : '') . ' value="' . $vM['id'] . '">' . $vM['name'] . '</option>';
                        }
                    }

                    $row[] = '
                        <select style="width: 150px;" name="machine_id" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', ' . $aRow['to_in'] . ')" data-placeholder="Máy móc" class="machine_id">
                            ' . $opMachines . '
                        </select>
                    ';
                } else if ($v == 'mat_in') {
                    $row[] = '<div class="">
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', ' . $aRow['to_in'] . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
                    </div>';
                } else if ($v == 'date_delivery') {
                    $row[] = !empty($aRow[$v]) ? _d($aRow[$v]) : '';
                } else if ($v == 'so_gio_thuc_te') {
                    $tong_thoi_gian = $aRow['tong_thoi_gian'];
                    $so_gio_thuc_te = $aRow['so_gio_thuc_te'];
                    $str_status = '';
                    if (empty($so_gio_thuc_te)) {
                        $str_status = '';
                    } else if ($so_gio_thuc_te <= $tong_thoi_gian) {
                        $str_status = '(Đạt)';
                    } else {
                        $str_status = '(Không đạt)';
                    }

                    $row[] = '<div class="text-center so_gio_thuc_te">' . formatNumber($aRow[$v]) . ''.$str_status.'</div>';
                } else {
                    $row[] = $aRow[$v];
                }
            }
            $output['aaData'][] = $row;
        }
		$output['total'] = !empty($total) ? $total : [];
        echo json_encode($output);
    }

    public function updateModerationPlan()
    {
        $data = [];
        $optionsUp = [];
        if (!$this->perEditProductionList) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $_name = $this->input->post('_name');
        $_value = $this->input->post('_value');
        $_po_id = $this->input->post('_po_id');
        $_item_id = $this->input->post('_item_id');
        $_type_productionlist_id = $this->input->post('_type_productionlist_id');
        $_stage_id = $this->input->post('_stage_id');

        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

        if ($_name == 'ngay_bat_dau_ke_hoach' || $_name == 'ngay_ket_thuc_ke_hoach' || $_name == 'ngay_bat_dau_thuc_te' || $_name == 'ngay_ket_thuc_thuc_te') {
            if (!empty($_value)) {
                $_value = to_sql_date($_value, true);
            } else {
                $_value = NULL;
            }
        } else if ($_name == 'so_luong_thuc_te' || $_name == 'tong_thoi_gian' || $_name == 'mat_in') {
            if (!empty($_value)) {
                $_value = number_unformat($_value);
            } else {
                $_value = 0;
            }
        } else if ($_name == 'machine_id') {
        }

        $options = [
            $_name => $_value,
            'po_id' => $_po_id,
            'item_id' => $_item_id,
            'type_productionlist_id' => $_type_productionlist_id,
            'stage_id' => $_stage_id,
            'updated_by' => get_staff_user_id(),
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        if (!empty($moderationPlan)) {
            $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
        } else {
            $rs = $this->production_list_model->insertModerationPlan($options);
        }

        if ($_name == 'machine_id') {
            $_tong_so_to_in = number_unformat($this->input->post('_tong_so_to_in'));
            $machine_id = $_value;
            $machine = $this->production_list_model->getMachinesById($machine_id);
            // $product = $this->products_model->rowProduct($_item_id);
            $tong_thoi_gian = 0;
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($machine)) {
                // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                $thoi_gian_canh_bai = $machine['preparation_time'];
                $nang_suat_may = $machine['quota_productivity'];
                $so_mat = $moderationPlan['mat_in'];
                if ($nang_suat_may > 0) {
                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                }
            }

            $optionsUp['tong_so_to_in'] = $_tong_so_to_in;
            $optionsUp['tong_thoi_gian'] = $tong_thoi_gian;
            $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $optionsUp);
        } else if ($_name == 'mat_in') {
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($moderationPlan)) {
                // $_tong_so_to_in = number_unformat($moderationPlan['tong_so_to_in']);
                $_tong_so_to_in = number_unformat($this->input->post('_tong_so_to_in'));
                $machine_id = $moderationPlan['machine_id'];
                $machine = $this->production_list_model->getMachinesById($machine_id);

                $tong_thoi_gian = 0;
                if (!empty($machine)) {
                    // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                    $thoi_gian_canh_bai = $machine['preparation_time'];
                    $nang_suat_may = $machine['quota_productivity'];
                    $so_mat = $moderationPlan['mat_in'];
                    if ($nang_suat_may > 0) {
                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                        // print_arrays($tong_thoi_gian, '<br>', $_tong_so_to_in, '<br>', $so_mat, '<br>', $nang_suat_may, '<br>', $thoi_gian_canh_bai);
                    }
                }
            }

            $optionsUp['tong_so_to_in'] = $_tong_so_to_in;
            $optionsUp['tong_thoi_gian'] = $tong_thoi_gian;
            // print_arrays($optionsUp)
            $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $optionsUp);
        } else if ($_name == 'ngay_bat_dau_thuc_te' || $_name == 'ngay_ket_thuc_thuc_te') {
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            $ngay_bat_dau_thuc_te = $moderationPlan['ngay_bat_dau_thuc_te'];
            $ngay_ket_thuc_thuc_te = $moderationPlan['ngay_ket_thuc_thuc_te'];
            $so_gio_thuc_te = 0;
            if (!empty($ngay_bat_dau_thuc_te) && !empty($ngay_ket_thuc_thuc_te)) {
                $time1 = strtotime($ngay_bat_dau_thuc_te);
                $time2 = strtotime($ngay_ket_thuc_thuc_te);
                $hours = ($time2 - $time1) / 3600;
                $hours = number_format($hours, 3);
                $so_gio_thuc_te = $hours;
            }
            $optionsUp['so_gio_thuc_te'] = $so_gio_thuc_te;
            $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $optionsUp);
        }

        if ($rs) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function export_excel_moderation_plan()
    {
        $date_start = $this->input->post('date_start') ? to_sql_date($this->input->post('date_start')) . ' 00:00:00' : null;
        $date_start_cel = $this->input->post('date_start') ? to_sql_date($this->input->post('date_start')) : null;
        $date_end = $this->input->post('date_end') ? to_sql_date($this->input->post('date_end')) . ' 23:59:59' : null;
        $date_end_cel = $this->input->post('date_end') ? to_sql_date($this->input->post('date_end')) : null;
        // $status_table_stages = $this->input->post('status_table_stages');
        $status_table_stages = $this->input->post('category_stages');
        $machine_id_new = $this->input->post('machine_id_new');
        $start_date_delivery = $this->input->post('start_date_delivery') ? to_sql_date($this->input->post('start_date_delivery')) : null;
        $end_date_delivery = $this->input->post('end_date_delivery') ? to_sql_date($this->input->post('end_date_delivery')) : null;

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";

        $tbPurchasesErrors = "(
            SELECT
                tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            WHERE tbl_purchase_products.is_errors = 1
            GROUP BY tbl_purchase_products.productions_orders_details_id
        ) tb_purchases_errors";

        $tbDateDelivery = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                MIN(tbl_productions_plan_details.date) as date_shipping
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
            WHERE tbl_productions_plan_items.is_preventive = 0
            GROUP BY tbl_productions_orders_items.productions_orders_id
        ) tb_date_delivery";

        $tbDateExport = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.date_active as date_active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.stage_id = '" . STAGES_MATERIAL . "' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
         ) tb_date_export";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tb_date_delivery.date_shipping as date_shipping,
                tb_date_export.date_active as date_export,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                tbl_products.quantity_child_molds as quantity_child_molds,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
                SUM(tb_purchases_errors.quantity_errors) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
            LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
            LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $this->db->select('
            tbl_productions_orders.id as id,
            tbl_productions_orders.date as ngay_giao_hang_he_thong,
            tbl_productions_orders.reference_no as reference_no_po,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tb_production_order_item.quantity as so_luong_san_xuat,
            tb_production_order_item.items_id as items_id,
            GROUP_CONCAT(DISTINCT tb_production_order_item.items_id) as items_id,
            tb_production_order_item.plan_id as plan_id,
            tbl_category_stages.is_in as is_in,
            tbl_productions_orders_items_stages.stage_id as stage_id,
            GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face) as face,
            GROUP_CONCAT(DISTINCT tbl_productions_orders_items_stages.face_after) as face_after,
            tbl_category_stages.type_productionlist_id as type_productionlist_id,
            GROUP_CONCAT(
                DISTINCT IF(
                    tbl_category_stages.is_in = 1,
                    tbl_stages.name,
                    ""
                )
                SEPARATOR "<br>"
            ) as stage,
            tb_production_order_item.date_shipping as date_delivery,
            tb_production_order_item.versions_stage as versions_stage,
        ');
        $this->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'inner');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        $this->db->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left');
        $this->db->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left');

        if (!empty($date_start)) {
            $this->db->where('tbl_productions_orders.date >= ', $date_start);
        }
        if (!empty($date_end)) {
            $this->db->where('tbl_productions_orders.date <= ', $date_end);
        }

        if (!empty($start_date_delivery)) {
            $this->db->where('tb_production_order_item.date_shipping >= ', $start_date_delivery);
        }
        if (!empty($end_date_delivery)) {
            $this->db->where('tb_production_order_item.date_shipping <= ', $end_date_delivery);
        }

        if (!empty($status_table_stages)) {
            $this->db->where('tbl_category_stages.id', $status_table_stages);
            $this->db->where('tbl_category_stages.type_productionlist_id > 0');
        }
        if (!empty($machine_id_new)) {
            $this->db->join('tbl_moderation_plan', 'tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id   AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id', 'inner');
            $this->db->where('tbl_moderation_plan.machine_id', $machine_id_new);
        }
        $this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id');
        // $this->db->limit(10);
        $result = $this->db->get('tbl_productions_orders')->result_array();

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $cloumns_excel = cloumns_excel();
        $colName = [
            'id' => 'STT',
            'ngay_giao_hang_he_thong' => 'Ngày mở lệnh',
            'reference_no_po' => 'Mã LSX',
            'item_code' => 'Mã sản phẩm',
            'item_name' => 'Tên sản phẩm',
            'so_luong_san_xuat' => 'Tổng số con',
            'stage' => 'Công đoạn in',
            'date_delivery' => 'Ngày giao hàng dự kiến',
            'so_con_tren_to_in' => 'Tờ',
            'to_in' => 'Tờ in',
            'mat_in' => 'Số mặt in',
            'machine_id' => 'Máy móc',
            'tong_thoi_gian' => 'H',
            'ngay_bat_dau_ke_hoach' => 'Bắt đầu (Ngày - H)',
            'ngay_ket_thuc_ke_hoach' => 'Kết thúc (Ngày - H)',
            'ngay_bat_dau_thuc_te' => 'Bắt đầu (Ngày - H)',
            'ngay_ket_thuc_thuc_te' => 'Kết thúc (Ngày - H)',
            'so_luong_thuc_te' => 'Số lượng thực tế',
            'so_gio_thuc_te' => 'Số giờ thực tế',
            // 'so_luong_thuc_te' => 'Trạng thái',
            'status' => 'Trạng thái',
            'time' => 'Thời gian dùng máy',
            'note' => 'Ghi chú',
            'sign' => 'Ký tên',
        ];
        $rowspan = [
            'id',
            'ngay_giao_hang_he_thong',
            'reference_no_po',
            'item_code',
            'item_name',
            'so_luong_san_xuat',
            'stage',
            'date_delivery',
            'mat_in',
            'machine_id',
            'status',
            'time',
            'note',
            'sign',
        ];
        $colspan = [
            'Số con' => ['so_con_tren_to_in'],
            'Tổng số' => ['to_in'],
            'Tổng TG dự kiến' => ['tong_thoi_gian'],
            'Kế hoạch' => ['ngay_bat_dau_ke_hoach', 'ngay_ket_thuc_ke_hoach'],
            'Thực tế' => ['ngay_bat_dau_thuc_te', 'so_gio_thuc_te'],
        ];
        $aColumns = array_keys($colName);

        $styleTitle = [
            // 'borders' => array(
            //     'allborders' => array(
            //         'style' => PHPExcel_Style_Border::BORDER_THIN
            //     )
            // ),
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
        $styleText = [
            // 'borders' => array(
            //     'allborders' => array(
            //         'style' => PHPExcel_Style_Border::BORDER_THIN
            //     )
            // ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            // 'alignment' => array(
            //     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            //     'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            // )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 14,
                'name' => 'Times New Roman'
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
        ];
        
        $stylePlain = [
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
        ];


        $worksheet = $objPHPExcel->getActiveSheet();
        $excelRowNum = 1;
        $maxCol = count($colName)-1;
        $this->db->select('tbl_category_stages.name');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_category_stages.id', $status_table_stages);
        $category_stages = $this->db->get()->row_array();
        if (!empty($category_stages['name'])) {
            $category_stages = $category_stages['name'];
        }

        $logoUrl = get_upload_path_by_type('company') . get_option('company_logo');
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Sample Image');
        $objDrawing->setDescription('Image');
        $objDrawing->setPath($logoUrl);
        $objDrawing->setCoordinates('B'.$excelRowNum); // Vị trí cột và dòng để đặt hình ảnh
        $objDrawing->setWidth(35); // Đặt chiều rộng (đơn vị pixel)
        $objDrawing->setHeight(70); // Đặt chiều cao (đơn vị pixel)
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // $objPHPExcel->getActiveSheet()->getColumnDimension($columnIndex)->setWidth($columnWidth);
        $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(60);
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, 'KẾ HOẠCH ĐIỀU ĐỘ CÔNG ĐOẠN '.$category_stages)->getStyle('A' . $excelRowNum)->applyFromArray($styleTitle);
        
        $excelRowNum = 2;
        $machine = get_table_where('tbl_machines', ['id'=>$machine_id_new], '', 'row_array', '', 'name');
        $machine = (!empty($machine['name']) ? $machine['name'] : '');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $excelRowNum, 'Tên thiết bị: '.$machine)->getStyle('D' . $excelRowNum)->applyFromArray($styleText);

        // $dateCell = 'Từ ngày: '._d($start_date_delivery) . '                                Đến ngày: '._d($end_date_delivery);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $excelRowNum, 'Từ ngày: '._d($date_start_cel))->getStyle('E' . $excelRowNum)->applyFromArray($styleText);

        $objPHPExcel->getActiveSheet()->setCellValue('G' . $excelRowNum, 'Đến ngày: '._d($date_end_cel))->getStyle('G' . $excelRowNum)->applyFromArray($styleText);
        
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $excelRowNum, 'TỔNG THỜI GIAN DỰ KIẾN')->getStyle('K' . $excelRowNum)->applyFromArray($styleHeader);
        // $objPHPExcel->getActiveSheet()->mergeCells('L1:M1');
        // $objPHPExcel->getActiveSheet()->setCellValue('L' . $excelRowNum, 'KẾ HOẠCH')->getStyle('L' . $excelRowNum)->applyFromArray($styleHeader);
        // $objPHPExcel->getActiveSheet()->mergeCells('N1:O1');
        // $objPHPExcel->getActiveSheet()->setCellValue('N' . $excelRowNum, 'THỰC TẾ')->getStyle('N' . $excelRowNum)->applyFromArray($styleHeader);

        $excelRowNum = 4;
        
        foreach ($aColumns as $key => $value) {
            // $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
            if (in_array($value, $rowspan)) {
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[$key].($excelRowNum-1).':'.$cloumns_excel[$key].$excelRowNum);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . ($excelRowNum-1), ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum-1).':'.$cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            }
            $cellWidth = strlen($colName[$value]) * 1.7;
            $columnDimension = $worksheet->getColumnDimension($cloumns_excel[$key]);
            $columnDimension->setWidth($cellWidth);
        }

        foreach ($colspan as $hTitle => $col) {
            $startIndex = array_search($col[0], $aColumns);
            $endIndex = (!empty($col[1]) ? array_search($col[1], $aColumns) : $startIndex);
            if ($startIndex == $endIndex) {
                $cell = $cloumns_excel[$startIndex].($excelRowNum-1);
            } else {
                $cell = $cloumns_excel[$startIndex].($excelRowNum-1).':'.$cloumns_excel[$endIndex].($excelRowNum-1);
                $objPHPExcel->getActiveSheet()->mergeCells($cell);
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$startIndex] . ($excelRowNum-1), $hTitle)->getStyle($cell)->applyFromArray($styleHeader);
        }

        $dtStatusProductionsLists = $this->production_list_model->getStatusProductionsLists();
        $dtMachines = $this->production_list_model->getMachines();
        $group_id = 0;
        $excelRowNum = 5;
        foreach ($result as $key => $aRow) {
            $row = [];
            // $start++;

            $productions_orders_id = $aRow['id'];
            $items_id = $aRow['items_id'];
            $type_productionlist_id = $aRow['type_productionlist_id'];
            $versions_stage = $aRow['versions_stage'];

            if ($group_id != $productions_orders_id && $aRow['is_in']) {
                $group_id = $productions_orders_id;
            }

            $this->db->select('
                tbl_products.code as product_code,
                tbl_products.name as product_name,
                GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "\n") as landscape_print_size,
                GROUP_CONCAT(DISTINCT ppb_materials.number_children_size SEPARATOR "\n") as number_children_size,
                SUM(ppb_materials.paper_exchange) as paper_exchange,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);
            $dtQuantityNew = $this->db->get()->row_array();

            $plan_id = $aRow['plan_id'];
            if ($aRow['is_in'] == 1) {
                $this->db->select('
                    (ppb_materials.item_type) as type, 
                    (ppb_materials.item_id), 
                    (ppb_materials.landscape_print_size), 
                    (ppb_materials.number_children_size), 
                    (ppb_materials.unit_parent_id), 
                    (ppb_materials.quantity_single),
                    SUM(ppb_materials.quantity) as quantity,
                    (ppb_materials.quantity_single) as quantity_single,
                ', false);
            } else {
                $this->db->select('
                    (ppb_materials.item_type) as type, 
                    (ppb_materials.item_id), 
                    (ppb_materials.landscape_print_size), 
                    (ppb_materials.number_children_size), 
                    (ppb_materials.unit_parent_id), 
                    (ppb_materials.quantity_single),
                    SUM(ppb_materials.quantity) as quantity,
                    (ppb_materials.quantity_single) as quantity_single,
                ', false);
            }
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            $this->db->where('(tbl_productions_orders_items.items_id IN (' . $items_id . '))');

            $this->db->where('(
                ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                OR exists (
                    SELECT
                        tbl_materials.id
                    FROM tbl_materials
                    INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                    WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                )
            )', false, false);

            $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
            $bom = $this->db->get()->result_array();

            $total_paper_exchange = 0;
            $total_quantity_compensation = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                    $quantity = ceil($vB['quantity']);
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                    $total_quantity_compensation += $quantity_compensation;
                }
            }
            $quantityNew = $total_paper_exchange;

            $so_con_tren_to_in = $dtQuantityNew['number_children_size'];
            $aRow['so_con_tren_to_in'] = $so_con_tren_to_in;

            $so_to_in = $quantityNew;
            $aRow['to_in'] = $so_to_in;

            $_po_id = $productions_orders_id;
            $_item_id = $items_id;
            $_type_productionlist_id = $type_productionlist_id;
            $_stage_id = $aRow['stage_id'];

            $face = array_unique(explode(',', $aRow['face']));
            $face_after = array_unique(explode(',', $aRow['face_after']));
            $countFace = 0;
            if (in_array(1, $face)) {
                $countFace++;
            }

            if (in_array(2, $face_after)) {
                $countFace++;
            }

            // $aRow['mat_in'] = $countFace;
            // $aRow['mat_in'] = $countFace;

            $this->db->select('SUM(quantity) as quantity');
            $this->db->from('tbl_productions_orders_items');
            $this->db->where('productions_orders_id', $productions_orders_id);
            $this->db->where('items_id IN (' . $items_id . ')');
            $this->db->where('object_item_type', 'business_plan');
            $quantityDp = $this->db->get()->row_array()['quantity'];


            $this->db->select('SUM(quantity) as quantity,SUM(quantity_warehoused) as quantity_warehoused');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('tbl_productions_orders_items.items_id IN (' . $items_id . ')');
            $this->db->where('object_item_type', 'orders');
            $quantityAll = $this->db->get()->row_array();

            $aRow['so_luong_san_xuat'] = (float)$quantityAll['quantity'] + (float)$quantityDp;
            $temp_item_id = explode(',', $_item_id);
            if ($_item_id) {
                $_item_id = explode(',', $_item_id);
                $_item_id = $_item_id[0];
            }

            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($moderationPlan)) {
                $aRow['so_gio_thuc_te'] = $moderationPlan['so_gio_thuc_te'];
                $aRow['so_luong_thuc_te'] = $moderationPlan['so_luong_thuc_te'];
                $aRow['tong_thoi_gian'] = $moderationPlan['tong_thoi_gian'];
                $aRow['ngay_bat_dau_ke_hoach'] = $moderationPlan['ngay_bat_dau_ke_hoach'];
                $aRow['ngay_ket_thuc_ke_hoach'] = $moderationPlan['ngay_ket_thuc_ke_hoach'];
                $aRow['ngay_bat_dau_thuc_te'] = $moderationPlan['ngay_bat_dau_thuc_te'];
                $aRow['ngay_ket_thuc_thuc_te'] = $moderationPlan['ngay_ket_thuc_thuc_te'];
                $aRow['status'] = $moderationPlan['status'];
                $aRow['machine_id'] = $moderationPlan['machine_id'];
                $aRow['mat_in'] = $moderationPlan['mat_in'];
            } else {
                $aRow['so_gio_thuc_te'] = 0;
                $aRow['so_luong_thuc_te'] = 0;
                $aRow['tong_thoi_gian'] = 0;
                $aRow['ngay_bat_dau_ke_hoach'] = '';
                $aRow['ngay_ket_thuc_ke_hoach'] = '';
                $aRow['ngay_bat_dau_thuc_te'] = '';
                $aRow['ngay_ket_thuc_thuc_te'] = '';
                $aRow['status'] = '';
                $aRow['machine_id'] = 0;
                $aRow['mat_in'] = 0;
            }

            if (empty($aRow['machine_id'])) {
                if (!empty($versions_stage)) {
                    $this->db->select('
                        tbl_product_stages_versions.machines as machines
                    ', false);
                    $this->db->from('tbl_product_stages');
                    $this->db->join('tbl_product_stages_versions', 'tbl_product_stages_versions.version_id = tbl_product_stages.id');
                    $this->db->where_in('tbl_product_stages.versions', explode(',', $versions_stage));
                    $this->db->where_in('tbl_product_stages.product_id', $temp_item_id);
                    $this->db->where('tbl_product_stages_versions.machines >', 0);
                    $this->db->where('tbl_product_stages_versions.stage_id', $_stage_id);
                    $product_versions = $this->db->get()->row_array();
                    // print_arrays($this->db->last_query());
                    if (!empty($product_versions['machines'])) {
                        $_tong_so_to_in = $aRow['to_in'];
                        $machine_id = $product_versions['machines'];
                        $machine = $this->production_list_model->getMachinesById($machine_id);
                        $tong_thoi_gian = 0;
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                        ];

                        if (!empty($machine)) {
                            // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                            $thoi_gian_canh_bai = $machine['preparation_time'];
                            $nang_suat_may = $machine['quota_productivity'];
                            $so_mat = $moderationPlan['mat_in'];
                            if ($nang_suat_may > 0) {
                                $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                            }

                            $options['tong_so_to_in'] = $_tong_so_to_in;
                            $options['tong_thoi_gian'] = $tong_thoi_gian;
                            $options['machine_id'] = $machine_id;

                            if (!empty($moderationPlan)) {
                                $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
                            } else {
                                $rs = $this->production_list_model->insertModerationPlan($options);
                            }

                            if ($rs) {
                                $aRow['machine_id'] = $machine_id;
                                $aRow['tong_thoi_gian'] = $tong_thoi_gian;
                            }
                        }
                    }
                }
            }

            if (empty($aRow['mat_in'])) {
                $this->db->select('
                    MAX(tbl_product_stages_versions.face) as face,
                    MAX(tbl_product_stages_versions.face_after) as face_after
                ', false);
                $this->db->from('tbl_product_stages');
                $this->db->join('tbl_product_stages_versions', 'tbl_product_stages_versions.version_id = tbl_product_stages.id');
                $this->db->where_in('tbl_product_stages.versions', explode(',', $versions_stage));
                $this->db->where_in('tbl_product_stages.product_id', $temp_item_id);
                // $this->db->where('tbl_product_stages_versions.machines >', 0);
                $this->db->where('tbl_product_stages_versions.stage_id', $_stage_id);
                $product_versions = $this->db->get()->row_array();
                if (!empty($product_versions)) {
                    $countFace = 0;
                    if ($product_versions['face'] > 0) {
                        $countFace++;
                    }

                    if ($product_versions['face_after'] > 0) {
                        $countFace++;
                    }

                    if ($countFace > 0) {
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                        ];
                        $options['mat_in'] = $countFace;

                        if (!empty($moderationPlan)) {
                            $_tong_so_to_in = $aRow['to_in'];
                            $machine_id = $moderationPlan['machine_id'];
                            $machine = $this->production_list_model->getMachinesById($machine_id);
                            if (!empty($machine)) {
                                $thoi_gian_canh_bai = $machine['preparation_time'];
                                $nang_suat_may = $machine['quota_productivity'];
                                $so_mat = $options['mat_in'];
                                $tong_thoi_gian = 0;
                                if ($nang_suat_may > 0) {
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                }

                                $options['tong_thoi_gian'] = $tong_thoi_gian;
                                $aRow['tong_thoi_gian'] = $tong_thoi_gian;
                            }
                        }


                        if (!empty($moderationPlan)) {
                            $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
                        } else {
                            $rs = $this->production_list_model->insertModerationPlan($options);
                        }
                    }
                    $aRow['mat_in'] = $countFace;
                }
            }

            foreach ($aColumns as $k => $v) {
                if ($v == 'id') {
                    $cellValue = (++$key);
                } else if ($v == 'ngay_giao_hang_he_thong') {
                    $cellValue = ($aRow[$v] ? _d($aRow[$v]) : '');
                } else if ($v == 'so_con_tren_to_in') {
                    $cellValue = (isset($aRow[$v]) ? $aRow[$v] : '');
                    $objPHPExcel->getActiveSheet()->getStyle($cloumns_excel[$k].$excelRowNum)->getAlignment()->setWrapText(true);
                } else if ($v == 'status') {
                    $StatusProductionsCode = '';
                    if (!empty($dtStatusProductionsLists)) {
                        if (empty($aRow['status'])) {
                            $StatusProductionsCode = $dtStatusProductionsLists[0]['code'];
                        } else {
                            foreach ($dtStatusProductionsLists as $kS => $vS) {
                                if ($vS['id'] == $aRow['status']) {
                                    $StatusProductionsCode = $vS['code'];
                                }
                            }
                        }
                    }
                    $cellValue = $StatusProductionsCode;
                } else if ($v == 'machine_id') {
                    $machineName = '';
                    if (!empty($dtMachines)) {
                        foreach ($dtMachines as $kM => $vM) {
                            if ($vM['id'] == $aRow[$v]) {
                                $machineName = $vM['name'];
                            }
                        }
                    }
                    $cellValue = $machineName;
                } else if ($v == 'date_delivery' || $v == 'ngay_bat_dau_ke_hoach' || $v == 'ngay_ket_thuc_ke_hoach' || $v == 'ngay_bat_dau_thuc_te' || $v == 'ngay_ket_thuc_thuc_te') {
                    $cellValue = (isset($aRow[$v]) ? _d($aRow[$v]) : '');
                } else {
                    $cellValue = (isset($aRow[$v]) ? $aRow[$v] : '');
                }
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$k] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$k] . $excelRowNum)->applyFromArray($stylePlain);
            }

            $excelRowNum++;
        }

        $filename = 'moderation_plan' . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
        echo '<pre>';
        var_dump($result);
    }
}
