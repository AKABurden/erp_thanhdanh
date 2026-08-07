<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>

    $(function()
    {
        $('a[href="#customer_tab_advisory_lead"]').parent('li').addClass('active');
        initDataTable('.table-advisory_lead', admin_url+'advisory_lead/table_advisory_lead_tab<?=!empty($leadid)? '/'.$leadid : ''?>', [0], [0], {}, [5, 'desc']);
    });

    function editAdvisory_lead(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: 'please wait...'});
        button.button('loading');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        if($.isNumeric(id))
        {
            data['id'] = id;
        }
        $.post(admin_url+'advisory_lead/getModal', data, function(data){
            $('#modal_advisory_lead').html(data);
            $('#modal_advisory_lead').modal('show');
        }).always(function() {
            button.button('reset')
        });
    }

    function deleteAdvisory_lead(id = "", _this) {
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
                $.post(admin_url+'advisory_lead/delete_advisory_lead', data, function(data){
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success)
                    {
                        $('.table-advisory_lead').DataTable().ajax.reload();
                    }
                }).always(function() {
                        button.button('reset')
                    });
            }
        }
    }
    $('body').on('click', '.update_status_lead', function(e){
        var id_assigned  = $(this).attr('id-data');

        var button = $(this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var status_procedure  = $(this).attr('status-procedure');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id_assigned;
        data['status_procedure'] = status_procedure;
        $.post(admin_url+'advisory_lead/update_status/', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    })


    $('.table-advisory_lead').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var _table = $('.table-advisory_lead');
        var lengthTh = _table.find('thead tr th').length;

        var TD_child = _table.find('tbody').find('tr.TD_child');
        TD_child.find('td:nth-child(1)').attr('colspan', lengthTh);
        TD_child.find('td:gt(0)').remove();
    })

    function restore_advisory_lead(id = "", _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        $.post(admin_url+'advisory_lead/restore_advisory_lead', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    }
    function BreakAdvisory(id = "", status, _this)
    {
        var button = $(_this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        $.post(admin_url+'advisory_lead/break_advisory', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        }).always(function() {
            button.button('reset')
        });
    }

</script>



</body>
</html>
