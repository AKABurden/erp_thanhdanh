<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_evaluate') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($evaluate['date_evaluate']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_code_evaluate') ?>: </div>
                            <div class="ml-at t-bold"><?= $evaluate['code_evaluate'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_name_evaluate') ?>: </div>
                            <div class="ml-at t-bold"><?= $evaluate['name_evaluate'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_content_evaluate') ?>: </div>
                            <div class="ml-at t-bold"><?= $evaluate['content_evaluate'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php if(!empty($attachments)): ?>
                    <div class="row task_attachments_wrapper">
                        <div class="col-md-12" id="attachments">
                            <hr />
                            <h4 class="th font-medium mbot15"><?php echo _l('task_view_attachments'); ?></h4>
                            <div class="row">
                                <?php
                                $i = 1;
                                // Store all url related data here
                                $comments_attachments = array();
                                $attachments_data = array();
                                $show_more_link_task_attachments = hooks()->apply_filters('show_more_link_task_attachments', 2);
                                foreach ($attachments as $attachment) { ?>
                                    <?php ob_start(); ?>
                                    <div data-num="<?php echo $i; ?>" data-commentid="" data-comment-attachment="<?php echo $attachment['task_comment_id']; ?>" data-task-attachment-id="<?php echo $attachment['id']; ?>" class="task-attachment-col col-md-6<?php if ($i > $show_more_link_task_attachments) { echo ' hide task-attachment-col-more';} ?>">
                                        <ul class="list-unstyled task-attachment-wrapper" data-placement="right" data-toggle="tooltip" data-title="<?php echo $attachment['file_name']; ?>">
                                            <li class="mbot10 task-attachment<?php if (strtotime($attachment['dateadded']) >= strtotime('-16 hours')) { echo ' highlight-bg'; } ?>">
                                                <div class="mbot10 pull-right task-attachment-user">
                                                    <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                                        <a href="#" class="pull-right" onclick="remove_evaluate_attachment(this,<?php echo $attachment['id']; ?>); return false;">
                                                            <i class="fa fa fa-times"></i>
                                                        </a>
                                                    <?php }
                                                    $externalPreview = false;
                                                    $is_image = false;
                                                    $path = get_upload_path_by_type('evaluate') . $attachment['rel_id'] . '/' . $attachment['file_name'];
                                                    $href_url = site_url('download/file/evaluateattachment/' . $attachment['attachment_key']);
                                                    $isHtml5Video = is_html5_video($path);
                                                    $fileType = explode('/', $attachment['filetype']);
                                                    if (!empty($fileType[0]) && $fileType[0] == 'image') {
                                                        $is_image = is_image($path);
                                                        $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $attachment['filetype']);
                                                        $href_url = $img_url;
                                                    } else if (empty($attachment['external'])) {
                                                        $is_image = is_image($path);
                                                        $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $attachment['filetype']);
                                                    } else if ((!empty($attachment['thumbnail_link']) || !empty($attachment['external']))
                                                        && !empty($attachment['thumbnail_link'])
                                                    ) {
                                                        $is_image = true;
                                                        $img_url = optimize_dropbox_thumbnail($attachment['thumbnail_link']);
                                                        $externalPreview = $img_url;
                                                        $href_url = $attachment['external_link'];
                                                    } else if (!empty($attachment['external']) && empty($attachment['thumbnail_link'])) {
                                                        $href_url = $attachment['external_link'];
                                                    }
                                                    if (!empty($attachment['external']) && $attachment['external'] == 'dropbox' && $is_image) { ?>
                                                        <a href="<?php echo $href_url; ?>" target="_blank" class="" data-toggle="tooltip" data-title="<?php echo _l('open_in_dropbox'); ?>"><i class="fa fa-dropbox" aria-hidden="true"></i></a>
                                                    <?php } else if (!empty($attachment['external']) && $attachment['external'] == 'gdrive') { ?>
                                                        <a href="<?php echo $href_url; ?>" target="_blank" class="" data-toggle="tooltip" data-title="<?php echo _l('open_in_google'); ?>"><i class="fa fa-google" aria-hidden="true"></i></a>
                                                    <?php }
                                                    if ($attachment['staffid'] != 0) {
                                                        echo '<a href="' . admin_url('profile/' . $attachment['staffid']) . '" target="_blank">' . get_staff_full_name($attachment['staffid']) . '</a> - ';
                                                    } else if ($attachment['contact_id'] != 0) {
                                                        echo '<a href="' . admin_url('clients/client/' . get_user_id_by_contact_id($attachment['contact_id']) . '?contactid=' . $attachment['contact_id']) . '" target="_blank">' . get_contact_full_name($attachment['contact_id']) . '</a> - ';
                                                    }
                                                    echo '<span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($attachment['dateadded']) . '">' . time_ago($attachment['dateadded']) . '</span>';
                                                    ?>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="<?php if ($is_image) {
                                                                echo 'preview-image';
                                                            } else if (!$isHtml5Video) {
                                                                echo 'task-attachment-no-preview';
                                                            } ?>">
                                                    <?php
                                                    // Not link on video previews because on click on the video is opening new tab
                                                    if (!$isHtml5Video) { ?>
                                                        <a href="<?php echo (!$externalPreview ? $href_url : $externalPreview); ?>" target="_blank" <?php if ($is_image) { ?> data-lightbox="task-attachment" <?php } ?> class="<?php if ($isHtml5Video) { echo 'video-preview'; } ?>">
                                                        <?php } ?>
                                                        <?php if ($is_image) { ?>
                                                            <img src="<?php echo $img_url; ?>" class="img img-responsive">
                                                        <?php } else if ($isHtml5Video) { ?>
                                                            <video width="100%" height="100%" src="<?php echo site_url('download/preview_video?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>" controls>
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        <?php } else { ?>
                                                            <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                                                            <?php echo $attachment['file_name']; ?>
                                                        <?php } ?>
                                                        <?php if (!$isHtml5Video) { ?>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                                <div class="clearfix"></div>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                    $attachments_data[$attachment['id']] = ob_get_contents();
                                    if ($attachment['task_comment_id'] != 0) {
                                        $comments_attachments[$attachment['task_comment_id']][$attachment['id']] = $attachments_data[$attachment['id']];
                                    }
                                    ob_end_clean();
                                    echo $attachments_data[$attachment['id']];
                                    ?>
                                <?php
                                    $i++;
                                } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (($i - 1) > $show_more_link_task_attachments) { ?>
                            <div class="col-md-12" id="show-more-less-task-attachments-col">
                                <a href="#" class="task-attachments-more" onclick="slideToggle('.task_attachments_wrapper .task-attachment-col-more', task_attachments_toggle); return false;"><?php echo _l('show_more'); ?></a>
                                <a href="#" class="task-attachments-less hide" onclick="slideToggle('.task_attachments_wrapper .task-attachment-col-more', task_attachments_toggle); return false;"><?php echo _l('show_less'); ?></a>
                            </div>
                        <?php } ?>
                        <div class="col-md-12 text-center">
                            <!-- <hr />
                        <a href="<?php //echo admin_url('tasks/download_files/' . $task->id); 
                                    ?>" class="bold">
                            <?php //echo _l('download_all'); 
                            ?> (.zip)
                        </a> -->
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    function remove_evaluate_attachment(e, t) {
        confirm_delete() && requestGetJSON("evaluate/remove_evaluate_attachment/" + t).done(function (e) {
            !0 !== e.success && "true" != e.success || $('[data-task-attachment-id="' + t + '"]').remove(), _task_attachments_more_and_less_checks(), e.comment_removed && $("#comment_" + e.comment_removed).remove()
        })
    }
</script>