<style>
    .createTag{
        padding-left: 2px;
        padding-right: 2px;
    }
</style>
<div class="chat-area">
    <div class="chat-area-header">
        <div class="profile-chat">
            <div class="profile-info mleft5">
                <img class="id_profile_chat" src="">
                <span class="id_name_profile_chat"></span>
                <p class="profile_staff_assigned hide">
                    <select id="browsers_staff_assigned" class="selectpicker" data-live-search="true" multiple data-none-selected-text="<?=_l('cong_staff_assigned')?>">
                            <?php
                                if(!empty($staff))
                                {
                                    foreach($staff as $key => $value){
                                            echo '<option value="'.$value['staffid'].'">'.$value['lastname'].' '.$value['firstname'].'</option>';
                                    }
                                }
                            ?>
                    </select>
                </p>
            </div>
            <div class="action-profile">
                <i class="lnr lnr-question-circle"></i>
                <i class="lnr lnr-cart"></i>
                <i class="lnr lnr-sync"></i>
                <i class="lnr lnr-trash"></i>
                <span class="dropdown">
                    <a  class="dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-link" aria-hidden="true"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a target="_blank" class="dropdown-item" href="https://www.facebook.com/<?=$_COOKIE['page_active']?>/inbox">
                            <i class="fa fa-compress" aria-hidden="true"></i>
                            <?=_l('cong_view_in_facebook')?>
                        </a>
                    </div>
                </span>
            </div>
        </div>
    </div>
    <div class="tab-content chat-area-body" id="chat_content_body">
        <?php $this->load->view('messagers/manage/content_mid')?>
    </div>
    <div class="chat-area-reply hide">
        <div class="ViewTag row font11" style="margin-right: 0px;margin-left: 0px;">
            <?php $tagview = get_tagsFB_table(); ?>
            <?php foreach($tagview as $key => $value){?>
                <?php if($key<=4){?>
                    <div class="col-md-2 pointer createTag" style="background-color: <?=$value['background_color']?>; color:<?=$value['color']?>;" title="<?=$value['name']?>">
                        <?= (mb_strlen($value['name'], 'UTF-8') > 10) ? mb_substr($value['name'],0,10, "utf-8").'...' : $value['name']?>
                    </div>
                <?php } else { break; }?>
            <?php } ?>
            <div class="col-md-2 div_tag_hidden">
                <label for="taghidden" class="control-label font11"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('cong_tag_other').' +'.count($tagview); ?></label>
                <input type="text" class="tagstypehidden hide" id="taghidden" value="<?=!empty($data->userid) ? GetDataTag($data->userid, 'client') : ''?>" data-role="tagstype">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="reply-box-container">
            <img src="https://graph.facebook.com/<?= $_COOKIE['page_active']?>/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">
            <textarea class="replyTextarea" id="replyMessager" placeholder="<?=_l('cong_input_rep')?>" type="text" wrap="hard"></textarea>
            <div class="action-profile">
                <div class="btn-group dropup">
                    <i class="fa fa-picture-o dropdown-toggle dropup" data-toggle="dropdown"></i>
                    <div class="dropdown-menu">
                        <h5 class="dropdown-header text-success text-center dropdown-not-padding"><?=_l('cong_image')?></h5>
                        <a class="dropdown-item border-top" onclick="AddPhotoModal()"><?=_l('cong_select_img_facebook')?></a>
                        <a class="dropdown-item border-top poiner" onclick="GetFilePC()" ><?=_l('cong_upload_image_pc')?></a>
                        <a class="dropdown-item border-top" onclick="GetFileLink()"><?=_l('cong_url_bk')?></a>
                    </div>
                </div>
                <i class="fa fa-magic"></i>
                <i class="fa fa-pencil-square-o"></i>
            </div>
            <form action="<?=base_url('messager/uploadfilepc')?>" id="form_uploadfile" autocomplete="off" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <input class="hide" type="file" name="file" id="file"/>
            </form>
        </div>
        <div id="all_file_send"></div>
    </div>
</div>