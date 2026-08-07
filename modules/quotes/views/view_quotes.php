<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<style type="text/css">
    #table-items_wrapper th:nth-child(5),
    #table-items_wrapper td:nth-child(5) {
        display: none !important;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('view_estimate') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $print = $this->perPrintQuotes ? '<a href="' . base_url('admin/quotes/print_pdf/' . $quote['id']) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('quotes') . '</a>' : '';

                    $edit = $this->perEditQuotes ? '<a href="' . base_url('admin/quotes/edit/' . $quote['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('quotes') . '</a>' : '';

                    $email = $this->perPrintQuotes ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotes/email_quotes/' . $quote['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-envelope-o"></i> ' . lang('Email') . ' ' . lang('quotes') . '</a>' : '';

                    $actions = '
                    <div class="dropdown pull-right mbot5">
                        <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        ' . lang('actions') . '
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                            <li>' . $edit . '</li>
                            <li>' . $email . '</li>
                            <li>' . $print . '</li>
                        </ul>
                    </div>';
                    echo $actions;
                    ?>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('date') ?>: </div>
                                    <div class="ml-at t-bold"><?= _dt($quote['date']) ?></div>
                                </div>

                                <div class="row-contro">
                                    <div><?= lang('tnh_reference_no_quote') ?>: </div>
                                    <div class="ml-at t-bold"><?= ($quote['reference_no']) ?></div>
                                </div>

                                <div class="row-contro">
                                    <div><?= lang('customers') ?>: </div>
                                    <div class="ml-at t-bold"><?= ($quote['type_customer'] == 'customers') ? (!empty($customer['company_short']) ? $customer['company_short'] : '') : (!empty($customer['name']) ? $customer['name'] : '') ?></div>
                                </div>

                                <div class="row-contro">
                                    <div><?= lang('tnh_status') ?>: </div>
                                    <div class="ml-at t-bold"><?= lang($quote['status']) ?></div>
                                </div>

                                <div class="row-contro">
                                    <div><?= lang('tnh_user_agree') ?>: </div>
                                    <div class="ml-at t-bold"><?= $user_status ?></div>
                                </div>

                                <div class="row-contro">
                                    <div><?= lang('tnh_date_agree') ?>: </div>
                                    <div class="ml-at t-bold"><?= _dt($quote['date_status']) ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_person_contact') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($person_contact) ? $person_contact['firstname'] . ' ' . $person_contact['lastname'] : '' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_address_delivery') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($address_delivery) ? $address_delivery['address'] : '' ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_delivery_term') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['delivery_term'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_ship_to') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['ship_to'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_payment_detail') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['payment_detail'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_payment_term') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['payment_term'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('Chi nhánh xưởng') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($quote['id_branch']) ? get_table_where('tblbranch', ['id' => $quote['id_branch']], '', 'row')->name : '' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_expiration_date') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($quote['expiration_date']) ? _d($quote['expiration_date']) : '' ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_bale_parameters') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['bale_parameters'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_currencies') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($currencies) ? $currencies['name'] : '' ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('Yêu cầu báo giá') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php
                                        $quotation_request = get_table_where('tblquotation_request', ['id' => $quote['quotation_request_id']], '', 'row_array', '', 'id, code');
                                        echo !empty($quotation_request) ? $quotation_request['code'] : '';
                                        ?>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('Báo giá lại') ?>: </div>
                                    <div class="ml-at t-bold"><?= $quote['is_quote_again'] == 1 ? 'Có' : '' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- ./.row-modal -->
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="dt-table table table-hover dont-responsive-table" style="max-height: 400px !important;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th class="text-center"><?= lang('tnh_images') ?></th>
                                            <th class="text-center"><?= lang('products') ?></th>
                                            <th class="text-center"><?= lang('Bảng giá công đoạn') ?></th>
                                            <th class="text-center"><?= lang('tnh_technical_explanation') ?></th>
                                            <th class="text-center"><?= lang('tnh_unit') ?></th>
                                            <th class="text-center"><?= lang('tnh_moq') ?></th>
                                            <th class="text-center"><?= lang('tnh_unit_price') ?></th>
                                            <th class="text-center"><?= lang('tnh_discount_percent') ?></th>
                                            <th class="text-center"><?= lang('tnh_leadtime') ?></th>
                                            <th class="text-center"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $body_items ?>
                                    </tbody>
                                    <tfoot class="hide">
                                        <tr class="bold">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($quote['id'], 'quotes');
                                    ?>
                                    <?php if (!empty($history)): ?>
                                        <?php foreach ($history as $key => $value): ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                    </div>
                                                    <div class="activity-time">
                                                        ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                    </div>
                                                    <div>
                                                        ' . $value['content'] . '
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
                <div class="col-md-12 hide">
                    <table class="tnh-table table">
                        <tr class="danger bold">
                            <td style="width: 10%;"><?= lang('tnh_total_quantity') ?></td>
                            <td style="width: 10%;" class="total-quantity text-center"><?= formatNumber($quote['total_quantity']) ?></td>
                            <td style="width: 10%;"><?= lang('tnh_total_amount') ?></td>
                            <td style="width: 10%;" class="total-amount text-right"><?= formatMoney($quote['total']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%;"><?= lang('tax', 'tax') ?></td>
                                <td class="text-right"><?= $quote['tax_name'] ?></td>
                            </tr>
                            <tr class="success" style="font-weight: 700;">
                                <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                <td class="td-grand-total-all text-right"><?= formatMoney($quote['grand_total']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row col-md-12">
                        <?= lang('note', 'note') ?>
                        <?= tnh_html_entity_decode($quote['note']) ?>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($quote['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($quote['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_quote_id" id="view_quote_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            // "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            scrollY: true,
            scrollX: true,
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                // var api = this.api(), data;
                // pageTotalQuantity = api
                //     .column( 5, { page: 'current'} )
                //     .data()
                //     .reduce( function (a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0 );

                // pageTotalAmount = api
                //     .column( 7, { page: 'current'} )
                //     .data()
                //     .reduce( function (a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0 );

                // $( api.column( 5 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalQuantity)+'</div>');
                // $( api.column( 7 ).footer() ).html('<div class="text-right">'+tnhFormatNumber(pageTotalAmount)+'</div>');
            }
        });
    });
</script>