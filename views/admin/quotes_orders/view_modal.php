  <style>
      img.c_img_item_30{
          border-radius: 50%;
          width: 30px;
          height: 30px;
      }
      .table-view_order tbody td{
          border-right: 2px solid!important;
      }
      .table-view_order tbody td:last-child{
          border-right: none!important;
      }
      .ribbon span{
         font-size: 7px;
      }
      #views_orders .well{
          margin-bottom: 0px;
      }
  </style>
    <div class="modal fade in" id="views_orders" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: none;">
      <div class="modal-dialog modal-lg no-modal-header" style="width: 80%;">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php
              $type = '';
              $name_status = _l('cong_new_create');
              if (!isset($quotes_orders)) {
                  $type = 'info';
                  $name_status = _l('cong_new_create');
              }
              elseif ($quotes_orders->status == 1) {
                  $type = 'success';
                  $name_status = _l('cong_have_create_orders');
              }
              elseif ($quotes_orders->status == 2){
                  $type = 'danger';
                  $name_status = _l('cong_have_cancel_quotes_orders');
              }
              ?>
            <h4 class="modal-title">
                <span class="book-title">
                    <?=!empty($title) ? $title : ''?>
                    <b class="text-<?=$type?>">
                        (<?= $name_status ?>)
                    </b>
                </span>
            </h4>
          </div>
          <div class="modal-body">
            <div class="row">
                  <div class="col-md-12  pull-left">
                      <div class="panel panel-success">
                      <div style="right: 10px;" class="ribbon <?= $type ?>">
                          <?php
                            $status = $name_status;
                          ?>
                        <span>
                            <?= $status ?>
                        </span>
                      </div>
                          <div class="panel-heading">
                              <h3 class="panel-title"><?= _l('cong_infomation_orders')?></h3>
                          </div>
                          <div class="panel-body">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>
                                            <p>
                                                <b><?=_l('cong_code_orders')?>: </b>
                                                <?php echo $quotes_orders->prefix.$quotes_orders->code ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <b><?=_l('cong_customer_orders')?>: </b>
                                                <?php echo $quotes_orders->company?>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <b><?=_l('cong_orders')?>: </b>
                                                <?php
                                                    if($quotes_orders->status == 1)
                                                    {
                                                        $orders = get_table_where('tblorders', ['id_quotes_orders' => $quotes_orders->id], '', 'row');
                                                        if(!empty($orders))
                                                        {
                                                            echo $orders->prefix.$orders->code;
                                                        }
                                                        else
                                                        {
                                                            echo _l('cong_not_found_orders');
                                                        }
                                                    }
                                                ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <b><?=_l('cong_note')?>: </b>
                                                <?php echo $quotes_orders->note?>
                                            </p>
                                        </div>
                                    </div>
                                    <md- class="col-md-6">
                                        <div class="col-md-4">
                                            <p>
                                                <b><?=_l('cong_create_by')?>: </b>
                                            </p>
                                        </div>
                                        <div class="col-md-8">
                                            <?php echo staff_profile_image( (!empty($quotes_orders->create_by) ? $quotes_orders->create_by : ''), [
                                                'staff-profile-image-small'], 'small',[
                                                'data-toggle' => 'tooltip',
                                                'data-title' => !empty($quotes_orders->create_by) ? get_staff_full_name($quotes_orders->create_by) : ''
                                            ])  ?>
                                        </div>

                                        <div class="clearfix"></div>

                                        <div class="col-md-4">
                                            <p><b><?=_l('cong_date_create')?>: </b></p>
                                        </div>
                                        <div class="col-md-8">
                                            <?php echo _dt($quotes_orders->date_create) ?>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-md-4">
                                            <p>
                                                <b><?=_l('cong_assigned')?>: </b>
                                            </p>
                                        </div>
                                        <div class="col-md-8">
                                            <?php echo staff_profile_image( (!empty($quotes_orders->assigned) ? $quotes_orders->assigned : ''), [
                                                'staff-profile-image-small'], 'small',[
                                                'data-toggle' => 'tooltip',
                                                'data-title' => !empty($quotes_orders->create_by) ? get_staff_full_name($quotes_orders->assigned) : ''
                                            ])  ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="table-responsive">
                      <table  class="table table-bordered table-hover table-striped table-color" style="width: 100%; margin-bottom: 5px;">
                            <thead>
                                <tr>
                                  <th><?=_l('cong_item_code')?></th> <!--mã hàng-->
                                  <th><?=_l('cong_item_image')?></th> <!--Hình ảnh-->
                                  <th><?=_l('cong_item_name')?></th> <!--Tên hàng-->
                                  <th><?=_l('cong_quantity')?></th> <!--Số lượng -->
                                  <th><?=_l('cong_price_thinh')?></th> <!--Giá thỉnh-->
                                  <th><?=_l('cong_discount')?></th> <!--Chiết khấu-->
                                  <th><?=_l('cong_cost_trans')?></th> <!--Phí vận chuyển-->
                                  <th><?=_l('cong_info_money')?></th> <!--Thành tiền-->
                                  <th><?=_l('cong_buy_gif')?></th> <!--Thành tiền-->
                                  <th><?=_l('cong_size')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php foreach($quotes_orders->detail as $key => $Vorder){?>
                                        <tr>
                                            <td>
                                                <p class="text-left"><?=$Vorder->code_items?></p>
                                                <?php if ($Vorder->type_items == "items"): ?>
                                                    <span class="label label-success"><?= lang('ch_items') ?></span>
                                                <?php elseif ($Vorder->type_items == "products"): ?>
                                                    <span class="label label-warning"><?= lang('tnh_products') ?></span>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <p class="text-center">
                                                    <img src="<?=base_url($Vorder->avatar);?>" class="c_img_item_30">
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-left"><?php echo !empty($Vorder->name) ? $Vorder->name : $Vorder->name_product;?></p>
                                            </td>
                                            <td>
                                                <p class="text-center"><?=number_format($Vorder->quantity);?></p>
                                            </td>
                                            <td>
                                                <p class="text-right"><?=number_format($Vorder->price)?></p>
                                            </td>
                                            <td>
                                                <?php if(!empty($Vorder->discount)){?>
                                                    <?= ($Vorder->type_discount == 1) ? ('<p class="text-center">'.number_format($Vorder->discount).' (%)</p>') : ('<p class="text-right">'.number_format($Vorder->discount).' ('._l('cong_money').')'.'</p>') ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <p class="text-right"><?=number_format($Vorder->cost_trans)?></p>
                                            </td>
                                            <td>
                                                <p class="text-right"><?=number_format($Vorder->grand_total)?></p>
                                            </td>
                                            <td>
                                                <?php
                                                if(!empty($Vorder->id_customer))
                                                {
                                                    $option_client = get_table_where(db_prefix().'clients', ['userid' => $Vorder->id_customer], '', 'row');
                                                    if(!empty($option_client))
                                                    {
                                                        echo '<p class="text-left"><a target="_blank" href="'.admin_url('clients/client/'.$option_client->userid).'">'.$option_client->company.'</a></p>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <p class="text-center"><?= $Vorder->size ?></p>
                                            </td>
                                        </tr>
                                    <?php }?>
                            </tbody>
                      </table>
                  </div>
                  <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                  <table class="table table-bordered table-view_order totals" style="margin-bottom:0;">
                        <tbody>
                            <tr class="success">
                                <td>
                                    <b><?=_l('cong_num_item_order')?>: </b>
                                    <span class="pull-right text-danger">
                                        <?=!empty($quotes_orders->total_item) ? number_format($quotes_orders->total_item) : '' ?>
                                    </span>
                                </td>
                                <td>
                                    <b><?=_l('cong_cost_trans')?>: </b>
                                    <span class="pull-right text-danger">
                                        <?=!empty($quotes_orders->total_cost_trans) ? number_format($quotes_orders->total_cost_trans) : '0' ?>
                                    </span>
                                </td>
                                <td>
                                    <b><?=_l('cong_total_orders')?>: </b>
                                    <span class="pull-right text-danger">
                                        <?=!empty($quotes_orders->grand_total) ? number_format($quotes_orders->grand_total) : '0' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr class="success">
                                <td>
                                    <b><?=_l('cong_type_pay')?>: </b>
                                    <span class="pull-right text-danger">
                                        <?=_l('cong_not_update')?>
                                    </span>
                                </td>
                                <td>
                                    <b><?=_l('cong_total_order_debt')?>:</b>
                                    <span class="pull-right text-danger">
                                        <?=_l('cong_not_update')?>
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                  </table>
              </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('cong_exit')?></button>
              </div>
          </div>
      </div>
      <script type="text/javascript">
          $(document).ready( function() {
              $('#views_orders').modal('show');
          });
      </script>

  </div>