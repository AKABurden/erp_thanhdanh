<?php
// $this->db->select('tbl_stages.*', false);
// $this->db->from('tbl_stages');
// $this->db->where('tbl_stages.parent_id', 0);
// $stages = $this->db->get()->result_array();
$stages = $this->manufactures_model->getStagesPO($id);
?>
<div class="col-md-7 task-single-col-left" style="min-height: 450px; max-height: 450px; background: #f7f8fa; padding: 10px;">
    <div class="div-items-products">
        <div class="panel panel-default" style="height: 430px;">
            <div class="panel-body p-0">
                <div class="bold"><?= lang('items') ?></div>
                <div class="dataTables_empty"><?= lang('Vui lòng chọn giai đoạn') ?></div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-5 task-single-col-right" style="max-height: 450px; min-height: 450px;">
    <h4 class="task-info-heading"><i class="fa fa-info-circle" aria-hidden="true"></i> <?= lang('tnh_cong_doan') ?></h4>
    <hr class="task-info-separator">
    <div class="task-info p-0" style="overflow: auto; max-height: 370px;">
        <?php if ($stages) : ?>
            <?php foreach ($stages as $key => $value) : ?>
                <div class="info-stages" <?= $value['id'] != STAGE_PRINT_BARCODE ? 'onclick="clickActiveStages(this, \'' . $value['id'] . '\')"' : '' ?>><?= $value['name'] ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    function clickActiveStages(_this, c_stage_id) {
        task_info = $(_this).closest('.task-info');
        dataPost = {};
        productions_orders_id = $('#productions_orders_id').val();
        dataPost[csrfData['token_name']] = csrfData['hash'];
        dataPost['stage_id'] = c_stage_id;
        dataPost['productions_orders_id'] = productions_orders_id;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/activeStages',
            data: dataPost,
            dataType: "html",
            success: function(response) {
                $('.div-items-products').html(response);
                task_info.find('.info-stages').removeClass('active');
                $(_this).addClass('active');
            }
        });
    }
    $(document).ready(function() {

    });
</script>