<?php
    $html_reply = "<div>";
    $html_reply .= "    <div class='panel panel-primary'>";
    $html_reply .= "        <div class='panel-heading'>Quick reply</div>";
    $html_reply .= "        <div class='panel-body'>";
    $html_reply .= "            <div>";
    $html_reply .= "               <h4>When pressed</h4>";
    $html_reply .= "               <div class='aPressed newReply'><a class='open'>create new reply</a></div>";
    $html_reply .= "               <div class='clearfix'></div>";
    $html_reply .= "               <div class='aPressed'><a class='openWebsite'>Open website</a></div>";
    $html_reply .= "               <div class='clearfix'></div>";
    $html_reply .= "               <div class='aPressed'><a class='openCall'>Call number</a></div>";
    $html_reply .= "            </div>";
    $html_reply .= "        </div>";
    $html_reply .= "        <div class='panel-footer'>";
    $html_reply .= "           <button type='button' class='btn btn-default removeReply'>Delete</button>";
    $html_reply .= "           <button type='button' class='btn btn-info pull-right Complete'>Complete</button>";
    $html_reply .= "        </div>";
    $html_reply .= "    </div>";
    $html_reply .= "</div>";
?>
<input class="form-control aReplyInput" id-data="<?=$id_data_item?>" id-orders="<?=$id_orders_item?>"  data-toggle="popover" data-html="true" data-content="<?=$html_reply?>">

