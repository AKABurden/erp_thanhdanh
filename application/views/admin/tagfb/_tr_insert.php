<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<tr class="Tr-new bg-info" role="row">
    <td>#</td>
    <td><?=_l('cong_new')?></td>
    <td class="sorting_1">
        <div class="input-lable">
            <input type="text" name="name" class="form-control" value="">
        </div>
    </td>
    <td>
        <div class="input-lable input-group colorpicker-input colorpicker-element">
            <input type="text" name="color" value="" class="form-control">
            <span class="input-group-addon"><i></i></span>
        </div>
    </td>
    <td>
        <div class="input-lable input-group colorpicker-input colorpicker-element">
            <input type="text" name="background_color" value="" class="form-control">
            <span class="input-group-addon"><i></i></span>
        </div>
    </td>
    <td>
        <a class="btn btn-info btn-icon input-lable UpdateTr" data-toggle="tooltip" data-placement="top" title="<?=_l('cong_save')?>">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </a>
        <a class="btn btn-warning btn-icon input-lable NotInsertTr" data-toggle="tooltip" data-placement="top" title="<?=_l('cong_not_save')?>">
            <i class="fa fa-repeat" aria-hidden="true"></i>
        </a>
    </td>
</tr>
