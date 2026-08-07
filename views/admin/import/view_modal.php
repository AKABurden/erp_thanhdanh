  <style type="text/css">
      .tab-pane {
          display: none;
      }

      .tab-pane.active {
          display: block;
      }

      .img_ch {
          height: 25px;
          width: 25px;
      }
  </style>
  <div style="z-index: 999999999999;" class="modal fade in" id="views_import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
      <div class="modal-dialog " style="width: 90%;">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">
                      <span class="book-title"><?php echo _l('ch_import_t'); ?> </span>
                  </h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="info">
                              <?php
                                $type = '';
                                if (!isset($items))
                                    $type = 'warning';
                                elseif ($items->status == 1)
                                    $type = 'warning';
                                elseif ($items->status == 2)
                                    $type = 'danger';
                                elseif ($items->status == 3)
                                    $type = 'success';
                                ?>
                              <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                  <?php
                                    if (isset($items)) {
                                        $status = format_import_status($items->status, '', false);
                                    } else {
                                        $status = format_import_status(-1, '', false);
                                    }
                                    ?>
                                  <span><?= $status ?></span>
                              </div>
                              <div class="title-modal">
                                  <h3>Thông tin</h3>
                              </div>
                              <div class="body-modal">
                                  <div class="row-modal">
                                      <div class="row-group">
                                          <?php if (format_purchase_order_father($items->id_order, '', true, '12px')) { ?>
                                              <div class="row-contro">
                                                  <?= format_purchase_order_father($items->id_order, '', true, '12px') ?></div>
                                          <?php } ?>
                                          <div class="row-contro">
                                              <div><?= _l('ch_code_p') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $items->prefix . '-' . $items->code ?>
                                              </div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_date_p') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('warehouse') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $warehouse_name->name ?></div>
                                          </div>
                                      </div>
                                      <!--<div ./row-group >-->
                                      <div class="row-group">
                                          <?php
                                            $history_status = explode('|', $items->history_status);
                                            foreach ($history_status as $key => $value) {
                                                $data = explode(',', $value);
                                                if (is_numeric($data[0])) { ?>
                                                  <div class="row-contro">
                                                      <div><?= _l('ch_status_import') ?>: </div>
                                                      <div class="ml-at t-bold"><?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                    'data-toggle' => 'tooltip',
                                                                                    'data-title' => ' Vào lúc: ' . _dt($data[1])
                                                                                )) . get_staff_full_name($data[0]) ?>
                                                      </div>
                                                  </div>
                                          <?php
                                                }
                                            }
                                            ?>
                                          <div class="row-contro">
                                              <div><?= _l('ch_staff_crate_rfq') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                            'data-toggle' => 'tooltip',
                                                                            'data-title' => get_staff_full_name($items->staff_create)
                                                                        )) . get_staff_full_name($items->staff_create) ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_note_t') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                          </div>
                                          <?php if (!empty($items->invoice_id)) { ?>
                                              <?php
                                                $invoice_id = explode(',', $items->invoice_id);
                                                foreach ($invoice_id as $key => $value) { ?>
                                                  <?php $invoice = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row'); ?>
                                                  <?php if (count($invoice_id) == 1) {
                                                        echo '<div class="row-contro">
                                                        <div><b>' . _l('ch_code_invoice') . ': </b>' . $invoice->code_invoice . '
                                                        </div>
                                                    </div>
                                                        <div class="row-contro">
                                                        <div><b>' . _l('ch_date_invoice') . ':
                                                            </b>' . _d($invoice->date_invoice) . '</div>
                                                    </div>';
                                                    }else{
                                                        echo '<div class="row-contro">
                                                        <div><b>' . _l('ch_code_invoice') . ' ('.($key+1).'): </b>' . $invoice->code_invoice . '
                                                        </div>
                                                    </div>
                                                        <div class="row-contro">
                                                        <div><b>' . _l('ch_date_invoice') . ' ('.($key+1).'):
                                                            </b>' . _d($invoice->date_invoice) . '</div>
                                                    </div>';
                                                    } ?>
                                              <?php } ?>
                                          <?php } ?>
                                      </div>

                                      <div class="clearfix"></div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <?php
                        $customer_custom_fields = false;
                        if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'imports', 'active' => 1)) > 0) {
                            $customer_custom_fields = true;
                        }
                        ?>
                      <?php if ($customer_custom_fields) { ?>
                          <div class="col-md-6  pull-left">
                              <div class="panel panel-info">

                                  <div class="panel-heading">
                                      <h3 class="panel-title"><?php echo _l('custom_fields'); ?></h3>
                                  </div>
                                  <div class="panel-body">
                                      <div class="well well-sm">
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <?php $custom_fields = get_table_custom_fields('imports'); ?>
                                                  <?php
                                                    $custom_fields = get_custom_fields('imports', array('show_on_table' => 1));
                                                    foreach ($custom_fields as $field) { ?>
                                                      <div class="form-group border_ch">
                                                          <label class="form-label control-label ng-binding"><?php echo $field['name']; ?>:</label>
                                                          <span>
                                                              <?php $value = get_custom_field_value((isset($items) && isset($items->id) ? $items->id : ''), $field['id'], 'imports'); ?>
                                                              <strong class="ng-binding"><?php echo (isset($items) && $value != '' ? $value : '-') ?></strong>
                                                          </span>
                                                      </div>
                                                  <?php } ?>
                                              </div>
                                              <div class="clearfix"></div>
                                          </div>
                                          <div class="clearfix"></div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      <?php } ?>
                  </div>
                  <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active">
                          <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab">
                              <i class="icon-foso fal fa-info-circle"></i>
                              <?= _l('ch_information') ?>
                          </a>
                      </li>
                      <li role="presentation">
                          <a href="#tab_feedback" aria-controls="tab_feedback" role="tab" data-toggle="tab">
                              <i class="icon-foso fa fa-comments-o"></i>
                              <?= _l('FeedBack') ?>
                              <span class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span>
                          </a>
                      </li>
                      <li role="presentation">
                          <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab">
                              <i class="icon-foso fal fa-history"></i>
                              <?= _l('activity_log_puchases') ?>
                          </a>
                      </li>
                  </ul>
                  <div role="tabpanel" class="tab-pane active" id="item_info">
                      <?php
                        $totalQuantity = 0;
                        $totalQuantitystock = 0;
                        $totalQuantitypay = 0;
                        $total = 0;
                        if (isset($items->items) && (count($items->items) > 0)) { ?>
                          <div class="">
                              <table id="view-enquiry" class="table" style="width: 100%;">
                                  <thead>
                                      <tr>
                                          <th style="width: 100px;" class="text-center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                          <th style="width: 200px;" class="text-center"><?php echo _l('Mã hàng'); ?>
                                          </th>
                                          <th style="width: 200px;" class="text-center"><?php echo _l('ch_items_name_t'); ?>
                                          </th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('Lot'); ?></th>
                                          <th style="width: 200px;" class="text-center">
                                              <?php echo _l('warehouse_localtion'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('ch_items_date_use'); ?></th>
                                          <!-- <th style="width: 100px;" class="text-center"><?php echo _l('item_quantity_confirm'); ?></th> -->
                                          <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_standard'); ?>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_stock'); ?>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_payment'); ?>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('tnh_price_import'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('promotion_suppliers'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('tax'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('invoice_total'); ?></th>
                                          <th style="width: 200px;" class="text-center"><?php echo _l('note'); ?></th>
                                          <!-- <th class="text-center"><?php echo _l('Tác vụ'); ?></th> -->
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($items->items as $key => $value) { ?>
                                          <tr>
                                              <td style="width:80px">
                                                  <div>
                                                      <div class="preview_image text-center" style="width: 40px;float: left;margin-bottom:0;margin-top:0">
                                                          <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                              <div>
                                                                  <a href="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                      <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>">
                                                                  </a>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </td>
                                              <td style="width:100px">
                                                  <div>
                                                      <?php echo $value['code_item']; ?>
                                                  </div>
                                              </td>
                                              <td style="width:200px">
                                                  <div>
                                                      <?php echo $value['name_item']; ?><br><?= format_item_color($value['product_id'], $value['type']) ?>
                                                  </div>
                                              </td>
                                              <td style="width:100px" class="text-center">
                                                  <?php echo $value['lot_code']; ?>
                                              </td>
                                              <td style="width:100px">
                                                  <?php echo $value['localtion_name']; ?>
                                              </td>
                                              <td style="width:100px">
                                                  <div class="<?= ($value['type'] == 'tools' ? 'hide' : '') ?>" style="width: 120px;"><?= _l('ch_date_of_manufacture') ?>: <span style="color:red;"><?= _d($value['date_sx']); ?></span></div>
                                                  <div class="<?= ($value['type'] == 'tools' ? 'hide' : '') ?>"><?= _l('ch_items_dateed') ?>: <span style="color:red;"><?= _d($value['date_sd']); ?></span></div>
                                                  <div class="<?= ($value['type'] == 'tools' ? 'hide' : '') ?>"><?= _l('ch_items_date_use') ?>: <span style="color:red;"><?= $value['date_use']; ?></span></div>
                                              </td>
                                              <!-- <td class="center">
                                                  <?php echo formatNumber($value['quantity_net']); ?>
                                              </td> -->
                                              <td style="width:80px" class="center">
                                                  <?php echo formatNumber($value['quantity_unit']); ?>/<?= $value['unit'] ?>
                                              </td>
                                              <td style="width:80px" class="center">
                                                  <?php echo formatNumber($value['quantity_stock']); ?>/<?= $value['unit_name_stock'] ?>
                                              </td>
                                              <td style="width:80px" class="center">
                                                  <?php echo formatNumber($value['quantity_payment']); ?>/<?= $value['unit_name_payment'] ?>
                                              </td>
                                              <td class="text-right">
                                                  <?php echo formatNumber($value['price']); ?>
                                              </td>
                                              <td class="text-right">
                                                  <?php echo formatNumber($value['promotion_suppliers']); ?>
                                              </td>
                                              <td class="center">
                                                  <?php echo $value['tax_rate']; ?> %
                                              </td>
                                              <td class="text-right">
                                                  <?php echo formatNumber($value['amount']); ?>
                                              </td>
                                              <td class="text-left">
                                                  <?php echo $value['note']; ?>
                                              </td>
                                              <!-- <td class="text-center">
                                                  <a href="#" class="btn btn-success pull-right" onclick="barcode(<?= $items->id ?>,<?= $value['id'] ?>); return false;"><i class="fa fa-barcode"></i></a>
                                              </td> -->
                                          </tr>
                                      <?php
                                            $totalQuantity += $value['quantity_unit'];
                                            $totalQuantitystock += $value['quantity_stock'];
                                            $totalQuantitypay += $value['quantity_payment'];
                                            $total += $value['amount'];
                                        } ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php } ?>
                      <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                          <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                              <tbody>
                                  <tr class="success">
                                      <td><?= _l('item_quantity_all') ?> đơn vị chuẩn :<span class="pull-right"><?= formatNumber($totalQuantity) ?></span></td>
                                      <td><?= _l('item_quantity_all') ?> đơn vị lưu kho :<span class="pull-right"><?= formatNumber($totalQuantitystock) ?></span></td>
                                      <td><?= _l('item_quantity_all') ?> đơn vị thanh toán :<span class="pull-right"><?= formatNumber($totalQuantitypay) ?></span></td>

                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="tab_feedback">
                      <div class="col-md-12 mtop5">
                          <?php include_once(APPPATH . 'views/admin/feedback/import/feedback.php'); ?>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="item_activity">
                      <div class="activity-container">
                          <?php foreach ($dataLog as $key => $value) { ?>
                              <div class="feed-item">
                                  <div class="activity-text">
                                      <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?>
                                      <?= get_staff_full_name($value['staff_id']); ?>
                                  </div>
                                  <div class="activity-time">
                                      <?= time_ago($value['date']) ?> <span class="activity-module"><?= _l($value['table_obj']) ?></span>
                                  </div>
                                  <div>
                                      <?= $value['content'] ?>
                                  </div>
                              </div>
                          <?php } ?>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                  </div>
              </div>
          </div>
      </div>
      <script type="text/javascript">
          $(document).ready(function() {
              $('.tip').tooltip();
          });
          $('body').on('hidden.bs.modal', '#views_import', function() {
              $('#import_data').html('');
              tAPI.draw('page');
          });
          $(document).ready(function() {
              var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
              dtItems = $('#view-enquiry').DataTable({
                  "language": app.lang.datatables,
                  "pageLength": app.options.tables_pagination_limit,
                  "lengthMenu": [
                      [10, 25, 50, 100, -1],
                      [10, 25, 50, 100, "<?= lang('all') ?>"]
                  ],
                  // scrollY: '300px',
                  scrollX: true,
                  // fixedColumns:   {
                  //     leftColumns: 4,
                  //     rightColumns: 0
                  // },
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
                      var api = this.api(),
                          data;
                      var api = this.api(),
                          data;
                      pageGrandAmount = api
                          .column(7, {
                              page: 'current'
                          })
                          .data()
                          .reduce(function(a, b) {
                              return intVal(a) + Number(intVal(b));
                          }, 0);

                      $(api.column(7).footer()).html('<div class="text-right">' + formatNumber(
                          pageGrandAmount) + '</div>');
                  }
              });
              setTimeout(function() {
                  dtItems.draw('page');
              }, 150);
              <?php if (!has_permission('import', '', 'view_price')) { ?>
                  dtItems.columns(9).visible(false, false);
                  dtItems.columns(8).visible(false, false);
                  dtItems.columns(6).visible(false, false);
                  dtItems.columns(7).visible(false, false);
              <?php } ?>
          });
      </script>