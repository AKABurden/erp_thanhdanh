<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .breadcrumb{
        margin-bottom: 0px !important;
    }
    #tb-payroll-salary_wrapper table th{
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <input type="hidden" name="ids" class="ids" value="<?= $ids ?>">
    <?php echo form_open('admin/payroll/editPayroll', array('id' => 'payroll')); ?>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel" style="margin-bottom: 3px;">
                    <div class="panel-body" style="padding: 0px;">
                        <div class="row" style="padding: 1px;display:flex">
                            <div class="col-md-2">
                                <?= lang('year', 'year') ?>
                                <select name=" year" id="year" class="none-event" data-placeholder="<?= lang('year') ?>"
                                    style="width: 100%;" style="width: 100%;">
                                    <?php if (!empty(getYear())) : ?>
                                    <?php foreach (getYear() as $key => $value) : ?>
                                    <option <?= $year == $key ? 'selected' : '' ?> value="<?= $key ?>">
                                        <?= $value ?>
                                    </option>
                                    <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <?= lang('month', 'month') ?>
                                <select name="month" id="month" class="none-event"
                                    data-placeholder="<?= lang('month') ?>" style="width: 100%;" style="width: 100%;">
                                    <?php if (!empty(getMonth())) : ?>
                                    <?php foreach (getMonth() as $key => $value) : ?>
                                    <option <?= $month == $key ? 'selected' : '' ?> value="<?= $key ?>">
                                        <?= $value ?>
                                    </option>
                                    <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <?= lang('Chi nhánh', 'branch') ?>
                                <select class="selectpicker branch none-event form-control" name="branch_search" id="branch_search" data-live-search="true"  title='<?php echo _l('Chi nhánh'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php if (!empty($branch)) { ?>
                                        <?php foreach ($branch as $key => $value) { ?>
                                            <option <?= $branch_new == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?= lang('Nhân viên', 'staff_search') ?>
                                <input name="staff_search" id="staff_search" class="staff_search form-control"
                                    placeholder="<?= lang('Tìm kiếm nhân viên') ?>" style="width: 100%;">
                            </div>
                            <div class="col-md-2" style="margin-top: 5px">
                                <br>
                                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                                    <?php echo _l('submit'); ?>
                                </button>
                            </div>

                        </div>
                        <br>
                        <div class="col-md-12 view-table-payroll-salary" style="padding: 1px; margin-top: 10px">

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var oTable = '';
var workDayDefault = 0;
count_error = 0;

function loadPayrollSalaryEdit() {
    month = $('#month').val();
    year = $('#year').val();
    ids = $('.ids').val();

    if (month && year) {
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/payroll/loadPayrollSalaryEdit',
            data: {
                month: month,
                year: year,
                ids: ids,
                csrf_token_name: hash
            },
            dataType: "html",
            success: function(response) {
                if (response) {
                    $('.view-table-payroll-salary').html(response);
                }
            }
        });
    }
}


$(document).ready(function() {
    $('.action-menu').click();
    $('#month').select2();
    $('#year').select2();
    loadPayrollSalaryEdit();


    $(document).on('change', '#year, #month', function(event) {
        loadPayrollSalaryEdit();
    });

});
appValidateForm($('#payroll'), {
    month: 'required',
    year: 'required',
}, db);

//save db
function db(form) {
    if (count_error > 0){
        alert_float('danger','Kiểm tra lại dữ liệu nhập');
        return ;
    }
    $('.add').attr('disabled', 'disabled');
    var url = form.action;
    var form = $(form),
        formData = new FormData(),
        formParams = form.serializeArray();
    $.each(formParams, function(i, val) {
        formData.append(val.name, val.value);
    });
    $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        })
        .done(function(data) {
            if (data.result) {
                alert_float('success', data.message);
                window.location.href = site.base_url + 'admin/payroll/payroll_salary';
            } else {
                alert_float('danger', data.message, 10000);
                $('.add').removeAttr('disabled', 'disabled');
            }
        })
        .fail(function() {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
    return false;
}
$(document).ready(function () {
    searchTableCustomNew('.tb-payroll-salary-new', '#staff_search', '.tpagination');
});

function tpanigationNew(elTable, pageCurrent, iCall = 0)
{
    if (iCall == 0) {
        $(''+elTable+' tbody tr').attr('tsearch','ok');
    }
    numberPage = 1000;
    $(''+elTable+' tbody tr[tsearch="notok"]').css('display','none');
    $(''+elTable+' tbody tr[tsearch="ok"]').css('display','block');
    sum = $(''+elTable+' tbody tr[tsearch="ok"]').length;
    numPages = Math.ceil(sum/numberPage);
    start = (pageCurrent - 1) * numberPage;
    end   = numberPage * pageCurrent - 1;
    listRows = $(''+elTable+' tbody tr[tsearch="ok"]');
    for (i = 0; i<listRows.length; i++)
    {
        if(i >= start && i <= end)
        {
            listRows[i].style.display='';
        }
        else{
            listRows[i].style.display = 'none';
        }
    }
    soNut = numPages;
}

function searchTableCustomNew(elTable, elSearch, elPanigation) {
    elTableNew = '.table';
    $(elSearch).keyup(function(event){
        var search_string = bodauTiengViet($.trim($(elSearch).val()).replace(/ +/g,' ').toLowerCase());
        if (search_string == '') {
            $(''+elTable+' tbody tr').attr('tsearch','ok');
            tpanigationNew(elTable, 1, 1);
            tpanigationNew(elTableNew, 1, 1);
        } else {
            var listRows = $(''+elTable+' tbody tr');
            $(listRows).attr('tsearch','notok');
            for(i = 0 ; i<listRows.length; i++)
            {
                var str = bodauTiengViet($(listRows[i].children[2]).find('.td-name-staff').html().toLowerCase());
                if(str.search(search_string) >=0 )
                {
                    $(listRows[i]).attr('tsearch','ok');
                }
            }
            var listRowsNew = $(''+elTableNew+' tbody tr');
            $(listRowsNew).attr('tsearch','notok');
            for(i = 0 ; i<listRowsNew.length; i++)
            {
                if ($(listRowsNew[i].children[2]).find('.td-name-staff').html() != undefined) {
                    var str = bodauTiengViet($(listRowsNew[i].children[2]).find('.td-name-staff').html().toLowerCase());
                    if (str.search(search_string) >= 0) {
                        $(listRowsNew[i]).attr('tsearch', 'ok');
                    }
                }
            }
            tpanigationNew(elTable, 1, 1);
            tpanigationNew(elTableNew, 1, 1);
        }
        createPanigation(elTable, elPanigation, 1);
        createPanigation(elTableNew, elPanigation, 0);
    });
}
</script>