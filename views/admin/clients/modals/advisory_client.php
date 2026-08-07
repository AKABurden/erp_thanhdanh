<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <?php echo form_open('admin/clients/advisory_client/' . $client, array('id' => 'form-advisory-client')); ?>
        <div class="modal-header">
            <button type="button" class="close close-advisory-client-modal" aria-label="Close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('set_advisory'); ?>" data-placement="bottom"></i>
                <?php echo $title; ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo form_hidden('id');
                    $type_advisory = [
                        ['id' => '1', 'name' => 'Ngày Tư Vấn Sau Khi Khách Nhận Được Vật Thỉnh'],
                        ['id' => '2', 'name' => 'Ngày Khách Hỏi Thêm'],
                        ['id' => '3', 'name' => 'Ngày Khách Thỉnh Thêm']
                    ];
                    $selected = (!empty($advisory->type) ? $advisory->type : '');
                    echo render_select('type', $type_advisory, ['id', 'name'], 'cong_advisory_client_type', $selected);

                    $value = (!empty($advisory->date) ? _d($advisory->date) : '');
                    echo render_date_input('date', 'cong_date_reality');

                    $value = (!empty($advisory->remind) ? _d($advisory->remind) : '');
                    echo render_date_input('remind', 'cong_date_remind_expected', $value);

                    $value = (!empty($advisory->cycle) ? $advisory->cycle : '');
                    echo render_input('cycle', 'cong_date_cycle', $value);
                    ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info">
                <?php echo _l('submit'); ?>
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <?php echo _l('close'); ?>
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
