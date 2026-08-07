<script>
    function parseTime(s) {
        var c = s.split(':');
        return parseInt(c[0]) * 60 + parseInt(c[1]);
    }

    function totalTime() {
        tb = '#tb-detail-time tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.td-number').html(stt);
            // quantity = intVal($(element).find('.td-quantity').html());
            timestart = ($(element).find('.timestart').val());
            timeend = ($(element).find('.timeend').val());
            //
            total = timeend - timestart;
            var total = parseTime(timeend) - parseTime(timestart);
            if (typeof(timestart) == 'undefined' || typeof(timeend) == 'undefined') {
                $(element).find('.alltime ').val();
            } else {
                $(element).find('.alltime ').val((total / 60).toFixed(2));
            }
        }
    }
    $(document).ready(function() {
        if (edit_time == 0) {} else if (edit_time == 1) {
            for (i = 0; i < counter_time; i++) {
                items = $('input#items_time_' + i + '').attr('data-id');
                ajaxSelectCallBack($('input#items_time_' + i + ''), 'admin/plan_propose/searchMachines', items);
                console.log(i)
            }
            $('select.unit_id').select2();
        }
        dt_time = $('#tb-detail-time').DataTable({
            "language": lang_datatables,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        });
    });

    function removeRow_time(el) {
        dt_time.row($(el).parents('tr')).remove().draw();
    }

    function repoFormatHtml(item) {
        var originalOption = item.element;
        return item.text;
    }

    $('.add-row-time').on('click', function(event) {
        event.preventDefault();

        tdNumber = '<div class="td-number text-center"></div>';
        tdSupplies = '<div class="td-code mbot10" ><input type="hidden" id="counter" class="form-control counter" value="' + counter_time + '">\
                <input type="text" name="time[' + counter_time + '][items_id_time]" id="items_time_' + counter_time + '" class="items_id_time" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value=""></div>' +
            '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow_time(this)">' + lang_core['delete'] + '</a></div></div>';
        tdStaff = '<div class="td-staff"><input type="text" name="time[' + counter_time + '][staff]" id="staff[]" class="form-control staff " style="width: 100%;" value=""></div>';
        tdTimestart = '<div class="td-timestart"><input type="time" onchange="totalTime()" name="time[' + counter_time + '][timestart]" id="timestart[]" class="form-control timestart " style="width: 100%;" value=""></div>';
        tdTimeend = '<div class="td-timeend"><input type="time" onchange="totalTime()" name="time[' + counter_time + '][timeend]" id="timeend[]" class="form-control timeend " style="width: 100%;" value=""></div>';
        tdAllTime = '<div class="td-alltime"><input  readonly type="text" name="time[' + counter_time + '][alltime]" id="alltime[]" class="form-control alltime " style="width: 100%;" value=""></div>';
        tdAllPlan = '<div class="td-allplan"><input type="text" name="time[' + counter_time + '][allplan]" id="allplan[]" class="number-format form-control allplan " style="width: 100%;" value=""></div>';
        tdEvaluate = '<div class="td-evaluate"><input type="text" name="time[' + counter_time + '][evaluate]" id="evaluate[]" class="form-control evaluate " style="width: 100%;" value=""></div>';

        tdExceededtheQuota = '<div class="td-exceededthequota"><input type="text" name="time[' + counter_time + '][exceededthequota]" id="exceededthequota[]" class="number-format form-control exceededthequota " style="width: 100%;" value=""></div>';
        tdUnderperformingtheNorm = '<div class="td-underperformingthenorm"><input type="text" name="time[' + counter_time + '][underperformingthenorm]" id="underperformingthenorm[]" class="number-format form-control underperformingthenorm " style="width: 100%;" value=""></div>';
        tdHandoverDesk = '<div class="td-handoverdesk"><input type="text" name="time[' + counter_time + '][handoverdesk]" id="handoverdesk[]" class="form-control handoverdesk " style="width: 100%;" value=""></div>';

        tdWarranty = '<div class="td-warranty"><input type="text" name="time[' + counter_time + '][warranty]" id="warranty[]" class=" form-control warranty " style="width: 100%;" value=""></div>';
        tdSign = '<div class="td-sign"><input type="text" name="time[' + counter_time + '][sign]" id="sign[]" class="form-control sign " style="width: 100%;" value=""></div>';

        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow_time(this)"></i></div>';


        rowNode = dt_time.row.add([
            tdNumber,
            tdSupplies,
            tdStaff,
            tdTimestart,
            tdTimeend,
            tdAllTime,
            tdAllPlan,
            tdEvaluate,
            tdExceededtheQuota,
            tdUnderperformingtheNorm,
            tdHandoverDesk,
            tdWarranty,
            tdSign,
            tdActions
        ]).draw(false).node();
        ajaxSelectCallBack($('input#items_time_' + counter_time + ''), 'admin/plan_propose/searchMachines', 0);

        counter_time++;
        init_datepicker();

        // totalPurchaseProducts();
    });
</script>