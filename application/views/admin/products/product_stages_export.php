<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .td-input-field {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    .delete_input_field i {
        color: #949494;
        font-size: 1.5em;
    }

    .delete_input_field i:hover {
        color: #000;
        cursor: pointer;
    }

    .panel_box {
        margin: 0;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, .2), 0 2px 2px 0 rgba(0, 0, 0, .14), 0 1px 5px 0 rgba(0, 0, 0, .12);
    }

    .head-setting {
        font-weight: 500;
    }

    .line-head-setting {
        border-bottom: 1px solid #ccc;
    }

    .div-note img {
        width: 100px;
        height: 100px;
    }

    .div-note table {
        width: 100px !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.2') ?>">
<div id="wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Xuất quy trình sản phẩm</h4>
        </div>

        <div class="panel-body">
            <div style="margin-bottom: 15px;">
                <input type="text" id="keyword" class="form-control" placeholder="Tìm mã hoặc tên sản phẩm..." style="max-width: 300px; display: inline-block;">

                <button type="button" id="btnStartExport" class="btn btn-primary">
                    Tải dữ liệu
                </button>

                <button type="button" id="btnStopExport" class="btn btn-danger" style="display:none;">
                    Dừng
                </button>
            </div>

            <div id="exportStatus" style="margin-bottom: 10px;">
                Chưa tải dữ liệu.
            </div>

            <div class="progress" style="height: 20px;">
                <div
                    id="exportProgressBar"
                    class="progress-bar progress-bar-success"
                    role="progressbar"
                    style="width: 0%;">
                    0%
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="productExportTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">STT</th>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Loài</th>
                            <th>Công đoạn</th>
                            <th>Máy</th>
                            <th>Thứ tự</th>
                        </tr>
                    </thead>

                    <tbody id="productExportBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    var lang_product = <?= json_encode(array('tnh_sequence' => lang('tnh_sequence'), 'tnh_stage' => lang('tnh_stage'), 'tnh_number_date' => lang('tnh_number_date'), 'tnh_number_date' => lang('tnh_number_date'))) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        category_search: '#category_search',
        products_search: '#products_search',
        code_bom_search: '#code_bom_search',
        date_start_search: '#date_start_search',
        date_end_search: '#date_end_search',
    };
    var oTable = '';
    var iDt = 0;
</script>
<script>
    let exportOffset = 0;
    let exportLimit = 100;
    let exportRunning = false;
    let exportStopped = false;

    $('#btnStartExport').on('click', function() {
        exportOffset = 0;
        exportRunning = true;
        exportStopped = false;

        $('#productExportBody').html('');
        $('#btnStartExport').prop('disabled', true);
        $('#btnStopExport').show();

        updateProgress(0, 0);

        loadProductExportChunk();
    });

    $('#btnStopExport').on('click', function() {
        exportStopped = true;
        exportRunning = false;

        $('#btnStartExport').prop('disabled', false);
        $('#btnStopExport').hide();

        $('#exportStatus').text('Đã dừng tải dữ liệu.');
    });

    function loadProductExportChunk() {
        if (!exportRunning || exportStopped) {
            return;
        }

        $.ajax({
            url: '<?= admin_url('products/ajax_product_stages_export_chunk') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                offset: exportOffset,
                limit: exportLimit,
                keyword: $('#keyword').val()
            },
            success: function(res) {
                if (!res || !res.success) {
                    exportRunning = false;
                    $('#btnStartExport').prop('disabled', false);
                    $('#btnStopExport').hide();
                    $('#exportStatus').text('Có lỗi khi tải dữ liệu.');
                    return;
                }

                $('#productExportBody').append(res.html);

                exportOffset = res.next_offset;

                updateProgress(res.loaded, res.total);

                if (res.done) {
                    exportRunning = false;

                    $('#btnStartExport').prop('disabled', false);
                    $('#btnStopExport').hide();

                    $('#exportStatus').text('Đã tải xong ' + res.total + ' sản phẩm.');
                    return;
                }

                setTimeout(function() {
                    loadProductExportChunk();
                }, 100);
            },
            error: function() {
                exportRunning = false;

                $('#btnStartExport').prop('disabled', false);
                $('#btnStopExport').hide();

                $('#exportStatus').text('Lỗi kết nối khi tải dữ liệu.');
            }
        });
    }

    function updateProgress(loaded, total) {
        let percent = 0;

        if (total > 0) {
            percent = Math.round((loaded / total) * 100);
        }

        $('#exportStatus').text('Đã tải ' + loaded + ' / ' + total + ' sản phẩm.');

        $('#exportProgressBar')
            .css('width', percent + '%')
            .text(percent + '%');
    }
</script>