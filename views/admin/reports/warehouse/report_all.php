<style type="text/css">
   .dataTables_filter {
      display: none;
   }
</style>
<div id="report_all_of_stock" class="hide">
   <div class="row">
      <div class="col-md-3">
         <?= lang('category', 'category_search') ?>
         <select name="category_search" id="category_search" data-placeholder="<?= lang('tnh_item_materials_category') ?>" class="modal-select2" style="width: 100%;">
            <option value=""></option>
            <?= recursiveCategoryItems() ?>
         </select>
      </div>
      <div class="col-md-3">
         <?php echo render_select('material_id', $material, array('id', 'name','code'), 'Nguyên vật liệu'); ?>
      </div>
   </div>
   <table class="table table table-striped table-warehouse-all-report">
      <thead>
         <tr class="bold" style="text-align: center;font-weight: bold;">
            <th style="text-align: center;"><?php echo ucwords(_l('dt_code_material')); ?></th>
            <th style="text-align: center;"><?php echo ucwords(_l('Tên NL')); ?></th>
            <th style="text-align: center;"><?php echo ucwords(_l('tnh_dvt')); ?></th>
            <?php foreach ($warehouse as $key => $value) { ?>
               <th style="text-align: center;"><?= $value['name']; ?></th>
            <?php } ?>
            <th style="text-align: center;"><?php echo ucwords(_l('dt_sum_stock')); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <tr>
            <td></td>
            <td></td>
            <td></td>
            <?php foreach ($warehouse as $key => $value) {
               echo "<td></td>";
            } ?>
            <td></td>
         </tr>
      </tfoot>
   </table>
</div>
<!-- <div id="return_suppliers_data"></div>
<div id="view_adjusted_data"></div>
<div id="export_different_data"></div>
<div id="view_transfer_data"></div> -->