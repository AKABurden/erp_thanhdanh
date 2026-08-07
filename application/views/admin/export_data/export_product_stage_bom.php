<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?= $title ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keyword">Từ khóa (Mã/Tên Thành phẩm)</label>
                                    <input type="text" id="keyword" class="form-control" placeholder="Nhập mã hoặc tên sản phẩm...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Danh mục sản phẩm</label>
                                    <select id="category_id" class="form-control selectpicker" data-live-search="true" multiple="true" data-actions-box="true" title="-- Tất cả danh mục --">
                                        <?php
                                        $categories = get_table_where('tbl_category_products');
                                        foreach ($categories as $cat) {
                                            echo '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop20">
                            <div class="col-md-12 text-right">
                                <button type="button" id="btn-start-export" class="btn btn-info">
                                    <i class="fa fa-file-excel-o"></i> Bắt đầu xuất Excel
                                </button>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div id="export-progress-container" class="mtop20" style="display: none;">
                            <h5 id="export-status-text">Đang phân tích dữ liệu...</h5>
                            <div class="progress">
                                <div id="export-progress-bar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    0%
                                </div>
                            </div>
                            <p id="export-details" class="text-muted"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    $(function() {
        var exportId = '';
        var limit = 50; // Export chunk size

        $('#btn-start-export').on('click', function() {
            var btn = $(this);
            btn.button('loading');
            $('#export-progress-container').show();
            $('#export-progress-bar').css('width', '0%').text('0%').addClass('active');
            $('#export-status-text').text('Đang khởi tạo file Excel...');
            $('#export-details').text('');

            // init export
            $.ajax({
                url: admin_url + 'export_data/init_export_product_stage_bom',
                type: 'POST',
                dataType: 'json',
                data: {
                    keyword: $('#keyword').val(),
                    category_id: $('#category_id').val(),
                    [csrfData.token_name]: csrfData.hash
                },
                success: function(res) {
                    if (res.success) {
                        exportId = res.export_id;
                        var total = res.total;
                        if (total === 0) {
                            $('#export-status-text').text('Không có dữ liệu phù hợp.');
                            $('#export-progress-bar').removeClass('active');
                            btn.button('reset');
                            return;
                        }

                        $('#export-status-text').text('Đang xuất dữ liệu...');
                        processChunk(0, limit, total);
                    } else {
                        alert_float('danger', res.message || 'Lỗi khởi tạo');
                        btn.button('reset');
                    }
                },
                error: function() {
                    alert_float('danger', 'Lỗi kết nối máy chủ');
                    btn.button('reset');
                }
            });
        });

        function processChunk(offset, limit, total) {
            $.ajax({
                url: admin_url + 'export_data/export_product_stage_bom_chunk',
                type: 'POST',
                dataType: 'json',
                data: {
                    export_id: exportId,
                    offset: offset,
                    limit: limit,
                    keyword: $('#keyword').val(),
                    category_id: $('#category_id').val(),
                    [csrfData.token_name]: csrfData.hash
                },
                success: function(res) {
                    if (res.success) {
                        var percent = Math.round((res.loaded / total) * 100);
                        $('#export-progress-bar').css('width', percent + '%').text(percent + '%');
                        $('#export-details').text('Đã xử lý ' + res.loaded + ' / ' + total + ' sản phẩm');

                        if (res.done) {
                            $('#export-status-text').text('Hoàn tất! Đang tải file xuống...');
                            $('#export-progress-bar').removeClass('active');
                            $('#btn-start-export').button('reset');

                            // Trigger download
                            window.location.href = admin_url + 'export_data/download_product_stage_bom_excel?export_id=' + exportId;
                        } else {
                            processChunk(res.next_offset, limit, total);
                        }
                    } else {
                        alert_float('danger', res.message || 'Lỗi trong quá trình xuất');
                        $('#btn-start-export').button('reset');
                    }
                },
                error: function() {
                    alert_float('danger', 'Lỗi kết nối máy chủ khi tải chunk');
                    $('#btn-start-export').button('reset');
                }
            });
        }
    });
</script>
</body>
</html>
