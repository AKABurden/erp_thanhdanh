<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <span class="title">
                    <?=(!empty($care_of_clients) ? _l('cong_update_care_of') : _l('cong_add_care_of') )?>
                </span>
            </h4>
        </div>
        <?php echo form_open('admin/care_of_clients/detail',array('id'=>'care_of_from')); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                        <?php
                            if(empty($id_client)) {
                                echo '<div class="col-md-6">';
                                $value = !empty($care_of_clients) ? $care_of_clients->client : '';
                                echo render_select('client', $clients, array('userid', 'company', 'full_code'), 'cong_client', $value);
                                echo '</div>';
                            }
                            else
                            {
                                echo form_hidden('client', $id_client);
                            }
                        ?>
                    <?php if(empty($care_of_clients->count_care_of)){ ?>
                        <div class="col-md-6">
                            <?php $value = !empty($care_of_clients->date) ? _d($care_of_clients->date) : _d(date('Y-m-d'))?>
                            <?php echo render_date_input('date','cong_date_start', $value); ?>
                        </div>
                    <?php } ?>
                    <?php if(empty($care_of_clients->count_care_of)){ ?>
                        <div class="col-md-6">
                            <?php $value = !empty($care_of_clients->status_procedure) ? $care_of_clients->status_procedure : ''?>
                            <?php echo render_select('status_procedure', $procedure_detail, array('id', 'name'), 'cong_status_procedure', $value); ?>
                        </div>
                    <?php } ?>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->theme_of) ? $care_of_clients->theme_of : '' ?>
                        <?php echo render_input('theme_of', 'cong_theme_care_of', $value);?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->event_care_of) ? $care_of_clients->event_care_of : '' ?>
                        <?php echo render_input('event_care_of', 'cong_event_care_of', $value);?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->solution) ? $care_of_clients->solution : '' ?>
                        <?php echo render_input('solution', 'cong_solution_care_of', $value)?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->rating) ? $care_of_clients->rating : '' ?>
                        <?php echo render_select('rating', $rating, ['id', 'name'], 'cong_vip_rating', $value);?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->date_contact) ? _dt($care_of_clients->date_contact) : '' ?>
                        <?php echo render_datetime_input('date_contact', 'cong_date_contact', $value)?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                            <select name="priority" class="selectpicker" id="priority" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php foreach(get_care_of_clients_priorities() as $priority) { ?>
                                    <option value="<?php echo $priority['id']; ?>"<?php if((isset($care_of_clients) && $care_of_clients->priority == $priority['id']) || (empty($care_of_clients) && $priority['id'] == 2) ){echo ' selected';} ?>><?php echo $priority['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php $value = !empty($care_of_clients->note) ? $care_of_clients->note : '' ?>
                        <?php echo render_textarea('note', 'cong_note_care_of_client', $value)?>
                    </div>

                    <?php $id = !empty($care_of_clients->id) ? $care_of_clients->id : '' ?>
                    <?php echo form_hidden('id', $id); ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>

    $(function(){
       appValidateForm($('#care_of_from'), {
           client: 'required',
           date: 'required',
           status_procedure: 'required'
        }, manage_advisory);

        function manage_advisory(form) {
            var button = $('#care_of_from').find('button[type="submit"]');
            button.button({loadingText: '<?=_l('cong_please_wait')?>'});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.success == true) {
                    if($.fn.DataTable.isDataTable('.table-care_of_clients')){
                        $('.table-care_of_clients').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                    var id_facebook = $('#id_facebook').val();
                    if(id_facebook)
                    {
                        varInfoUser(id_facebook);
                    }
                }
                $('#modal_care_of_clients').modal('hide');
            }).always(function() {
                button.button('reset')
            });
            return false;
        }
        $('#care_of_from').find('.selectpicker').selectpicker('refresh');
        init_datepicker();
    })

</script>
