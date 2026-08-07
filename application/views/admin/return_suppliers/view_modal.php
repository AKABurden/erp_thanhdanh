  <style type="text/css">
      .img_ch {
          height: 20px;
          width: 20px;
      }

      .tab-pane {
          display: none;
      }

      .tab-pane.active {
          display: block;
      }
  </style>
  <div class="modal fade in" id="return_suppliers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">
                      <span class="book-title"><?php echo _l('ch_return_supplierss_t'); ?> </span>
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
                                          <?php if (format_purchase_order_father($items->id, '', true, '12px')) { ?>
                                              <div class="row-contro"><?= format_purchase_order_father($items->id, '', true, '12px') ?></div>
                                          <?php } ?>
                                          <div class="row-contro">
                                              <div><?= _l('ch_code_p') ?>:</div>
                                              <div class="ml-at t-bold"><?php echo $items->prefix . $items->code ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_staff_crate_rfq') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                            'data-toggle' => 'tooltip',
                                                                            'data-title' => get_staff_full_name($items->staff_create)
                                                                        )) . get_staff_full_name($items->staff_create) ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_date_p') ?>:</div>
                                              <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('warehouse') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $warehouse_name->name ?></div>
                                          </div>
                                      </div><!-- ./row-group -->
                                      <div class="row-group">
                                          <?php
                                            $history_status = explode('|', $items->history_status);
                                            foreach ($history_status as $key => $value) {
                                                $data = explode(',', $value);
                                                if (is_numeric($data[0])) {
                                            ?>
                                                  <div class="row-contro">
                                                      <div><?= _l('ch_status_import') ?>:</div>
                                                      <div class="ml-at t-bold"><?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                    'data-toggle' => 'tooltip',
                                                                                    'data-title' => ' Vào lúc: ' . _dt($data[1])
                                                                                )) . get_staff_full_name($data[0]) ?></div>
                                                  </div>
                                          <?php
                                                }
                                            }
                                            ?>
                                          <div class="row-contro">
                                              <div><?= _l('ch_note_t') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                          </div>
                                      </div>
                                      <div class="clearfix"></div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active">
                          <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><i class="icon-foso fal fa-info-circle"></i><?= _l('ch_information') ?></a>
                      </li>
                      <li role="presentation">
                          <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><i class="icon-foso fal fa-history"></i><?= _l('activity_log_puchases') ?></a>
                      </li>
                  </ul>
                  <div role="tabpanel" class="tab-pane active" id="item_info">
                      <?php
                        $totalQuantity = 0;
                        $total = 0;
                        if (isset($items->items) && (count($items->items) > 0)) { ?>
                          <table id="view-enquiry" class="table" style="width: 100%;">
                              <thead>
                                  <tr>
                                      <th style="width: 100px;" class="center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                      <th style="width: 200px;" class="text-center"><?php echo _l('Mã hàng'); ?></th>
                                      <th style="width: 200px;" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                      <th style="width: 150px;" class="text-center"><?php echo _l('warehouse_localtion'); ?></th>
                                      <th style="width: 100px;" class="text-center"><?php echo _l('item_unit'); ?></th>
                                      <th style="width: 100px;" class="text-center"><?php echo _l('quantity'); ?></th>
                                      <th style="width: 70px;" class="text-center"><?php echo _l('SL DVTT'); ?></th>
                                      <th style="width: 100px;" class="text-center"><?php echo _l('cong_price_thinh'); ?></th>
                                      <th style="width: 100px;" class="text-center"><?php echo _l('invoice_total'); ?></th>
                                      <th style="width: 150px;" class="text-center"><?php echo _l('note'); ?></th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php foreach ($items->items as $key => $value) { 
                                    $totalQuantity+=$value['quantity_net'];
                                    $total+=$value['amount'];
                                    ?>
                                      <tr>
                                          <?php if ($value['avatar'] == '') {
                                                $value['avatar'] = 'uploads/no-img.jpg';
                                            }
                                            ?>
                                          <td class="center">
                                              <div class="preview_image text-center" style="width: 135px;margin-bottom:0;margin-top:0">
                                                  <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                      <div>
                                                          <a href="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                              <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>"><?= format_item_purchases($value['type']) ?>
                                                          </a>
                                                      </div>
                                                  </div>
                                              </div>
                                          </td>
                                          <td>
                                              <?= $value['code_item'] ?>
                                          </td>
                                          <td>
                                              <?php echo $value['name_item']; ?><br><?= format_item_color($value['product_id'], $value['type']) ?>
                                          </td>


                                          <td class="">
                                              <?= $value['localtion_name'] ?>
                                              <div style="font-size: 11px;font-style: italic;">
                                                  <?= _l('Lot') ?>:<?= $value['lot_code'] ?>
                                                  <?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                                      <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                                      <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
                                                  <?php } ?>
                                              </div>
                                          </td>

                                          <td><?= $value['unit_name_stock'] ?></td>

                                          <td class="center">
                                              <?= formatNumber($value['quantity_net']); ?><br>
                                              <!--                                                  <span style="font-size: 10px;color: red;font-weight: 500;">SL DVTT: --><? //= formatNumber($value['quantity_payment']); 
                                                                                                                                                                            ?><!--</span>-->
                                          </td>
                                          <td class="center">
                                              <span style="color: red;font-weight: 500;"><?= formatNumber($value['quantity_payment']); ?></span>
                                          </td>

                                          <td class="align_right">
                                              <?= number_format($value['price']); ?>
                                          </td>

                                          <td class="align_right">
                                              <?= number_format($value['amount']); ?>
                                          </td>

                                          <td>
                                              <?= $value['note'] ?>
                                          </td>
                                      </tr>
                                  <?php  } ?>
                              </tbody>
                          </table>
                      <?php } ?>
                      <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                          <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                              <tbody>
                                  <tr class="success">
                                      <td><?= _l('item_quantity_all') ?>:<span class="pull-right"><?= formatNumber($totalQuantity) ?></span></td>
                                      <td><?= _l('ch_all_total') ?>:<span class="pull-right"><?= number_format($total) ?></span></td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="item_activity">
                      <div class="activity-container">
                          <?php foreach ($dataLog as $key => $value) { ?>
                              <div class="feed-item">
                                  <div class="activity-text">
                                      <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
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
              $('.table-import').DataTable().ajax.reload();
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
                  // scrollY: '250px',
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

                      $(api.column(7).footer()).html('<div class="text-right">' + formatNumber(pageGrandAmount) + '</div>');
                  }
              });
              setTimeout(function() {
                  dtItems.draw('page');
              }, 150);
          });
      </script>