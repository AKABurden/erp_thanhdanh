  <style type="text/css">
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
  </style>
  <div class="modal fade in" id="views_items" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: block;">
    <div class="modal-dialog modal-lg no-modal-header" style="width: 80%;">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_items_detail'); ?> </span>
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
                          if (isset($items))
                            {
                            $status = format_supplers_status($items->status, '', false);
                          }
                          else
                            {
                            $status = format_supplers_status(-1, '', false);
                          }
                          ?>
                        <span><?= $status ?></span>
                      </div>
                          <div class="title-modal">
                              <h3>Thông tin</h3>
                          </div>
                          <div class="body-modal">
                            <!-- <div> -->
                                <div class="row-modal">
                                    <div class="row-group">
                                        <div class="row-contro">
                                          <div><?=_l('ch_suppliers')?>: </div>
                                          <div class="ml-at t-bold"><?php echo get_supplier_full_name($items->suppliers_id) ?></div>
                                        </div>
                                        <div class="row-contro">
                                          <div><?=_l('ch_code_p')?>: </div>
                                          <div class="ml-at t-bold"><?php echo $items->prefix.'-'.$items->code ?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?=_l('ch_date_p')?>: </div>
                                            <div class="ml-at t-bold"><?php echo _d($items->date)?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?=_l('ch_note_t')?>: </div>
                                            <div class="ml-at t-bold"><?php echo $items->note?></div>
                                        </div>
                                        <p></p>
                                    </div>
                                    <div class="row-group">
                                      <div class="row-contro">
                                        <div><?=_l('ch_staff_crate_rfq')?>: </div>
                                        <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($items->staff_create)
                                          )).get_staff_full_name($items->staff_create)?>
                                          </div>
                                      </div>
                                       <?php
                                                $history_status = explode('|',$items->history_status);
                                                foreach ($history_status as $key => $value) {
                                                    $data=explode(',',$value);
                                                    if(is_numeric($data[0]))
                                                    {
                                                        ?>
                                                        <div class="row-contro">
                                                          <div><?=_l('ch_status_import')?>:</div>
                                                          <div class="ml-at t-bold"><?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                              'data-toggle' => 'tooltip',
                                                              'data-title' => ' Vào lúc: '._dt($data[1])
                                                          )).get_staff_full_name($data[0])?>
                                                          </div>
                                                        </div>
                                                        <?php 
                                                    }
                                                } ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            <!-- </div> -->
                          </div>
                      </div>
                  </div>
                  <?php
                    $customer_custom_fields = false;
                    if(total_rows(db_prefix().'customfields',array('fieldto'=>'supplier_quotes','active'=>1)) > 0 ){
                         $customer_custom_fields = true;
                       }
                    ?>
                <?php if($customer_custom_fields) { ?>
                <div class="col-md-6  pull-left">
                      <div class="panel panel-info">
                      
                          <div class="panel-heading">
                              <h3 class="panel-title"><?php echo _l('custom_fields'); ?></h3>
                          </div>
                          <div class="panel-body">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-6">
                        <?php $custom_fields = get_table_custom_fields('supplier_quotes'); ?>
                          <?php
                          $custom_fields = get_custom_fields('supplier_quotes',array('show_on_table'=>1));
                           foreach($custom_fields as $field){
                            ?>
                          <div class="form-group border_ch"> 
                            <label class="form-label control-label ng-binding"><?php echo $field['name']; ?>:</label> 
                            <span>
                              <?php $value = get_custom_field_value((isset($items) && isset($items->id) ? $items->id : ''), $field['id'], 'supplier_quotes'); ?> 
                              <strong class="ng-binding"><?php echo (isset($items) && $value != '' ? $value : '-') ?></strong>
                            </span> 
                          </div>
                          <?php 
                           }
                           ?>  
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
                    <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><i class="icon-foso fal fa-info-circle"></i><?=_l('ch_information')?></a>
                </li>
                <li role="presentation">
                    <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><i class="icon-foso fal fa-history"></i><?=_l('activity_log_puchases')?></a>
                </li>
              </ul>
              <div role="tabpanel" class="tab-pane active" id="item_info">
                <div class="table-responsive">
                    <table id="view-enquiry" class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="border-top text-center" ><?=_l('#')?></th>
                                <th class="border-top text-center"><?=_l('ch_image')?></th>
                                <th class="border-top text-center" style="width: 250px;"><?=_l('ch_items_name_t')?></th>
                                <th class="border-top text-center" style="width: 100px;"><?=_l('ch_color')?></th>
                                <th class="border-top text-center" style="width: 100px;"><?=_l('item_unit')?></th>
                                <th class="border-top text-center" style="width: 100px;"><?=_l('item_quantity')?></th>
                                <th class="border-top text-center" style="width: 150px;"><?=_l('price')?></th>
                                <th class="border-top text-center" style="width: 150px;"><?=_l('amount')?></th>
                                <th class="border-top text-center" style="width: 100px;"><?=_l('tax')?></th>
                                <th class="border-top text-center" style="width: 150px;"><?=_l('estimate_total')?></th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $totalQuantity=0;
                           if(isset($items->items)&&(count($items->items)>0)){
                            $totalQuantity_approve=0;
                            
                            ?>
                          <?php foreach ($items->items as $key => $value) { ?>
                            <tr>
                                <td class="text-center border-left"><?=($key+1)?></td>
                                <td class="text-center">
                                    <div  class="preview_image text-center" style="width: 135px;margin-bottom:0;margin-top:0">
                                    <div class="display-block contract-attachment-wrapper img-<?=$value['id']?>">
                                        <div>
                                            <a href="<?=(!empty($value['avatar'])?(file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/'.$value['avatar']) ? base_url('uploads/materials/'.$value['avatar']) : (file_exists('uploads/products/'.$value['avatar']) ? base_url('uploads/products/'.$value['avatar']) : (file_exists('uploads/tools_supplies/'.$value['avatar']) ? base_url('uploads/tools_supplies/'.$value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))):base_url('assets/images/preview-not-available.jpg'))?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                    <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?=(!empty($value['avatar'])?(file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/'.$value['avatar']) ? base_url('uploads/materials/'.$value['avatar']) : (file_exists('uploads/products/'.$value['avatar']) ? base_url('uploads/products/'.$value['avatar']) : (file_exists('uploads/tools_supplies/'.$value['avatar']) ? base_url('uploads/tools_supplies/'.$value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))):base_url('assets/images/preview-not-available.jpg'))?>">
                                                      <?=format_item_purchases($value['type'])?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                    
                                <td>
                                  <div><?=($value['name_item'])?> (<?=$value['code_item']?>)</div>
                                  
                                </td>
                                <td class="text-center"><?=format_item_color($value['product_id'],$value['type'],1)?></td>
                                <td class="text-center"><div><?=($value['unit'])?></div></td>
                                <td class="text-center"><?=(formatNumber($value['quantity']))?></td>
                                <td class="text-right"><?=(number_format($value['unit_cost']))?></td>
                                <td class="text-right"><?=(number_format($value['unit_cost']*$value['quantity']))?></td>
                                <td class="text-center"><?=(number_format($value['tax_rate']))?> %</td>
                                <td class="text-right"><?=(number_format($value['subtotal']))?></td>
                            </tr>
                          <?php 
                          $totalQuantity+=$value['quantity'];
                          }} ?>
                      </tbody>
                    </table>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tbody>
                          <tr class="success">
                            <td><?=_l('item_quantity_all')?>:<span class="pull-right"><?=formatNumber($totalQuantity)?></span></td>
                            <td><?=_l('estimate_discount')?>:<span class="pull-right"><?=number_format($items->discount)?></span></td>
                              <td><?=_l('ch_all_total')?>:<span class="pull-right"><?=number_format($items->subtotal)?></span></td>
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
                            <?= time_ago($value['date']) ?> <span class="activity-module"><?=_l($value['table_obj'])?></span>
                        </div>
                        <div>
                            <?=$value['content']?>
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
      $(document).ready( function() {
          $('.tip').tooltip();
          // $('#view-enquiry').dataTable();
      });
      $('body').on('hidden.bs.modal', '#views_items', function() {
      $('#view_supplier_quotes').html('');
      tAPI.draw('page');
      });
  $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        dtItems = $('#view-enquiry').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
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
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
              
            }
        });
  setTimeout(function(){dtItems.draw('page');}, 150);
    });   
  </script>
