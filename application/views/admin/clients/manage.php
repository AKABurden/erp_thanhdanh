<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style type="text/css">
    .checkbox-templates {
        float: left;
        width: 18%;
        margin-top: 0 !important;
        margin-right: 10px;
        margin-left: 10px;
    }

    #wrapper {
        min-height: unset !important;
    }

    .tags-labels {
        white-space: initial !important;
    }

    .table-clients tbody tr td {
        white-space: inherit;
        min-width: 120px;
    }

    .table-clients tbody tr td:nth-child(1) {
        white-space: inherit;
        min-width: 50px;
    }

    .table-clients tbody tr td:nth-child(2) {
        white-space: inherit;
        min-width: 50px;
        text-align: center;
    }

    .table-clients tbody tr td:nth-child(3) {
        white-space: inherit;
        min-width: 50px;
        text-align: center;
    }

    .table-clients tbody tr td:nth-child(5) {
        white-space: inherit;
        min-width: 50px;
    }

    .table-clients tbody tr td:nth-child(10) {
        white-space: inherit;
        min-width: 200px;
    }

    .table-clients tbody tr td:nth-child(4) {
        white-space: inherit;
        min-width: 110px;
        text-align: center;
    }

    .table-clients tbody tr td:nth-child(6) {
        white-space: inherit;
        min-width: 110px;
    }

    .table-clients.fixedHeader-floating {
        top: 40px !important;
    }

    .img-small {
        height: 20px !important;
        width: 20px !important;
    }

    .font-11 {
        font-size: 11px !important;
    }
    .bootstrap-select{
        width: 200px !important;
    }
    .hide_btn_options .buttons-collection.btn-default-dt-options {
      display: block!important;
    }
</style>
<?php
$colum_view = [0, 1];
$table_data = array();
$_table_data = array(
    '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label for="mass_select_all"></label></div>',
    //        array(
    //            'name' => _l('the_number_sign'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-number')
    //        ),
    //        array(
    //            'name' => _l('cong_image_lead_profile'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-images')
    //        ),
    array(
        'name' => _l('cong_zcode'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-zcode')
    ),
    array(
        'name' => _l('cong_company_system_lead'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-company')
    ),
    array(
        'name' => _l('representative'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-representative')
    ),
    //        array(
    //            'name' => _l('contact_primary'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-primary-contact')
    //        ),
    //        array(
    //            'name' => _l('company_primary_email'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-primary-contact-email')
    //        ),
    array(
        'name' => _l('clients_list_phone'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-phone')
    ),
    array(
        'name' => _l('cong_lead_asigned_procedure_lead'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-customer_admin')
    ),
    //        array(
    //            'name' => _l('customer_active'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-active')
    //        ),
    array(
        'name' => _l('customer_groups'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-groups')
    ),
    array(
        'name' => _l('date_created'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-date-created')
    ),
    array(
        'name' => _l('client_vat_number'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-vat')
    ),
    array(
        'name' => _l('client_address'),
        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-address')
    ),
    //        array(
    //            'name' => _l('cong_note'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-note')
    //        ),
    //        array(
    //            'name' => _l('client_website'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-website')
    //        ),
    //        array(
    //            'name' => _l('cong_date_create_company'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-date_create_company')
    //        ),
    //        array(
    //            'name' => _l('clients_country'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-country')
    //        ),
    //        array(
    //            'name' => _l('cong_client_city'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-city')
    //        ),
    //        array(
    //            'name' => _l('cong_client_district'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-district')
    //        ),
    //        array(
    //            'name' => _l('cong_client_sources'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-sources')
    //        ),
    //        array(
    //            'name' => _l('cong_client_introduction'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-introduction')
    //        ),
    //        array(
    //            'name' => _l('cong_debt_limit'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-debt_limit')
    //        ),
    //        array(
    //            'name' => _l('cong_debt_limit_day'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-debt_limit_day')
    //        ),
    //        array(
    //            'name' => _l('cong_discount'),
    //            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-discount')
    //        ),
           array(
               'name' => _l('Phân loại khách hàng'),
               'th_attrs' => array('class' => 'toggleable', 'id' => 'th-status_client')
           )
    // [
    //  'name' => _l('table_set_prices'),
    //  'th_attrs' => ['class' => 'toggleable', 'id' => 'th-set_prices']
    // ],
    // [
    //  'name' => _l('cong_discount'),
    //  'th_attrs' => ['class' => 'toggleable', 'id' => 'th-set_discount']
    // ]
);
$custom_fields = get_custom_fields('customers', array('show_on_table' => 1));
$countTable = count($_table_data);

?>

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <!-- ==================================== -->
                <div class="dropdown pull-right">
                    <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        <?= lang('actions') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                        <?php if (is_admin()) { ?>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#quantrivien">
                                    <i class="lnr lnr-star-half"></i>
                                    <?php echo _l('Sửa quản trị viên'); ?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (has_permission('customers', '', 'create')) { ?>
                            <li>
                                <a href="<?= admin_url('clients/import_client') ?>">
                                    <i class="fal fa-upload"></i>
                                    <?php echo _l('Import excel'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <li>

                        </li>
                        <li>
                            <a href="#" data-toggle="tooltip" data-title="<?php echo _l('delete'); ?>" onclick="DeleteList_ch('.table-clients', 'clients/deleteList')">
                                <i class="fal fa-trash-alt"></i>
                                <?php echo _l('delete'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php if (has_permission('customers', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?php echo admin_url('clients/client'); ?>" class="btn btn-info test H_action_button">
                            <?php echo _l('create_add_new'); ?>
                        </a>
                    </div>
                <?php } ?>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right mright5" href="javascript:void(0)">Xuất Excel</a>
                <div class="pull-right mright5 H_border hide">
                    <a data-toggle="modal" data-target="#export_excel_clent" class="btn btn-info test H_action_button">
                        <i class="fal fa-download" style="display: contents;"></i>
                        <?php echo _l('Export excel'); ?>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <?php if (!empty($tamdong)) { ?>

            <div id="templates" class="collapse templates" style="background: #fff;">
                <?php if (!empty($hidden_colum->field)) {
                    $hidden_colum->field = json_decode($hidden_colum->field);
                } ?>
                <?php foreach ($_table_data as $key => $value) {
                    if ($key > 1) { ?>
                        <?php
                        $checked = '';
                        if (!empty($hidden_colum->field->{$value['th_attrs']['id']}) || (empty($hidden_colum->field) && $key <= 8)) {
                            $colum_view[] = $key;
                            $checked = 'checked';
                        }
                        ?>
                        <div class="checkbox checkbox-primary checkbox-templates">
                            <input type="checkbox" class="field_client" id="field['<?= $value['th_attrs']['id'] ?>']" name="field['<?= $value['th_attrs']['id'] ?>']" <?= $checked ?> value="<?= $value['th_attrs']['id'] ?>">
                            <label for="clients_list_company_show">
                                <?= $value['name'] ?>
                            </label>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php
                if (!empty($info_group)) {
                    if (!empty($hidden_colum)) {
                        $hidden_colum->group_detail = json_decode($hidden_colum->group_detail);
                    }

                    foreach ($info_group as $key => $value) {
                        $countTable++;
                        $info_group[$key]['count'] = ($countTable - 1);
                        $checked = '';
                        if (!empty($hidden_colum->group_detail->{$value['id']})) {
                            $colum_view[] = ($countTable - 1);
                            $checked = 'checked';
                        }
                ?>
                        <div class="checkbox checkbox-primary checkbox-templates">
                            <input type="checkbox" class="group_detail" id="<?= 'group_detail[' . $value['id'] . ']' ?>" name="<?= 'group_detail[' . $value['id'] . ']' ?>" value="<?= $value['id'] ?>" <?= $checked ?>>
                            <label for="<?= 'group_detail[' . $value['id'] . ']' ?>">
                                <?= $value['name'] ?>
                            </label>
                        </div>
                <?php }
                }
                ?>

                <?php
                if (!empty($custom_fields)) {
                    if (!empty($hidden_colum->field_customer)) {
                        $hidden_colum->field_customer = json_decode($hidden_colum->field_customer);
                    }
                    foreach ($custom_fields as $key => $value) {
                        $countTable++;
                        $checked = '';
                        $custom_fields[$key]['count'] = ($countTable - 1);
                        if (!empty($hidden_colum->field_customer->{$value['id']})) {
                            $colum_view[] = ($countTable - 1);
                            $checked = 'checked';
                        }
                ?>
                        <div class="checkbox checkbox-primary checkbox-templates">
                            <input type="checkbox" class="field_customer" id="<?= 'field_customer[' . $value['id'] . ']' ?>" name="<?= 'field_customer[' . $value['id'] . ']' ?>" value="<?= $value['id'] ?>" <?= $checked ?>>
                            <label for="<?= 'group_detail[' . $value['id'] . ']' ?>">
                                <?= $value['name'] ?>
                            </label>
                        </div>
                <?php }
                }
                ?>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    </div>

    <div class="modal fade bulk_actions" id="quantrivien" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo _l('Thêm nhanh quản trị viên'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php $staff_manage = !empty($staff) ? $staff : []; ?>
                    <div class="form-group" app-field-wrapper="staffid_id">
                        <label for="staffid_id" class="control-label">Nhân viên</label>
                        <select id="staffid_id" name="staffid_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                            <option></option>
                            <option value="remove" data-content="<span class='text-danger'><b>Xóa quản trị viên khách hàng</b></span>"></option>
                            <?php foreach ($staff_manage as $key => $value) { ?>
                                <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="renew" value="1">
                        <label>Thay thế nhân viên quản trị khách</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <a href="#" class="btn btn-info" onclick="add_admin(this); return false;"><?php echo _l('confirm'); ?></a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_filters _hidden_inputs hidden">
                    <?php
                    echo form_hidden('my_customers');
                    echo form_hidden('requires_registration_confirmation');
                    ?>
                </div>
                <div class="panel_s">
                    <div class="">
                        <?php if (has_permission('customers', '', 'view') || have_assigned_customers()) {
                            $where_summary = '';
                            if (!has_permission('customers', '', 'view')) {
                                $where_summary = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                            }
                        ?>
                            <div id="height_top">
                                <div id="infomation_client" class="collapse" aria-expanded="false">
                                    <div class="row mbot15">
                                        <div class="col-md-12">
                                            <h4 class="no-margin"><?php echo _l('customers_summary'); ?></h4>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                            <h3 class="bold"><?php echo total_rows(db_prefix() . 'clients', ($where_summary != '' ? substr($where_summary, 5) : '')); ?></h3>
                                            <span class="text-dark"><?php echo _l('customers_summary_total'); ?></span>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                            <h3 class="bold">
                                                <?php echo total_rows(db_prefix() . 'clients', 'active=1' . $where_summary); ?>
                                            </h3>
                                            <span class="text-success">
                                                <?php echo _l('active_customers'); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                            <h3 class="bold">
                                                <?php echo total_rows(db_prefix() . 'clients', 'active=0' . $where_summary); ?>
                                            </h3>
                                            <span class="text-danger">
                                                <?php echo _l('inactive_active_customers'); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                            <h3 class="bold">
                                                <?php echo total_rows(db_prefix() . 'contacts', 'active=1' . $where_summary); ?>
                                            </h3>
                                            <span class="text-info">
                                                <?php echo _l('customers_summary_active'); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2  col-xs-6 border-right">
                                            <h3 class="bold">
                                                <?php echo total_rows(db_prefix() . 'contacts', 'active=0' . $where_summary); ?>
                                            </h3>
                                            <span class="text-danger">
                                                <?php echo _l('customers_summary_inactive'); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2 col-xs-6">
                                            <h3 class="bold">
                                                <?php echo total_rows(db_prefix() . 'contacts', 'last_login LIKE "' . date('Y-m-d') . '%"' . $where_summary); ?>
                                            </h3>
                                            <span class="text-muted">
                                                <?php
                                                $contactsTemplate = '';
                                                if (!empty($contacts_logged_in_today) && count($contacts_logged_in_today) > 0) {
                                                    foreach ($contacts_logged_in_today as $contact) {
                                                        $url = admin_url('clients/client/' . $contact['userid'] . '?contactid=' . $contact['id']);
                                                        $fullName = $contact['firstname'] . ' ' . $contact['lastname'];
                                                        $dateLoggedIn = _dt($contact['last_login']);
                                                        $html = "<a href='$url' target='_blank'>$fullName</a><br /><small>$dateLoggedIn</small><br />";
                                                        $contactsTemplate .= htmlspecialchars('<p class="mbot5">' . $html . '</p>');
                                                    }
                                                ?>
                                                <?php } ?>
                                                <span <?php if ($contactsTemplate != '') { ?> class="pointer text-has-action" data-toggle="popover" data-title="<?php echo _l('customers_summary_logged_in_today'); ?>" data-html="true" data-content="<?php echo $contactsTemplate; ?>" data-placement="bottom" <?php } ?>>
                                                    <?php echo _l('customers_summary_logged_in_today'); ?></span>
                                            </span>
                                        </div>
                                    </div>

                                    <hr class="hr-panel-heading" />
                                </div>
                            <?php } ?>
                            <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-clients">
                                <?php echo _l('bulk_actions'); ?>
                            </a>
                            <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">
                                                <?php echo _l('bulk_actions'); ?>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php if (has_permission('customers', '', 'delete')) { ?>
                                                <div class="checkbox checkbox-danger">
                                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                                </div>
                                                <hr class="mass_delete_separator" />
                                            <?php } ?>
                                            <div id="bulk_change">
                                                <?php echo render_select('move_to_groups_customers_bulk[]', $groups, array('id', 'name'), 'customer_groups', '', array('multiple' => true), array(), '', '', false); ?>
                                                <p class="text-danger">
                                                    <?php echo _l('bulk_action_customers_groups_warning'); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                            <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;">
                                                <?php echo _l('confirm'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            <div id="search">
                                <!--                                <div class="col-md-3 row">-->
                                <!--                                    --><?php //echo render_select('groups_search', $groups, array('id', 'name'), 'customer_groups', '', array('data-width' => '100%', 'data-none-selected-text' => _l('customer_groups')), array(), 'no-mbot'); 
                                                                            ?>
                                <!--                                </div>-->
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group no-mbot" app-field-wrapper="customer_admins_search" style="display: flex; flex-direction: column;">
                                            <label for="customer_admins_search" class="control-label"><?= _l('staff_admin') ?></label>
                                            <select id="customer_admins_search" name="customer_admins_search" class="form-control selectpicker" data-none-selected-text="<?= _l('staff_admin') ?>" data-live-search="true" tabindex="-98">
                                                <option></option>
                                                <option value="-1" data-content="<span class='label label-danger font-11'><i class='fa fa-check-circle-o'></i> CHƯA CÓ CHỈ ĐỊNH NHÂN VIÊN</span>"></option>
                                                <?php
                                                if (!empty($staff)) :
                                                    foreach ($staff as $key => $value) : ?>
                                                        <?php $imageAvatar = staff_profile_image_url($value['staffid']) ?>
                                                        <option value="<?= $value['staffid'] ?>" data-content="<img class='img staff-profile-image-small img-small' src='<?= $imageAvatar ?>'>  <?= $value['firstname'] ?> <?= $value['lastname'] ?>"></option>
                                                <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                        <!--                                    --><?php //echo render_select('customer_admins_search', $manag_staff, array('staffid', array('firstname', 'lastname')), 'staff_admin', '', array('data-width' => '100%', 'data-none-selected-text' => _l('staff_admin')), array(), 'no-mbot'); 
                                                                                    ?>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="from-group">
                                            <?= lang('Ngày tạo bắt đầu', 'date_start_search') ?>
                                            <input type="text" name="date_start_search" id="date_start_search" placeholder="<?= lang('Ngày tạo bắt đầu') ?>" class="form-control date_start_search datepicker" autocomplete="off" value="" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="from-group">
                                            <?= lang('Ngày tạo kết thúc', 'end_start_search') ?>
                                            <input type="text" name="end_start_search" id="end_start_search" placeholder="<?= lang('Ngày tạo kết thúc') ?>" class="form-control end_start_search datepicker" autocomplete="off" value="" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="checkbox mtop30">
                                            <input type="checkbox" checked id="exclude_inactive" name="exclude_inactive">
                                            <label for="exclude_inactive"><?php echo _l('exclude_inactive'); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix mtop20"></div>
                            </div>
                            </div>

                            <div class="alert_typeTbable"></div>
                            <div class="col-md-12" style="margin-left: -15px">
                                <!--                            <input type="hidden" id="filterStatus" value=""/>-->
                                <input type="hidden" name="groups_search" id="groups_search" value="" />
                                <div data-toggle="btn" class="mbot15 mtop20">
                                    <div class="horizontal-scrollable-tabs">
                                        <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                        <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                        <div class="horizontal-tabs">
                                            <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                                <li>
                                                    <a style="padding: 3px;">
                                                        <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="all">
                                                            <?= _l('leads_all') ?>
                                                            <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                            <span class="check-show" style="float: left; margin-right: 5px;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="9pt" height="9pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                                                    <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="white" stroke="none">
                                                                        <path d="M2370 5114 c-19 -2 -78 -9 -130 -14 -330 -36 -695 -160 -990 -336 -375 -224 -680 -529 -904 -904 -173 -290 -294 -643 -336 -980 -13 -109 -13 -531 0 -640 96 -778 555 -1476 1240 -1884 670 -400 1508 -465 2245 -174 112 44 352 166 401 203 50 38 78 105 71 172 -11 101 -79 169 -180 180 -50 5 -58 2 -187 -65 -338 -176 -627 -256 -975 -269 -950 -37 -1827 576 -2124 1483 -96 294 -130 585 -101 875 33 330 156 694 320 943 291 445 681 750 1156 905 625 204 1283 121 1841 -233 51 -32 109 -62 130 -67 156 -36 291 130 224 276 -23 50 -43 68 -161 142 -315 199 -668 324 -1050 373 -88 12 -429 21 -490 14z" />
                                                                        <path d="M4843 4698 c-17 -6 -45 -22 -62 -37 -16 -14 -501 -613 -1077 -1331 -576 -718 -1056 -1313 -1068 -1322 -34 -28 -81 -33 -120 -12 -19 11 -269 241 -554 512 -286 271 -530 501 -543 511 -79 60 -185 52 -257 -19 -72 -73 -81 -182 -19 -259 34 -43 1023 -982 1097 -1042 205 -166 490 -155 676 26 66 64 2148 2650 2171 2697 79 158 -73 331 -244 276z" />
                                                                        <path d="M4760 3334 c-106 -46 -140 -141 -105 -293 46 -195 65 -462 46 -661 -33 -346 -138 -659 -321 -960 -76 -126 -85 -171 -47 -253 19 -42 35 -61 68 -80 58 -34 110 -42 163 -27 74 20 116 71 232 280 296 536 387 1186 249 1786 -20 87 -32 118 -57 148 -57 69 -151 94 -228 60z" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </a>
                                                </li>
                                                <?php if (!empty($groups)) { ?>
                                                    <?php foreach ($groups as $key => $value) { ?>
                                                        <li role="presentation">
                                                            <a style="padding: 3px;">
                                                                <button style="font-size: 11px; color: #fff; background: <?= $value['color'] ?>" type="button" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                                                    <?= $value['name'] ?>
                                                                    <span class="badge menu-badge bg-warning" id="group_<?= $value['id'] ?>" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                                    <span class="check-show" style="float: left; margin-right: 5px;">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="9pt" height="9pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                                                            <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="white" stroke="none">
                                                                                <path d="M2370 5114 c-19 -2 -78 -9 -130 -14 -330 -36 -695 -160 -990 -336 -375 -224 -680 -529 -904 -904 -173 -290 -294 -643 -336 -980 -13 -109 -13 -531 0 -640 96 -778 555 -1476 1240 -1884 670 -400 1508 -465 2245 -174 112 44 352 166 401 203 50 38 78 105 71 172 -11 101 -79 169 -180 180 -50 5 -58 2 -187 -65 -338 -176 -627 -256 -975 -269 -950 -37 -1827 576 -2124 1483 -96 294 -130 585 -101 875 33 330 156 694 320 943 291 445 681 750 1156 905 625 204 1283 121 1841 -233 51 -32 109 -62 130 -67 156 -36 291 130 224 276 -23 50 -43 68 -161 142 -315 199 -668 324 -1050 373 -88 12 -429 21 -490 14z" />
                                                                                <path d="M4843 4698 c-17 -6 -45 -22 -62 -37 -16 -14 -501 -613 -1077 -1331 -576 -718 -1056 -1313 -1068 -1322 -34 -28 -81 -33 -120 -12 -19 11 -269 241 -554 512 -286 271 -530 501 -543 511 -79 60 -185 52 -257 -19 -72 -73 -81 -182 -19 -259 34 -43 1023 -982 1097 -1042 205 -166 490 -155 676 26 66 64 2148 2650 2171 2697 79 158 -73 331 -244 276z" />
                                                                                <path d="M4760 3334 c-106 -46 -140 -141 -105 -293 46 -195 65 -462 46 -661 -33 -346 -138 -659 -321 -960 -76 -126 -85 -171 -47 -253 19 -42 35 -61 68 -80 58 -34 110 -42 163 -27 74 20 116 71 232 280 296 536 387 1186 249 1786 -20 87 -32 118 -57 148 -57 69 -151 94 -228 60z" />
                                                                            </g>
                                                                        </svg>
                                                                    </span>
                                                                </button>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <li>
                                                    <a style="padding: 3px;">
                                                        <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-danger btn-search" data-value="not_all">
                                                            <?= _l('Chưa phân nhóm') ?>
                                                            <span class="badge menu-badge bg-warning" id="not_all" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                            <span class="check-show" style="float: left; margin-right: 5px;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="9pt" height="9pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                                                    <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="white" stroke="none">
                                                                        <path d="M2370 5114 c-19 -2 -78 -9 -130 -14 -330 -36 -695 -160 -990 -336 -375 -224 -680 -529 -904 -904 -173 -290 -294 -643 -336 -980 -13 -109 -13 -531 0 -640 96 -778 555 -1476 1240 -1884 670 -400 1508 -465 2245 -174 112 44 352 166 401 203 50 38 78 105 71 172 -11 101 -79 169 -180 180 -50 5 -58 2 -187 -65 -338 -176 -627 -256 -975 -269 -950 -37 -1827 576 -2124 1483 -96 294 -130 585 -101 875 33 330 156 694 320 943 291 445 681 750 1156 905 625 204 1283 121 1841 -233 51 -32 109 -62 130 -67 156 -36 291 130 224 276 -23 50 -43 68 -161 142 -315 199 -668 324 -1050 373 -88 12 -429 21 -490 14z" />
                                                                        <path d="M4843 4698 c-17 -6 -45 -22 -62 -37 -16 -14 -501 -613 -1077 -1331 -576 -718 -1056 -1313 -1068 -1322 -34 -28 -81 -33 -120 -12 -19 11 -269 241 -554 512 -286 271 -530 501 -543 511 -79 60 -185 52 -257 -19 -72 -73 -81 -182 -19 -259 34 -43 1023 -982 1097 -1042 205 -166 490 -155 676 26 66 64 2148 2650 2171 2697 79 158 -73 331 -244 276z" />
                                                                        <path d="M4760 3334 c-106 -46 -140 -141 -105 -293 46 -195 65 -462 46 -661 -33 -346 -138 -659 -321 -960 -76 -126 -85 -171 -47 -253 19 -42 35 -61 68 -80 58 -34 110 -42 163 -27 74 20 116 71 232 280 296 536 387 1186 249 1786 -20 87 -32 118 -57 148 -57 69 -151 94 -228 60z" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </a>
                                                </li>
                                            </ul>
                                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--                        <input type="hidden" name="filterStatus">-->
                            <div id="H_height" class="hide_btn_options">

                                <?php
                                foreach ($_table_data as $_t) {
                                    array_push($table_data, $_t);
                                }
                                if (!empty($info_group)) {
                                    foreach ($info_group as $key => $value) {
                                        array_push($table_data, [
                                            'name' => $value['name'],
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-' . $value['id']]
                                        ]);
                                    }
                                }

                                foreach ($custom_fields as $field) {
                                    array_push($table_data, $field['name']);
                                }

                                //bổ xung cột cập nhập bảng giá, chiết khấu
                                //                            array_push($table_data, [
                                //                                'name' => _l('table_set_prices'),
                                //                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-set_prices']
                                //                            ]);
                                //                            array_push($table_data, [
                                //                                'name' => _l('cong_discount'),
                                //                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-set_discount']
                                //                            ]);
                                //end
                                array_push($table_data, [
                                    'name' => _l('Chi nhánh'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'dt-branch']
                                ]);
                                $table_data = hooks()->apply_filters('customers_table_columns', $table_data);
                                render_datatable($table_data, 'clients table-bordered', []);
                                ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="init-modal-customer">

</div>
<?php init_tail(); ?>
<?php
include_once(APPPATH . 'views/admin/export_excel/export_client.php');
?>
<style type="text/css">
    .table-clients th,
    .table-clients td {
        white-space: nowrap;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/fixedHeader.dataTables.min.css') ?>">
<script type="text/javascript" src="<?= base_url('assets/js/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    $('.btn-search').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-value');
        console.log(value)
        $('input[name="groups_search"]').val(value);
        $('input[name="groups_search"]').change();
    });
    var tAPI;
    var CustomersServerParams = {
        'filterStatus': '[name="filterStatus"]',
        'groups_search': '[name="groups_search"]',
        'customer_admins_search': '[name="customer_admins_search"]',
        'date_start_search': '[name="date_start_search"]',
        'end_start_search': '[name="end_start_search"]',
    };


    $.each($('._hidden_inputs._filters input'), function() {
        CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
    tAPI = initDataTable('.table-clients', admin_url + 'clients/table', [0, 10, 11], [0, 10, 11], CustomersServerParams, [1, 'desc']);

    var arrayColum = <?= !empty($colum_view) ? json_encode($colum_view) : '[]' ?>;
    var colum = <?= $countTable - 1 ?>;
    $(function() {
        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.draw('page');
        });

        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
    });

    function DeleteList_ch(ThisTable, href) {
        if (confirm(app.lang.confirm_action_prompt)) {
            if (confirm(app.lang.comfim_delete_all_list)) {
                $('.alert_typeTbable').html('');
                var Table = $(ThisTable);
                var MassSelect = Table.find('tbody').find('td:nth-child(1)').find('input[type="checkbox"]:checked');
                var ListID = [];
                $.each(MassSelect, function(i, v) {
                    ListID.push($(v).val());
                })
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['listData'] = ListID;
                $.post(admin_url + href, data, function(data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        tAPI.draw('page');
                    }
                    if (data.ktConnect) {
                        $.each(data.ktConnect, function(i, v) {
                            var StringTab = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + v.code;
                            $.each(v.data, function(ii, vv) {
                                StringTab += '<p>' + vv.message + ' : ' + vv.data + '</p>';

                            })
                            StringTab += '</div>';
                            $('.alert_typeTbable').append(StringTab);

                        })
                    }
                    alert_float(data.alert_type, data.message);
                })
            }
        }
    }

    function customers_bulk_action(event) {
        var r = confirm(app.lang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if (mass_delete == false || typeof(mass_delete) == 'undefined') {
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'clients/bulk_action', data).done(function() {
                    window.location.reload();
                });
            }, 50);
        }
    }

    $("body").on("click", "._deleteRow", function(e) {
        if (confirm(app.lang.confirm_action_prompt)) {
            var table = $(this).parents('table.dataTable');
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post($(this).attr('href'), data, function(result) {
                result = JSON.parse(result);
                if (result.success) {
                    tAPI.draw('page');
                }
                alert_float(result.alert_type, result.message);
            })
        }
        return false;
    })

    $('.table-clients').on('draw.dt', function() {
        var total_tr = $('.table-clients').find('tbody').find('tr');
        $.each(total_tr, function(i, v) {
            var id_set_price = $('#set_price_' + i).attr('data-idprice');
            $('#set_price_' + i).select2({
                'allowClear': true
            });
            $('#set_price_' + i).select2('val', id_set_price);

            var id_discount = $('#set_discount_' + i).attr('data-iddiscount');
            $('#set_discount_' + i).select2({
                'allowClear': true
            });
            $('#set_discount_' + i).select2('val', id_discount);
        });
    });

    $(document).on('change', '.set_price', function(e) {
        var id_customer = $(this).attr('data-customer');
        var id_set_price = $(this).val();

        var data = {};
        if (!id_set_price) {
            id_set_price = 0;
        }
        data['id_customer'] = id_customer;
        data['id_set_price'] = id_set_price;
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'set_prices/set_price_to_customer', data).done(function(response) {});
    });

    $(document).on('change', '.set_discount', function(e) {
        var id_customer = $(this).attr('data-customer');
        var id_discount = $(this).val();

        var data = {};
        if (!id_discount) {
            id_discount = 0;
        }
        data['id_customer'] = id_customer;
        data['id_discount'] = id_discount;
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'discount/set_discount_to_customer', data).done(function(response) {});
    });

    function status_mark_as(status, userid) {
        $.ajax({
            type: "get",
            async: false,
            url: '<?= admin_url('clients/upstatus_mark_as') ?>' + '/' + status + '/' + userid,
            dataType: "json",
            success: function(response) {
                tAPI.draw('page');
                alert_float('success', response.message);
            }
        });
    }
    $(document).on('click', '._delete', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                $('.table-shipping_client').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });


    function add_admin(event) {
        var r = confirm(app.lang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var ids = [];
            var data = {};
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            if (empty(ids)) {
                alert("<?= _l('Bạn chưa check chọn khách hàng cần thêm quản trị viên') ?>");
                return;
            }
            if ($('#staffid_id').val() == '') {
                alert("<?= _l('Bạn chưa chọn nhân viên') ?>");
                return;
            }
            data.ids = ids;
            data.staffid_id = $('#staffid_id').val();
            data[csrfData['token_name']] = csrfData['hash'];
            if ($('#renew:checked').val()) {
                data.renew = true;
            }

            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'clients/add_admin', data).done(function(response) {
                    response = JSON.parse(response);
                    alert_float(response.success, response.message);
                    $('#quantrivien').modal('hide');
                    tAPI.draw('page');
                    $(event).removeClass('disabled');
                    $('#staffid_id').val('').selectpicker("refresh");

                });
            }, 50);
        }
    }
    $('body').on('hidden.bs.modal', '#quantrivien', function() {
        $('#staffid_id').val('').selectpicker("refresh");
    });

    $('.table-clients').on('draw.dt', function() {
        init_selectpicker();
        var itemsTable = $(this).DataTable();
        get_total_limit_client();
        // $('#tb-clients tbody tr td:nth-child(1)').click();
    });


    function get_total_limit_client() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
        };
        $.each(CustomersServerParams, function(index, value) {
            dataString[index] = $(value).val();
        })
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>clients/count_all_client/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('#all_status').html(data.sum_all);
                $('#not_all').html(data.not_all);
                $.each(data.groups, (index, value) => {
                    if (value.sum == 0) {
                        value.sum = "";
                    }
                    $('#group_' + value.id).html(value.sum);
                });
            }
        });
    }
    $(document).on('change', '[id="branch[]"]', function (e) {
        var client_id = $(this).attr('data-client');
        var branch_id = $(this).val();
        var athis = $(this);
        var data = {};
        if (!branch_id) {
            branch_id = 0;
        }
        data['client_id'] = client_id;
        data['branch_id'] = branch_id;
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'clients/add_branch_client', data).done(function (response) {
            response = JSON.parse(response);
            alert_float(response.alert_type, response.message);
        });
    });


    function exportExcel() {
        var dataString = {};
        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            ['export_excel']: 1
        };

        $.each(CustomersServerParams, function(index, value) {
            dataString[index] = $(value).val();
        });


        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/clients/export_excel_client',
            data: dataString,
            // data: {
            //     csrf_token_name: hash,
            //     export_excel: 1,
            // },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>
</body>

</html>