<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .bg-group {
        background: #daeaf9;
    }

    .group {
        background-color: #f9f9f9;
    }

    .staff-profile-image-small-new {
        width: 20px !important;
        height: 20px !important;
    }

    .tag-cs-red {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid red;
        color: red;
    }

    .tag-cs-color {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #2886e7;
        color: #2886e7;
    }

    .staff-profile-image-small-new {
        width: 25px;
        height: 25px;
        margin-top: 5px;
    }

    .tag-cs-danger {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #fb101b;
        color: #fb101b;
    }

    .tag-cs-primary {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #17adf1;
        color: #17adf1;
    }
    .active-machines{
        border-color:red !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.2') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <a class="btn btn-info test pull-right H_action_button chosen_machines">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('Chọn máy móc'); ?></a>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                                        <input type="text" name="productions_orders_search"
                                               data-placeholder="<?= lang('tnh_reference_productions_orders') ?>"
                                               id="productions_orders_search" class="productions_orders_search"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Công đoạn', 'stage_search') ?>
                                        <select class="selectpicker stage_search form-control" name="stage_search[]"
                                                id="stage_search"
                                                data-live-search="true"
                                                multiple
                                                data-actions-box="true"
                                                title='<?php echo _l('Công đoạn'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach ($stage as $key => $value) { ?>
                                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search"
                                               placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control"
                                               style="width: 100%;"
                                               value="">
                                    </div>
                                    <div>
                                        <button type="button" onclick="filter()" style="margin-top: 27px;" class="btn btn-default btn-primary"><span class="fa fa-filter"></span> Lọc</button>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table html_machines" role="tablist">
                                    <li role="presentation" class="active">
                                        <a style="padding: 3px 1px 3px 1px;">
                                            <button style="font-size: 11px;" type="button" id="tab_all"
                                                    data-toggle="tab" value="all" class="btn btn-danger btn-search"
                                                    data-value="all">
                                                <?= lang('Điều độ') ?>
                                            </button>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a style="padding: 3px 1px 3px 1px;">
                                            <button style="font-size: 11px;" type="button" id="tab_all_new"
                                                    data-toggle="tab" value="all_new" class="btn btn-warning btn-search"
                                                    data-value="all_new">
                                                <?= lang('Tổng hợp') ?>
                                            </button>
                                        </a>
                                    </li>
                                    <?php $machinesNew = $machines; $machinesNew = [];  if (!empty($machinesNew)) { ?>
                                        <?php foreach ($machinesNew as $key => $value) { ?>
                                            <li role="presentation">
                                                <a style="padding: 3px 1px 3px 1px;">
                                                    <button style="font-size: 11px; color: #fff;" type="button"
                                                            value="<?= $value['id'] ?>" data-toggle="tab"
                                                            class="btn btn-info btn-search"
                                                            data-value="<?= $value['id'] ?>">
                                                        <?= $value['name'] ?>
                                                    </button>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                       class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="table-responsive data-table">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="view_add_business_fee_boiler"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script>
    var dtMachines = <?= !empty($machines) ? json_encode($machines) : '{}' ?>;
    var getArrIdNew = localStorage.getItem('arrID') != null ? JSON.parse(localStorage.getItem('arrID')) : [];
    var arrId = getArrIdNew.length > 0 ? getArrIdNew : [];
    var arrIdNew = getArrIdNew.length > 0 ? getArrIdNew : [];
    $(document).ready(function () {
        choseMachines();
        // $("#tab_all").click();
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/production_smoothing/getListProductions', 0, false, true);
    });


    $(document).on('click', '.btn-search', function (event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        stage_search = $("#stage_search").val();
        console.log('aaa');

        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            'id': status_table,
            'stage_search[]': stage_search,
        };
        $(".data-table").html('');
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>production_smoothing/changeMachines/",
            data: dataString,
            dataType: "html",
            cache: false,
            success: function (data) {
                $(".data-table").html(data)
            }
        });
    });


    $("#stage_search").change(function () {
        status_table = $('#status_table').val();
        stage_search = $(this).val();
        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            'id': status_table,
            'stage_search[]': stage_search,
        };
        $(".data-table").html('');
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>production_smoothing/changeMachines/",
            data: dataString,
            dataType: "html",
            cache: false,
            success: function (data) {
                $(".data-table").html(data)
            }
        });
    });

    function filter(){
        status_table = $('#status_table').val();
        stage_search = $("#stage_search").val();

        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            'id': status_table,
            'stage_search[]': stage_search,
        };
        $(".data-table").html('');
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>production_smoothing/changeMachines/",
            data: dataString,
            dataType: "html",
            cache: false,
            success: function (data) {
                $(".data-table").html(data)
            }
        });
    }


    function get_total() {

        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            'name_search': $("#name_search").val(),
            'start_date_search': $("#start_date_search").val(),
            'end_date_search': $("#end_date_search").val(),
            "staff_search[]": $('#staff_search').val(),
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>business_fee/get_total/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $(".count-un_approved").html(tnhFormatNumber(data.un_approved));
                $(".count-approved").html(tnhFormatNumber(data.approved));
                $(".count-all").html(tnhFormatNumber(data.all));
            }
        });
    }

    var inner_popover_template =
        '<div class="popover" style="min-width:1300px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.chosen_machines', function (e) {
        var htmlMachines = '';
        arrIdNew = [...arrId];
        $.each(dtMachines,function (k,v){
            htmlMachines += `<div class="col-md-3" style="margin-top: 15px"><a onclick="clickSearchMachines(this)" class="click-search-machines ${arrId.includes(v.id) ? 'active-machines' : ''}" style="margin-right:5px;border: 1px solid #cdc6c6;padding:5px 7px 5px 7px;text-decoration: unset;color: #403e3e;border-radius: 5px;" data-value="${v.id}">${v.name}</a></div>`;
        })
        var dropdown_menu = `
           <div><input type="text" name="search" class="search_new form-control" placeholder="Nhập máy cần tìm"></div>
           <div class="row data_machines">
           ${htmlMachines}
           </div>
           <div class="clearfix"></div>
           <div class="row" style="margin-top: 20px;margin-bottom: 10px">
                <div class="col-md-12" style="text-align: right">
                    <a onclick="removeChoseMachines(this);" style="margin-right:5px;border: 1px solid red;padding:5px 25px 5px 25px;text-decoration: unset;color: #403e3e;border-radius: 5px;color: red">Bỏ chọn</a>
                    <a onclick="choseMachines(this)" style="margin-right:5px;border: 1px solid green;padding:5px 35px 5px 35px;text-decoration: unset;color: #403e3e;border-radius: 5px;color: green">Chọn</a>
                </div>
           </div>
        </div>`;
        $(this).popover({
            html: true,
            container: 'body',
            placement: "left",
            trigger: 'manual',
            title: '<?= _l('CHỌN MÁY MÓC') ?><button type="button" class="close close_pay">&times;</button>',
            content: function () {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        $(this).popover('toggle');
        searchCustom('.data_machines', '.search_new');
    });

    $(document).on('click', '.close_pay', function(e) {
        $('.chosen_machines').popover('hide');
        arrIdNew = [...arrId];
        $(this).parents('.popover').popover("destroy").popover();
    });

    function clickSearchMachines(_this){
        id = $(_this).attr('data-value');
        if ($(_this).hasClass('active-machines')){
            $(_this).removeClass('active-machines');
        } else {
            $(_this).addClass('active-machines');
        }
        index = jQuery.inArray(id, arrIdNew);
        if (index !== -1) {
            arrIdNew.splice(index, 1);
        } else {
            arrIdNew.push(id);
        }
    }

    function removeChoseMachines(){
        $(".click-search-machines").removeClass('active-machines');
        arrId = [];
        arrIdNew = [];
        choseMachines();
        $('.popover').popover("destroy").popover();
    }

    async function choseMachines(){
        arrId = arrIdNew;
        await saveFilter();
        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            "arrId[]": arrId,
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>production_smoothing/loadTabMachines/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                html = '';
                html += `<li role="presentation" class="active">
                            <a style="padding: 3px 1px 3px 1px;">
                                <button style="font-size: 11px;" type="button" id="tab_all"
                                        data-toggle="tab" value="all" class="btn btn-danger btn-search"
                                        data-value="all">
                                        Điều độ
                                </button>
                            </a>
                         </li>
                          <li role="presentation">
                            <a style="padding: 3px 1px 3px 1px;">
                                <button style="font-size: 11px;" type="button" id="tab_all_new"
                                        data-toggle="tab" value="all_new" class="btn btn-warning btn-search"
                                        data-value="all_new">
                                        Tổng hợp
                                </button>
                            </a>
                          </li>
                        `;
                $.each(data.machines,function (k,v){
                    html += `<li role="presentation">
                                <a style="padding: 3px 1px 3px 1px;">
                                    <button style="font-size: 11px; color: #fff;" type="button"
                                            value="${v.id}" data-toggle="tab"
                                            class="btn btn-info btn-search"
                                            data-value="${v.id}">
                                            ${v.name}
                                    </button>
                                </a>
                            </li>`;
                });
                $(".html_machines").html(html);
                $("#tab_all").click();
                $('.popover').popover("destroy").popover();
            }
        });
    }

    function saveFilter() {
        localStorage.setItem('arrID', JSON.stringify(arrId));
        var getArrId = JSON.parse(localStorage.getItem('arrID'));

        arrId = getArrId;
    }

    function bodauTiengViet(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
        str = str.replace(/đ/g, 'd');
        return str;
    }

    function hideData(elTable,iCall)
    {
        if (iCall == 0) {
            $(`${elTable} > div`).attr('tsearch','ok');
        }
        $(`${elTable} > div[tsearch="notok"]`).css('display','none');
        $(`${elTable} > div[tsearch="ok"]`).css('display','block');
        listRows = $(`${elTable} > div[tsearch="ok"]`);
        for (i = 0; i<listRows.length; i++)
        {
        }
    }

    function searchCustom(elTable, elSearch) {
        $(elSearch).keyup(function(event){
            var search_string = bodauTiengViet($.trim($(elSearch).val()).replace(/ +/g,' ').toLowerCase());
            if (search_string == '') {
                $(`${elTable} > div`).attr('tsearch','ok');
                hideData(elTable, 1);
            } else {
                var listRows = $(`${elTable} > div`);
                $(listRows).attr('tsearch','notok');
                for(i = 0 ; i<listRows.length; i++)
                {
                    var str = bodauTiengViet(listRows[i].innerHTML.toLowerCase());
                    if(str.search(search_string) >=0 )
                    {
                        $(listRows[i]).attr('tsearch','ok');
                    }
                }
                hideData(elTable, 1);
            }
        });
    }
</script>