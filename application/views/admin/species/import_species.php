<div id="modal_import_excel" class="modal fade" role="dialog">
    <form action="<?=admin_url('categories/import_capacity')?>" id="import_form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= $title ?></h4>
                </div>
                <div class="modal-body">
                    <a target="_blank" href="<?=base_url('uploads/template/template_Chung_loai.xlsx?vs=1.1')?>">Download file mẫu</a>
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>File excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import" required="">
                        </span>
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
    $('#modal_import_excel').modal('show');
    function get_data_file_excel() {
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
        var url = admin_url + 'species/import_species';
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
                $('#modal_import_excel').modal('hide');
                alert_float(data.alert_type, data.message);
                if (typeof oTable !== 'undefined') {
                    oTable.draw();
                }
            }
        });
        return false;
    }
</script>