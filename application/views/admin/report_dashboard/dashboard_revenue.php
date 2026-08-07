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

    #pbiAppPlaceHolder {
        /* height: 100%; */
        min-height: 670px;
        /* min-width: 1330px; */
    }
</style>

<div id="wrappers">
    <div class="content">   
        <div class="row">
            <div id="pbiAppPlaceHolder">
                <!-- <iframe title="l1" width="100%" height="541" src="https://app.powerbi.com/reportEmbed?reportId=61160561-e8b0-4a46-9c43-781e83117b40&autoAuth=true&ctid=20ee9bda-5963-4d9b-b45d-040dd8b46435" frameborder="0" allowFullScreen="true"></iframe> -->
                <iframe  id="iframe"  title="dashboard_orders" width="100%" height="670" src="https://app.powerbi.com/view?r=eyJrIjoiNTViODRlMTgtMWU3OS00YjNhLWIzMDctMTJkZDU2N2IwNmEyIiwidCI6IjIwZWU5YmRhLTU5NjMtNGQ5Yi1iNDVkLTA0MGRkOGI0NjQzNSIsImMiOjEwfQ%3D%3D&pageName=ReportSection" frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var height = document.body.offsetHeight;
    $('#iframe').css('height', (height - 85));
</script>