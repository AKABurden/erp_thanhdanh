<script src="http://malsup.github.com/jquery.form.js"></script> 
<div class="modal fade" id="view_video" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('videos/edit_upload/'.$main->id),array('id'=>'id_videos')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Thêm mới videos'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('title','Tiêu đề video',$main->name); ?>
                        <?php echo render_select('type', $type, array('id', 'name'), 'Loại video',$main->type); ?>
                        <video  width="400" controls>
                          <source src="<?=base_url($main->link)?>" type="<?=$main->type_videos?>">
                        </video>
                        <?php echo form_textarea('note', $main->note, 'placeholder="'.lang('Mô tả video').'" id="note" class="form-control input-tip tinymce"'); ?>
                        <div id="targetLayer" style="display:none;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" id="uploadSubmit" value="Lưu" class="btn btn-info" />
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('body').on('hidden.bs.modal', '#view_video', function() {
        $('#view_video_html').html('');
        tinymce.remove();
    }); 
    $(document).ready(function() {
        init_editor('textarea[name="note"]');
    });
    appValidateForm($('#id_videos'), {
        title:'required',
        type:'required',
        },manage_videos);
    function manage_videos(form) {
        tinymce.get('note').save();
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-videos')){
                    $('.table-videos').DataTable().ajax.reload();
                }
                alert_float(response.alert_type, response.message);
                $('#view_video').modal('hide');
            }
        })
        return false;


            var data = $(form).serializeArray();
            var url = form.action;
            $.each(data, function(key, Val){
                form_data.append(Val.name, Val.value);
            })
        $.ajax({
                url: url,
                type       : 'POST',
                contentType: false,
                cache      : false,
                processData: false,
                data       : form_data,
                success    : function ( data )
                {
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    $('#view_video').modal('hide');
                }
            });
        return false;
    }
//     $(document).ready(function(){
//     $('#uploadImage').submit(function(event){
//         alert(123);
//         if($('#uploadFile').val())
//         {
//             event.preventDefault();
//             $('#loader-icon').show();
//             $('#targetLayer').hide();
//             $(this).ajaxSubmit({
//                 target: '#targetLayer',
//                 beforeSubmit:function(){
//                     $('.progress-bar').width('50%');
//                 },
//                 uploadProgress: function(event, position, total, percentageComplete)
//                 {
//                     $('.progress-bar').animate({
//                         width: percentageComplete + '%'
//                     }, {
//                         duration: 1000
//                     });
//                 },
//                 success:function(){
//                     $('#loader-icon').hide();
//                     $('#targetLayer').show();
//                 },
//                 resetForm: true
//             });
//         }
//         return false;
//     });
// });
</script>