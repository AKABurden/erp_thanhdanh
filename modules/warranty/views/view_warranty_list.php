<style>
    .tab-pane{
        display: none;
    }
    .tab-pane.active{
        display: block;
    }
    .table-issue td {
      padding: 10px;
      border: 1px solid #cacaca !important;
    }
    .img-issue img {
        width: 100px;
        height: 80px;
    }
    .img-issue {
        position: relative;
        float: left;
        margin-top: 10px;
        margin-left: 10px;
    }

    .wrap-container-process {
        min-width: 575px;
    }
    .wrap-content-process {
        float: left;
        text-align: center;
        width: 110px;
    }
    .wrap-step-process {
        position: relative;
        width: 10px;
        margin: auto;
        height: 10px;
        background: #7b7b7b;
        border-radius: 50%;
    }
    .wrap-title-process {
        color: #676767;
        font-size: 10px;
    }
    .wrap-user-process {
        color: #676767;
        font-size: 10px;
    }
    .wrap-step-process.line:before {
        content: "";
        position: absolute;
        top: 40%;
        left: 10px;
        width: 110px;
        height: 2px;
        background: #7b7b7b;
    }
    .wrap-content-process.active .wrap-step-process {
        background: #55b776;
    }
    .wrap-content-process.active .wrap-step-process.line:before {
        background: #55b776;
    }
</style>
<div class="modal fade in" id="view_warranty_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('view_warranty_list'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="lead-view" id="leadViewWrapper">
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('clients') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $customer_name ?></span>
              </div>
              <div class="wap-content second">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('name_of_machine') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $contact ?></span>
              </div>
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('service_type') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $service_type ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="lead-view" id="leadViewWrapper">
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Mã phiếu bảo hành') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $code ?></span>
              </div>
              <div class="wap-content second">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_employees_charge') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $employees ?></span>
              </div>
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('localtion_warranty') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $localtion_warranty ?></span>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
          <br>
          <div class="col-md-12" style="display: flex; justify-content: center;">
            <div class="wrap-container-process">
              <div class="wrap-content-process active">
                  <div class="wrap-step-process line"></div>
                  <div class="wrap-title-process">
                      <?=_l('warranty_process_create')?>
                  </div>
              </div>
              <?php $check_export_supplies = get_table_where('tblwarranty_export_supplies',array('id_warranty'=>$id_warranty),'','row'); ?>
              <?php
                if($check_export_supplies) {
                    $detail_warranty = get_table_where('tblwarranty',array('id'=>$id_warranty),'','row');
                    $check_export_different = get_table_where('tblexport_different',array('id_warranty_export_supplies'=>$check_export_supplies->id),'','row');
                }
                $getAllItem = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_warranty));
                //check xuất kho
                $checkExportWarehouse = true;
                foreach ($getAllItem as $keyAllItem => $valueAllItem) {
                    if($valueAllItem['quantity'] > $valueAllItem['export_warehouse']) {
                        $checkExportWarehouse = false;
                        break;
                    }
                }
                if(!$getAllItem) {
                  $checkExportWarehouse = false;
                }
                //end
              ?>
              <div class="wrap-content-process <?= ($check_export_supplies ? 'active' : '') ?>">
                  <div class="wrap-step-process line"></div>
                  <div class="wrap-title-process">
                      <?=_l('warranty_process_export_supplies')?>
                  </div>
              </div>
              <div class="wrap-content-process <?= ($check_export_supplies && !empty($check_export_supplies->id_purchases) ? 'active' : '') ?> <?= ($checkExportWarehouse == true ? 'active' : '') ?>">
                  <div class="wrap-step-process line"></div>
                  <div class="wrap-title-process">
                      <?=_l('warranty_process_purchases')?>
                  </div>
              </div>
              <div class="wrap-content-process <?= ($checkExportWarehouse == true ? 'active' : '') ?>">
                  <div class="wrap-step-process line"></div>
                  <div class="wrap-title-process">
                      <?=_l('warranty_process_export_warehouse')?>
                  </div>
              </div>
              <div class="wrap-content-process <?= ($checkExportWarehouse == true && isset($detail_warranty) && $detail_warranty->status_done == 1 ? 'active' : '') ?>">
                  <div class="wrap-step-process"></div>
                  <div class="wrap-title-process">
                      <?=_l('warranty_process_done')?>
                  </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="clearfix"></div>
          <br>
          <!-- tab content -->
          <div class="col-md-12">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#issue_AND_solution" aria-controls="issue_AND_solution" role="tab" data-toggle="tab"><?=_l('issue_AND_solution')?></a>
                </li>
                <li role="presentation">
                    <a href="#expenses" aria-controls="expenses" role="tab" data-toggle="tab"><?=_l('client_expenses_tab')?></a>
                </li>
                <li role="presentation">
                    <a href="#supplies" aria-controls="supplies" role="tab" data-toggle="tab"><?=_l('supplies')?></a>
                </li>
            </ul>
            <div role="tabpanel" class="tab-pane active" id="issue_AND_solution">
              <div class="table-responsive">
                <table id="table-items" class="dt-table table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important;">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 5%;">STT</th>
                      <th class="text-center" style="width: 10%;"><?= lang('image') ?></th>
                      <th class="text-center" style="width: 20%;"><?= lang('tnh_product_code') ?></th>
                      <th class="text-center" style="width: 20%;"><?= lang('tnh_product_name') ?></th>
                      <th class="text-center" style="width: 15%;"><?= lang('series') ?></th>
                      <th class="text-center" style="width: 15%;"><?= lang('warranty_time') ?></th>
                      <th class="text-center" style="width: 15%;"><?= lang('warranty_end_time') ?></th>
                      <th class="hide"><?= lang('sub') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($dataITem as $key => $value) { ?>
                      <tr>
                        <td class="text-center details-control"></td>
                        <td class="text-center">
                            <?=$value['img_item']?>
                        </td>
                        <td class="text-left">
                            <?=$value['code_item']?>
                        </td>
                        <td class="text-left">
                            <?=$value['name_item']?>
                        </td>
                        <td class="text-center">
                            <?=$value['series']?>
                        </td>
                        <td class="text-center">
                            <?=$value['month_warranty']?>
                        </td>
                        <td class="text-center">
                            <?=$value['deadline_warranty']?>
                        </td>
                        <td class="hide">
                          <table class="table-issue tnh-table table-bordered" style="width: 90%; float: right;">
                            <body>
                              <tr>
                                  <td class="text-center" style="width: 5%;">STT</td>
                                  <td class="text-center" style="width: 20%;">Vấn đề</td>
                                  <td class="text-center" style="width: 20%;">Giải pháp</td>
                                  <td class="text-center" style="width: 55%;">Hình ảnh</td>
                              </tr>
                              <?php $getIssue = get_table_where('tblwarranty_issue',array('id_warranty_item'=>$value['id_warranty_item'])); ?>
                              <?php foreach ($getIssue as $keyIssue => $valueIssue) { ?>
                                <?php $getIssue_name = get_table_where('tblissue',array('id'=>$valueIssue['id_issue']),'','row'); ?>
                                <tr>
                                  <td class="text-center"><?= ++$keyIssue ?></td>
                                  <td><?= $getIssue_name->name ?></td>
                                  <td><?= $valueIssue['solution'] ?></td>
                                  <td>
                                    <div class="data-file">
                                        <?php $get_file = get_table_where('tblwarranty_file',array('id_warranty_issue'=>$valueIssue['id'])); ?>
                                        <?php foreach ($get_file as $key_file => $value_file) { ?>
                                            <div class="img-issue">
                                                <img src="<?= base_url('modules/warranty/uploads/warranty/'.$valueIssue['id'].'/'.$value_file['name']); ?>">
                                            </div>
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                    </div>
                                  </td>
                                </tr>
                              <?php } ?>
                            </body>
                          </table>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <?php $totalAll = 0; ?>
                    <tr>
                      <td class="text-left bold"><?= _l('total_c') ?></td>
                      <td class="text-right bold" colspan="6">
                        <?php
                          $total = 0;
                          foreach ($expenses as $key => $value) {
                            $total += $value['amount'];
                          }
                          $totalAll += $total;
                          echo number_format($total);
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-left bold"><?= _l('total_v') ?></td>
                      <td class="text-right bold" colspan="6">
                        <?php
                          $total = 0;
                          foreach ($supplies as $key => $value) {
                            $total += $value['amount'] * $value['quantity'];
                          }
                          $totalAll += $total;
                          echo number_format($total);
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-left bold"><?= _l('total') ?></td>
                      <td class="text-right bold" colspan="6">
                        <?php
                          echo number_format($totalAll);
                        ?>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="expenses">
              <div class="table-responsive">
                <table id="table-expenses" class="dt-table table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important;">
                  <thead>
                      <tr>
                          <th style="width: 1%;" class="text-center">
                              STT
                          </th>
                          <th style="width: 59%;" class="text-center"><?=_l('name_expenses')?></th>
                          <th style="width: 20%;" class="text-center"><?=_l('ch_costs')?></th>
                          <th style="width: 20%;" class="text-center"><?=_l('amount_expenses')?></th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($expenses as $key => $value) { ?>
                        <tr>
                            <td class="text-center stt">
                                <?=++$key?>
                            </td>
                            <td class="text-left">
                                  <?php $get_costs = get_table_where('tblcosts',array('id'=>$value['name']),'','row'); ?>
                                <div class="input-group">
                                    <span><?= $get_costs->name ?></span>
                                </div>
                            </td>
                            <td class="text-left">
                                <span><?=($value['type'] == 1 ? 'Khách chịu' : 'Công ty chịu')?></span>
                            </td>
                            <td class="text-right">
                                <?=number_format($value['amount'])?>
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                      <tr>
                          <th class="bold text-left">Tổng cộng:</th>
                          <th></th>
                          <th></th>
                          <th class="bold text-right"></th>
                      </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="supplies">
              <div class="table-responsive">
                <table id="table-supplies" class="dt-table table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important;">
                  <thead>
                      <tr>
                          <th style="width: 5%;" class="text-center">
                              STT
                          </th>
                          <th style="width: 5%;" class="text-center"><?=_l('image')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('code_supplies')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('name_supplies')?></th>
                          <th style="width: 10%;" class="text-center"><?=_l('item_quantity')?></th>
                          <th style="width: 10%;" class="text-center"><?=_l('ch_costs')?></th>
                          <th style="width: 10%;" class="text-center"><?=_l('ch_price')?></th>
                          <th style="width: 10%;" class="text-center"><?=_l('tnh_subtotal')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('note')?></th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($supplies as $key => $value) { ?>
                        <?php
                            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
                            if($value['type_item'] == 'materials') {
                                $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                                $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
                                if($getDetail && !empty($getDetail->images)) {
                                    $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
                                }
                            }
                            else if($value['type_item'] == 'supplies') {
                                $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                                $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
                                if($getDetail && !empty($getDetail->images)) {
                                    $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
                                }
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?=++$key?></td>
                            <td class="text-center">
                                <?=$img?>
                            </td>
                            <td class="text-left">
                                <?=$getDetail->code?>
                                <?php if($value['additional_supplies'] == 1) { ?>
                                  <br><span class="label label-primary">Vật tư bổ sung</span>
                                <?php } ?>
                            </td>
                            <td class="text-left">
                                <?=$name?>
                            </td>
                            <td class="text-center">
                                <?=$value['quantity']?>
                            </td>
                            <td class="text-center">
                                <span><?=($value['type_amount'] == 1 ? 'Hỗ trợ' : 'Tính phí')?></span>
                            </td>
                            <td class="text-right">
                                <?=number_format($value['amount'])?>
                            </td>
                            <td class="text-right"><?=number_format($value['total'])?></td>
                            <td class="text-center">
                                <?=$value['note']?>
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                        <th class="bold text-left">Tổng cộng:</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center bold"></th>
                        <th></th>
                        <th class="text-right bold"></th>
                        <th class="text-right bold"></th>
                        <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <!-- end -->
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
      nStr += '';
      x = nStr.split(decSeperate);
      x1 = x[0];
      x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
      }
      return x1 + x2;
  }
  function unformatNumber(nStr, decSeperate=".", groupSeperate=",") {
      return nStr.replace(/\,/g,'');
  }

  $(document).ready(function() {
    var arr = [];
    function formatProductionsOrders(d) {
        sub = d[7];
        return sub;
    }
    var dtItems = $('#table-items').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      // scrollY: true,
      // scrollX: true,
      "initComplete": function(settings, json) {
          var t = this;
          t.parents('.table-loading').removeClass('table-loading');
          t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
      }
    });

    $('#table-items tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var records = tr.find('#records').val();
        var row = dtItems.row( tr );

        if ( row.child.isShown() ) {
            arr = removeArray(arr, records);
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            if (!arr.includes(records)) {
                arr.push(records);
            }
            row.child( formatProductionsOrders(row.data()) ).show();
            tr.addClass('shown');
        }
    });

    $('#table-expenses').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "initComplete": function(settings, json) {
          var t = this;
          t.parents('.table-loading').removeClass('table-loading');
          t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;
        pageTotalQuantity = api
            .column( 3, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return Number(intVal(a)) + Number(intVal(b));
            }, 0 );

        $( api.column( 3 ).footer() ).html(formatNumber(pageTotalQuantity));
      }
    });

    $('#table-supplies').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "initComplete": function(settings, json) {
          var t = this;
          t.parents('.table-loading').removeClass('table-loading');
          t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;
        pageTotalQuantity = api
            .column( 4, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return Number(intVal(a)) + Number(intVal(b));
            }, 0 );

        pageTotalAmount = api
            .column( 6, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return Number(intVal(a)) + Number(intVal(b));
            }, 0 );

        pageTotal = api
            .column( 7, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return Number(intVal(a)) + Number(intVal(b));
            }, 0 );

        $( api.column( 4 ).footer() ).html(formatNumber(pageTotalQuantity));
        $( api.column( 6 ).footer() ).html(formatNumber(pageTotalAmount));
        $( api.column( 7 ).footer() ).html(formatNumber(pageTotal));
      }
    });
  });
</script>