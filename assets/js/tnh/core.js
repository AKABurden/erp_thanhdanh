function get_tnh_datatable_buttons(e) {
    var n = {
            body: function(e, t, a, n) {
                var i = $("<div></div>", e);
                if (
                    (i.append(e),
                        i.find("[data-note-edit-textarea]").length > 0 &&
                        (i.find("[data-note-edit-textarea]").remove(),
                            (e = i.html().trim())),
                        i.find(".row-options").length > 0 &&
                        (i.find(".row-options").remove(),
                            (e = i.html().trim())),
                        i.find(".table-export-exclude").length > 0 &&
                        (i.find(".table-export-exclude").remove(),
                            (e = i.html().trim())),
                        e)
                ) {
                    var o = new RegExp(
                        "([0-9]{1,3})(,)([0-9]{" +
                        app.options.decimal_places +
                        "," +
                        app.options.decimal_places +
                        "})",
                        "gm"
                    );
                    e.matchAll(o) && (e = e.replace(o, "$1.$3"));
                }
                var r = document.createElement("div");
                return (
                    (r.innerHTML = e),
                    (r.textContent || r.innerText || "").trim()
                );
            },
        },
        o = [];

    // "function" == typeof table_export_button_is_hidden && table_export_button_is_hidden() || o.push({
    //     extend: "collection",
    //     text: app.lang.dt_button_export,
    //     className: "btn btn-default-dt-options",
    //     buttons: [{
    //         extend: "excel",
    //         text: app.lang.dt_button_excel,
    //         footer: !0,
    //         exportOptions: {
    //             columns: [":not(.not-export)"], rows: function (t) {
    //                 return _dt_maybe_export_only_selected_rows(t, e)
    //             }, format: n
    //         }
    //     }, {
    //         extend: "csvHtml5",
    //         text: app.lang.dt_button_csv,
    //         footer: !0,
    //         exportOptions: {
    //             columns: [":not(.not-export)"], rows: function (t) {
    //                 return _dt_maybe_export_only_selected_rows(t, e)
    //             }, format: n
    //         }
    //     }, {
    //         extend: "pdfHtml5",
    //         text: app.lang.dt_button_pdf,
    //         footer: !0,
    //         exportOptions: {
    //             columns: [":not(.not-export)"], rows: function (t) {
    //                 return _dt_maybe_export_only_selected_rows(t, e)
    //             }, format: n
    //         },
    //         orientation: "landscape",
    //         customize: function (t) {
    //             var a = $(e).DataTable().columns().visible(), n = a.length, o = [], r = 0;
    //             for (i = 0; i < n; i++) 1 == a[i] && r++;
    //             setTimeout(function () {
    //                 if (r <= 5) {
    //                     for (i = 0; i < r; i++) o.push(735 / r);
    //                     t.content[1].table.widths = o
    //                 }
    //             }, 10), "persian" != app.user_language.toLowerCase() && "arabic" != app.user_language.toLowerCase() || (t.defaultStyle.font = Object.keys(pdfMake.fonts)[0]), t.styles.tableHeader.alignment = "left", t.styles.tableHeader.margin = [5, 5, 5, 5], t.pageMargins = [12, 12, 12, 12]
    //         }
    //     }, {
    //         extend: "print",
    //         text: app.lang.dt_button_print,
    //         footer: !0,
    //         exportOptions: {
    //             columns: [":not(.not-export)"], rows: function (t) {
    //                 return _dt_maybe_export_only_selected_rows(t, e)
    //             }, format: n
    //         }
    //     }]
    // });

    var r = $("body").find(".table-btn");
    return (
        $.each(r, function() {
            var t = $(this);
            t.length &&
                t.attr("data-table") &&
                $(e).is(t.attr("data-table")) &&
                o.push({
                    text: t.text().trim(),
                    className: "btn btn-default-dt-options",
                    action: function(e, a, n, i) {
                        t.click();
                    },
                });
        }),
        $(e).hasClass("dt-inline") ||
        o.push({
            text: '<i class="fa fa-refresh"></i>',
            className: "btn btn-default-dt-options btn-dt-reload",
            action: function(e, t, a, n) {
                t.ajax.reload();
            },
        }),
        o
    );
}

function initTnhDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder) {
    var table = typeof(selector) == 'string' ? $("body").find('table' + selector) : selector;

    if (table.length === 0) {
        return false;
    }

    fnserverparams = (fnserverparams == 'undefined' || typeof(fnserverparams) == 'undefined') ? [] : fnserverparams;

    // If not order is passed order by the first column
    if (typeof(defaultorder) == 'undefined') {
        defaultorder = [
            [0, 'asc']
        ];
    } else {
        if (defaultorder.length === 1) {
            defaultorder = [defaultorder];
        }
    }

    var user_table_default_order = table.attr('data-default-order');

    if (!empty(user_table_default_order)) {
        var tmp_new_default_order = JSON.parse(user_table_default_order);
        var new_defaultorder = [];
        for (var i in tmp_new_default_order) {
            // If the order index do not exists will throw errors
            if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
                new_defaultorder.push(tmp_new_default_order[i]);
            }
        }
        if (new_defaultorder.length > 0) {
            defaultorder = new_defaultorder;
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

    if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
        length_options.push(app.options.tables_pagination_limit);
        length_options_names.push(app.options.tables_pagination_limit);
    }

    length_options.sort(function(a, b) {
        return a - b;
    });
    length_options_names.sort(function(a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);

    var dtSettings = {
        "language": app.lang.datatables,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 750,
        "bDeferRender": true,
        // "responsive": true,
        "autoWidth": false,
        dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>",
        "pageLength": app.options.tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnDrawCallback": function(oSettings) {
            _table_jump_to_page(this, oSettings);
            if (oSettings.aoData.length === 0) {
                $(oSettings.nTableWrapper).addClass('app_dt_empty');
            } else {
                $(oSettings.nTableWrapper).removeClass('app_dt_empty');
            }
        },
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "initComplete": function(settings, json) {
            var t = this;
            var $btnReload = $('.btn-dt-reload');
            $btnReload.attr('data-toggle', 'tooltip');
            $btnReload.attr('title', app.lang.dt_button_reload);

            var $btnColVis = $('.dt-column-visibility');
            $btnColVis.attr('data-toggle', 'tooltip');
            $btnColVis.attr('title', app.lang.dt_button_column_visibility);

            if (t.hasClass('scroll-responsive') || app.options.scroll_responsive_tables == 1) {
                t.wrap('<div class="table-responsive"></div>');
            }

            var dtEmpty = t.find('.dataTables_empty');
            if (dtEmpty.length) {
                dtEmpty.attr('colspan', t.find('thead th').length);
            }

            // Hide mass selection because causing issue on small devices
            if (is_mobile() && $(window).width() < 400 && t.find('tbody td:first-child input[type="checkbox"]').length > 0) {
                t.DataTable().column(0).visible(false, false).columns.adjust();
                $("a[data-target*='bulk_actions']").addClass('hide');
            }

            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            var th_last_child = t.find('thead th:last-child');
            var th_first_child = t.find('thead th:first-child');
            if (th_last_child.text().trim() == app.lang.options) {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
        },
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            }
        },
        buttons: get_datatable_buttons(table),
    };

    if (table.hasClass('scroll-responsive') || app.options.scroll_responsive_tables == 1) {
        dtSettings.responsive = false;
    }

    table = table.dataTable(dtSettings);
    var tableApi = table.DataTable();

    var hiddenHeadings = table.find('th.not_visible');
    var hiddenIndexes = [];

    $.each(hiddenHeadings, function() {
        hiddenIndexes.push(this.cellIndex);
    });

    setTimeout(function() {
        for (var i in hiddenIndexes) {
            tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
        }
    }, 10);

    if (table.hasClass('customizable-table')) {

        var tableToggleAbleHeadings = table.find('th.toggleable');
        var invisible = $('#hidden-columns-' + table.attr('id'));
        try {
            invisible = JSON.parse(invisible.text());
        } catch (err) {
            invisible = [];
        }

        $.each(tableToggleAbleHeadings, function() {
            var cID = $(this).attr('id');
            if ($.inArray(cID, invisible) > -1) {
                tableApi.column('#' + cID).visible(false);
            }
        });
    }

    // Fix for hidden tables colspan not correct if the table is empty
    if (table.is(':hidden')) {
        table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
    }

    table.on('preXhr.dt', function(e, settings, data) {
        if (settings.jqXHR) settings.jqXHR.abort();
    });

    return tableApi;
}

function setResize()
{
    height_page = $('body').height();
    height_header = $('#header').height();
    height_title = 0;
    if ($('.status-table').height() > 0) {
        height_status_table = $('.status-table').height() + 25 + 25;
    } else {
        height_status_table = 0;
    }
    if ($('.minus-height-more')) {
        height_minus_more = 25;
    }

    if ($('#H_scroll').height() > 0) {
        height_title = $('#H_scroll').height();
    }

    height_search = 0;
    if ($('.div-search').height() > 0) {
        height_search = $('.div-search').height();
    }

    height_tabs = 0;
    if ($('.status-table')) {
        height_tabs = 70;
    }

    // height_page+= 100;
    height_body = (height_page - height_header - height_title - height_status_table - height_minus_more - 150) +'px';
    // console.log(height_body);
    var width_document = $(document).width();
    // console.log(width_document);
    if(Number(width_document) < 768){
        height_body = '400px';
    }
    // height_body = '400px';
    $('.dataTables_scrollBody').css('height', height_body);
}

$(window).resize(function(){
    setResize();
});
setResize();

$(document).on('click', '.tnh-modal', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    $.ajax({
    	url: link,
    	type: 'GET',
    	dataType: 'html',
    	data: {
    		token: hash
    	},
    })
    .done(function(data) {
    	$('#tnhModal').html(data);
    })
    .fail(function() {
    	console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal').modal({backdrop: 'static', keyboard: true});
});

function showModalCustom(c_link, c_view_modal, c_keyboard = true, data_post) {
    $.ajax({
    	url: c_link,
    	type: 'POST',
    	dataType: 'html',
    	data: data_post,
    })
    .done(function(data) {
    	$(c_view_modal).html(data);
    })
    .fail(function() {
    	console.log("error");
    });
    $(c_view_modal).modal({backdrop: 'static', keyboard: c_keyboard});
}

$(document).on('click', '.tnh-modal2', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash
        },
    })
    .done(function(data) {
        $('.modal-select2').select2('close');
        $('#tnhModal2').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal2').modal({backdrop: 'static', keyboard: true});
});


// $(document).on('hide.bs.modal', '.modal', function () {
// 	// tinyMCE.remove();
//     console.log(123);
//     $('.modal-select2').select2('close');
// });

// $(document).on('hidden.bs.modal', '.modal', function () {
//     console.log(456);
// });

$(document).on('click', '.close', function(event) {
    event.preventDefault();
    $('.modal-select2').select2('close');
});

function loadAjax()
{
    $(document).on({
        ajaxStart: function() { $("#loader").removeClass('hide'); },
        ajaxStop: function() { $("#loader").addClass('hide'); }
    });
}

function filterCustom(element_search, table, filters)
{
    $.each(filters, function(index, el) {
        title = el.label;
        element = el.element;
        if (typeof el.label == 'undefined')
        {
            title = $(element).text();
        }
        type = el.type;
        if (type == 'text')
        {
            $(element).html( '<input type="text"  placeholder="'+title+'" class="column_search form-control" style="width: 100%;" />' );
        }
    });
    $(element_search).on( 'keyup', ".column_search",function () {
        table.column( $(this).parent().index() ).search( this.value ).draw();
    });
}

// function reload

// function tnhDatatable(selector, initParams)
// {
//     initParams.cache = false;
//     initParams.pageLength = intVal(app.options.tables_pagination_limit);
//     oTableCustom = $(selector).DataTable(initParams);
//     reLoadDatatable();

//     // setTimeout(function(){ oTableCustom.draw(); }, 1000);
//     return oTableCustom;
// }

function tnhDatatable(selector, initParams, btnButtons = 0) {
    var e = selector;

    table = $(selector);
    if (table.length === 0) {
        return false;
    }

    var n = "undefined";
    var s = [];
    var o = "string" == typeof e ? $("body").find("table" + e) : e;
    // if (0 === o.length) return !1;
    (n = "undefined" == n || void 0 === n ? [] : n),
    void 0 === s ? (s = [
        [0, "asc"]
    ]) : 1 === s.length && (s = [s]);
    var l = o.attr("data-default-order");
    if (!empty(l)) {
        var d = JSON.parse(l),
            r = [];
        for (var c in d)
            o.find("thead th:eq(" + d[c][0] + ")").length > 0 && r.push(d[c]);
        r.length > 0 && (s = r);
    }

    initParams.cache = false;
    initParams.pageLength = intVal(app.options.tables_pagination_limit);

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    app.options.tables_pagination_limit = parseFloat(
        app.options.tables_pagination_limit
    );

    if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
        length_options.push(app.options.tables_pagination_limit);
        length_options_names.push(app.options.tables_pagination_limit);
    }

    length_options.sort(function(a, b) {
        return a - b;
    });
    length_options_names.sort(function(a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);
    initParams.lengthMenu = [length_options, length_options_names];

    // initParams.dom =
    //     "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>";
    // if (typeof initParams.buttons !== "undefined" && initParams.buttons) {
    //     initParams.buttons.unshift({
    //         text: '<i class="fa fa-refresh"></i>',
    //         action: function ( e, dt, node, config ) {
    //             dt.ajax.reload();
    //         }
    //     });
    // } else {
    //     initParams.buttons = get_tnh_datatable_buttons(o);
    // }

    console.log(table);
    initParams.dom = "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>";
    initParams.buttons = get_datatable_buttons(table);
    if (btnButtons == 1) {
        var buttonTwo = [{
            extend: "excel",
            text: app.lang.dt_button_excel,
            footer: !0,
            exportOptions: {
                columns: [":not(.not-export)"],
                rows: function (t) {
                    return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                },
                format: {
                    header: function ( data, columnIdx ) {
                        var _data = `<p>${data}</p>`;
                        return $(_data).text().toUpperCase();
                    },
                    body: function(data, row, column, node) {
                        data = $('<p>' + data + '</p>').text();
                        if(column == 4){
                            let trimmedText = data.trim();
                            let noWhiteSpaceText = trimmedText.replace(/\s+/g, " ");
                            let noCommaText = noWhiteSpaceText.replace(/,/g, "");
                            return noCommaText;
                        }
                        else {
                            // return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                            return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data;
                        }
                    },
                    footer: function ( data, columnIdx ) {
                        data = $('<p>' + data + '</p>').text();
                        return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data.toUpperCase();
                        // return data.toUpperCase();
                    },
                }
            },
            customize: function (xlsx) {
                var footers = $('row:last-child', sheet); // Giả định dòng cuối cùng là footer
    
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                var mergeCells = $('mergeCells', sheet);
    
                footers.each(function() {
                    var cols = $(this).find('c');
                    var prevColspan = 0;
    
                    cols.each(function(index) {
                        var cell = $(this);
                        var cellAddress = cell.attr('r');
                        var colIndex = cellAddress.replace(/[0-9]/g, ''); // Lấy chỉ số cột từ địa chỉ ô
    
                        // Bỏ qua các ô bị trùng do colspan
                        if (prevColspan > 0) {
                            prevColspan--;
                            cell.remove();
                            return;
                        }
    
                        // Kiểm tra và thiết lập colspan
                        var colspan = 1; // Đặt giá trị colspan của ô tại đây nếu có
                        if (colspan > 1) {
                            // Thay đổi giá trị này theo colspan thực tế
                            prevColspan = colspan - 1;
    
                            // Mã xử lý để kết hợp các ô nếu cần thiết
                            var newCellAddress = colIndex + (index + 1); // Điều chỉnh theo nhu cầu của bạn
                            cell.attr('r', newCellAddress);
                            cell.attr('s', '0'); // Đặt style ID theo nhu cầu của bạn
                            // Bạn có thể cần cập nhật nội dung của ô hoặc giá trị thuộc tính khác
                        }
                    });
                });
    
                var columsExcel = [
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                    'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                    'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
                    'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
                    'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
                ];
    
    
                var downrows = 0;
                var code = '';
                var list_name = '';
                var titleExcel = $(table).attr('title_excel');
                if(titleExcel) {
                    titleExcel = JSON.parse(titleExcel);
                    code = typeof titleExcel[0] != 'undefined' ? titleExcel[0] : '';
                    list_name = typeof titleExcel[1] != 'undefined' ? titleExcel[1] : '';
    
                }
                // console.log(list_name);
                // code = $('.code_data').text();
                // name = $('.name_data').text();
    
                var downrows = 2;
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: {
                        ref: 'A1:G1'
                    }
                }));
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: {
                        ref: 'A2:G2'
                    }
                }));
                if($.isArray(list_name)) {
                    var isStt = 0;
                    $.each(list_name, function(index, value) {
                        mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            attr: {
                                ref: columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3'
                            }
                        }));
                        console.log(columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3')
                        isStt = isStt + 2;
                    })
                }
                else {
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A3:G3'
                        }
                    }));
                }
    
    
                var clRow = $('row', sheet);
    
                function _createNode(doc, nodeName, opts) {
                    var tempNode = doc.createElement(nodeName);
    
                    if (opts) {
                        if (opts.attr) {
                            $(tempNode).attr(opts.attr);
                        }
    
                        if (opts.children) {
                            $.each(opts.children, function (key, value) {
                                tempNode.appendChild(value);
                            });
                        }
    
                        if (opts.text !== null && opts.text !== undefined) {
                            tempNode.appendChild(doc.createTextNode(opts.text));
                        }
                    }
    
                    return tempNode;
                }
    
                //update Row
                clRow.each(function () {
                    var attr = $(this).attr('r');
                    var ind = parseInt(attr);
                    ind = ind + downrows;
                    $(this).attr("r", ind);
                });
    
                // Update  row > c
                var maxRow = 0;//lấy số lượng max của cột để kẻ khung
                $('row c ', sheet).each(function () {
                    var attr = $(this).attr('r');
                    var pre = attr.substring(0, 1);
                    var ind = parseInt(attr.substring(1, attr.length));
                    ind = ind + downrows;
                    if($.inArray(pre, columsExcel) > maxRow) {
                        maxRow = $.inArray(pre, columsExcel);
                    }
    
                    $(this).attr("r", pre + ind);
                    if(ind > 3) {
                        $(this).attr("s", '25');
                    }
                    else if(ind == 3) {
                        $(this).attr("s", '22');
                    }
                    if(ind == 4) {
                        $(this).attr("s", '27');
                    }
                });
                maxRow += 1; // vì mảng bắt đầu từ 0 đến + 1 để đi theo sheet của excel
    
                $('row', sheet).each(function (index, value) {
                    if(index >= 2) {
                        var indexRow = $(value).attr('r');
                        for(var i = 0; i < maxRow; i++) {
                            if($($(sheet).find('row')[index]).find(`c[r="${columsExcel[i]}${indexRow}"]`).length == 0) { //kiểm tra cột nào chưa có để bổ sung
                                if(i == 0) {// là cột đầu tiên
                                    $($(sheet).find('row')[index]).prepend(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                }
                                else {// là các cột tiếp theo.. thêm vào để kẻ khung table
                                    $($(sheet).find('row')[index]).find(`c[r="${columsExcel[i - 1]}${indexRow}"]`).after(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                }
                            }
                        }
                    }
                })
                function Addrow(index, data) {
                    msg = '<row r="' + index + '">'
                    for (i = 0; i < data.length; i++) {
                        var key = data[i].k;
                        var value = data[i].v;
                        msg += '<c t="inlineStr" r="' + key + index + '" s="' + (index == 1 ? '51' : '2') + '">';
                        msg += '<is>';
                        msg += '    <t>' + value + '</t>';
                        msg += '</is>';
                        msg += '</c>';
                    }
                    msg += '</row>';
                    return msg;
                }
                
                var r3 = Addrow(1, [{ k: 'A', v: escapeHTML($('head title').text()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                var r1 = code ? Addrow(2, [{ k: 'A', v: escapeHTML(code.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]) : '';
                var r2 = ''
                if($.isArray(list_name)) {
                    var isStt = 0;
                    var listRow = [];
                    $.each(list_name, function(index, value) {
                        listRow.push({ k: columsExcel[isStt], v: (value.toUpperCase()) });
                        isStt = isStt + 2;
                    })
                    r2 = Addrow(3, listRow);
                }
                else {
                    r2 = Addrow(3, [{ k: 'A', v: escapeHTML(list_name.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                }
                
                sheet.childNodes[0].childNodes[1].innerHTML = r3 + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                $('row', sheet).each(function (index, value) {
                    // console.log(value);
                })
            }
        }];
        var buttonOne = get_datatable_buttons(table);
        delete buttonOne[0];
        buttonOne.push(buttonTwo);

        initParams.buttons = buttonOne;
    }

    oTableCustom = $(selector).DataTable(initParams);
    // reLoadDatatable();

    // setTimeout(function(){ oTableCustom.draw(); }, 1000);
    return oTableCustom;
}

function reLoadDatatable()
{
    $('div.reload').remove();
    $("div.dataTables_length").after('<div class="dt-buttons btn-group reload"><button class="btn btn-default btn-default-dt-options btn-dt-reload" tabindex="0" aria-controls="table-leads" type="button" data-toggle="tooltip" title="" data-original-title="Tải lại"><span><i class="fa fa-refresh"></i></span></button></div>');

    setTimeout(function(){
        width_table = $('.dataTables_scrollBody tbody').width();
        // if (width_table > 100)
        // {
        //     $('.dataTables_scrollHeadInner table').css('width', (width_table + 1)+'px');
        //     $('.dataTables_scrollFootInner table').css('width', (width_table + 1)+'px');
        // }
        // oTableCustom.draw();
    }, 1000);
}

function reloadDataTableFullScreen() {
    $("div.dataTables_length").after('<div class="btn-group fullscreen"><button class="btn btn-default btn-default-dt-options" tabindex="0" aria-controls="table-leads" type="button" data-toggle="tooltip" title="" data-original-title=""><span><i class="fa fa-expand text-primary"></i></span></button></div>');
}

$(document).on('click', '.fullscreen', function(event) {
    event.preventDefault();
});

//popover
row_popover = '';
$(document).on('click', '.po', function() {
    row_popover = $(this).closest('div');
    // $(this).popover('show');
});

$(document).on('click', '.po-close', function() {
    // $('.po').popover('hide');
    row_popover.find('.po').trigger('click');
    return false;
});

$(document).on('click', '.po-delete', function() {
    row_popover = $(this).closest('div');
});

$(document).on('click', '.po-close-new', function() {
    // $(this).closest('.popover').popover('hide');
    row_popover.find('.po-delete').trigger('click');
    return false;
});

$(document).on('click', '.po-custom', function(e) {
    e.preventDefault();
    // $('.po-custom').popover({html: true, placement: 'auto', trigger: 'manual'}).popover('show').not(this).popover('hide');
    $('.po-custom').popover({html: true, placement: 'left', trigger: 'manual'}).popover('show').not(this).popover('hide');;
    return false;
});
$(document).on('click', '.po-close-custom', function() {
    $('.po-custom').popover('hide');
    return false;
});

$(document).on('click', '.po-delete-json', function(e) {
    var row = $(this).closest('tr');
    e.preventDefault();
    $('.po').popover('hide');
    $('.po-delete').popover('hide');
    var link = $(this).attr('href');
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'JSON',
        data: {
            param1: 'value1'
        },
    })
    .done(function(data) {
        if (data)
        {
            if (data.result == 1) {
                if (typeof data.table != 'undefined')
                {
                    if (data.type == "BOM") {
                        $('table[data-bom="'+data.table+'"]').remove();
                    } else if (data.type == 'stages') {
                        $('table[data-stages="'+data.table+'"]').remove();
                    } else if (data.type = 'type') {
                        if (typeof dtSuggest != 'undefined') {
                            dtSuggest.draw('page');
                        }
                        if (typeof oTable != 'undefined') {
                            oTable.draw('page');
                        }
                    }
                } else {
                    if (typeof oTable != 'undefined') {
                        oTable.draw('page');
                    }
                }
                alert_float('success', data.message);
            } else {
                alert_float('danger', data.message);
            }
        }
    })
    .fail(function() {
        alert_float('danger', 'fail');
    })
    return false;
});

$(document).on('click', '.delete-confirm-json', function(event) {
    event.preventDefault();
    var row = $(this).closest('tr');
    var link = $(this).attr('href');
    bootbox.confirm({
        message: lang_core['you_want_remove'],
        buttons: {
            confirm: {
                label: lang_core['yes'],
                className: 'btn-success'
            },
            cancel: {
                label: lang_core['no'],
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    url: link,
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        delete: true
                    },
                })
                .done(function(data) {
                    if (data)
                    {
                        if (data.result == 1) {
                            if (typeof oTable != 'undefined') {
                                oTable.draw('page');
                            }
                            alert_float('success', data.message);
                        } else {
                            alert_float('danger', data.message);
                        }
                        if (typeof data.errors != "undefined" && data.errors) {
                            $('.show-alert').show();
                            $('.show-errors').html(data.errors);
                        }
                    }
                })
                .fail(function() {
                    alert_float('danger', 'fail');
                })
            }
        }
    });
    return false;
});

$(document).on('click', '.po-delete-multiple-json', function(e) {
    var row = $(this).closest('tr');
    e.preventDefault();
    $('.po').popover('hide');
    var data = $('form').serialize();
    var link = $(this).attr('href');
    $.ajax({
        url: link,
        type: 'POST',
        dataType: 'JSON',
        data: data
    })
    .done(function(data) {
        if (data)
        {
            if (data.result == 1) {
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                alert_float('success', data.message);
            } else {
                alert_float('danger', data.message);
            }
            if (typeof data.errors != "undefined" && data.errors) {
                $('.show-alert').show();
                $('.show-errors').html(data.errors);
            }
        }
    })
    .fail(function() {
        alert_float('danger', 'fail');
    })
    return false;
});


function selectAjax(selector, server_data, link, change_link = false, attrs = false)
{
    var ajaxSelector = $('body').find(selector);
    if (ajaxSelector.length) {
        var options = {
            ajax: {
                url: site.base_url+link,
                type: "GET",
                data: function() {
                    var data = {};
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    data.token = hash;
                    if (typeof(server_data) != 'undefined') {
                        jQuery.extend(data, server_data);
                    }
                    return data;
                }
            },
            locale: {
                emptyTitle: app.lang.search_ajax_empty,
                statusInitialized: app.lang.search_ajax_initialized,
                statusSearching: app.lang.search_ajax_searching,
                statusNoResults: app.lang.not_results_found,
                searchPlaceholder: app.lang.search_ajax_placeholder,
                currentlySelected: app.lang.currently_selected
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function(processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': processData[i].id,
                        'text': processData[i].name,
                    };
                    if (attrs == 'products') {
                        tmp_data.data = {
                            subtext: processData[i].product_name,
                            image: processData[i].images,
                            unit_name_manu: processData[i]?.unit_name_manu,
                        };
                    } else if (attrs == true) {
                        tmp_data.data = {
                            subtext: processData[i].subtext,
                        };
                    }
                    // if (processData[i].subtext) {
                    //     tmp_data.data = { subtext: processData[i].subtext };
                    // }
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'before',
            preserveSelected: true
        };
        if (ajaxSelector.data('empty-title')) {
            options.locale.emptyTitle = ajaxSelector.data('empty-title');
        }
        ajaxSelector.selectpicker().ajaxSelectPicker(options);
        if (change_link) {
            ajaxSelector.data('AjaxBootstrapSelect').options.ajax.data = function() {
                var data = {};
                data.rel_id = '';
                data.q = '{{{q}}}';
                data.token = hash;
                if (typeof(server_data) != 'undefined') {
                    jQuery.extend(data, server_data);
                }
                return data;
            };
            ajaxSelector.data('AjaxBootstrapSelect').options.ajax.url = change_link;
        }
    }
}



function formatNumberCus(nStr, decSeperate, groupSeperate) {
    //decSeperate= ki tu cach,groupSeperate= ki tu noi
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    // console.log(x1);
    return x1 + x2;
}

function formatNumBerKeyUpCus(id_input)
{
    // key = "";
    // money = $(id_input).val().replace(/[^\-\d\.]/g, '');
    // a=money.split(".");
    // $.each(a , function (index, value){
    //     key=key+value;
    // });
    // $(id_input).val(formatNumberCus(money, '.', ','));

    vl = $(id_input).val().replace(/[^\-\d\.]/g, '');
    vl = vl.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    $(id_input).val(vl)
}

function formatNumberTnh(nStr, decSeperate=".", groupSeperate=",") {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    x2=x2.substr(0,2);
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
};

$(document).ready(function() {
    $(document).ready(function() {
        $(document).on('click', '.dropdown-menu .not-outside', function(event) {
            event.stopPropagation();
        });
    });
});

$(document).ready(function() {
    $(document).on('changed.bs.select', 'select.unit_exchange', function (e, clickedIndex, isSelected, previousValue) {
        lastrow = $('.table-exchange tbody tr')[$('.table-exchange tbody tr').length - 1];
        if ($(lastrow).find('select.unit_exchange').val() > 0) {
            $('.table-exchange thead tr th .btn-add-items').trigger('click');
        }
    });
});


function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', text);
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function optionCloumnExcel(cloumn_excel, selected_cloumn = 0)
{
    option = '<option></option>';
    $.each(cloumn_excel, function(index, el) {
        selected = selected_cloumn == el ? 'selected' : '';
        option+= '<option '+selected+' value="'+el+'">'+el+'</option>';
    });
    return option;
}

function optionFields(fields, selected_field = 0)
{
    option = '<option></option>';
    $.each(fields, function(index, el) {
        selected = selected_field == index ? 'selected' : '';
        option+= '<option '+selected+' value="'+index+'">'+el+'</option>';
    });
    return option;
}

function textErrors(text)
{
    return '<div class="text-danger">'+text+'</div>';
}

function bs_input_file() {
    $(".input-file").before(
        function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
                var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
                element.attr("name",$(this).attr("name"));
                element.change(function(){
                    element.next(element).find('input').val((element.val()).split('\\').pop());
                });
                $(this).find("button.btn-choose").click(function(){
                    element.click();
                });
                $(this).find("button.btn-reset").click(function(){
                    element.val(null);
                    $(this).parents(".input-file").find('input').val('');
                });
                $(this).find('input').css("cursor","pointer");
                $(this).find('input').mousedown(function() {
                    $(this).parents('.input-file').prev().click();
                    return false;
                });
                return element;
            }
        }
    );
}

function bs_input_file_multiple() {
    $(".input-file-multiple").before(
        function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
                var element = $("<input type='file' class='input-ghost' multiple style='visibility:hidden; height:0'>");
                element.attr("name", $(this).attr("name"));

                element.change(function(){
                    var files = $("input[name='files[]']")[0].files;
                    var countFiles = files.length;
                    element.next(element).find('input').val(countFiles+' files');
                });
                $(this).find("button.btn-choose").click(function(){
                    element.click();
                });
                $(this).find("button.btn-reset").click(function(){
                    element.val(null);
                    $(this).parents(".input-file-multiple").find('input').val('');
                });
                $(this).find('input').css("cursor","pointer");
                $(this).find('input').mousedown(function() {
                    $(this).parents('.input-file-multiple').prev().click();
                    return false;
                });
                return element;
            }
        }
    );
}

function fld(oObj) {
    if (typeof oObj != 'undefined' && oObj != null) {
        var aDate = oObj.split('-');
        var bDate = aDate[2].split(' ');
        year = aDate[0], month = aDate[1], day = bDate[0], time = bDate[1];
        return day + "/" + month + "/" + year + " " + time;
        // if (site.dateFormats.js_sdate == 'dd-mm-yyyy')
        //     return day + "-" + month + "-" + year + " " + time;
        // else if (site.dateFormats.js_sdate === 'dd/mm/yyyy')
        //     return day + "/" + month + "/" + year + " " + time;
        // else if (site.dateFormats.js_sdate == 'dd.mm.yyyy')
        //     return day + "." + month + "." + year + " " + time;
        // else if (site.dateFormats.js_sdate == 'mm/dd/yyyy')
        //     return month + "/" + day + "/" + year + " " + time;
        // else if (site.dateFormats.js_sdate == 'mm-dd-yyyy')
        //     return month + "-" + day + "-" + year + " " + time;
        // else if (site.dateFormats.js_sdate == 'mm.dd.yyyy')
        //     return month + "." + day + "." + year + " " + time;
        // else
        //     return oObj;
    } else {
        return '';
    }
}


function fsd(oObj) {
    if (typeof oObj != 'undefined' && oObj != null) {
        var aDate = oObj.split('-');
        return aDate[2] + "/" + aDate[1] + "/" + aDate[0];
        // if (site.dateFormats.js_sdate == 'dd-mm-yyyy')
        //     return aDate[2] + "-" + aDate[1] + "-" + aDate[0];
        // else if (site.dateFormats.js_sdate === 'dd/mm/yyyy')
        //     return aDate[2] + "/" + aDate[1] + "/" + aDate[0];
        // else if (site.dateFormats.js_sdate == 'dd.mm.yyyy')
        //     return aDate[2] + "." + aDate[1] + "." + aDate[0];
        // else if (site.dateFormats.js_sdate == 'mm/dd/yyyy')
        //     return aDate[1] + "/" + aDate[2] + "/" + aDate[0];
        // else if (site.dateFormats.js_sdate == 'mm-dd-yyyy')
        //     return aDate[1] + "-" + aDate[2] + "-" + aDate[0];
        // else if (site.dateFormats.js_sdate == 'mm.dd.yyyy')
        //     return aDate[1] + "." + aDate[2] + "." + aDate[0];
        // else
        //     return oObj;
    } else {
        return '';
    }
}

$(document).ready(function() {
    $('.dateranger').daterangepicker({
        // "locale": {
        //     lang_daterangepicker
        // }
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
            // "daysOfWeek": [
            //     "Su",
            //     "Mo",
            //     "Tu",
            //     "We",
            //     "Th",
            //     "Fr",
            //     "Sa"
            // ],
            // "monthNames": [
            //     "January",
            //     "February",
            //     "March",
            //     "April",
            //     "May",
            //     "June",
            //     "July",
            //     "August",
            //     "September",
            //     "October",
            //     "November",
            //     "December"
            // ],
        }
    });

    $('.dateranger-custom').daterangepicker({
        opens: 'left',
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
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Week': [moment().startOf('isoWeek'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "startDate": moment().subtract(29, 'days'),
        "endDate": moment()
    });
});

$(document).ready(function() {
    $('table').addClass('dont-responsive-table');
});

function formatSA (x) {
    x=x.toString();
    var afterPoint = '';
    if(x.indexOf('.') > 0)
       afterPoint = x.substring(x.indexOf('.'),x.length);
    x = Math.floor(x);
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;

    return res;
}

function tnhFormatZero(x, d = 0) {
    return accounting.formatNumber(x, d, site.thousands_sep == 0 ? ' ' : site.thousands_sep, site.decimals_sep);
}

function tnhFormatNumber(x, d = null) {
    // if(!d) { d = site.decimals_number; }
    if(d === null) { d = site.decimals_number; }
    x = x * 1;
    if (x % 1 == 0) {
        d = 0;
    }
    return accounting.formatNumber(x, d, site.thousands_sep == 0 ? ' ' : site.thousands_sep, site.decimals_sep);
}

function tnhFormatMoney(x, d = 0) {
    if(!d) { d = site.decimals_money; }
    x = x * 1;
    if (x % 1 == 0) {
        d = 0;
    }
    return accounting.formatNumber(x, d, site.thousands_sep == 0 ? ' ' : site.thousands_sep, site.decimals_sep);
}

function tnhFormatMoneySymbol(x, symbol) {
    if(!symbol) { symbol = ""; }
    if(site.sac == 1) {
        return symbol+''+formatSA(parseFloat(x).toFixed(site.decimals_money));
    }
    return accounting.formatMoney(x, symbol, site.decimals, site.thousands_sep == 0 ? ' ' : site.thousands_sep, site.decimals_sep, "%s%v");
}

function arraysEqual(arr1, arr2) {
    if(arr1.length !== arr2.length)
        return false;
    for(var i = arr1.length; i--;) {
        if(arr1[i] !== arr2[i])
            return false;
    }

    return true;
}

function removeArray(data, removeItem) {
    data = jQuery.grep(data, function(value) {
        return value != removeItem;
    });
    return data;
}

function openFullscreen(elem) {
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) { /* Firefox */
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { /* IE/Edge */
        elem.msRequestFullscreen();
    }
}

/* Close fullscreen */
function closeFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) { /* Firefox */
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE/Edge */
        document.msExitFullscreen();
    }
}

function ajaxSelectCallBack(element, url, id, types = '')
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        types: types,
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        types: types,
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
            }
        });
    }
}

function ajaxSelectParams(element, url, id, params = false, clearSl2 = false)
{
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                        if (data.row) {
                            if (data.row.id === 0) {
                                $(element).val(0);
                            }
                        }
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
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
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
            }
        });
    }
}

function ajaxSelectParamsCallback(element, url, id, params = false, clearSl2 = false, txtJson = false)
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            initSelection: function (element, callback) {
                if (txtJson) {
                    callback(txtJson);
                } else {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.row);
                        }
                    });
                }

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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
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
            }
        });
    }
}

function ajaxSelectMultipleParams(element, url, id, params = false)
{
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            multiple: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
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
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            multiple: true,
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
            }
        });
    }
}

function formatCustomer(result)
{
    if (!result.id) return result.text; // optgroup
    tr = '';
    if (result) {
        tr+= '<td style="width: 33%;">'+result.text+'</td>';
        tr+= '<td style="width: 33%;">'+result.fullname+'</td>';
        tr+= '<td style="width: 33%;">'+result.phonenumber+'</td>';
    }
    tableSelect = '<table class="tnh-table table-bordered dont-responsive-table">'+
                        '<tbody>'+
                            tr
                        '</tbody>'+
                    '</table>';
    return tableSelect;
}

function ajaxSelectCustomerFormatTableCallBack(element, url, id)
{
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            escapeMarkup: function(m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatCustomer,
            // formatSelection: formatTable,
            escapeMarkup: function(m) {
                return m;
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    }
}

function ajaxSelectFormatTableCallBack(element, url, id)
{
    if (id > 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatTable,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatTable,
            // formatSelection: formatTable,
            escapeMarkup: function(m) {
                return m;
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    }
}

function ajaxSelectParentCallBack(element, url, id)
{
    if (id > 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatParent,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatParent,
            // formatSelection: formatTable,
            escapeMarkup: function(m) {
                return m;
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
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
            }
        });
    }
}

function ajaxSelectMultipleCallBack(element, url, id, types = '')
{
    if (id > 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        types: types,
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            multiple: true,
            //allowClear: true,
            width: 'resolve',
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        types: types,
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
            }
        });
    }
}

$(document).ready(function() {
    $('.tnh-select').select2();

    $(document).on('change', '.tnh-select', function(event) {
        event.preventDefault();
        if ($(this).val()) {
            $('#departments-error').html('');
        }
    });
    $(document).on('change', '#customers', function(event) {
        event.preventDefault();
        if ($(this).val()) {
            $('#customers-error').html('');
        }
    });

    $(document).on('change', '#address_delivery', function(event) {
        event.preventDefault();
        if ($(this).val()) {
            $('#address_delivery-error').html('');
        }
    });
    $(document).on('click', '.number-format', function(event) {
        event.preventDefault();
        // $(this).select();
        // formatNumBerKeyUpCus(this);
    });
    $(document).on('click', '.money-format', function(event) {
        event.preventDefault();
        // $(this).select();
    });

    $(document).on('change', '.number-format', function(event) {
        event.preventDefault();
        // $(this).select();
        formatNumBerKeyUpCus(this);
    });
    $(document).on('change', '.money-format', function(event) {
        event.preventDefault();
        // $(this).select();
        formatNumBerKeyUpCus(this);
    });

    formatNumberPlugin();
    formatMoneyPlugin();
});

function formatNumberPlugin() {
    // $('.number-format').number(true, site.decimals_number, site.decimals_sep, site.thousands_sep);
}

function formatMoneyPlugin() {
    // $('.money-format').number(true, site.decimals_money, site.decimals_sep, site.thousands_sep);
}

$("body").on("change", "#mass_select_all", function() {
    var e, t, a;
    e = $(this).data("to-table"), t = $(".table-" + e).find("tbody tr"), a = $(this).prop("checked"), $.each(t, function() {
        $($(this).find("td").eq(1)).find("input").prop("checked", a)
    })
});


function nowDate()
{
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10){
        dd = '0'+dd;
    }
    if (mm < 10){
        mm = '0'+mm;
    }
    today = dd+'/'+mm+'/'+yyyy;
    return today;
}

function addDate(date, days, format, date_time = false) {
    var newdate = new Date(date);
    newdate.setDate(newdate.getDate() + days);
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    if (date_time) {
        h = newdate.getHours();
        m = newdate.getMinutes();
    }
    if (mm < 10) {
        mm = '0'+mm;
    }
    if (dd < 10) {
        dd = '0'+dd;
    }
    var y = newdate.getFullYear();
    if (date_time) {
        if (format == "dd/mm/yyyy") {
            someFormattedDate = dd + '/' + mm + '/' + y +' '+ h +':'+ m;
        } else {
            someFormattedDate = mm + '/' + dd + '/' + y +' '+ h +':'+ m;
        }
    } else {
        if (format == "dd/mm/yyyy") {
            someFormattedDate = dd + '/' + mm + '/' + y;
        } else {
            someFormattedDate = mm + '/' + dd + '/' + y;
        }
    }
    return someFormattedDate;
}

function formatDate(oObj, format, format_convert){
    if (oObj != null) {
        var aDate = oObj.split('/');
        if (format == "dd/mm/yyyy") {
            if (format_convert == "mm-dd-yyyy") {
                return aDate[1] + "-" + aDate[0] + "-" + aDate[2];
            } else if (format_convert == "yyyy-mm-dd") {
                return aDate[2] + "-" + aDate[1] + "-" + aDate[0];
            }
        } else if (format == "mm-dd-yyyy") {
            if (format_convert == "dd/mm/yyyy") {
                return aDate[1] + "/" + aDate[0] + "/" + aDate[2];
            }
        }
    } else {
        return '';
    }
    return oObj;
}

function minusTwoDate(dateStartCal, dateEndCal)
{
    if (!dateStartCal || !dateEndCal) {
        return 0;
    }
    var dateStartCal = new Date(dateStartCal);
    var dateEndCal = new Date(dateEndCal);
    var diffTime = Math.abs(dateEndCal.getTime() - dateStartCal.getTime());
    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}


$(document).on('click', '.modal', function(event) {
    // event.preventDefault();
    $('.modal-select2').select2('close');
});

$(document).ready(function() {
    app.options.tables_pagination_limit = intVal(app.options.tables_pagination_limit);
});

function loadNotificationCustom(el)
{
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $('.notifications-custom ul').hide();
    $.ajax({
        url: admin_url+'dashboard/loadNotificationCustom',
        type: 'GET',
        dataType: 'html',
        data: data,
    })
    .done(function(response) {
        $('.notifications-custom').html(response);
    })
    .fail(function() {
        console.log("error");
    })
}

// $('li#notifications-custom').click(function(event) {
//     loadNotificationCustom(this);
// });

// $(document).on('click', 'li#notifications-custom', function(event) {
//     loadNotificationCustom(this);
// });

function agreeNotification(el, type, id)
{
    bootbox.confirm({
        message: lang_core['you_want_agree'],
        buttons: {
            confirm: {
                label: lang_core['yes'],
                className: 'btn-success'
            },
            cancel: {
                label: lang_core['no'],
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                var link = '';
                var rs = [];
                var status = ''
                if (type == "quotes") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/quotes/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            quote_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "orders") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/orders/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            order_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "business_plan") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/business_plan/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            business_plan_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "productions_plan") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/manufactures/agreeProductionsPlan',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            productions_plan_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "productions_capacity") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/manufactures/agreeProductionsCapacity',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            productions_capacity_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "productions_orders") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/manufactures/agreeProductionsOrders',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            productions_orders_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "list_suggest_exporting") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/manufactures/agreeSuggestExporting',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            suggest_exporting_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "exporting_producion") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/stock/agreeStock',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            suggest_exporting_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "purchase_internal") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/stock/agreePurchaseInternal',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            purchase_internal_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                } else if (type == "returned_goods") {
                    status = "approved";
                    $.ajax({
                        url: site.base_url+'admin/returned_goods/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            csrf_token_name: hash,
                            returned_goods_id: id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                }
            }
        }
    });
}

$(document).on('click', '.add-flash-customer', function(event) {
    event.preventDefault();
    link = site.base_url+'admin/clients/addFlashCustomer';
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash
        },
    })
    .done(function(data) {
        $('#tnhModal').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    $('#tnhModal').modal({backdrop: 'static', keyboard: true});
});

function clickCloseClassify(_this)
{
    $(_this).closest('.notifi-classify').remove();
    $('.notifi-wrap-classify-container').css('bottom', '-100%');
}

// $(document).on('click','.notifi-close-classify', function (e) {
//     $(this).closest('.notifi-classify').remove();
// });

$(document).on('click', '.tnh-modal-attr', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    arrParams = [];
    $(this).each(function() {
        $.each(this.attributes, function() {
            mName = this.name; 
            mValue = this.value;
            // arrParams[mName] = mValue
            arrParams.push({mName:mName, mValue:mValue });
        });
    });
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash,
            arrParams: arrParams
        },
    })
    .done(function(data) {
        $('#tnhModal').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.tnh-modal-attr2', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    arrParams = [];
    $(this).each(function() {
        $.each(this.attributes, function() {
            mName = this.name; 
            mValue = this.value;
            // arrParams[mName] = mValue
            arrParams.push({mName:mName, mValue:mValue });
        });
    });
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash,
            arrParams: arrParams
        },
    })
    .done(function(data) {
        $('#tnhModal2').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal2').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.tnh-modal-attr3', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    arrParams = [];
    $(this).each(function() {
        $.each(this.attributes, function() {
            mName = this.name; 
            mValue = this.value;
            // arrParams[mName] = mValue
            arrParams.push({mName:mName, mValue:mValue });
        });
    });
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash,
            arrParams: arrParams
        },
    })
    .done(function(data) {
        $('#tnhModal3').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal3').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.tnh-modal3', function(event) {
    event.preventDefault();
    this.blur();
    link = this.href;
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        data: {
            token: hash
        },
    })
    .done(function(data) {
        $('.modal-select2').select2('close');
        $('#tnhModal3').html(data);
    })
    .fail(function() {
        console.log("error");
    });
    // $('#tnhModal').modal('show');
    $('#tnhModal2').modal({backdrop: 'static', keyboard: true});
});

function formatPOPlan(result)
{
    if (!result.id) return result.text; // optgroup
    txtPodPlan = '<div class="bold">'+result.text+'</div>';
    if (typeof result.company !== "undefined" && result.company) {
        txtPodPlan+= '<div class="italic" style="font-size: 11px;">Khách hàng: '+result.company+'</div>';
    }
    if (typeof result.items !== "undefined" && result.items.length > 0) {
        $.each(result.items, function (index, value) { 
            txtPodPlan+= `<div style="font-size: 11px;">${value.item_name}(${value.item_code})</div>`;
        });
    }
    return txtPodPlan;
}

function ajaxSelectFormatPOPlanMultipleCallBack(element, url, id, params = false, clearSl2 = false)
{
    if (id > 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            multiple: true,
            formatResult: formatPOPlan,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results);
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
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatPOPlan,
            multiple: true,
            // formatSelection: formatTable,
            escapeMarkup: function(m) {
                return m;
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
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    }
}

function formatNumberFixed(x, d = 0) {
    if(!d) { d = site.decimals_money; }
    return x.toFixed(d);
}

function dataTableLengthMenu() {
    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

    if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
        length_options.push(app.options.tables_pagination_limit);
        length_options_names.push(app.options.tables_pagination_limit);
    }

    length_options.sort(function(a, b) {
        return a - b;
    });
    length_options_names.sort(function(a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);

    return [length_options, length_options_names];
}

function csFixme(element) {
    var fixmeTop = $(element).offset().top;
    $(window).scroll(function() {
        var currentScroll = $(window).scrollTop();
        if (currentScroll >= fixmeTop) {
            $(element).css({
                position: 'fixed',
                top: '0',
                // left: '0',
                'z-index': '9999'
            });
        } else {
            $(element).css({
                position: 'static'
            });
        }
    });
}

function tnhInitDataTable(selector, url, initParams, notsearchable = [0], notsortable = [0], fnserverparams = [], defaultorder = [0], fixedColumns = {leftColumns: 0, rightColumns: 0}, btnButton = 0) {
    // var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;
    table = $(selector);
    if (table.length === 0) {
        return false;
    }


    fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

    // If not order is passed order by the first column
    if (typeof (defaultorder) == 'undefined') {
        defaultorder = [
            [0, 'asc']
        ];
    } else {
        if (defaultorder.length === 1) {
            defaultorder = [defaultorder];
        }
    }

    var user_table_default_order = table.attr('data-default-order');

    if (!empty(user_table_default_order)) {
        var tmp_new_default_order = JSON.parse(user_table_default_order);
        var new_defaultorder = [];
        for (var i in tmp_new_default_order) {
            // If the order index do not exists will throw errors
            if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
                new_defaultorder.push(tmp_new_default_order[i]);
            }
        }
        if (new_defaultorder.length > 0) {
            defaultorder = new_defaultorder;
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

    if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
        length_options.push(app.options.tables_pagination_limit);
        length_options_names.push(app.options.tables_pagination_limit);
    }

    length_options.sort(function (a, b) {
        return a - b;
    });
    length_options_names.sort(function (a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);
    var width_document = $(document).width();
    if (Number(width_document) <= 768) {
        fixedColumns.leftColumns = 0;
        fixedColumns.rightColumns = 0;
    }
    var dtSettings = {
        "language": app.lang.datatables,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 750,
        "bDeferRender": true,
        // scrollY: '400px',
        // scrollX: true,
        // fixedColumns: {
        //     leftColumns: fixedColumns.leftColumns,
        //     rightColumns: fixedColumns.rightColumns
        // },
        // "responsive": true,
        "autoWidth": false,
        dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
        "pageLength": app.options.tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnDrawCallback": function (oSettings) {
            _table_jump_to_page(this, oSettings);
            if (oSettings.aoData.length === 0) {
                $(oSettings.nTableWrapper).addClass('app_dt_empty');
            } else {
                $(oSettings.nTableWrapper).removeClass('app_dt_empty');
            }
        },
        "fnCreatedRow": function (nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "initComplete": function (settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            mainWrapperHeightFix();
        },
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function (d) {
                if (typeof (csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            }
        },
        buttons: get_datatable_buttons(table),
    };

    if (table.hasClass('scroll-responsive') || app.options.scroll_responsive_tables == 1) {
        dtSettings.responsive = false;
    }

    //tnh custom
    if (initParams) {
        if (typeof initParams.order !== 'undefined') {
            dtSettings.order = initParams.order;
        }
        if (typeof initParams.ajax !== 'undefined') {
            dtSettings.ajax = initParams.ajax;
        }

        if (typeof initParams.sAjaxSource !== 'undefined') {
            dtSettings.sAjaxSource = initParams.sAjaxSource;
        }

        if (typeof initParams.fnServerData !== 'undefined') {
            dtSettings.fnServerData = initParams.fnServerData;
        }

        if (typeof initParams.columnDefs !== 'undefined') {
            dtSettings.columnDefs = initParams.columnDefs;
        }

        if (typeof initParams.fixedHeader !== 'undefined') {
            dtSettings.fixedHeader = initParams.fixedHeader;
        }

        if (typeof initParams.responsive !== 'undefined') {
            dtSettings.responsive = initParams.responsive;
        }

        if (typeof initParams.searching !== 'undefined') {
            dtSettings.searching = initParams.searching;
        }

        if (typeof initParams.ordering !== 'undefined') {
            dtSettings.ordering = initParams.ordering;
        }

        if (typeof initParams.fixedColumns !== 'undefined') {
            dtSettings.fixedColumns = initParams.fixedColumns;
        }

        if (typeof initParams.scrollY !== 'undefined') {
            dtSettings.scrollY = initParams.scrollY;
        }

        if (typeof initParams.scrollX !== 'undefined') {
            dtSettings.scrollX = initParams.scrollX;
        }

        if (typeof initParams.createdRow !== 'undefined') {
            dtSettings.fnCreatedRow = initParams.createdRow;
        }

        if (typeof initParams.dom !== 'undefined') {
            dtSettings.dom = initParams.dom;
        }

        if (typeof initParams.paging !== 'undefined') {
            dtSettings.paging = initParams.paging;
        }

        if (typeof initParams.info !== 'undefined') {
            dtSettings.info = initParams.info;
        }

        if (typeof initParams.fnRowCallback !== 'undefined') {
            dtSettings.fnRowCallback = initParams.fnRowCallback;
        }

        if (typeof initParams.initComplete !== 'undefined') {
            dtSettings.initComplete = initParams.initComplete;
        }

        if (typeof initParams.buttons !== 'undefined') {
            dtSettings.buttons = initParams.buttons;
        }

        if (typeof initParams.btnButtons !== 'undefined') {
            var buttonTwo = [{
                extend: "excel",
                text: app.lang.dt_button_excel,
                footer: !0,
                exportOptions: {
                    columns: [":not(.not-export)"],
                    rows: function (t) {
                        return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                    },
                    format: {
                        header: function ( data, columnIdx ) {
                            var _data = `<p>${data}</p>`;
                            return $(_data).text().toUpperCase();
                        },
                        body: function(data, row, column, node) {
                            data = $('<p>' + data + '</p>').text();
                            if(column == 4){
                                let trimmedText = data.trim();
                                let noWhiteSpaceText = trimmedText.replace(/\s+/g, " ");
                                let noCommaText = noWhiteSpaceText.replace(/,/g, "");
                                return noCommaText;
                            }
                            else{
                                // return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                                return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data;
                            }
                        },
                        footer: function ( data, columnIdx ) {
                            data = $('<p>' + data + '</p>').text();
                            return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data.toUpperCase();
                            // return data.toUpperCase();
                        },
                    }
                },
                customize: function (xlsx) {
                    var footers = $('row:last-child', sheet); // Giả định dòng cuối cùng là footer
        
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var mergeCells = $('mergeCells', sheet);
        
                    footers.each(function() {
                        var cols = $(this).find('c');
                        var prevColspan = 0;
        
                        cols.each(function(index) {
                            var cell = $(this);
                            var cellAddress = cell.attr('r');
                            var colIndex = cellAddress.replace(/[0-9]/g, ''); // Lấy chỉ số cột từ địa chỉ ô
        
                            // Bỏ qua các ô bị trùng do colspan
                            if (prevColspan > 0) {
                                prevColspan--;
                                cell.remove();
                                return;
                            }
        
                            // Kiểm tra và thiết lập colspan
                            var colspan = 1; // Đặt giá trị colspan của ô tại đây nếu có
                            if (colspan > 1) {
                                // Thay đổi giá trị này theo colspan thực tế
                                prevColspan = colspan - 1;
        
                                // Mã xử lý để kết hợp các ô nếu cần thiết
                                var newCellAddress = colIndex + (index + 1); // Điều chỉnh theo nhu cầu của bạn
                                cell.attr('r', newCellAddress);
                                cell.attr('s', '0'); // Đặt style ID theo nhu cầu của bạn
                                // Bạn có thể cần cập nhật nội dung của ô hoặc giá trị thuộc tính khác
                            }
                        });
                    });
        
                    var columsExcel = [
                        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                        'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
                        'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
                        'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
                    ];
        
        
                    var downrows = 0;
                    var code = '';
                    var list_name = '';
                    var titleExcel = $(table).attr('title_excel');
                    if(titleExcel) {
                        titleExcel = JSON.parse(titleExcel);
                        code = typeof titleExcel[0] != 'undefined' ? titleExcel[0] : '';
                        list_name = typeof titleExcel[1] != 'undefined' ? titleExcel[1] : '';
        
                    }
                    // console.log(list_name);
                    // code = $('.code_data').text();
                    // name = $('.name_data').text();
        
                    var downrows = 2;
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A1:G1'
                        }
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A2:G2'
                        }
                    }));
                    if($.isArray(list_name)) {
                        var isStt = 0;
                        $.each(list_name, function(index, value) {
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3'
                                }
                            }));
                            console.log(columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3')
                            isStt = isStt + 2;
                        })
                    }
                    else {
                        mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            attr: {
                                ref: 'A3:G3'
                            }
                        }));
                    }
        
        
                    var clRow = $('row', sheet);
        
                    function _createNode(doc, nodeName, opts) {
                        var tempNode = doc.createElement(nodeName);
        
                        if (opts) {
                            if (opts.attr) {
                                $(tempNode).attr(opts.attr);
                            }
        
                            if (opts.children) {
                                $.each(opts.children, function (key, value) {
                                    tempNode.appendChild(value);
                                });
                            }
        
                            if (opts.text !== null && opts.text !== undefined) {
                                tempNode.appendChild(doc.createTextNode(opts.text));
                            }
                        }
        
                        return tempNode;
                    }
        
                    //update Row
                    clRow.each(function () {
                        var attr = $(this).attr('r');
                        var ind = parseInt(attr);
                        ind = ind + downrows;
                        $(this).attr("r", ind);
                    });
        
                    // Update  row > c
                    var maxRow = 0;//lấy số lượng max của cột để kẻ khung
                    $('row c ', sheet).each(function () {
                        var attr = $(this).attr('r');
                        var pre = attr.substring(0, 1);
                        var ind = parseInt(attr.substring(1, attr.length));
                        ind = ind + downrows;
                        if($.inArray(pre, columsExcel) > maxRow) {
                            maxRow = $.inArray(pre, columsExcel);
                        }
        
                        $(this).attr("r", pre + ind);
                        if(ind > 3) {
                            $(this).attr("s", '25');
                        }
                        else if(ind == 3) {
                            $(this).attr("s", '22');
                        }
                        if(ind == 4) {
                            $(this).attr("s", '27');
                        }
                    });
                    maxRow += 1; // vì mảng bắt đầu từ 0 đến + 1 để đi theo sheet của excel
        
                    $('row', sheet).each(function (index, value) {
                        if(index >= 2) {
                            var indexRow = $(value).attr('r');
                            for(var i = 0; i < maxRow; i++) {
                                if($($(sheet).find('row')[index]).find(`c[r="${columsExcel[i]}${indexRow}"]`).length == 0) { //kiểm tra cột nào chưa có để bổ sung
                                    if(i == 0) {// là cột đầu tiên
                                        $($(sheet).find('row')[index]).prepend(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                    }
                                    else {// là các cột tiếp theo.. thêm vào để kẻ khung table
                                        $($(sheet).find('row')[index]).find(`c[r="${columsExcel[i - 1]}${indexRow}"]`).after(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                    }
                                }
                            }
                        }
                    })
                    function Addrow(index, data) {
                        msg = '<row r="' + index + '">'
                        for (i = 0; i < data.length; i++) {
                            var key = data[i].k;
                            var value = data[i].v;
                            msg += '<c t="inlineStr" r="' + key + index + '" s="' + (index == 1 ? '51' : '2') + '">';
                            msg += '<is>';
                            msg += '    <t>' + value + '</t>';
                            msg += '</is>';
                            msg += '</c>';
                        }
                        msg += '</row>';
                        return msg;
                    }
                    
                    var r3 = Addrow(1, [{ k: 'A', v: escapeHTML($('head title').text()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                    var r1 = code ? Addrow(2, [{ k: 'A', v: escapeHTML(code.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]) : '';
                    var r2 = ''
                    if($.isArray(list_name)) {
                        var isStt = 0;
                        var listRow = [];
                        $.each(list_name, function(index, value) {
                            listRow.push({ k: columsExcel[isStt], v: (value.toUpperCase()) });
                            isStt = isStt + 2;
                        })
                        r2 = Addrow(3, listRow);
                    }
                    else {
                        r2 = Addrow(3, [{ k: 'A', v: escapeHTML(list_name.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                    }
                    
                    sheet.childNodes[0].childNodes[1].innerHTML = r3 + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                    $('row', sheet).each(function (index, value) {
                        // console.log(value);
                    })
                }
            }];
            var buttonOne = get_datatable_buttons(table);
            delete buttonOne[0];
            buttonOne.push(buttonTwo);
            dtSettings.buttons = buttonOne;
        }
        // console.log(dtSettings);
    }
    
    table = table.dataTable(dtSettings);
    var tableApi = table.DataTable();

    var hiddenHeadings = table.find('th.not_visible');
    var hiddenIndexes = [];

    $.each(hiddenHeadings, function () {
        hiddenIndexes.push(this.cellIndex);
    });

    setTimeout(function () {
        for (var i in hiddenIndexes) {
            tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
        }
    }, 10);

    if (table.hasClass('customizable-table')) {

        var tableToggleAbleHeadings = table.find('th.toggleable');
        var invisible = $('#hidden-columns-' + table.attr('id'));
        try {
            invisible = JSON.parse(invisible.text());
        } catch (err) {
            invisible = [];
        }

        $.each(tableToggleAbleHeadings, function () {
            var cID = $(this).attr('id');
            if ($.inArray(cID, invisible) > -1) {
                tableApi.column('#' + cID).visible(false);
            }
        });
    }

    // Fix for hidden tables colspan not correct if the table is empty
    if (table.is(':hidden')) {
        table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
    }

    table.on('preXhr.dt', function (e, settings, data) {
        if (settings.jqXHR) settings.jqXHR.abort();
    });

    if (typeof initParams.btnButtons !== 'undefined') {
        table.on('draw.dt', function(e, settings, data) {
            var paymentReceivedReportsTable = $(this).DataTable();
            var title_excel = paymentReceivedReportsTable.ajax.json().title_excel;
            if(title_excel) {
                $(this).attr('title_excel', JSON.stringify(title_excel));
            }
        });
    }

    return tableApi;
}

function animationHeight(winY) {
    $('body').animate({
        scrollTop: winY
    }, 'slow');
}

function animationElement(el, pxMore) {
    setTimeout(() => {
        $('html, body').animate({
            scrollTop: $(el).offset().top + pxMore
        }, 'slow');
    }, 1500);
}

function formatDecimal(number)
{
    number = intVal(number);
    return +(number).toFixed(12);
}

function formatDecimalToFixed(number, d)
{
    number = intVal(number);
    return +(number).toFixed(d);
}

function fixedModal(element, top = false,height_scroll = 200,height_scroll_modal = 320) {
    $('.modal').on('scroll', function() {
        var sticky = $(element+' > thead'),
            scroll = $(window).scrollTop();
        var scrollModal = $('#tnhModal').scrollTop();
        if (scrollModal >= (scroll + height_scroll)) {
            sticky.addClass('fixed-cs');
            if (top == true) {
                sticky.css({
                    top: 0,
                });
            } else {
                sticky.css({
                    top: (scrollModal - height_scroll_modal),
                });
            }
        } else {
            sticky.removeClass('fixed');
        }
    });
}

function dateRangerCustom(el, startDate = '', endDate = '') {
    $(el).daterangepicker({
        // opens: 'left',
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
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Week': [moment().startOf('isoWeek'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "startDate": startDate ? startDate : moment().subtract(29, 'days'),
        "endDate": endDate ? endDate : moment()
    });
}

function tnhToFixedNumber(x, d = false) {
    if(d === false) { d = site.decimals_number; }
    if (x % 1 == 0) {
        d = 0;
    }
    return x.toFixed(d) * 1;
}

function workingDaysBetweenDates(d0, d1, holidays = 1) {
    var startDate = parseDate(d1);
    var endDate = parseDate(d0);  
    // Validate input
    minus = '';
    if (endDate < startDate) {
        minus = '-';
        startDate = parseDate(d0);
        endDate = parseDate(d1);
    }
    // return 0;

    // Calculate days between dates
    var millisecondsPerDay = 86400 * 1000; // Day in milliseconds
    startDate.setHours(0,0,0,1);  // Start just after midnight
    endDate.setHours(23,59,59,999);  // End just before midnight
    var diff = endDate - startDate;  // Milliseconds between datetime objects    
    var days = Math.ceil(diff / millisecondsPerDay);

    if (holidays == 1) {
        // Thứ bảy, Chủ nhật
        // Subtract two weekend days for every week in between
        var weeks = Math.floor(days / 7);
        days = days - (weeks * 2);

        // Handle special cases
        var startDay = startDate.getDay();
        var endDay = endDate.getDay();

        // Remove weekend not previously removed.   
        if (startDay - endDay > 1)         
            days = days - 2;      

        // Remove start day if span starts on Sunday but ends before Saturday
        if (startDay == 0 && endDay != 6)
            days = days - 1  

        // Remove end day if span ends on Saturday but starts after Sunday
        if (endDay == 6 && startDay != 0)
            days = days - 1  
    } else if (holidays == 11) {
        //only sundays
        var weeks = Math.floor(days / 7);
        days = days - (weeks * 1);

        var startDay = startDate.getDay();
        var endDay = endDate.getDay();

        // Remove weekend not previously removed.   
        if (startDay - endDay > 1)         
            days = days - 1;      

        // Remove start day if span starts on Sunday but ends before Saturday
        if (startDay == 0 && endDay != 6)
            days = days - 1  

        // Remove end day if span ends on Saturday but starts after Sunday
        if (endDay == 6 && startDay != 0)
            days = days - 1  
    }

    return minus+days;
}

function parseDate(input) {
    // Transform date from text to date
  var parts = input.match(/(\d+)/g);
  // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
  return new Date(parts[0], parts[1]-1, parts[2]); // months are 0-based
}

function escapeHTML(text) {
    return text.replace(/&/g, "&amp;");
}

function selectAjaxV2(selector, server_data, link, change_link = false, attrs = false, is_load_data = false, container = '', _preserveSelected = true)
{
    var ajaxSelector = $('body').find(selector);
    if (ajaxSelector.length) {
        if (is_load_data) {
            var loadData = function() {
                // Gọi hàm Ajax để lấy dữ liệu từ server
                $.ajax({
                    url: site.base_url + link,
                    type: "GET",
                    data: {
                        q: '',
                        rel_id: '',
                        token: hash,
                        ...(typeof(server_data) != 'undefined' ? server_data : {})
                    },
                    dataType: "json",
                    success: function (data) {
                        valueSelected = (typeof(server_data.valueSelected) != 'undefined' ? server_data.valueSelected : [])
                        var items = data.filter((item) => {
                            if (valueSelected.includes(item.id) == false){
                                return item;
                            }
                        });
                         var itemsNew = items.map((item) => {
                            return {
                                value: item.id,
                                text: item.name,
                                'data-subtext' : typeof item.subtext !== 'undefined' ? item.subtext : '',
                                'data-quantity' : typeof item.data_quantity !== 'undefined' ? item.data_quantity : '',
                                'data-staff_sale' : typeof item.staff_sale !== 'undefined' ? item.staff_sale : '',
                                'data-_pco' : typeof item._pco !== 'undefined' ? item._pco : '',
                            };
                        });

                        // console.log(items.map(item => $('<option>', item)));
                        ajaxSelector.append(itemsNew.map(item => $('<option>', item)));
                        ajaxSelector.selectpicker('refresh');
                    },
                    error: function (error) {
                        console.error("Đã xảy ra lỗi khi tải dữ liệu:", error);
                    }
                });
            };
            loadData();
        }

        var options = {
            ajax: {
                url: site.base_url+link,
                type: "GET",
                data: function() {
                    var data = {};
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    data.token = hash;
                    if (typeof(server_data) != 'undefined') {
                        jQuery.extend(data, server_data);
                    }
                    return data;
                },
            },
            // emptyRequest: true,
            // minimumInputLength: 10,
            // log: 3,
            locale: {
                emptyTitle: app.lang.search_ajax_empty,
                statusInitialized: app.lang.search_ajax_initialized,
                statusSearching: app.lang.search_ajax_searching,
                statusNoResults: app.lang.not_results_found,
                searchPlaceholder: app.lang.search_ajax_placeholder,
                currentlySelected: app.lang.currently_selected
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function(processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': processData[i].id,
                        'text': processData[i].name,
                    };

                    if (attrs == 'products') {
                        tmp_data.data = {
                            subtext: processData[i].product_name,
                            image: processData[i].images,
                        };
                    } else if (attrs == true) {
                        tmp_data.data = {
                            subtext: processData[i].subtext,
                        };
                    }

                    if (typeof processData[i].subtext !== 'undefined' && processData[i].subtext) {
                        tmp_data.data = { subtext: processData[i].subtext};
                    }

                    if (typeof contentHtml !== 'undefined' && contentHtml) {
                        tmp_data.data = { content: contentHtml };
                    }

                    if (processData[i]?.data_quantity) {
                        if (tmp_data?.data) {
                            tmp_data.data.quantity = processData[i]?.data_quantity;
                        } else {
                            tmp_data.data = {
                                quantity: processData[i]?.data_quantity
                            };
                        }
                    }

                    if (processData[i]?.staff_sale) {
                        if (tmp_data?.data) {
                            tmp_data.data.staff_sale = processData[i]?.staff_sale;
                        } else {
                            tmp_data.data = {
                                staff_sale: processData[i]?.staff_sale
                            };
                        }
                    }

                    if (processData[i]?._pco) {
                        if (tmp_data?.data) {
                            tmp_data.data._pco = processData[i]?._pco;
                        } else {
                            tmp_data.data = {
                                _pco: processData[i]?._pco
                            };
                        }
                    }
                    
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'before',
            // clearOnEmpty: true,
            preserveSelected: _preserveSelected
        };
        if (ajaxSelector.data('empty-title')) {
            options.locale.emptyTitle = ajaxSelector.data('empty-title');
        }
        
        ajaxSelector.selectpicker({container: container}).ajaxSelectPicker(options);
        if (change_link) {
            ajaxSelector.data('AjaxBootstrapSelect').options.ajax.data = function() {
                var data = {};
                data.rel_id = '';
                data.q = '{{{q}}}';
                data.token = hash;
                if (typeof(server_data) != 'undefined') {
                    jQuery.extend(data, server_data);
                }
                return data;
            };
            ajaxSelector.data('AjaxBootstrapSelect').options.ajax.url = change_link;
        }
    }
}