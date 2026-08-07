<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'id',
	'name',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'_videos_groups';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $aRow) {
	$j++;
	$row = [];
    $row[] = $j;
    $_data ='<div class="group_name_plain_text">'.$aRow['name'].'</div><div class="group_edit hide">
                     <div class="input-group">
                     <input class="hide" id="group_id" value="'.$aRow['id'].'" class="form-control">
                      <input type="text" id="group_name" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-info p8 update-item-group" type="button">'._l('submit').'</button>
                      </span>
                    </div>
                  </div>';
   
    $row[] = $_data;
    $row[] ='<div>
                <a class="btn btn-success btn-icon edit-item-groups_ch" ><i class="fa fa-pencil"></i></a>
                <a href="'.admin_url('videos/delete_group/'.$aRow['id']).'"  type="button" class="btn btn-danger po btn-icon delete-remind_gb" data-container="body" data-html="true" data-toggle="popover" data-placement="left"
                    "><i class="fa fa-remove"></i></a>
            </div>';
  

    $output['aaData'][] = $row;
}
