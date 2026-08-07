<div id="modal_import_excel" class="modal fade" role="dialog">
    <form action="<?=admin_url('production_report/import')?>" id="import_form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog" style="min-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Import Báo Cáo Vi Phạm (Báo cáo không phù hợp)</h4>
                </div>
                <div class="modal-body">
                    <a target="_blank" href="<?=base_url('uploads/template/Mau_bao_cao_vi_pham.xls?vs=1.7')?>">Download file mẫu</a>
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>File excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import" required="">
                        </span>
                    </div>
                    <div class="text-warning">Lưu ý: Dữ liệu sẽ bắt đầu import dòng số 3, và không nên import quá 40 báo cáo trong 1 lần</div>
                    <hr/>
                    <div id="import_error" class="text-danger"></div>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" onclick="get_data_file_excel()">import</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $('#modal_import_excel').modal('show');

    function get_data_file_excel() {
        $('#import_error').html("");
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var fileInput = document.getElementById('file_import');
        var filePath = fileInput.value;//lấy giá trị input theo id
        if (filePath != "") {
            var allowedExtensions = /(\.XLSX|\.XLS)$/i;//các tập tin cho phép
            //Kiểm tra định dạng
            if (!allowedExtensions.exec(filePath)) {
                alert('Vui lòng upload các file có định dạng: .XLSX/.XLS only.');
                fileInput.value = '';
                return false;
            }
        }
        var url = admin_url + 'production_report/import';
        var file_data = $('#import_form input#file_import').prop('files');
        var form_data = new FormData();
        $.each(file_data, function (infile, valFile) {
            form_data.append('file', valFile);
        })
        form_data.append(csrfData['token_name'], csrfData['hash']);
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            success: function (data) {
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
                console.log(data.error);
                if(data.error != "") {
                    $('#import_error').html(data.error);
                }
                else {
                    if (data.success) {
                        $('#modal_import_excel').modal('hide');
                        if (typeof(TableData) !== 'undefined') {
                            TableData.ajax.reload();
                        }
                    }
                }
            }
        });
        return false;
    }
</script>