<style type="text/css">
.table-view_pay_slip thead tr th {
    text-align: center;
}

.table-view_pay_slip tr td:nth-child(1) {
    min-width: 50px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(2) {
    min-width: 110px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(3) {
    min-width: 200px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(4) {
    min-width: 80px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(5) {
    min-width: 90px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(6) {
    min-width: 90px;
    white-space: unset;
}

.table-view_pay_slip tr td:nth-child(7) {
    min-width: 90px;
    white-space: unset;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}
</style>
<div class="modal fade in" id="view_pay_slip" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_pay_slip_t'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  pull-left">
                        <div class="panel panel-success">
                            <?php 
                      $type = '';
                      if (!isset($items))
                        $type = 'warning';
                      elseif ($items->status == 0)
                        $type = 'warning';
                      elseif ($items->status == 2)
                        $type = 'danger';
                      elseif ($items->status == 1)
                        $type = 'info';
                      ?>
                            <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                <?php 
                          if (isset($items))
                            {
                            $status = format_status_pay_slip_s($items->status, '', false);
                          }
                          ?>
                                <span><?= $status ?></span>
                            </div>
                            <div class="panel-heading">
                                <h3 class="panel-title"><?=_l('ch_information_t')?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div>
                                                <b><?=_l('ch_code_p')?>:
                                                </b><?php echo $items->prefix.'-'.$items->code ?>
                                            </div>
                                            <div><b><?=_l('ch_staff_crate_rfq')?>: </b><?php echo staff_profile_image($items->staff_id, array('staff-profile-image-small mright5'), 'small', array(
                                                  'data-toggle' => 'tooltip',
                                                  'data-title' => get_staff_full_name($items->staff_id)
                                              )).get_staff_full_name($items->staff_id)?></div>
                                            <div><b><?=_l('ch_date_p')?>: </b><?php echo _d($items->day_vouchers)?>
                                            </div>
                                            <div><b><?=_l('ch_note_pay_slip')?>: </b><?php echo $items->note?></div>
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
                </div>
                <?php 
              $total = 0;
              $j=0;
              if(isset($items->item)&&(count($items->item)>0)){
               ?>
                <?php foreach ($items->item as $key => $value) {
                $total+=$value['total'];
                $j++;
                 ?>
                <?php } ?>
                <?php }?>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;margin-top:0;">
                        <thead>
                            <tr class="success">
                                <th><?=_l('ch_all_total')?>:<span class="pull-right"><?=number_format($total)?></span>
                                </th>
                                <th><?=_l('ch_status_pays_slip')?>:<span
                                        class="pull-right"><?=number_format($items->payment)?></span></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#item_info" aria-controls="item_info" role="tab"
                            data-toggle="tab"><?=_l('ch_information')?></a>
                    </li>
                    <li role="presentation">
                        <a href="#item_activity" aria-controls="item_activity" role="tab"
                            data-toggle="tab"><?=_l('activity_log_puchases')?></a>
                    </li>
                </ul>
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <input type="hidden" id="view" name="view" value="," />
                    <input type="hidden" id="type" name="type" value="<?= $items->type ?>" />
                    <div class="clearfix mtop20"></div>
                    <?php if($items->type == 5){ ?>
                    <?php $table_data = array(
                      _l('#'),
                      _l('ch_import_old'),
                      _l('tnh_items'),
                      _l('Số lượng'),
                      _l('Giá'),
                      _l('invoice_payments_table_amount_heading'),
                    );
                    render_datatable($table_data,'view_pay_slip');
                  ?>
                    <?php }else{ ?>
                    <?php $table_data = array(
                      _l('#'),
                      _l('ch_import_old'),
                      _l('tnh_items'),
                      _l('invoice_table_quantity_heading'),
                      _l('ch_value'),
                      _l('invoice_table_tax_heading'),
                      _l('promotion_suppliers'),
                      _l('invoice_payments_table_amount_heading'),
                    );
                    render_datatable($table_data,'view_pay_slip');
                  ?>
                    <?php } ?>
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
                                <?= time_ago($value['date']) ?> <span
                                    class="activity-module"><?=_l($value['table_obj'])?></span>
                            </div>
                            <div>
                                <?=$value['content']?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.tip').tooltip();
    });
    var tAPI;
    $(function() {
        var type = $("#type").val();
        var CustomersServerParams = {
            'view': '[name="view"]',
            'type': '[name="type"]',
        };
        if ($.fn.DataTable.isDataTable('.table-view_pay_slip')) {
            $('.table-view_pay_slip').DataTable().destroy();
        }
        tAPI = initDataTable('.table-view_pay_slip', admin_url + 'pay_slip/view_pay_slip/' + <?=$items->id?> +
            '/' + <?=$items->type?>, [0], [0], CustomersServerParams, [0, 'desc']);
        if (type == 5) {
            tAPI.columns(2).visible(false, false);
            tAPI.columns(3).visible(false, false);
            tAPI.columns(4).visible(false, false);
        } else {
            tAPI.columns(2).visible(false, false);
            tAPI.columns(3).visible(false, false);
            tAPI.columns(4).visible(false, false);
            tAPI.columns(5).visible(false, false);
            tAPI.columns(6).visible(false, false);
        }

        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.ajax.reload();
            });
        });
    });

    function view(id, type_old = '') {
        var view = $('[name="view"]').val();
        view = view + id + ',';
        $('[name="view"]').val(view);
        if (type_old == 5) {
            tAPI.columns(2).visible(true, true);
            tAPI.columns(3).visible(true, true);
            tAPI.columns(4).visible(true, true);
        } else {
            tAPI.columns(2).visible(true, true);
            tAPI.columns(3).visible(true, true);
            tAPI.columns(4).visible(true, true);
            tAPI.columns(5).visible(true, true);
            tAPI.columns(6).visible(true, true);
        }
        $('.table-view_pay_slip').DataTable().ajax.reload();
    }

    function no_view(id, type_old = '') {
        var view = $('[name="view"]').val();
        view = view.replace(',' + id + ',', ',');
        $('[name="view"]').val(view);
        if (view == ',') {
            if (type_old == 5) {
                tAPI.columns(2).visible(false, false);
                tAPI.columns(3).visible(false, false);
                tAPI.columns(4).visible(false, false);
            } else {
                tAPI.columns(2).visible(false, false);
                tAPI.columns(3).visible(false, false);
                tAPI.columns(4).visible(false, false);
                tAPI.columns(5).visible(false, false);
                tAPI.columns(6).visible(false, false);
            }
        }
        $('.table-view_pay_slip').DataTable().ajax.reload();
    }
    </script>