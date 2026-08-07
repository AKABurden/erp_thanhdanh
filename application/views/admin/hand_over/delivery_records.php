<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.4') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <?php if(has_permission('delivery_records', '', 'create')) {?>
                    <a href="<?= base_url('admin/hand_over/handling_delivery_records') ?>" class="btn btn-info H_action_button tnh-modal">
                        <?php echo _l('add'); ?>
                    </a>
                <?php } ?>
                
                <a onclick="export_excel()" class="btn btn-info H_action_button">
					<?php echo _l('Xuất excel'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12 mbot10">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <?= lang('Người bàn giao', 'staff_search') ?>
                        <select name="staff_search" data-placeholder="<?= lang('Người bàn giao') ?>" id="staff_search" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($staffs)) : ?>
                                <?php foreach ($staffs as $key => $value) : ?>
                                    <option value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('Người nhận bàn giao', 'receiver_search') ?>
                        <select name="receiver_search" data-placeholder="<?= lang('Người nhận bàn giao') ?>" id="receiver_search" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($staffs)) : ?>
                                <?php foreach ($staffs as $key => $value) : ?>
                                    <option value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
<!--                    <div class="col-md-2">-->
<!--                        --><?//= lang('Bộ phận bàn giao', 'module_category_hand_over_search') ?>
<!--                        <select name="module_category_hand_over_search" id="module_category_hand_over_search" data-none-selected-text="--><?//= lang('Bộ phận bàn giao') ?><!--" class="form-control selectpicker">-->
<!--                            <option></option>-->
<!--                            --><?php //if(!empty($module_hand_over)): ?>
<!--                                --><?php //foreach($module_hand_over as $key => $value): ?>
<!--                                    <option value="--><?//= $value['id'] ?><!--">--><?//= $value['name'] ?><!--</option>-->
<!--                                --><?php //endforeach; ?>
<!--                            --><?php //endif; ?>
<!--                        </select>-->
<!--                    </div>-->
                    <div class="col-md-3">
                        <?= lang('Loại bàn giao', 'category_hand_over_search') ?>
                        <select name="category_hand_over_search" id="category_hand_over_search" data-none-selected-text="<?= lang('Loại bàn giao') ?>" class="form-control selectpicker">
                            <option></option>
                            <?php if(!empty($category_hand)): ?>
                                <?php foreach($category_hand as $key => $value): ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-delivery_records" class="table dt-tnh table-hover table-delivery_records" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="hand_over"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center" style="width: 150px;"><?= lang('tnh_reference_no_delivery_records') ?></th>
                                        <th class="text-center" style="width: 130px;"><?= lang('tnh_date_delivery_records') ?></th>
                                        <th class="text-center" style="width: 150px;"><?= lang('Loại bàn giao') ?></th>
                                        <th class="text-center"><?= lang('Nhóm bàn giao') ?></th>
                                        <th class="text-center"><?= lang('Chi tiết bàn giao') ?></th>

                                        <th class="text-center" style="width: 200px;"><?= lang('tnh_content_delivery_records') ?></th>
                                        <th class="text-center"><?= lang('Kết quả') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo sự cố') ?></th>
                                        <th class="text-center"><?= lang('Người bàn giao') ?></th>
                                        <th class="text-center"><?= lang('Người nhận bàn giao') ?></th>
                                        <th class="text-center"><?= lang('tnh_status') ?></th>
                                        <th class="text-center"><?= lang('QR') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                                        <th class="text-center" style="width: 200px;"><?= lang('Hạng mục bàn giao') ?></th>
<!--                                        <th class="text-center" style="width: 150px;">--><?//= lang('tnh_handing_over_department') ?><!--</th>-->
                                        
<!--                                        <th class="text-center" style="width: 200px;">--><?//= lang('tnh_content_delivery_records') ?><!--</th>-->
<!--                                        <th class="text-center">--><?//= lang('Kết quả') ?><!--</th>-->
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        staff_search: "#staff_search",
        receiver_search: "#receiver_search",
        category_hand_over_search: "#category_hand_over_search",
    };
    var oTable = '';

    function handling_status(delivery_records_id, status, type) {
        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['delivery_records_id'] = delivery_records_id;
        dataPOST['status'] = status;
        dataPOST['type'] = type;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/hand_over/handling_status',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result == 1) {
                    oTable.draw(false);
                }
                alert_float(response.type, response.message);
            }
        });
    }

    $(document).ready(function() {
        $('#staff_search').select2({allowClear: true});
        $('#receiver_search').select2({allowClear: true});
        oTable = tnhInitDataTable('#table-delivery_records', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/hand_over/getDeliveryRecords') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return `<div class="checkbox checkbox-info">
                            <input type="checkbox" name="delivery_records[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        </div>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": 5,
                    'orderable': false,
                    'searchable': false,
                },
                {
                    "targets": 6,
                    'orderable': false,
                    'searchable': false
                },
                {
                    "name": 'actions',
                    "targets": 7,
                    'orderable': false,
                    'searchable': false,
                }
            ],
        });

        $(document).on('click', '#agree-hand_over', function(event) {
            event.preventDefault();
            index = this;
            hand_over_id = $(this).attr('hand_over_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (hand_over_id) {
                $.ajax({
                        url: site.base_url + 'admin/hand_over/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            hand_over_id: hand_over_id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    })
                    .fail(function(data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $('#staff_search, #module_category_hand_over_search, #category_hand_over_search, #receiver_search').change(function(event) {
            oTable.draw();
        });
    });


    function export_excel() {
    
        var originalForm = $('input[name="delivery_records[]"]:checked');
        if(originalForm.length > 0) {
            // Tạo một form ẩn để gửi dữ liệu POST
            var form = document.createElement('form');
            form.style.display = 'none';
            form.method = 'POST';
            form.action = site.base_url + 'admin/hand_over/export_delivery_records'; // Điều chỉnh URL đích
            $.each(originalForm, function (index, value) {
                $(value).attr('name', 'list_id[]');
                var clonedElement = value.cloneNode(true);
                form.appendChild(clonedElement);
            })
            if (typeof (csrfData) !== 'undefined') {
                var inputHash = document.createElement("input");
                $(inputHash).attr('name', csrfData['token_name']);
                $(inputHash).val(csrfData['hash']);
                form.appendChild(inputHash);
            }
            // Thêm form ẩn vào body và submit nó
            document.body.appendChild(form);
            form.submit();
        }
        else {
            alert_float('danger', 'Vui lòng chọn phiếu cần xuất');
        }
    }

</script>