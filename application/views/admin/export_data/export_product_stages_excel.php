<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    table tr td {
        vertical-align: middle !important;
    }

    .panel_box {
        margin: 0;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, .2), 0 2px 2px 0 rgba(0, 0, 0, .14), 0 1px 5px 0 rgba(0, 0, 0, .12);
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.2') ?>">
<div id="wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Xuất Excel quy trình sản phẩm</h4>
        </div>

        <div class="panel-body">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-3">
                    <input
                        type="text"
                        id="keyword"
                        class="form-control"
                        placeholder="Tìm mã hoặc tên sản phẩm..."
                    >
                </div>

                <div class="col-md-3">
                    <input
                        type="text"
                        id="category_id"
                        class="form-control"
                        placeholder="ID danh mục nếu cần lọc..."
                    >
                </div>

                <div class="col-md-3">
                    <select id="export_limit" class="form-control">
                        <option value="50">50 sản phẩm/lần</option>
                        <option value="100" selected>100 sản phẩm/lần</option>
                        <option value="200">200 sản phẩm/lần</option>
                        <option value="300">300 sản phẩm/lần</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="button" id="btnExportProductStages" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Xuất Excel
                    </button>

                    <button type="button" id="btnStopExportProductStages" class="btn btn-danger" style="display:none;">
                        <i class="fa fa-stop"></i> Dừng
                    </button>
                </div>
            </div>

            <div id="exportStatus" style="margin-bottom: 10px;">
                Chưa xuất dữ liệu.
            </div>

            <div class="progress" style="height: 22px;">
                <div
                    id="exportProgressBar"
                    class="progress-bar progress-bar-success"
                    role="progressbar"
                    style="width: 0%;">
                    0%
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
let productExportId = null;
let productExportOffset = 0;
let productExportLimit = 100;
let productExportRunning = false;
let productExportStopped = false;

$('#btnExportProductStages').on('click', function () {
    productExportId = null;
    productExportOffset = 0;
    productExportLimit = parseInt($('#export_limit').val()) || 100;
    productExportRunning = true;
    productExportStopped = false;

    $('#btnExportProductStages').prop('disabled', true);
    $('#btnStopExportProductStages').show();

    $('#exportStatus').text('Đang khởi tạo file xuất...');
    updateExportProgress(0, 0);

    $.ajax({
        url: '<?= admin_url('export_data/init_export_product_stages_excel') ?>',
        type: 'POST',
        dataType: 'json',
        data: getExportFilters(),
        success: function (res) {
            if (!res || !res.success) {
                resetExportButton();
                $('#exportStatus').text('Không thể khởi tạo file xuất.');
                return;
            }

            productExportId = res.export_id;

            if (parseInt(res.total) <= 0) {
                resetExportButton();
                $('#exportStatus').text('Không có dữ liệu để xuất.');
                updateExportProgress(0, 0);
                return;
            }

            $('#exportStatus').text('Đang xuất dữ liệu...');
            exportProductStagesChunk();
        },
        error: function () {
            resetExportButton();
            $('#exportStatus').text('Lỗi khi khởi tạo file xuất.');
        }
    });
});

$('#btnStopExportProductStages').on('click', function () {
    productExportStopped = true;
    productExportRunning = false;

    resetExportButton();

    $('#exportStatus').text('Đã dừng xuất dữ liệu.');
});

function exportProductStagesChunk() {
    if (!productExportRunning || productExportStopped || !productExportId) {
        return;
    }

    let data = getExportFilters();

    data.export_id = productExportId;
    data.offset = productExportOffset;
    data.limit = productExportLimit;

    $.ajax({
        url: '<?= admin_url('export_data/export_product_stages_excel_chunk') ?>',
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (res) {
            if (!res || !res.success) {
                resetExportButton();
                $('#exportStatus').text(res && res.message ? res.message : 'Có lỗi khi xuất dữ liệu.');
                return;
            }

            productExportOffset = res.next_offset;

            updateExportProgress(res.loaded, res.total);

            if (res.done) {
                productExportRunning = false;
                productExportStopped = false;

                resetExportButton();

                $('#exportStatus').text('Xuất xong. Đang tải file...');

                let downloadUrl = '<?= admin_url('export_data/download_product_stages_excel') ?>'
                    + '?export_id=' + encodeURIComponent(productExportId);

                window.location.href = downloadUrl;

                return;
            }

            setTimeout(function () {
                exportProductStagesChunk();
            }, 100);
        },
        error: function () {
            resetExportButton();
            $('#exportStatus').text('Lỗi kết nối khi xuất dữ liệu.');
        }
    });
}

function getExportFilters() {
    return {
        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
        keyword: $('#keyword').val(),
        category_id: $('#category_id').val()
    };
}

function updateExportProgress(loaded, total) {
    let percent = 0;

    loaded = parseInt(loaded) || 0;
    total = parseInt(total) || 0;

    if (total > 0) {
        percent = Math.round((loaded / total) * 100);
    }

    $('#exportStatus').text('Đã xuất ' + loaded + ' / ' + total + ' sản phẩm.');

    $('#exportProgressBar')
        .css('width', percent + '%')
        .text(percent + '%');
}

function resetExportButton() {
    productExportRunning = false;

    $('#btnExportProductStages').prop('disabled', false);
    $('#btnStopExportProductStages').hide();
}
</script>
