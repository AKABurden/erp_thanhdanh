<div role="tabpanel" class="tab-pane" id="tab_assigned">
    <a href="#" class="btn btn-info" onclick="AddAssigned_Lead(<?=$lead->id?>)">
        <i class="fa fa-bell-o"></i> <?php echo _l('cong_add_lead_assigned'); ?>
    </a>
    <hr/>
        <?php render_datatable(
            array(_l('cong_staff'),
                _l('cong_create_add'),
                _l('cong_date_create'),
            ), 'assigned-leads'); ?>
</div>