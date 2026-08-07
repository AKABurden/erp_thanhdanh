<?php $folder = 'purchases_feedback';?>
<?php if(!empty($feedback)) { ?>
    <?php $fullnameStaff = get_staff_full_name($feedback->create_by);?>
    <div data-feed-back-purchases="<?=$feedback->id?>" class="tc-content task-comment">
        <a href="<?= admin_url('profile/'.$feedback->create_by) ?>" target="_blank">
            <?php echo staff_profile_image($feedback->create_by, array('staff-profile-image-small mright5'), 'small', array(
                                                      'data-toggle' => 'tooltip',
                                                      'data-title' => $fullnameStaff
                                                  ))
            ?>
        </a>
        <small class="mtop5 text-muted"><?=_dt($feedback->date_create)?></small>
        <small class="mtop25 pull-right">
            <a class="btn btn-danger btn-icon" onclick="removeFeedBack(<?=$feedback->id?>)" >
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
        </small>
        <div class="media-body">
            <a href="<?= admin_url('profile/'.$feedback->create_by) ?>" target="_blank"><?=$fullnameStaff?></a>
            <div class="comment-content"><?=$feedback->feedback?></div>
            <div class="fild-content mtop10">
                    <?php if(!empty($feedback->file)) {
                        foreach($feedback->file as $keyFile => $valFile) {?>
                            <?php if(explode('/',$valFile->filetype)[0] == 'image'){ ?>
                                <div class="preview_image" style="width: auto;">
                                    <div class="display-block contract-attachment-wrapper img">
                                        <div style="width:150px;">
                                            <a href="<?=base_url('uploads/'.$folder.'/'.$feedback->id.'/'.$valFile->file_name)?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                <div class="">
                                                    <img src="<?=base_url('uploads/'.$folder.'/'.$feedback->id.'/'.$valFile->file_name)?>" style="max-height: 100px">
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <a  target="_blank" href="<?=base_url('uploads/'.$folder.'/'.$feedback->id.'/'.$valFile->file_name)?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a>
                            <?php } ?>
                        <?php }
                    }
                ?>
            </div>
        </div>
        <hr class="task-info-separator">
    </div>
<?php } ?>