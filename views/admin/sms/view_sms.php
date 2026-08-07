<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .uppercase {
        text-transform: uppercase;
        border-bottom: 1px solid #cecece;
    }

    .bold {
        font-weight: bold;
    }

    .red {
        color: red;
    }

    .notification-pad {
        padding: 15px;
        background: #ffc379;
        margin-top: 15px;
    }

    .title-templates {
        background: #b1b1b1;
    }

    .content-templates {
        background: #c1e8ff;
    }

    .padding10 {
        padding: 10px;
    }

    .scroll-auto {
        height: 300px;
        overflow: auto;
    }

    .padding0 {
        padding: 0 !important;
    }

    .content-templates:hover {
        cursor: pointer;
    }

    .wrap-templates:hover {
        position: relative;
        top: -5px;
        transition: all 1s;
    }

    .color000 {
        color: #fff0;
    }

    .wrap-templates:hover .color000 {
        color: #717171;
    }
</style>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <?php
                    $array_name_status = [
                        _l('cong_not_send'),
                        _l('cong_warting_send'),
                        _l('cong_true_send'),
                        _l('cong_false_send')
                    ];
                    $array_color_status = [
                        'default',
                        'warning',
                        'success',
                        'danger'
                    ];

                    ?>
                    <div class="ribbon <?=$array_color_status[$sms->status]?>">
                        <span><?=$array_name_status[$sms->status]?></span>
                    </div>
                    <div class="panel-body">
                        <h2 class="uppercase mbot20">
                            <?=_l('SMS')?>
                            <?=(!empty($sms->type) ? (_l('send_in').' '._dt($sms->date_send)) : (_l('send_now').' '._dt($sms->datecreated)))?>
                        </h2>
                        <div class="col-md-6">
                            <div class="uppercase bold mbot10">
                                <?= _l('brand_name') ?>
                            </div>
                            <label for="brand_name" class="control-label"><?= _l('brand_name') ?></label>
                            <div class="form-control"><?= $sms->brandname ?></div>

                            <div class="form-group mtop10">
                                <p>
                                    <b>
                                        <?= _l('list_user_send') ?>:
                                    </b>
                                </p>
                                <ul class="tagit ui-widget ui-widget-content ui-corner-all">
                                    <li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
                                        <span class="tagit-label">
                                            <?php
                                                if(!empty($sms->id_contact))
                                                {
                                                    echo $sms->fullname;
                                                }
                                                else if(!empty($sms->userid))
                                                {
                                                    echo $sms->company;
                                                }
                                                else
                                                {
                                                    echo $sms->phone;
                                                }
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <!--END Danh sách khách hàng từ dữ liệu khách hàng-->
                        </div>
                        <div class="col-md-6">
                            <div class="uppercase bold">
                                <?= _l('announcement_message') ?>
                            </div>
                            <div class="mtop10">
                                <?php echo render_textarea('content', '', $sms->message, array('rows' => 10)) ?>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script>


    // hỗ trợ css
    $(".close-templates").click(function () {
        $(".open-templates").click();
    });
    //end
    //lấy text đưa vào textarea nội dung
    $(".content-templates").click(function (e) {
        var current = $(e.currentTarget);
        var content = current.text();
        $('#content').text($.trim(content));
    });
    //end

    $('body').on('change', 'input[name="type"]:checked', function (e) {
        if ($(this).val() == 0) {
            $('.div_date_send').addClass('hide');
            $('#date_send').val('');
        } else {
            $('.div_date_send').removeClass('hide');
        }
    })
</script>
</body>
</html>
