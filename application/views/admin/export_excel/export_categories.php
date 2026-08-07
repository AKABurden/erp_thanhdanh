<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="export_excel_categories" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bala.DualSelectList.css" xmlns="http://www.w3.org/1999/html">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('export_excel/action_export_categories'), array('id' => 'export_form', 'enctype' => 'multipart/form-data')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= _l('Xuất dữ liệu nhà cung cấp') ?></h4>
            </div>
            <div class="modal-body">
                <select class="" name="list_colum[]" id="list_colum" multiple="multiple"></select>
                <div id="dualSelectExample"></div><br>
                <?php
                    $listArray = [];
                ?>
                <?php $colum_fields_client = get_fields_export_excel_categories_hau();

                 ?>
                <?php
                foreach ($colum_fields_client['colum_categories'] as $key => $value) {
                    $listArray[] = [$value => mb_strtoupper(_l('ch_cate' . $value), 'UTF-8')];
                } ?>
                 ?>



            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="submit">
                    <?= _l('cong_export_file') ?>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
    <script src="<?= base_url() ?>assets/js/bala.DualSelectList.jquery.js"></script>

    <script>
        $(document).ready(function(){
            var dsl = $('#dualSelectExample').DualSelectList({
                // 'candidateItems' : [{'item1' : 'item1'},{'item2' : 'item2'},{'item3' : 'item3'}],
                'candidateItems' : <?=json_encode($listArray);?>,
                'selectionItems' : [],
                'idSelect':'#list_colum',
                'css_dsl_panel':'overflow: auto;height: 400px;',
                // 'colors' : {
                //     'itemText' : 'white',
                //     'itemBackground' : 'rgb(0, 51, 204)',
                //     'itemHoverBackground' : '#0066ff'
                // }
            });
        });
    </script>
</div>

