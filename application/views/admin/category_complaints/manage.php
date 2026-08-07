<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <?php if ($this->preAddCategoryComplaints) : ?>
                    <a href="#" onclick="new_category_complaints(); return false;" id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                <?php endif ?>
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
                            _l('Mã'),
                            _l('Tên'),
                            _l('options')
                        ), 'category_complaints'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('category_complaints/add_category_complaints'), array('id' => 'id_category_complaints')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ch_category_complaints_edit'); ?></span>
                    <span class="add-title"><?php echo _l('ch_category_complaints_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('code', 'ch_code_category_complaints'); ?>
                        <?php echo render_input('name', 'ch_name_category_complaints'); ?>
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
                url: admin_url + 'category_complaints/get_row_category_complaints/' + id,
                dataType: 'json',
            })
            .done(function(data) {
                if (data != "") {
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#id_category_complaints').prop('action', admin_url + 'category_complaints/update_category_complaints/' + id);
                }
            });
    }
    $(function() {
        var CustomersServerParams = {};
        $.each($('._hidden_inputs._filters input'), function() {
            CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

        var tAPI = initDataTable('.table-category_complaints', admin_url + 'category_complaints/table', [0], [0], CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'asc'))); ?>
        );
        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.ajax.reload();
        });
    });
    $(function() {
        appValidateForm($('#id_category_complaints'), {
            code: 'required',
            name: 'required'
        }, manage_contract_types);



        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="category_complaints"]').val('');
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
            $('.table-category_complaints').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_category_complaints() {
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#code').val('');
        $('#name').val('');
        $('#id_category_complaints').attr('action', admin_url + 'category_complaints/add_category_complaints');
    }

    function edit_type(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#type input[name="category_complaints"]').val(name);
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
                $('.table-category_complaints').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });

    function view_compose(params) {

    }
</script>