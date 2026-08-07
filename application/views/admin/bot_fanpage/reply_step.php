<?php
    $html_popver = "<div>";
    $html_popver .= "    <div class='panel panel-primary'>";
    $html_popver .= "        <div class='panel-heading'>Edit Button</div>";
    $html_popver .= "        <div class='panel-body'>";
    $html_popver .= "            <div class='form-group' app-field-wrapper='title'>";
    $html_popver .= "                <label for='title' class='control-label'>Title</label>";
    $html_popver .= "                <input type='text' id='title' name='title' class='form-control title_button' value='Button ".(!empty($id_orders) ? '#'.$id_orders : '')."'>";
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
<div class="panel items-panel">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <button type="button" class="oPenLeft" id-data-left="<?=!empty($id_data) ? ($id_data-1) : '0'?>" id-orders-left="<?=!empty($id_orders) ? ($id_orders) : '0'?>">
                <i class="fa fa-angle-left" aria-hidden="true"></i>
            </button>
            <?=_l('lead_general_info')?>
        </div>
        <div class="panel-body body_content">
            <div class="itemEvent item_<?=!empty($id_data) ? $id_data : '0'?>" id-data-item="<?=!empty($id_data) ? $id_data : '0'?>" id-orders-item="<?=!empty($id_orders) ? $id_orders : '0'?>">
                <textarea class="form-control"></textarea>
                <div class="clearfix"></div>
                <div class="buttonEvent"></div>
                <a type="button" class="btn form-control aPressed" onclick="createEventButton(this)">
                    +Add Button
                </a>
            </div>
        </div>
    </div>
</div>