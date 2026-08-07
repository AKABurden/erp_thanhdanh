<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Production_list extends AdminController
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
        $this->perUpdateProductionList = has_permission('production_list', '', 'approve');
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
        // echo 'Module này hiện không còn sử dụng';
        // die;
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
    public function moderation_plan_bk()
    {
        if (!$this->perViewProductionList) {
            accessDenied();
        }
        $data['group'] = $this->input->get('group');
        $data['category_stages'] = $this->production_list_model->getCategoryStages();
        $data['type_productionlist'] = $this->production_list_model->getTypeProductionList();
        $type_title = '';
        $this->db->select('tbl_category_stages.*');
        $this->db->where('tbl_category_stages.id', $data['group']);
        $this->db->where('tbl_category_stages.type_use', 0);
        $category_stages = $this->db->get('tbl_category_stages')->row_array();
        if (!empty($category_stages)) {
            $type_title = $category_stages['name'];
        }
        $data['title'] = lang('tnh_moderation_plan') . ' (' . $type_title.')';
        $this->load->view('admin/production_list/moderation_plan', $data);
    }

    public function moderation_plan()
    {
        if (!$this->perViewProductionList) {
            accessDenied();
        }
        // error_reporting(-1);
		// ini_set('display_errors', 1);
        $data['group'] = $this->input->get('group');
        $data['category_stages'] = $this->production_list_model->getCategoryStages();
        $data['type_productionlist'] = $this->production_list_model->getTypeProductionList();
        $type_title = '';

        $this->db->select('tbl_category_stages.*');
        $this->db->where('tbl_category_stages.id', $data['group']);
        $this->db->where('tbl_category_stages.type_use', 0);
        $category_stages = $this->db->get('tbl_category_stages')->row_array();
        if (!empty($category_stages)) {
            $type_title = $category_stages['name'];
        }
        $data['title'] = lang('tnh_moderation_plan') . ' (' . $type_title.')';
        $this->load->view('admin/production_list/moderation_plan_new', $data);
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
        $start_date_bd_kh = $this->input->post('start_date_bd_kh') ? to_sql_date($this->input->post('start_date_bd_kh')) . ' 00:00:00' : null;
        $end_date_bd_kh = $this->input->post('end_date_bd_kh') ? to_sql_date($this->input->post('end_date_bd_kh')) . ' 23:59:59' : null;
        $productions_orders_search = $this->input->post('productions_orders_search');
        $start_date_reality = $this->input->post('start_date_reality') ? to_sql_date($this->input->post('start_date_reality')) . ' 00:00:00' : null;
        $end_date_reality = $this->input->post('end_date_reality') ? to_sql_date($this->input->post('end_date_reality')) . ' 23:59:59' : null;
        $start_date_reality_kt = $this->input->post('start_date_reality_kt') ? to_sql_date($this->input->post('start_date_reality_kt')) . ' 00:00:00' : null;
        $end_date_reality_kt = $this->input->post('end_date_reality_kt') ? to_sql_date($this->input->post('end_date_reality_kt')) . ' 23:59:59' : null;

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

        $wherePOI = '';
        if (!empty($productions_orders_search)) {
            $wherePOI .= ' AND tbl_productions_orders_items.productions_orders_id = ' . $productions_orders_search . '';
        }

        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tb_date_delivery.date_shipping as date_shipping,
        //         tb_date_export.date_active as date_export,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         tbl_products.quantity_child_molds as quantity_child_molds,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
        //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors
        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
        //     LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
        //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
        //     LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
        //     WHERE 1 $wherePOI
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tb_date_delivery.date_shipping as date_shipping,
                '' as date_export,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                tbl_products.quantity_child_molds as quantity_child_molds,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
                SUM(0) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
            WHERE 1 $wherePOI
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
            '0 as so_lan_tren_mat',
            '0 as so_lan_van_hanh',
            '0 as so_duong_dao_cat',
            '0 as machine_id',
            '0 as nang_suat_nhan_vien',
            '0 as tong_thoi_gian',
            '"" as ngay_bat_dau_ke_hoach',
            '"" as ngay_ket_thuc_ke_hoach',
            '"" as ngay_bat_dau_ngung_may',
            '"" as ngay_ket_thuc_ngung_may',
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
            // 'LEFT JOIN ' . $tbProductionsPlanOrdersByOrders . ' ON tb_orders.productions_order_id = tbl_productions_orders.id',
            // 'LEFT JOIN ' . $tbProductionsPlanOrdersByBusinessPlan . ' ON tb_business_plan.productions_order_id = tbl_productions_orders.id',
        ];

        array_push($where, ' AND exists (
            SELECT 1
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders_items_stages.productions_orders_id AND tbl_productions_orders_items_stages.stage_id = ' . STAGES_MATERIAL . ' AND tbl_productions_orders_items_stages.active = 1
        ) ');

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
            // $join[] = 'LEFT JOIN tbl_moderation_plan ON tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id   AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id';
            // array_push($where, ' AND tbl_moderation_plan.machine_id = ' . $machine_id_new);

            array_push($where, ' AND exists (
                SELECT 1
                FROM tbl_moderation_plan
                WHERE tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id  AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id AND tbl_moderation_plan.machine_id = ' . $machine_id_new . '
            ) ');
        }

        $whereDateBD = '';
        if (!empty($start_date_bd_kh)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_ke_hoach >= "' . $start_date_bd_kh . '"';
        }

        if (!empty($end_date_bd_kh)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_ke_hoach <= "' . $end_date_bd_kh . '"';
        }

        if (!empty($start_date_reality)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_thuc_te >= "' . $start_date_reality . '"';
        }

        if (!empty($end_date_reality)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_thuc_te <= "' . $end_date_reality . '"';
        }

        if (!empty($start_date_reality_kt)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_ket_thuc_thuc_te >= "' . $start_date_reality_kt . '"';
        }

        if (!empty($end_date_reality_kt)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_ket_thuc_thuc_te <= "' . $end_date_reality_kt . '"';
        }

        if (!empty($whereDateBD)) {
            array_push($where, ' AND exists (
                SELECT 1
                FROM tbl_moderation_plan
                WHERE tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id   AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id ' . $whereDateBD . ' 
            ) ');
        }

        if (!empty($productions_orders_search)) {
            // array_push($where, ' AND tbl_productions_orders.id = ' . $productions_orders_search . '');
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
            'tb_production_order_item.versions_stage as versions_stage',
            'tbl_productions_orders_items_stages.number_face as number_face',
            'tbl_productions_orders_items_stages.number_operations as number_operations',
            'tbl_productions_orders_items_stages.number_cutting as number_cutting',
            'tbl_productions_orders_items_stages.quota_time_f1 as quota_time_f1',
            'tbl_productions_orders_items_stages.quota_time_f2 as quota_time_f2',
        ], 'GROUP BY tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id ORDER BY tb_production_order_item.date_shipping ASC', []);

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

            $tbProductionsOrderItemsCs = "(
                SELECT
                    GROUP_CONCAT(DISTINCT tbl_productions_orders_items.items_id) as item_id
                FROM tbl_productions_orders_items
                WHERE tbl_productions_orders_items.productions_orders_id = $productions_orders_id
            )";
            $dtItems = $this->db->query($tbProductionsOrderItemsCs)->row_array();

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
            // $this->db->where('(tbl_productions_orders_items.items_id IN (' . $items_id . '))');
            $this->db->where('(tbl_productions_orders_items.items_id IN (' . $dtItems['item_id'] . '))');

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

            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $total_paper_exchange = 0;
            $total_quantity_compensation = 0;
            $quantity_zinc = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation / $count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //

                    // $quantity = ceil($vB['quantity']);
                    $quantity = ceil(round($vB['quantity'], 4));
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                    $total_quantity_compensation += $quantity_compensation;
                }
            }

            $dtZinc = $this->production_list_model->getBOMZinc($plan_id);
            if (!empty($dtZinc)) {
                $quantity_zinc = $dtZinc['quantity_compensation'];
            }

            if (empty($quantity_zinc)) {
                $quantity_zinc = 0;
            }

            // print_arrays($quantity_zinc);
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

            $so_luong_san_xuat = (float)$quantityAll['quantity'] + (float)$quantityDp;
            $aRow['so_luong_san_xuat'] = $so_luong_san_xuat;
            $temp_item_id = explode(',', $_item_id);
            if ($_item_id) {
                $_item_id = explode(',', $_item_id);
                $_item_id = $_item_id[0];
            }

            $number_face = $aRow['number_face'];
            $number_operations = $aRow['number_operations'];
            $number_cutting = $aRow['number_cutting'];
            $quota_time_f1 = $aRow['quota_time_f1'];
            $quota_time_f2 = $aRow['quota_time_f2'];

            $aRow['so_lan_tren_mat'] = $number_face;
            $aRow['so_lan_van_hanh'] = $number_operations;
            $aRow['so_duong_dao_cat'] = $number_cutting;
            $aRow['quota_time_f1'] = $quota_time_f1;
            $aRow['quota_time_f2'] = $quota_time_f2;

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
                $aRow['nang_suat_nhan_vien'] = $moderationPlan['nang_suat_nhan_vien'];
                $aRow['ngay_bat_dau_ngung_may'] = $moderationPlan['ngay_bat_dau_ngung_may'];
                $aRow['ngay_ket_thuc_ngung_may'] = $moderationPlan['ngay_ket_thuc_ngung_may'];
            }

            if (!empty($aRow['machine_id'])) {
                $machine = $this->production_list_model->getMachinesById($aRow['machine_id']);
                $soup_ingredients = (float)$machine['soup_ingredients'];
                $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
            }

            $nang_suat_nhan_vien = $aRow['nang_suat_nhan_vien'];
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
                        // $_tong_so_to_in = $aRow['to_in'];
                        $machine_id = $product_versions['machines'];
                        $machine = $this->production_list_model->getMachinesById($machine_id);
                        $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
                        $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;

                        $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
                        $_tong_so_to_in = $aRow['to_in'];

                        $tong_thoi_gian = 0;
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                            'soup_ingredients' => $soup_ingredients,
                            'number_face' => $number_face,
                            'number_operations' => $number_operations,
                            'number_cutting' => $number_cutting,
                            'quantity_zinc' => $quantity_zinc,
                            'so_luong_san_xuat' => $so_luong_san_xuat,
                            'nang_suat_nhan_vien' => $nang_suat_nhan_vien,
                            'time_change_size' => $time_change_size,
                            'quota_time_f1' => $quota_time_f1,
                            'quota_time_f2' => $quota_time_f2,
                        ];

                        if (!empty($machine)) {
                            // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                            $thoi_gian_canh_bai = $machine['preparation_time'];
                            $nang_suat_may = $machine['quota_productivity'];
                            $so_mat = $moderationPlan['mat_in'];
                            if ($nang_suat_may > 0) {
                                if (in_array($_type_productionlist_id, CAL_PL_1)) {
                                    //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may) + ($time_change_size * $quantity_zinc);
                                } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                                    //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may);
                                } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                                    //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $number_operations) / $nang_suat_may);
                                } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                                    //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $number_cutting) / $nang_suat_may);
                                } else {
                                    // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                }

                                // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                // // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) - $thoi_gian_canh_bai;
                            }

                            if (in_array($_type_productionlist_id, CAL_PL_5)) {
                                //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                                if ($nang_suat_nhan_vien) {
                                    $tong_thoi_gian = ($so_luong_san_xuat / $nang_suat_nhan_vien);
                                } else {
                                    $tong_thoi_gian = 0;
                                }
                            }

                            $tong_thoi_gian = $tong_thoi_gian + (float)$quota_time_f1 + (float)$quota_time_f2;
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
                            'number_face' => $number_face,
                            'number_operations' => $number_operations,
                            'number_cutting' => $number_cutting,
                            'quota_time_f1' => $quota_time_f1,
                            'quota_time_f2' => $quota_time_f2,
                        ];
                        $options['mat_in'] = $countFace;

                        if (!empty($moderationPlan)) {
                            $machine_id = $moderationPlan['machine_id'];
                            $machine = $this->production_list_model->getMachinesById($machine_id);
                            $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
                            $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;
                            $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
                            $_tong_so_to_in = $aRow['to_in'];
                            $options['soup_ingredients'] = $soup_ingredients;

                            if (!empty($machine)) {
                                $thoi_gian_canh_bai = $machine['preparation_time'];
                                $nang_suat_may = $machine['quota_productivity'];
                                $so_mat = $options['mat_in'];
                                $tong_thoi_gian = 0;
                                if ($nang_suat_may > 0) {

                                    if (in_array($_type_productionlist_id, CAL_PL_1)) {
                                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may) + ($time_change_size * $quantity_zinc);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                                        //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $number_operations) / $nang_suat_may);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                                        //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $number_cutting) / $nang_suat_may);
                                    } else {
                                        // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                    }
                                }

                                if (in_array($_type_productionlist_id, CAL_PL_5)) {
                                    //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                                    if ($nang_suat_nhan_vien) {
                                        $tong_thoi_gian = ($so_luong_san_xuat / $nang_suat_nhan_vien);
                                    } else {
                                        $tong_thoi_gian = 0;
                                    }
                                }

                                $tong_thoi_gian = $tong_thoi_gian + (float)$quota_time_f1 + (float)$quota_time_f2;
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
                    $row[] = '<div class="text-center">' . $start . '</div>';
                } else if ($v == 'ngay_giao_hang_he_thong') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? _d($aRow[$v]) : '') . '</div>';
                } else if ($v == 'so_luong_san_xuat' || $v == 'so_con_tren_to_in' || $v == 'to_in') {
                    $row[] = '<div class="text-center">' . ($aRow[$v] ? (is_numeric($aRow[$v]) ? formatNumber($aRow[$v]) : $aRow[$v]) : '') . '</div>';
                    // $row[] = '<div class="text-center">'.($aRow[$v] ? ($aRow[$v]) : '').'</div>';
                } else if ($v == 'tong_thoi_gian') {
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', 0, 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" name="' . $v . '" class="form-control number-format ' . $v . '" value="' . (!empty($aRow[$v]) ? formatNumber($aRow[$v]) : '') . '">
                    </div>';
                    $total['tong_thoi_gian'] += (!empty($aRow[$v]) ? ($aRow[$v]) : '0');
                } else if ($v == 'ngay_bat_dau_ke_hoach' || $v == 'ngay_ket_thuc_ke_hoach' || $v == 'ngay_bat_dau_thuc_te' || $v == 'ngay_ket_thuc_thuc_te' || $v == 'ngay_bat_dau_ngung_may' || $v == 'ngay_ket_thuc_ngung_may') {
                    // $row[] = '<div>
                    //     <input type="text" style="width: 150px;" onchange="updatePlan(this, \''.$v.'\', '.$aRow['id'].')" name="'.$v.'" class="form-control datetimepicker '.$v.'" value="'.(!empty($aRow[$v]) ? date_format(date_create($aRow[$v]), 'd/m/Y H:i') : '').'">
                    // </div>';

                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', 0, 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" name="' . $v . '" class="form-control datetimepicker ' . $v . '" value="' . (!empty($aRow[$v]) ? date_format(date_create($aRow[$v]), 'd/m/Y H:i') : '') . '">
                    </div>';
                } else if ($v == 'so_luong_thuc_te') {
                    // $row[] = '<div>
                    //     <input type="text" style="width: 150px;" onchange="updatePlan(this, \''.$v.'\', '.$aRow['id'].')" name="'.$v.'" class="form-control '.$v.'" value="'.formatNumber($aRow[$v]).'">
                    // </div>';
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', 0, 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
                    </div>';
                    $total['tong_so_luong'] += (!empty($aRow[$v]) ? ($aRow[$v]) : '0');
                } else if ($v == 'nang_suat_nhan_vien') {
                    $row[] = '<div>
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', 0, 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
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
                        <select name="status" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', 0, 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" class="form-control status">
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
                        <select style="width: 150px;" name="machine_id" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', ' . $aRow['to_in'] . ', 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" data-placeholder="Máy móc" class="machine_id">
                            ' . $opMachines . '
                        </select>
                    ';
                } else if ($v == 'mat_in') {
                    $row[] = '<div class="">
                        <input type="text" style="width: 150px;" onchange="updateModerationPlan(this, \'' . $v . '\', ' . $_po_id . ', ' . $_item_id . ', ' . $_type_productionlist_id . ', ' . $_stage_id . ', ' . $aRow['to_in'] . ', 0, ' . $number_face . ', ' . $number_operations . ', ' . $number_cutting . ', ' . $quantity_zinc . ', ' . $so_luong_san_xuat . ', ' . $quota_time_f1 . ', ' . $quota_time_f2 . ')" name="' . $v . '" class="form-control ' . $v . '" value="' . formatNumber($aRow[$v]) . '">
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

                    $row[] = '<div class="text-center so_gio_thuc_te">' . formatNumber($aRow[$v]) . '' . $str_status . '</div>';
                } else if ($v == 'item_code') {
                    $row[] = '<div><a class="tnh-modal" href="' . base_url('admin/products/view_product/' . $_item_id) . '">' . $aRow[$v] . '</a></div>';
                } else if ($v == 'so_lan_tren_mat' || $v == 'so_lan_van_hanh' || $v == 'so_duong_dao_cat') {
                    $row[] = '<div class="text-center">' . formatNumber($aRow[$v]) . '</div>';
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
        $_number_face = (float)$this->input->post('_number_face');
        $_number_operations = (float)$this->input->post('_number_operations');
        $_number_cutting = (float)$this->input->post('_number_cutting');
        $_quantity_zinc = (float)$this->input->post('_quantity_zinc');
        $_so_luong_san_xuat = (float)$this->input->post('_so_luong_san_xuat');
        $_quota_time_f1 = (float)$this->input->post('_quota_time_f1');
        $_quota_time_f2 = (float)$this->input->post('_quota_time_f2');

        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

        if ($_name == 'ngay_bat_dau_ke_hoach' || $_name == 'ngay_ket_thuc_ke_hoach' || $_name == 'ngay_bat_dau_thuc_te' || $_name == 'ngay_ket_thuc_thuc_te' || $_name == 'ngay_bat_dau_ngung_may' || $_name == 'ngay_ket_thuc_ngung_may') {
            if (!empty($_value)) {
                $_value = to_sql_date($_value, true);
            } else {
                $_value = NULL;
            }
        } else if ($_name == 'so_luong_thuc_te' || $_name == 'tong_thoi_gian' || $_name == 'mat_in' || $_name == 'nang_suat_nhan_vien') {
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
            'number_face' => $_number_face,
            'number_operations' => $_number_operations,
            'number_cutting' => $_number_cutting,
            'quantity_zinc' => $_quantity_zinc,
            'so_luong_san_xuat' => $_so_luong_san_xuat,
            'quota_time_f1' => $_quota_time_f1,
            'quota_time_f2' => $_quota_time_f2,
            'updated_by' => get_staff_user_id(),
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        if ($_name == 'nang_suat_nhan_vien') {
            if (in_array($_type_productionlist_id, CAL_PL_5)) {
                //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                $nang_suat_nhan_vien = $_value;
                if ($nang_suat_nhan_vien) {
                    $tong_thoi_gian = ($_so_luong_san_xuat / $nang_suat_nhan_vien);
                } else {
                    $tong_thoi_gian = 0;
                }
                $options['tong_thoi_gian'] = $tong_thoi_gian;
            }
        }

        if (!empty($moderationPlan)) {
            $rs = $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $options);
        } else {
            $rs = $this->production_list_model->insertModerationPlan($options);
        }

        if ($_name == 'machine_id') {
            $_tong_so_to_in = number_unformat($this->input->post('_tong_so_to_in'));
            $machine_id = $_value;
            $machine = $this->production_list_model->getMachinesById($machine_id);
            $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
            $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;
            // $product = $this->products_model->rowProduct($_item_id);
            $tong_thoi_gian = 0;
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($machine)) {
                $thoi_gian_canh_bai = $machine['preparation_time'];
                $nang_suat_may = $machine['quota_productivity'];
                $so_mat = $moderationPlan['mat_in'];
                $nang_suat_nhan_vien = $moderationPlan['nang_suat_nhan_vien'];
                if ($nang_suat_may > 0) {
                    if (in_array($_type_productionlist_id, CAL_PL_1)) {
                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $_number_face) / $nang_suat_may) + ($time_change_size * $_quantity_zinc);
                    } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $_number_face) / $nang_suat_may);
                    } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                        //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                        $tong_thoi_gian = (($_tong_so_to_in * $_number_operations) / $nang_suat_may);
                    } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                        //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                        $tong_thoi_gian = (($_tong_so_to_in * $_number_cutting) / $nang_suat_may);
                    } else {
                        // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                        // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) - $thoi_gian_canh_bai;
                    }
                }

                if (in_array($_type_productionlist_id, CAL_PL_5)) {
                    //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                    if ($nang_suat_nhan_vien) {
                        $tong_thoi_gian = ($_so_luong_san_xuat / $nang_suat_nhan_vien);
                    } else {
                        $tong_thoi_gian = 0;
                    }
                }
            }

            $tong_thoi_gian = $tong_thoi_gian + $_quota_time_f1 + $_quota_time_f2;
            $optionsUp['tong_so_to_in'] = $_tong_so_to_in;
            $optionsUp['tong_thoi_gian'] = $tong_thoi_gian;
            $optionsUp['soup_ingredients'] = $soup_ingredients;
            $optionsUp['time_change_size'] = $time_change_size;
            $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $optionsUp);
        } else if ($_name == 'mat_in') {
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            if (!empty($moderationPlan)) {
                // $_tong_so_to_in = number_unformat($moderationPlan['tong_so_to_in']);
                $_tong_so_to_in = number_unformat($this->input->post('_tong_so_to_in'));
                $machine_id = $moderationPlan['machine_id'];
                $machine = $this->production_list_model->getMachinesById($machine_id);
                $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
                $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;

                $tong_thoi_gian = 0;
                if (!empty($machine)) {
                    // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                    $thoi_gian_canh_bai = $machine['preparation_time'];
                    $nang_suat_may = $machine['quota_productivity'];
                    $so_mat = $moderationPlan['mat_in'];
                    $nang_suat_nhan_vien = $moderationPlan['nang_suat_nhan_vien'];
                    if ($nang_suat_may > 0) {
                        if (in_array($_type_productionlist_id, CAL_PL_1)) {
                            //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                            $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $_number_face) / $nang_suat_may) + ($time_change_size * $_quantity_zinc);
                        } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                            //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                            $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $_number_face) / $nang_suat_may);
                        } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                            //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                            $tong_thoi_gian = (($_tong_so_to_in * $_number_operations) / $nang_suat_may);
                        } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                            //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                            $tong_thoi_gian = (($_tong_so_to_in * $_number_cutting) / $nang_suat_may);
                        } else {
                            // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                            $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                        }
                        // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                    }

                    if (in_array($_type_productionlist_id, CAL_PL_5)) {
                        //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                        if ($nang_suat_nhan_vien) {
                            $tong_thoi_gian = ($_so_luong_san_xuat / $nang_suat_nhan_vien);
                        } else {
                            $tong_thoi_gian = 0;
                        }
                    }
                }
            }

            $tong_thoi_gian = $tong_thoi_gian + $_quota_time_f1 + $_quota_time_f2;
            $optionsUp['tong_so_to_in'] = $_tong_so_to_in;
            $optionsUp['tong_thoi_gian'] = $tong_thoi_gian;
            // print_arrays($optionsUp)
            $this->production_list_model->updateModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id, $optionsUp);
        } else if ($_name == 'ngay_bat_dau_thuc_te' || $_name == 'ngay_ket_thuc_thuc_te' || $_name == 'ngay_bat_dau_ngung_may' || $_name == 'ngay_ket_thuc_ngung_may') {
            $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);
            $ngay_bat_dau_thuc_te = $moderationPlan['ngay_bat_dau_thuc_te'];
            $ngay_ket_thuc_thuc_te = $moderationPlan['ngay_ket_thuc_thuc_te'];
            $ngay_bat_dau_ngung_may = $moderationPlan['ngay_bat_dau_ngung_may'];
            $ngay_ket_thuc_ngung_may = $moderationPlan['ngay_ket_thuc_ngung_may'];
            $so_gio_thuc_te = 0;
            if (!empty($ngay_bat_dau_thuc_te) && !empty($ngay_ket_thuc_thuc_te)) {
                $time1 = strtotime($ngay_bat_dau_thuc_te);
                $time2 = strtotime($ngay_ket_thuc_thuc_te);
                $hours = ($time2 - $time1) / 3600;
                $hours = number_format($hours, 3);
                $so_gio_thuc_te = $hours;
            }

            if (!empty($ngay_bat_dau_ngung_may) && !empty($ngay_ket_thuc_ngung_may)) {
                $time1 = strtotime($ngay_bat_dau_ngung_may);
                $time2 = strtotime($ngay_ket_thuc_ngung_may);
                $hours = ($time2 - $time1) / 3600;
                $hours = number_format($hours, 3);
                $so_gio_thuc_te -= $hours;
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
        $start_date_bd_kh = $this->input->post('start_date_bd_kh') ? to_sql_date($this->input->post('start_date_bd_kh')) . ' 00:00:00' : null;
        $end_date_bd_kh = $this->input->post('end_date_bd_kh') ? to_sql_date($this->input->post('end_date_bd_kh')) . ' 23:59:59' : null;
        $productions_orders_search = $this->input->post('productions_orders_search');
        $start_date_reality = $this->input->post('start_date_reality') ? to_sql_date($this->input->post('start_date_reality')) . ' 00:00:00' : null;
        $end_date_reality = $this->input->post('end_date_reality') ? to_sql_date($this->input->post('end_date_reality')) . ' 23:59:59' : null;
        $start_date_reality_kt = $this->input->post('start_date_reality_kt') ? to_sql_date($this->input->post('start_date_reality_kt')) . ' 00:00:00' : null;
        $end_date_reality_kt = $this->input->post('end_date_reality_kt') ? to_sql_date($this->input->post('end_date_reality_kt')) . ' 23:59:59' : null;

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

        $wherePOI = '';
        if (!empty($productions_orders_search)) {
            $wherePOI .= ' AND tbl_productions_orders_items.productions_orders_id = ' . $productions_orders_search . '';
        }

        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tb_date_delivery.date_shipping as date_shipping,
        //         tb_date_export.date_active as date_export,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         tbl_products.quantity_child_molds as quantity_child_molds,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
        //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors
        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
        //     LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
        //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
        //     LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
        //     WHERE 1 $wherePOI
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tb_date_delivery.date_shipping as date_shipping,
                '' as date_export,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                tbl_products.quantity_child_molds as quantity_child_molds,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders_items.versions_stage) as versions_stage,
                SUM(0) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
            WHERE 1 $wherePOI
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
            tbl_productions_orders_items_stages.number_face as number_face,
            tbl_productions_orders_items_stages.number_operations as number_operations,
            tbl_productions_orders_items_stages.number_cutting as number_cutting,
            tbl_productions_orders_items_stages.quota_time_f1 as quota_time_f1,
            tbl_productions_orders_items_stages.quota_time_f2 as quota_time_f2,
        ');
        $this->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'inner');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        // $this->db->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left');
        // $this->db->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left');

        $this->db->where(' exists (
            SELECT 1
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders_items_stages.productions_orders_id AND tbl_productions_orders_items_stages.stage_id = ' . STAGES_MATERIAL . ' AND tbl_productions_orders_items_stages.active = 1
        ) ', false, false);

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

        $whereDateBD = '';
        if (!empty($start_date_bd_kh)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_ke_hoach >= "' . $start_date_bd_kh . '"';
        }

        if (!empty($end_date_bd_kh)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_ke_hoach <= "' . $end_date_bd_kh . '"';
        }

        if (!empty($start_date_reality)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_thuc_te >= "' . $start_date_reality . '"';
        }

        if (!empty($end_date_reality)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_bat_dau_thuc_te <= "' . $end_date_reality . '"';
        }

        if (!empty($start_date_reality_kt)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_ket_thuc_thuc_te >= "' . $start_date_reality_kt . '"';
        }

        if (!empty($end_date_reality_kt)) {
            $whereDateBD .= ' AND tbl_moderation_plan.ngay_ket_thuc_thuc_te <= "' . $end_date_reality_kt . '"';
        }

        if (!empty($whereDateBD)) {
            $this->db->where(' exists (
                SELECT 1
                FROM tbl_moderation_plan
                WHERE tbl_moderation_plan.po_id = tbl_productions_orders.id AND tbl_moderation_plan.item_id = tb_production_order_item.items_id  AND tbl_moderation_plan.type_productionlist_id = tbl_category_stages.type_productionlist_id   AND tbl_moderation_plan.stage_id = tbl_productions_orders_items_stages.stage_id ' . $whereDateBD . ' 
            ) ', false, false);
        }

        $this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id');
        $this->db->order_by('tb_production_order_item.date_shipping asc');
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

        $this->db->select('tbl_category_stages.name');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_category_stages.id', $status_table_stages);
        $category_stages = $this->db->get()->row_array();
        $category_stages_name = '';
        if (!empty($category_stages['name'])) {
            $category_stages = $category_stages['name'];
            $category_stages_name =  $category_stages;
        }

        $colName = [
            'type_productionlist_id' => '',
            'id' => 'STT',
            'ngay_giao_hang_he_thong' => 'Ngày mở lệnh',
            'reference_no_po' => 'Mã LSX',
            'item_code' => 'Mã sản phẩm',
            'item_name' => 'Tên sản phẩm',
            'so_luong_san_xuat' => 'Tổng số con',
            'stage' => 'Công đoạn ' . $category_stages_name,
            'date_delivery' => 'Ngày giao hàng dự kiến',
            'so_con_tren_to_in' => 'Tờ',
            'to_in' => 'Tờ',
            'mat_in' => 'Số mặt in',
            'so_lan_tren_mat' => 'Số lần trên mặt',
            'so_lan_van_hanh' => 'Số lần vận hành',
            'so_duong_dao_cat' => 'Số đường dao cắt',
            'machine_id' => 'Máy móc',
            'nang_suat_nhan_vien' => 'Năng suất nhân viên',
            'tong_thoi_gian' => 'H',
            'ngay_bat_dau_ke_hoach' => 'Bắt đầu (Ngày - H)',
            'ngay_ket_thuc_ke_hoach' => 'Kết thúc (Ngày - H)',
            'ngay_bat_dau_ngung_may' => 'Ngày bắt đầu ngưng máy (Ngày - H)',
            'ngay_ket_thuc_ngung_may' => 'Ngày kết thúc ngưng máy (Ngày - H)',
            'ngay_bat_dau_thuc_te' => 'Bắt đầu (Ngày - H)',
            'ngay_ket_thuc_thuc_te' => 'Kết thúc (Ngày - H)',
            'so_luong_thuc_te' => 'Số lượng thực tế',
            'so_gio_thuc_te' => 'Số giờ thực tế',
            // 'so_luong_thuc_te' => 'Trạng thái',
            'status' => 'Trạng thái',
            'time' => 'Thời gian dừng máy',
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
            'so_lan_tren_mat',
            'so_lan_van_hanh',
            'so_duong_dao_cat',
            'machine_id',
            'nang_suat_nhan_vien',
            'status',
            'time',
            'note',
            'sign',
            'ngay_bat_dau_ngung_may',
            'ngay_ket_thuc_ngung_may',
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

        $styleSum = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 13,
                'name' => 'Times New Roman'
            ),
        ];


        $worksheet = $objPHPExcel->getActiveSheet();
        $excelRowNum = 1;
        $maxCol = count($colName) - 1;


        $logoUrl = get_upload_path_by_type('company') . get_option('company_logo');
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Sample Image');
        $objDrawing->setDescription('Image');
        $objDrawing->setPath($logoUrl);
        $objDrawing->setCoordinates('B' . $excelRowNum); // Vị trí cột và dòng để đặt hình ảnh
        $objDrawing->setWidth(35); // Đặt chiều rộng (đơn vị pixel)
        $objDrawing->setHeight(70); // Đặt chiều cao (đơn vị pixel)
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // $objPHPExcel->getActiveSheet()->getColumnDimension($columnIndex)->setWidth($columnWidth);
        $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(60);
        $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, 'KẾ HOẠCH ĐIỀU ĐỘ CÔNG ĐOẠN ' . $category_stages)->getStyle('A' . $excelRowNum)->applyFromArray($styleTitle);

        $excelRowNum = 2;
        $machine = get_table_where('tbl_machines', ['id' => $machine_id_new], '', 'row_array', '', 'name');
        $machine = (!empty($machine['name']) ? $machine['name'] : '');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $excelRowNum, 'Tên thiết bị: ' . $machine)->getStyle('E' . $excelRowNum)->applyFromArray($styleText);

        // $dateCell = 'Từ ngày: '._d($start_date_delivery) . '                                Đến ngày: '._d($end_date_delivery);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $excelRowNum, 'Từ ngày: ' . _d($date_start_cel))->getStyle('F' . $excelRowNum)->applyFromArray($styleText);

        $objPHPExcel->getActiveSheet()->setCellValue('H' . $excelRowNum, 'Đến ngày: ' . _d($date_end_cel))->getStyle('H' . $excelRowNum)->applyFromArray($styleText);

        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $excelRowNum, 'TỔNG THỜI GIAN DỰ KIẾN')->getStyle('K' . $excelRowNum)->applyFromArray($styleHeader);
        // $objPHPExcel->getActiveSheet()->mergeCells('L1:M1');
        // $objPHPExcel->getActiveSheet()->setCellValue('L' . $excelRowNum, 'KẾ HOẠCH')->getStyle('L' . $excelRowNum)->applyFromArray($styleHeader);
        // $objPHPExcel->getActiveSheet()->mergeCells('N1:O1');
        // $objPHPExcel->getActiveSheet()->setCellValue('N' . $excelRowNum, 'THỰC TẾ')->getStyle('N' . $excelRowNum)->applyFromArray($styleHeader);

        $excelRowNum = 4;

        foreach ($aColumns as $key => $value) {
            // $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
            if (in_array($value, $rowspan)) {
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[$key] . ($excelRowNum - 1) . ':' . $cloumns_excel[$key] . $excelRowNum);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . ($excelRowNum - 1), ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum - 1) . ':' . $cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
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
                $cell = $cloumns_excel[$startIndex] . ($excelRowNum - 1);
            } else {
                $cell = $cloumns_excel[$startIndex] . ($excelRowNum - 1) . ':' . $cloumns_excel[$endIndex] . ($excelRowNum - 1);
                $objPHPExcel->getActiveSheet()->mergeCells($cell);
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$startIndex] . ($excelRowNum - 1), $hTitle)->getStyle($cell)->applyFromArray($styleHeader);
        }

        $hideColumnIndex = 0;
        $columnDimension = $worksheet->getColumnDimensionByColumn($hideColumnIndex);
        $columnDimension->setVisible(false);

        $dtStatusProductionsLists = $this->production_list_model->getStatusProductionsLists();
        $dtMachines = $this->production_list_model->getMachines();
        $group_id = 0;
        $excelRowNum = 5;
        $start_row = $excelRowNum;
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

            $tbProductionsOrderItemsCs = "(
                SELECT
                    GROUP_CONCAT(DISTINCT tbl_productions_orders_items.items_id) as item_id
                FROM tbl_productions_orders_items
                WHERE tbl_productions_orders_items.productions_orders_id = $productions_orders_id
            )";
            $dtItems = $this->db->query($tbProductionsOrderItemsCs)->row_array();

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
            // $this->db->where('(tbl_productions_orders_items.items_id IN (' . $items_id . '))');
            $this->db->where('(tbl_productions_orders_items.items_id IN (' . $dtItems['item_id'] . '))');

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

            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $total_paper_exchange = 0;
            $total_quantity_compensation = 0;
            $quantity_zinc = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation / $count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //

                    // $quantity = ceil($vB['quantity']);
                    $quantity = ceil(round($vB['quantity'], 4));
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                    $total_quantity_compensation += $quantity_compensation;
                }
            }

            $dtZinc = $this->production_list_model->getBOMZinc($plan_id);
            if (!empty($dtZinc)) {
                $quantity_zinc = $dtZinc['quantity_compensation'];
            }

            if (empty($quantity_zinc)) {
                $quantity_zinc = 0;
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

            $so_luong_san_xuat = (float)$quantityAll['quantity'] + (float)$quantityDp;
            $aRow['so_luong_san_xuat'] = $so_luong_san_xuat;
            $temp_item_id = explode(',', $_item_id);
            if ($_item_id) {
                $_item_id = explode(',', $_item_id);
                $_item_id = $_item_id[0];
            }

            $number_face = $aRow['number_face'];
            $number_operations = $aRow['number_operations'];
            $number_cutting = $aRow['number_cutting'];
            $quota_time_f1 = $aRow['quota_time_f1'];
            $quota_time_f2 = $aRow['quota_time_f2'];

            $aRow['so_lan_tren_mat'] = $number_face;
            $aRow['so_lan_van_hanh'] = $number_operations;
            $aRow['so_duong_dao_cat'] = $number_cutting;

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
                $aRow['nang_suat_nhan_vien'] = $moderationPlan['nang_suat_nhan_vien'];
                $aRow['ngay_bat_dau_ngung_may'] = $moderationPlan['ngay_bat_dau_ngung_may'];
                $aRow['ngay_ket_thuc_ngung_may'] = $moderationPlan['ngay_ket_thuc_ngung_may'];
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
                $aRow['nang_suat_nhan_vien'] = 0;
                $aRow['ngay_bat_dau_ngung_may'] = '';
                $aRow['ngay_ket_thuc_ngung_may'] = '';
            }

            if (!empty($aRow['machine_id'])) {
                $machine = $this->production_list_model->getMachinesById($aRow['machine_id']);
                $soup_ingredients = (float)$machine['soup_ingredients'];
                $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
            }

            $nang_suat_nhan_vien = $aRow['nang_suat_nhan_vien'];
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
                        // $_tong_so_to_in = $aRow['to_in'];
                        $machine_id = $product_versions['machines'];
                        $machine = $this->production_list_model->getMachinesById($machine_id);
                        $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
                        $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;
                        $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
                        $_tong_so_to_in = $aRow['to_in'];

                        $tong_thoi_gian = 0;
                        $moderationPlan = $this->production_list_model->getModerationPlan($_po_id, $_item_id, $_type_productionlist_id, $_stage_id);

                        $options = [
                            'po_id' => $_po_id,
                            'item_id' => $_item_id,
                            'type_productionlist_id' => $_type_productionlist_id,
                            'stage_id' => $_stage_id,
                            'updated_by' => get_staff_user_id(),
                            'date_updated' => date('Y-m-d H:i:s'),
                            'soup_ingredients' => $soup_ingredients,
                            'number_face' => $number_face,
                            'number_operations' => $number_operations,
                            'number_cutting' => $number_cutting,
                            'quantity_zinc' => $quantity_zinc,
                            'so_luong_san_xuat' => $so_luong_san_xuat,
                            'nang_suat_nhan_vien' => $nang_suat_nhan_vien,
                            'time_change_size' => $time_change_size,
                        ];

                        if (!empty($machine)) {
                            // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                            $thoi_gian_canh_bai = $machine['preparation_time'];
                            $nang_suat_may = $machine['quota_productivity'];
                            $so_mat = $moderationPlan['mat_in'];
                            // if ($nang_suat_may > 0) {
                            //     $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                            //     // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) - $thoi_gian_canh_bai;
                            // }

                            if ($nang_suat_may > 0) {
                                if (in_array($_type_productionlist_id, CAL_PL_1)) {
                                    //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may) + ($time_change_size * $quantity_zinc);
                                } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                                    //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may);
                                } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                                    //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $number_operations) / $nang_suat_may);
                                } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                                    //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                                    $tong_thoi_gian = (($_tong_so_to_in * $number_cutting) / $nang_suat_may);
                                } else {
                                    // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                                    $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                }

                                // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                // // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) - $thoi_gian_canh_bai;
                            }

                            if (in_array($_type_productionlist_id, CAL_PL_5)) {
                                //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                                if ($nang_suat_nhan_vien) {
                                    $tong_thoi_gian = ($so_luong_san_xuat / $nang_suat_nhan_vien);
                                } else {
                                    $tong_thoi_gian = 0;
                                }
                            }

                            $tong_thoi_gian = $tong_thoi_gian + (float)$quota_time_f1 + (float)$quota_time_f2;
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
                            'number_face' => $number_face,
                            'number_operations' => $number_operations,
                            'number_cutting' => $number_cutting,
                        ];
                        $options['mat_in'] = $countFace;

                        if (!empty($moderationPlan)) {
                            // $_tong_so_to_in = $aRow['to_in'];
                            $machine_id = $moderationPlan['machine_id'];
                            $machine = $this->production_list_model->getMachinesById($machine_id);
                            $soup_ingredients = !empty($machine['soup_ingredients']) ? (float)$machine['soup_ingredients'] : 0;
                            $time_change_size = !empty($machine['time_change_size']) ? (float)$machine['time_change_size'] : 0;
                            $options['soup_ingredients'] = $soup_ingredients;
                            $aRow['to_in'] = $aRow['to_in'] - $soup_ingredients;
                            $_tong_so_to_in = $aRow['to_in'];

                            if (!empty($machine)) {
                                $thoi_gian_canh_bai = $machine['preparation_time'];
                                $nang_suat_may = $machine['quota_productivity'];
                                $so_mat = $options['mat_in'];
                                $tong_thoi_gian = 0;
                                // if ($nang_suat_may > 0) {
                                //     $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                //     // $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) - $thoi_gian_canh_bai;
                                // }

                                if ($nang_suat_may > 0) {

                                    if (in_array($_type_productionlist_id, CAL_PL_1)) {
                                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY + THỜI GIAN THAY SIZE (15 PHÚT 1 KẼM)
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may) + ($time_change_size * $quantity_zinc);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_2)) {
                                        //(SỐ TỜ IN x SỐ MẶT x SỐ LẦN/MẶT)/NĂNG SUẤT CỦA MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat * $number_face) / $nang_suat_may);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_3)) {
                                        //(SỐ TỜ IN x SỐ LẦN VẬN HÀNH)/NĂNG SUẤT CỦA MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $number_operations) / $nang_suat_may);
                                    } else if (in_array($_type_productionlist_id, CAL_PL_4)) {
                                        //(SỐ TỜ IN x SỐ ĐƯỜNG DAO CĂT)/NĂNG SUẤT MÁY
                                        $tong_thoi_gian = (($_tong_so_to_in * $number_cutting) / $nang_suat_may);
                                    } else {
                                        // TỔNG THỜI GIAN DỰ KIẾN= ((Tổng số tờ In * Số mặt)/Năng suất máy)+Thời gian canh bài
                                        $tong_thoi_gian = (($_tong_so_to_in * $so_mat) / $nang_suat_may) + $thoi_gian_canh_bai;
                                    }
                                }

                                if (in_array($_type_productionlist_id, CAL_PL_5)) {
                                    //SỐ CON TRÊN LỆNH SẢN XUẤT/NĂNG SUẤT NHÂN VIÊN
                                    if ($nang_suat_nhan_vien) {
                                        $tong_thoi_gian = ($so_luong_san_xuat / $nang_suat_nhan_vien);
                                    } else {
                                        $tong_thoi_gian = 0;
                                    }
                                }

                                $tong_thoi_gian = $tong_thoi_gian + (float)$quota_time_f1 + (float)$quota_time_f2;
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
                if ($v == 'type_productionlist_id') {
                    $cellValue = $aRow['id'] . '::' . $aRow['items_id'] . '::' . $aRow['type_productionlist_id'] . '::' . $aRow['stage_id'];
                } else if ($v == 'id') {
                    $cellValue = (++$key);
                } else if ($v == 'ngay_giao_hang_he_thong') {
                    $cellValue = ($aRow[$v] ? _d($aRow[$v]) : '');
                } else if ($v == 'so_con_tren_to_in') {
                    $cellValue = (isset($aRow[$v]) ? $aRow[$v] : '');
                    $objPHPExcel->getActiveSheet()->getStyle($cloumns_excel[$k] . $excelRowNum)->getAlignment()->setWrapText(true);
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
                } else if ($v == 'date_delivery') {
                    $cellValue = (isset($aRow[$v]) ? _d($aRow[$v]) : '');
                } else if ($v == 'ngay_bat_dau_ke_hoach' || $v == 'ngay_ket_thuc_ke_hoach' || $v == 'ngay_bat_dau_thuc_te' || $v == 'ngay_ket_thuc_thuc_te' || $v == 'ngay_bat_dau_ngung_may' || $v == 'ngay_ket_thuc_ngung_may') {
                    if (!empty($aRow[$v])) {
                        $time = date("H:i", strtotime($aRow[$v]));
                    } else {
                        $time = '';
                    }
                    $cellValue = $time;
                } else {
                    $cellValue = (isset($aRow[$v]) ? $aRow[$v] : '');
                }
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$k] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$k] . $excelRowNum)->applyFromArray($stylePlain);
            }

            $excelRowNum++;
        }
        $end_row = $excelRowNum - 1;

        foreach ($aColumns as $k => $v) {
            if ($v == 'ngay_giao_hang_he_thong') {
                $cellValue = 'Tổng cộng';
            } else if ($v == 'tong_thoi_gian' || $v == 'so_luong_thuc_te') {
                $cellValue = '=SUM(' . $cloumns_excel[$k] . $start_row . ':' . $cloumns_excel[$k] . $end_row . ')';
            } else {
                $cellValue = '';
            }

            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$k] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$k] . $excelRowNum)->applyFromArray($styleSum);
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

    public function import_excel()
    {
        if (!$this->perEditProductionList) {
            accessDenied($js = true);
        }

        $data = [];
        if ($this->input->post()) {
            $actual_date = $this->input->post('actual_date');
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
            $arraydata          = array();

            // Các cột cần thiết
            $columnNeed = [
                'A', // data
                'Q', // Ngày bắt đầu thực tế
                'R', // Ngày kết thúc thực tế
            ];

            // if ($highestRow > 5) {
            //     $highestRow = 5;
            // }
            for ($row = 5; $row <= $highestRow; ++$row) {
                // for ($col = 0; $col < $highestColumnIndex; ++$col) {
                foreach ($columnNeed as $colIndex => $colIndexWord) {
                    $col = PHPExcel_Cell::columnIndexFromString($colIndexWord) - 1;
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 5][$colIndexWord] = $value;
                }
            }

            $successCount = 0;
            $errors = '';
            if (!empty($arraydata)) {
                foreach ($arraydata as $key => $value) {
                    if (!empty($value['A'])) {
                        $indexData = $value['A'];
                        $indexData = explode('::', $indexData);
                        $po_id = !empty($indexData[0]) ? $indexData[0] : '';
                        $item_id = !empty($indexData[1]) ? $indexData[1] : '';
                        $type_productionlist_id = !empty($indexData[2]) ? $indexData[2] : '';
                        $stage_id = !empty($indexData[3]) ? $indexData[3] : '';
                        $start_date = $value['Q'];
                        $end_date = $value['R'];
                        $isSuccess = false;
                        if (!empty($po_id) && !empty($item_id) && !empty($type_productionlist_id) && !empty($stage_id)) {
                            if (!empty($start_date)) { // Ngày bắt đầu
                                // Kiểm tra có đúng format H:i
                                $timeString = $start_date;
                                $dateTime = DateTime::createFromFormat("H:i", $timeString);
                                if ($dateTime && $dateTime->format("H:i") === $timeString) {
                                    $timeString = to_sql_date($actual_date) . ' ' . $timeString . ':00';
                                    $options = [
                                        'ngay_bat_dau_thuc_te' => $timeString,
                                        'po_id' => $po_id,
                                        'item_id' => $item_id,
                                        'type_productionlist_id' => $type_productionlist_id,
                                        'stage_id' => $stage_id,
                                        'updated_by' => get_staff_user_id(),
                                        'date_updated' => date('Y-m-d H:i:s'),
                                    ];
                                    if ($this->production_list_model->submitModerationPlan($options)) {
                                        $isSuccess = true;
                                    } else {
                                        $errors .= '<div class="text-danger">Thất bại</div>';
                                    }
                                } else {
                                    $errors .= '<div class="text-danger">Giờ Bắt đầu "' . $start_date . '" không đúng định dạng.</div>';
                                }
                            }
                            if (!empty($end_date)) { // Ngày kết thúc
                                // Kiểm tra có đúng format H:i
                                $timeString = $end_date;
                                $dateTime = DateTime::createFromFormat("H:i", $timeString);
                                if ($dateTime && $dateTime->format("H:i") === $timeString) {
                                    $timeString = to_sql_date($actual_date) . ' ' . $timeString . ':00';
                                    $options = [
                                        'ngay_ket_thuc_thuc_te' => $timeString,
                                        'po_id' => $po_id,
                                        'item_id' => $item_id,
                                        'type_productionlist_id' => $type_productionlist_id,
                                        'stage_id' => $stage_id,
                                        'updated_by' => get_staff_user_id(),
                                        'date_updated' => date('Y-m-d H:i:s'),
                                    ];
                                    if ($this->production_list_model->submitModerationPlan($options)) {
                                        $isSuccess = true;
                                    } else {
                                        $errors .= '<div class="text-danger">Thất bại</div>';
                                    }
                                } else {
                                    $errors .= '<div class="text-danger">Giờ Kết thúc "' . $end_date . '" không đúng định dạng.</div>';
                                }
                            }
                            if ($isSuccess) {
                                $successCount++;
                            }
                        } else {
                            $errors .= '<div class="text-danger">File import không hợp lệ! Vui lòng tải lại file mẫu.</div>';
                        }
                    } else {
                        // $errors.= '<div class="text-danger">Dữ liệu rỗng.</div>';
                        continue;
                    }
                }
            } else {
                $errors = '<div class="text-danger">Không có dữ liệu</div>';
            }

            $data['errors'] = $errors;
            if ($successCount) {
                $data['result'] = 1;
                $data['message'] = lang('cong_update_true') . ' ' . $successCount . ' dòng';
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data = [];
            $this->load->view('admin/production_list/import_excel', $data);
        }
    }

    public function loadDataPlan() {
        $data = [];
        $this->load->view('admin/production_list/load_data_plan', $data);
    }

    public function handlingModerationPlan() {
        $data = [];
        $dataPOST = $this->input->post();

        $date_start = $dataPOST['date_start'];
        $date_end = $dataPOST['date_end'];

        if (!$this->perEditProductionList && !$this->perUpdateProductionList) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        if (empty($date_start) || empty($date_end)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn ngày bắt đầu và kết thúc');
            echo json_encode($data);
            die;
        }

        $date_start = to_sql_date($date_start);
        $date_end = to_sql_date($date_end);

        $type_productionlist_id = !empty($dataPOST['type_productionlist_id']) ? $dataPOST['type_productionlist_id'] : 0;
        $production_lists_total_end = $this->production_list_model->rowProductionListsTotalDateEnd($date_end, $type_productionlist_id)['production_lists_total'];
        if (empty($type_productionlist_id)) {
            $data['result'] = 0;
            $data['message'] = lang('Không xác định được loại công đoạn');
            echo json_encode($data);
            die;
        }

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

        $nang_suat_may_uv = !empty($dataPOST['nang_suat_may_uv']) ? number_unformat($dataPOST['nang_suat_may_uv']) : 0;

        $thay_size = !empty($dataPOST['thay_size']) ? number_unformat($dataPOST['thay_size']) : 0;
        $rua_may = !empty($dataPOST['rua_may']) ? number_unformat($dataPOST['rua_may']) : 0;
        $items_canh_bai = !empty($dataPOST['items_canh_bai']) ? json_encode($dataPOST['items_canh_bai'], JSON_UNESCAPED_UNICODE) : null;
        $nang_suat_may_tu_dong = !empty($dataPOST['nang_suat_may_tu_dong']) ? number_unformat($dataPOST['nang_suat_may_tu_dong']) : 0;
        $nang_suat_may_dat_tay = !empty($dataPOST['nang_suat_may_dat_tay']) ? number_unformat($dataPOST['nang_suat_may_dat_tay']) : 0;

        if (!$this->perEditProductionList && $this->perUpdateProductionList) {
            // Nếu không có quyền sửa, lấy lại các giá trị từ production_lists_total_end (không cho phép sửa)
            if (!empty($production_lists_total_end)) {
                if (!empty($production_lists_total_end['so_luong_may'])) $so_luong_may = $production_lists_total_end['so_luong_may'];
                if (!empty($production_lists_total_end['nhom_tho'])) $nhom_tho = $production_lists_total_end['nhom_tho'];
                if (!empty($production_lists_total_end['nang_suat_may'])) $nang_suat_may = $production_lists_total_end['nang_suat_may'];
                if (!empty($production_lists_total_end['_thoi_gian_canh_bai'])) $_thoi_gian_canh_bai = $production_lists_total_end['_thoi_gian_canh_bai'];
                if (!empty($production_lists_total_end['thoi_gian_lam_viec_chuan'])) $thoi_gian_lam_viec_chuan = $production_lists_total_end['thoi_gian_lam_viec_chuan'];
                if (!empty($production_lists_total_end['thoi_gian_lam_viec_ot'])) $thoi_gian_lam_viec_ot = $production_lists_total_end['thoi_gian_lam_viec_ot'];
                if (!empty($production_lists_total_end['thoi_gian_cho_kho'])) $thoi_gian_cho_kho = $production_lists_total_end['thoi_gian_cho_kho'];
                if (!empty($production_lists_total_end['bong_os_nhung'])) $bong_os_nhung = $production_lists_total_end['bong_os_nhung'];
                if (!empty($production_lists_total_end['capacity_1'])) $capacity_1 = $production_lists_total_end['capacity_1'];
                if (!empty($production_lists_total_end['capacity_2'])) $capacity_2 = $production_lists_total_end['capacity_2'];
                if (!empty($production_lists_total_end['capacity_3'])) $capacity_3 = $production_lists_total_end['capacity_3'];
                if (!empty($production_lists_total_end['so_luong_tho'])) $so_luong_tho = $production_lists_total_end['so_luong_tho'];
                if (!empty($production_lists_total_end['nang_suat_may_in_300'])) $nang_suat_may_in_300 = $production_lists_total_end['nang_suat_may_in_300'];
                if (!empty($production_lists_total_end['nang_suat_may_in_600'])) $nang_suat_may_in_600 = $production_lists_total_end['nang_suat_may_in_600'];
                if (!empty($production_lists_total_end['nang_suat_dau_in_trang_den'])) $nang_suat_dau_in_trang_den = $production_lists_total_end['nang_suat_dau_in_trang_den'];
                if (!empty($production_lists_total_end['nang_suat_dau_in_mau'])) $nang_suat_dau_in_mau = $production_lists_total_end['nang_suat_dau_in_mau'];
                if (!empty($production_lists_total_end['thoi_gian_canh_bai_in_trang_den'])) $thoi_gian_canh_bai_in_trang_den = $production_lists_total_end['thoi_gian_canh_bai_in_trang_den'];
                if (!empty($production_lists_total_end['thoi_gian_canh_bai_in_mau'])) $thoi_gian_canh_bai_in_mau = $production_lists_total_end['thoi_gian_canh_bai_in_mau'];
                if (!empty($production_lists_total_end['nang_suat_keo_tay'])) $nang_suat_keo_tay = $production_lists_total_end['nang_suat_keo_tay'];
                if (!empty($production_lists_total_end['nang_suat_may_boi_mot_mat'])) $nang_suat_may_boi_mot_mat = $production_lists_total_end['nang_suat_may_boi_mot_mat'];
                if (!empty($production_lists_total_end['nang_suat_may_boi_hai_mat'])) $nang_suat_may_boi_hai_mat = $production_lists_total_end['nang_suat_may_boi_hai_mat'];
                if (!empty($production_lists_total_end['nang_suat_may_be_giay_thuong'])) $nang_suat_may_be_giay_thuong = $production_lists_total_end['nang_suat_may_be_giay_thuong'];
                if (!empty($production_lists_total_end['nang_suat_may_demi_be_giay_boi_pet'])) $nang_suat_may_demi_be_giay_boi_pet = $production_lists_total_end['nang_suat_may_demi_be_giay_boi_pet'];
                if (!empty($production_lists_total_end['thay_size'])) $thay_size = $production_lists_total_end['thay_size'];
                if (!empty($production_lists_total_end['rua_may'])) $rua_may = $production_lists_total_end['rua_may'];
                if (!empty($production_lists_total_end['items_canh_bai'])) $items_canh_bai = $production_lists_total_end['items_canh_bai'];
                if (!empty($production_lists_total_end['nang_suat_may_uv'])) $nang_suat_may_uv = $production_lists_total_end['nang_suat_may_uv'];
                if (!empty($production_lists_total_end['nang_suat_may_tu_dong'])) $nang_suat_may_tu_dong = $production_lists_total_end['nang_suat_may_tu_dong'];
                if (!empty($production_lists_total_end['nang_suat_may_dat_tay'])) $nang_suat_may_dat_tay = $production_lists_total_end['nang_suat_may_dat_tay'];
            }
        }

        if ($type_productionlist_id == 1) {
            $capacity_2 = $nhom_tho * $nang_suat_may * $thoi_gian_lam_viec_chuan;
            $capacity_3 = $nhom_tho * $nang_suat_may * $thoi_gian_lam_viec_ot;
        } else if ($type_productionlist_id == 2) {
            $capacity_2 = $so_luong_tho * $nang_suat_may * $thoi_gian_lam_viec_chuan;
            $capacity_3 = $so_luong_tho * $nang_suat_may * $thoi_gian_lam_viec_ot;
        }

        $items = !empty($dataPOST['items']) ? $dataPOST['items'] : null;
        $ngay_now = date('Y-m-d');

        $production_lists_total = [
            'date_start' => $date_start,
            'production_list_id' => 0,
            'date_end' => $date_end,
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

            'thay_size' => $thay_size,
            'rua_may' => $rua_may,
            'items_canh_bai' => $items_canh_bai,
            'nang_suat_may_uv' => $nang_suat_may_uv,
            'nang_suat_may_tu_dong' => $nang_suat_may_tu_dong,
            'nang_suat_may_dat_tay' => $nang_suat_may_dat_tay,
        ];

        $arrItems = [];
        $arrProductionsListsDate = [];
        $arrDeleteProductionListsItems = [];
        if (!empty($items)) {

            $arrPOID = array_column($items, 'po_id');
            $productionListItems = $this->production_list_model->getProductionListsItemsMul($arrPOID, true);
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
                $thoi_gian_khac = !empty($value['thoi_gian_khac']) ? number_unformat($value['thoi_gian_khac']) : 0;

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

                $face = !empty($value['face']) ? $value['face'] : 0;
                $face_after = !empty($value['face_after']) ? $value['face_after'] : 0;

                $loai_canh_bai = !empty($value['loai_canh_bai']) ? $value['loai_canh_bai'] : 0;
                $mat = !empty($value['mat']) ? $value['mat'] : '';
                $so_lan_thay_size = !empty($value['so_lan_thay_size']) ? number_unformat($value['so_lan_thay_size']) : 0;
                $so_lan_rua_may = !empty($value['so_lan_rua_may']) ? number_unformat($value['so_lan_rua_may']) : 0;
                $thoi_gian_thay_size = !empty($value['thoi_gian_thay_size']) ? number_unformat($value['thoi_gian_thay_size']) : 0;
                $thoi_gian_rua_may = !empty($value['thoi_gian_rua_may']) ? number_unformat($value['thoi_gian_rua_may']) : 0;
                $to_in_bu_hao = !empty($value['to_in_bu_hao']) ? $value['to_in_bu_hao'] : 0;
                $so_mau_in = !empty($value['so_mau_in']) ? $value['so_mau_in'] : 0;
                $loai = !empty($value['loai']) ? ($value['loai']) : 0;

                $ngay_bat_dau_ke_hoach = !empty($value['ngay_bat_dau_ke_hoach']) ? to_sql_date($value['ngay_bat_dau_ke_hoach'], true) : null;
                $ngay_ket_thuc_ke_hoach = !empty($value['ngay_ket_thuc_ke_hoach']) ? to_sql_date($value['ngay_ket_thuc_ke_hoach'], true) : null;
                $ngay_bat_dau_thuc_te = !empty($value['ngay_bat_dau_thuc_te']) ? to_sql_date($value['ngay_bat_dau_thuc_te'], true) : null;
                $ngay_ket_thuc_thuc_te = !empty($value['ngay_ket_thuc_thuc_te']) ? to_sql_date($value['ngay_ket_thuc_thuc_te'], true) : null;
                $so_luong_thuc_te = !empty($value['so_luong_thuc_te']) ? number_unformat($value['so_luong_thuc_te']) : 0;
                $thoi_gian_canh_bai_thuc_te = !empty($value['thoi_gian_canh_bai_thuc_te']) ? number_unformat($value['thoi_gian_canh_bai_thuc_te']) : 0;
                $npl_canh_bai_thuc_te = !empty($value['npl_canh_bai_thuc_te']) ? ($value['npl_canh_bai_thuc_te']) : '';

                if (!$this->perEditProductionList && $this->perUpdateProductionList) {
                    $stage_id = !empty($value['stage_id']) ? ($value['stage_id']) : 0;
                    $_key = !empty($value['_key']) ? $value['_key'] : 0;
                    $_index = $po_id.'__'.$item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
                    $production_list_item = $productionListItems[$_index] ?? null;
                    if (!empty($production_list_item)) {
                        // Nếu không có quyền sửa, lấy lại các giá trị từ production_list_item (không cho phép sửa)
                        if (!empty($production_list_item['ngay_mo_lsx'])) {
                            $ngay_mo_lsx = $production_list_item['ngay_mo_lsx'];
                            $value['ngay_mo_lsx'] = _d($ngay_mo_lsx);
                        }

                        if (!empty($production_list_item['ngay_giao_hang_he_thong'])) {
                            $ngay_giao_hang_he_thong = $production_list_item['ngay_giao_hang_he_thong'];
                            $value['ngay_giao_hang_he_thong'] = $ngay_giao_hang_he_thong;
                        }

                        if (!empty($production_list_item['so_mat_in'])) {
                            $so_mat_in = $production_list_item['so_mat_in'];
                            $value['so_mat_in'] = $so_mat_in;
                        }

                        if (!empty($production_list_item['loai_canh_bai'])) {
                            $loai_canh_bai = $production_list_item['loai_canh_bai'];
                            $value['loai_canh_bai'] = $loai_canh_bai;
                        }

                        if (!empty($production_list_item['so_lan_thay_size'])) {
                            $so_lan_thay_size = $production_list_item['so_lan_thay_size'];
                            $value['so_lan_thay_size'] = $so_lan_thay_size;
                        }

                        if (!empty($production_list_item['so_lan_rua_may'])) {
                            $so_lan_rua_may = $production_list_item['so_lan_rua_may'];
                            $value['so_lan_rua_may'] = $so_lan_rua_may;
                        }

                        if (!empty($production_list_item['thoi_gian_khac'])) {
                            $thoi_gian_khac = $production_list_item['thoi_gian_khac'];
                            $value['thoi_gian_khac'] = $thoi_gian_khac;
                        }

                        if (!empty($production_list_item['ngay_ve_nvl_du_kien'])) {
                            $ngay_ve_nvl_du_kien = $production_list_item['ngay_ve_nvl_du_kien'];
                            $value['ngay_ve_nvl_du_kien'] = _d($ngay_ve_nvl_du_kien);
                        }

                        if (!empty($production_list_item['ngay_bat_dau_du_kien'])) {
                            $ngay_bat_dau_du_kien = $production_list_item['ngay_bat_dau_du_kien'];
                            $value['ngay_bat_dau_du_kien'] = _d($ngay_bat_dau_du_kien);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_in'])) {
                            $ngay_hoan_thanh_in = $production_list_item['ngay_hoan_thanh_in'];
                            $value['ngay_hoan_thanh_in'] = _d($ngay_hoan_thanh_in);
                        }

                        if (!empty($production_list_item['may_in'])) {
                            $may_in = $production_list_item['may_in'];
                            $value['may_in'] = $may_in;
                        }

                        if (!empty($production_list_item['ngay_bat_dau_ke_hoach'])) {
                            $ngay_bat_dau_ke_hoach = $production_list_item['ngay_bat_dau_ke_hoach'];
                            $value['ngay_bat_dau_ke_hoach'] = _d($ngay_bat_dau_ke_hoach);
                        }

                        if (!empty($production_list_item['ngay_ket_thuc_ke_hoach'])) {
                            $ngay_ket_thuc_ke_hoach = $production_list_item['ngay_ket_thuc_ke_hoach'];
                            $value['ngay_ket_thuc_ke_hoach'] = _d($ngay_ket_thuc_ke_hoach);
                        }

                        if (!empty($production_list_item['ngay_bat_dau_thuc_te'])) {
                            $ngay_bat_dau_thuc_te = $production_list_item['ngay_bat_dau_thuc_te'];
                            $value['ngay_bat_dau_thuc_te'] = _d($ngay_bat_dau_thuc_te);
                        }

                        if (!empty($production_list_item['ngay_ket_thuc_thuc_te'])) {
                            $ngay_ket_thuc_thuc_te = $production_list_item['ngay_ket_thuc_thuc_te'];
                            $value['ngay_ket_thuc_thuc_te'] = _d($ngay_ket_thuc_thuc_te);
                        }

                        if (!empty($production_list_item['so_luong_thuc_te'])) {
                            $so_luong_thuc_te = $production_list_item['so_luong_thuc_te'];
                            $value['so_luong_thuc_te'] = $so_luong_thuc_te;
                        }

                        if (!empty($production_list_item['hoan_thanh'])) {
                            $hoan_thanh = $production_list_item['hoan_thanh'];
                            $value['hoan_thanh'] = $hoan_thanh;
                        }

                        if (!empty($production_list_item['ghi_chu'])) {
                            $ghi_chu = $production_list_item['ghi_chu'];
                            $value['ghi_chu'] = $ghi_chu;
                        }

                        if (!empty($production_list_item['loai_in_flexo'])) {
                            $loai_in_flexo = $production_list_item['loai_in_flexo'];
                            $value['loai_in_flexo'] = $loai_in_flexo;
                        }

                        if (!empty($production_list_item['dau_in'])) {
                            $dau_in = $production_list_item['dau_in'];
                            $value['dau_in'] = $dau_in;
                        }

                        if (!empty($production_list_item['ghi_chu_2'])) {
                            $ghi_chu_2 = $production_list_item['ghi_chu_2'];
                            $value['ghi_chu_2'] = $ghi_chu_2;
                        }

                        if (!empty($production_list_item['ghi_chu_2'])) {
                            $ghi_chu_2 = $production_list_item['ghi_chu_2'];
                            $value['ghi_chu_2'] = $ghi_chu_2;
                        }

                        if (!empty($production_list_item['so_lan_canh_dao'])) {
                            $so_lan_canh_dao = $production_list_item['so_lan_canh_dao'];
                            $value['so_lan_canh_dao'] = $so_lan_canh_dao;
                        }

                        if (!empty($production_list_item['so_mat_phun_bong'])) {
                            $so_mat_phun_bong = $production_list_item['so_mat_phun_bong'];
                            $value['so_mat_phun_bong'] = $so_mat_phun_bong;
                        }

                        if (!empty($production_list_item['so_lan_phun_bong'])) {
                            $so_lan_phun_bong = $production_list_item['so_lan_phun_bong'];
                            $value['so_lan_phun_bong'] = $so_lan_phun_bong;
                        }

                        if (!empty($production_list_item['loai_boi'])) {
                            $loai_boi = $production_list_item['loai_boi'];
                            $value['loai_boi'] = $loai_boi;
                        }

                        if (!empty($production_list_item['so_lan_van_hanh'])) {
                            $so_lan_van_hanh = $production_list_item['so_lan_van_hanh'];
                            $value['so_lan_van_hanh'] = $so_lan_van_hanh;
                        }

                        if (!empty($production_list_item['loai_giay'])) {
                            $loai_giay = $production_list_item['loai_giay'];
                            $value['loai_giay'] = $loai_giay;
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_in'])) {
                            $ngay_hoan_thanh_in = $production_list_item['ngay_hoan_thanh_in'];
                            $value['ngay_hoan_thanh_in'] = _d($ngay_hoan_thanh_in);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_bong'])) {
                            $ngay_hoan_thanh_bong = $production_list_item['ngay_hoan_thanh_bong'];
                            $value['ngay_hoan_thanh_bong'] = _d($ngay_hoan_thanh_bong);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_can_mang'])) {
                            $ngay_hoan_thanh_can_mang = $production_list_item['ngay_hoan_thanh_can_mang'];
                            $value['ngay_hoan_thanh_can_mang'] = _d($ngay_hoan_thanh_can_mang);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_boi'])) {
                            $ngay_hoan_thanh_boi = $production_list_item['ngay_hoan_thanh_boi'];
                            $value['ngay_hoan_thanh_boi'] = _d($ngay_hoan_thanh_boi);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_lua'])) {
                            $ngay_hoan_thanh_lua = $production_list_item['ngay_hoan_thanh_lua'];
                            $value['ngay_hoan_thanh_lua'] = _d($ngay_hoan_thanh_lua);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_flexo'])) {
                            $ngay_hoan_thanh_flexo = $production_list_item['ngay_hoan_thanh_flexo'];
                            $value['ngay_hoan_thanh_flexo'] = _d($ngay_hoan_thanh_flexo);
                        }

                        if (!empty($production_list_item['ngay_hoan_thanh_hp'])) {
                            $ngay_hoan_thanh_hp = $production_list_item['ngay_hoan_thanh_hp'];
                            $value['ngay_hoan_thanh_hp'] = _d($ngay_hoan_thanh_hp);
                        }

                        if (!empty($production_list_item['so_duong_dao_cat'])) {
                            $so_duong_dao_cat = $production_list_item['so_duong_dao_cat'];
                            $value['so_duong_dao_cat'] = $so_duong_dao_cat;
                        }

                        if (!empty($production_list_item['ngay_ket_thuc'])) {
                            $ngay_ket_thuc = $production_list_item['ngay_ket_thuc'];
                            $value['ngay_ket_thuc'] = _d($ngay_ket_thuc);
                        }

                        if (!empty($production_list_item['thoi_gian_canh_bai_thuc_te'])) {
                            $thoi_gian_canh_bai_thuc_te = $production_list_item['thoi_gian_canh_bai_thuc_te'];
                            $value['thoi_gian_canh_bai_thuc_te'] = $thoi_gian_canh_bai_thuc_te;
                        }

                        if (!empty($production_list_item['npl_canh_bai_thuc_te'])) {
                            $npl_canh_bai_thuc_te = $production_list_item['npl_canh_bai_thuc_te'];
                            $value['npl_canh_bai_thuc_te'] = $npl_canh_bai_thuc_te;
                        }
                    }
                }

                $nang_suat = 0;
                $tong_tua = 0;
                $thoi_gian_in = 0;
                $tua_sau_in = 0;
                $thoi_gian_xu_ly = 0;
                if ($type_productionlist_id == 1) {
                    $tong_tua = $so_mat_in * $to_in;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $nang_suat = $nang_suat_may;
                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                    $tua_sau_in = ($so_con_tren_kb_offset > 0 ? ($so_con_tren_to_in / $so_con_tren_kb_offset * $to_in) : $to_in);
                }

                //type_productionlist_id = 2
                $so_luong_san_xuat = !empty($value['so_luong_san_xuat']) ? number_unformat($value['so_luong_san_xuat']) : 0;
                $so_con_tren_kb_flexo = !empty($value['so_con_tren_kb_flexo']) ? number_unformat($value['so_con_tren_kb_flexo']) : 0;
                $so_tua_in_flexo = 0;
                $loai_in_flexo = !empty($value['loai_in_flexo']) ? number_unformat($value['loai_in_flexo']) : 0;
                if ($type_productionlist_id == 2) {
                    // $tong_tua = $so_mat_in * $to_in;

                    $so_tua_in_flexo = 0;
                    if ($so_con_tren_kb_flexo > 0) {
                        $so_tua_in_flexo = $so_luong_san_xuat / $so_con_tren_kb_flexo;
                    }

                    $thoi_gian_in = 0;
                    $nang_suat_may = $nang_suat_may;
                    if ($loai_in_flexo == 2) {
                        $nang_suat_may = $nang_suat_may_uv;
                    }

                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $so_tua_in_flexo / $nang_suat_may;
                    }

                    $nang_suat = $nang_suat_may;
                    $tong_tua = $so_tua_in_flexo;
                    // $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai;
                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                //type_productionlist_id = 3
                $dau_in = !empty($value['dau_in']) ? number_unformat($value['dau_in']) : 0;
                $so_tua_in = 0;
                if ($type_productionlist_id == 3) {
                    $nang_suat = ($dau_in == 300) ? $nang_suat_may_in_300 : $nang_suat_may_in_600;
                    $so_tua_in = $so_luong_san_xuat;

                    $thoi_gian_in = 0;
                    if ($nang_suat > 0) {
                        $thoi_gian_in = $so_tua_in / $nang_suat;
                    }

                    $tong_tua = $so_tua_in;
                    // $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
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
                    // $so_tua_in = $to_in * $so_mat_in;
                    // $nang_suat = $nang_suat_may;

                    // $thoi_gian_xu_ly = 0;
                    // if ($nang_suat > 0) {
                    //     $thoi_gian_xu_ly = $so_tua_in / $nang_suat;
                    // }

                    // $thoi_gian_canh_bai = $_thoi_gian_canh_bai;
                    // $tong_thoi_gian = $thoi_gian_xu_ly + $thoi_gian_canh_bai;

                    $tong_tua = $so_mat_in * $to_in;
                    $nang_suat = $nang_suat_may;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                if ($type_productionlist_id == 10) {
                    //định hình
                    $tong_tua = $so_mat_in * $to_in;
                    $nang_suat = $nang_suat_may;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                $so_lan_canh_dao = !empty($value['so_lan_canh_dao']) ? number_unformat($value['so_lan_canh_dao']) : 0;
                if ($type_productionlist_id == 11) {
                    //cắt demi
                    $tong_tua = $so_mat_in * $to_in;
                    $nang_suat = $nang_suat_may;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                    // print_arrays($thoi_gian_in, '<br>', $thoi_gian_canh_bai, '<br>', $thoi_gian_khac, '<br>', $thoi_gian_thay_size, '<br>', $thoi_gian_rua_may);
                }

                if ($type_productionlist_id == 12) {
                    //Cán băng kep
                    $tong_tua = $so_mat_in * $to_in;
                    $nang_suat = $nang_suat_may;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                //type_productionlist_id = 7 - Phun bóng
                $boi = !empty($value['boi']) ? $value['boi'] : '';
                $be_xa_khoan_lo_2 = !empty($value['be_xa_khoan_lo_2']) ? $value['be_xa_khoan_lo_2'] : '';
                $so_mat_phun_bong = !empty($value['so_mat_phun_bong']) ? number_unformat($value['so_mat_phun_bong']) : 0;
                $so_lan_phun_bong = !empty($value['so_lan_phun_bong']) ? number_unformat($value['so_lan_phun_bong']) : 0;
                if ($type_productionlist_id == 7) {
                    $so_tua_in = $to_in * $so_mat_phun_bong * $so_lan_phun_bong;
                    $tong_tua =  $so_tua_in;
                    $nang_suat = $loai == 1 ? $nang_suat_may_tu_dong : $nang_suat_may_dat_tay;

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                //type_productionlist_id = 8 - Bồi
                $so_con_tren_kb = !empty($value['so_con_tren_kb']) ? number_unformat($value['so_con_tren_kb']) : 0;
                $loai_boi = !empty($value['loai_boi']) ? number_unformat($value['loai_boi']) : 0;
                $so_lan_van_hanh = !empty($value['so_lan_van_hanh']) ? number_unformat($value['so_lan_van_hanh']) : 0;
                if ($type_productionlist_id == 8) {
                    $nang_suat = 0;

                    $tong_tua = $to_in * $so_lan_van_hanh;
                    $so_tua_in = $tong_tua;

                    if ($loai_boi == 2 || $loai_boi == '2') {
                        $nang_suat = $nang_suat_may_boi_hai_mat;
                    } else {
                        $nang_suat = $nang_suat_may_boi_mot_mat;
                    }

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
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
                    //bế
                    $tong_tua = 0;
                    // if ($so_con_tren_kb > 0) {
                        // $tong_tua = $so_con_tren_to_in / $so_con_tren_kb * $to_in;
                        $tong_tua = $to_in * $so_lan_van_hanh;
                        $nang_suat = 0;
                        // if (strtoupper($loai_giay) == 'THƯỜNG' || $loai_giay == 'thường' || $loai_giay == 'Thường') {
                        if ($loai_giay == 1) {
                            $nang_suat = $nang_suat_may_be_giay_thuong;
                        } else {
                            $nang_suat = $nang_suat_may_demi_be_giay_boi_pet;
                        }

                        if ($nang_suat > 0) {
                            $thoi_gian_in = $tong_tua / $nang_suat;
                        }

                        $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                    // }
                }

                //type_productionlist_id = 13 - Xả TP
                $so_duong_dao_cat = !empty($value['so_duong_dao_cat']) ? number_unformat($value['so_duong_dao_cat']) : 0;
                if ($type_productionlist_id == 13) {
                    //Xả TP
                    $tong_tua = 0;
                    $nang_suat = 0;

                    $tong_tua = $to_in * $so_duong_dao_cat;
                    if ($loai_giay == 1) {
                        $nang_suat = $nang_suat_may_be_giay_thuong;
                    } else {
                        $nang_suat = $nang_suat_may_demi_be_giay_boi_pet;
                    }

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                //type_productionlist_id = 14 - Khoan lỗ
                if ($type_productionlist_id == 14) {
                    //Khoan lỗ
                    $tong_tua = 0;
                    $nang_suat = $nang_suat_may;
                    $tong_tua = $so_luong_san_xuat;

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                if ($type_productionlist_id == 15) {
                    //Gở bế
                    $tong_tua = 0;
                    $nang_suat = $nang_suat_may;
                    $tong_tua = $so_luong_san_xuat;

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                if ($type_productionlist_id == 16) {
                    //Soạn
                    $tong_tua = 0;
                    $nang_suat = $nang_suat_may;
                    $tong_tua = $so_luong_san_xuat;

                    if ($nang_suat > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat;
                    }

                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                }

                if ($type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) {
                    // HP
                    $tong_tua = $so_mat_in * $to_in;
                    if ($nang_suat_may > 0) {
                        $thoi_gian_in = $tong_tua / $nang_suat_may;
                    }

                    $nang_suat = $nang_suat_may;
                    $thoi_gian_xu_ly = $thoi_gian_in + $thoi_gian_canh_bai + $thoi_gian_khac + $thoi_gian_thay_size + $thoi_gian_rua_may;
                    $tua_sau_in = ($so_con_tren_kb_offset > 0 ? ($so_con_tren_to_in / $so_con_tren_kb_offset * $to_in) : $to_in);
                }

                $stage_id = !empty($value['stage_id']) ? ($value['stage_id']) : 0;
                $ngay_ket_thuc = !empty($value['ngay_ket_thuc']) ? to_sql_date($value['ngay_ket_thuc']) : null;
                $_key = !empty($value['_key']) ? $value['_key'] : 0;

                $_index = $po_id.'__'.$item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
                $id_pli = $productionListItems[$_index]['id'] ?? 0;
                if ($id_pli) {
                    $arrDeleteProductionListsItems[] = $id_pli;
                }

                $arrItems[] = [
                    'id' => $id_pli,
                    'production_list_id' => 0,
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
                    'thoi_gian_khac' => $thoi_gian_khac,
                    'face' => $face,
                    'face_after' => $face_after,

                    'mat' => $mat,
                    'thoi_gian_thay_size' => $thoi_gian_thay_size,
                    'thoi_gian_rua_may' => $thoi_gian_rua_may,
                    'loai_canh_bai' => $loai_canh_bai,
                    'so_lan_thay_size' => $so_lan_thay_size,
                    'so_lan_rua_may' => $so_lan_rua_may,
                    '_key' => $_key,
                    'to_in_bu_hao' => $to_in_bu_hao,
                    'ngay_now' => $ngay_now,
                    'loai_in_flexo' => $loai_in_flexo,
                    'so_lan_canh_dao' => $so_lan_canh_dao,
                    'so_lan_van_hanh' => $so_lan_van_hanh,
                    'so_duong_dao_cat' => $so_duong_dao_cat,
                    'so_lan_phun_bong' => $so_lan_phun_bong,

                    // 'ngay_bat_dau_thuc_te' => !empty($dtListItem['ngay_bat_dau_thuc_te']) ? $dtListItem['ngay_bat_dau_thuc_te'] : null,
                    // 'ngay_ket_thuc_thuc_te' => !empty($dtListItem['ngay_ket_thuc_thuc_te']) ? $dtListItem['ngay_ket_thuc_thuc_te'] : null,
                    // 'so_luong_thuc_te' => !empty($dtListItem['so_luong_thuc_te']) ? $dtListItem['so_luong_thuc_te'] : 0,
                    // 'ngay_bat_dau_ke_hoach' => !empty($dtListItem['ngay_bat_dau_ke_hoach']) ? $dtListItem['ngay_bat_dau_ke_hoach'] : null,
                    // 'ngay_ket_thuc_ke_hoach' => !empty($dtListItem['ngay_ket_thuc_ke_hoach']) ? $dtListItem['ngay_ket_thuc_ke_hoach'] : null,
                    'status' => !empty($dtListItem['status']) ? $dtListItem['status'] : 1,

                    'ngay_bat_dau_thuc_te' => $ngay_bat_dau_thuc_te,
                    'ngay_ket_thuc_thuc_te' => $ngay_ket_thuc_thuc_te,
                    'thoi_gian_canh_bai_thuc_te' => $thoi_gian_canh_bai_thuc_te,
                    'npl_canh_bai_thuc_te' => $npl_canh_bai_thuc_te,
                    'so_luong_thuc_te' => $so_luong_thuc_te,
                    'ngay_bat_dau_ke_hoach' => $ngay_bat_dau_ke_hoach,
                    'ngay_ket_thuc_ke_hoach' => $ngay_ket_thuc_ke_hoach,
                ];

                // print_arrays($arrItems);

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

        if (!empty($production_lists_total_end)) {
            $rs = $this->production_list_model->updateProductionListsTotal($production_lists_total_end['id'], $production_lists_total);
            if ($rs) {
                $production_list_total_id = $production_lists_total_end['id'];
            }
        } else {
            $production_list_total_id = $this->production_list_model->insertProductionListsTotal($production_lists_total);
        }

        if ($production_list_total_id) {
            $production_list_id = 0;

            if (!empty($arrDeleteProductionListsItems)) {
                $this->production_list_model->deleteProductionListsItemsListId($arrDeleteProductionListsItems);
            }

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
                $arrDateDeleted = [];
                foreach ($arrProductionsListsDate as $key => $value) {
                    $arrDateDeleted[] = $value['date_handling'];

                    $arrListsDate[$i] = $value;
                    $arrListsDate[$i]['production_list_id'] = $production_list_id;
                    $arrListsDate[$i]['production_list_total_id'] = $production_list_total_id;
                    $i++;
                }

                if (!empty($arrDateDeleted)) {
                    $this->production_list_model->deleteProductionListsInDate($arrDateDeleted);
                }

                $this->production_list_model->insertBatchProductionListsDate($arrListsDate);
            }

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }

        echo responseData($data);
    }

    public function export_excel_moderation_plan_new()
    {
        $data = [];
        if (!$this->perViewProductionList) {
            echo responseData(['result' => 0, 'message' => lang('access_denied')]);
            die;
        }

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

        $styleTitle = [
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
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
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

        $styleSum = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 13,
                'name' => 'Times New Roman'
            ),
        ];


        $worksheet = $objPHPExcel->getActiveSheet();
        $excel = cloumns_excel();
        $iExcel = 0;
        $rowBegin = 0;

        $productions_orders_search = $this->input->post('productions_orders_search');
        $group = $this->input->post('group');
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $status_filter = $this->input->post('status_filter');
        $date_start_expected = $this->input->post('date_start_expected');
        $date_end_expected = $this->input->post('date_end_expected');
        $date_start_finished = $this->input->post('date_start_finished');
        $date_end_finished = $this->input->post('date_end_finished');
        if (empty($date_start) || empty($date_end)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn ngày bắt đầu và kết thúc');
            echo responseData($data); die;
        }

        $_date_start = to_sql_date($date_start);
        $_date_end = to_sql_date($date_end);

        $this->db->select('
            tbl_category_stages.*,
            tbl_type_productionlist.code as code_type_productionlist
        ', false);
        $this->db->from('tbl_category_stages');
        $this->db->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id');
        $this->db->where('tbl_category_stages.id', $group);
        $dtCategoryStages = $this->db->get()->row_array();

        if (empty($dtCategoryStages)) {
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy loại nhóm công đoạn');
            echo responseData($data); die;
        }

        $category_stage_id = $dtCategoryStages['id'];
        $type_productionlist_id = $dtCategoryStages['type_productionlist_id'];
        $this->db->dbprefix  = '';

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.items_id, 
                tbl_productions_orders_items.productions_orders_id, 
                tbl_productions_plan.note as note_plan,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id
    
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            WHERE 1 
            GROUP BY tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $this->db->select('
            tbl_productions_orders.id as id,
            tbl_productions_orders.date as date,
            tb_production_order_item.items_id, 
            tb_production_order_item.productions_orders_id, 
            tbl_productions_orders_items_stages.stage_id,
            tbl_productions_orders_items_stages.face, 
            tbl_productions_orders_items_stages.face_after,
            tbl_productions_orders.reference_no as reference_no_po,
            tbl_products.id as item_id,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_products.images as images,
            tbl_stages.name as name_stage,
            (tb_production_order_item.quantity) as quantity,
            tb_production_order_item.plan_id as plan_id,
            tbl_productions_orders_items_stages.number_face as number_face,
            tbl_productions_orders_items_stages.number_operations as number_operations,
            tbl_productions_orders_items_stages.number_cutting as number_cutting,
            tbl_productions_orders_items_stages.quota_time_f1 as quota_time_f1,
            tbl_productions_orders_items_stages.quota_time_f2 as quota_time_f2,
            tbl_products.quantity_child_sheet as quantity_child_sheet,
            tbl_products.quantity_child_molds_offset as quantity_child_molds_offset,
            tbl_products.quantity_child_molds_flexo as quantity_child_molds_flexo,
            tbl_products.quantity_child_molds as quantity_child_molds,
            tb_production_order_item.note_plan as note_plan,
            tbl_productions_orders.date_npl as date_npl,
        ');
        $this->db->from('tbl_productions_orders');
        $this->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id');
        $this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->where('tbl_productions_orders.date >=', $_date_start.' 00:00:00');
        $this->db->where('tbl_productions_orders.date <=', $_date_end.' 23:59:59');
        $this->db->where('tbl_category_stages.id', $group);
        $this->db->where('tbl_category_stages.type_productionlist_id', $type_productionlist_id);

        if (!empty($productions_orders_search)) {
            $this->db->where_in('tbl_productions_orders.id', $productions_orders_search);
        }

        if ($type_productionlist_id == 1) {
            // $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0)', false, false);
            $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
            $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
        } else if ($type_productionlist_id == 2) {
            $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
            $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
        } else if ($type_productionlist_id == 2 || $type_productionlist_id == 3 || $type_productionlist_id == 7 || $type_productionlist_id == 6 || $type_productionlist_id == 10 || $type_productionlist_id == 11 || $type_productionlist_id == 12 || $type_productionlist_id == 8 || $type_productionlist_id == 9 || $type_productionlist_id == 13 || $type_productionlist_id == 14  || $type_productionlist_id == 15  || $type_productionlist_id == 16) {
            // $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id');

            $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
            $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
        }  else if ($type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) {
            $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
            $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
        }

        $whereProductionListsItems = '';
        if (!empty($status_filter) && $status_filter != 'ALL') {
            if ($status_filter == 'CHT') {
                $this->db->where(' NOT EXISTS (
                    SELECT 1
                    FROM tbl_production_lists_items
                    WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id AND tbl_productions_orders_items_stages.face = tbl_production_lists_items.face AND tbl_productions_orders_items_stages.face_after =tbl_production_lists_items.face_after AND tbl_production_lists_items.hoan_thanh = "HT"
                )', false, false);
            } else if ($status_filter == 'HT') {
                $this->db->where(' EXISTS (
                    SELECT 1
                    FROM tbl_production_lists_items
                    WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id AND tbl_productions_orders_items_stages.face = tbl_production_lists_items.face AND tbl_productions_orders_items_stages.face_after =tbl_production_lists_items.face_after AND tbl_production_lists_items.hoan_thanh = "HT"
                )', false, false);
            }
        }

        if (!empty($date_start_expected)) {
            $date_start_expected = to_sql_date($date_start_expected);
            $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_bat_dau_du_kien >= "'.$date_start_expected.'"';
        }

        if (!empty($date_end_expected)) {
            $date_end_expected = to_sql_date($date_end_expected);
            $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_bat_dau_du_kien <= "'.$date_end_expected.'"';

        }

        if (!empty($date_start_finished)) {
            $date_start_finished = to_sql_date($date_start_finished);
            $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_hoan_thanh_in >= "'.$date_start_finished.'"';
        }

        if (!empty($date_end_finished)) {
            $date_end_finished = to_sql_date($date_end_finished);
            $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_hoan_thanh_in <= "'.$date_end_finished.'"';
        }

        if (!empty($whereProductionListsItems)) {
            $this->db->where(' EXISTS (
                SELECT 1
                FROM tbl_production_lists_items
                WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id '.$whereProductionListsItems.'
            )', false, false);
        }

        $this->db->order_by('tbl_productions_orders.id ASC, tbl_productions_orders_items_stages.id ASC');
        $productions_orders_items = $this->db->get()->result_array();

        $number = 0;
        $rowTemBegin = 0;
        if (!empty($productions_orders_items)) {
            $arrPOID = [];
            $arrItemID = [];
            $arrStageID = [];
            $arrPlanID = [];
            foreach ($productions_orders_items as $kOI => $vOI) {
                $arrPOID[] = $vOI['productions_orders_id'];
                $arrItemID[] = $vOI['items_id'];
                $arrStageID[] = $vOI['stage_id'];
                $arrPlanID[] = $vOI['plan_id'];
            }

            if (!empty($arrPOID)) {
                $arrPOID = array_unique($arrPOID);
                $arrItemID = array_unique($arrItemID);
                $arrStageID = array_unique($arrStageID);
                $arrPlanID = array_unique($arrPlanID);

                //BOM
                $this->db->select('
                    tbl_productions_orders_items.productions_orders_id, 
                    ppb_materials.item_type as type, 
                    ppb_materials.item_id, 
                    ppb_materials.landscape_print_size, 
                    ppb_materials.number_children_size, 
                    ppb_materials.unit_parent_id, 
                    SUM(ppb_materials.quantity) as quantity,
                    ppb_materials.quantity_single as quantity_single,
                ', false);
                $this->db->from('tbl_productions_plan_bom ppb_primary');
                $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
                $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
                $this->db->where_in('tbl_productions_orders_items.productions_orders_id', $arrPOID);
                $this->db->where('ppb_primary.parent_id', 0);
                // $this->db->where_in('tbl_productions_orders_items.items_id', $arrItemID);
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

                $this->db->group_by('tbl_productions_orders_items.productions_orders_id, ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
                $listBom = $this->db->get()->result_array();
                if ($listBom) {
                    $listBom = array_reduce($listBom, function($carry, $item) {
                        $carry[$item['productions_orders_id']][] = $item;
                        return $carry;
                    });
                }

                //danh sách kẽm
                if ($arrPlanID) {
                    $this->db->select('
                        tbl_productions_plan_compensation.productions_plan_id as plan_id,
                        SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
                    ', false);
                    $this->db->from('tbl_productions_plan_compensation');
                    $this->db->where('tbl_productions_plan_compensation.is_zinc', 1);
                    $this->db->where_in('tbl_productions_plan_compensation.productions_plan_id', $arrPlanID);
                    $this->db->group_by('tbl_productions_plan_compensation.productions_plan_id');
                    $listPlanZinc = $this->db->get()->result_array();
                    if ($listPlanZinc) {
                        $listPlanZinc = array_reduce($listPlanZinc, function($carry, $item) {
                            $carry[$item['plan_id']][] = $item;
                            return $carry;
                        });
                    }
                }

                //ngày giao hàng hệ thống
                $tbDateDelivery = "
                    SELECT
                        tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                        MIN(tbl_productions_plan_details.date) as date_shipping
                    FROM tbl_productions_plan_items
                    INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
                    JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
                    WHERE tbl_productions_plan_items.is_preventive = 0 AND tbl_productions_orders_items.productions_orders_id IN (".implode(',', $arrPOID).")
                    GROUP BY tbl_productions_orders_items.productions_orders_id
                ";
                $listDateDelivery = $this->db->query($tbDateDelivery)->result_array();
                if ($listDateDelivery) {
                    $listDateDelivery = array_reduce($listDateDelivery, function($carry, $item) {
                        $carry[$item['productions_orders_id']] = $item;
                        return $carry;
                    });
                }
            }

            $productionListItems = $this->production_list_model->getProductionListsItemsMul($arrPOID, true);

            $temp_production_lists_total = $this->production_list_model->rowProductionListsTotalDateEnd($_date_end, $type_productionlist_id, true);
            $production_lists_total = $temp_production_lists_total['production_lists_total'];
            if (empty($temp_production_lists_total['result']) && $production_lists_total['items_canh_bai']) {
                $timeCardAlignment = json_decode($production_lists_total['items_canh_bai'], true);
            }

            $productions_orders_items_new = [];
            foreach ($productions_orders_items as $item) {
                if ($item['face'] && $item['face_after'] && ($type_productionlist_id == 1 || $type_productionlist_id == 2 || $type_productionlist_id == 3 || $type_productionlist_id == 7 || $type_productionlist_id == 6 || $type_productionlist_id == 10 || $type_productionlist_id == 11 || $type_productionlist_id == 12 || $type_productionlist_id == 8 || $type_productionlist_id == 9 || $type_productionlist_id == 13 || $type_productionlist_id == 14  || $type_productionlist_id == 15  || $type_productionlist_id == 16 || $type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26)) {
                    $newItem = $item;
                    $newItem['face'] = 1;
                    $newItem['face_after'] = 0;
                    $newItem['_key'] = 1;
                    $productions_orders_items_new[] = $newItem;

                    $newItem = $item;
                    $newItem['face'] = 0;
                    $newItem['face_after'] = 2;
                    $newItem['_key'] = 2;
                    $productions_orders_items_new[] = $newItem;
                } else {
                    $productions_orders_items_new[] = $item;
                }
            }

            //
            $rowBegin = 1;
            $iExcel = -1;

            // $company_logo = get_option('company_logo');
            // $img = 'uploads/company/'.$company_logo;
            // $company_name = "CÔNG TY TRÁCH NHIỆM HỮU HẠN IN 3D THÀNH DANH\nTHANH DANH 3D PRINTING CO.,LTD";

            // $objRichText = new PHPExcel_RichText();
            // $fi = $objRichText->createTextRun('CÔNG TY TRÁCH NHIỆM HỮU HẠN ');
            // $fi->getFont()->setBold(true);
            // $fi->getFont()->setSize(16);

            // $boldPart = $objRichText->createTextRun('IN 3D THÀNH DANH');
            // $boldPart->getFont()->setBold(true);
            // $boldPart->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));
            // $boldPart->getFont()->setSize(15);

            // $objRichText->createText("\n");
            // $coloredPart = $objRichText->createTextRun('THANH DANH 3D PRINTING CO.,LTD');
            // $coloredPart->getFont()->setBold(true);
            // $coloredPart->getFont()->setColor(new PHPExcel_Style_Color('FF800080')); // Màu xanh
            // $coloredPart->getFont()->setSize(15);

            // // Gán nội dung Rich Text vào một ô
            // $sheet = $objPHPExcel->getActiveSheet();
            // $cellCoordinate = 'C1'; // Chọn ô bạn muốn chèn
            // $sheet->getCell($cellCoordinate)->setValue($objRichText);
            // // Định dạng ô để nội dung xuống dòng tự động
            // $sheet->getStyle($cellCoordinate)->getAlignment()->setWrapText(true);
            // $objPHPExcel->getActiveSheet()->mergeCells('C1:'.$excel[$iExcel + 20].'2');
            // $objPHPExcel->getActiveSheet()->getStyle($cellCoordinate)->applyFromArray([
            //     'font' => array(
            //         'bold' => true,
            //         'size' => 16,

            //     ),
            //     'alignment' => array(
            //         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            //         'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            //     )
            // ]);
            // // Tùy chỉnh chiều cao của hàng để phù hợp nội dung
            // $sheet->getRowDimension('1')->setRowHeight(20);

            // if (file_exists($img)) {
            //     $objDrawing = new PHPExcel_Worksheet_Drawing();
            //     $objDrawing->setName($company_logo);
            //     $objDrawing->setDescription('Image');
            //     $objDrawing->setPath($img);
            //     $objDrawing->setCoordinates('A1');
            //     $objDrawing->setWidth(100);
            //     // $objDrawing->setHeight(15);
            //     // $objDrawing->setOffsetX(10);
            //     // $objDrawing->setOffsetY(2);
            //     $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            //     $objPHPExcel->getActiveSheet()->mergeCells('A1:B4');
            // }
            insertCompanyInfo($objPHPExcel);

            // ++$iExcel;
            $objPHPExcel->getActiveSheet()->setCellValue('C3', 'KẾ HOẠCH ĐIỀU ĐỘ CÔNG ĐOẠN NHÓM '.$dtCategoryStages['code'].': '.$dtCategoryStages['code_type_productionlist']);
            $objPHPExcel->getActiveSheet()->mergeCells('C3:'.$excel[$iExcel + 20].'4');
            $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 16,

                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);

            ++$iExcel;
            ++$iExcel;
            $rowBegin = $rowBegin + 4;
            //items
            $listMachines = [];
            if (!empty($productions_orders_items_new)) {
                foreach ($productions_orders_items_new as $kOI => $vOI) {
                    $po_id = $vOI['id'];
                    $plan_id = $vOI['plan_id'];
                    $bom = $listBom[$po_id] ?? null;
                    $_item_id = $vOI['item_id'];
                    $stage_id = $vOI['stage_id'];

                    $face = $vOI['face'];
                    $face_after = $vOI['face_after'];
                    $countFace = 0;
                    $mat = '';
                    if ($face > 0) {
                        $countFace++;
                        $mat = 'A';
                    }

                    if ($face_after > 0) {
                        $countFace++;
                        $mat = 'B';
                    }

                    $_key = !empty($vOI['_key']) ? $vOI['_key'] : 0;

                    $_index = $po_id.'__'.$_item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
                    $dtProductionListsItem = $productionListItems[$_index] ?? null;
                    if (!empty($dtProductionListsItem)) {
                        $may_in = $dtProductionListsItem['may_in'];
                        $ngay_bat_dau_du_kien = $dtProductionListsItem['ngay_bat_dau_du_kien'];

                        if ($ngay_bat_dau_du_kien && $may_in) {
                            if (empty($listMachines[$may_in])) {
                                $listMachines[$may_in] = [
                                    'name_machine' => $dtProductionListsItem['name_machine'],
                                    'tong_tua' => $dtProductionListsItem['tong_tua'],
                                    'thoi_gian_in' => $dtProductionListsItem['thoi_gian_xu_ly'],
                                ];
                            } else {
                                $listMachines[$may_in]['tong_tua'] = $listMachines[$may_in]['tong_tua'] + $dtProductionListsItem['tong_tua'];
                                $listMachines[$may_in]['thoi_gian_in'] = $listMachines[$may_in]['thoi_gian_in'] + $dtProductionListsItem['thoi_gian_xu_ly'];
                            }
                        }
                    }
                }
            }

            $now = date('d/m/Y');
            if (!empty($listMachines)) {
                foreach ($listMachines as $kL => $vL) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[2].$rowBegin, $vL['name_machine']);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[5].$rowBegin, $vL['tong_tua'])->getStyle($excel[5].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($vL['tong_tua'], 3));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[6].$rowBegin, 'Tua');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[7].$rowBegin, 'Ca:');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[8].$rowBegin, $vL['thoi_gian_in'])->getStyle($excel[8].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($vL['thoi_gian_in'], 3));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[9].$rowBegin, 'tiếng');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[10].$rowBegin, 'Ngày:');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[11].$rowBegin, $now);
                    $objPHPExcel->getActiveSheet()->getStyle($excel[2].$rowBegin.':'.$excel[12].$rowBegin)->applyFromArray([
                        'font' => array(
                            'bold' => true
                        ),
                    ]);
                }
            }

            if ($type_productionlist_id == 1 || $type_productionlist_id == 2 || $type_productionlist_id == 3 || $type_productionlist_id == 7 || $type_productionlist_id == 6 || $type_productionlist_id == 10 || $type_productionlist_id == 11 || $type_productionlist_id == 12 || $type_productionlist_id == 8 || $type_productionlist_id == 9 || $type_productionlist_id == 13 || $type_productionlist_id == 14  || $type_productionlist_id == 15  || $type_productionlist_id == 16 || $type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) {
                $rowBegin++;
                $rowTemBegin = $rowBegin;
                $iExcel = -1;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'STT');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(5);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'ID');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(10);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Mã LSX');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(18);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Mã sản phẩm');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(30);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Hình ảnh đại diện');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Công đoạn');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(30);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Tổng số con');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày giao hàng dự kiến');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Số con/tờ');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Tổng số');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[($iExcel + 1)].$rowBegin);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel].($rowBegin + 1), 'Tờ in');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'Tờ bù hao theo LSX');

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Mặt in');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                if ($type_productionlist_id == 2) {
                    //flexo
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Số con/khuôn bế');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(10);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Loại in Flexo');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Tổng tua');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(10);
                } 
                
                if ($type_productionlist_id == 1) {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Số mặt in');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Tổng tua');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thời gian in');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Loại');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thời gian canh bài');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Số lần thay size');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Số lần rửa máy');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thời gian khác');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thời gian xử lý');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày mở lệnh');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày GH hệ thống');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thời gian còn lại');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Phiếu PTM');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Mẫu Sản Xuất');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Layout Ghép');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Khuân Bế');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'NPL');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Vật Tư');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Phiếu Cắt Giấy');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày về NVL dự kiến');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày bắt đầu dự kiến');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày hoàn thành in');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);
                    
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'SL Tay thay size');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Tổng TG dự kiến');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày bắt đầu sản xuất dự kiến');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày về NVL dự kiến');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ngày hoàn thành in');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(12);
                }


                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Kế hoạch');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[($iExcel + 1)].$rowBegin);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel].($rowBegin + 1), 'Bắt đầu');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'Kết thúc');

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thực tế');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[($iExcel + 4)].$rowBegin);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel].($rowBegin + 1), 'Bắt đầu');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'Kết thúc');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'Thời gian canh bài thực tế');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'NPL canh bài thực tế');
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].($rowBegin + 1), 'Số lượng thực tế');

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Ghi chú');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(20);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Thợ ký tên');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'QA xác nhận');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Máy in dự kiến');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'Trạng thái');
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1));
                $objPHPExcel->getActiveSheet()->getColumnDimension($excel[$iExcel])->setWidth(8);

                $objPHPExcel->getActiveSheet()->getStyle($excel[0].$rowBegin.':'.$excel[$iExcel].($rowBegin + 1))->applyFromArray([
                    'font' => array(
                        'color' => array('rgb' => 'FFFFFF'),
                        'bold' => true
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '3f51b5'),
                        'bold' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    )
                ]);

                $rowBegin++;
                if (!empty($productions_orders_items_new)) {
                    foreach ($productions_orders_items_new as $kOI => $vOI) {
                        $po_id = $vOI['id'];
                        $plan_id = $vOI['plan_id'];
                        $bom = $listBom[$po_id] ?? null;
                        $_item_id = $vOI['item_id'];
                        $name_stage = $vOI['name_stage'];
                        $stage_id = $vOI['stage_id'];

                        $arrCountItems = [];
                        if (FIX_QUANTITY_COMPENSATION) {
                            if (!empty($bom)) {
                                foreach ($bom as $kB => $vB) {
                                    $strKey = $vB['type'] . '__' . $vB['item_id'];
                                    if (!empty($arrCountItems[$strKey])) {
                                        $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                                    } else {
                                        $arrCountItems[$strKey]['count'] = 1;
                                        $arrCountItems[$strKey]['decimal'] = 0;
                                    }
                                }
                            }
                        }

                        $total_paper_exchange = 0;
                        $total_quantity_compensation = 0;
                        $quantity_zinc = 0;
                        $number_children_size = 0;

                        if (!empty($bom)) {
                            foreach ($bom as $kB => $vB) {
                                $item_id = $vB['item_id'];
                                $type = $vB['type'];
                                $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                                $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
            
                                if ($type == 'materials' && empty($number_children_size)) {
                                    $number_children_size = $vB['number_children_size'];
                                }
            
                                //fix quantity compensation
                                if (FIX_QUANTITY_COMPENSATION) {
                                    $strKey = $vB['type'] . '__' . $vB['item_id'];
                                    $count_item = $arrCountItems[$strKey]['count'];
                                    $division = $quantity_compensation / $count_item;
                                    if (is_decimal($division)) {
                                        if ($arrCountItems[$strKey]['decimal']) {
                                            $quantity_compensation = floor($division);
                                        } else {
                                            $arrCountItems[$strKey]['decimal'] = 1;
                                            $quantity_compensation = ceil($division);
                                        }
                                    } else {
                                        $quantity_compensation = $division;
                                    }
                                }
                                //
            
                                $quantity = ceil(round($vB['quantity'], 4));
                                $quantity_single = $vB['quantity_single'];
                                $quantity_need = $quantity + $quantity_compensation;
                                $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                                $total_paper_exchange += $paper_exchange;
            
                                $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                                $total_quantity_compensation += $quantity_compensation;
                            }
                        }

                        //tờ in
                        $so_to_in = $total_paper_exchange;

                        //mặt in
                        $face = $vOI['face'];
                        $face_after = $vOI['face_after'];
                        $countFace = 0;
                        $mat = '';
                        if ($face > 0) {
                            $countFace++;
                            $mat = 'A';
                        }

                        if ($face_after > 0) {
                            $countFace++;
                            $mat = 'B';
                        }

                        if (empty($countFace)) $countFace = 1;

                        //số lượng kẽm
                        $dtZinc = $listPlanZinc[$plan_id] ?? null;
                        $quantity_zinc = 0;
                        if (!empty($dtZinc)) {
                            $quantity_zinc = $dtZinc['quantity_compensation'] ?? 0;
                        }

                        $ngay_mo_lenh_sx = date_format(date_create($vOI['date']), 'd/m/Y');

                        $dtDateDelivery = $listDateDelivery[$po_id] ?? null;
                        $ngay_giao_hang = !empty($dtDateDelivery['date_shipping']) ? _d($dtDateDelivery['date_shipping']) : '';

                        //số con tờ tin
                        $so_con_tren_to_in = $vOI['quantity_child_sheet'];
                        $so_con_tren_kb_flexo = $vOI['quantity_child_molds_flexo'];
                        $so_con_tren_kb_offset = $vOI['quantity_child_molds_offset'];
                        $so_con_tren_kb = $vOI['quantity_child_molds'];
                        $note_plan = $vOI['note_plan'];

                        $so_luong_san_xuat = $vOI['quantity'];
                        $_key = !empty($vOI['_key']) ? $vOI['_key'] : 0;

                        $_index = $po_id.'__'.$_item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
                        $dtProductionListsItem = $productionListItems[$_index] ?? null;
                        $thoi_gian_thay_size = 0;
                        $thoi_gian_rua_may = 0;
                        $to_in_bu_hao = $total_quantity_compensation;

                        //
                        $rowBegin++;
                        $iExcel = -1;
                        $number++;
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $number);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, (!empty($dtProductionListsItem) ? ($dtProductionListsItem['id']) : ''));

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $vOI['reference_no_po']);
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $vOI['item_name']);

                        $images = get_upload_path_by_type('products') . $vOI['images'];
                        if (!empty($vOI['images']) && file_exists($images)) {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName($vOI['images']);
                            $objDrawing->setDescription('Image');
                            $objDrawing->setPath($images);
                            list($originalWidth, $originalHeight) = getimagesize($images);
                            $maxWidth = 30;  // Chiều rộng tối đa của ô
                            $maxHeight = 30; // Chiều cao tối đa của ô
                            $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                            $scaledWidth = $originalWidth * $scale;
                            $scaledHeight = $originalHeight * $scale;
                            $objDrawing->setWidth($scaledWidth);
                            $objDrawing->setHeight($scaledHeight);
                            $offsetX = ($maxWidth - $scaledWidth) / 2;
                            $offsetY = ($maxHeight - $scaledHeight) / 2;
                            $objDrawing->setOffsetX($offsetX + 2);
                            $objDrawing->setOffsetY($offsetY + 2);
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                            $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                            $objDrawing->setCoordinates($excel[++$iExcel].$rowBegin);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                        }

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $name_stage);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_luong_san_xuat)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($so_luong_san_xuat, 3));

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, ($ngay_giao_hang));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $number_children_size);
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_to_in)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($so_to_in, 3));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $to_in_bu_hao)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($to_in_bu_hao, 3));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $mat);

                        if ($type_productionlist_id == 2) {
                            //flexo
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_con_tren_kb_flexo);
                            $loai_in_flexo = !empty($dtProductionListsItem['loai_in_flexo']) ? $dtProductionListsItem['loai_in_flexo'] : 1;
                            $loai_in_flexo_text = $loai_in_flexo == 2 ? 'UV' : 'Thường';
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $loai_in_flexo_text);
                            $tong_tua = !empty($dtProductionListsItem['tong_tua']) ? $dtProductionListsItem['tong_tua'] : 0;
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $tong_tua)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($tong_tua, 3));
                        }

                        if ($type_productionlist_id == 1) {
                            // Số mặt in
                            $so_mat_in = !empty($dtProductionListsItem['so_mat_in']) ? $dtProductionListsItem['so_mat_in'] : $countFace;
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_mat_in);

                            // Tổng tua
                            $tong_tua = !empty($dtProductionListsItem['tong_tua']) ? $dtProductionListsItem['tong_tua'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $tong_tua)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($tong_tua, 3));

                            // Thời gian in
                            $thoi_gian_in = !empty($dtProductionListsItem['thoi_gian_in']) ? $dtProductionListsItem['thoi_gian_in'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_in)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_in, 3));

                            // Loại (canh bài)
                            $loai_canh_bai = isset($dtProductionListsItem['loai_canh_bai']) ? $dtProductionListsItem['loai_canh_bai'] : '';
                            if ($loai_canh_bai !== '' && is_numeric($loai_canh_bai)) {
                                switch ((int) $loai_canh_bai) {
                                    case 1:
                                        $loai_canh_bai = 'T/Đ';
                                        break;
                                    case 2:
                                        $loai_canh_bai = 'Màu';
                                        break;
                                    case 3:
                                        $loai_canh_bai = 'Mẫu';
                                        break;
                                    default:
                                        $loai_canh_bai = '';
                                        break;
                                }
                            }
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $loai_canh_bai);

                            // Thời gian canh bài
                            $thoi_gian_canh_bai = !empty($dtProductionListsItem['thoi_gian_canh_bai']) ? $dtProductionListsItem['thoi_gian_canh_bai'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_canh_bai)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_canh_bai, 3));

                            // Số lần thay size
                            $so_lan_thay_size = isset($dtProductionListsItem['so_lan_thay_size']) ? $dtProductionListsItem['so_lan_thay_size'] : '';
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_lan_thay_size);

                            // Số lần rửa máy
                            $so_lan_rua_may = isset($dtProductionListsItem['so_lan_rua_may']) ? $dtProductionListsItem['so_lan_rua_may'] : '';
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_lan_rua_may);

                            // Thời gian khác
                            $thoi_gian_khac = !empty($dtProductionListsItem['thoi_gian_khac']) ? $dtProductionListsItem['thoi_gian_khac'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_khac)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_khac, 3));

                            // Thời gian xử lý
                            $thoi_gian_xu_ly = !empty($dtProductionListsItem['thoi_gian_xu_ly']) ? $dtProductionListsItem['thoi_gian_xu_ly'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_xu_ly)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_xu_ly, 3));

                            // Ngày mở lệnh
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($dtProductionListsItem['ngay_mo_lsx'] ?? null));

                            // Ngày GH hệ thống
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($dtProductionListsItem['ngay_giao_hang_he_thong'] ?? null));

                            // Thời gian còn lại
                            $thoi_gian_con_lai = !empty($dtProductionListsItem['thoi_gian_con_lai']) ? $dtProductionListsItem['thoi_gian_con_lai'] : 0;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_con_lai)
                                ->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_con_lai, 3));

                            // Phiếu PTM
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_ptm']) ? 'Có' : '');

                            // Mẫu Sản Xuất
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_color']) ? 'Có' : '');
                            // Layout Ghép
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_layout']) ? 'Có' : '');

                            // Khuân Bế
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_sewing']) ? 'Có' : '');

                            // NPL
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_npl']) ? 'Có' : '');

                            // Vật Tư
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_material']) ? 'Có' : '');

                            // Phiếu Cắt Giấy
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($vOI['is_cutting']) ? 'Có' : '');

                            // Ngày về NVL dự kiến
                            $ngay_ve_nvl_du_kien = !empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? $dtProductionListsItem['ngay_ve_nvl_du_kien'] : ($vOI['date_npl'] ?? null);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($ngay_ve_nvl_du_kien));

                            // Ngày bắt đầu dự kiến
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($dtProductionListsItem['ngay_bat_dau_du_kien'] ?? null));

                            // Ngày hoàn thành in
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($dtProductionListsItem['ngay_hoan_thanh_in'] ?? null));
                        } else {
                            $so_lan_thay_size = !empty($dtProductionListsItem['so_lan_thay_size']) ? ($dtProductionListsItem['so_lan_thay_size']) : '';
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_lan_thay_size);

                            $thoi_gian_xu_ly = !empty($dtProductionListsItem['thoi_gian_xu_ly']) ? ($dtProductionListsItem['thoi_gian_xu_ly']) : 0;
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_xu_ly)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_xu_ly, 3));

                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, _d($dtProductionListsItem['ngay_bat_dau_du_kien'] ?? null));
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, (!empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? _d($dtProductionListsItem['ngay_ve_nvl_du_kien']) : (!empty($vOI['date_npl']) ? _d($vOI['date_npl']) : '')));
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, (!empty($dtProductionListsItem['ngay_hoan_thanh_in']) ? _d($dtProductionListsItem['ngay_hoan_thanh_in']) : ''));
                        }

                        // Ngày bắt đầu kế hoạch
                        $ngay_bat_dau_ke_hoach = !empty($dtProductionListsItem['ngay_bat_dau_ke_hoach']) ? date_format(date_create($dtProductionListsItem['ngay_bat_dau_ke_hoach']), 'H:i') : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $ngay_bat_dau_ke_hoach);

                        // Ngày kết thúc kế hoạch
                        $ngay_ket_thuc_ke_hoach = !empty($dtProductionListsItem['ngay_ket_thuc_ke_hoach']) ? date_format(date_create($dtProductionListsItem['ngay_ket_thuc_ke_hoach']), 'H:i') : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $ngay_ket_thuc_ke_hoach);

                        // Ngày bắt đầu thực tế
                        $ngay_bat_dau_thuc_te = !empty($dtProductionListsItem['ngay_bat_dau_thuc_te']) ? date_format(date_create($dtProductionListsItem['ngay_bat_dau_thuc_te']), 'H:i') : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $ngay_bat_dau_thuc_te);

                        // Ngày kết thúc thực tế
                        $ngay_ket_thuc_thuc_te = !empty($dtProductionListsItem['ngay_ket_thuc_thuc_te']) ? date_format(date_create($dtProductionListsItem['ngay_ket_thuc_thuc_te']), 'H:i') : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $ngay_ket_thuc_thuc_te);
                        // Thời gian canh bài thực tế
                        $thoi_gian_canh_bai_thuc_te = !empty($dtProductionListsItem) ? ($dtProductionListsItem['thoi_gian_canh_bai_thuc_te']) : 0;
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $thoi_gian_canh_bai_thuc_te)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($thoi_gian_canh_bai_thuc_te, 3));

                        // NPL canh bài thực tế
                        $npl_canh_bai_thuc_te = !empty($dtProductionListsItem) ? ($dtProductionListsItem['npl_canh_bai_thuc_te']) : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $npl_canh_bai_thuc_te);

                        // Số lượng thực tế
                        $so_luong_thuc_te = !empty($dtProductionListsItem) ? ($dtProductionListsItem['so_luong_thuc_te']) : '';
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $so_luong_thuc_te)->getStyle($excel[$iExcel].$rowBegin)->getNumberFormat()->setFormatCode(formatNumberExcel($so_luong_thuc_te, 3));
                        
                        $ghi_chu = !empty($dtProductionListsItem['ghi_chu']) ? ($dtProductionListsItem['ghi_chu']) : $note_plan;
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $ghi_chu);
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, $dtProductionListsItem['name_machine'] ?? '');
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, !empty($dtProductionListsItem['hoan_thanh']) && $dtProductionListsItem['hoan_thanh'] == 'HT' ? 'Hoàn thành' : 'Chưa hoàn thành');
                    }
                }

                //
                $rowBegin++;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[2].$rowBegin, 'Nghỉ trưa');
                $rowBegin++;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[2].$rowBegin, 'Rửa máy');
                $rowBegin++;
                $rowBegin++;
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$excel[$iExcel].($rowBegin))->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle($excel[0].$rowTemBegin.':'.$excel[$iExcel].$rowBegin)->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        ]);

        $rowBegin++;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[2].$rowBegin, 'Tổ trưởng in');
        $objPHPExcel->getActiveSheet()->getStyle($excel[2].$rowBegin)->applyFromArray([
           'font' => array(
                'bold' => true
            ),
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue($excel[11].$rowBegin, 'Tổ máy');
        $objPHPExcel->getActiveSheet()->getStyle($excel[11].$rowBegin)->applyFromArray([
           'font' => array(
                'bold' => true
            ),
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue($excel[17].$rowBegin, 'Người lập');
        $objPHPExcel->getActiveSheet()->getStyle($excel[17].$rowBegin)->applyFromArray([
           'font' => array(
                'bold' => true
            ),
        ]);

        $rowBegin = $rowBegin + 4;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[1].$rowBegin, '* GHI CHÚ NHỮNG PHÁT SINH :');
        $objPHPExcel->getActiveSheet()->getStyle($excel[1].$rowBegin)->applyFromArray([
           'font' => array(
                'bold' => true
            ),
        ]);

        $filename = $dtCategoryStages['code'].$dtCategoryStages['code_type_productionlist'] . '.xls';
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
    }

    public function import_excel_po() {
        if (!$this->perEditProductionList && !$this->perUpdateProductionList) {
            accessDenied($js = true);
        }

        $data = [];
        if ($this->input->post()) {
            // Lấy dữ liệu từ form
            $start_row = (int)$this->input->post('start_row');
            $end_row = (int)$this->input->post('end_row');
            $col_id = strtoupper($this->input->post('col_id'));
            
            $col_ngay_bat_dau_du_kien = strtoupper($this->input->post('col_ngay_bat_dau_du_kien'));
            $col_ngay_hoan_thanh_in = strtoupper($this->input->post('col_ngay_hoan_thanh_in'));
            $col_ngay_bat_dau_ke_hoach = strtoupper($this->input->post('col_ngay_bat_dau_ke_hoach'));
            $col_ngay_ket_thuc_ke_hoach = strtoupper($this->input->post('col_ngay_ket_thuc_ke_hoach'));
            $col_ngay_bat_dau_thuc_te = strtoupper($this->input->post('col_ngay_bat_dau_thuc_te'));
            $col_ngay_ket_thuc_thuc_te = strtoupper($this->input->post('col_ngay_ket_thuc_thuc_te'));
            $col_so_luong_thuc_te = strtoupper($this->input->post('col_so_luong_thuc_te'));
            $col_thoi_gian_canh_bai_thuc_te = strtoupper($this->input->post('col_thoi_gian_canh_bai_thuc_te'));
            $col_npl_canh_bai_thuc_te = strtoupper($this->input->post('col_npl_canh_bai_thuc_te'));

            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load("$fullfile");

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();

            // Validate dòng bắt đầu và kết thúc
            if ($start_row < 1 || $end_row < $start_row || $end_row > $highestRow) {
                $data['result'] = 0;
                $data['message'] = 'Dòng bắt đầu/kết thúc không hợp lệ!';
                echo json_encode($data);
                return;
            }

            // Validate các cột
            $requiredCols = [
                'col_id' => $col_id,
                'col_ngay_bat_dau_du_kien' => $col_ngay_bat_dau_du_kien,
                'col_ngay_hoan_thanh_in' => $col_ngay_hoan_thanh_in,
                'col_ngay_bat_dau_ke_hoach' => $col_ngay_bat_dau_ke_hoach,
                'col_ngay_ket_thuc_ke_hoach' => $col_ngay_ket_thuc_ke_hoach,
                'col_ngay_bat_dau_thuc_te' => $col_ngay_bat_dau_thuc_te,
                'col_ngay_ket_thuc_thuc_te' => $col_ngay_ket_thuc_thuc_te,
                'col_so_luong_thuc_te' => $col_so_luong_thuc_te,
                'col_thoi_gian_canh_bai_thuc_te' => $col_thoi_gian_canh_bai_thuc_te,
                'col_npl_canh_bai_thuc_te' => $col_npl_canh_bai_thuc_te,
            ];
            foreach ($requiredCols as $key => $col) {
                if (empty($col) || !preg_match('/^[A-Z]+$/', $col)) {
                    $data['result'] = 0;
                    $data['message'] = 'Cột ' . $key . ' không hợp lệ!';
                    echo json_encode($data);
                    return;
                }
            }

            $parseDateNoTime = function($v) {
                if ($v === null || $v === '') {
                    return false;
                }
                // Excel serial date number
                if (is_numeric($v)) {
                    try {
                        $ts = PHPExcel_Shared_Date::ExcelToPHP($v);
                        return date('Y-m-d', (int) $ts);
                    } catch (Exception $e) {
                        // fallthrough
                    }
                }
                $v = trim((string) $v);
                $formats = ['d/m/Y', 'Y-m-d'];
                foreach ($formats as $fmt) {
                    $dt = DateTime::createFromFormat($fmt, $v);

                    if ($dt instanceof DateTime) {
                        return $dt->format('Y-m-d');
                    }
                }
                return false;
            };

            $successCount = 0;
            $errors = '';

            $ids = [];
            for ($row = $start_row; $row <= $end_row; ++$row) {
                $id = $objWorksheet->getCell($col_id . $row)->getValue();
                if (!empty($id) && is_numeric($id)) {
                    $ids[] = $id;
                }
            }

            if (!empty($ids)) {
                $this->db->select('tbl_production_lists_items.*', false);
                $this->db->from('tbl_production_lists_items');
                $this->db->where_in('id', $ids);
                $existingItems = $this->db->get()->result_array();
                if (!empty($existingItems)) {
                    $existingItems = array_reduce($existingItems, function($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    }, []);
                }
            }

            for ($row = $start_row; $row <= $end_row; ++$row) {
                $id = $objWorksheet->getCell($col_id . $row)->getValue();
                $ngay_bat_dau_du_kien = $objWorksheet->getCell($col_ngay_bat_dau_du_kien . $row)->getValue();
                $ngay_hoan_thanh_in = $objWorksheet->getCell($col_ngay_hoan_thanh_in . $row)->getValue();
                $ngay_bat_dau_ke_hoach = $objWorksheet->getCell($col_ngay_bat_dau_ke_hoach . $row)->getValue();
                $ngay_ket_thuc_ke_hoach = $objWorksheet->getCell($col_ngay_ket_thuc_ke_hoach . $row)->getValue();
                $ngay_bat_dau_thuc_te = $objWorksheet->getCell($col_ngay_bat_dau_thuc_te . $row)->getValue();
                $ngay_ket_thuc_thuc_te = $objWorksheet->getCell($col_ngay_ket_thuc_thuc_te . $row)->getValue();
                $so_luong_thuc_te = number_unformat($objWorksheet->getCell($col_so_luong_thuc_te . $row)->getValue());
                $thoi_gian_canh_bai_thuc_te = number_unformat($objWorksheet->getCell($col_thoi_gian_canh_bai_thuc_te . $row)->getValue());
                $npl_canh_bai_thuc_te = $objWorksheet->getCell($col_npl_canh_bai_thuc_te . $row)->getValue();

                // Validate id
                if (empty($id) || !is_numeric($id)) {
                    $errors .= "<div class='text-danger'>Dòng $row: ID không hợp lệ</div>";
                    continue;
                }

                // Validate bắt buộc: ngày bắt đầu dự kiến và ngày hoàn thành in (d/m/Y hoặc Excel serial), convert Y-m-d
                $parsed_ngay_bat_dau_du_kien = $parseDateNoTime($ngay_bat_dau_du_kien);
                if ($parsed_ngay_bat_dau_du_kien === false) {
                    $errors .= "<div class='text-danger'>Dòng $row: Ngày bắt đầu dự kiến phải có và đúng định dạng (d/m/Y hoặc Excel date)</div>";
                    continue;
                }

                $parsed_ngay_hoan_thanh_in = $parseDateNoTime($ngay_hoan_thanh_in);
                if ($parsed_ngay_hoan_thanh_in === false) {
                    $errors .= "<div class='text-danger'>Dòng $row: Ngày hoàn thành in phải có và đúng định dạng (d/m/Y hoặc Excel date)</div>";
                    continue;
                }

                // Validate ngày (cho phép rỗng, nếu có thì phải đúng định dạng d/m/Y H:i hoặc Y-m-d H:i:s)
                // $fields = [
                //     'ngay_bat_dau_ke_hoach' => $ngay_bat_dau_ke_hoach,
                //     'ngay_ket_thuc_ke_hoach' => $ngay_ket_thuc_ke_hoach,
                //     'ngay_bat_dau_thuc_te' => $ngay_bat_dau_thuc_te,
                //     'ngay_ket_thuc_thuc_te' => $ngay_ket_thuc_thuc_te,
                // ];

                $update = [];
                // foreach ($fields as $f => $v) {
                //     if ($v === null || $v === '') {
                //         $update[$f] = null;
                //         continue;
                //     }

                //     $saved = false;
                //     // Excel serial date number
                //     if (is_numeric($v)) {
                //         try {
                //             $ts = PHPExcel_Shared_Date::ExcelToPHP($v);
                //             $update[$f] = date('Y-m-d H:i:s', (int) $ts);
                //             $saved = true;
                //         } catch (Exception $e) {
                //             // fallthrough to string parsing
                //         }
                //     }

                //     if (!$saved) {
                //         $v = trim((string) $v);
                //         $formats = [
                //             'd/m/Y H:i',
                //             'd/m/Y H:i:s',
                //             'Y-m-d H:i',
                //             'Y-m-d H:i:s',
                //         ];
                //         foreach ($formats as $fmt) {
                //             $dt = DateTime::createFromFormat($fmt, $v);
                //             if ($dt instanceof DateTime) {
                //                 $update[$f] = $dt->format('Y-m-d H:i:s');
                //                 $saved = true;
                //                 break;
                //             }
                //         }
                //     }

                //     if (!$saved) {
                //         $errors .= "<div class='text-danger'>Dòng $row: $f không đúng định dạng (d/m/Y H:i hoặc Y-m-d H:i:s)</div>";
                //         continue;
                //     }
                // }

                $fields = [
                    'ngay_bat_dau_ke_hoach'   => [$ngay_bat_dau_ke_hoach, $parsed_ngay_bat_dau_du_kien],
                    'ngay_ket_thuc_ke_hoach'  => [$ngay_ket_thuc_ke_hoach, $parsed_ngay_bat_dau_du_kien],
                    'ngay_bat_dau_thuc_te'    => [$ngay_bat_dau_thuc_te, $parsed_ngay_hoan_thanh_in],
                    'ngay_ket_thuc_thuc_te'   => [$ngay_ket_thuc_thuc_te, $parsed_ngay_hoan_thanh_in],
                ];

                foreach ($fields as $f => [$time, $date]) {
                    if ($time === null || $time === '') {
                        $update[$f] = null;
                        continue;
                    }

                    $timeStr = null;

                    if (is_numeric($time)) {
                        // Excel time as fraction of day -> seconds in day, no timezone shift
                        $fraction = fmod((float) $time, 1.0);
                        if ($fraction < 0) {
                            $fraction += 1.0;
                        }
                        $seconds = (int) round($fraction * 86400);
                        $seconds = $seconds % 86400; // clamp to 0..86399
                        $timeStr = gmdate('H:i:s', $seconds);
                    } else {
                        $time = trim((string) $time);
                        // Support 24h and 12h with AM/PM, normalize without timezone
                        $formats = [
                            'H:i',
                            'H:i:s',
                            'G:i',
                            'G:i:s',
                            'h:i A',
                            'h:i:s A',
                            'g:i A',
                            'g:i:s A',
                        ];
                        foreach ($formats as $fmt) {
                            $tmp = DateTime::createFromFormat('!' . $fmt, $time);
                            if ($tmp instanceof DateTime) {
                                $timeStr = $tmp->format('H:i:s');
                                break;
                            }
                        }
                    }

                    if ($timeStr !== null) {
                        $update[$f] = $date . ' ' . $timeStr;
                    } else {
                        $errors .= "<div class='text-danger'>Dòng $row: $f không đúng định dạng (H:i, H:i:s, hoặc h:i(:s) AM/PM, ví dụ 2:55:00 PM)</div>";
                        continue 2;
                    }
                }

                // Validate số lượng thực tế
                if ($so_luong_thuc_te !== null && $so_luong_thuc_te !== '') {
                    if (!is_numeric($so_luong_thuc_te)) {
                        $errors .= "<div class='text-danger'>Dòng $row: Số lượng thực tế không hợp lệ</div>";
                        continue;
                    }
                    $update['so_luong_thuc_te'] = $so_luong_thuc_te;
                }

                // Thời gian canh bài thực tế
                if ($thoi_gian_canh_bai_thuc_te !== null && $thoi_gian_canh_bai_thuc_te !== '') {
                    if (!is_numeric($thoi_gian_canh_bai_thuc_te)) {
                        $errors .= "<div class='text-danger'>Dòng $row: Thời gian canh bài thực tế không hợp lệ</div>";
                        continue;
                    }
                    $update['thoi_gian_canh_bai_thuc_te'] = $thoi_gian_canh_bai_thuc_te;
                }
                // NPL canh bài thực tế
                $update['npl_canh_bai_thuc_te'] = $npl_canh_bai_thuc_te;
                $update['ngay_bat_dau_du_kien'] = $parsed_ngay_bat_dau_du_kien;
                $update['ngay_hoan_thanh_in'] = $parsed_ngay_hoan_thanh_in;

                // Kiểm tra có quyền cập nhật
                if (!$this->perEditProductionList && $this->perUpdateProductionList) {
                    $existingItem = $existingItems[$id] ?? null;
                    if (!empty($existingItem)) {
                        if (!empty($existingItem['ngay_bat_dau_ke_hoach'])) {
                            unset($update['ngay_bat_dau_ke_hoach']);
                        }

                        if (!empty($existingItem['ngay_ket_thuc_ke_hoach'])) {
                            unset($update['ngay_ket_thuc_ke_hoach']);
                        }

                        if (!empty($existingItem['ngay_bat_dau_thuc_te'])) {
                            unset($update['ngay_bat_dau_thuc_te']);
                        }

                        if (!empty($existingItem['ngay_ket_thuc_thuc_te'])) {
                            unset($update['ngay_ket_thuc_thuc_te']);
                        }

                        if (!empty($existingItem['so_luong_thuc_te'])) {
                            unset($update['so_luong_thuc_te']);
                        }

                        if (!empty($existingItem['ngay_bat_dau_du_kien'])) {
                            unset($update['ngay_bat_dau_du_kien']);
                        }

                        if (!empty($existingItem['ngay_hoan_thanh_in'])) {
                            unset($update['ngay_hoan_thanh_in']);
                        }

                        if (!empty($existingItem['thoi_gian_canh_bai_thuc_te'])) {
                            unset($update['thoi_gian_canh_bai_thuc_te']);
                        }

                        if (!empty($existingItem['npl_canh_bai_thuc_te'])) {
                            unset($update['npl_canh_bai_thuc_te']);
                        }
                    }
                }

                if (empty($update)) {
                    $errors .= "<div class='text-danger'>Dòng $row: Không có dữ liệu cập nhật</div>";
                    continue;
                }

                if (empty($update)) {
                    $errors .= "<div class='text-danger'>Dòng $row: Không có dữ liệu cập nhật</div>";
                    continue;
                }

                $this->db->where('id', $id);
                $result = $this->db->update('tbl_production_lists_items', $update);
                if ($result) {
                    $successCount++;
                } else {
                    $errors .= "<div class='text-danger'>Dòng $row: Cập nhật thất bại</div>";
                }
            }

            $data['errors'] = $errors;
            if ($successCount) {
                $data['result'] = 1;
                $data['message'] = lang('cong_update_true') . ' ' . $successCount . ' dòng';
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data = [];
            $this->load->view('admin/production_list/import_excel_po', $data);
        }
    }
}
