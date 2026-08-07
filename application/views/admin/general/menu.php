<link rel="stylesheet" type="text/css" href="<?= css('menu.css?vs=1.2') ?>">
<style>
    .not-permission{
        cursor: no-drop !important;
        /*pointer-events:none;*/
        background: #dbd9d5 !important;
        display: none !important;
    }
    .not-permission > a{
        cursor: no-drop;
        pointer-events:none;
        display: none !important;
    }
    .div-box-sub-menu .not-permission:hover{
        background: #dbd9d5 !important;
        border-radius: unset !important;
    }

    .flex-center {
        display: flex;
        align-items: center;
    }
</style>
<?php

$menu_dashboard = getMenuDashboard();
$items = $menu_dashboard['category']['items'];
?>
<div class="row">
    <div class="col-md-3">
        <div class="div-box-menu">
            <div class="uppercase div-title"><?= $menu_dashboard['category']['name'] ?></div>
            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $i => $v) : ?>
                    <?php
                    $activeCategory = '';
                    // if ($i == 'created_group') {
                    //     $activeCategory = 'active';
                    // }
                    if ($i == 'crm') {
                        $activeCategory = 'active';
                    }

                    $is_not_click = !empty($v['is_not_click']) ? $v['is_not_click'] : 0;
                    $is_sub = !empty($v['is_sub']) ? $v['is_sub'] : 0;
                    $sub_name = !empty($v['sub_name']) ? $v['sub_name'] : '';
                    ?>
                    <?php if (empty($is_sub)) : ?>
                        <div style="<?= $is_not_click ? 'pointer-events: none;' : '' ?>"  class="<?= $i ?> <?= $i == 'kpi' ? 'kpi_menu' : '' ?> click_localStorage uppercase div-label p-relative <?= $activeCategory ?>" data-value="<?= $i ?>">
                            <?= $v['name'] ?>
                            <?php if ($is_not_click) : ?>
                                <svg class="svg-abs" xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292893 0.292893C0.683417 -0.0976311 1.31658 -0.0976311 1.70711 0.292893L7 5.58579L12.2929 0.292893C12.6834 -0.0976311 13.3166 -0.0976311 13.7071 0.292893C14.0976 0.683417 14.0976 1.31658 13.7071 1.70711L7.70711 7.70711C7.31658 8.09763 6.68342 8.09763 6.29289 7.70711L0.292893 1.70711C-0.0976311 1.31658 -0.0976311 0.683417 0.292893 0.292893Z" fill="#040921" />
                                </svg>
                            <?php endif; ?>
                            <div class="div-sub-name"><?= $sub_name ? '('.$sub_name.')' : '' ?></div>
                        </div>
                    <?php else : ?>
                        <div class="uppercase p-relative">
                            <i class="fa fa-caret-right" style="position: absolute; left: 35px; top: 9px; font-size: 20px; color: #AFB0B8;" aria-hidden="true"></i>
                            <!-- <svg style="position: absolute; left: 10px;" xmlns="http://www.w3.org/2000/svg" width="40" height="50" viewBox="0 0 40 50" fill="none">
                                <rect x="20" width="1" height="50" fill="#AFB0B8"/>
                                <path d="M20.5 8V15.5C20.5 17.7091 22.2909 19.5 24.5 19.5H36" stroke="#AFB0B8"/>
                            </svg> -->
                            <div class="div-sub-label div-label click_localStorage <?= $i ?>" data-value="<?= $i ?>">
                                <?= $v['name'] ?>
                                <div class="div-sub-name"><?= $sub_name ? '('.$sub_name.')' : '' ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <?php if (!empty($items)) : ?>
            <?php foreach ($items as $i => $v) : ?>
                <?php
                $style = "display: none;";
                // if ($i == 'created_group') {
                //     $style = "display: block;";
                // }
                if ($i == 'crm') {
                    $style = "display: block;";
                }

                ?>
                <div class="tab-sub-menu cl-<?= $i ?>" id="<?= $i ?>" style="<?= $style ?>">
                    <?php if ($i != 'kpi') { ?>
                        <div class="row">
                            <?php
                            $sub_menu_one = !empty($v['sub_menu_one']) ? $v['sub_menu_one'] : null;
                            $sub_menu_two = !empty($v['sub_menu_two']) ? $v['sub_menu_two'] : null;
                            $sub_menu_three = !empty($v['sub_menu_three']) ? $v['sub_menu_three'] : null;
                            $sub_menu_four = !empty($v['sub_menu_four']) ? $v['sub_menu_four'] : null;
                            ?>
                            <div class="col-md-3">
                                <div class="div-box-sub-menu">
                                    <div class="mtop20"></div>
                                    <?php if (!empty($sub_menu_one)) : ?>
                                        <?php foreach ($sub_menu_one as $kS => $vS) : ?>
                                            <div class="uppercase div-title" style="<?= !empty($vS['color']) ? 'color:'.$vS['color'].' !important;' : '' ?>"><?= $vS['name'] ?></div>
                                            <?php
                                            $sub = !empty($vS['sub']) ? $vS['sub'] : null;
                                            ?>
                                            <?php if ($sub) : ?>
                                                <?php foreach ($sub as $kSub => $vSub) : ?>
                                                    <?php
                                                        $is_permission = isset($vSub['is_permission']) ? $vSub['is_permission'] : false;
                                                    ?>
                                                    <div style="<?= !empty($vSub['backgound']) ? 'background: '.$vSub['backgound'].' !important;' : '' ?>" class="sub-div flex-center <?= empty($is_permission) ? 'not-permission' : '' ?>">
                                                        <?php
                                                        $href = !empty($vSub['link']) ? base_url($vSub['link']) : 'javascript:void(0)';
                                                        $is_settings = !empty($vSub['is_settings']) ? $vSub['is_settings'] : 0;
                                                        ?>
                                                        <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                            <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4" />
                                                        </svg>
                                                        <a class="<?= $is_settings ? 'menu-settings' : 'menu-not-settings' ?>" style="width: 100%;" href="<?= empty($is_permission) ? 'javascript:void(0)' :  $href ?>">
                                                            <?= $vSub['name'] ?>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="div-box-sub-menu">
                                    <div class="mtop20"></div>
                                    <?php if (!empty($sub_menu_two)) : ?>
                                        <?php foreach ($sub_menu_two as $kS => $vS) : ?>
                                            <div class="uppercase div-title" style="<?= !empty($vS['color']) ? 'color:'.$vS['color'].' !important;' : '' ?>"><?= $vS['name'] ?></div>
                                            <?php
                                            $sub = !empty($vS['sub']) ? $vS['sub'] : null;
                                            ?>
                                            <?php if ($sub) : ?>
                                                <?php foreach ($sub as $kSub => $vSub) : ?>
                                                    <?php
                                                        $is_permission = isset($vSub['is_permission']) ? $vSub['is_permission'] : false;
                                                    ?>
                                                    <div style="<?= !empty($vSub['backgound']) ? 'background: '.$vSub['backgound'].' !important;' : '' ?>" class="sub-div flex-center <?= empty($is_permission) ? 'not-permission' : '' ?>">
                                                        <?php
                                                        $href = !empty($vSub['link']) ? base_url($vSub['link']) : 'javascript:void(0)';
                                                        $is_settings = !empty($vSub['is_settings']) ? $vSub['is_settings'] : 0;
                                                        ?>
                                                        <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                            <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4" />
                                                        </svg>
                                                        <a class="<?= $is_settings ? 'menu-settings' : 'menu-not-settings' ?>" style="width: 100%;" href="<?= $href ?>">

                                                            <?= $vSub['name'] ?>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="div-box-sub-menu">
                                    <div class="mtop20"></div>
                                    <?php if (!empty($sub_menu_three)) : ?>
                                        <?php foreach ($sub_menu_three as $kS => $vS) : ?>
                                            <div class="uppercase div-title" style="<?= !empty($vS['color']) ? 'color:'.$vS['color'].' !important;' : '' ?>"><?= $vS['name'] ?></div>
                                            <?php
                                            $sub = !empty($vS['sub']) ? $vS['sub'] : null;
                                            ?>
                                            <?php if ($sub) : ?>
                                                <?php foreach ($sub as $kSub => $vSub) : ?>
                                                    <?php
                                                        $is_permission = isset($vSub['is_permission']) ? $vSub['is_permission'] : false;
                                                    ?>
                                                    <div style="<?= !empty($vSub['backgound']) ? 'background: '.$vSub['backgound'].' !important;' : '' ?>" class="sub-div flex-center <?= empty($is_permission) ? 'not-permission' : '' ?>">
                                                        <?php
                                                        $href = !empty($vSub['link']) ? base_url($vSub['link']) : 'javascript:void(0)';
                                                        $is_settings = !empty($vSub['is_settings']) ? $vSub['is_settings'] : 0;
                                                        ?>
                                                        <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                            <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4" />
                                                        </svg>
                                                        <a class="<?= $is_settings ? 'menu-settings' : 'menu-not-settings' ?>" style="width: 100%;" href="<?= $href ?>">

                                                            <?= $vSub['name'] ?>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="div-box-sub-menu">
                                    <div class="mtop20"></div>
                                    <?php if (!empty($sub_menu_four)) : ?>
                                        <?php foreach ($sub_menu_four as $kS => $vS) : ?>
                                            <div class="uppercase div-title" style="<?= !empty($vS['color']) ? 'color:'.$vS['color'].' !important;' : '' ?>"><?= $vS['name'] ?></div>
                                            <?php
                                            $sub = !empty($vS['sub']) ? $vS['sub'] : null;
                                            ?>
                                            <?php if ($sub) : ?>
                                                <?php foreach ($sub as $kSub => $vSub) : ?>
                                                    <?php
                                                        $is_permission = isset($vSub['is_permission']) ? $vSub['is_permission'] : false;
                                                    ?>
                                                    <div style="<?= !empty($vSub['backgound']) ? 'background: '.$vSub['backgound'].' !important;' : '' ?>" class="sub-div flex-center <?= empty($is_permission) ? 'not-permission' : '' ?>">
                                                        <?php
                                                        $href = !empty($vSub['link']) ? base_url($vSub['link']) : 'javascript:void(0)';
                                                        $is_settings = !empty($vSub['is_settings']) ? $vSub['is_settings'] : 0;
                                                        ?>
                                                        <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                            <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4" />
                                                        </svg>
                                                        <a class="<?= $is_settings ? 'menu-settings' : 'menu-not-settings' ?>" style="width: 100%;" href="<?= $href ?>">

                                                            <?= $vSub['name'] ?>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>

                    <?php } ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-3">
        <div class="div-box-menu">
            <div class="uppercase div-title" ><?= lang('Hạng mục') ?></div>
            <div class="uppercase div-label active" data-value="div-1"><?= lang('Danh mục nhóm tạo') ?></div>
            <div class="uppercase div-label" data-value="div-2"><?= lang('I. CRM') ?></div>
            <div class="uppercase div-label"><?= lang('II. SCC') ?></div>
            <div class="uppercase div-label"><?= lang('III. ERP') ?></div>
            <div class="uppercase div-label"><?= lang('IV. Thống Kê - Báo Cáo') ?></div>
            <div class="uppercase div-label"><?= lang('V. Đánh Giá KPI Tháng/Năm') ?></div>
            <div class="uppercase div-label"><?= lang('VI. Dashbood Power BI') ?></div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-sub-menu" id="div-1" style="display: block;">
            <div class="row">
                <div class="col-md-4">
                    <div class="div-box-sub-menu">
                        <div class="uppercase div-title mtop20"><?= lang('Khách hàng') ?></div>
                        <div class="sub-div flex-center">
                            <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4"/>
                            </svg>
                            Nhóm khách hàng
                        </div>
                        <div class="sub-div active flex-center">
                            <svg class="mr-5" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                <circle opacity="0.6" cx="2" cy="2" r="2" fill="#9295A4"/>
                            </svg>
                            Phân loại khách hàng
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
        </div>
        <div class="tab-sub-menu" id="div-2" style="display: none;">

        </div>
    </div>
</div> -->
<script>
     //function view_kpi_evaluation_new() {
     //    var filter_month = $(".pannel-box").find("select#filter_month_new").val();
     //    console.log(filter_month)
     //    if (filter_month) {
     //        $.ajax({
     //            type: 'POST',
     //            url: admin_url+'dashboard/view_kpi_evaluation',
     //            data: {
     //                "<?//= $this->security->get_csrf_token_name() ?>//": "<?//= $this->security->get_csrf_hash() ?>//",
     //                filter_month: filter_month,
     //            },
     //            dataType: "JSON",
     //            success: function (response) {
     //                $('tbody#html_view_kpi_evaluation').html(response.html);
     //            }
     //        });
     //    }
     //}

    $(document).ready(function() {
        // $(".pannel-box").find("select#filter_month_new").select2();
        // view_kpi_evaluation_new();
        // $(".pannel-box").find("select#filter_month_new").change(function () {
        //     view_kpi_evaluation_new();
        // });
        $(document).on('click', '.div-box-menu .div-label', function(event) {
            $('.div-label').removeClass('active');
            $(this).addClass('active');

            var vActive = $(this).attr('data-value');
            $('.tab-sub-menu').hide();
            // $('#' + vActive).show();
            $('.cl-' + vActive).show();
        });

        $('.menu-settings').click(function() {
            $.get(admin_url + 'misc/set_setup_menu_open')
        });

        $('.menu-not-settings').click(function() {
            $.get(admin_url + 'misc/set_setup_menu_closed')
        });
    });
     $(".kpi_menu").click(function (){
         location.href = site_url + 'admin/kpi/staff_kpi_evaluation';
     })
</script>