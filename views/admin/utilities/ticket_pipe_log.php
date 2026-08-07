<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="col-md-4">
                <?php echo render_date_input('activity_log_date','utility_activity_log_filter_by_date','',array(),array(),'','activity-log-date'); ?>
            </div>
            <div class="col-md-8 text-right mtop20">
                <a class="btn btn-danger _delete H_action_button" href="<?php echo admin_url('utilities/clear_pipe_log'); ?>"><?php echo _l('clear_activity_log'); ?></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="panel_s">
                    <div class="panel-body">
                    <?php render_datatable(array(
                        _l('ticket_pipe_name'),
                        _l('ticket_pipe_date'),
                        _l('ticket_pipe_email_to'),
                        _l('ticket_pipe_email'),
                        _l('ticket_pipe_subject'),
                        _l('ticket_pipe_message'),
                        _l('ticket_pipe_status'),
                        ),'activity-log'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
