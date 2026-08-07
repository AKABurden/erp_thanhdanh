<style>
    .select2-choice {
        height: auto !important;
    }

    .tnh-tb td label,
    .item-purchases th {
        text-transform: capitalize;
    }

    #top_search_buttons {
        position: absolute;
        right: 2px;
        top: 3px;
        z-index: 9999999;
    }
</style>
<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 85%;">
        <?php echo form_open(admin_url('internal_proposal/add'), array('id' => 'add-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo $title; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hide">
                            <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo $object->id ?>">
                        </div>
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="">
                            <tbody>
                                <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_info_general') ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;">
                                        <label for="date" class="control-label">
                                            <small class="req text-danger">* </small>
                                            1.1 <?php echo _l('intpro_date'); ?>
                                        </label>
                                    </td>
                                    <td style="width: 30%;">
                                        <?= form_input('date', !empty($object) ? $object->date : _dt(date('Y-m-d H:i:s')), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                    </td>
                                    <td style="width: 15%;">
                                        <label for="id_branch" class="control-label">
                                            6. <?php echo _l('id_branch'); ?>
                                        </label>
                                    </td>
                                    <?php
                                    if (empty($branch)) {
                                        $branch = get_table_where('tblbranch');
                                    }
                                    ?>
                                    <td style="width: 40%;"><?php echo render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], '', (!empty($object->id_branch) ? $object->id_branch : 0)) ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;">
                                        <label for="date_finish" class="control-label">
                                            <small class="req text-danger">* </small>
                                            1.2 <?php echo _l('Ngày hoàn thành'); ?>
                                        </label>
                                    </td>
                                    <td style="width: 30%;">
                                        <?= form_input('date_finish', !empty($object) ? $object->date_finish : '', 'id="date" class="form-control datetimepicker" autocomplete="off" placeholder="' . lang('Ngày hoàn thành') . '" required ') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="">
                                        <label for="code" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('2. Mã Đề Xuất'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo form_input('code', $object->code, 'placeholder="' . lang('intpro_code') . '" id="code" class="form-control input-tip"'); ?>
                                    </td>
                                    <td class="hide">
                                        <?= lang('7. Khối-Phòng', 'id_departments') ?>
                                    </td>
                                    <td class="hide">
                                        <?php
                                        $departmentsN = $this->site_model->getRoom();
                                        $value = !empty($object->id_departments) ? $object->id_departments : ''
                                        ?>
                                        <?= render_select('id_departments', (!empty($departmentsN) ? $departmentsN : []), ['id', 'name'], '', $value) ?>
                                    </td>
                                    <td>
                                        <?= lang('7. Vị trí', 'search_role') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $boardN = $this->site_model->getBoard();
                                        $blockN = $this->site_model->getBlock();
                                        ?>
                                        <div class="col-md-4">
                                            <?= render_select('id_board_search', (!empty($boardN) ? $boardN : []), ['id', 'name'], 'Lọc vị trí theo hội ban', '', ['onchange' => 'changeRoles();']) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= render_select('id_block_search', (!empty($blockN) ? $blockN : []), ['id', 'name'], 'Lọc vị trí theo khối', '', ['onchange' => 'changeRoles();']) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= render_select('id_departments_search', (!empty($departmentsN) ? $departmentsN : []), ['id', 'name'], 'Lọc vị trí theo phòng', '', ['onchange' => 'changeRoles();']) ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="mbot10"></div>
                                        <?php
                                        if (!empty($object->id_departments)) {
                                            $this->db->where('tblroles.departments_id', $object->id_departments);
                                            $this->db->where('tblroles.active_role', 1);
                                            $data_roles = $this->db->get('tblroles')->result_array();
                                        }
                                        $this->db->where('tblroles.type', 0);
                                        $this->db->where('tblroles.active_role', 1);
                                        $data_roles = $this->db->get('tblroles')->result_array();
                                        ?>
                                        <label for="role_id" class="control-label">Vị trí</label>
                                        <select id="role_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option></option>
                                            <?php if (!empty($data_roles)) {
                                                foreach ($data_roles as $key => $value) { ?>
                                                    <option data-subtext="<?= $value['code_role'] ?>" value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang('3 Nhóm đề xuất', 'recommended_list_group_id') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dtRecommendedListG = $this->recommended_list_model->getRecommendedListParent([0], 1);
                                        // $dtRecommendedListG = null;
                                        // $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$object->recommended_list_group_id]);
                                        // if (!empty($object->recommended_list_group_id)) {
                                        //     $this->db->select(
                                        //         'tbl_recommended_list.id as id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
                                        //         false
                                        //     );
                                        //     $this->db->from('tbl_recommended_list');
                                        //     $this->db->where('tbl_recommended_list.id', $object->recommended_list_group_id);
                                        //     $dtRecommendedListG = $this->db->get()->result_array();
                                        // }
                                        ?>
                                        <select name="recommended_list_group_id" id="recommended_list_group_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Nhóm đề xuất') ?>" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedListG)) : ?>
                                                <?php foreach ($dtRecommendedListG as $key => $value) : ?>
                                                    <option data-type_plan_propose="<?= $value['type_plan_propose'] ?>" data-bonus="<?= $value['type_bonus'] ?>" <?= ((!empty($object->recommended_list_group_id) && $object->recommended_list_group_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>

                                    <td style="">
                                        <label for="staff" class="control-label">
                                            <small class="req text-danger">* </small>
                                            8. <?php echo _l('Người lập đề xuất'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select name="staff" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('staff') ?>" id="staff" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($staff_list_all)) : ?>
                                                <?php foreach ($staff_list_all as $key => $value) : ?>
                                                    <option <?= ($object->staff == $value['staffid'] ? 'selected' : '') ?> data-department="<?= $value['name_department'] ?>" value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>


                                    <td>
                                        <?= lang('4.1 Loại đề xuất', 'recommended_list_id') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dtRecommendedList = null;
                                        if (!empty($object->recommended_list_group_id)) {
                                            $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$object->recommended_list_group_id]);
                                        }
                                        // $this->db->select(
                                        //     'tbl_recommended_list.id as id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
                                        //     false
                                        // );
                                        // $this->db->from('tbl_recommended_list');
                                        // $this->db->where('tbl_recommended_list.type_show', 1);
                                        // $this->db->where('tbl_recommended_list.parent_id >', 0);
                                        // $dtRecommendedList = $this->db->get()->result_array();
                                        ?>
                                        <select name="recommended_list_id" id="recommended_list_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết đề xuất') ?>" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedList)) : ?>
                                                <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                    <option <?= ((!empty($object->recommended_list_id) && $object->recommended_list_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td style="">
                                        <label for="staff_assigned" class="control-label">
                                            <?php echo _l('9. Người duyệt đề xuất'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->staff_assigned) ? $object->staff_assigned : [] ?>
                                        <?php echo render_select('staff_assigned', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>
                                </tr>

                                </tr>
                                <tr>
                                    <td style="">
                                        <label for="type_plan_propose" class="control-label">
                                            <?php echo _l('4.2 Loại kế hoạch'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="form-group" app-field-wrapper="type_plan_propose">
                                            <select name="type_plan_propose" id="type_plan_propose" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                                <option></option>
                                                <?php if (!empty($type_plan_propose)) : ?>
                                                    <?php foreach ($type_plan_propose as $key => $value) : ?>
                                                        <option <?= ((!empty($object->type_plan_propose) && $object->type_plan_propose == $value['id']) ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="">
                                        <label for="staff_bod" class="control-label">
                                            <?php echo _l('10. Người duyệt thực thi'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->staff_bod) ? $object->staff_bod : [] ?>
                                        <?php echo render_select('staff_bod', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>


                                </tr>

                                <td></td>
                                <td></td>
                                <td style="">
                                    <label for="manager_id" class="control-label">
                                        <?php echo _l('11. Người hoàn thành 1'); ?>
                                    </label>
                                </td>
                                <td>
                                    <?php $value = !empty($object->manager_id) ? $object->manager_id : [] ?>
                                    <?php echo render_select('manager_id', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                </td>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="head_of_department_id" class="control-label">
                                            <?php echo _l('12. Người Hoàn Thành 2'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->head_of_department_id) ? $object->head_of_department_id : [] ?>
                                        <?php echo render_select('head_of_department_id', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="staff_controller_completes" class="control-label">
                                            <?php echo _l('13. Người Hoàn Thành 3'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->staff_controller_completes) ? $object->staff_controller_completes : [] ?>
                                        <?php echo render_select('staff_controller_completes', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="staff_auditor_completes" class="control-label">
                                            <?php echo _l('14. Người Hoàn Thành 4'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->staff_auditor_completes) ? $object->staff_auditor_completes : [] ?>
                                        <?php echo render_select('staff_auditor_completes', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="auditor_id" class="control-label">
                                            <?php echo _l('15. Người kiểm soát hoàn thành'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->auditor_id) ? $object->auditor_id : [] ?>
                                        <?php echo render_select('auditor_id', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="monitor_id" class="control-label">
                                            <?php echo _l('16. Người Kiểm Toán Hoàn Thành'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->monitor_id) ? $object->monitor_id : [] ?>
                                        <?php echo render_select('monitor_id', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value) ?>
                                    </td>
                                </tr>
                                <tr>

                                    <?php
                                    $dtRecommendedList = null;
                                    if (!empty($object->recommended_list_id)) {
                                        $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$object->recommended_list_id]);
                                    }
                                    $arrId = [];
                                    if (!empty($object) && !empty($object->recommended_list)) {
                                        foreach ($object->recommended_list as $kk => $vv) {
                                            $arrId[] = $vv['recommended_list_detail_id'];
                                        }
                                    }
                                    ?>
                                    <td>
                                        <?= lang('5. Chi tiết đề xuất', 'recommended_list_detail_id') ?>
                                    </td>
                                    <td>
                                        <select name="recommended_list_detail_id[]" id="recommended_list_detail_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết đề xuất') ?>" class="selectpicker" multiple>
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedList)) : ?>
                                                <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                    <option <?= ((!empty($arrId) && in_array($value['id'], $arrId)) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>

                                    <td class="hide" style="">
                                        <label for="id_purchase_order" class="control-label">
                                            <?php echo _l('14. Phiếu mua hàng (PO)'); ?>
                                        </label>
                                    </td>
                                    <td class="hide">
                                        <?php $value = !empty($object->id_purchase_order) ? $object->id_purchase_order : '' ?>
                                        <select id="id_purchase_order" name="id_purchase_order" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option value=""></option>
                                            <?php if (!empty($purchase_order)) { ?>
                                                <?php foreach ($purchase_order as $key => $value_purchase_order) { ?>
                                                    <option value="<?= $value_purchase_order['id'] ?>" <?= $value_purchase_order['id'] == $value ? 'selected' : '' ?> data-subtext="<?= $value_purchase_order['company'] ?> - <?= number_format_data($value_purchase_order['total_dqd']) ?>" data-total="<?= $value_purchase_order['total_dqd'] ?>"><?= $value_purchase_order['fullcode'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td style="">
                                        <label for="id_purchases" class="control-label">
                                            <?php echo _l('17. Phiếu YCMH'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = (!empty($object->id_purchases) && $object->id_purchases == -1) ? $internal_proposal_purchase : (!empty($object->id_purchases) ? [$object->id_purchases] : [])  ?>
                                        <?php echo render_select('id_purchases[]', (!empty($purchases) ? $purchases : []), ['id', ['prefix', 'code'], 'explanation'], '', $value, ['onchange' => 'loadItemsPurchase()', 'multiple' => true, 'data-actions-box' => true], array(), '', '', false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="html_decision <?= !empty($object) && !empty($object->type_bonus) ? ($object->type_bonus == 1 ? '' : 'hide') : 'hide' ?>">
                                            <?= lang('5.1. Phiếu quyết định khen thưởng-kỷ luật', 'decision_bonus_discipline_id') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="html_decision <?= !empty($object) && !empty($object->type_bonus) ? ($object->type_bonus == 1 ? '' : 'hide') : 'hide' ?>">
                                            <select onchange="changDataDecision(this)" name="decision_bonus_discipline_id" id="decision_bonus_discipline_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Phiếu quyết định khen thưởng-kỷ luật') ?>" class="selectpicker">
                                                <option value=""></option>
                                                <?php if (!empty($dtDecision)) { ?>
                                                    <?php foreach ($dtDecision as $key => $value) { ?>
                                                        <option <?= !empty($object->decision_bonus_discipline_id) && $object->decision_bonus_discipline_id == $value['id'] ? 'selected' : ''  ?> data-subtext="<?= $value['name_quota'] . '(' . $value['name_quy'] . ')' ?>" value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <label for="id_service" class="control-label">
                                            <?php echo _l('18. Phiếu dịch vụ'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->id_service) ? $object->id_service : '' ?>
                                        <?php echo render_select('id_service', (!empty($services) ? $services : []), ['id', ['prefix', 'code'], 'subtotal'], '', $value) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <?= lang('19. Loại phiếu yêu cầu', 'category_recommended_id') ?>
                                    </td>
                                    <td>
                                        <select name="category_recommended_id" data-none-selected-text="Loại phiếu yêu cầu" data-live-search="true" id="category_recommended_id" class="form-control selectpicker category_recommended_id">
                                            <option value=""></option>
                                            <?php if (!empty($categoryRecommended)) : ?>
                                                <?php foreach ($categoryRecommended as $key => $value) : ?>
                                                    <option <?= !empty($object->category_recommended_id) && $object->category_recommended_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="id_service" class="control-label">
                                            <?php echo _l('20. Phiếu yêu cầu'); ?>
                                        </label>
                                    </td>
                                    <td class="suggest_id <?= !empty($object->category_recommended_id) && $object->category_recommended_id == 41 ? 'hide' : '' ?>">
                                        <?php $valueSelected = !empty($object->suggest_id) ? $object->suggest_id : '' ?>
                                        <select class="form-control suggest_id selectpicker" name="suggest_id" id="suggest_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                            <option></option>
                                            <?php if (!empty($dtSuggest)) { ?>
                                                <?php foreach ($dtSuggest as $key => $value) { ?>
                                                    <option data-subtext="<?= $value['staff_suggest_name'] ?>" <?= ($value['id'] == $valueSelected) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="suggest_id_muti <?= !empty($object->category_recommended_id) ?  ($object->category_recommended_id == 41 ? '' : 'hide')  : 'hide' ?>">
                                        <?php
                                        $valueSelected = [];
                                        if (!empty($object->category_recommended_id) && $object->category_recommended_id == 41) {
                                            $suggest_id_muti = get_table_where('tbl_suggest_muti_id', ['id_internal_proposal' => $object->id]);
                                            foreach ($suggest_id_muti as $key => $value) {
                                                $valueSelected[] = $value['suggest_id'];
                                            }
                                        }
                                        ?>

                                        <select class="form-control suggest_muti_id selectpicker" name="suggest_muti_id[]" multiple="1" id="suggest_muti_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                            <option></option>
                                            <?php if (!empty($dtSuggest)) { ?>
                                                <?php foreach ($dtSuggest as $key => $value) { ?>
                                                    <option data-subtext="<?= $value['staff_suggest_name'] ?>" <?= (in_array($value['id'], $valueSelected)) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="">
                                        <label for="proposal_employee" class="control-label">
                                            21. <?php echo _l('Nhân viên phụ trách'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        $value = [];
                                        if (!empty($object)) {
                                            $this->db->from('tblinternal_proposal_employee');
                                            $this->db->where('tblinternal_proposal_employee.internal_proposal', $object->id);
                                            $dtData = $this->db->get()->result_array();
                                            if (!empty($dtData)) {
                                                foreach ($dtData as $kk => $vv) {
                                                    $value[] = $vv['staff_id'];
                                                }
                                            }
                                        }
                                        ?>
                                        <?php echo render_select('proposal_employee[]', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']], '', $value, ['multiple' => true, 'required' => true]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="money" class="control-label">
                                            BCKPH
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->production_report) ? $object->production_report : '';?>
                                        <?php echo render_select('production_report', (!empty($production_report) ? $production_report : []), ['id', 'reference_no'], '', $value) ?>
                                    </td>
                                    <td style="">
                                        <label for="money" class="control-label">
                                            22. <?php echo _l('intpro_money'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" id="money" onkeyup="formatNumBerKeyUp(this)" name="money" class="form-control " value="<?= number_format_data($object->money) ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="">
                                        <label for="category_tasks" class="control-label">
                                            <?php echo _l('Mã công việc'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="form-group" app-field-wrapper="category_tasks">
                                            <select name="category_tasks" id="category_tasks" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                                <option value=""></option>
                                                <?php if (!empty($category_tasks)) : ?>
                                                    <?php foreach ($category_tasks as $key => $value) : ?>
                                                        <option <?= ((!empty($object->category_tasks) && $object->category_tasks == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['content'] ?>" data-departments="<?= $value['departments'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="">
                                        <label for="" class="control-label">
                                            <?php echo _l('Phòng ban công việc'); ?>
                                        </label>
                                    </td>
                                    <td class="txt-type_name">
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <td style="">
                                        <label for="id_other_payslips" class="control-label">
                                            <?php echo _l('Phiếu chi khác'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->id_other_payslips) ? $object->id_other_payslips : '' ?>
                                        <?php echo render_select('id_other_payslips', (!empty($other_payslips) ? $other_payslips : []), ['id', 'fullcode', 'total'], '', $value) ?>
                                    </td>
                                    <td style="">
                                        <label for="type_object" class="control-label">
                                            <?php echo _l('Liên quan đến'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->type_object) ? $object->type_object : '' ?>
                                        <select id="type_object" name="type_object" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option value=""></option>
                                            <?php if (!empty($type_object)) { ?>
                                                <?php foreach ($type_object as $id_type => $name_type) { ?>
                                                    <option value="<?= $id_type ?>" <?= $id_type == $value ? 'selected' : '' ?>><?= $name_type ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <!-- Chi tiết ycmh -->
                                <tr>
                                    <td colspan="4">
                                        <div class="col-md-12">
                                            <div class="purchase_default_area">
                                                <table style="width: 50%;float: right;table-layout: fixed;" class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 25%">
                                                                <a type="submit" href="<?= base_url('uploads/import_inventory.xlsx?vs=1.0') ?>" class="btn btn-success">Tải mẫu import</a>
                                                            </td>
                                                            <td style="width: 50%">
                                                                <?php echo render_input('file_csv', '', '', 'file'); ?>
                                                            </td>
                                                            <td style="width: 25%">
                                                                <a href="#" id="import_export_client" class="btn btn-warning btn-icon" style="float: right;"><?= _l('import mặt hàng') ?></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <br>
                                                <!-- hauhau -->
                                                <div class="form-group">
                                                    <?= lang('23. Chi tiết yêu cầu', 'title', ['style' => 'text-transform: capitalize']) ?>
                                                    <div id="top_search_purchase" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 420px;">
                                                        <input type="search" id="SearchQR_purchase" class="form-control" placeholder="<?php echo _l('Quét QR...'); ?>">
                                                        <div id="search_results"></div>
                                                        <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history"></ul>
                                                        <div id="top_search_buttons">
                                                            <!-- <i class="fa fa-barcode" aria-hidden="true"></i> -->
                                                            <a class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                    <table class="dt-tnh table item-purchases table-bordered table-hover" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="200" class="text-left"></i> <?php echo _l('ch_items_name_t'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng đề xuất'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="110" class="text-center"><?php echo _l('Số lượng PO ĐV chuẩn'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="110" class="text-center"><?php echo _l('Số lượng PO ĐV kho'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV thanh toán'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Đơn giá'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thuế'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thành tiền'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhóm nhà cung cấp'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhà cung cấp'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Ghi chú đề xuất'); ?></th>
                                                                <th style="border-top: 1px solid #b4b9bf!important" width="50" class="text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table_purchase">
                                                            <?php echo $tbody ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <td>Tổng</td>
                                                            <td class="tfood_sldx text-center"></td>
                                                            <td class="tfood_slc text-center"></td>
                                                            <td class="tfood_slk text-center"></td>
                                                            <td class="tfood_slp text-center"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="tfood_total text-right"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tfoot>
                                                    </table>

                                                    <div class="table_category_kpi">
                                                        <?= !empty($html) ? $html : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="excel_proposal_area hide">
                                                <div class="form-group">
                                                    <label for="excel_proposal_file">Nhập tệp Excel dữ liệu</label>
                                                    <input type="file" name="excel_proposal_file" id="excel_proposal_file" class="form-control" accept=".xlsx, .xls">
                                                </div>
                                                <div class="excel_proposal_table_container mtop15" style="margin-top: 15px;">
                                                    <?php if (!empty($object->excel_data)): ?>
                                                        <?php 
                                                            $rows_data = json_decode($object->excel_data, true);
                                                            if (is_array($rows_data) && !empty($rows_data)) {
                                                                $headers = array_keys($rows_data[0]);
                                                                echo '<div class="table-responsive" style="max-height: 400px; overflow: auto; width: 100%; max-width: 0; min-width: 100%;">';
                                                                echo '<table class="table table-bordered table-striped" style="margin-bottom: 0; white-space: nowrap; width: 100%;">';
                                                                echo '<thead><tr class="success">';
                                                                foreach ($headers as $header) {
                                                                    echo '<th class="text-center bold" style="min-width: 150px; white-space: nowrap;">' . htmlspecialchars($header) . '</th>';
                                                                }
                                                                echo '</tr></thead>';
                                                                echo '<tbody>';
                                                                foreach ($rows_data as $row) {
                                                                    echo '<tr>';
                                                                    foreach ($headers as $header) {
                                                                        echo '<td>' . htmlspecialchars($row[$header] ?? '') . '</td>';
                                                                    }
                                                                    echo '</tr>';
                                                                }
                                                                echo '</tbody>';
                                                                echo '</table>';
                                                                echo '</div>';
                                                            }
                                                        ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Nội dung -->
                                <tr>
                                    <td colspan="4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                24. <?= lang('internal_proposal_content', 'title', ['style' => 'text-transform: capitalize']) ?>
                                                <?php echo form_textarea('content', $object->content, 'placeholder="' . lang('internal_proposal_content') . '" id="content" class="form-control input-tip tinymce"'); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="col-md-12">
                                            <div class="dropzone dropzone-manual">
                                                <div id="dropzoneTaskComment" class="dropzoneDragArea dz-default dz-message task-comment-dropzone">
                                                    <span><?php echo _l('drop_files_here_to_upload'); ?></span>
                                                </div>
                                                <div class="dropzone-task-comment-previews dropzone-previews"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#add_modal').modal({
        show: true,
        backdrop: 'static'
    });
    init_selectpicker();
    init_datepicker();
    init_editor('textarea[name="content"]');
    var key_departments = <?= !empty($key_departments) ? json_encode($key_departments) : '[]' ?>;
    // file upload
    Dropzone.options.expenseForm = false;
    var expenseDropzone;
    if ($('#dropzoneTaskComment').length > 0) {
        expenseDropzone = new Dropzone('#add-form', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneTaskComment',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }
    $('#id_other_payslips').change(function() {
        var total = $(this).find('option:selected').data('subtext');
        $('#money').val(tnhFormatNumber(total));
    })

    function addtotal() {
        var total_service = 0;
        if ($('#id_service').find('option:selected').data('subtext')) {
            var total_service = $('#id_service').find('option:selected').data('subtext');
        }
        var tfood_total = $('#add_modal').find('.tfood_total').text().replace(/\,/g, '');
        // tfood_total 
        $('#money').val(tnhFormatNumber(total_service + tfood_total));
    }
    $('#id_service, #id_purchase_order').change(function() {
        var total_service = 0;
        if ($('#id_service').find('option:selected').data('subtext')) {
            var total_service = $('#id_service').find('option:selected').data('subtext');
        }
        var total_purchase_order = 0;
        if ($('#id_purchase_order').find('option:selected').data('total')) {
            total_purchase_order = $('#id_purchase_order').find('option:selected').data('total');
        }
        // $('#money').val(tnhFormatNumber(total_service + total_purchase_order));
        addtotal();
    })
    // Chọn người đề xuất
    $('#staff').change(function(event) {
        department = $("#staff").select().find(":selected").data("department");
        // alert(department);
        $('.txt-department').html(department);
    })
    // Chọn Loại đề xuất
    $('#category_tasks').change(function(event) {
        departments = $("#category_tasks").find("option:selected").data("departments");
        var list = [];
        if (departments) {
            departments = departments + '';
            var list = departments ? departments.split(",") : '';
        }
        var subtext = "";
        $.each(list, function(index, value) {
            if (key_departments[value]) {
                subtext += key_departments[value] + ',';
            }
        })
        $('.txt-type_name').html(subtext);
    })

    $('#category_tasks').trigger('change');
    $(function() {
        appValidateForm($('#add-form'), {
            code: 'required',
            date: 'required',
            date_finish: 'required',
            staff: 'required',
            staff_bod: 'required',
            staff_assigned: 'required',
            monitor_id: 'required',
            manager_id: 'required',
            auditor_id: 'required',
            staff_controller_completes: 'required',
            staff_auditor_completes: 'required',
            head_of_department_id: 'required',
            category_tasks: 'required',
            type_plan_propose: 'required',
            id_branch: 'required',
        }, manage);

        function manage(form) {

            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(expenseDropzone.files, function(index, value) {
                formData.append('file[]', value);
            })
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            var button = $(form).find('button[type="submit"]');
            button.button({
                loadingText: 'please wait...'
            });
            button.button('loading');

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(response) {
                    if (response.success == true) {
                        alert_float('success', response.message);
                        oTable.draw(false);
                        $('#add_modal').modal('hide');
                    } else {
                        alert_float('danger', response.message);
                    }
                })
                .always(function() {
                    button.button('reset');
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                    button.button('reset');
                });
            return false;
        }
    });

    $(document).on('hide.bs.modal', '#add_modal', function() {
        tinyMCE.remove();
    });

    // $(document).on('change', 'select[name="id_purchases"]', function(e) {

    // });
    function loadItemsPurchase(type) {
        var id_purchases = $('select[name="id_purchases[]"]').val();

        // custom_item_select.find('option:gt(0)').remove();
        // custom_item_select.selectpicker('refresh');
        id_purchases_text = '';
        if (id_purchases.length) {
            $.each(id_purchases, function(i, v) {
                id_purchases_text += v + '_';
            });
            $.ajax({
                    url: admin_url + 'internal_proposal/items_purchases/' + id_purchases_text,
                    dataType: 'json',
                })
                .done(function(data) {
                    $('.table_purchase').html(data.tbody);
                    for (let index = 0; index < data.dem; index++) {
                        var id_supp = $('#suppliers_id_' + index).attr('data-id_supp');
                        var company_supp = $('#suppliers_id_' + index).attr('data-company_supp');
                        if (id_supp == 0) {
                            ajaxSelectCallBack($('#suppliers_id_' + index), "<?= admin_url('suppliers/SearchSupplierss') ?>", 0);
                        } else {
                            var txtJson = {
                                id: id_supp,
                                text: company_supp
                            };
                            ajaxSelectCallBack($('#suppliers_id_' + index), "<?= admin_url('suppliers/SearchSupplierss') ?>", id_supp, 0, txtJson);
                            $('#suppliers_id_' + index).change();
                        }
                    }
                    init_selectpicker();
                    getTotalPrice();
                });
        } else {
            $('.table_purchase').html('');
            getTotalPrice();
        }
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };

    function ajaxSelectCallBack(element, url, id, types = '', txtJson = false) {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function(element, callback) {
                    if (txtJson) {
                        callback(txtJson);
                    } else {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: url + '/' + id + '/' + types,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0].children[0]);
                            }
                        });
                    }
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    var base_url = '<?= base_url() ?>';

    function repoFormatSelection_ch(state) {
        if (!state.id) return state.text;

        return state.text;
    }

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parent().parent();
        let recipe = (current_row.find('.recipe').val());
        let paper = (current_row.find('.paper').val());
        let longs = (current_row.find('.longs').val());
        let wide = (current_row.find('.wide').val());
        let mainQuantity = unformat_number(current_row.find('.mainQuantity').val());
        let mainQuantity_suppliers = unformat_number(current_row.find('.mainQuantity_suppliers').val());
        let exchange_standard_unit = unformat_number(current_row.find('.exchange_standard_unit').val());
        let exchange_stock = unformat_number(current_row.find('.exchange_stock').val());
        let exchange_payment = unformat_number(current_row.find('.exchange_payment').val());
        var quantity_stock = Math.round((mainQuantity_suppliers / exchange_stock) * exchange_standard_unit)

        current_row.find('.text_mainquantity_stock').text(tnhFormatNumber(quantity_stock, 0));
        current_row.find('.mainquantity_stock').val((quantity_stock));

        if (recipe == 1) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * exchange_standard_unit)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 2) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * paper / 100)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 3) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * (longs * wide) / 10000)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        }
        let price_suppliers = unformat_number(current_row.find('.price_suppliers').val());
        let tax = unformat_number(current_row.find('.tax_rate').val());
        var total_suppliers = (quantity_payment * price_suppliers) * (1 + tax / 100);

        // current_row.find('.total_expected').text(tnhFormatNumber(total_expected));
        current_row.find('.total_suppliers').text(tnhFormatMoney(total_suppliers));
        getTotalPrice();
    };


    function getTotalPrice() {
        var items = $('table.item-purchases tbody').find('tr');
        var sldx = 0;
        var slc = 0;
        var slk = 0;
        var skp = 0;
        var total = 0;
        $.each(items, (index, value) => {
            // sldx += parseFloat($(value).find('.sldx').text().replace(/\,/g, ''));
            // slc += parseFloat($(value).find('.mainQuantity_suppliers').val().replace(/\,/g, ''));
            // slk += parseFloat($(value).find('.mainquantity_stock').val().replace(/\,/g, ''));
            // skp += parseFloat($(value).find('.mainquantity_payment').val().replace(/\,/g, ''));
            total += parseFloat($(value).find('.total_suppliers').text().replace(/\,/g, ''));
        });
        // $('.tfood_sldx').text(tnhFormatNumber(sldx));
        // $('.tfood_slc').text(tnhFormatNumber(slc));
        // $('.tfood_slk').text(tnhFormatNumber(slk));
        // $('.tfood_skp').text(tnhFormatNumber(skp));
        console.log(total)
        $('.tfood_total').text(tnhFormatNumber(total));
        addtotal();
    }

    function countrow() {

        var items = $('table.item-purchases tbody').find('tr');
        $.each(items, (index, value) => {
            var count = $(value).find('td').find('input.count').val();
            var suppliers_id = $(value).find('td').find('input#suppliers_id_' + count).val();
            $('#price_suppliers_' + count).change();
            // ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id);
            var id_supp = $('#suppliers_id_' + index).attr('data-id_supp');
            var company_supp = $('#suppliers_id_' + index).attr('data-company_supp');
            if (id_supp == 0) {
                ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id);
            } else {
                var txtJson = {
                    id: id_supp,
                    text: company_supp
                };
                ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id, 0, txtJson);
            }
        });
    }
    <?php if (!empty($id)) { ?>
        countrow();
    <?php } ?>
</script>

<script>
    $('#id_departments').change(function() {
        var id_departments = $(this).val();
        $.get(admin_url + 'production_report/get_list_role/' + id_departments, function(data) {
            data = JSON.parse(data);
            $('#role_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#role_id').append(`<option value="${value.roleid}">${value.name}</option>`);
            })
            $('#role_id').selectpicker('refresh');
            $('#role_id').trigger('change');
        })
    });

    function changeRoles() {
        var id_board_search = $('select#id_board_search').val();
        var id_block_search = $('select#id_block_search').val();
        var id_departments_search = $('select#id_departments_search').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_board_search'] = id_board_search;
        data['id_block_search'] = id_block_search;
        data['id_departments_search'] = id_departments_search;
        $.post(admin_url + 'production_report/get_list_role_search', data, function(data) {
            data = JSON.parse(data);
            $('#role_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#role_id').append(`<option data-subtext="${value.code_role}" value="${value.roleid}">${value.name}</option>`);
            })
            $('#role_id').selectpicker('refresh');
            $('#role_id').trigger('change');
        })
    }
    // $('#role_id').change(function() {
    //     var role_id = $('#role_id').val();
    //     var id_departments = $('#id_departments').val();
    //     var data = {};
    //     if (typeof(csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['role_id'] = role_id;
    //     data['id_departments'] = id_departments;
    //     data['internal_proposal'] = 1;
    //     $.post(admin_url + 'production_report/get_list_category_tasks', data, function(data) {
    //         data = JSON.parse(data);
    //         $('#category_tasks').html(`<option></option>`);
    //         $.each(data, function(index, value) {
    //             $('#category_tasks').append(`<option value="${value.id}" data-departments="${value.departments}" data-subtext="${value.content}">${value.code}</option>`);
    //         })
    //         $('#category_tasks').selectpicker('refresh');
    //         $('#category_tasks').trigger('change');
    //     });
    // });
    // $('#recommended_list_id').change(function() {
    //     var recommended_list_id = $(this).val();
    //     var data = {};
    //     if (typeof(csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['id'] = recommended_list_id;

    //     $.post(admin_url + 'production_report/getRecommendedListByParentrecommended_new', data, function(data) {
    //         data = JSON.parse(data);
    //         $('#recommended_list_group_id').html(`<option></option>`);
    //         $.each(data, function(index, value) {
    //             $('#recommended_list_group_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
    //         })
    //         $('#recommended_list_group_id').selectpicker('refresh');
    //     });
    // });
    $('#recommended_list_group_id').change(function() {
        var recommended_list_group_id = $(this).val();
        type_bonus = $('option:selected', this).attr("data-bonus");
        type_plan_propose = $('option:selected', this).attr("data-type_plan_propose");
        console.log(type_plan_propose)
        $('[name="type_plan_propose"]').val(type_plan_propose).selectpicker('refresh');
        if (type_bonus == 1) {
            $(".html_decision").removeClass('hide');
        } else {
            $(".html_decision").addClass('hide');
        }
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;

        $.post(admin_url + 'production_report/getRecommendedListByParentrecommended', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_id').selectpicker('refresh');
        });
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;
        $.post(admin_url + 'production_report/getRecommendedListProcess', data, function(data) {
            data = JSON.parse(data);
            if (data.mot != '') {
                $('#staff_bod').val(data.mot);
            }
            if (data.hai != '') {
                $('#staff_assigned').val(data.hai);
            }
            if (data.ba != '') {
                $('#head_of_department_id').val(data.ba);
            }
            if (data.bon != '') {
                $('#auditor_id').val(data.bon);
            }
            if (data.nam != '') {
                $('#staff').val(data.nam);
            }
            if (data.sau != '') {
                $('#manager_id').val(data.sau);
            }
            if (data.bay != '') {
                $('#monitor_id').val(data.bay);
            }
            if (data.tam != '') {
                $('#staff_controller_completes').val(data.tam);
            }
            if (data.chin != '') {
                $('#staff_auditor_completes').val(data.chin);
            }
            $('#staff').selectpicker('refresh');
            $('#staff_bod').selectpicker('refresh');
            $('#staff_assigned').selectpicker('refresh');
            $('#head_of_department_id').selectpicker('refresh');
            $('#monitor_id').selectpicker('refresh');
            $('#manager_id').selectpicker('refresh');
            $('#auditor_id').selectpicker('refresh');
            $('#staff_controller_completes').selectpicker('refresh');
            $('#staff_auditor_completes').selectpicker('refresh');
        });
        // $.post(admin_url + 'internal_proposal/getSuggestByRecommended', data, function(data) {
        //     data = JSON.parse(data);
        //     $('#suggest_id').html(`<option></option>`);
        //     $.each(data, function(index, value) {
        //         $('#suggest_id').append(`<option value="${value.id}">${value.reference_no}</option>`);
        //     })
        //     $('#suggest_id').selectpicker('refresh');
        // });
    });

    $('#category_recommended_id').change(function() {
        var category_recommended_id = $(this).val();
        $('.suggest_id').removeClass('hide');
        $('.suggest_id_muti').addClass('hide');
        if (category_recommended_id == 41) {
            $('.suggest_id').addClass('hide');
            $('.suggest_id_muti').removeClass('hide');
        }
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['category_recommended_id'] = category_recommended_id;

        $.post(admin_url + 'internal_proposal/getSuggestByRecommendedSingle', data, function(data) {
            data = JSON.parse(data);
            if (category_recommended_id == 41) {
                $('#suggest_muti_id').html(`<option></option>`);
                $.each(data, function(index, value) {
                    $('#suggest_muti_id').append(`<option data-subtext="${value.staff_suggest_name}" value="${value.id}">${value.reference_no}</option>`);
                })
                $('#suggest_muti_id').selectpicker('refresh');
            } else {
                $('#suggest_id').html(`<option></option>`);
                $.each(data, function(index, value) {
                    $('#suggest_id').append(`<option data-subtext="${value.staff_suggest_name}" value="${value.id}">${value.reference_no}</option>`);
                })
                $('#suggest_id').selectpicker('refresh');
            }
        });
    });

    $('#suggest_id').change(function() {
        $(".table_category_kpi").html('');
        var suggest_id = $(this).val();
        var category_recommended_id = $("#category_recommended_id").val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['suggest_id'] = suggest_id;
        data['category_recommended_id'] = category_recommended_id;

        $.post(admin_url + 'internal_proposal/getSuggestCategoryKpi', data, function(data) {
            data = JSON.parse(data);
            $(".table_category_kpi").html(data.html);
            $('#money').val(tnhFormatMoney(data.total));
            if (category_recommended_id == 40 || category_recommended_id == 41){
                $('#money').attr('readonly', true);
            } else {
                $('#money').attr('readonly', false);
            }
        });
    });
    $('#suggest_muti_id').change(function() {
        $(".table_category_kpi").html('');
        var suggest_id = $(this).val();
        var category_recommended_id = $("#category_recommended_id").val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['suggest_id'] = suggest_id;
        data['category_recommended_id'] = category_recommended_id;

        $.post(admin_url + 'internal_proposal/getSuggestCategoryKpi_muiti', data, function(data) {
            data = JSON.parse(data);
            $(".table_category_kpi").html(data.html);
            $('#money').val(tnhFormatMoney(data.total));
            if (category_recommended_id == 40 || category_recommended_id == 41){
                $('#money').attr('readonly', true);
            } else {
                $('#money').attr('readonly', false);
            }
        });
    });
    $('#recommended_list_id').change(function() {
        var recommended_list_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_id;

        $.post(admin_url + 'production_report/getRecommendedListByParentrecommended', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_detail_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_detail_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_detail_id').selectpicker('refresh');
        });
    });
    var opt = {
        format: 'd/m/Y H:i:s',
        timepicker: true,
        scrollInput: false,
        lazyInit: true,
        minDate: 0,
    };
    $('#date').datetimepicker(opt);
    var opt = {
        format: 'd/m/Y H:i:s',
        timepicker: true,
        scrollInput: false,
        lazyInit: true,
    };
    $('#date_finish').datetimepicker(opt);
    $('#SearchQR_purchase').on('keydown', function(event) {
        // Giả sử bạn kiểm tra khi nhận được phím Enter (key code 13) từ máy quét
        var code = $(this).val();
        if (event.keyCode === 13) {
            // Ngăn chặn sự kiện submit của form
            event.preventDefault();
            $('#SearchQR_purchase').change();
            // Thực hiện xử lý khác nếu cần
        }
    });
    // function scanfrom(code) {

    // }
    $('#SearchQR_purchase').change(function() {
        var code = $(this).val();
        link = '';
        if (code) {
            var data = {
                'code': code
            };
            $.get(admin_url + 'internal_proposal/check_qr', data, function(data) {
                data = JSON.parse(data);
                if (data.result) {
                    alert_float('success', data.message)
                    if (data.id) {
                        var id_purchases = $('[name="id_purchases[]"]').val();
                        id_purchases.push(data.id);
                        $('[name="id_purchases[]"]').val(id_purchases).change();
                    }
                } else {
                    alert_float('danger', data.message)
                    Soundhau('<?= base_url('uploads/error.mp3') ?>');
                }

            })
        }
        $('#SearchQR_purchase').val('');
    })

    function changDataDecision(_this) {
        $.ajax({
                url: site.base_url + 'admin/internal_proposal/changDataDecision',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: "<?= $this->security->get_csrf_hash() ?>",
                    decision_bonus_discipline_id: $(_this).val()
                },
            })
            .done(function(data) {
                if (data.dtData != null) {
                    $("#money").val(tnhFormatMoney(data.dtData.grand_total));
                    tinymce.get('content').setContent(data.dtData.note);
                } else {
                    $("#money").val(tnhFormatMoney(0));
                    tinymce.get('content').setContent('');
                }
            })
            .fail(function() {
                console.log("error");
            });
    }

    function toggleInternalProposalExcelMode(is_on_load) {
        var recommended_list_id = $('#recommended_list_id').val();
        if (!recommended_list_id) {
            $('.purchase_default_area').removeClass('hide');
            $('.excel_proposal_area').addClass('hide');
            return;
        }
        $.get(admin_url + 'internal_proposal/get_recommended_list_excel_config', { id: recommended_list_id }, function(res) {
            res = JSON.parse(res);
            if (res && res.is_excel == 1) {
                $('.purchase_default_area').addClass('hide');
                $('.excel_proposal_area').removeClass('hide');
                
                var container = $('.excel_proposal_table_container');
                if (!is_on_load || container.html().trim() === '') {
                    try {
                        var headers = JSON.parse(res.excel);
                        if (Array.isArray(headers) && headers.length > 0) {
                            var tableHtml = '<div class="table-responsive" style="max-height: 400px; overflow: auto; width: 100%; max-width: 0; min-width: 100%;">';
                            tableHtml += '<table class="table table-bordered table-striped" style="margin-bottom: 0; white-space: nowrap; width: 100%;">';
                            tableHtml += '<thead><tr class="success">';
                            $.each(headers, function(i, header) {
                                tableHtml += '<th class="text-center bold" style="min-width: 150px; white-space: nowrap;">' + header + '</th>';
                            });
                            tableHtml += '</tr></thead>';
                            tableHtml += '<tbody><tr>';
                            $.each(headers, function(i, header) {
                                tableHtml += '<td>&nbsp;</td>';
                            });
                            tableHtml += '</tr></tbody>';
                            tableHtml += '</table>';
                            tableHtml += '</div>';
                            container.html(tableHtml);
                        } else {
                            container.html('');
                        }
                    } catch(e) {
                        container.html('');
                    }
                }
            } else {
                $('.purchase_default_area').removeClass('hide');
                $('.excel_proposal_area').addClass('hide');
            }
        });
    }

    $('#recommended_list_id').on('change', function() {
        toggleInternalProposalExcelMode(false);
    });
    
    $(function() {
        toggleInternalProposalExcelMode(true);
    });

    $(document).on('change', '#excel_proposal_file', function() {
        var fileInput = this;
        if (!fileInput.files.length) return;
        
        var recommended_list_id = $('#recommended_list_id').val();
        var formData = new FormData();
        formData.append('excel_proposal_file', fileInput.files[0]);
        formData.append('recommended_list_id', recommended_list_id);
        
        if (typeof(csrfData) !== 'undefined') {
            formData.append(csrfData['token_name'], csrfData['hash']);
        }
        
        var container = $('.excel_proposal_table_container');
        container.html('<div class="text-center ptop15"><i class="fa fa-spin fa-spinner fa-2x"></i> Đang tải dữ liệu...</div>');
        
        $.ajax({
            url: admin_url + 'internal_proposal/validate_excel_proposal',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(res) {
                if (res.success) {
                    alert_float('success', 'Tải dữ liệu tệp Excel thành công');
                    if (res.rows && res.rows.length > 0) {
                        var headers = Object.keys(res.rows[0]);
                        var tableHtml = '<div class="table-responsive" style="max-height: 400px; overflow: auto; width: 100%; max-width: 0; min-width: 100%;">';
                        tableHtml += '<table class="table table-bordered table-striped" style="margin-bottom: 0; white-space: nowrap; width: 100%;">';
                        tableHtml += '<thead><tr class="success">';
                        $.each(headers, function(i, header) {
                            tableHtml += '<th class="text-center bold" style="min-width: 150px; white-space: nowrap;">' + header + '</th>';
                        });
                        tableHtml += '</tr></thead>';
                        tableHtml += '<tbody>';
                        $.each(res.rows, function(idx, row) {
                            tableHtml += '<tr>';
                            $.each(headers, function(i, header) {
                                tableHtml += '<td>' + (row[header] !== undefined ? row[header] : '') + '</td>';
                            });
                            tableHtml += '</tr>';
                        });
                        tableHtml += '</tbody>';
                        tableHtml += '</table>';
                        tableHtml += '</div>';
                        container.html(tableHtml);
                    } else {
                        container.html('<div class="alert alert-warning text-center">Tệp Excel hợp lệ nhưng không có dòng dữ liệu</div>');
                    }
                } else {
                    alert_float('danger', res.message);
                    $(fileInput).val('');
                    toggleInternalProposalExcelMode(false);
                }
            },
            error: function() {
                alert_float('danger', 'Lỗi khi kiểm tra tệp Excel');
                $(fileInput).val('');
                toggleInternalProposalExcelMode(false);
            }
        });
    });
</script>