<div class="modal-dialog modal-xl modal-check">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('view') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($qualityControl['date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_qc') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $qualityControl['reference_no'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span
                                class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_productions_orders_details') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $pod ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Chi nhánh xưởng') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $branch ?></span>
                        </div>
                        <!-- <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Khách hàng') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $client['company'] ?></span>
                        </div> -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Số đơn hàng') ?>:
                            </span>
                            <span
                                class="bold font-medium-xs lead-name"><?= get_orders($qualityControl['order_id']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quantity_qc') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $qualityControl['quantity_qc'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Người tạo') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $created_by ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $qualityControl['note'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label style="cursor: pointer" for="tab1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->

                        <input type="radio" name="tabset" id="tab6" aria-controls="tab_feedback">
                        <label class="pointer" for="tab6"><i class="icon-foso fa fa-comments-o"></i>
                            <?= _l('FeedBack') ?><span
                                class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span>
                        </label>


                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label style="cursor: pointer" for="tab5"><?= lang('activity_log_puchases') ?></label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items"
                                        class="dt-table table table-bordered table-hover dont-responsive-table table-items-check"
                                        style="max-height: 400px !important;">
                                        <thead>
                                            <tr>
                                                <th class="text-center hide" style="width: 50px;">
                                                    <?= lang('tnh_numbers') ?>
                                                </th>
                                                <th style="width: 80px;"><?= lang('Hình ảnh') ?></th>
                                                <th style="width: 200px;"><?= lang('Tên/Mã thành phẩm') ?></th>
                                                <th style="width: 50px;"><?= lang('Đơn vị') ?></th>
                                                <th style="width: 80px;"><?= lang('SL kiểm tra ') ?></th>
                                                <th style="width: 80px;"><?= lang('SL lỗi ') ?></th>
                                                <th class="hide" style="width: 80px;"><?= lang('SL phế ') ?></th>
                                                <th style="width: 80px;"><?= lang('SL đạt ') ?></th>
                                                <th style="width: 80px;"><?= lang('Kết quả ') ?></th>
                                                <th style="width: 100px;"><?= lang('Tỉ lệ % không đạt') ?></th>
                                                <th style="width: 100px;"><?= lang('Tỉ lệ % đạt') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('error1') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('error2') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('stage') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('stage_again') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('image') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $key => $value):
                                            $images = '';
                                            $pod_name = '';
                                            $order_name = '';
                                            $plan_name = '';

                                            if ($value['type_item'] == "products" || $value['type_item'] =="semi_products") {
                                                $info = $this->products_model->rowProduct($value['item_id']);
                                                $unit = $this->unit_model->rowUnit($info['unit_id']);

                                                if (!empty($info['images'])) {
                                                    $images = base_url('uploads/products/'.$info['images']);
                                                } 
                                            }
                                            if(empty($images)) {
                                                $images = base_url('assets/images/tnh/no_image.png');
                                            }
                                            $pod = get_table_where('tbl_productions_orders_details',
                                                ['id' => $value['pod_id']], '', 'row_array');
                                            if (!empty($pod)) {
                                                $pod_name = $pod['reference_no'];
                                            }
                                            $stage_name = '';
                                            $stage_name_again = '';
                                            $stage = get_table_where('tbl_stages',['id'=>$value['id_stage']],'','row_array');
                                            if(!empty($stage)){
                                                $stage_name = $stage['name'];
                                            }
                                            $stage_again = get_table_where('tbl_stages',['id'=>$value['id_stage_again']],'','row_array');
                                            if(!empty($stage_again)){
                                                $stage_name_again = $stage_again['name'];
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-left details-control hide"><?= ++$key ?></td>
                                                <td>
                                                    <div class="td-image">
                                                        <div class="preview_image" style="width: auto;">
                                                            <div class="display-block contract-attachment-wrapper img">
                                                                <div style="width:45px; margin: auto;"><a
                                                                        href="<?= $images ?>"
                                                                        data-lightbox="customer-profile"
                                                                        class="display-block mbot5">
                                                                        <div class=""><img src="<?= $images ?>"
                                                                                style="border-radius: 50%">
                                                                        </div>
                                                                    </a></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $value['item_name'] .'('.$value['item_code'].')' ?>
                                                    <?php if($value['object_type'] == 'orders'){ 
                                                         $order = get_table_where('tbl_orders',
                                                         ['id' => $value['order_id']], '', 'row_array');
                                                         if (!empty($order)) {
                                                             $order_name = $order['reference_no'];
                                                         }
                                                    ?>

                                                    <div> Đơn hàng:<span style="color: red"> <?= $order_name ?></span>
                                                    </div>
                                                    <?php } elseif($value['object_type'] == 'business_plan'){
                                                           $plan = get_table_where('tbl_business_plan',
                                                           ['id' => $value['plan_id']], '', 'row_array');
                                                           if (!empty($plan)) {
                                                               $plan_name = $plan['reference_no'];
                                                           }
                                                    ?>
                                                    <div> KHKD: <span style="color: red"><?= $plan_name ?></span></div>
                                                    <?php } ?>
                                                    <div style="color: red"> LSXCT: <?= $pod_name ?></div>
                                                </td>
                                                <td class="text-center"><?= $unit['unit'] ?></td>
                                                <td class="text-center"><?= formatNumber($value['quantity_qc']) ?></td>
                                                <td class="text-center">
                                                    <?= formatNumber($value['quantity_recycling']) ?>
                                                    <!-- <?php if (!empty($value['data_json_taiche'])) { ?>
                                                    <div class="mtop5"><a
                                                            href="<?= base_url('admin/quality_control/view_reason/'.$value['id'].'/1') ?>"
                                                            class="btn btn-primary tnh-modal2">Chi tiết lỗi</a>
                                                    </div>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-center hide">
                                                    <?= formatNumber($value['quantity_waste']) ?>
                                                    <!-- <?php if (!empty($value['data_json_phe'])) { ?>
                                                    <div class="mtop5"><a
                                                            href="<?= base_url('admin/quality_control/view_reason/'.$value['id'].'/2') ?>"
                                                            class="btn btn-primary tnh-modal2">Chi tiết lỗi</a>
                                                    </div>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-center">
                                                    <?= formatNumber($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste'])) ?>
                                                </td>
                                                <td class="text-left">
                                                    <?php if($value['result'] == 1){ ?>
                                                    <span style="font-weight:bold;color:green">Đạt</span>
                                                    <?php } else { ?>
                                                    <span style="font-weight:bold;color:red">Không Đạt</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= formatNumber(($value['quantity_recycling'] + $value['quantity_waste']) * 100 / $value['quantity_qc']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= formatNumber(($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste'])) * 100 / $value['quantity_qc']) ?>
                                                </td>
                                                <td class="hide"><?= $value['data_json_taiche']; ?></td>
                                                <td class="hide"><?= $value['data_json_phe']; ?></td>
                                                <td class="hide"><?= $stage_name ?></td>
                                                <td class="hide"><?= $stage_name_again ?></td>
                                                <td class="hide"><?= $value['images_multiple'] ?></td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <section id="tab_feedback" class="tab-panel">
                                <div class="col-md-12 mtop5">
                                    <?php include_once(APPPATH . 'views/admin/feedback/check_quality/feedback.php'); ?>
                                </div>
                                <div class="clearfix"></div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($qualityControl['id'], 'check_quality');
                                    ?>
                                    <?php if (!empty($history)): ?>
                                    <?php foreach ($history as $key => $value): ?>
                                    <?php
                                            echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        '.staff_profile_image($value['staff_id'],
                                                    array('staff-profile-image-small'),
                                                    'small').''.$value['staff_name'].'
                                                    </div>
                                                    <div class="activity-time">
                                                        '.time_ago($value['date']).'<span class="activity-module">'._l($value['type_parent_obj']).'</span>
                                                    </div>
                                                    <div>
                                                        '.$value['content'].'
                                                    </div>
                                                </div>';
                                            ?>
                                    <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($qualityControl['date_created']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
var arr = [];

function formatProductionsOrders(d) {
    sub = d[6];
    return sub;
}

$(document).ready(function() {
    var dtItems = $('#table-items').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        // "lengthMenu": [
        //     [10, 25, 50, 100, -1],
        //     [10, 25, 50, 100, "<?= lang('all') ?>"]
        // ],
        // fixedColumns:   {
        //     leftColumns: 3,
        //     rightColumns: 0
        // },
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
        "footerCallback": function(row, data, start, end, display) {}
    });

    function format(d) {
        table = '';
        table_json_taiche = '';
        if (!empty(d[11])) {
            json_taiche = JSON.parse(d[11]);
            if (json_taiche != null) {
                if (json_taiche.length > 0) {
                    table_body_json_taiche = '';
                    j = 1;
                    $.each(json_taiche, function(k, v) {
                        if (v.quantity_quote > 0) {
                            table_body_json_taiche += `
                        <tr>
                            <td class="td-number-quote text-center">${(j)}</td>
                            <td class="td-name-quote text-left">${v.reason_name}</td>
                            <td class="td-qty-quote text-center">
                            ${v.quantity_quote}
                            </td>
                        </tr> `;
                            j++;
                        }
                    });
                    table_json_taiche = `
                <div class="col-md-6">
                    <div class="content-price">
                        <table id="tb-quote_norm" class="table  dataTable table-bordered table-hover dont-responsive-table"
                            style="margin-top: 10px !important;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;background-color: #daeaf9 !important;color: black !important;"><?= lang('tnh_numbers') ?></th>
                                    <th class="text-center" style="background-color: #daeaf9 !important;color: black !important;"><?= lang('Nguyên Nhân - Lỗi') ?></th>
                                    <th class="text-center" style="width: 150px;background-color: #daeaf9 !important;color: black !important;"><?= lang('Số lượng ') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${table_body_json_taiche}
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
                } else {
                    table_json_taiche = '';
                }
            } else {
                table_json_taiche = '';
            }
        }
        table_json_phe = '';
        if (!empty(d[12])) {
            json_phe = JSON.parse(d[12]);
            if (json_phe != null) {
                if (json_phe.length > 0) {
                    table_body_json_phe = '';
                    i = 1;
                    $.each(json_phe, function(kk, v) {
                        if (v.quantity_quote > 0) {
                            table_body_json_phe += `
                        <tr>
                            <td class="td-number-quote text-center">${i}</td>
                            <td class="td-name-quote text-left">${v.reason_name}</td>
                            <td class="td-qty-quote text-center">
                            ${v.quantity_quote}
                            </td>
                        </tr>`;
                            i++;
                        }
                    });
                    table_json_phe = `
                <div class="col-md-6">
                    <div class="content-price">
                        <table id="tb-quote_norm" class="table  dataTable table-bordered table-hover dont-responsive-table"
                            style="margin-top: 10px !important;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;background-color: #daeaf9 !important;color: black !important;"><?= lang('tnh_numbers') ?></th>
                                    <th class="text-center" style="background-color: #daeaf9 !important;color: black !important;"><?= lang('Nguyên Nhân - Phế') ?></th>
                                    <th class="text-center" style="width: 150px;background-color: #daeaf9 !important;color: black !important;"><?= lang('Số lượng ') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${table_body_json_phe}
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
                } else {
                    table_json_phe = '';
                }
            } else {
                table_json_phe = '';
            }
        }
        images_multiple = d[15];
        html_image = '';
        if (images_multiple != '' && images_multiple != null) {
            images = images_multiple.split('||');
            html_image += ` <div class="preview_image" id="avatar_view" style="width: auto;">
                            <div class="display-block contract-attachment-wrapper img-1">`;
            $.each(images, function(k, v) {
                html_image += `
                                        <div class="col-md-3">
                                        <input type="hidden" name="images_old[]" id="images_old[]" class="form-control" value="${v}">
                                        <button type="button" class="close remove-image" data-id="50" data-src="uploads/items/50/tru_ringlock.jpg" style="color:red;" aria-label="Close">
                                        </button>
                                        <a href="<?= base_url('uploads/check_quality/')?>${v}" data-lightbox="customer-profile" class="display-block mbot5">
                                            <div class="">
                                                <img style="max-width: 200px;height: 50px;" src="<?= base_url('uploads/check_quality/')?>${v}">
                                            </div>
                                        </a>
                                    </div>
                                    `;
            });
            html_image += `</div>
                        </div>`;
        }

        stage_again = '';
        if (d[14] != '') {
            stage_again = `<div class="col-md-4">
                        <div class="timeline-vertical">
                            <div class="wrapper">
                                <ul class="sessions" style="margin-left: 28px;">
                                    <li class="again">
                                    <p class="mtop10" style="color:red;font-size: 14px;font-weight: bold;">${d[14]}</p>
                                </li>
                                </ul>
                            </div>
                        </div>  
                    </div>
                    <div class="col-md-4">
                        ${html_image}
                    </div>
                `;
        }
        table = `
            <div class="row"> 
                <div class="col-md-4">
                    <div class="timeline-vertical">
                        <div class="wrapper">
                            <ul class="sessions" style="margin-left: 28px;">
                                <li class="active">
                                <p class="mtop10" style="color:green;font-size: 14px;font-weight: bold;">${d[13]}</p>
                            </li>
                            </ul>
                        </div>
                    </div>  

                </div>
                ${stage_again}
                <div class="clearfix"></div>
                ${table_json_taiche}
                ${table_json_phe}
            </div>
        `;
        return table;
    }

    $('#table-items').DataTable().rows().every(function() {
        var tr = $(this.node());
        var row = dtItems.row(tr);

        if (row.child.isShown()) {} else {
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
});
</script>