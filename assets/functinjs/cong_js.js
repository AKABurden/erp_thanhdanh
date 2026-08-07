//Delete data
$('table').on('click', '._deleteRow', function(e){
    if(confirm(app.lang.confirm_action_prompt))
    {
        if(confirm(app.lang.comfim_delete_all_list))
        {
            $('.alert_typeTbable').html('');
            var button = $(this);
            button.button({loadingText: 'please wait...'});
            button.button('loading');
            var table = $(this).parents('table.dataTable');
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post($(this).attr('href'), data, function(result){
                result = JSON.parse(result);
                if(result.success)
                {
                    table.DataTable().ajax.reload();
                }
                else
                {
                    if(result.ktConnect)
                    {
                        $.each(result.ktConnect, function(i, v){
                            $('.alert_typeTbable').append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+v.message+' : '+v.data+'</div>');
                        })
                    }
                }
                alert_float(result.alert_type, result.message);
                return false;
            }).always(function() {
                button.button('reset')
            });
        }
    }
    return false;
})


function DeleteList(ThisTable, href)
{
    if(confirm(app.lang.confirm_action_prompt)) {
        if(confirm(app.lang.comfim_delete_all_list)) {
            $('.alert_typeTbable').html('');
            var Table = $(ThisTable);
            var MassSelect = Table.find('tbody').find('td:nth-child(1)').find('input[type="checkbox"]:checked');
            var ListID = [];
            $.each(MassSelect, function (i, v) {
                ListID.push($(v).val());
            })
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['listData'] = ListID;
            $.post(admin_url + href, data, function (data) {
                data = JSON.parse(data);
                if (data.success) {
                    $(ThisTable).DataTable().ajax.reload();
                }
                if (data.ktConnect) {
                    $.each(data.ktConnect, function (i, v) {
                        var StringTab = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + v.code;
                        $.each(v.data, function (ii, vv) {
                            StringTab += '<p>' + vv.message + ' : ' + vv.data + '</p>';

                        })
                        StringTab += '</div>';
                        $('.alert_typeTbable').append(StringTab);

                    })
                }
                alert_float(data.alert_type, data.message);
            })
        }
    }
}

function Backloader()
{
    $(document).on({
        ajaxStart: function() {
            $('.dataTables_processing').remove(),$("#loader").removeClass('hide');
        },
        ajaxStop: function() { $("#loader").addClass('hide'); }
    });
}

function repoFormatSelection(state) {
    if (!state.id) return state.text;
    if(state.img)
    {
        var img = '<img class="img_option" src="'+site_url +state.img+'"/> ';
    }
    else
    {
        var img = '<img class="img_option" src="'+site_url +'download/preview_image"/> ';
    }
    var Stringreturn = img + state.text;
    if(state.price)
    {
        Stringreturn += ' - '+ C_formatNumber(state.price)
    }

    return  Stringreturn ;
}

$(document).on('click', '.c_modal', function(e) {
    var url = $(this).attr('href');
    $.get(url, function(result) {
        $('.modal-backdrop.in').remove();
        $('#cong_modal').html(result);
    }).error(function (response) {
        alert_float('danger', response.responseText);
    });
    return false;
})

$(document).on('click', '.c_check_url', function(e) {
    var url = $(this).attr('href');
    var url2 = $(this).attr('href2');
    if(confirm('Bạn có chắc chắn thực hiện không?')) {
        $.get(url, function (result) {
            result = JSON.parse(result);
            if (result.success) {
                if (result.id && url2) {
                    url2 = url2 + result.id;
                    $.ajax({
                        url: url2,
                        type: 'GET',
                        success: function (response) {
                            $('#tnhModal').html(response);
                            $('#tnhModal').modal('show');

                            if (typeof tableEprofile !== 'undefined' && tableEprofile) {
                                tableEprofile.ajax.reload();
                            }
                            if (typeof oTable !== 'undefined' && oTable) {
                                oTable.ajax.reload();
                            }
                        },
                        error: function () {
                            alert_float('danger', 'Có lỗi khi tải chi tiết');
                        }
                    });
                }
            }
            alert_float(result.alert_type, result.message);
        }).error(function (response) {
            alert_float('danger', response.responseText);
        });
    }
    return false;
})


function ajaxSelectParamsGet(element, url, id, params = false, clearSl2 = false, dataGet = {}, multiple = false)
{
    if (id)
    {
        list_id = $(element).val();
        if(multiple == true) {
            list_id = list_id.replace(/,/g, '-');
        }
        $(element).val(id).select2({
            // minimumInputLength: 1,
            multiple: multiple,
            width: 'resolve',
            allowClear: clearSl2,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + list_id,
                    dataType: "json",
                    data: dataGet,
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
                    var dataReturn = {
                        params: params,
                        term: term,
                        limit: 50
                    };
                    if(dataGet) {
                        $.each(dataGet, function(index, value) {
                            dataReturn[index] = value;
                        })
                    }
                    return dataReturn;
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
            multiple: multiple,
            width: 'resolve',
            allowClear: clearSl2,
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    var dataReturn = {
                        params: params,
                        term: term,
                        limit: 50
                    };
                    if(dataGet) {
                        $.each(dataGet, function(index, value) {
                            dataReturn[index] = value;
                        })
                    }
                    return dataReturn;
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

$('body').on('click', 'a.c_more', function(e) {
    var data_more = $(this).attr('text-data');
    var data_remore = $(this).parents('span').find('t').html();
    var data_remore2 = $(this).parents('span').find('t').text();
    $(this).parents('span').html('<t>' + data_more + '</t> <a class="c_remore" text-data="' + data_remore+ '"> Thu gọn</a>');
});

$('body').on('click', 'a.c_remore', function(e) {
    var data_remore = $(this).attr('text-data');
    var data_more = $(this).parents('span').find('t').html();
    $(this).parents('span').html('<t>' +data_remore + '</t> <a class="c_more" text-data="' + data_more+ '"> Xem thêm</a>');
});