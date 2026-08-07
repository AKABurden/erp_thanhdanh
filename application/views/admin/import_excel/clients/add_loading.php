<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    h1 {
        margin-bottom:10px;
    }
    #load-percent {
        margin-top:40px;
        font-size:20px;
        font-weight: 100;
    }
    .tiny {
        text-align:right;
        font-size:11px;
    }
    .tiny a {
        color:white;
        text-decoration:none;
    }
    .tiny a:hover {

    }
    #done {
        height:80px;
        width:80px;
        display:none;
    }
    .bar {
        position: relative;
        top: 1px;
        left: 50%;
        height: 18px;
        width: 30px;
        margin-top: -10px;
        /* half the height */
        margin-left: -169px;
        /* half the width */
        border-radius: 20px;
        background-image: -webkit-linear-gradient(-45deg, #ff9a1a 25%, #ff9a1a 25%, rgba(255, 154, 26, 0) 50%, rgba(255, 154, 26, 0) 50%, #ff9a1a 75%, #ff9a1a 75%);
        background-image: -moz-linear-gradient(-45deg, #ff9a1a 25%, #ff9a1a 25%, #ff9a1a 50%, #ff9a1a 50%, #ff9a1a 75%, #ff9a1a 75%);
        background-image: -o-linear-gradient(-45deg, #ff9a1a 25%, #ff9a1a 25%, #ff9a1a 50%, #ff9a1a 50%, #ff9a1a 75%, #ff9a1a 75%);
        background-image: linear-gradient(-45deg, #ff9a1a 25%, #ff9a1a 25%, #ff9a1a 50%, #ff9a1a 50%, #ff9a1a 75%, #ff9a1a 75%);
        background-color: #d3d3d3;
        background-size: 50px 50px;
        -webkit-box-shadow: inset 2px -1px 4px rgba(0, 0, 0, 0.5);
        box-shadow: inset 2px -1px 4px rgba(0, 0, 0, 0.5);
        -webkit-animation: move 2s linear infinite;
        -moz-animation: move 2s linear infinite;
        -ms-animation: move 2s linear infinite;
        animation: move 2s linear infinite; }
    @-webkit-keyframes move {
        0% {
            background-position: 0 0; }
        100% {
            background-position: 50px 50px; } }
    @-moz-keyframes move {
        0% {
            background-position: 0 0; }
        100% {
            background-position: 50px 50px; } }
    @-ms-keyframes move {
        0% {
            background-position: 0 0; }
        100% {
            background-position: 50px 50px; } }
    @-webkit-keyframes move {
        0% {
            background-position: 0 0; }
        100% {
            background-position: 50px 50px; } }
    .bar-holder {
        position: relative;
        top: 30px;
        width: 340px;
        border-radius: 20px;
        background-color: #D2D2D2;
        left: 50%;
        margin-left: -170px;
        height: 20px;
        -webkit-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.45);
        box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.45);
    }

</style>
<style>
    ._bg-success {
        background: #0fc31d!important;
        color: white;
    }
    ._bg-warning {
        background: yellow!important;
        color: black;
    }
    ._bg-danger {
        background: red!important;
        color: white;
    }
</style>
<div id="wrapper">
    <div class="panel_s">
            <div class="panel-body">
            <h1 class="text-center"><?= _l('cong_system_add_data_client')?></h1>
            <?php $count = !empty($data) ? count($data) : "0"; ?>
            <h1 class="text-center"><b class="data_now">0</b>/<b class="data_total"><?=$count?></b></h1>
            <div class="bar-holder">
                <div class="bar"></div>
            </div>
            <div id="load-percent" class="text-center">
                <span id="loader-text">0</span><span>%</span>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-4"><?= _l('cong_add_true') ?></div>
                <div class="col-md-4"><?= _l('cong_isset_'.$action)?></div>
                <div class="col-md-4"><?=_l('cong_add_false')?></div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-4 _bg-success data_add">0</div>
                <div class="col-md-4 _bg-warning data_update">0</div>
                <div class="col-md-4 _bg-danger data_none">0</div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <h3 class="text-success">
                    <div class="result_active text-center"></div>
                </h3>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
    var DataClients = <?= !empty($data) ? json_encode($data) : "[]" ?>;
    var countClients = <?= $count ?>;
    function loadProgress(step = 0) {
        var percent = $('.bar-holder').width();
        var widthBar_New = percent * step / 100;
        $('.bar').width(widthBar_New);
        $('#loader-text').text(step);
    }

    var nowData = 0;
    var now_add = 0;
    var now_update = 0;
    var now_none = 0;
    if(DataClients.length > 0) {

        var data = DataClients[countClients - nowData - 1];
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['action'] = "<?= !empty($action) ? $action : '' ?>";
        data['field_unique'] = "<?= !empty($colum_unique) ? $colum_unique : '' ?>";
        $.post("<?=admin_url('import_excel/AddClient')?>", data, function(result) {
            result = JSON.parse(result);
            if(result.success) {
                if(result.add) {
                    now_add++;
                    $('.data_add').text(now_add);
                }
                else if(result.update) {
                    now_update++;
                    $('.data_update').text(now_update);
                }
                else {
                    now_none++;
                    $('.data_none').text(now_add);
                }
            }
        }).always(function() {
            delete DataClients[countClients - nowData - 1];
            nowData++;
            addNext();
            loadProgress(Math.floor((nowData / countClients) * 100));
            $('.data_now').text(nowData);
        })
    }

    function addNext() {
        if(nowData < countClients) {
            var data = DataClients[countClients - nowData - 1];
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['action'] = "<?= !empty($action) ? $action : '' ?>";
            data['field_unique'] = "<?= !empty($colum_unique) ? $colum_unique : '' ?>";
            $.post("<?=admin_url('import_excel/AddClient')?>", data, function(result) {
                result = JSON.parse(result);
                if(result.success) {
                    if(result.add) {
                        now_add++;
                        $('.data_add').text(now_add);
                    }
                    else if(result.update) {
                        now_update++;
                        $('.data_update').text(now_update);
                    }
                    else {
                        now_none++;
                        $('.data_none').text(now_add);
                    }
                }
                
                return false;
            }).always(function() {
                delete DataClients[countClients - nowData - 1];
                nowData++;
                addNext();
                loadProgress(Math.floor((nowData / countClients) * 100));
                $('.data_now').text(nowData);
            }).fail(function(){
            });
        }
        else  {
            $('.result_active').text('Hoàn thành');
            setTimeout(function(){
                //location.href   =   "<?//=admin_url('import_excel/import_client')?>//";
            }, 3000);
        }
    }

</script>
