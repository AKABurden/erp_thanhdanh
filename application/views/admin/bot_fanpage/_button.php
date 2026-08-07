<?php
$html_popver = "<div>";
$html_popver .= "    <div class='panel panel-primary'>";
$html_popver .= "        <div class='panel-heading'>Edit Button</div>";
$html_popver .= "        <div class='panel-body'>";
$html_popver .= "            <div class='form-group' app-field-wrapper='title'>";
$html_popver .= "                <label for='title' class='control-label'>Title</label>";
$html_popver .= "                <input type='text' id='title' name='title' class='form-control title_button' value='Button ".(!empty($orders) ? '#'.$orders : '')."'>";
$html_popver .= "            </div>";
$html_popver .= "            <div>";
$html_popver .= "               <h4>When pressed</h4>";
$html_popver .= "               <div class='aPressed newReply'><a class='open'>create new reply</a></div>";
$html_popver .= "               <div class='clearfix'></div>";
$html_popver .= "               <div class='aPressed'><a class='openWebsite'>Open website</a></div>";
$html_popver .= "               <div class='clearfix'></div>";
$html_popver .= "               <div class='aPressed'><a class='openCall'>Call number</a></div>";
$html_popver .= "            </div>";
$html_popver .= "        </div>";
$html_popver .= "        <div class='panel-footer'>";
$html_popver .= "           <button type='button' class='btn btn-default removeReply'>Delete</button>";
$html_popver .= "           <button type='button' class='btn btn-info pull-right Complete'>Complete</button>";
$html_popver .= "        </div>";
$html_popver .= "    </div>";
$html_popver .= "</div>";
?>
<button type="button" id-data="<?=$id_data_item?>" id-orders="<?=$id_orders_item?>" class="btn form-control border-button btnPopover DataEvent" href="#" data-toggle="popover" data-html="true" data-content="<?=$html_popver?>">
    Button <?=!empty($id_orders_item) ? '#'.$id_orders_item : ''?>
</button>
