<style>
    .progressbar {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }
    .progressbar li {
        list-style-type: none;
        width: 16%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }
    .progressbar li:before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }
    .progressbar li:after {
        width: 100%!important;
        height: 2px!important;
        content: ''!important;
        position: absolute!important;
        background-color: #7d7d7d!important;
        top: 4px!important;
        left: -50%!important;
        z-index: -1!important;
    }
    .progressbar li:first-child:after {
        content: none;
        display: none;
    }
    .progressbar li.active {
        color: green;
    }
    .progressbar li.active:before {
        border-color: #55b776;
    }
    .progressbar li.active + li:after {
        background-color: #55b776!important;
    }
</style>
<div role="tabpanel" class="tab-pane" id="tab_advisory_lead">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="tab" href="#tabs_manage_advisory_lead"><?=_l('cong_list_advisory')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tabs_log_advisory_lead"><?=_l('cong_list_log_advisory')?></a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane container active" id="tabs_manage_advisory_lead">
            <?php render_datatable(array(
                _l('cong_fullcode_advisory'),
                _l('cong_date_start'),
                _l('cong_product_other_buy'),
                _l('cong_address_other_buy'),
                _l('cong_date_create'),
                _l('cong_create_by'),
                _l('cong_step_advisory')
            ),'advisory_lead_modal'); ?>
        </div>
        <div class="tab-pane container fade" id="tabs_log_advisory_lead">
            <?php
                include_once(APPPATH . 'views/admin/leads/cong_tab/tab_log_advisory.php');
            ?>
        </div>
    </div>
</div>