<script>
    $('body').on('click', '#day_all', function (e) {
        if ($(this).prop('checked')) {
            $('.check_week').prop('checked', true);
        } else {
            $('.check_week').prop('checked', false);
        }
    })

    $('body').on('click', '.check_week', function (e) {
        if ($(this).prop('checked') == false) {
            if ($(this).prop('id') != 'day_all') {
                $('#day_all').prop('checked', false);
            }
        } else {
            if ($('.check_week:checked').length > 7) {
                $('#day_all').prop('checked', true);
            }
        }

        if ($('.check_week:checked').length > 0) {
            $('#time').attr('disabled', 'disabled');
            $('#day').selectpicker('val', []);
            $('#day').attr('disabled', 'disabled').selectpicker('refresh');
        } else {
            $('#time').removeAttr('disabled');
            $('#day').removeAttr('disabled').selectpicker('refresh');
        }
    })

    $('body').on('change', '#day', function (e) {
        if ($('#day').val().length > 0) {
            $('.check_week').prop('checked', false);
            $('.check_week').attr('disabled', 'disabled');
            $('#time').attr('disabled', 'disabled');
            $('#check_time').attr('disabled',);
        } else {
            $('.check_week').removeAttr('disabled');
            $('#time').removeAttr('disabled');
            $('#check_time').removeAttr('disabled');
        }
    })

    $('body').on('change', '#check_time', function (e) {
        if ($(this).prop('checked')) {
            $('#time').removeAttr('disabled');
            $('#day').attr('disabled', 'disabled').selectpicker('refresh');
            $('#day').selectpicker('val', []);
            $('.check_week').attr('disabled', 'disabled');
        } else {
            $('#day').removeAttr('disabled').selectpicker('refresh');
            $('.check_week').removeAttr('disabled');
        }
    })

    var unit = <?=$i ? $i : 0?>;

    /*
     *Thêm thông báo theo lịch trình
     */
    $('body').on('click', '#send-infomation-staff', function () {
        var str_unit = AddTrInfomation("<?=_l('send_infomation_staff')?>");
        $('#modal_toggle').find('.tab-pane').removeClass('in active');
        $.post(admin_url + 'automations/get_infomation', {
            unit: str_unit,
            [csrfData['token_name']]: csrfData['hash']
        }, function (data) {
            $('#modal_toggle').append(data);
            $('#infomation_' + str_unit).find('.selectpicker').selectpicker('refresh');
        })
    })

    function View_infomation_staff(id) {
        $('#modal_toggle').find('.tab-pane').removeClass('in active');
        $('#infomation_' + id).addClass('in active');
        $('.table_information tbody tr').removeClass('active');
        $('.TrInfomation_' + id).addClass('active');
    }

    /*
     *END Thêm thông báo theo lịch trình
     */



    /*
     * Xóa Kết quả thực hiện
     */
    $('body').on('click', '.removeTr', function (e) {
        var id_data = $(this).parent().parent().attr('id_data');
        $('#' + id_data).remove();
        $(this).parent().parent().remove();
    })
    /*
     * Edn xóa kết quả thực hiện
     */


    /*
        Lấy dữ liệu send mail
     */

    $('body').on('click', '#send-email-staff-client', function () {
        var str_unit = AddTrInfomation("<?=_l('send_email_staff_client')?>");
        $('#modal_toggle').find('.tab-pane').removeClass('in active');
        $.post(admin_url + 'automations/get_infomation_sendmail', {
            unit: str_unit,
            [csrfData['token_name']]: csrfData['hash']
        }, function (data) {
            $('#modal_toggle').append(data);
            $('#infomation_' + str_unit).find('.selectpicker').selectpicker('refresh');
            init_editor();
        })
    })

    $('body').on('click', '#create-tasks-auto', function () {
        var str_unit = AddTrInfomation("<?=_l('create_tasks_auto')?>");
        $('#modal_toggle').find('.tab-pane').removeClass('in active');
        $.post(admin_url + 'automations/get_modal_tasks', {
            unit: str_unit,
            [csrfData['token_name']]: csrfData['hash']
        }, function (data) {
            $('#modal_toggle').append(data);
            $('#infomation_' + str_unit).find('.selectpicker').selectpicker('refresh');
            init_editor();
        })
    })

    //Add tr và return id
    function AddTrInfomation(title = "") {
        $('.table_information > tbody').append('<tr class="TrInfomation_' + unit + ' pointer" id_data="infomation_' + unit + '" onclick="View_infomation_staff(' + unit + ')">' +
            '                                       <td class="vertical_middle">' + title + '</td>' +
            '                                       <td class="vertical_middle text-center"><a class="removeTr pointer text-danger">X</a></td>' +
            '                                    </tr>');
        unit++;
        return (unit - 1);
    }



    $('body').on('change', '#action', function (e) {
        var id_action = $(this).val();
        RemoveAction();
    })
    function RemoveAction(){
        $('#theme-action').html('');
    }

    var Cinit = <?= (!empty($Cinit) ? $Cinit : 0); ?>;
    $('body').on('click', '#AddCondition', function(e){

        var button = $(this);
        button.button({loadingText: 'please wait...'});
        button.button('loading');
        if($('#action').val() == '1')
        {
            $.post(admin_url+'automations/AddConditionClient', {Cinit:Cinit,[csrfData['token_name']]: csrfData['hash']}, function(data){
                $('#theme-action').append(data);
                $('#AddCondition').button('reset');
                ++Cinit;
            })
        }
        else if($('#action').val() == '2')
        {
            $.post(admin_url+'automations/AddConditionLead', {Cinit:Cinit,[csrfData['token_name']]: csrfData['hash']}, function(data){
                $('#theme-action').append(data);
                $('#AddCondition').button('reset');
                ++Cinit;
            })
        }
        else
        {
            setTimeout(function(){ $('#AddCondition').button('reset'); },500),
            alert_float('danger','<?=_l('cong_you_need_object')?>');

        }
    })

    $('body').on('click', '.delete_well', function(e){
        $(this).parent().parent().remove();
    })


</script>