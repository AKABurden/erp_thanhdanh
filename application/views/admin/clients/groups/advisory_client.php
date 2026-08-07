<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
    <a href="#" data-toggle="modal" data-target="#advisory-client-modal" class="btn btn-info mbot25" onclick="AddAdvisory()">
        <i class="fa fa-bell-o"></i>
        <?php echo _l('title_add_advisory-client'); ?>
    </a>
    <div class="clearfix"></div>
    <?php render_datatable(array(
            _l( 'cong_type'),
            _l( 'cong_date_reality'),
            _l( 'cong_date_remind_expected'),
            _l( 'cong_number_remind'),
            _l( 'cong_expire'),
        ), 'advisory_client');
//    $this->load->view('admin/clients/modals/advisory_client', ['id' => $client->userid]);
} ?>
<div id="div_modal_advisory_client">
    <div class="modal fade" id="advisory-client-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    </div>
</div>
