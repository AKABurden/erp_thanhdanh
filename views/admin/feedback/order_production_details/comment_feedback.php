<?php $folder = 'order_production_details_feedback'; ?>
<?php if (!empty($feedback)) { ?>
    <?php 
        $staff = get_table_where('tblstaff', ['staffid' => $feedback->create_by], '', 'row_array', '', 'firstname, lastname, staffid, profile_image');
        $fullnameStaff = $staff['firstname'].' '.$staff['lastname'];
        $staffImage = base_url('assets/images/user-placeholder.jpg');
        if (!empty($staff['profile_image'])) {
            $staffImage = base_url('uploads/staff_profile_images/'.$staff['staffid'].'/small_'.$staff['profile_image']);
        }
    ?>
    <div class="media" data-feed-back-order_production_details="<?= $feedback->id ?>">
        <a class="pull-left" href="<?= admin_url('profile/' . $feedback->create_by) ?>" target="_blank">
            <img class="media-object" data-toggle="tooltip" data-title="<?= $fullnameStaff ?>" src="<?= $staffImage ?>" alt="">
        </a>
        <div class="media-body">
            <h4 class="media-heading"><?= $fullnameStaff ?></h4>
            <p><?= $feedback->feedback ?></p>
            <?php if (!empty($feedback->file)) {
                foreach ($feedback->file as $keyFile => $valFile) { ?>
                    <?php if (explode('/', $valFile->filetype)[0] == 'image') { ?>
                        <div class="preview_image" style="width: auto; margin-top: 15px; margin-bottom: 15px;" >
                            <div class="display-block contract-attachment-wrapper img">
                                <div style="width:150px;">
                                    <a href="<?= base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                        <div class="">
                                            <img src="<?= base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>" style="max-height: 100px; width: 50px;">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div><a target="_blank" href="<?= base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a></div>
                    <?php } ?>
            <?php }
            }
            ?>
            <ul class="list-unstyled list-inline media-detail pull-left">
                <li class="p0"><?= _dt($feedback->date_create) ?></li>
                <!-- <li><i class="fa fa-thumbs-up"></i>13</li> -->
            </ul>
            <ul class="list-unstyled list-inline media-detail pull-right">
                <?php if (is_admin() || $feedback->create_by == get_staff_user_id()) { ?>
                    <a type="button" onclick="removeFeedBack(<?= $feedback->id ?>)" class="text-danger"><i class="fa fa-remove"></i></a>
                <?php } ?>
                <!-- <li class=""><a href="">Reply</a></li> -->
            </ul>
        </div>
    </div>

    <!-- <div data-feed-back-order_production_details="<?= $feedback->id ?>" class="tc-content task-comment feed-item hide">
        <a href="<?= ''//admin_url('profile/' . $feedback->create_by) ?>" target="_blank">
            <?php 
            // echo staff_profile_image($feedback->create_by, array('staff-profile-image-small mright5'), 'small', array(
            //     'data-toggle' => 'tooltip',
            //     'data-title' => $fullnameStaff
            // ))
            ?>
        </a>
        <span class="mtop5 text-muted">
            <a href="<?= ''//admin_url('profile/' . $feedback->create_by) ?>" target="_blank"><?= $fullnameStaff ?></a>
            <br /><small><?= ''//_dt($feedback->date_create) ?></small>
        </span>
        <small class="mtop5 pull-right">
            <?php //if (is_admin() || $feedback->create_by == get_staff_user_id()) { ?>
                <a class="btn btn-danger btn-icon" onclick="removeFeedBack(<?= $feedback->id ?>)">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            <?php //} ?>
        </small>
        <div class="media-body">

            <div class="comment-content"><?= ''//$feedback->feedback ?></div>
            <div class="fild-content mtop10">
                <?php //if (!empty($feedback->file)) {
                    //foreach ($feedback->file as $keyFile => $valFile) { ?>
                        <?php //if (explode('/', $valFile->filetype)[0] == 'image') { ?>
                            <div class="preview_image" style="width: auto;">
                                <div class="display-block contract-attachment-wrapper img">
                                    <div style="width:150px;">
                                        <a href="<?= ''//base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                            <div class="">
                                                <img src="<?= ''//base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>" style="max-height: 100px">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php //} else { ?>
                            <a target="_blank" href="<?= ''//base_url('uploads/' . $folder . '/' . $feedback->id . '/' . $valFile->file_name) ?>"><i class="fa fa-file-archive-o"></i> <?= ''//$valFile->file_name ?></a>
                        <?php //} ?>
                <?php //}
                //}
                ?>
            </div>
        </div>
    </div> -->
<?php } ?>