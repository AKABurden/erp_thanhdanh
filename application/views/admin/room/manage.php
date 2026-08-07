<?php init_head(); ?>
<style type="text/css">
    .table-room tbody tr td:nth-child(1) {
        white-space: inherit;
        width: 60px;
        text-align: center;
    }
    .view-switch {
        display: inline-flex;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 4px;
        gap: 4px;
    }

    .view-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        font-size: 14px;
        color: #555;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .view-btn i {
        font-size: 14px;
    }

    /* Hover */
    .view-btn:hover {
        background: #f5f7fa;
    }

    /* Active */
    .view-btn.active {
        background: #eaf2ff;
        border-color: #3b82f6;
        color: #2563eb;
        font-weight: 500;
    }

</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a class="btn btn-info pull-right H_action_button mleft5" onclick="ViewPDF(); return false;"><i class="fa fa-print"></i> <?php echo _l('In QR'); ?></a>
                <a href="<?= base_url('admin/room/update_room') ?>" id="suppliers_modal" class="btn btn-info tnh-modal mright5 test pull-right H_action_button">
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
                        <div class="view-switch" style="margin-bottom: 5px">
                            <button class="view-btn active" data-view="list_room">
                                <i class="fa fa-table"></i>
                                Danh sách phòng ban
                            </button>
                            <button class="view-btn" data-view="category_room">
                                <i class="fa fa-table"></i>
                                Danh mục phòng ban
                            </button>
                        </div>

                        <div class="table-list-room">
                            <?php render_datatable(array(
                                _l('STT'),
                                _l('Mã NV'),
                                _l('Họ tên'),
                                _l('Chi nhánh'),
                                _l('ch_code_room'),
                                _l('ch_name_room'),
                                _l('Khối vận hành'),
                                _l('Email'),
                                _l('Trưởng phòng'),
                                _l('Mục tiêu'),
                                _l('Ngân sách tối đa'),
                                _l('Trạng thái'),
                                _l('Thời gian hiệu lực từ'),
                                _l('Thời gian hiệu lực đến'),
                                _l('Link quy định'),
                                _l('Link làm việc'),
                                _l('Ghi chú'),
                            ), 'list_room'); ?>
                        </div>
                        <div class="table-category-room hide">
                            <?php render_datatable(array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="room"><label></label></div>',
                                _l('ch_code_room'),
                                _l('ch_name_room'),
                                _l('Khối vận hành'),
                                _l('Email'),
                                _l('Trưởng phòng'),
                                _l('Mục tiêu'),
                                _l('Ngân sách tối đa'),
                                _l('Trạng thái'),
                                _l('Thời gian hiệu lực từ'),
                                _l('Thời gian hiệu lực đến'),
                                _l('Link quy định'),
                                _l('Link làm việc'),
                                _l('Ghi chú'),
                                _l('options')
                            ), 'room'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('room/add_room'), array('id' => 'id_room')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ch_room_edit'); ?></span>
                    <span class="add-title"><?php echo _l('ch_room_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('code', 'ch_code_room'); ?>
                        <?php echo render_input('name', 'ch_name_room'); ?>
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
    var tAPI;
    var oTable;
    var fnserverparams = {}
    var CustomersServerParams = {};
    $.each($('._hidden_inputs._filters input'), function() {
        CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
    function view_init_department(id) {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        $.ajax({
                url: admin_url + 'room/get_row_room/' + id,
                dataType: 'json',
            })
            .done(function(data) {
                if (data != "") {
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#id_room').prop('action', admin_url + 'room/update_room/' + id);
                }
            });
    }
    $(function() {

        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.ajax.reload();
        });
        loadListTable();
    });

    function loadListTable(){
        oTable = tnhInitDataTable('.table-list_room',
        '<?= site_url('admin/room/getListRoom') ?>', {
            'order': [
                [0, 'asc'],
            ],
            "ajax": {
                "url": '<?= site_url('admin/room/getListRoom') ?>',
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
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [{}],
        });
    }

    function loadCategoryTable(){
        tAPI = initDataTable('.table-room', admin_url + 'room/table', [0], [0], CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'asc'))); ?>
        );
    }

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        if (status_table == 'list_room'){
            $(".table-category-room").addClass('hide');
            $(".table-list-room").removeClass('hide');
            if (typeof oTable != 'undefined' && oTable != '') {
                oTable.draw();
            } else {
                loadListTable();
            }
        } else {
            $(".table-category-room").removeClass('hide');
            $(".table-list-room").addClass('hide');
            if (typeof tAPI != 'undefined' && tAPI != '') {
                tAPI.draw();
            } else {
                loadCategoryTable();
            }
        }
    });

    $(function() {
        appValidateForm($('#id_room'), {
            code: 'required',
            name: 'required'
        }, manage_contract_types);



        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input[name="room"]').val('');
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
            $('.table-room').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_room() {
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#code').val('');
        $('#name').val('');
        $('#id_room').attr('action', admin_url + 'room/add_room');
    }

    function edit_type(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#type input[name="room"]').val(name);
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
                $('.table-room').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });

    function view_compose(params) {

    }


    function ViewPDF() {
        var ids = '';
        var rows = $('.table-room').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn phòng cần in mã vạch');
            return;
        }
        url = admin_url + 'room/print_pdf_html?ids=' + ids;
        window.open(url, "_blank");
    }

    $(document).on('click', '.view-btn', function () {
        $('.view-btn').removeClass('active');

        $(this).addClass('active');

        // lấy view
        const view = $(this).data('view');
        console.log('Đang chọn:', view);

        if (view == 'list_room'){
            $(".table-category-room").addClass('hide');
            $(".table-list-room").removeClass('hide');
            if (typeof oTable != 'undefined' && oTable != '') {
                oTable.draw();
            } else {
                loadListTable();
            }
        } else {
            $(".table-category-room").removeClass('hide');
            $(".table-list-room").addClass('hide');
            if (typeof tAPI != 'undefined' && tAPI != '') {
                tAPI.draw();
            } else {
                loadCategoryTable();
            }
        }
    });
</script>