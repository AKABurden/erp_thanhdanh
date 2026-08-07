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
  <div class="modal fade in" id="view_export_different" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">
                      <span class="book-title"><?php echo _l('ch_export_different_t_ch'); ?> </span>
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
                                        $status = format_status_inventory($items->status, '', false);
                                    } else {
                                        $status = format_status_inventory(-1, '', false);
                                    }
                                    ?>
                                  <span><?= $status ?></span>
                              </div>
                              <div class="title-modal">
                                  <h3><?= _l('info') ?></h3>
                              </div>

                              <div class="body-modal">
                                  <div class="row-modal">
                                      <div class="row-group">
                                          <div class="row-contro">
                                              <div><?= _l('ch_code_p') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $items->prefix . '-' . $items->code ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_staff_crate_rfq') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_id, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                            'data-toggle' => 'tooltip',
                                                                            'data-title' => get_staff_full_name($items->staff_id)
                                                                        )) . get_staff_full_name($items->staff_id) ?></div>
                                          </div>
                                          <div class="row-contro">
                                              <div><?= _l('ch_date_p') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                          </div>
                                          <div class="row-contro">

                                              <div><?= _l('ch_note_t') ?>: </div>
                                              <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                          </div>
                                            <div class="row-contro hide">
                                                <div><?= lang('Lệnh sản xuất tổng') ?>: </div>
                                                <div class="ml-at t-bold">
                                                    <?php
                                                        $dtPO = $items->po_id ? get_table_where('tbl_productions_orders', ['id' => $items->po_id], '', 'row_array') : null;
                                                        if (!empty($dtPO)) {
                                                            echo $dtPO['reference_no'] ?? '';
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row-contro">
                                                <div><?= lang('Loại') ?>: </div>
                                                <div class="ml-at t-bold"><?= $items->type_po == 1 ? 'Xuất khuôn bể' : ($items->type_po == 2 ? 'Xuất kẽm' : '') ?></div>
                                            </div>
                                      </div>
                                      <div class="row-group">
                                          <?php
                                            $history_status = explode('|', $items->history_status);
                                            foreach ($history_status as $key => $value) {

                                                if ($key > 0) {
                                                    $data = explode(',', $value);
                                                    if (is_numeric($data[0])) {
                                            ?>
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
                                            }
                                            ?>
                                      </div>
                                      <div class="clearfix"></div>

                                  </div>
                              </div>
                          </div>
                      </div>

                  </div>
                  <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active">
                          <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><?= _l('ch_information') ?></a>
                      </li>
                      <li role="presentation">
                          <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><?= _l('activity_log_puchases') ?></a>
                      </li>
                  </ul>
                  <div role="tabpanel" class="tab-pane active" id="item_info">
                      <?php
                        $subtotal = 0;
                        if (isset($items->items) && (count($items->items) > 0)) { ?>
                          <div class="table-responsive">
                              <table id="view-enquiry" class="table" style="width: 100%; max-height: 400px !important;">
                                  <thead style="width: 100%;">
                                      <tr>
                                          <th style="width: 100px;" class="center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                          <th style="width: 200px;" class="text-center"><?php echo _l('Mã hàng'); ?></th>
                                          <th style="width: 200px;" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                          <th style="width: 150px;" class="text-center"><?php echo _l('warehouse'); ?></th>
                                          <th style="width: 150px;" class="text-center"><?php echo _l('warehouse_localtion'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo lang('Lệnh sản xuất tổng'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('item_unit'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('quantity'); ?></th>
                                          <th style="width: 70px;" class="text-center"><?php echo _l('SL DVTT'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('cong_price_thinh'); ?></th>
                                          <th style="width: 100px;" class="text-center"><?php echo _l('invoice_total'); ?></th>
                                          <th style="width: 150px;" class="text-center"><?php echo _l('note'); ?></th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($items->items as $key => $value) { ?>
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
                                                  <?= $value['warehouse_name'] ?>
                                                  <div style="font-size: 11px;font-style: italic;">
                                                      <?= _l('Lot') ?>:<?= $value['lot_code'] ?>
                                                      <?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                                          <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                                          <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
                                                      <?php } ?>
                                                  </div>
                                              </td>

                                              <td class="center">
                                                  <?= $value['localtion_name'] ?>
                                              </td>
                                                <td>
                                                    <?php 
                                                        $dtPO = $value['po_id'] ? get_table_where('tbl_productions_orders', ['id' => $value['po_id']], '', 'row_array') : null;
                                                        if (!empty($dtPO)) {
                                                            echo $dtPO['reference_no'] ?? '';
                                                        }
                                                    ?>
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
                                  <tfoot class="bold">
                                      <tr>
                                          <th class="text-center" style="text-transform: uppercase;" colspan="8"><?= lang('tnh_grand_total') ?></th>
                                          <th></th>
                                          <th></th>

                                      </tr>
                                  </tfoot>
                              </table>
                          </div>
                      <?php } ?>
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
                  <div class="clearfix"></div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                  </div>
              </div>
          </div>
      </div>
      <script type="text/javascript">
          $(document).ready(function() {
              $('.tip').tooltip();
          });

          $('body').on('hidden.bs.modal', '#view_export_different', function() {
              $('#export_different_data').html('');
              tAPI.draw('page');
          });
          var dtItems;

          function unformat_number(number) {
              var _number = 0;
              if (number) {
                  _number = number.replace(/[^\-\d\.]/g, '');
              }
              return _number;
          };

          function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
              nStr += '';
              x = nStr.split(decSeperate);
              x1 = x[0];
              x2 = x.length > 1 ? '.' + x[1] : '';
              x2 = x2.substr(0, 2);
              var rgx = /(\d+)(\d{3})/;
              while (rgx.test(x1)) {
                  x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
              }
              return x1 + x2;
          };
          $(document).ready(function() {
              var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
              dtItems = $('#view-enquiry').DataTable({
                  "language": app.lang.datatables,
                  "pageLength": -1,
                  "lengthMenu": [
                      [10, 25, 50, 100, -1],
                      [10, 25, 50, 100, "<?= lang('all') ?>"]
                  ],
                  'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                  buttons: get_datatable_buttons($('#view-enquiry')),
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
                          .column(10, {
                              page: 'current'
                          })
                          .data()
                          .reduce(function(a, b) {
                              return intVal(a) + Number(intVal(b));
                          }, 0);

                      $(api.column(8).footer()).html('<div class="text-right">' + formatNumber(pageGrandAmount) + '</div>');
                    }
              });
              setTimeout(function() {
                  dtItems.draw('page');
              }, 150);

          });
      </script>

  </div>