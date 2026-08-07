<div class="modal fade in" id="view_warranty_receive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('view_warranty'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <?php foreach ($results as $key => $value) { ?>
            <div class="col-md-6">
              <div class="lead-view" id="leadViewWrapper">
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop bold"><?= lang('code_warranty') ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?= $value['code'] ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date_machine') ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?= $value['date'] ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop bold"><?= lang('service_type') ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?= $value['service_type'] ?></span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="lead-view" id="leadViewWrapper">
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop bold"><?= lang('clients') ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?= $value['customer_id'] ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop bold"><?= lang('name_of_machine') ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?= $value['name_of_machine'] ?></span>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div class="col-md-12">
                <table class="tnh-tb table-bordered table-hover m-group0" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width: 10%;" class="text-center">NO.</th>
                            <th style="width: 10%;" class="text-center"><?=_l('image')?></th>
                            <th style="width: 20%;" class="text-center"><?=_l('series')?></th>
                            <th style="width: 20%;" class="text-center"><?=_l('tnh_product_name')?></th>
                            <th style="width: 20%;" class="text-center"><?=_l('count_warranty')?></th>
                            <th style="width: 20%;" class="text-center"><?=_l('Thời hạn bảo hành còn lại')?></th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($value['seriesItem'] as $keyItem => $valueItem) { ?>
                        <tr>
                          <td class="text-center"><?=++$keyItem?></td>
                          <td class="text-center">
                              <?=$valueItem['img_item']?>
                          </td>
                          <td class="text-center">
                              <?=$valueItem['id_series']?>
                          </td>
                          <td class="text-left">
                            <?=$valueItem['name_item']?>
                          </td>
                          <td class="text-left">
                            <?=$valueItem['strCount']?>
                          </td>
                          <td class="text-center">
                            <?=$valueItem['deadline_warranty']?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
</script>