<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(false); ?>
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
                    <iframe id="iframe" title="Tổng quan công việc" width="100%" height="1060"
                            src="https://app.powerbi.com/view?r=eyJrIjoiZDVmZTc5MjItNjVjYi00OTc4LWJmYTktYzRjMjJmZDIxZmM4IiwidCI6IjIwZWU5YmRhLTU5NjMtNGQ5Yi1iNDVkLTA0MGRkOGI0NjQzNSIsImMiOjEwfQ%3D%3D"
                            frameborder="0" allowFullScreen="true"></iframe>

                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    // $('#side-menu').html('show-small');
    var height = document.body.offsetHeight;
    $('#iframe').css('height', (height - 85));
</script>