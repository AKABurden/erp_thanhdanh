<?php
    $senders = json_decode($list_data)->data;
?>
<?php
foreach($senders as $key => $value){
?>

    <!--Lấy thông tin người dùng facebook-->
    <?php
        $infoFacebook = getInfoIdFacebook($value->senders->data[0]->id);
        $dataInfoHTML = ' phone="'.(!empty($infoFacebook['phone']) ? $infoFacebook['phone'] : '').'"';
        $dataInfoHTML .= ' orders="'.(!empty($infoFacebook['orders']) ? $infoFacebook['orders'] : '').'"';
        $dataInfoHTML .= ' assigned="'.(!empty($infoFacebook['assigned']) ? $infoFacebook['assigned'] : '').'"';
    ?>
    <div class="content-profile" <?=$dataInfoHTML?> id_senders="<?=$value->id?>" id_user = "<?=$value->senders->data[0]->id?>" data-toggle="tab" href="#tab_<?=$value->id?>">
        <div class="img-info">
            <?php
                $img = 'https://graph.facebook.com/'.$value->senders->data[0]->id.'/picture?height=100&width=100&access_token='.$_COOKIE['access_token_page_active'];

            ?>
            <img src="<?=$img?>">
        </div>
        <div class="some-info">
            <div class="name-profile">
               <?=$value->senders->data[0]->name?>
            </div>
            <div class="chat-profile" id="chat_<?=$value->senders->data[0]->id?>">...</div>
        </div>
        <div class="time-info">
            <?=time_ago($value->updated_time)?>
        </div>

        <div class="count-inbox hide" id="<?=$value->senders->data[0]->id?>"></div>
        <div class="tag_left">
            <?php  $get_info_tag = getInfoTagFacebook($value->senders->data[0]->id);?>
            <?php
            if(!empty($get_info_tag))
            {
                foreach($get_info_tag as $Ktag => $vtag){?>
                    <span class="label label-default inline-block pointer mtop5" id="tag-lef-<?=$vtag['id']?>">
                        <i class="fa fa-circle" style="color:<?=$vtag['background_color']?>" aria-hidden="true"></i>
                        <i><?=$vtag['name'];?></i>
                    </span>
                <?php   }
            }
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } ?>
