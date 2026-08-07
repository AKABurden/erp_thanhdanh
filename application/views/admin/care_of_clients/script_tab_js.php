<script>
    $(function(){
        $('a[href="#tabs_manage_care_of_client"]').parent('li').addClass('active');
        initDataTable('.table-care_of_clients', admin_url+'care_of_clients/table_tab<?=!empty($userid)? '/'.$userid : ''?>', [0], [0], {}, [1, 'desc']);
    });

    $('.table-care_of_clients').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var data = invoiceReportsTable.ajax.json().count_detail;
        $('td').find('.progressbar').find('li').css('width',100/data+'%');
        $('.progressbar').parent('td').css('white-space', 'inherit');
    })

    function editCare_of_clients(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        if($.isNumeric(id))
        {
            data['id'] = id;
        }
        $.post(admin_url+'care_of_clients/getModal', data, function(data){
            $('#modal_care_of_clients').html(data);
            $('#modal_care_of_clients').modal('show');
        }).always(function() {
            button.button('reset');
        });
    }

    function deleteCare_of_clients(id = "", _this) {
        if($.isNumeric(id))
        {
            if(confirm("<?=_l('cong_you_must_delete')?>"))
            {
                var button = $(_this);
                button.button({loadingText: '<?=_l('cong_please_wait')?>'});
                button.button('loading');
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['id'] = id;
                $.post(admin_url+'care_of_clients/delete_care_of_clients', data, function(data){
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success)
                    {
                        $('.table-care_of_clients').DataTable().ajax.reload();
                    }
                }).always(function() {
                    button.button('reset');
                });
            }
        }
    }
    $('body').on('click', '.update_status_client', function(e){
        var id_assigned  = $(this).attr('id-data');
        var status_procedure  = $(this).attr('status-procedure');
        var data = {};
        var button = $(this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id_assigned;
        data['status_procedure'] = status_procedure;
        $.post(admin_url+'care_of_clients/update_status/', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-care_of_clients').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset');
        });
    })


    // $('.table-care_of_clients').on('draw.dt', function() {
    //     var invoiceReportsTable = $(this).DataTable();
    //     var _table = $('.table-care_of_clients');
    //     var lengthTh = _table.find('thead tr th').length;
    //
    //     var TD_child = _table.find('tbody').find('tr.TD_child');
    //     TD_child.find('td:nth-child(1)').attr('colspan', lengthTh);
    //     TD_child.find('td:gt(0)').remove();
    // })

    function restore_Care_of_clients(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        $.post(admin_url+'care_of_clients/restore_care_of_clients', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-care_of_clients').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset');
        });
    }
    function BreakCare_of(id = "", status, _this)
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        $.post(admin_url+'care_of_clients/break_care_of', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-care_of_clients').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset');
        });
    }

</script>
<script>
    $('body').on('click', 'button.search-btn', function(e){
        var _div_parent = $(this).parent('.div-form-group');
        var id_procedure = _div_parent.attr('id_data');
        var  date_start = _div_parent.find('.date_start_filter').val();
        var  date_end = _div_parent.find('.date_end_filter').val();
        $('input[name="date_start"]').val(date_start);
        $('input[name="date_end"]').val(date_end);
        $('input[name="procedure"]').val(id_procedure);
        $('.table-care_of_clients').DataTable().ajax.reload();
    })
    $(document).on('click', '.btn-filter',function(e){
        if(!$(this).hasClass('filter_all'))
        {
            var id_procedure = $(this).attr('id_data');
            var procedure = $('input[name="procedure"]').val();
            if(id_procedure == procedure)
            {
                var  date_start = $('input[name="date_start"]').val();
                var  date_end = $('input[name="date_end"]').val();
                var _div_parent = $(this).parent('.div-form-group');
                $('.date_start_filter[id_data="'+procedure+'"]').val(date_start);
                $('.date_end_filter[id_data="'+procedure+'"]').val(date_end);
            }
            init_datepicker();
        }
        else
        {
            $('input[name="procedure"]').val('');
            $('input[name="date_start"]').val('');
            $('input[name="date_end"]').val('');
            $('.table-care_of_clients').DataTable().ajax.reload();
        }
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
    })
    function care_of_change_priority(priority, id)
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['priority'] = priority;
        data['id'] = id;
        $.post(admin_url+'care_of_clients/change_priority', data).done(function (data) {
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-care_of_clients').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).fail(function () {
            alert_float('danger', 'err');
        }).always(function () {

        })
    }
</script>