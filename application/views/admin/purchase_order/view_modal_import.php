
<div class="modal fade" id="view_modal_import_ch" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('Thông tin đơn hàng mua');?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  pull-left">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?=_l('Thông tin đơn hàng mua')?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                          <div><?=format_purchase_order_father($items->id,'',true,'12px')?></div>
                                            <div>
                                              <b><?=_l('ch_code_p')?>: </b><?php echo $items->prefix.'-'.$items->code ?></div>
                                                <div><b><?=_l('ch_staff_crate_rfq')?>: </b><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                      'data-toggle' => 'tooltip',
                                                      'data-title' => get_staff_full_name($items->staff_create)
                                                  )).get_staff_full_name($items->staff_create)?></div>
                                                <div><b><?=_l('ch_date_p')?>: </b><?php echo _d($items->date)?></div>
                                                <div><b><?=_l('ch_delivery_date')?>: </b><?php echo _d($items->delivery_date)?></div>
                                            <p></p>
                                        </div>
                                        <div class="col-md-6">
                                           
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                  <table id="view-enquiry" class="table table-striped  item-inventory table-bordered" style="width: 100%;">
                      <thead>
                          <tr>
                            <th style="width: 20%;" class="text-center"><?=_l('image')?></th>
                            <th style="width: 20%;" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                            <th style="width: 20%;" class="text-center"><?= _l('quantity_order'); ?>
                            <th style="width: 20%;"class="text-center"><?php echo _l('ch_quantity_import'); ?></th>
                            <th style="width: 20%;" class="text-center"><?php echo _l('ch_total_left'); ?></th>
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
                              <img style="border-radius: 50%;width: 3em;height: 3em;" src="<?=$value['avatar']?>"><br>
                              <?=format_item_purchases($value['type'])?>
                            </td>
                            <td>
                                <?php echo $value['name_item'].' ('.$value['code_item'].')'; ?><br><?=format_item_color($value['product_id'],$value['type'])?>
                            </td>
                            <td class="center">
                              <?php echo number_format($value['quantity']); ?>
                            </td>
                            <td class="center">
                              <?php echo number_format($value['quantity_import']); ?>
                            </td>
                            <td class="center">
                              <?php echo number_format($value['quantity'] - $value['quantity_import']); ?>
                            </td>
                          </tr>
                        <?php } ?>
                    </tbody>
                  </table>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
      $(document).ready( function() {
          $('.tip').tooltip();
      });

        $(document).ready(function() {  
            var table = $('#view-enquiry').DataTable({
              responsive : true,
              "bLengthChange" : false,
                    "language": app.lang.datatables,
                    "pageLength": app.options.tables_pagination_limit,
                    "lengthMenu": [[10], [10]],
                    // scrollY:'450px',
                    scrollX:false,
                    // "sScrollXInner": "100%",
                    "initComplete": function(settings, json) {
                        var t = this;
                        t.parents('.table-loading').removeClass('table-loading');
                        t.removeClass('dt-table-loading');
                    },
                    "footerCallback": function ( row, data, start, end, display ) {
                    }
            });
        });

  </script>
