<div id="modal_import_excel" class="modal fade" role="dialog">
    <form action="<?= admin_url('quote_stage/import_full') ?>?>" id="import_form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
                </div>
                <div class="modal-body">
                    <a target="_blank" href="<?= base_url('uploads/template/import_quote_stage_full.xlsx?vs=1.1') ?>">Download file mẫu</a>
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>File excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import" required="">
                        </span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="show-errors" id="import_errors" style="max-height:300px;overflow-y:auto;"></div>
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
        var filePath = fileInput.value;
        if (filePath != "") {
            var allowedExtensions = /(\.XLSX|\.XLS)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Vui lòng upload các file có định dạng: .XLSX/.XLS only.');
                fileInput.value = '';
                return false;
            }
        }
        var url = admin_url + 'quote_stage/import_full';
        var file_data = $('#import_form input#file_import').prop('files');
        var form_data = new FormData();
        $.each(file_data, function(infile, valFile) {
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
            success: function(data) {
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
                if (data.errors && data.errors.length > 0) {
                    window.importErrors = data.errors;
                    var grouped = {};
                    $.each(data.errors, function(i, err) {
                        var sheet = err.sheet || 'Không rõ Sheet';
                        var code = err.code || 'Không rõ Bảng giá';
                        if (!grouped[sheet]) {
                            grouped[sheet] = {};
                        }
                        if (!grouped[sheet][code]) {
                            grouped[sheet][code] = [];
                        }
                        grouped[sheet][code].push(err);
                    });

                    var errHtml = '<div style="margin-top: 15px; font-family: sans-serif;">';
                    errHtml += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
                    errHtml += '  <h5 class="text-danger" style="font-weight: bold; margin: 0;"><i class="fa fa-exclamation-triangle"></i> Danh sách lỗi phát hiện:</h5>';
                    errHtml += '  <button type="button" class="btn btn-warning btn-xs" style="margin-right: 10px;" onclick="exportErrorsToExcel()"><i class="fa fa-file-excel-o"></i> Xuất excel</button>';
                    errHtml += '</div>';

                    $.each(grouped, function(sheetName, codes) {
                        errHtml += '<div class="sheet-error-group" style="margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">';
                        errHtml += '  <div style="background-color: #f5f5f5; padding: 8px 12px; font-weight: bold; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between;">';
                        errHtml += '    <span><i class="fa fa-table text-info"></i> Sheet: ' + sheetName + '</span>';
                        errHtml += '  </div>';
                        errHtml += '  <div style="padding: 10px;">';

                        $.each(codes, function(codeName, errList) {
                            errHtml += '    <details style="margin-bottom: 8px; border: 1px solid #e3e3e3; border-radius: 4px; background: #fff;" open>';
                            errHtml += '      <summary style="padding: 8px 12px; font-weight: 600; color: #c7254e; background-color: #fcf8e3; cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none;">';
                            errHtml += '        <span><i class="fa fa-folder-open"></i> Bảng giá: ' + codeName + ' (' + errList.length + ' lỗi)</span>';
                            errHtml += '        <span style="font-size: 11px; color: #666;"><i class="fa fa-chevron-down"></i> Click để thu gọn/mở rộng</span>';
                            errHtml += '      </summary>';
                            errHtml += '      <div style="padding: 10px; max-height: 200px; overflow-y: auto; border-top: 1px solid #e3e3e3;">';
                            errHtml += '        <ul class="list-unstyled" style="margin: 0; padding-left: 5px;">';

                            $.each(errList, function(j, errorItem) {
                                errHtml += '          <li style="margin-bottom: 6px; font-size: 13px; line-height: 1.4; display: flex; align-items: flex-start;">';
                                errHtml += '            <span class="label label-danger" style="margin-right: 8px; margin-top: 1px; flex-shrink: 0;">Dòng ' + errorItem.row + '</span>';
                                errHtml += '            <span style="color: #333;">' + errorItem.message + '</span>';
                                errHtml += '          </li>';
                            });

                            errHtml += '        </ul>';
                            errHtml += '      </div>';
                            errHtml += '    </details>';
                        });

                        errHtml += '  </div>';
                        errHtml += '</div>';
                    });

                    errHtml += '</div>';
                    $('#import_errors').html(errHtml);
                } else {
                    window.importErrors = [];
                    $('#import_errors').html('');
                }

                if (data.is_success) {
                    if (typeof tAPI !== 'undefined') {
                        tAPI.draw();
                    }
                }
            }
        });
        return false;
    }

    function exportErrorsToExcel() {
        if (!window.importErrors || window.importErrors.length === 0) {
            alert_float('warning', 'Không có dữ liệu lỗi để xuất.');
            return;
        }

        var csvContent = "\uFEFF"; // UTF-8 BOM
        csvContent += "Sheet,Bảng giá,Dòng,Chi tiết lỗi\n";

        window.importErrors.forEach(function(err) {
            var sheet = err.sheet || '';
            var code = err.code || '';
            var row = err.row || '';
            var message = err.message || '';

            var rowData = [
                '"' + sheet.replace(/"/g, '""') + '"',
                '"' + code.replace(/"/g, '""') + '"',
                '"' + row + '"',
                '"' + message.replace(/"/g, '""') + '"'
            ];
            csvContent += rowData.join(",") + "\n";
        });

        var blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        var url = URL.createObjectURL(blob);
        var link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "danh_sach_loi_import_" + new Date().getTime() + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>