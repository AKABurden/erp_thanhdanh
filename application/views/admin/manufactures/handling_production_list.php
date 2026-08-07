<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/production_list/handling', array('id' => 'handling_production_list', 'autocomplete' => 'off')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <table class="tnh-tb table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 15%;">
                                <?= lang('tnh_reference_no_production_list', 'reference_no') ?>
                            </td style="width: 35%;">
                            <td>
                                <div class="form-group">
                                    <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($production_lists) ? $production_lists['reference_no'] : lang('auto') ?>" readonly="" aria-invalid="false">
                                </div>
                            </td>
                            <td style="width: 15%;">
                                <?= lang('tnh_date_production_list', 'date') ?>
                            </td>
                            <td style="width: 35%;">
                                <div class="form-group">
                                    <?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" autocomplete="off" placeholder="' . lang('date') . '" required ') ?>
                                </div>
                                <input type="hidden" name="production_list_id" id="production_list_id" class="form-control" value="<?= $production_list_id ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= lang('start_date', 'start_date') ?>
                            </td>
                            <td>
                                <input type="text" name="start_date" id="start_date" required placeholder="<?= lang('start_date') ?>" class="form-control start_date datepicker" autocomplete="off" value="<?= !empty($production_lists) ? _d($production_lists['start_date']) : '' ?>">
                            </td>
                            <td>
                                <?= lang('end_date', 'end_date') ?>
                            </td>
                            <td>
                                <input type="text" name="end_date" id="end_date" required placeholder="<?= lang('end_date') ?>" class="form-control end_date datepicker" autocomplete="off" value="<?= !empty($production_lists) ? _d($production_lists['end_date']) : '' ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 mtop10">
                <a class="btn btn-primary" onclick="loadDataProductionList(this)"><?= lang('tnh_load_data') ?></a>
            </div>
            <div class="col-md-12 mtop10 div-data-production-list">

            </div>
        </div>
        <hr>
        <hr>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add-production-list">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>

<script>
    function totalProductionList(_c_type_productionlist_id = 0, change_date = 0) {
        tb = '#tb-items tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        // console.log(change_date);
        var so_luong_may = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.so_luong_may').val());
        var nhom_tho = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nhom_tho').val());
        var nang_suat_may = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may').val());
        var _thoi_gian_canh_bai = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('._thoi_gian_canh_bai').val());
        var thoi_gian_lam_viec_chuan = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_lam_viec_chuan').val());
        var thoi_gian_lam_viec_ot = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_lam_viec_ot').val());
        
        var so_luong_tho = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.so_luong_tho').val());

        var nang_suat_may_in_300 = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may_in_300').val());
        var nang_suat_may_in_600 = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may_in_600').val());

        var nang_suat_dau_in_trang_den = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_dau_in_trang_den').val());
        var nang_suat_dau_in_mau = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_dau_in_mau').val());
        var thoi_gian_canh_bai_in_trang_den = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_canh_bai_in_trang_den').val());
        var thoi_gian_canh_bai_in_mau = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_canh_bai_in_mau').val());

        var nang_suat_keo_tay = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_keo_tay').val());

        if (_c_type_productionlist_id == 1) {
            capacity_1 = nhom_tho * nang_suat_may * thoi_gian_lam_viec_chuan;
            capacity_2 = nhom_tho * nang_suat_may * thoi_gian_lam_viec_ot;
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-2').html(tnhFormatNumber(capacity_1));
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-3').html(tnhFormatNumber(capacity_2));
        } else if (_c_type_productionlist_id == 2) {
            capacity_1 = so_luong_tho * nang_suat_may * thoi_gian_lam_viec_chuan;
            capacity_2 = so_luong_tho * nang_suat_may * thoi_gian_lam_viec_ot;

            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-2').html(tnhFormatNumber(capacity_1));
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-3').html(tnhFormatNumber(capacity_2));
        } else if (_c_type_productionlist_id == 3) {
            $('.thoi_gian_canh_bai').attr('readonly', 'readonly');
            $('.thoi_gian_canh_bai').val(_thoi_gian_canh_bai);
        } else if (_c_type_productionlist_id == 3) {
            $('.thoi_gian_canh_bai').attr('readonly', 'readonly');
        }

        nang_suat_may_boi_mot_mat = intVal($('.nang_suat_may_boi_mot_mat').val());
        nang_suat_may_boi_hai_mat = intVal($('.nang_suat_may_boi_hai_mat').val());

        nang_suat_may_be_giay_thuong = intVal($('.nang_suat_may_be_giay_thuong').val());
        nang_suat_may_demi_be_giay_boi_pet = intVal($('.nang_suat_may_demi_be_giay_boi_pet').val());

        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];
            so_mat_in = intVal($(element).find('.so_mat_in').val());
            to_in = intVal($(element).find('.td-to-in').html());

            if (_c_type_productionlist_id == 1) {
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                thoi_gian_canh_bai = intVal($(element).find('.thoi_gian_canh_bai').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
                
                to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_con_tren_kb_offset = intVal($(element).find('.td-so-con-tren-kb-offset').html());
                tua_sau_in = (so_con_tren_kb_offset > 0 ? (so_con_tren_to_in/so_con_tren_kb_offset*to_in) : to_in);

                $(element).find('.td-tua-sau-in').html(tnhFormatNumber(tua_sau_in));
            } else if (_c_type_productionlist_id == 2) {
                so_luong_san_xuat = intVal($(element).find('.td-so-luong-san-xuat').html());
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_canh_bai = intVal($(element).find('.thoi_gian_canh_bai').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

                so_con_tren_kb_flexo = intVal($(element).find('.td-so-con-tren-kb-flexo').html());
                so_tua_in = 0;
                if (so_con_tren_kb_flexo > 0) {
                    so_tua_in = so_luong_san_xuat/so_con_tren_kb_flexo;
                }
                $(element).find('.td-so-tua-in-flexo').html(tnhFormatNumber(so_tua_in));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = so_tua_in/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
            } else if (_c_type_productionlist_id == 3) {
                so_luong_san_xuat = intVal($(element).find('.td-so-luong-san-xuat').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                dau_in = intVal($(element).find('.dau-in').val());

                nang_suat = (dau_in == 300) ? nang_suat_may_in_300 : nang_suat_may_in_600;
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));

                so_tua_in = so_luong_san_xuat;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 4) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_in = intVal($(element).find('.so_mat_in').val());
                loai = $(element).find('.loai').val();

                nang_suat = (loai == "T/D") ? nang_suat_dau_in_trang_den : nang_suat_dau_in_mau;
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));
                so_tua_in = so_to_in * so_mat_in;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
                
                thoi_gian_canh_bai = (loai == "T/D") ? thoi_gian_canh_bai_in_trang_den : thoi_gian_canh_bai_in_mau;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 5) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_in = intVal($(element).find('.so_mat_in').val());
                so_mau_in = intVal($(element).find('.so_mau_in').val());
                so_tua_in = so_to_in * so_mat_in * so_mau_in;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                nang_suat = nang_suat_keo_tay;
                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 6) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_in = intVal($(element).find('.so_mat').val());
                so_tua_in = so_to_in * so_mat_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(so_tua_in));

                nang_suat = nang_suat_may;
                thoi_gian_xu_ly = 0;
                if (nang_suat > 0) {
                    thoi_gian_xu_ly = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));

                tong_thoi_gian = thoi_gian_xu_ly + thoi_gian_canh_bai;
                $(element).find('.td-tong-thoi-gian').html(tnhFormatNumber(tong_thoi_gian));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;
                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 7) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_phun_bong = intVal($(element).find('.so_mat_phun_bong').val());
                so_tua_in = so_to_in * so_mat_phun_bong;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(so_tua_in));

                nang_suat = nang_suat_may;
                thoi_gian_xu_ly = 0;
                if (nang_suat > 0) {
                    thoi_gian_xu_ly = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));

                tong_thoi_gian = thoi_gian_xu_ly + thoi_gian_canh_bai;
                $(element).find('.td-tong-thoi-gian').html(tnhFormatNumber(tong_thoi_gian));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;
                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 8) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_tua_in = so_to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(so_tua_in));
                loai_boi = $(element).find('.loai_boi').val();
                nang_suat = 0;
                if (loai_boi == 2 || loai_boi == '2') {
                    nang_suat = nang_suat_may_boi_hai_mat;
                } else {
                    nang_suat = nang_suat_may_boi_mot_mat;
                }

                thoi_gian_xu_ly = 0;
                if (nang_suat > 0) {
                    thoi_gian_xu_ly = so_tua_in/nang_suat;
                }
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));

                tong_thoi_gian = thoi_gian_xu_ly + thoi_gian_canh_bai;
                $(element).find('.td-tong-thoi-gian').html(tnhFormatNumber(tong_thoi_gian));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;
                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 9) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_con_tren_kb = intVal($(element).find('.td-so-con-tren-kb').html());

                tong_tua = 0;
                if (so_con_tren_kb > 0) {
                    tong_tua = so_con_tren_to_in/so_con_tren_kb * so_to_in;
                }
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                loai_giay = $(element).find('.loai_giay').val();

                nang_suat = 0;
                if (loai_giay == 'Thường' || loai_giay == 'thường') {
                    nang_suat = nang_suat_may_be_giay_thuong;
                } else {
                    nang_suat = nang_suat_may_demi_be_giay_boi_pet;
                }
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));

                thoi_gian_xu_ly = 0;
                if (nang_suat > 0) {
                    thoi_gian_xu_ly = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));

                tong_thoi_gian = thoi_gian_xu_ly + thoi_gian_canh_bai;
                $(element).find('.td-tong-thoi-gian').html(tnhFormatNumber(tong_thoi_gian));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;
                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            }
        }

        if (change_date) {
            $.each($('.div-type_productionlist_id-'+_c_type_productionlist_id+' .table-date tr.tr-sum td'), function (index, value) { 
                _date = $(value).attr('data-date');
                total_thoi_gian_xu_ly = 0;
                $.each($(tb), function (iTB, vTB) { 
                    ngay_bat_dau_du_kien = $(vTB).find('.ngay_bat_dau_du_kien').val();
                    if (_date == ngay_bat_dau_du_kien) {
                        total_thoi_gian_xu_ly+= intVal($(vTB).find('.td-thoi-gian-xu-ly').html());
                    }
                });
                $(value).html(total_thoi_gian_xu_ly > 0 ? tnhFormatNumber(total_thoi_gian_xu_ly, 2) : '');
            });
        }
    }

    function loadDataProductionList() {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();

        if (!start_date || !end_date) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc')
            return;
        }

        dataPOST['start_date'] = start_date;
        dataPOST['end_date'] = end_date;
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/production_list/loadDataProductionList',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.div-data-production-list').html(response);
            }
        });
    }

    $(document).ready(function () {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        if (start_date && end_date) {
            loadDataProductionList();
        }

        appValidateForm($('#handling_production_list'), {
            reference_no: 'required',
            date: 'required',
            start_date: 'required',
            end_date: 'required',
        }, db);

        function db(form) {
            $('.add-production-list').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
           
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url : url,
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    $('#reference_no').val(data.reference_no);
                    $('#production_list_id').val(data.production_list_id);
                    $('.add-production-list').removeAttr('disabled', 'disabled');
                } else {
                    alert_float('danger', data.message);
                    $('.add-production-list').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add-production-list').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    });
</script>