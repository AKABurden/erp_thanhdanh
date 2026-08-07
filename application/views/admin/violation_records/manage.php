<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .popover-content a.label {
        border: 1px solid black;
        color: black;
        margin-bottom: 10px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <a href="<?=admin_url('violation_records/detail')?>" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
					<?php echo _l('create_add_new'); ?>
                </a>
                <div class="line-sp"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="horizontal-scrollable-tabs">
                    <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                            <li class="active">
                                <a class="H_filter in-title" data-id="">
									<?= _l('leads_all') ?> (<span class="fieldstatus status_all">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter in-title" data-id="0">
									<?= _l('Chưa duyệt') ?> (<span class="fieldstatus status_0">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter in-title" data-id="1">
									<?= _l('Đã duyệt') ?> (<span class="fieldstatus status_1">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter in-title" data-id="2">
									<?= _l('Đã hủy') ?> (<span class="fieldstatus status_2">0</span>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                <div class="panel_s">
                    <div class="panel-body">
						<?php render_datatable(array(
							_l('#'),
							_l('c_code_list_protocol'),
							_l('c_date_violation_records'),
							_l('c_staff'),
							_l('c_title_list_protocol'),
							_l('c_object'),
							_l('c_ct'),
							_l('Trưởng phòng duyệt'),
							_l('Nhân viên xác nhận'),
							_l('c_note'),
							_l('create_by'),
							_l('options'),
						),'violation_records'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $('.H_filter').click(function (e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value).change();
    });


    var filterList = {
        'datestart' : '[name="date_start"]',
        'dateend' : '[name="date_end"]',
        'filterStatus' : '[name="filterStatus"]',
    };
    var tAPI;
    $(function(){
        tAPI = initDataTable('.table-violation_records', admin_url + 'violation_records/table', [0], [0], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-violation_records').hasClass('dataTable')) {
                $('.table-violation_records').DataTable().ajax.reload();
            }
        })
    })

    $('.table-violation_records').on('draw.dt', function () {
        var invoiceReportsTable = $(this).DataTable();
        var status = invoiceReportsTable.ajax.json().status;
        // var all = 0;
        $('.fieldstatus').text(0);
        $.each(status, function (i, v) {
            $('.status_' + i).text(v);
            // all += parseFloat(v);
        })
        // $('.status_all').text(all);
    })

    $('body').on('click', '#agree', function() {
        var id = $(this).data('id');
        var status = $(this).attr('value');
        var person = '';

        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;

        if(status == 2) {
            if(!confirm('Bạn có chắc muốn hủy phiếu?')) {
                return false;
            }

            person = prompt("Lý do bạn muốn hủy phiếu:", "");
            if(person == null) {
                return false;
            }
            data['person_status'] = person;

        }
        $.post(admin_url + 'violation_records/update_status_admin', data, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
            if(result.success) {
                $('.table-violation_records').DataTable().ajax.reload();
                $('.popover').closest('div.popover').popover('hide');
            }
        })
    })

    $('body').on('click', '#agree_staff', function() {
        var id = $(this).data('id');
        var status = $(this).attr('value');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;

        $.post(admin_url + 'violation_records/update_status_staff', data, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
            if(result.success) {
                $('.table-violation_records').DataTable().ajax.reload();
                $('.popover').closest('div.popover').popover('hide');
            }
        })
    })

    $(document).on('click', '.po-close', function() {
        $('.popover').closest('div.popover').popover('hide');
        return false;
    });
</script>
