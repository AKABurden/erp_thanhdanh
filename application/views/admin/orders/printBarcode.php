<div id="PrintElem">
  <?php foreach($data as $key => $value) { ?>
    <?php if(!empty($value['code_items'])) { ?>
          <?php for($i = 0 ; $i < $quantity[$value['id']]; $i++) {?>
              <div class="img-barcode" style="width: 180px!important;">
                  <div style="text-align: center;margin-bottom: 3px;"><?=$value['reference_no']?>_<?=$value['category_code']?>_<?=$value['date_lo']?>_<?=$value['name_size']?>_<?=sprintf("%03s", ($i + 1)) ?></div>
                  <img style="margin-left: 30px;" src="<?=base_url('barcode/set_barcode/').$value['id'].'-'.$value['item_id']?>/0" />
                  <div style="text-align: center;margin-top: 3px;"><?=$value['referenceId_api']?>id</div>
              </div>
          <?php } ?>
    <?php } ?>
  <?php } ?>
</div>
<!-- <?=sprintf("%02s", $value['stt']) ?> -->
