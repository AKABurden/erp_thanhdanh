<?php init_head(); ?>
<style type="text/css">
    .table-nest tbody tr td:nth-child(1) {
        white-space: inherit;
        width: 60px;
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a class="btn btn-info pull-right H_action_button mleft5" onclick="ViewPDF(); return false;"><i class="fa fa-print"></i> <?php echo _l('In QR'); ?></a>
                <a href="#" onclick="new_nest(); return false;" id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
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
                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="nest"><label></label></div>',
                            _l('Mã'),
                            _l('Tên'),
                            _l('options')
                        ), 'nest'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('nest/add_nest'), array('id' => 'id_nest')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ch_nest_edit'); ?></span>
                    <span class="add-title"><?php echo _l('ch_nest_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('code', 'ch_code_nest'); ?>
                        <?php echo render_input('name', 'ch_name_nest'); ?>
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
<?php init_tail(); ?>
<script>
    function view_init_department(id) {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        $.ajax({
                url: admin_url + 'nest/get_row_nest/' + id,
                dataType: 'json',
            })
            .done(function(data) {
                if (data != "") {
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#id_nest').prop('action', admin_url + 'nest/update_nest/' + id);
                }
            });
    }
    $(function() {
        var CustomersServerParams = {};
        $.each($('._hidden_inputs._filters input'), function() {
            CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

        var tAPI = initDataTable('.table-nest', admin_url + 'nest/table', [0], [0], CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'asc'))); ?>
        );
        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.ajax.reload();
        });
    });
    $(function() {
        appValidateForm($('#id_nest'), {
            code: 'required',
            name: 'required'
        }, manage_contract_types);



        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="nest"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });

    function manage_contract_types(form) {
        var data = $(form).serialize();

        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
            }
            if (response.success == false) {
                alert_float('danger', response.message);
            }
            $('.table-nest').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_nest() {
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#code').val('');
        $('#name').val('');
        $('#id_nest').attr('action', admin_url + 'nest/add_nest');
    }

    function edit_type(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#type input[name="nest"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }
    $(document).on('click', '.delete-reminders', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                $('.table-nest').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });

    function view_compose(params) {

    }

    function ViewPDF() {
        var ids = '';
        var rows = $('.table-nest').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn tổ cần in mã vạch');
            return;
        }
        url = admin_url + 'nest/print_pdf_html?ids=' + ids;
        window.open(url, "_blank");
    }
</script>