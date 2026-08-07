<?php $folder = 'feedback/order_production_details'; ?>
<?php $class = 'order_production_details'; ?>
<?php $mC = '-order_production_details'; ?>
<?php $urlForm = 'feedback/add_feed_back_order_production_details'; ?>
<?php $urlRemove = 'feedback/remove_feed_back_order_production_details'; ?>

<?php $idComment =   'feedback_' . time(); ?>
<?php echo form_open_multipart(admin_url($urlForm), array(
    'id' => 'feedback-form' . $mC,
    'class' => 'dropzone dropzone-manual',
    'enctype' => 'multipart/form-data',
    'style' => 'min-height:auto;background-color:#fff;'
)); ?>


<div class="examples">
    <textarea name="comment_feedback" id="feedback_<?= time(); ?>" placeholder="Nhập @ , để nhắc tới tên cần tag" id="feedback_comment" rows="3" class="form-control ays-ignore mention-textarea"></textarea>
</div>
<!--<a data-toggle="collapse" class="pull-left mtop10 mbot10" data-target="#div_upload">File Đính Kèm</a>-->
<div class="clearfix"></div>
<!--<div id="div_upload" class="collapse">-->
<div id="div_upload">
    <div id="dropzoneFeedback<?= $mC ?>" class="dropzoneDragArea dz-default dz-message feedback-comment-dropzone">
        <span><?php echo _l('drop_files_here_to_upload'); ?></span>
    </div>
</div>
<div class="dropzone-task-comment-previews dropzone-previews"></div>
<button type="button" class="btn btn-warning pull-right mbot20" autocomplete="off" onclick="add_feedback(<?= !empty($production_detail) ? $production_detail['id'] : '' ?>);">
    Thêm bình luận
</button>
<?php echo form_close(); ?>

<div class="clearfix"></div>
<!-- content-item activity-feed  -->
<div id="comments" class="content-item feedback-<?= $class ?> data-feed-back-order_production_details-<?= !empty($production_detail) ? $production_detail['id'] : '' ?>">
    <?php if (!empty($feedback)) { ?>
        <?php foreach ($feedback as $key => $value) {
            $this->load->view('admin/' . $folder . '/comment_feedback', ['feedback' => $value]);
        } ?>
    <?php } ?>
</div>