
<?php
if(!empty($chat_area))
{?>
    <div class="chat-area-container tab-pane" id="tab_<?=$id_chat?>">
        <div class="chat-area-content">
            <div class="chat-area-content-profile">
                <?php
                if(!empty($message))
                {
                    $dateCreate = "";
                    $message = json_decode($message)->data;
                    if(!empty($message))
                    {
                        $count_message = count($message);
                        for($key = ($count_message-1); $key >= 0; $key--)
                        {?>
                            <?php
                            $created_time = strtotime($message[$key]->created_time);
                            if(empty($dateCreate) ||  abs($created_time - $dateCreate)>3600 ){?>
                                <div class="time-chat">
                                    <span>
                                        <?= _dt($message[$key]->created_time);?>
                                    </span>
                                </div>
                                <?php $dateCreate = $created_time;?>
                            <?php }?>

                            <?php if($message[$key]->from->id == $_COOKIE['page_active']){?>
                                <!-- tin nhắn gửi đi -->
                                <div class="my-batch-content-container">
                                    <div class="my-messages">
                                        <div class="my-messages-container content-message my-message" time="<?=$created_time?>" id="<?=$message[$key]->id?>">
                                            <span>
                                                <?php
                                                    if(empty($message[$key]->attachments)){
                                                            echo $message[$key]->message;
                                                     }
                                                    else
                                                     {

                                                        foreach($message[$key]->attachments->data as $kt => $vt){
                                                            if($vt->mime_type == 'image/jpeg')
                                                            {
                                                                echo '<img class="mtop10" src="'.$vt->image_data->url.'"/>';
                                                            }
                                                            else
                                                            {
                                                                echo '<a target="_blank" href="'.$vt->file_url.'">'.$vt->name.'</a>';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                        <?php for($_key = ($key-1); $_key >= 0; $_key--){?>
                                            <?php

                                                if($message[$_key]->from->id == $_COOKIE['page_active']){
                                                    $created_time = strtotime($message[$_key]->created_time);
                                                    if(empty($dateCreate) ||  abs($created_time - $dateCreate) > 3600 ){
                                                        break;
                                                    }
                                                    ?>
                                                    <div class="my-messages-container content-message my-message" time="<?=$created_time?>" id="<?=$message[$_key]->id?>">
                                                        <span>
                                                            <?php

                                                            if(empty($message[$_key]->attachments)){
                                                                echo $message[$_key]->message;
                                                            }
                                                            else
                                                            {
                                                                foreach($message[$_key]->attachments->data as $kt => $vt){
                                                                    if($vt->mime_type == 'image/jpeg')
                                                                    {
                                                                        echo '<img class="mtop10" src="'.$vt->image_data->url.'"/>';
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<a target="_blank" href="'.$vt->file_url.'">'.$vt->name.'</a>';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <?php
                                                        $key = $_key;
                                                        if($message[$_key]->from->id != $_COOKIE['page_active'])
                                                        {
                                                            break;
                                                        }
                                                    ?>
                                            <?php } else {
                                                    break;
                                                }?>
                                        <?php }?>
                                    </div>
                                </div>
                            <?php } else {?>

                                <!-- tin nhắn gửi đến -->
                                <div class="batch-content-container">
                                    <div class="avatar">
                                        <img src="https://graph.facebook.com/<?= $message[$key]->from->id;?>/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">
                                    </div>
                                    <div class="messages">
                                        <div class="messages-container content-message client-message" time="<?=$created_time?>" id="<?=$message[$key]->id?>">
                                            <span>
                                                <?php
                                                if(empty($message[$key]->attachments)){
                                                    echo $message[$key]->message;
                                                } else{
                                                    foreach($message[$key]->attachments->data as $kt => $vt){
                                                        if($vt->mime_type == 'image/jpeg')
                                                        {
                                                            echo '<img class="mtop10" src="'.$vt->image_data->url.'"/>';
                                                        }
                                                        else
                                                        {
                                                            echo '<a target="_blank" href="'.$vt->file_url.'">'.$vt->name.'</a>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </span>
                                        </div>


                                        <?php for($_key = ($key-1); $_key >= 0; $_key--){?>
                                            <?php if($message[$_key]->from->id != $_COOKIE['page_active']){
                                                    $created_time = strtotime($message[$_key]->created_time);
                                                    if(empty($dateCreate) ||  abs($created_time - $dateCreate) > 3600 ){
                                                        break;
                                                    }
                                                ?>
                                                    <div class="messages-container content-message client-message" time="<?=$created_time?>" id="<?=$message[$_key]->id?>">
                                                        <span>
                                                            <?php
                                                            if(empty($message[$_key]->attachments)){
                                                                echo $message[$_key]->message;
                                                            }
                                                            else
                                                            {
                                                                foreach($message[$_key]->attachments->data as $kt => $vt){
                                                                    if($vt->mime_type == 'image/jpeg')
                                                                    {
                                                                        echo '<img class="mtop10" src="'.$vt->image_data->url.'"/>';
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<a target="_blank" href="'.$vt->file_url.'">'.$vt->name.'</a>';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>
                                                <?php
                                                    $key = $_key;
                                                    if($message[$_key]->from->id == $_COOKIE['page_active'])
                                                    {
                                                        break;
                                                    }
                                                ?>
                                            <?php } else {
                                                        break;
                                                  }?>
                                        <?php }?>
                                    </div>
                                </div>
                                <!-- tin nhắn gửi đi -->

                            <?php }?>

                    <?php } ?>
                <?php } ?>
            <?php } ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="chat-area-container">
        <div class="chat-area-content">
        </div>
    </div>
<?php }?>
