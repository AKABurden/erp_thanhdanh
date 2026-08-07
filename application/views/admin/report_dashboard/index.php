<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .border-1-color {

        border: 1px solid #0263c4;

        padding: 15px;

        height: 200px;

    }

    .imgDash {

        height: 70px;

        /*width: 70px;*/

        padding: 5px;

    }

    #wrapper {

        background: none;

    }

    .content {

        background: none;

    }

    .panel-body {

        background: white;

        box-shadow: 3px 3px 4px #aaa;

    }

    #side-menu {

        display: none;

    }

    /*.height70 {*/

    /*    height: 70px;*/

    /*}*/

    #wrappers {

        /* margin-top: 100px; */
        margin-top: 10px;

    }

    .btn-menu {
        border: 2px solid #9d2022;
        width: 100%;
        padding: 5px;

    }

    .btn-menu {
        color: #9d2022;
    }

    .app-menu-item {
        padding: 7px 0px;
    }

    .h4_button {
        margin-bottom: 5px !important;
    }
</style>

<div id="wrappers">
    <div class="content">
        <div class="row">
            <div class="">
                <div class="col-md-12">
                    <?php $dem = 0;
                    foreach ($menu as $key => $value) {
                        $dem++;
                    ?>
                        <div class="col-md-4">
                            <div class="col-md-12 border-radius-4 border-1-color text-center panel-body">
                                <div class="height70">
                                    <img class="imgDash" src="<?=$value['logo']?>">
                                </div>
                                <h4 class="h4_button">
                                    <a class="app-menu-item " href="<?=$value['link']?>">
                                        <h4 class="h4_button"><b><?= $value['name'] ?></b></h4>
                                    </a>
                                </h4>
                            </div>
                        </div>
                        <?php if ($dem == 3) {
                            $dem = 0;
                        ?>
                            <div class="clearfix"></div>
                            <br>
                            <br>
                        <?php } ?>

                    <?php } ?>

                    
                </div>
            </div>
        </div>
    </div>
</div>
<div style="padding: 10px;"></div>
<?php init_tail(); ?>
<script>
    // $('#side-menu').html('show-small');
</script>