<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a class="btn btn-info pull-right H_action_button" onclick="add(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('create_add_new'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                            _l('STT'),
                            _l('slide_content'),
                            _l('slide_image'),
                            _l('options'),
                        ),'slide'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_slide" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <?php echo form_open_multipart(admin_url('slide/add'),array('id'=>'form_slide','autocomplete'=>'off')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_slide'); ?></span>
                    <span class="add-title"><?php echo _l('add_slide'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            echo render_input('stt','stt_slide','','number'); 
                        ?>
                    </div>
                    <div class="col-md-12">
                        <?php
                            echo render_textarea('content','slide_content','',array(),array(),'','tinymce'); 
                        ?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="image"><?php echo _l('slide_image'); ?></label>
                            <input type="file" name="image" class="form-control" id="image"> <br />

                            <div class="preview_image hide">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-slide', window.location.href, [0], [0], [], [0, 'DESC']);
    });

    function add() {
        $('.add-title').removeClass('hide');
        $('.edit-title').addClass('hide');
        $('#form_slide').attr("action","<?=admin_url('slide/add')?>");

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'slide/getData_add', data).done(function(response){
            $('#stt').val(response);
            $('#modal_slide').modal({backdrop: 'static', keyboard: false});
        });
    }

    function edit(id) {
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('#form_slide').attr("action","<?=admin_url('slide/edit/')?>"+id);

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'slide/getData_edit/'+id, data).done(function(response){
            response = JSON.parse(response);
            $('#stt').val(response.stt);
            $('#content').val(response.content);
            $('#modal_slide').modal({backdrop: 'static', keyboard: false});
        });
    }

    //load trang voi action
    $(function(){
        _validate_form($('#form_slide'),{stt:'required',content:'required'},add_slide_s);
    });
    //end

    function add_slide_s(form) {
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
            url : url,
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
        .done(function(data) {
            alert_float(data.alert_type, data.message);
            $('.table-slide').DataTable().ajax.reload();
            $('#modal_slide').modal('hide');
        })
        .fail(function() {
        });
        return false;
    }
</script>
</body>
</html>
