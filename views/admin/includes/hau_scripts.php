<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table tbody tr td {
        border-bottom: 1px solid #cedae6;
        border-left: 1px solid #cedae6;
    }

    .table tbody tr td:last-child {
        border-right: 1px solid #cedae6;
    }

    .table tbody tr:first-child {
        /*border-top: 1px solid #cedae6 !important;*/
    }

    .table tfoot tr td {
        border-bottom: 1px solid #cedae6;
        border-left: 1px solid #cedae6;
    }

    .table tfoot tr td:last-child {
        border-right: 1px solid #cedae6;
    }
</style>
<div id="purchases_data"></div>
<div id="view_supplier_quotes"></div>
<div id="rdq_modal_data"></div>
<div id="suppliers_view_data"></div>
<div id="purchases_data_view"></div>
<div id="evaluate_modal_data"></div>
<div id="purchase_order_data"></div>
<div id="purchases_data_rdq"></div>
<div id="import_data"></div>
<div id="view_modal_import"></div>
<div id="add_flash_transporters"></div>
<div id="view_service_detail"></div>
<script type="text/javascript">
    function view_detail_service(id) {
        $('#view_service_detail').html('');
        $.get(admin_url + 'service/view_service_detail/' + id).done(function(response) {
            $('#view_service_detail').html(response);
            $('#detail_service').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function add_html_evaluate(id_supplier) {
        $('.evaluate_view').html('');
        $.ajax({
                url: admin_url + 'suppliers/get_html_evaluate/' + id_supplier,
                dataType: 'json',
            })
            .done(function(data) {
                $('.evaluate_view').html(data.data);
            });
    }

    function int_suppliers_view(id = null, edit = false) {
        $('#suppliers_view_data').html('');
        $.get(admin_url + 'suppliers/int_suppliers_view/' + edit + '/' + id + '/1').done(function(response) {
            $('#suppliers_view_data').html(response);
            $('#suppliers_add').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            add_html_evaluate(id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function int_suppliers_view_new(id = null, edit) {
        $('#suppliers_view_data').html('');
        $.get(admin_url + 'suppliers/int_suppliers_add/' + edit + '/' + id).done(function(response) {
            $('#suppliers_view_data').html(response);
            add_html_evaluate(id);
            $('#suppliers_add').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#suppliers_add', function() {
        $('#suppliers_view_data').html('');
    });

    function view_purchases(id) {
        $('#purchases_data_view').html('');
        $.get(admin_url + 'purchases/views_purchases/' + id).done(function(response) {
            $('#purchases_data').html(response);
            $('#views_purchases').modal('show');
            changeRowNew('tblpurchases', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#views_purchases', function() {
        $('#purchases_data').html('');
    });

    function view_import(id) {
        $('#import_data').html('');
        $.get(admin_url + 'import/views_import/' + id).done(function(response) {
            $('#import_data').html(response);
            changeRowNew('tblimport', id);
            $('#views_import').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function view_supplier_quotes(id) {
        $('#purchases_data').html('');
        $.get(admin_url + 'supplier_quotes/view_supplier_quotes/' + id).done(function(response) {
            $('#view_supplier_quotes').html(response);
            changeRowNew('tblsupplier_quotes', id);
            $('#views_items').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function rdq_modal(id) {
        $('#purchases_data').html('');
        $.get(admin_url + 'purchases/rfq_modal/' + id + '/2').done(function(response) {
            $('#rdq_modal_data').html(response);
            changeRowNew('tblrfq_ask_price', id);
            $('#views_purchases').modal('hide');
            $('#rdq_modal').modal('show');
            init_selectpicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }


    function view_purchase_order(id) {
        $('#purchase_order_data').html('');
        $.get(admin_url + 'purchase_order/view_purchase_order/' + id).done(function(response) {
            $('#purchase_order_data').html(response);
            changeRowNew('tblpurchase_order', id);
            $('#view_purchase_order').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function view_modal_import(id) {
        $('#purchase_order_data').html('');
        $.get(admin_url + 'purchase_order/view_purchase_order_import/' + id).done(function(response) {
            $('#view_modal_import').html(response);
            changeRowNew('tblpurchase_order', id);
            $('#view_modal_import_ch').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function add_flash_transporters(id) {
        $('#add_flash_transporters').html('');
        $.get(admin_url + 'suppliers/add_flash_transporters/').done(function(response) {
            $('#add_flash_transporters').html(response);
            $('#add_transporters').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function initDataTable_manufactures(e, t, a, i, n, s) {
        var o = "string" == typeof e ? $("body").find("table" + e) : e;
        if (0 === o.length) return !1;
        n = "undefined" == n || void 0 === n ? [] : n, void 0 === s ? s = [
            [0, "asc"]
        ] : 1 === s.length && (s = [s]);
        var l = o.attr("data-default-order");
        if (!empty(l)) {
            var d = JSON.parse(l),
                r = [];
            for (var c in d) o.find("thead th:eq(" + d[c][0] + ")").length > 0 && r.push(d[c]);
            r.length > 0 && (s = r)
        }
        var p = [10, 25, 50, 100],
            _ = [10, 25, 50, 100];
        app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit), -1 == $.inArray(app.options
            .tables_pagination_limit, p) && (p.push(app.options.tables_pagination_limit), _.push(app.options
            .tables_pagination_limit)), p.sort(function(e, t) {
            return e - t
        }), _.sort(function(e, t) {
            return e - t
        }), p.push(-1), _.push(app.lang.dt_length_menu_all);
        var m = {
            rowsGroup: [4],
            language: app.lang.datatables,
            processing: !0,
            retrieve: !0,
            serverSide: !0,
            paginate: !0,
            searchDelay: 750,
            bDeferRender: !0,
            responsive: !0,
            autoWidth: !1,
            // dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>", old table
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            pageLength: app.options.tables_pagination_limit,
            lengthMenu: [p, _],
            columnDefs: [{
                searchable: !1,
                targets: a
            }, {
                sortable: !1,
                targets: i
            }],
            fnDrawCallback: function(e) {
                _table_jump_to_page(this, e), 0 === e.aoData.length ? $(e.nTableWrapper).addClass("app_dt_empty") :
                    $(e.nTableWrapper).removeClass("app_dt_empty")
            },
            fnCreatedRow: function(e, t, a) {
                $(e).attr("data-title", t.Data_Title), $(e).attr("data-toggle", t.Data_Toggle)
            },
            initComplete: function(e, t) {
                var a = this,
                    i = $(".btn-dt-reload");
                i.attr("data-toggle", "tooltip"), i.attr("title", app.lang.dt_button_reload);
                var n = $(".dt-column-visibility");
                n.attr("data-toggle", "tooltip"), n.attr("title", app.lang.dt_button_column_visibility), (a
                    .hasClass("scroll-responsive") || 1 == app.options.scroll_responsive_tables) && a.wrap(
                    '<div class="table-responsive"></div>');
                var s = a.find(".dataTables_empty");
                s.length && s.attr("colspan", a.find("thead th").length), is_mobile() && $(window).width() < 400 &&
                    a.find('tbody td:first-child input[type="checkbox"]').length > 0 && (a.DataTable().column(0)
                        .visible(!1, !1).columns.adjust(), $("a[data-target*='bulk_actions']").addClass("hide")), a
                    .parents(".table-loading").removeClass("table-loading"), a.removeClass("dt-table-loading");
                var o = a.find("thead th:last-child"),
                    l = a.find("thead th:first-child");
                o.text().trim() == app.lang.options && o.addClass("not-export"), l.find('input[type="checkbox"]')
                    .length > 0 && l.addClass("not-export"), mainWrapperHeightFix()
            },
            order: s,
            ajax: {
                url: t,
                type: "POST",
                data: function(e) {
                    "undefined" != typeof csrfData && (e[csrfData.token_name] = csrfData.hash);
                    for (var t in n) e[t] = $(n[t]).val();
                    o.attr("data-last-order-identifier") && (e.last_order_identifier = o.attr(
                        "data-last-order-identifier"))
                }
            },
            buttons: get_datatable_buttons(o)
        };
        (o.hasClass("scroll-responsive") || 1 == app.options.scroll_responsive_tables) && (m.responsive = !1);
        var u = (o = o.dataTable(m)).DataTable(),
            f = o.find("th.not_visible"),
            h = [];
        if ($.each(f, function() {
                h.push(this.cellIndex)
            }), setTimeout(function() {
                for (var e in h) u.columns(h[e]).visible(!1, !1).columns.adjust()
            }, 10), o.hasClass("customizable-table")) {
            var v = o.find("th.toggleable"),
                b = $("#hidden-columns-" + o.attr("id"));
            try {
                b = JSON.parse(b.text())
            } catch (e) {
                b = []
            }
            $.each(v, function() {
                var e = $(this).attr("id");
                $.inArray(e, b) > -1 && u.column("#" + e).visible(!1)
            })
        }
        return o.is(":hidden") && o.find(".dataTables_empty").attr("colspan", o.find("thead th").length), o.on("preXhr.dt",
            function(e, t, a) {
                t.jqXHR && t.jqXHR.abort()
            }), u
    }

    function initDataTable_ch(e, t, a, i, n, s) {
        var o = "string" == typeof e ? $("body").find("table" + e) : e;
        if (0 === o.length) return !1;
        n = "undefined" == n || void 0 === n ? [] : n, void 0 === s ? s = [
            [0, "asc"]
        ] : 1 === s.length && (s = [s]);
        var l = o.attr("data-default-order");
        if (!empty(l)) {
            var d = JSON.parse(l),
                r = [];
            for (var c in d) o.find("thead th:eq(" + d[c][0] + ")").length > 0 && r.push(d[c]);
            r.length > 0 && (s = r)
        }
        var p = [10, 25, 50, 100],
            _ = [10, 25, 50, 100];
        app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit), -1 == $.inArray(app.options
            .tables_pagination_limit, p) && (p.push(app.options.tables_pagination_limit), _.push(app.options
            .tables_pagination_limit)), p.sort(function(e, t) {
            return e - t
        }), _.sort(function(e, t) {
            return e - t
        }), p.push(-1), _.push(app.lang.dt_length_menu_all);
        var m = {
            rowsGroup: [0, 1],
            language: app.lang.datatables,
            processing: !0,
            retrieve: !0,
            serverSide: !0,
            paginate: !0,
            searchDelay: 750,
            bDeferRender: !0,
            responsive: !0,
            autoWidth: !1,
            // dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>", old table
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            pageLength: app.options.tables_pagination_limit,
            lengthMenu: [p, _],
            columnDefs: [{
                searchable: !1,
                targets: a
            }, {
                sortable: !1,
                targets: i
            }],
            fnDrawCallback: function(e) {
                _table_jump_to_page(this, e), 0 === e.aoData.length ? $(e.nTableWrapper).addClass("app_dt_empty") :
                    $(e.nTableWrapper).removeClass("app_dt_empty")
            },
            fnCreatedRow: function(e, t, a) {
                $(e).attr("data-title", t.Data_Title), $(e).attr("data-toggle", t.Data_Toggle)
            },
            initComplete: function(e, t) {
                var a = this,
                    i = $(".btn-dt-reload");
                i.attr("data-toggle", "tooltip"), i.attr("title", app.lang.dt_button_reload);
                var n = $(".dt-column-visibility");
                n.attr("data-toggle", "tooltip"), n.attr("title", app.lang.dt_button_column_visibility), (a
                    .hasClass("scroll-responsive") || 1 == app.options.scroll_responsive_tables) && a.wrap(
                    '<div class="table-responsive"></div>');
                var s = a.find(".dataTables_empty");
                s.length && s.attr("colspan", a.find("thead th").length), is_mobile() && $(window).width() < 400 &&
                    a.find('tbody td:first-child input[type="checkbox"]').length > 0 && (a.DataTable().column(0)
                        .visible(!1, !1).columns.adjust(), $("a[data-target*='bulk_actions']").addClass("hide")), a
                    .parents(".table-loading").removeClass("table-loading"), a.removeClass("dt-table-loading");
                var o = a.find("thead th:last-child"),
                    l = a.find("thead th:first-child");
                o.text().trim() == app.lang.options && o.addClass("not-export"), l.find('input[type="checkbox"]')
                    .length > 0 && l.addClass("not-export"), mainWrapperHeightFix()
            },
            order: s,
            ajax: {
                url: t,
                type: "POST",
                data: function(e) {
                    "undefined" != typeof csrfData && (e[csrfData.token_name] = csrfData.hash);
                    for (var t in n) e[t] = $(n[t]).val();
                    o.attr("data-last-order-identifier") && (e.last_order_identifier = o.attr(
                        "data-last-order-identifier"))
                }
            },
            buttons: get_datatable_buttons(o)
        };
        (o.hasClass("scroll-responsive") || 1 == app.options.scroll_responsive_tables) && (m.responsive = !1);
        var u = (o = o.dataTable(m)).DataTable(),
            f = o.find("th.not_visible"),
            h = [];
        if ($.each(f, function() {
                h.push(this.cellIndex)
            }), setTimeout(function() {
                for (var e in h) u.columns(h[e]).visible(!1, !1).columns.adjust()
            }, 10), o.hasClass("customizable-table")) {
            var v = o.find("th.toggleable"),
                b = $("#hidden-columns-" + o.attr("id"));
            try {
                b = JSON.parse(b.text())
            } catch (e) {
                b = []
            }
            $.each(v, function() {
                var e = $(this).attr("id");
                $.inArray(e, b) > -1 && u.column("#" + e).visible(!1)
            })
        }
        return o.is(":hidden") && o.find(".dataTables_empty").attr("colspan", o.find("thead th").length), o.on("preXhr.dt",
            function(e, t, a) {
                t.jqXHR && t.jqXHR.abort()
            }), u
    }

    function initDataTableFixedHeader_ch(table, url, notsearchable, notsortable, fnserverparams, defaultorder,
        fixedColumns) {
        // alert(table);
        var _table_name = table;
        if ($(table).length == 0) {
            return;
        }
        if (fnserverparams == 'undefined' || typeof(fnserverparams) == 'undefined') {
            fnserverparams = []
        }

        // If not order is passed order by the first column
        if (typeof(defaultorder) == 'undefined') {
            defaultorder = [
                [0, 'ASC']
            ];
        } else {
            if (defaultorder.length == 1) {
                defaultorder = [defaultorder]
            }
        }

        var length_options = [10, 25, 50, 100];
        var length_options_names = [10, 25, 50, 100];

        app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

        if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
            length_options.push(app.options.tables_pagination_limit)
            length_options_names.push(app.options.tables_pagination_limit)
        }

        length_options.sort(function(a, b) {
            return a - b;
        });
        length_options_names.sort(function(a, b) {
            return a - b;
        });

        length_options.push(-1);
        length_options_names.push(app.lang.dt_length_menu_all);

        var table = $('body').find(table).dataTable({
            fixedColumns: fixedColumns || false,
            "sScrollX": "100%",
            "sScrollXInner": "100%",
            "bScrollCollapse": true,
            scrollY: '80vh',
            scrollCollapse: true,
            language: app.lang.datatables,
            "processing": true,
            "retrieve": true,
            "serverSide": true,
            'paginate': true,
            'searchDelay': 700,
            "bDeferRender": true,
            "responsive": true,
            "autoWidth": false,
            dom: "<'mbot25'B><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [length_options, length_options_names],
            "columnDefs": [{
                "searchable": false,
                "targets": notsearchable,
            }, {
                "sortable": false,
                "targets": notsortable
            }],
            "fnCreatedRow": function(nRow, aData, iDataIndex) {
                // If tooltips found
                $(nRow).attr('data-title', aData.Data_Title)
                $(nRow).attr('data-toggle', aData.Data_Toggle)
            },
            "initComplete": function(settings, json) {
                var a = this,
                    i = $(".btn-dt-reload");
                i.attr("data-toggle", "tooltip"), i.attr("title", app.lang.dt_button_reload);
                var n = $(".dt-column-visibility");
                n.attr("data-toggle", "tooltip"), n.attr("title", app.lang.dt_button_column_visibility), (a
                    .hasClass("scroll-responsive") || 1 == app.options.scroll_responsive_tables) && a.wrap(
                    '<div class="table-responsive"></div>');
                var s = a.find(".dataTables_empty");
                s.length && s.attr("colspan", a.find("thead th").length), is_mobile() && $(window).width() <
                    400 && a.find('tbody td:first-child input[type="checkbox"]').length > 0 && (a.DataTable()
                        .column(0).visible(!1, !1).columns.adjust(), $("a[data-target*='bulk_actions']")
                        .addClass("hide")), a.parents(".table-loading").removeClass("table-loading"), a
                    .removeClass("dt-table-loading");
                var o = a.find("thead th:last-child"),
                    l = a.find("thead th:first-child");
                o.text().trim() == app.lang.options && o.addClass("not-export"), l.find(
                    'input[type="checkbox"]').length > 0 && l.addClass("not-export"), mainWrapperHeightFix()
            },
            "order": defaultorder,
            "ajax": {
                "url": url,
                "type": "POST",
                "data": function(d) {
                    "undefined" != typeof csrfData && (d[csrfData.token_name] = csrfData.hash);
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                }
            },
            buttons: get_datatable_buttons(table),
        });

        var tableApi = table.DataTable();
        var hiddenHeadings = $(table).find('th.not_visible');
        $.each(hiddenHeadings, function() {
            tableApi.columns(this.cellIndex).visible(false, false);
        });
        // Fix for hidden tables colspan not correct if the table is empty
        if ($(_table_name).is(':hidden')) {
            $(_table_name).find('.dataTables_empty').attr('colspan', $(_table_name).find('thead th').length);
        }

        return tableApi;
    }
    var base_url_ch = '<?= base_url() ?>';

    function repoFormatSelection_ch(result) {
        if (!result.id) return result.text; // optgroup
        tr = '';
        if (result) {
            if (result.img) {
                var img = '<img class="img_option" src="' + base_url_ch + result.img + '"/> ';
            } else {
                var img = '<img class="img_option" src="' + base_url_ch + 'download/preview_image"/> ';
            }
            if (result.type == 'tools') {
                tr += '<td style="width: 100%;border:0 !important;padding:0 !important">' +
                    '<div class="bold" style="font-size: 14px;">' + result.text + ' (' + result.code + ')</div>' +
                    '</td>';
            } else {
                tr += '<td style="width: 100%;border:0 !important;padding:0 !important">' +
                    '<div class="bold" style="font-size: 14px;">' + result.text + ' (' + result.code + ')</div>' +
                    '</td>';
            }
            // tr+= '<td style="width: 15%;">'+result.name_color+'</td>';
            // tr+= '<td style="width: 15%;">'+result.mode+'</td>';
            // tr+= '<td style="width: 15%;">'+result.mt+'</td>';
            // tr+= '<td style="width: 10%;" class="text-center">'+result.qty_warehouse+'</td>';
        }
        tableSelect = '<table class="tnh-table-bottom dont-responsive-table">' + '<thead>' + tr + '</thead>' + '</table>';
        return tableSelect;
    }
    $(function() {
        $('[name="months-report"], [name="report-from"],[name="report-to"]').change(function() {
            var warehouse_id = $("#warehouse_id option:selected").text();
            var report_months = $('[name="months-report"]').val();
            var report_from = $('[name="report-from"]').val();
            var report_to = $('[name="report-to"]').val();
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
                data['report_months'] = report_months;
                data['report_from'] = report_from;
                data['report_to'] = report_to;
            }
            var date_text = '';
            $.post(admin_url + 'warehouse/GetDate/', data).done(function(response) {
                date_text = response;
                //insert
                $('#date_export_ch').val(date_text);

            });
        });
    });
</script>