<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
<!--                <a class="btn btn-info test H_action_button">-->
<!--					--><?php //echo _l('Export excel'); ?><!--</a>-->
            </div>
            <div class="pull-right mright5 H_border">
                <a href="#" id="add_new" class="btn btn-info H_action_button" data-toggle="modal" data-target="#evaluation_criteria">
					<?php echo _l('create_add_new'); ?></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <input type="text" name="view" value="," class="hide">
						<?php render_datatable(array(
							_l('ID'),
							_l('Mã tiêu chú'),
							_l('ch_name_evaluation_criteria'),
							_l('Màu'),
							_l('ch_option'),
						),
							'evaluation_criteria'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="evaluation_criteria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
						<?php echo _l('ch_evaluation_criteria'); ?>
                    </h4>
                </div>
				<?php echo form_open_multipart(admin_url('suppliers/add_evaluation_criteria'), array('class' => 'evaluation_criteria-form', 'autocomplete' => 'off')); ?>
                <div class="modal-body">
					<?php echo render_input('code_evaluation_criteria', 'Mã tiêu chí đánh giá NCC'); ?>
					<?php echo render_input('name', 'ch_evaluation_criteria'); ?>
                    <label class="bold mbot10 inline-block"><?= _l('kb_group_color') ?></label>
                    <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                        <input type="text" value="" name="color" id="color" class="form-control colorpicker">
                        <span class="input-group-addon">
                            <i class="i_color"></i>
                        </span>
                    </div>
                    <input type="text" class="hide" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="evaluation_criteria_children" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
						<?php echo _l('ch_evaluation_criteria_children'); ?>
                    </h4>
                </div>
				<?php echo form_open_multipart(admin_url('suppliers/add_evaluation_criteria_children'), array('class' => 'evaluation_criteria_children-form', 'autocomplete' => 'off')); ?>
                <div class="modal-body">
					<?php echo render_input('code_children', 'Mã tiêu chí đánh giá NCC'); ?>
					<?php echo render_input('name_children', 'ch_evaluation_criteria'); ?>
                    <input type="text" class="hide" name="id_evaluation" id="id_evaluation">
                    <input type="text" class="hide" name="id_evaluation_children" id="id_evaluation_children">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
	<?php init_tail(); ?>
    <script>
        $('#add_new').click(function (e) {
            $('#code_evaluation_criteria').val('');
            $('#name').val('');
            $('#color').colorpicker();
            $('#color').val('');
            $(".i_color").attr("style", "");
        });
        appValidateForm($('.evaluation_criteria_children-form'), {
            code_children: 'required',
            name_children: 'required'
        }, manage_evaluation_criteria_children);
        
        function manage_evaluation_criteria_children(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                response = JSON.parse(response);
                if (response.alert_type == "success") {
                    if ($.fn.DataTable.isDataTable('.table-evaluation_criteria')) {
                        $('.table-evaluation_criteria').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                    $('#evaluation_criteria_children').modal('hide');
                } else {
                    alert_float('danger', response.message);
                }
            });
            return false;
        }
        
        $('#evaluation_criteria_children').on('hidden.bs.modal', function () {
            $('#id_evaluation').val('');
            $('#id_evaluation_children').val('');
            $('#code_children').val('');
            $('#name_children').val('');
        })
        
        $('#evaluation_criteria').on('hidden.bs.modal', function () {
            $('#id').val('');
            $('#code_evaluation_criteria').val('');
            $('#name').val('');
        })
        
        function add_evaluation_criteria_children(id) {
            $('#evaluation_criteria_children').modal('show');
            $('#id_evaluation').val(id);
        }
        
        function edit_evaluation_criteria_children(id) {
            jQuery.ajax({
                type: "post",
                url: admin_url + "suppliers/get_evaluation_criteria_children/" + id,
                data: {[csrfData['token_name']]: csrfData['hash']},
                cache: false,
                success: function (data) {
                    var json = JSON.parse(data);
                    {
                        $('#id_evaluation').val(json.id_evaluation);
                        $('#id_evaluation_children').val(id);
                        $('#code_children').val(json.code_children);
                        $('#name_children').val(json.name_children);
                        $('#evaluation_criteria_children').modal('show');
                    }
                }
            });
        }
        
        function edit_evaluation_criteria(id) {
            jQuery.ajax({
                type: "post",
                url: admin_url + "suppliers/get_evaluation_criteria/" + id,
                data: {[csrfData['token_name']]: csrfData['hash']},
                cache: false,
                success: function (data) {
                    var json = JSON.parse(data);
                    {
                        $('#id').val(id);
                        $('#code_evaluation_criteria').val(json.code_evaluation_criteria);
                        $('#name').val(json.name);
                        $('#color').val(json.color);
                        $(".i_color").attr("style", "background: " + json.color);
                        $('#color').colorpicker();
                        $('#evaluation_criteria').modal('show');
                    }
                }
            });
        }
        
        appValidateForm($('.evaluation_criteria-form'), {
            code_evaluation_criteria: 'required',
            name: 'required'
        }, manage_evaluation_criteria);
        
        function manage_evaluation_criteria(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                response = JSON.parse(response);
                if (response.alert_type == "success") {
                    if ($.fn.DataTable.isDataTable('.table-evaluation_criteria')) {
                        $('.table-evaluation_criteria').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                    $('#evaluation_criteria').modal('hide');
                }
                else {
                    alert_float('danger', response.message);
                }
            });
            return false;
        }
        
        $(document).on('click', '.delete-remind', function () {
            var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
                $.get($(this).attr('href'), function (response) {
                    alert_float(response.alert_type, response.message);
                    $('.table-evaluation_criteria').DataTable().ajax.reload();
                }, 'json');
            }
            return false;
        });
        $(function () {
            var fnServerParams = {
                "view": '[name="view"]',
            };
            var notSortableAndSearchableItemColumns = [];
            initDataTable('.table-evaluation_criteria', '<?=admin_url('suppliers/table_evaluation_criteria/')?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, fnServerParams, '');
        });
        
        function view(id) {
            var view = $('[name="view"]').val();
            view = view + id + ',';
            $('[name="view"]').val(view);
            $('.table-evaluation_criteria').DataTable().ajax.reload();
        }
        
        function no_view(id) {
            var view = $('[name="view"]').val();
            view = view.replace(',' + id + ',', ',');
            $('[name="view"]').val(view);
            $('.table-evaluation_criteria').DataTable().ajax.reload();
        }
    </script>
    </body>
    </html>
