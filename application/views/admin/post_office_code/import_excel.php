<div id="modal_import_excel_machines" class="modal fade" role="dialog">
    <form action="<?= admin_url('import_excel/action_imports_client') ?>" id="post_office_code_import" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= $title ?></h4>
                </div>
                <div class="modal-body">
                    <a target="_blank" href="<?= base_url('uploads/template/Template_post_office_code.xlsx?vs=1.3') ?>">Download file mẫu</a>
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>File excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import" required="">
                        </span>
                        <div class="show-errors text-danger"></div>
                    </div>
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
    $('#modal_import_excel_machines').modal('show');

    function get_data_file_excel() {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var fileInput = document.getElementById('file_import');
        var filePath = fileInput.value; //lấy giá trị input theo id
        if (filePath != "") {
            var allowedExtensions = /(\.XLSX|\.XLS)$/i; //các tập tin cho phép
            //Kiểm tra định dạng
            if (!allowedExtensions.exec(filePath)) {
                alert('Vui lòng upload các file có định dạng: .XLSX/.XLS only.');
                fileInput.value = '';
                return false;
            }
        }
        var url = admin_url + 'post_office_code/import/<?= $type ?>';
        var file_data = $('#post_office_code_import input#file_import').prop('files');
        var form_data = new FormData();
        $.each(file_data, function(infile, valFile) {
            form_data.append('file', valFile);
        })
        form_data.append(csrfData['token_name'], csrfData['hash']);
        $('.show-errores').html('');
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            success: function(data) {
                data = JSON.parse(data);
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                $('.show-errors').html(data.errors);
                if (!data.errors) {
                    $('#modal_import_excel_machines').modal('hide');
                }
                alert_float(data.alert_type, data.message);
            }
        });
        return false;
    }
</script>