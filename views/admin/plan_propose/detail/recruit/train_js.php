<script>
    function totalTrain() {
        tb = '#tb-detail-train tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.td-number').html(stt);
            // quantity = intVal($(element).find('.td-quantity').html());
            quantity = intVal($(element).find('.quantity').val());
            //  
        }
    }


    $(document).ready(function() {
        if (edit_train == 0) {} else if (edit_train == 1) {
            for (i = 0; i < counter; i++) {
                items = $('input#items_' + i + '').attr('data-id');
                items_replace = $('input#items_replace_' + i + '').attr('data-id');
                cost = $('select#costs_' + i + '').attr('data-id');
                ajaxSelectCallBack($('input#items_' + i + ''), 'admin/plan_propose/searchMachines', items);
                ajaxSelectCallBack($('input#items_replace_' + i + ''), 'admin/plan_propose/searchMachines', items_replace);
                getCost(i, cost);
            }
            $('select.unit_id').select2();
        }

        dt = $('#tb-detail-train').DataTable({
            "language": lang_datatables,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        });

    });

    $('.add-row').on('click', function(event) {
        event.preventDefault();

        tdNumber = '<div class="td-number text-center"></div>';
        tdSupplies = '<div class="td-code mbot10"><input type="hidden" id="counter" class="form-control counter" value="' + counter + '">\
                <input type="text" name="train[' + counter + '][items_id]" id="items_' + counter + '" class="items_id" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value=""></div>' +
            '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' + lang_core['delete'] + '</a></div></div>';
        tdLevel = '<div class="td-level"><input type="text" name="train[' + counter + '][level]" id="level[]" class="form-control level " style="width: 100%;" value=""></div>';
        tdSpecialize = '<div class="td-specialize"><input type="text" name="train[' + counter + '][specialize]" id="specialize[]" class="form-control specialize " style="width: 100%;" value=""></div>';
        tdStandard = '<div class="td-standard"><input type="text" name="train[' + counter + '][standard]" id="standard[]" class="form-control standard " style="width: 100%;" value=""></div>';
        tdQuantity = '<div class="td-quantity"><input onchange="totalTrain()" type="text" name="train[' + counter + '][quantity]" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"></div>';
        tdDateFinish = '<div class="td-date-from">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_finish]" id="input" class="form-control datepicker date_finish" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';
        tdDateFrom = '<div class="td-date-from">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_from]" id="input" class="form-control datepicker date_from" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';
        tdDateTo = '<div class="td-date-to">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_to]" id="input" class="form-control datepicker date_to" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';



        tdAcceptance = '<div class="td-acceptance"><input type="text" name="train[' + counter + '][acceptance]" id="acceptance[]" class="form-control acceptance " style="width: 100%;" value=""></div>';
        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';

        rowNode = dt.row.add([
            tdNumber,
            tdSupplies,
            tdLevel,
            tdSpecialize,
            tdStandard,
            tdQuantity,
            tdDateFinish,
            tdDateFrom,
            tdDateTo,
            tdAcceptance,
            tdActions
        ]).draw(false).node();
        ajaxSelectCallBack($('input#items_' + counter + ''), 'admin/plan_propose/searchMachines', 0);

        getCost(counter, '');

        counter++;
        init_datepicker();

        // totalPurchaseProducts();
    });

    function removeRow(el) {
        dt.row($(el).parents('tr')).remove().draw();
    }

    function repoFormatHtml(item) {
        var originalOption = item.element;
        return item.text;
    }

    function getCost(counter, cost) {
        optionWh = '';
        optionWh = '<option value=""></option>';
        if (costs.length > 0) {
            $.each(costs, function(k, v) {
                optionWh += `<option value="${v.id}">${v.name}</option>`;
            })
        }
        $('#costs_' + counter + '').html(optionWh);
        $('#costs_' + counter + '').select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('#costs_' + counter + '').select2("val", cost);
        $('#costs_' + counter + '').change();
    }
</script>