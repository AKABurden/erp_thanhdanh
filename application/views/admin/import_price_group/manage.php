<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a class="btn btn-info mright5 test pull-right H_action_button btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><?= lang('search') ?></a>
                <?php if (has_permission('import_price_group', '', 'create')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?php echo admin_url('import_price_group/import'); ?>" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('dt_import_price'); ?></a>
                <?php } ?>
                <div class="pull-right mright5 H_border">
                    <a onclick="" href="<?= base_url('admin/import_price_group/export_excel') ?>" class="btn btn-info pull-right mright10 H_action_button">
                        <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                        <?php echo _l('EXPORT EXCEL'); ?>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3 form-group">
                        <?php echo render_select('price', $data_price, array('id', 'name_price', 'year'), 'Tên bảng giá'); ?>
                    </div>
                    <div class="col-md-3">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="client_search" data-placeholder="<?= lang('customers') ?>" id="client_search" class="client_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?php echo render_input('year_search', 'Năm'); ?>
                    </div>
                    <div class="col-md-3">
                        <?= lang('Thành phẩm', 'items_search') ?>
                        <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Thành phẩm" value="">
                    </div>
                    <div class="clearfix"></div>
                    <hr />
                </div>
            </div>
            <input type="hidden" id="filterStatus" name="filterStatus" value="" />
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('STT'),
                            _l('Tên bảng giá'),
                            //							_l('year'),
                            _l('Khách hàng'),
                            //							'<div class="text-center">' . _l('Chiết khấu (%)') . '</div>',
                            _l('options')
                        ), 'import_price_group'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="view_data"></div>
<?php init_tail(); ?>
<script>
    ajaxSelectParams('#client_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
    ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0, true, true);

    function updateprice(id) {
        $.get(admin_url + 'import_price_group/UpdatePirceOrder/' + id, function(response) {
            alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        }, 'json');
    }

    function view_detail(id) {
        $('#view_data').html('');
        $.get(admin_url + 'import_price_group/show_detail_price/' + id).done(function(response) {
            // console.log(response);
            $('#view_data').html(response);
            $('#detail_price').modal({
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

    function view_detail_discount(id, client) {
        $('#view_data').html('');
        $.get(admin_url + 'import_price_group/show_detail_price_discount/' + id + '/' + client).done(function(response) {
            console.log(response);
            $('#view_data').html(response);
            $('#detail_price').modal({
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
    var tAPI;
    $(function() {
        appValidateForm($('#id_unit'), {
            unit: 'required'
        }, manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="unit"]').val('');
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
            $('.table-units').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_unit() {
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#unit').val('');
        $('#id_type').attr('action', admin_url + 'units/add_unit');
    }

    function edit_type(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#type input[name="unit"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }
    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });
    $(function() {
        var CustomersServerParams = {
            'price_name': '[name="price"]',
            'client_search': '[name="client_search"]',
            'year_search': '[name="year_search"]',
            'items_search': '[name="items_search"]',
        };
        $.each($('._hidden_inputs._filters input'), function() {
            CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
        tAPI = initDataTable('.table-import_price_group', admin_url + 'import_price_group/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $(filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.ajax.reload();
        });
    });


    function formatNumBerKeyUpCusFour(id_input) {
        vl = $(id_input).val().replace(/[^\-\d\.]/g, '');
        vl = vl.split('.');
        vl[0] = vl[0].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        if (vl.length > 1) {
            vl = vl[0] + '.' + vl[1];
        } else {
            vl = vl[0];
        }
        $(id_input).val(vl);
    }
</script>