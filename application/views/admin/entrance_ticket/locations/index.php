<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="pull-right">
                    <!-- <a href="<?= admin_url('entrance_ticket/location_detail') ?>" class="btn btn-info H_action_button pull-right mright5 tnh-modal" data-toggle="modal" data-target="#myModal">
                        <?php //echo _l('add'); ?>
                    </a> -->
                    <a href="<?= admin_url('entrance_ticket') ?>" class="btn btn-info H_action_button pull-right mright5">
                        <i class="fa fa-reply"></i> <?= lang('Trở lại') ?>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table dt-tnh table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px">STT</th>
                                    <th><?= lang('Tên Vị Trí') ?></th>
                                    <th><?= lang('Nhóm Vai Trò (Roles)') ?></th>
                                    <th class="text-center" style="width:100px"><?= lang('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($locations as $key => $loc): ?>
                                    <tr>
                                        <td class="text-center"><?= ++$key ?></td>
                                        <td><strong><?= $loc['name'] ?></strong></td>
                                        <td>
                                            <?php
                                            if (!empty($loc['role_ids'])) {
                                                $role_names = [];
                                                $ids = $loc['role_ids'];
                                                foreach ($ids as $rid) {
                                                    $rname = $roles_map[$rid] ?? '';
                                                    if ($rname) $role_names[] = '<span class="label label-info mright5">' . $rname . '</span>';
                                                }
                                                echo implode(' ', $role_names);
                                            } else {
                                                echo '<span class="text-muted">Chưa chọn role</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= admin_url('entrance_ticket/location_detail/' . $loc['id']) ?>" class="btn btn-default btn-icon tnh-modal" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i></a>
                                            <a href="<?= admin_url('entrance_ticket/delete_location/' . $loc['id']) ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
