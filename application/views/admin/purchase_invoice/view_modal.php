  <div class="modal fade in" id="view_purchase_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_po_t'); ?> </span>
            </h4>
          </div>
          <div class="modal-body">
            <div class="row">
                  <div class="col-md-6  pull-left">
                      <div class="panel panel-success">
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
                            $status = format_purchase_status($items->status, '', false);
                          }
                          else
                            {
                            $status = format_purchase_status(-1, '', false);
                          }
                          ?>
                        <span><?= $status ?></span>
                      </div>
                          <div class="panel-heading">
                              <h3 class="panel-title">Thông tin</h3>
                          </div>
                          <div class="panel-body">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                      <div><?=format_purchase_order_father($items->id,'',true,'12px')?></div>
                                        <div>
                                          <b><?=_l('ch_code_p')?>: </b><?php echo $items->prefix.'-'.$items->code ?></div>
                                            <div><b><?=_l('ch_staff_crate_rfq')?>: </b><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5'), 'small', array(
                                                  'data-toggle' => 'tooltip',
                                                  'data-title' => get_staff_full_name($items->staff_create)
                                              )).get_staff_full_name($items->staff_create)?></div>
                                            <div><b><?=_l('ch_date_p')?>: </b><?php echo _d($items->date)?></div>
                                            <div><b><?=_l('ch_note_t')?>: </b><?php echo $items->note?></div>
                                        <p></p>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                          $history_status = explode('|',$items->history_status);
                                          foreach ($history_status as $key => $value) {
                                              $data=explode(',',$value);
                                              if(is_numeric($data[0]))
                                              {
                                                  ?>
                                                  <div><b><?=_l('ch_status_import')?>: <?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => ' Vào lúc: '._dt($data[1])
                                                    )).get_staff_full_name($data[0])?>
                                                  </div>
                                                  <?php 
                                              }
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
                  <?php
                    $customer_custom_fields = false;
                    if(total_rows(db_prefix().'customfields',array('fieldto'=>'purchase_order','active'=>1)) > 0 ){
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
                                  <?php $custom_fields = get_table_custom_fields('purchase_order'); ?>
                                    <?php
                                    $custom_fields = get_custom_fields('purchase_order',array('show_on_table'=>1));
                                     foreach($custom_fields as $field){
                                      ?>
                                    <div class="form-group border_ch"> 
                                      <label class="form-label control-label ng-binding"><?php echo $field['name']; ?>:</label> 
                                      <span>
                                        <?php $value = get_custom_field_value((isset($items) && isset($items->id) ? $items->id : ''), $field['id'], 'purchase_order'); ?> 
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
              <?php if(isset($items->items)&&(count($items->items)>0)){ ?>
              <div class="table-responsive">
                  <table id="view-enquiry" class="table table-bordered table-hover table-color layout-fixed" width="2000">
                      <thead>
                          <tr>
                            <th width="100" class="center"><?=_l('image')?></th>
                            <th width="100"><?=_l('code_item_in_invoice')?></th>
                            <th width="100"><?php echo _l('status_item_in_internal'); ?></th>
                            <th width="100"><?php echo _l('status_item_in_suppliers'); ?></th>
                            <th width="250" class="text-left"><?php echo _l('ch_items_name_t'); ?></th>
                            <th width="100" class="text-center"><?= _l('item_quantity'); ?>
                            <th width="100" class="text-center"><?php echo _l('quantity_suppliers'); ?></th>
                            <th width="100" class="text-center"><?php echo _l('quantity_inventory_missing'); ?></th>
                            <th width="100" class="text-left"><?= _l('item_unit'); ?></th>
                            <th width="100" class="text-right"><?php echo _l('price_expected'); ?></th>
                            <th width="100" class="text-right"><?php echo _l('price_suppliers'); ?></th>
                            <th width="100" class="text-right"><?php echo _l('amount_expected_vnd'); ?></th>
                            <th width="100" class="text-right"><?php echo _l('promotion_suppliers'); ?></th>
                            <th width="100" class="text-center"><?= _l('tax'); ?></th>
                            <th width="100" class="text-right"><?php echo _l('amount_suppliers_vnd'); ?></th>
                            <th width="250" class="text-left"><?= _l('note'); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($items->items as $key => $value) { ?>
                          <tr>
                            <?php if($value['avatar'] == '') 
                              {
                                $value['avatar'] = 'uploads/no-img.jpg';
                              }
                            ?>
                            <td class="center">
                              <img style="width: 4em;height: 4em;" src="<?=$value['avatar']?>">
                              <?=format_item_purchases($value['type'])?>
                            </td>
                            <td class="center">???</td>
                            <td class="center">???</td>
                            <td class="center">???</td>
                            <td>
                                <?php echo $value['name_item'].' ('.$value['code_item'].')'; ?><br><?=format_item_color($value['product_id'],$value['type'])?>
                            </td>
                            <td class="center">
                              <?php echo number_format($value['quantity']); ?>
                            </td>
                            <td class="center">
                              <?php echo number_format($value['quantity_suppliers']); ?>
                            </td>
                            <td class="center">???</td>
                            <td><?=$value['unit']?></td>
                            <td class="align_right">
                              <?php echo number_format($value['price_expected']); ?>
                            </td>
                            <td class="align_right">
                              <?php echo number_format($value['price_suppliers']); ?>
                            </td>
                            <td class="align_right">
                              <?php echo number_format($value['total_expected']); ?>
                            </td>
                            <td class="align_right">
                              <?php echo number_format($value['promotion_expected']); ?>
                            </td>
                            <td class="center"><?=(number_format($value['tax_rate']))?> %</td>
                            <td class="align_right">
                              <?php echo number_format($value['total_suppliers']); ?>
                            </td>
                            <td>
                              <?php echo $value['note']; ?>
                            </td>
                          </tr>
                        <?php } ?>
                    </tbody>
                  </table>
              </div>
            <?php }?>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
              </div>
          </div>
      </div>
  </div>
  <script type="text/javascript">
      $(document).ready( function() {
          $('.tip').tooltip();
      });
  </script>

  </div>