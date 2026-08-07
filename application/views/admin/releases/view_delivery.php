<style>
    <?php if (!$this->perPriceDeliveries): ?>
    #view-items tr th:nth-child(12), #view-items tr td:nth-child(12) {
        display: none;
    }
    #view-items tr th:nth-child(13), #view-items tr td:nth-child(13) {
        display: none;
    }
    #view-items tr th:nth-child(14), #view-items tr td:nth-child(14) {
        display: none;
    }
    #view-items tr th:nth-child(15), #view-items tr td:nth-child(15) {
        display: none;
    }
    #view-items tr th:nth-child(17), #view-items tr td:nth-child(17) {
        display: none;
    }
    <?php endif ?>
    #view-items tr th:nth-child(15), #view-items tr td:nth-child(15) {
        display: none;
    }
    /* #view-items tr th:nth-child(16), #view-items tr td:nth-child(16) {
        display: none;
    }
    #view-items tr th:nth-child(17), #view-items tr td:nth-child(17) {
        display: none;
    } */

    #view-items tr th:nth-child(12), #view-items tr td:nth-child(12) {
        display: none;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div class="modal-dialog modal-lg modal-view-delivery" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('sum_view_releases') ?></h4>
		</div>
		<div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                        $edit = '<a href="'.base_url('admin/releases/edit_delivery/'.$delivery['id']).'"><i class="fa fa-edit"></i> '.lang('edit').' '.lang('deliveries').'</a>';
                        $print = '<a href="'.base_url('admin/releases/print_delivery/'.$delivery['id']).'" target="_blank"><i class="fa fa-print"></i> '.lang('print').' '.lang('deliveries').'</a>';
                        $export_warehouse_sales = '<a class="ews" href="'.base_url('admin/releases/export_warehouse_sales/'.$delivery['id']).'"><i class="fa fa-cube"></i> '.lang('tnh_export_warehouse_sales').'</a>';

                        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\''.base_url('admin/releases/deleteDelivery/'.$delivery['id']).'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
                        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').' '.lang('deliveries').'</a>';

                        $edit_address_delivery = '<a class="tnh-modal2" href="'.base_url('admin/releases/edit_address_delivery/'.$delivery['address_delivery_id']).'"><i class="fa fa-edit"></i> Sửa địa chỉ giao hàng</a>';

                        $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            '.lang('actions').'
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>'.$print.'<li>
                                <li>'.$edit_address_delivery.'<li>
                                <li class="not-outside">'.$delete.'</li>
                            </ul>
                        </div>';
                        if ($this->input->get('view') != 'seen')
                        {
                            echo $actions;
                        }
                    ?>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($delivery['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_deliveries') ?>: </div>
                            <div class="ml-at t-bold"><?= ($delivery['reference_no']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_orders') ?>: </div>
                            <div class="ml-at t-bold"><?= $referenceOrder['reference_order'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('customers') ?>: </div>
                            <div class="ml-at t-bold"><?= $company['company'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_address_delivery') ?>: </div>
                            <div class="ml-at t-bold tnh_address_delivery"><?= !empty($address_delivery) ? $address_delivery['address'] : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_employees_charge') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($employee) ? $employee : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $delivery['note'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('status') ?>: </div>
                            <div class="ml-at t-bold"><?= $delivery['warehouseman_id'] == 0 ? lang('tnh_status_undelivery') : lang('tnh_status_delivery') ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($delivery['id_branch']) ? get_table_where('tblbranch', ['id' => $delivery['id_branch']], '', 'row')->name : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <?php if ($this->input->get('view') != 'seen'): ?>
                        <input type="radio" name="tabset" class="hide" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5" class="hide"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>
                        <?php endif ?>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-hover dont-responsive-table" style="max-height: 400px !important;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 50px"><?= lang('tnh_images') ?></th>
                                            <th style="width: 100px;"><?= lang('code') ?></th>
                                            <th style="width: 100px;"><?= lang('Tên thành phẩm của KH') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_reference_orders') ?></th>
                                            <th style="width: 50px;"><?= lang('tnh_unit') ?></th>
                                            <th style="width: 150px;"><?= lang('tnh_warehouses') ?></th>
                                            <th style="width: 150px;"><?= lang('tnh_location_warehouse') ?></th>
                                            <th style="width: 100px;"><?= lang('SL đặt') ?></th>
                                            <th style="width: 100px;"><?= lang('SL loss') ?></th>
                                            <th style="width: 100px;"><?= lang('SL mẫu') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_unit_exchange') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_unit_price') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_total_amount') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_discount_percent') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_discount_direct') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_grand_total') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <?php if ($this->input->get('view') != 'seen'): ?>
                            <section id="view-activity-log" class="tab-panel hide">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($delivery['id'], 'deliveries');
                                    ?>
                                    <?php if (!empty($history)): ?>
                                        <?php foreach ($history as $key => $value): ?>
                                            <?php
                                                echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        '.staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small').''.$value['staff_name'].'
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
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php if ($this->perPriceDeliveries): ?>
                        <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                            <tbody>
                                <tr>
                                    <td style="width: 40%;"><?= lang('tax', 'tax') ?></td>
                                    <!-- <td class="text-right"><?= $delivery['tax_name'] ?></td> -->
                                    <td class="text-right"><?= formatMoney($delivery['total_tax']) ?>(<?= $delivery['tax_rate'] ?>%)</td>
                                </tr>
                                <tr class="hide">
                                    <td><?= lang('tnh_discount_percent', 'discount_percent') ?></td>
                                    <!-- <td class="text-right"><?= $delivery['discount_percent'] ?>%</td> -->
                                    <td class="text-right"><?= formatMoney($delivery['total_discount_percent']) ?></td>
                                </tr>
                                <tr class="hide">
                                    <td><?= lang('tnh_discount_direct', 'discount_direct') ?></td>
                                    <td class="text-right"><?= formatMoney($delivery['total_discount_direct']) ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('Chi phí công thêm', 'additional_costs') ?></td>
                                    <td class="text-right"><?= formatMoney($delivery['additional_costs']) ?></td>
                                </tr>
                                <tr class="success" style="font-weight: 700;">
                                    <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                    <td class="td-grand-total-all text-right"><?= formatMoney($delivery['grand_total']) ?></td>
                                </tr>
                                <tr class="success" style="font-weight: 700;">
                                    <td><?= lang('Tổng tiền(VND)', 'grand_total') ?></td>
                                    <td class="td-grand-total-all text-right"><?= formatMoney($delivery['grand_total'] * $_order['amount_to_vnd']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif ?>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($delivery['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($delivery['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            // scrollY: '300px',
            scrollY: true,
            scrollX: true,
            fixedColumns:   {
                leftColumns: 3,
                rightColumns: 0
            },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                pageTotalQuantity = api
                    .column( 8, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                pageTotalQuantityLoss = api
                    .column( 9, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageTotalQuantitySample = api
                    .column( 10, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageTotalAmount = api
                    .column( 13, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageGrandAmount = api
                    .column( 16, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );


                $( api.column( 8 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalQuantity)+'</div>');
                $( api.column( 9 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalQuantityLoss)+'</div>');
                $( api.column( 10 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalQuantitySample)+'</div>');
                $( api.column( 13 ).footer() ).html('<div class="text-right">'+tnhFormatMoney(pageTotalAmount)+'</div>');
                $( api.column( 16 ).footer() ).html('<div class="text-right">'+tnhFormatMoney(pageGrandAmount)+'</div>');
            }
        });

        setTimeout(function(){ dtItems.draw(); }, 1000);

        $('#tab1').click(function(event) {
            dtItems.draw();
        });

        if (flagView == 1) {
            oTable.draw('page');
        }
    });
</script>