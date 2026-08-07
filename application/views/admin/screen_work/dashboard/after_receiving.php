<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__, ".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
    <div class="panel_s user-data">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="wap-container">
                <div>
                    <div class="wap-left">
                        <div class="wap-title center">
                            <?=!empty($client) ? $client['name'] : ''?>
                        </div>
                    </div>
                    <div class="wap-center title">
                        <div class="wap-title center">
                            <?=_l('cong_code_client')?>
                        </div>
                    </div>
                    <div class="wap-right">
                        <div class="wap-title center">
                            % <?=_l('cong_Finish')?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="scoll-content">
                    <?php foreach($client['detail'] as $key => $value){?>
                        <div>
                            <div class="wap-left">
                                <div class="wap-content bold900">
                                    <?=$value['name']?>
                                </div>
                            </div>
                            <div class="wap-center">
                                <?php $info_full = array_merge($value['info_success'], $value['info_waiting']);?>
                                <div class="wap-content">
                                    <!-- red tô đỏ -->
                                    <div <?=!empty($info_full[0]['active']) ? 'class="red"' : ''?> >
                                        <a <?=!empty($info_full[0]['active']) ? 'class="text-danger"' : 'class="text-black"'?> href="<?=!empty($info_full[0]['lead']) ? admin_url('leads/index/'.$info_full[0]['lead']) : '#'?>"><?=!empty($info_full[0]['fullname']) ? '• '.$info_full[0]['fullname'] : 'NONE' ?></a> <!-- show người đầu tiên -->
                                    </div>
                                    <div class="center drop-down">
                                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                    </div>
                                </div>

                                <div class="wap-hide-content">
                                    <?php foreach($info_full as $Kinfo => $Vinfo){?>
                                        <!-- đổ tất cả dữ liệu -->
                                        <div <?=!empty($Vinfo['active']) ? 'class="red"' : ''?> >
                                            <a <?=!empty($Vinfo['active']) ? 'class="text-danger"' : 'class="text-black"'?> href="<?=!empty($Vinfo['lead']) ? admin_url('leads/index/'.$Vinfo['lead']) : '#'?>"><?=!empty($Vinfo['fullname']) ? '• '.$Vinfo['fullname'] : 'NONE' ?></a> <!-- show người đầu tiên -->
                                        </div>
                                        <!-- end -->
                                    <?php }?>

                                </div>
                            </div>
                            <?php
                            $count_success = count($value['info_success']);
                            $count_waiting = count($value['info_waiting']);
                            $sum_count = $count_waiting+$count_success;
                            ?>
                            <div class="wap-right">
                                <div class="wap-content">
                                    <div class="wap-percent" style="width: <?=!empty($sum_count) ? ($count_success/($sum_count))*100: 0?>%;">
                                        <?=!empty($sum_count) ? ($count_success/($sum_count))*100: 0?>%
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>
