<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">

<style>
    .table-items tr th {
        width: 100px;
        min-width: 100px;
        max-width: 100px;
    }

    .table-items tr td {
        width: 100px;
        min-width: 100px;
        max-width: 100px;
        word-wrap: break-word;
        white-space: pre-line;
    }

    .DTFC_LeftBodyWrapper {
        border-right: 1px solid #cedae6;
    }

    .DTFC_LeftHeadWrapper {
        border-right: 1px solid #cedae6;
    }
</style>

<div class="modal-dialog modal-lg modal-produciton-list" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_production_list') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_date_production_list') ?>: </div>
                                    <div class="ml-at t-bold"><?= _d($production_lists['date']) ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_reference_no_production_list') ?>: </div>
                                    <div class="ml-at t-bold"><?= $production_lists['reference_no'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('start_date') ?>: </div>
                                    <div class="ml-at t-bold"><?= _d($production_lists['start_date']) ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('end_date') ?>: </div>
                                    <div class="ml-at t-bold"><?= _d($production_lists['end_date']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_created_by') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php if (!empty($production_lists['created_by'])) : ?>
                                            <?= get_staff_full_name($production_lists['created_by']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('date_created') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php if (!empty($production_lists['date_created'])) : ?>
                                            <?= _d($production_lists['date_created']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <?php if (!empty($production_lists['updated_by'])) : ?>
                                    <div class="row-contro">
                                        <div><?= lang('tnh_updated_by') ?>: </div>
                                        <div class="ml-at t-bold">
                                            <?= get_staff_full_name($production_lists['updated_by']) ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('tnh_date_updated') ?>: </div>
                                        <div class="ml-at t-bold">
                                            <?= _d($production_lists['date_updated']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php if (!empty($type_productions_list_total)) : ?>
                                <?php foreach ($type_productions_list_total as $key => $value) : ?>
                                    <li role="presentation" onclick="clickTab(<?= $value['type_productionlist_id'] ?>)" class="<?= $key == 0 ? 'active' : '' ?>">
                                        <a href="#tabs-<?= $value['type_productionlist_id'] ?>" aria-controls="home" role="tab" data-toggle="tab"><?= $value['code'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                        <div class="tab-content">
                            <?php if (!empty($type_productions_list_total)) : ?>
                                <?php foreach ($type_productions_list_total as $key => $value) : ?>
                                    <div role="tabpanel" class="tab-pane <?= $key == 0 ? 'active' : '' ?>" id="tabs-<?= $value['type_productionlist_id'] ?>">
                                        <?php if ($value['type_productionlist_id'] == 1) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 130px;"><?= lang('Thời gian chờ khô') ?></td>
                                                                <td style="width: 100px;"></td>
                                                                <td style="width: 100px;" class="text-right">
                                                                    <?= !empty($value) ? ($value['thoi_gian_cho_kho']) : '' ?>
                                                                </td>
                                                                <td class="text-center"><?= lang('Giờ') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Nhóm thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nhom_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('nhóm') ?></td>
                                                                <td><?= lang('Bóng OS/ Nhung') ?></td>
                                                                <td></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['bong_os_nhung']) : '' ?>
                                                                </td>
                                                                <td class="text-center"><?= lang('Giờ') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td class="td-capacity-1 text-right">
                                                                    <?= !empty($value) ? formatNumber($value['capacity_1']) : '' ?>
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="td-capacity-1 text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td class="td-capacity-2 text-right">
                                                                    <?= !empty($value) ? formatNumber($value['capacity_2']) : '' ?>
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td class="td-capacity-3 text-right"><?= !empty($value) ? formatNumber($value['capacity_3']) : '' ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <?php
                                                    $this->db->select('tbl_machines.id, tbl_machines.code, tbl_machines.name');
                                                    $this->db->join('tbl_machines_stage', 'tbl_machines_stage.machines_id = tbl_machines.id', 'inner');
                                                    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_machines_stage.category_stage_id', 'inner');
                                                    $this->db->where('tbl_category_stages.type_productionlist_id', $value['type_productionlist_id']);
                                                    $this->db->order_by('tbl_machines.code');
                                                    $optionMachine = $this->db->get('tbl_machines')->result_array();
                                                ?>
                                                <!-- <hr> -->
                                                <!-- <div class="col-md-12 mtop10 hide">
                                                    <div class="col-md-3 mtop10">
                                                        <?//= render_select('filter_machine', $optionMachine, ['id', 'code', 'name'], '') ?>
                                                    </div>
                                                </div> -->
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số mặt in') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng tua') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/KB') ?></th>
                                                                    <th class="text-center"><?= lang('Tua sau in') ?></th>
                                                                    <th class="text-center"><?= lang('Công đoạn in') ?></th>
                                                                    <th class="text-center"><?= lang('Bóng màng') ?></th>
                                                                    <th class="text-center"><?= lang('Hoàn thành') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : 
                                                                        if (!empty($vI['may_in'])) {
                                                                            $may_in = get_table_where('tbl_machines', ['id'=>''.$vI['may_in'].''], '', 'row_array', '', 'id, code, name');
                                                                        }
                                                                        if (!empty($may_in['id'])) {
                                                                            $may_in = $may_in['code'] .' ('.$may_in['name'].')';
                                                                        } else {
                                                                            $may_in = '';
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mat_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_tua']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_mo_lsx']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ve_nvl_du_kien']) ? _d($vI['ngay_ve_nvl_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ban_giao_san_xuat']) ? _d($vI['ngay_ban_giao_san_xuat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= $may_in ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['so_con_tren_to_in']) ? formatNumber($vI['so_con_tren_to_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['so_con_tren_kb_offset']) ? formatNumber($vI['so_con_tren_kb_offset']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tua_sau_in']) ? formatNumber($vI['tua_sau_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['stage_name']) ? ($vI['stage_name']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['bong_mang']) ? ($vI['bong_mang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['hoan_thanh']) ? ($vI['hoan_thanh']) : '' ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 2) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Số lượng thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('thợ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td>
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td class="td-capacity-2 text-right"><?= !empty($value) ? formatNumber($value['capacity_2']) : '' ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td>
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td class="td-capacity-3 text-right"><?= !empty($value) ? formatNumber($value['capacity_3']) : '' ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Số lượng SX (con)') ?></th>
                                                                    <th class="text-center"><?= lang('Số tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/ tờ') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/KB') ?></th>
                                                                    <th class="text-center"><?= lang('Số tua in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                    <th class="text-center"><?= lang('Công đoạn') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : 
                                                                        if (!empty($vI['may_in'])) {
                                                                            $may_in = get_table_where('tbl_machines', ['id'=>''.$vI['may_in'].''], '', 'row_array', '', 'id, code, name');
                                                                        }
                                                                        if (!empty($may_in['id'])) {
                                                                            $may_in = $may_in['code'] .' ('.$may_in['name'].')';
                                                                        } else {
                                                                            $may_in = '';
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_luong_san_xuat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_kb_flexo']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_tua_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_mo_lsx']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ve_nvl_du_kien']) ? _d($vI['ngay_ve_nvl_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ban_giao_san_xuat']) ? _d($vI['ngay_ban_giao_san_xuat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ket_thuc']) ? _d($vI['ngay_ket_thuc']) : '' ?></td>

                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= $may_in ?></td>
                                                                            <td class="text-center"><?= !empty($vI['stage_name']) ? ($vI['stage_name']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 3) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="7" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Số lượng thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('thợ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất đầu in 300:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_in_300']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất đầu in 600:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_in_600']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Số lượng SX (con)') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/ tờ') ?></th>
                                                                    <th class="text-center"><?= lang('Đầu in') ?></th>
                                                                    <th class="text-center"><?= lang('Năng suất') ?></th>
                                                                    <th class="text-center"><?= lang('Số tua in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_luong_san_xuat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['dau_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['nang_suat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_tua_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_mo_lsx']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ve_nvl_du_kien']) ? _d($vI['ngay_ve_nvl_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ban_giao_san_xuat']) ? _d($vI['ngay_ban_giao_san_xuat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ket_thuc']) ? _d($vI['ngay_ket_thuc']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 4) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Số lượng thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('thợ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất đầu in trắng/đen:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_dau_in_trang_den']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất đầu in màu:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_dau_in_mau']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài in trắng/đen:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_canh_bai_in_trang_den']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài in màu:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_canh_bai_in_mau']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Số tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số mặt in') ?></th>
                                                                    <th class="text-center"><?= lang('Loại') ?></th>
                                                                    <th class="text-center"><?= lang('Năng suất') ?></th>
                                                                    <th class="text-center"><?= lang('Số tua in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú 2') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mat_in']) ?></td>
                                                                            <td class="text-center"><?= $vI['loai'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['nang_suat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_tua_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_mo_lsx']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ve_nvl_du_kien']) ? _d($vI['ngay_ve_nvl_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ban_giao_san_xuat']) ? _d($vI['ngay_ban_giao_san_xuat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ket_thuc']) ? _d($vI['ngay_ket_thuc']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu_2']) ? ($vI['ghi_chu_2']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 5) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Số lượng thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('thợ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất kéo tay:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_keo_tay']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Số tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số mặt in') ?></th>
                                                                    <th class="text-center"><?= lang('Số màu in') ?></th>
                                                                    <th class="text-center"><?= lang('Số tua in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian in') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày mở LSX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bàn giao SX') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày kết thúc') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Bế/Xả/Cắt') ?></th>
                                                                    <th class="text-center"><?= lang('Hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mat_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mau_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_tua_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_mo_lsx']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ve_nvl_du_kien']) ? _d($vI['ngay_ve_nvl_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ban_giao_san_xuat']) ? _d($vI['ngay_ban_giao_san_xuat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_ket_thuc']) ? _d($vI['ngay_ket_thuc']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['be_xa_cat']) ? ($vI['be_xa_cat']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['hoan_thanh']) ? ($vI['hoan_thanh']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 6) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Nhóm thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nhom_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('nhóm') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số mặt') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng tua') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành màng') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Công đoạn') ?></th>
                                                                    <th class="text-center"><?= lang('Bế/ Xả/ Khoan') ?></th>
                                                                    <th class="text-center"><?= lang('Hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mat_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_tua']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_thoi_gian']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_mang']) ? _d($vI['ngay_hoan_thanh_mang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['stage_name']) ? ($vI['stage_name']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['be_xa_khoan']) ? ($vI['be_xa_khoan']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['hoan_thanh']) ? ($vI['hoan_thanh']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 7) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Nhóm thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nhom_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('nhóm') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số mặt phun bóng') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng tua') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Công đoạn') ?></th>
                                                                    <th class="text-center"><?= lang('Bồi') ?></th>
                                                                    <th class="text-center"><?= lang('Bế/ Xả/ Khoan lỗ 2') ?></th>
                                                                    <th class="text-center"><?= lang('Hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_mat_phun_bong']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_tua']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_thoi_gian']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_bong']) ? _d($vI['ngay_hoan_thanh_bong']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['stage_name']) ? ($vI['stage_name']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['boi']) ? ($vI['boi']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['be_xa_khoan_lo_2']) ? ($vI['be_xa_khoan_lo_2']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['hoan_thanh']) ? ($vI['hoan_thanh']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 8) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Nhóm thợ:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nhom_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('nhóm') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy bồi 1 mặt:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_boi_mot_mat']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy bồi 2 mặt:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_boi_hai_mat']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-right">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/tờ') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/KB') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng tua') ?></th>
                                                                    <th class="text-center"><?= lang('Loại bồi') ?></th>
                                                                    <th class="text-center"><?= lang('Năng suất') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian Xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành màng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Bế/ Xả/ Khoan') ?></th>
                                                                    <th class="text-center"><?= lang('Hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_kb']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_tua']) ?></td>
                                                                            <td class="text-center"><?= ($vI['loai_boi']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['nang_suat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_thoi_gian']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_bong']) ? _d($vI['ngay_hoan_thanh_bong']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_mang']) ? _d($vI['ngay_hoan_thanh_mang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['be_xa_khoan']) ? ($vI['be_xa_khoan']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['hoan_thanh']) ? ($vI['hoan_thanh']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($value['type_productionlist_id'] == 9) : ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <table class="table dataTable table-bordered" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="8" class="text-center bold color-white" style="background: #607d8b;"><?= $value['code'] ?></td>
                                                                <td><?= lang('Số lượng máy:') ?></td>
                                                                <td style="width: 150px;" class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['so_luong_may']) : '' ?>
                                                                </td>
                                                                <td><?= lang('máy') ?></td>
                                                                <td style="width: 50px;"></td>
                                                                <td style="width: 80px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Nhóm thợ:') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['nhom_tho']) : '' ?>
                                                                </td>
                                                                <td><?= lang('nhóm') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy (bế giấy thường):') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_be_giay_thuong']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Năng suất máy (demi, bế giấy bồi/ PET):') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['nang_suat_may_demi_be_giay_boi_pet']) : '' ?>
                                                                </td>
                                                                <td><?= lang('tua/giờ') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian canh bài:') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['_thoi_gian_canh_bai']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ/bài') ?></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_chuan']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= lang('Thời gian làm việc có OT:') ?></td>
                                                                <td class="text-center">
                                                                    <?= !empty($value) ? formatNumber($value['thoi_gian_lam_viec_ot']) : '' ?>
                                                                </td>
                                                                <td><?= lang('giờ') ?></td>
                                                                <td><?= lang('Capacity') ?></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <?php
                                                    $production_lists_date = $this->production_list_model->getProductionListsDatePLT($value['id']);
                                                    ?>
                                                    <?php if (!empty($production_lists_date)) : ?>
                                                        <?php
                                                        $chunkArrDate = array_chunk($production_lists_date, 7);
                                                        ?>
                                                        <table class="table dataTable table-bordered table-date" style="width: 100%;">
                                                            <tbody>
                                                                <?php if (!empty($chunkArrDate)) : ?>
                                                                    <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                                                        <tr>
                                                                            <?php
                                                                            $tdRow2 = '';
                                                                            ?>
                                                                            <?php foreach ($arDate as $k => $v) : ?>
                                                                                <td class="text-center"><?= _d($v['date_handling']) ?></td>
                                                                                <?php
                                                                                $tdRow2 .= '<td style="padding: 0;" class="text-center">
                                                                                    ' . formatNumber($v['thoi_gian_xu_ly']) . '
                                                                                </td>';
                                                                                ?>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <tr class="tr-sum">
                                                                            <?= $tdRow2 ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <div class="table-responsive">
                                                        <table id="table-<?= $value['type_productionlist_id'] ?>" class="table table-hover dataTable table-items">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                                                                    <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                                                    <th class="text-center"><?= lang('Tờ in') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/tờ') ?></th>
                                                                    <th class="text-center"><?= lang('Số con/KB') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng tua') ?></th>
                                                                    <th class="text-center"><?= lang('Loại giấy') ?></th>
                                                                    <th class="text-center"><?= lang('Năng suất') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                                                                    <th class="text-center"><?= lang('Tổng thời gian') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng hệ thống') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày giao hàng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành bóng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành cán màng') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành bồi') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành lụa') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành flexo') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành HP') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                                                                    <th class="text-center"><?= lang('Ngày hoàn thành') ?></th>
                                                                    <th class="text-center"><?= lang('Tình trạng') ?></th>
                                                                    <th class="text-center"><?= lang('Ghi chú') ?></th>
                                                                    <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                                                                    <th class="text-center"><?= lang('Công đoạn') ?></th>
                                                                    <th class="text-center"><?= lang('Máy in') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $items = $this->production_list_model->getProductionListsItemsView($value['id']);
                                                                ?>
                                                                <?php if (!empty($items)) : ?>
                                                                    <?php foreach ($items as $kI => $vI) : ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $vI['reference_no'] ?></td>
                                                                            <td class="text-center"><?= $vI['item_code'] ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_to_in']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['so_con_tren_kb']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_tua']) ?></td>
                                                                            <td class="text-center"><?= ($vI['loai_giay']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['nang_suat']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_xu_ly']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['thoi_gian_canh_bai']) ?></td>
                                                                            <td class="text-center"><?= formatNumber($vI['tong_thoi_gian']) ?></td>
                                                                            <td class="text-center"><?= _d($vI['ngay_giao_hang_he_thong']) ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_giao_hang']) ? _d($vI['ngay_giao_hang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_bong']) ? _d($vI['ngay_hoan_thanh_bong']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_can_mang']) ? _d($vI['ngay_hoan_thanh_can_mang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_boi']) ? _d($vI['ngay_hoan_thanh_boi']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_lua']) ? _d($vI['ngay_hoan_thanh_lua']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_flexo']) ? _d($vI['ngay_hoan_thanh_flexo']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_hp']) ? _d($vI['ngay_hoan_thanh_hp']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_bat_dau_du_kien']) ? _d($vI['ngay_bat_dau_du_kien']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ngay_hoan_thanh_in']) ? _d($vI['ngay_hoan_thanh_in']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['tinh_trang']) ? ($vI['tinh_trang']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['ghi_chu']) ? ($vI['ghi_chu']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['thoi_gian_con_lai']) ? formatNumber($vI['thoi_gian_con_lai']) : '' ?></td>
                                                                            <td class="text-center"><?= !empty($vI['stage_name']) ? ($vI['stage_name']) : '' ?></td>
                                                                            <td class="text-center"><?= $vI['machines_name'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <script>
                                            var dtItems<?= $value['type_productionlist_id'] ?> = '';
                                            var arrItems = {};
                                            $(document).ready(function () {
                                                dtItems<?= $value['type_productionlist_id'] ?> = $('#table-<?= $value['type_productionlist_id'] ?>').DataTable({
                                                    "language": app.lang.datatables,
                                                    "pageLength": app.options.tables_pagination_limit,
                                                    // "lengthMenu": dataTableLengthMenu(),
                                                    // "responsive": true,
                                                    scrollY: true,
                                                    scrollX: true,
                                                    fixedColumns:   {
                                                        leftColumns: 3,
                                                    },
                                                    'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                                                    "initComplete": function(settings, json) {
                                                        var t = this;
                                                        t.parents('.table-loading').removeClass('table-loading');
                                                        t.removeClass('dt-table-loading');
                                                    },
                                                    "footerCallback": function(row, data, start, end, display) {
                                                    },
                                                    'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                                                    buttons: [{
                                                        extend: "excel",
                                                        text: app.lang.dt_button_excel,
                                                        footer: !0,
                                                        exportOptions: {
                                                            columns: [":not(.not-export)"],
                                                            rows: function (t) {
                                                                return _dt_maybe_export_only_selected_rows(t, $('#table-<?= $value['type_productionlist_id'] ?>'))
                                                            },
                                                            format: {
                                                                body: function(data, row, column, node) {
                                                                    data = $('<p>' + data + '</p>').text();
                                                                    return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                                                                }
                                                            }
                                                        },
                                                    }],
                                                });

                                                arrItems[<?= $value['type_productionlist_id'] ?>] = dtItems<?= $value['type_productionlist_id'] ?>;
                                                // setTimeout(function() {
                                                //     dtItems<?//= $value['type_productionlist_id'] ?>.columns.adjust().draw();
                                                // }, 2000);
                                                init_selectpicker();
                                            });
                                        </script>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    function clickTab(_type) {
        setTimeout(() => {
            arrItems[_type].columns.adjust().draw();
        }, 1000);
    }

    $(document).ready(function() {
        
    });
</script>