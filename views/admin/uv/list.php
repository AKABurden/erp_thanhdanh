<?php init_head() ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>

                <?php if (has_permission('units', '', 'create')) { ?>
                <div class="line-sp"></div>
                <a href="#" onclick="new_empl(); return false;" id="suppliers_modal"
                    class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('id'),
                            _l('hinh'),
                            _l('name'),
                            _l('phone'),
                            _l('address'),
                            _l('phongban'),
                            _l('chucvu'),
                            _l('action')

                        ), 'tbluv'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('uv/add_empl'), array('id' => 'id_employee')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ch_units_edit'); ?></span>
                    <span class="add-title"><?php echo _l('h_empl_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- <div class="col-md-12">
                        <input type="file" class="form-control" name="hinh" id="hinh">
                    </div> -->
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'Tên ứng viên'); ?>
                    </div>
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('email', 'Email'); ?>
                    </div>
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('phone', 'Số điện thoại'); ?>
                    </div>
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('address', 'Địa chỉ'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('roomID', $departments, array('departmentid', 'name'), 'Phòng Ban') ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('positionID', $roles, array('roleid', 'name'), 'Chức Vụ') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail() ?>

<script>
function view_init_department(id) {
    $('#type').modal('show');
    $('.add-title').addClass('hide');
    $.ajax({
            url: admin_url + 'uv/get_row/' + id,
            dataType: 'json',
        })
        .done(function(data) {
            if (data != "") {
                //$('#name').val(data.name);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#roomID').val(data.roomID).selectpicker('refresh');
                $('#positionID').val(data.positionID).selectpicker('refresh');
                $('#id_employee').prop('action', admin_url + 'uv/update_empl/' + id);
            }
        });
}
$(function() {
    var CustomersServerParams = {};
    $.each($('._hidden_inputs._filters input'), function() {
        CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

    var tAPI = initDataTable('.table-tbluv', admin_url + 'uv/table', [0], [0], CustomersServerParams,
        <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'asc'))); ?>
    );
    $('input[name="exclude_inactive"]').on('change', function() {
        tAPI.ajax.reload();
    });
});
$(function() {
    appValidateForm($('#id_employee'), {
        name: 'required',
        email: {
            email: true,
            required: true
        },
        phone: 'required',
        address: 'required',
    }, manage_employee_types);



    $('#type').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#name input[name="empl"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
});

$(document).on('click', '.delete-reminder_h', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
            alert_float(response.alert_type, response.message);
            $('.table-tbluv').DataTable().ajax.reload();
        }, 'json');
    }
    return false;
});

function manage_employee_types(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
        }
        $('.table-tbluv').DataTable().ajax.reload();
        $('#type').modal('hide');
    });
    return false;
}

function new_empl() {

    $('#type').modal('show');
    $('.edit-title').addClass('hide');
    $('#name').val('');
    $('#email').val('');
    $('#phone').val('');
    $('#address').val('');
    $('#roomID').val('').selectpicker('refresh');
    $('#positionID').val('').selectpicker('refresh');
    $('#id_type').attr('action', admin_url + 'units/add_unit');
}

function edit_type(invoker, id) {
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id', id));
    $('#type input[name="name"]').val(name);
    $('#type').modal('show');
    $('.add-title').addClass('hide');
}
</script>