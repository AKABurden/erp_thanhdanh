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
            <h4>Xuất Excel công đoạn sản phẩm</h4>
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
                    <button type="button" id="btnExportProductStage" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Xuất Excel
                    </button>

                    <button type="button" id="btnStopExportProductStage" class="btn btn-danger" style="display:none;">
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
let stageExportId = null;
let stageExportOffset = 0;
let stageExportLimit = 100;
let stageExportRunning = false;
let stageExportStopped = false;

$('#btnExportProductStage').on('click', function () {
    stageExportId = null;
    stageExportOffset = 0;
    stageExportLimit = parseInt($('#export_limit').val()) || 100;
    stageExportRunning = true;
    stageExportStopped = false;

    $('#btnExportProductStage').prop('disabled', true);
    $('#btnStopExportProductStage').show();

    $('#exportStatus').text('Đang khởi tạo file xuất...');
    updateStageExportProgress(0, 0);

    $.ajax({
        url: '<?= admin_url('export_data/init_export_product_stage') ?>',
        type: 'POST',
        dataType: 'json',
        data: getStageExportFilters(),
        success: function (res) {
            if (!res || !res.success) {
                resetStageExportButton();
                $('#exportStatus').text('Không thể khởi tạo file xuất.');
                return;
            }

            stageExportId = res.export_id;

            if (parseInt(res.total) <= 0) {
                resetStageExportButton();
                $('#exportStatus').text('Không có dữ liệu để xuất.');
                updateStageExportProgress(0, 0);
                return;
            }

            $('#exportStatus').text('Đang xuất dữ liệu...');
            exportProductStageChunk();
        },
        error: function () {
            resetStageExportButton();
            $('#exportStatus').text('Lỗi khi khởi tạo file xuất.');
        }
    });
});

$('#btnStopExportProductStage').on('click', function () {
    stageExportStopped = true;
    stageExportRunning = false;

    resetStageExportButton();

    $('#exportStatus').text('Đã dừng xuất dữ liệu.');
});

function exportProductStageChunk() {
    if (!stageExportRunning || stageExportStopped || !stageExportId) {
        return;
    }

    let data = getStageExportFilters();

    data.export_id = stageExportId;
    data.offset = stageExportOffset;
    data.limit = stageExportLimit;

    $.ajax({
        url: '<?= admin_url('export_data/export_product_stage_chunk') ?>',
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (res) {
            if (!res || !res.success) {
                resetStageExportButton();
                $('#exportStatus').text(res && res.message ? res.message : 'Có lỗi khi xuất dữ liệu.');
                return;
            }

            stageExportOffset = res.next_offset;

            updateStageExportProgress(res.loaded, res.total);

            if (res.done) {
                stageExportRunning = false;
                stageExportStopped = false;

                resetStageExportButton();

                $('#exportStatus').text('Xuất xong. Đang tải file...');

                let downloadUrl = '<?= admin_url('export_data/download_product_stage_excel') ?>'
                    + '?export_id=' + encodeURIComponent(stageExportId);

                window.location.href = downloadUrl;

                return;
            }

            setTimeout(function () {
                exportProductStageChunk();
            }, 100);
        },
        error: function () {
            resetStageExportButton();
            $('#exportStatus').text('Lỗi kết nối khi xuất dữ liệu.');
        }
    });
}

function getStageExportFilters() {
    return {
        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
        keyword: $('#keyword').val(),
        category_id: $('#category_id').val()
    };
}

function updateStageExportProgress(loaded, total) {
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

function resetStageExportButton() {
    stageExportRunning = false;

    $('#btnExportProductStage').prop('disabled', false);
    $('#btnStopExportProductStage').hide();
}
</script>
