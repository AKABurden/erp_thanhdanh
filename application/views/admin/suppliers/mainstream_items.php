<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
      .table-mainstream_items tbody tr:first-child td {
        max-width: 300px;
        white-space: inherit;
        min-width: 300px;
      }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
              <a class="btn btn-info test H_action_button">
                 <?php echo _l('Export excel'); ?></a>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
    </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                    <?php render_datatable(array(
                    _l('supplier'),  
                    _l('item_code'),
                    _l('item_name'),
                    ),
                    'mainstream_items'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
  $(function(){
    var notSortableAndSearchableItemColumns = [];
      initDataTable('.table-mainstream_items','<?=admin_url('suppliers/table_mainstream_items_all/')?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined','');
    });
</script>
</body>
</html>
