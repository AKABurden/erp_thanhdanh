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
            price = intVal($(element).find('.price').val());
        }
    }


    $(document).ready(function() {
        if (edit_train == 0) {} else if (edit_train == 1) {
            for (i = 0; i < counter; i++) {
                object = $('input#object_' + i + '').attr('data-id');
                cost = $('select#costs_' + i + '').attr('data-id');
                unit_cost = $('select#units_cost_' + i + '').attr('data-id');
                substitutequota = $('select#substitutequota_' + i + '').attr('data-id');

                ajaxSelectCallBack_type_items($('input#object_' + i + ''), 'admin/plan_propose/searchObject', object);

                getCost(i, cost);
                getunitCost(i, unit_cost);
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
        tdObject = '<div class="td-code mbot10">\
                <input type="text" name="train[' + counter + '][object]" id="object_' + counter + '" class="object" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value=""></div>' +
            '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' + lang_core['delete'] + '</a></div></div>';
        tdCost = '<td><div class="text-center mbot10">\
                <select name="train[' + counter + '][costs]" style="width: 100%;" data-placeholder="Danh mục"  id="costs_' + counter + '" class="costs modal-select2">\
                </select>\
                </td>';
        tdUnitCost = '<td><div class="text-center mbot10">\
                <select name="train[' + counter + '][units_cost]" style="width: 100%;" data-placeholder="Đơn vị tính"  id="units_cost_' + counter + '" class="units_cost modal-select2">\
                </select>\
                </td>';
        tdPrice = '<div class="td-price"><input onchange="totalTrain()" type="text" name="train[' + counter + '][price]" id="price[]" class="form-control text-right price number-format" style="width: 100%;" value="0"></div>';
        tdDateFinish = '<div class="td-date-from">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_finish]" id="input" class="form-control datepicker date_finish" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';
        tdDateFrom = '<div class="td-date-from">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_from]" id="input" class="form-control datepicker date_from" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';
        tdDateTo = '<div class="td-date-to">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_to]" id="input" class="form-control datepicker date_to" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';

        tdDateWarehouse = '<div class="td-date-to">' +
            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' + counter + '][date_warehouse]" id="input" class="form-control datepicker date_warehouse" autocomplete="off" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>';
        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';

        rowNode = dt.row.add([
            tdNumber,
            tdObject,
            tdCost,
            tdUnitCost,
            tdPrice,
            tdDateFinish,
            tdDateFrom,
            tdDateTo,
            tdDateWarehouse,
            tdActions
        ]).draw(false).node();
        ajaxSelectCallBack_type_items($('input#object_' + counter + ''), 'admin/plan_propose/searchObject', 0);

        getCost(counter, '');
        getunitCost(counter, '');
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

    function getunitCost(counter, unit) {
        optionWh = '';
        optionWh = '<option value=""></option>';
        if (units_cost.length > 0) {
            $.each(units_cost, function(k, v) {
                optionWh += `<option value="${v.id}">${v.name}</option>`;
            })
        }
        $('#units_cost_' + counter + '').html(optionWh);
        $('#units_cost_' + counter + '').select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('#units_cost_' + counter + '').select2("val", unit);
        $('#units_cost_' + counter + '').change();
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