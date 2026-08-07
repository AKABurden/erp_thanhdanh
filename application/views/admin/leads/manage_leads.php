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
    .table-leads th, .table-leads td {
        white-space: nowrap;
    }
    .tags-labels{
        white-space: initial!important;
    }
    .table-leads thead tr th{
        white-space: inherit;
        text-align: center;
    }
    .table-leads tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 120px;
        text-align: center;
    }
    .table-leads tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 200px;
    }
    .table-leads tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 110px;
    }
    .table-leads tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-leads tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
    }
    .table-leads tbody tr td:nth-child(11){
        white-space: inherit;
        min-width: 100px;
    }
    .table-leads tbody tr td:nth-child(12){
        white-space: inherit;
        min-width: 80px;
        text-align: center;
    }
    .table-leads tbody tr td:nth-child(13){
        white-space: inherit;
        min-width: 80px;
    }
    .table-leads tbody tr td:nth-child(15){
        white-space: inherit;
        min-width: 80px;
        text-align: center;
    }
    .table-leads tbody tr td:nth-child(16){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
</style>
<?php 
     $table_data = array();
                                        $_table_data = array(
                                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                            array(
                                                'name' => _l('the_number_sign'),
                                                'th_attrs' => array('class' => 'toggleable', 'id' => 'th-number')
                                            ),
                                            array(
                                                'name' => _l('cong_image_lead_profile'),
                                                'th_attrs' => array('class' => 'toggleable', 'id' => 'th-images')
                                            ),
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
                                            array(
                                                'name' => _l('cong_date_create_company'),
                                                'th_attrs' => array('class' => 'toggleable', 'id' => 'th-cong_date_create_company')
                                            ),
                                            array(
                                                'name' => _l('cong_vat'),
                                                'th_attrs' => array('class' => 'toggleable', 'id' => 'th-cong_vat')
                                            )

                                        );
                                        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                            $_table_data[] = array(
                                                'name' => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                                'th_attrs' => array('id' => 'th-consent', 'class' => 'not-export')
                                            );
                                        }
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_email'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-email')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_phonenumber'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-phone')
                                        );

                                        $_table_data[] = array(
                                            'name' => _l('tags'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-tags')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_assigned'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-assigned')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_status'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-status')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_source'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-source')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_last_contact'),
                                            'th_attrs' => array('class' => 'toggleable', 'id' => 'th-last-contact')
                                        );
                                        $_table_data[] = array(
                                            'name' => _l('leads_dt_datecreated'),
                                            'th_attrs' => array('class' => 'date-created toggleable', 'id' => 'th-date-created')
                                        );
                                        foreach ($_table_data as $_t) {
                                            array_push($table_data, $_t);
                                        }
                                        $custom_fields = get_custom_fields('leads', array('show_on_table' => 1));
                                        foreach ($custom_fields as $field) {
                                            array_push($table_data, $field['name']);
                                        }
?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
           <!--  <div class="btn-group pull-right btn-with-tooltip-group mright5" data-toggle="tooltip" data-title="<?php echo _l('setting_colum'); ?>">
                    <button class="btn btn-info" data-toggle="collapse" data-target="#templates">
                        <i class="fa fa-cog" aria-hidden="true"></i>
                    </button>
            </div> -->
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a href="#" class="btn btn-danger pull-right hidden-xs" data-toggle="tooltip" data-title="<?php echo _l('delete'); ?>" onclick="DeleteList('#table-leads', 'leads/deleteList')" >
                <?php echo _l('delete'); ?>
            </a>
            <a class="btn btn-info mright5 test pull-right H_action_button" data-toggle="modal" data-target="#export_excel_lead">
                <?php echo _l('Export excel'); ?>
            </a>
            <?php if (is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1') { ?>
                <a href="<?=admin_url('import_excel/import_leads')?>" class="btn btn-info pull-right H_action_button hidden-xs">
                    <?php echo _l('Import excel'); ?>
                </a>
            <?php } ?>
            <a class="btn btn-info mright5 test pull-right H_action_button" data-toggle="collapse" data-target="#search">
                <?php echo _l('search'); ?>
            </a>
            <a href="<?php echo admin_url('leads/switch_kanban/' . $switch_kanban); ?>" class="btn btn-info mleft10 hidden-xs pull-right H_action_button">
                <?php if ($switch_kanban == 1) {
                    echo _l('leads_switch_to_kanban');
                } else {
                    echo _l('switch_to_list_view');
                }; ?>
            </a>
            <a href="#" class="btn btn-default btn-with-tooltip pull-right" data-toggle="tooltip" data-title="<?php echo _l('leads_summary'); ?>" data-placement="bottom" onclick="slideToggle('.leads-overview'); return false;">
                <i class="fa fa-bar-chart"></i>
            </a>
            <div class="line-sp"></div>
            <a href="#" onclick="init_lead(); return false;" class="btn mright5 btn-info pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('create_add_new'); ?>
            </a>
            <div class="clearfix"></div>
            <div class="row hide leads-overview">
                <hr class="hr-panel-heading"/>
                <div class="col-md-12">
                    <h4 class="no-margin"><?php echo _l('leads_summary'); ?></h4>
                </div>
                <?php
                foreach ($summary as $status) { ?>
                    <div class="col-md-2 col-xs-6 border-right">
                        <h3 class="bold">
                            <?php
                            if (isset($status['percent'])) {
                                echo '<span data-toggle="tooltip" data-title="' . $status['total'] . '">' . $status['percent'] . '%</span>';
                            } else {
                                // Is regular status
                                echo $status['total'];
                            }
                            ?>
                        </h3>
                        <span style="color:<?php echo $status['color']; ?>" class="<?php echo isset($status['junk']) || isset($status['lost']) ? 'text-danger' : ''; ?>">
                            <?php echo $status['name']; ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="templates" class="collapse templates" style="background: #fff;">
            <?php if(!empty($hidden_colum->field))
            {
                $hidden_colum->field = json_decode($hidden_colum->field);
            }?>
            <?php foreach($_table_data as $key => $value){
                if($key > 1){?>
                    <?php
                        $checked = '';
                        if(!empty($hidden_colum->field->{$value['th_attrs']['id']}) || ( empty($hidden_colum->field) && $key <= 8 ))
                        {
                            $colum_view[]= $key;
                            $checked = 'checked';
                        }
                    ?>
                    <div class="checkbox checkbox-primary checkbox-templates">
                        <input type="checkbox" class="field_client" id="field['<?=$value['th_attrs']['id']?>']" name="field['<?=$value['th_attrs']['id']?>']" <?=$checked?>  value="<?=$value['th_attrs']['id']?>">
                        <label for="clients_list_company_show">
                            <?=$value['name']?>
                        </label>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php
                if(!empty($info_group))
                {
                    if(!empty($hidden_colum))
                    {
                        $hidden_colum->group_detail = json_decode($hidden_colum->group_detail);
                    }
                    foreach($info_group as $key => $value)
                    {
                        $countTable++;
                        $info_group[$key]['count'] = ($countTable - 1);
                        $checked = '';
                        if( !empty($hidden_colum->group_detail->{$value['id']}) )
                        {
                            $colum_view[]= ($countTable - 1);
                            $checked = 'checked';
                        }
                        ?>
                        <div class="checkbox checkbox-primary checkbox-templates">
                            <input type="checkbox" class="group_detail" id="<?='group_detail['.$value['id'].']'?>" name="<?='group_detail['.$value['id'].']'?>"  value="<?=$value['id']?>" <?= $checked?>>
                            <label for="<?='group_detail['.$value['id'].']'?>">
                                <?= $value['name'] ?>
                            </label>
                        </div>
                <?php }
                }
            ?>

            <?php
                if(!empty($custom_fields))
                {
                    if(!empty($hidden_colum->field_customer))
                    {
                        $hidden_colum->field_customer = json_decode($hidden_colum->field_customer);
                    }
                    foreach($custom_fields as $key => $value)
                    {
                        $countTable++;
                        $checked = '';
                        $custom_fields[$key]['count'] = ($countTable-1);
                        if( !empty($hidden_colum->field_customer->{$value['id']}) )
                        {
                            $colum_view[]= ($countTable - 1);
                            $checked = 'checked';
                        }
                        ?>
                        <div class="checkbox checkbox-primary checkbox-templates">
                            <input type="checkbox" class="field_customer" id="<?='field_customer['.$value['id'].']'?>" name="<?='field_customer['.$value['id'].']'?>"  value="<?=$value['id']?>" <?= $checked ?>>
                            <label for="<?='group_detail['.$value['id'].']'?>">
                                <?= $value['name'] ?>
                            </label>
                        </div>
                <?php }
                }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <?php
                            if ($this->session->has_userdata('leads_kanban_view') && $this->session->userdata('leads_kanban_view') == 'true') { ?>
                                <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                    <div class="kanban-leads-sort">
                                        <span class="bold"><?php echo _l('leads_sort_by'); ?>: </span>
                                        <a href="#" onclick="leads_kanban_sort('dateadded'); return false"
                                           class="dateadded">
                                            <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?>
                                            <?php echo _l('leads_sort_by_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('leadorder');return false;"
                                           class="leadorder">
                                            <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?>
                                            <?php echo _l('leads_sort_by_kanban_order'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('lastcontact');return false;"
                                           class="lastcontact">
                                            <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?>
                                            <?php echo _l('leads_sort_by_lastcontact'); ?>
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="container-fluid leads-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="leads-table">
                                    <div id="search" class="collapse">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="bold"><?php echo _l('filter_by'); ?></p>
                                                </div>
                                                <?php if (has_permission('leads', '', 'view')) { ?>
                                                    <div class="col-md-3 leads-filter-column">
                                                        <?php echo render_select('view_assigned', $staff, array('staffid', array('firstname', 'lastname')), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')), array(), 'no-mbot'); ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-md-3 leads-filter-column">
                                                    <div id="leads-filter-status">
                                                        <?php $selected = $this->input->get('status');?>
                                                        <select id="view_status[]" name="view_status[]" class="selectpicker" data-width="100%" data-none-selected-text="<?=_l('leads_all')?>" multiple="true" data-actions-box="1" data-live-search="true" tabindex="-98">
                                                            <option value="0" selected=""><?=_l('cong_client_cover_delete')?></option>
                                                            <?php  foreach ($statuses as $key => $status) {
                                                                if ($status['isdefault'] == 0) {
                                                                    echo "<option value='".$status['id']."' ".(!empty($selected) ? ($selected == $status['id'] ? 'selected' : '') : 'selected' ).">".$status['name']."</option>";
                                                                }
                                                                else
                                                                {
                                                                    echo "<option value='".$status['id']."' data-subtext='"._l('leads_converted_to_client')."' ".(!empty($selected) ? ($selected == $status['id'] ? 'selected' : '') : '' ).">".$status['name']."</option>";
                                                                }

                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 leads-filter-column">
                                                    <?php
                                                        echo render_select('view_source', $sources, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('leads_source')), array(), 'no-mbot');
                                                    ?>
                                                </div>
                                                <div class="col-md-3 leads-filter-column">
                                                    <div class="select-placeholder">
                                                        <select name="custom_view" title="<?php echo _l('additional_filters'); ?>" id="custom_view" class="selectpicker" data-width="100%">
                                                            <option value=""></option>
                                                            <option value="lost"><?php echo _l('lead_lost'); ?></option>
                                                            <option value="junk"><?php echo _l('lead_junk'); ?></option>
                                                            <option value="public"><?php echo _l('lead_public'); ?></option>
                                                            <option value="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></option>
                                                            <option value="created_today"><?php echo _l('created_today'); ?></option>
                                                            <?php if (has_permission('leads', '', 'edit')) { ?>
                                                                <option value="not_assigned"><?php echo _l('leads_not_assigned'); ?></option>
                                                            <?php } ?>
                                                            <?php if (isset($consent_purposes)) { ?>
                                                                <optgroup label="<?php echo _l('gdpr_consent'); ?>">
                                                                    <?php foreach ($consent_purposes as $purpose) { ?>
                                                                        <option value="consent_<?php echo $purpose['id']; ?>">
                                                                            <?php echo $purpose['name']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </optgroup>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading"/>
                                    </div>

                                    <?php echo form_open()?>
                                    <div class="col-md-12">
                                        <div class="alert_typeTbable"></div>
                                        <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1"
                                             role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">
                                                            <?php echo _l('bulk_actions'); ?>
                                                        </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php if (has_permission('leads', '', 'delete')) { ?>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" name="mass_delete" id="mass_delete">
                                                                <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                                            </div>
                                                            <hr class="mass_delete_separator"/>
                                                        <?php } ?>
                                                        <div id="bulk_change">
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-primary checkbox-inline">
                                                                    <input type="checkbox" name="leads_bulk_mark_lost" id="leads_bulk_mark_lost" value="1">
                                                                    <label for="leads_bulk_mark_lost">
                                                                        <?php echo _l('lead_mark_as_lost'); ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <?php
                                                                echo render_select('move_to_status_leads_bulk', $statuses, array('id', 'name'), 'ticket_single_change_status');
                                                            ?>
                                                            <?php
                                                                echo render_select('move_to_source_leads_bulk', $sources, array('id', 'name'), 'lead_source');
                                                                echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                                                if (has_permission('leads', '', 'edit')) {
                                                                    echo render_select('assign_to_leads_bulk', $staff, array('staffid', array('firstname', 'lastname')), 'leads_dt_assigned');
                                                                }
                                                            ?>
                                                            <div class="form-group">
                                                                <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                                <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group no-mbot">
                                                                <div class="radio radio-primary radio-inline">
                                                                    <input type="radio" name="leads_bulk_visibility" id="leads_bulk_public" value="public">
                                                                    <label for="leads_bulk_public">
                                                                        <?php echo _l('lead_public'); ?>
                                                                    </label>
                                                                </div>
                                                                <div class="radio radio-primary radio-inline">
                                                                    <input type="radio" name="leads_bulk_visibility" id="leads_bulk_private" value="private">
                                                                    <label for="leads_bulk_private">
                                                                        <?php echo _l('private'); ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                                            <?php echo _l('close'); ?>
                                                        </button>
                                                        <a href="#" class="btn btn-info" onclick="leads_bulk_action(this); return false;">
                                                            <?php echo _l('confirm'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        <?php

                                       
                                        $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                                        render_datatable($table_data, 'leads table-bordered',
                                            array('customizable-table'),
                                            array(
                                                'id' => 'table-leads',
                                                'data-last-order-identifier' => 'leads',
                                                'data-default-order' => get_table_last_order('leads'),
                                            )); ?>
                                    </div>
                                    <?php echo form_close()?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
   <?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>


</script>

<?php include_once(APPPATH . 'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
<?php
    include_once(APPPATH . 'views/admin/export_excel/export_lead.php');
?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var openLeadID = '<?php echo $leadid; ?>';
    $(function () {
        leads_kanban();
        $('#leads_bulk_mark_lost').on('change', function () {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh')
        });
        $('#move_to_status_leads_bulk').on('change', function () {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });
    });
    $(document).on('click', '.delete-remind_ch', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            _table_api.draw('page');
        }, 'json');
    }
    return false;
});
</script>
<!--Cong bổ sung-->
    <div class="div_modal_assigned"></div>
<!--END Cong bổ sung-->
</body>
</html>
