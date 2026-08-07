<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" id="jquery-gantt-css" href="<?= base_url('assets/plugins/gantt/css/style.css?v=2.3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('gantt.css') ?>">
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="<?=!empty($dataLog) ? 'col-md-9' : 'col-md-12'?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row mbot10">
                            <div class="col-md-12">
                                <div class="pull-right">
                                    <table class="tnh-table table-hover table-bordered fn-gantt" cellpadding="3">
                                        <tbody>
                                            <tr>
                                                <td class="ganttPrimary" style="width: 30px;"></td>
                                                <td><?= lang('tnh_bt') ?></td>
                                                <td class="ganttRed" style="width: 30px;"></td>
                                                <td><?= lang('tnh_trh') ?></td>
                                                <td class="ganttGreen" style="width: 30px;"></td>
                                                <td><?= lang('tnh_th') ?></td>
                                                <td class="ganttYellow" style="width: 30px;"></td>
                                                <td><?= lang('tnh_sth') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mbot10">
                            <div class="col-md-4">
                                <?= lang('ch_chose_suppliers') ?>
                                <input type="text" name="productions_orders" id="productions_orders" class="ajax-search" data-placeholder="<?= lang('ch_chose_suppliers') ?>" style="width: 100%;" value="<?= $this->input->post('productions_orders') ?>">
                            </div>
                            <div class="col-md-4">
                                <?= lang('ch_order') ?>
                                <input type="text" name="productions_orders_id" id="productions_orders_id" class="ajax-search" data-placeholder="<?= lang('dropdown_non_selected_tex') ?>" style="width: 100%;" value="<?= $this->input->post('productions_orders_id') ?>">
                            </div>
                            <div class="col-md-4 mtop20">
                                <button type="submit" name="search" class="btn btn-info"><?= lang('search') ?></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="gantt"></div>
                        <div class="text-center">
                            <ul class="pagination">
                                <?php
                                    $page = 1;
                                    for ($i = 0; $i < $numPages; $i++) {
                                        $active = $page == $pageCurrent ? 'active' : '';
                                        echo '<li class="'.$active.'"><a href="'.base_url('admin/puchases_gantt_order?page='.$page).'">'.$page.'</a></li>';
                                        $page++;
                                    }
                                ?>
                          </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="<?=!empty($dataLog) ? 'col-md-3' : 'hide'?>">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?=_l('activity_log_puchases')?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="date_space_activity"><?=_l('cong_automations_time')?></label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" id="date_space_activity" class="form-control date_space_activity" aria-invalid="false" data-module='purchase'>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo render_select('staff_activity',$staff,array('staffid','name'),'by_staff_log','',array('data-module'=>'purchase')); ?>
                        </div>
                        <hr />
                        <div class="activity-container" style="max-height: 600px;">
                            <?php foreach ($dataLog as $key => $value) { ?>
                                <div class="feed-item">
                                    <div class="activity-text">
                                        <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                                    </div>
                                    <div class="activity-time">
                                        <?= time_ago($value['date']) ?> <span class="activity-module"><?=_l($value['table_obj'])?></span>
                                    </div>
                                    <div>
                                        <?=$value['content']?>
                                    </div>
                                </div>
                          <?php } ?>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-info more_log" onclick="load_more_log('purchase'); return false;"><?=_l('load_more')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<?php $this->load->view('admin/popup_purchase_order/manage')?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" id="jquery-gantt-js" src="<?= base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js?v=2.3.3') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {status_table: '#status_table'};
    var oTable = '';
</script>
<script type="text/javascript">
    var gantt_data = {};
</script>
<script>
    $('body').on('hidden.bs.modal', '#view_modal_import_ch', function() {
    $('#view_modal_import').html('');
    });
    $(function(){
        active_daterangepicker();
        ajaxSelectParamsCallback('#productions_orders', 'admin/puchases_gantt_order/searchsuppliers', $('#productions_orders').val(), false);
        ajaxSelectParamsCallback_v2('#productions_orders_id', 'admin/puchases_gantt_order/productions_orders_id', $('#productions_orders_id').val(), false);
        <?php if (!empty($gantt_data)): ?>
            gantt_data = <?= json_encode($gantt_data) ?>;
        <?php endif ?>
        $(function(){
            $("#gantt").gantt({ 
                source: gantt_data,
                itemsPerPage: 10000000000000,
                months: app.months_json,
                navigate: 'scroll',
                onRender: function(ev) {
                    $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css('background', 'initial');
                    
                    var descLabelLength = $('.leftPanel .desc .fn-label').length;
                    for (var i = 0; i < descLabelLength; i++) {
                        el = $('.leftPanel .desc .fn-label')[i];
                        descLabel = $(el).html();
                        if (descLabel == "productions_orders") {
                            $(el).html('');
                        }
                    }
                    $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css({
                        'background': 'initial',
                        'width': '25px',
                    });
                    var emptyLength = $('#gantt .leftPanel .name .fn-label:empty').length;
                    for (var i = 0; i < emptyLength; i++) {
                        el = $('#gantt .leftPanel .name .fn-label:empty')[i];
                        descNext = $(el).closest('div').next('.desc');
                        descNext.css({
                            'width': '275px',
                        });

                        el = $('#gantt .leftPanel .name .fn-label:empty')[i];
                        descNext = $(el).closest('div').next('.desc');
                        descNext.css({
                            'background': 'initial',
                            'width': '275px',
                        });
                    }
                    $('#gantt .leftPanel .desc').find('.SO_parrens').parents('.desc').css('background', 'pink');
                },
                onItemClick: function(data) {
                    if(typeof(data.production_order_detail_id) != 'undefined') {
                        view_modal_import(data.production_order_detail_id);
                        // window.location.href = podViewUrl+'/'+data.production_order_detail_id;
                    }   
                },
                onAfterAutoSchedule: function(data){
                    console.log(123);
                }
            });
        });
    });

    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        
        return   '('+state.code+')-'+state.text;
    }
    function ajaxSelectParamsCallback_v2(element, url, id, params = false)
    {
        if (id != 0)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            supplier: $('#productions_orders').val(),
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoFormatSelectionss,
                formatSelection: repoFormatSelectionss,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            supplier: $('#productions_orders').val(),
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoFormatSelectionss,
                formatSelection: repoFormatSelectionss,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
            });
        }
    }
    function repoFormatSelectionss(state) {
        return state.text;
    }
    function ajaxSelectParamsCallback(element, url, id, params = false)
    {
        if (id != 0)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; }
            });
        }
    }

    var active_daterangepicker = () => {
        $('.date_space_activity').daterangepicker({
            opens: 'left',
            autoUpdateInput: false, 
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {
        });
        $('.date_space_activity').val('').datepicker("refresh");
        $('.date_space_activity').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $( "#date_space_activity" ).trigger( "change" );
        });
    };
</script>
