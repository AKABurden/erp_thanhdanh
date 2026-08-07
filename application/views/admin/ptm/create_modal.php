<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    /* Premium PTM Modal Styles */
    #myModal .modal-content {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: none;
    }
    
    #myModal .modal-header {
        background: linear-gradient(135deg, #1f2937, #111827);
        color: #fff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 15px 24px;
    }
    
    #myModal .modal-header .close {
        color: #fff;
        opacity: 0.8;
        font-size: 24px;
        margin-top: 0;
    }
    
    #myModal .modal-header .close:hover {
        opacity: 1;
        color: #fff;
    }
    
    #myModal .modal-header .modal-title {
        color: #fff;
        font-weight: 600;
        font-size: 18px;
    }
    
    /* Nav Tabs styling */
    .ptm-tabs {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 20px;
    }
    
    .ptm-tabs li a {
        border: none !important;
        background: transparent !important;
        color: #4b5563 !important;
        font-weight: 500;
        padding: 10px 20px;
        position: relative;
        transition: all 0.2s ease;
        border-radius: 6px 6px 0 0;
    }
    
    .ptm-tabs li a:hover {
        color: #2563eb !important;
        background-color: #f3f4f6 !important;
    }
    
    .ptm-tabs li.active a {
        color: #2563eb !important;
        font-weight: 700;
    }
    
    .ptm-tabs li.active a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #2563eb;
        border-radius: 2px;
    }
    
    /* Tab content container */
    .ptm-tab-content {
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 24px !important;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    /* Section headers */
    .ptm-section-header {
        font-weight: 700 !important;
        color: #1f2937 !important;
        border-left: 4px solid #2563eb;
        padding-left: 12px;
        margin-top: 15px;
        margin-bottom: 20px;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Styling inputs */
    .form-group label {
        font-weight: 600 !important;
        color: #374151;
        margin-bottom: 6px;
        font-size: 13px;
    }
    
    .form-control {
        border-radius: 6px !important;
        border: 1px solid #d1d5db !important;
        box-shadow: none !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        padding: 8px 12px;
    }
    
    .form-control:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    
    /* Static values card */
    .form-control-static {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 12px !important;
        color: #1f2937;
        min-height: 36px;
        margin-top: 4px;
    }
    
    /* Table design */
    .ptm-table {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb !important;
    }
    
    .ptm-table th {
        background-color: #f9fafb !important;
        color: #4b5563 !important;
        font-weight: 600 !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 12px 16px !important;
    }
    
    .ptm-table td {
        padding: 12px 16px !important;
        border-top: 1px solid #e5e7eb !important;
        color: #374151;
    }
    
    .ptm-table tbody tr:hover {
        background-color: #f9fafb;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 95%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Tạo/Cập nhật Phiếu Yêu Cầu Phát Triển Mẫu (PTM)</h4>
        </div>
        <?php echo form_open(admin_url('ptm/save'), array('id' => 'ptm-form')); ?>
        <div class="modal-body" style="max-height: 85vh; overflow-y: auto;">
            <div class="row">
                <input type="hidden" name="order_id" id="order_id" value="<?= $order['id'] ?>">
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ptm_no" class="control-label bold">Số Phiếu YCPTM</label>
                        <input type="text" class="form-control" value="<?= $ptm_no ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date" class="control-label bold">Ngày Lập Phiếu <span class="text-danger">*</span></label>
                        <input type="text" name="date" class="form-control datepicker" value="<?= date('d/m/Y', strtotime(!empty($products_data) ? current($products_data)['existing']['date'] ?? date('Y-m-d') : date('Y-m-d'))) ?>" required>
                    </div>
                </div>

                <div class="col-md-12 mtop15">
                    <!-- Bootstrap Nav Tabs -->
                    <ul class="nav nav-tabs ptm-tabs" role="tablist">
                        <?php 
                        $i = 0;
                        foreach ($products_data as $product_id => $prod) { 
                            $active_class = ($i === 0) ? 'active' : '';
                            $i++;
                        ?>
                            <li role="presentation" class="<?= $active_class ?>">
                                <a href="#tab_product_<?= $product_id ?>" aria-controls="tab_product_<?= $product_id ?>" role="tab" data-toggle="tab">
                                    <span class="bold text-primary"><?= html_escape($prod['item_code']) ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                    <!-- Tab Content Panes -->
                    <div class="tab-content mtop15 ptm-tab-content">
                        <?php 
                        $i = 0;
                        foreach ($products_data as $product_id => $prod) { 
                            $active_class = ($i === 0) ? 'active' : '';
                            $i++;
                            $existing = $prod['existing'] ?? [];
                        ?>
                            <div role="tabpanel" class="tab-pane <?= $active_class ?>" id="tab_product_<?= $product_id ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">A. Yêu Cầu Từ Khách Hàng</h4>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mau_<?= $product_id ?>" class="control-label bold">1. Mẫu</label>
                                            <select name="products[<?= $product_id ?>][mau]" id="mau_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="Y Mẫu Khách Hàng" <?= (isset($existing['mau']) && $existing['mau'] == 'Y Mẫu Khách Hàng') ? 'selected' : '' ?>>Y Mẫu Khách Hàng</option>
                                                <option value="Mẫu Sản Xuất" <?= (isset($existing['mau']) && $existing['mau'] == 'Mẫu Sản Xuất') ? 'selected' : '' ?>>Mẫu Sản Xuất</option>
                                                <option value="Mẫu Khách Hàng Ký Duyệt trước" <?= (isset($existing['mau']) && $existing['mau'] == 'Mẫu Khách Hàng Ký Duyệt trước') ? 'selected' : '' ?>>Mẫu Khách Hàng Ký Duyệt trước</option>
                                                <option value="Mẫu Hàng Hàng Kho" <?= (isset($existing['mau']) && $existing['mau'] == 'Mẫu Hàng Hàng Kho') ? 'selected' : '' ?>>Mẫu Hàng Hàng Kho</option>
                                                <option value="Theo Tờ In Proof" <?= (isset($existing['mau']) && $existing['mau'] == 'Theo Tờ In Proof') ? 'selected' : '' ?>>Theo Tờ In Proof</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="yeu_cau_dac_biet_<?= $product_id ?>" class="control-label bold">2. Yêu Cầu Đặc Biệt</label>
                                            <select name="products[<?= $product_id ?>][yeu_cau_dac_biet]" id="yeu_cau_dac_biet_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="Chạy Hàng Lấy Mẫu" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'Chạy Hàng Lấy Mẫu') ? 'selected' : '' ?>>Chạy Hàng Lấy Mẫu</option>
                                                <option value="Yêu Cầu Tiêu Chuẩn Chất Lượng" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'Yêu Cầu Tiêu Chuẩn Chất Lượng') ? 'selected' : '' ?>>Yêu Cầu Tiêu Chuẩn Chất Lượng</option>
                                                <option value="Croking Test" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'Croking Test') ? 'selected' : '' ?>>Croking Test</option>
                                                <option value="Chạy Y Mẫu Khách Hàng" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'Chạy Y Mẫu Khách Hàng') ? 'selected' : '' ?>>Chạy Y Mẫu Khách Hàng</option>
                                                <option value="NPL Đặc Biệt FSC" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'NPL Đặc Biệt FSC') ? 'selected' : '' ?>>NPL Đặc Biệt FSC</option>
                                                <option value="NPL Đặc Biệt Dán Kỹ Thuật" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'NPL Đặc Biệt Dán Kỹ Thuật') ? 'selected' : '' ?>>NPL Đặc Biệt Dán Kỹ Thuật</option>
                                                <option value="Bồi 2,3 lớp" <?= (isset($existing['yeu_cau_dac_biet']) && $existing['yeu_cau_dac_biet'] == 'Bồi 2,3 lớp') ? 'selected' : '' ?>>Bồi 2,3 lớp</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tieu_chuan_test_<?= $product_id ?>" class="control-label bold">3. Tiêu Chuẩn Test</label>
                                            <select name="products[<?= $product_id ?>][tieu_chuan_test]" id="tieu_chuan_test_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="Crocking Test" <?= (isset($existing['tieu_chuan_test']) && $existing['tieu_chuan_test'] == 'Crocking Test') ? 'selected' : '' ?>>Crocking Test</option>
                                                <option value="Oven Test" <?= (isset($existing['tieu_chuan_test']) && $existing['tieu_chuan_test'] == 'Oven Test') ? 'selected' : '' ?>>Oven Test</option>
                                                <option value="RSL Test" <?= (isset($existing['tieu_chuan_test']) && $existing['tieu_chuan_test'] == 'RSL Test') ? 'selected' : '' ?>>RSL Test</option>
                                                <option value="Test Kim Loại" <?= (isset($existing['tieu_chuan_test']) && $existing['tieu_chuan_test'] == 'Test Kim Loại') ? 'selected' : '' ?>>Test Kim Loại</option>
                                                <option value="Mã Vạch: Level A, B" <?= (isset($existing['tieu_chuan_test']) && $existing['tieu_chuan_test'] == 'Mã Vạch: Level A, B') ? 'selected' : '' ?>>Mã Vạch: Level A, B</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 font-group-wrapper">
                                        <div class="form-group">
                                            <label for="cach_xem_mau_<?= $product_id ?>" class="control-label bold">4. Cách Xem Mẫu</label>
                                            <select name="products[<?= $product_id ?>][cach_xem_mau]" id="cach_xem_mau_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="Xem Bằng Mắt Thường" <?= (isset($existing['cach_xem_mau']) && $existing['cach_xem_mau'] == 'Xem Bằng Mắt Thường') ? 'selected' : '' ?>>Xem Bằng Mắt Thường</option>
                                                <option value="Xem Hộp Lightbox" <?= (isset($existing['cach_xem_mau']) && $existing['cach_xem_mau'] == 'Xem Hộp Lightbox') ? 'selected' : '' ?>>Xem Hộp Lightbox</option>
                                                <option value="Mã Patone" <?= (isset($existing['cach_xem_mau']) && $existing['cach_xem_mau'] == 'Mã Patone') ? 'selected' : '' ?>>Mã Patone</option>
                                                <option value="Share approved" <?= (isset($existing['cach_xem_mau']) && $existing['cach_xem_mau'] == 'Share approved') ? 'selected' : '' ?>>Share approved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="quy_trinh_do_mau_select_<?= $product_id ?>" class="control-label bold">5. Quy Trình Đo Màu (Chọn nhiều & Sắp xếp)</label>
                                            <input type="hidden" name="products[<?= $product_id ?>][quy_trinh_do_mau]" id="quy_trinh_do_mau_<?= $product_id ?>" value="<?= isset($existing['quy_trinh_do_mau']) ? html_escape($existing['quy_trinh_do_mau']) : '' ?>">
                                            <select id="quy_trinh_do_mau_select_<?= $product_id ?>" class="form-control selectpicker quy_trinh_do_mau_select" multiple data-actions-box="true" data-live-search="true" data-product-id="<?= $product_id ?>">
                                                <option value="Đo Theo Điểm Đo">Đo Theo Điểm Đo</option>
                                                <option value="Đo Theo Dãy Mực In">Đo Theo Dãy Mực In</option>
                                                <option value="Đo Đều Màu Trên 1 Tờ In">Đo Đều Màu Trên 1 Tờ In</option>
                                                <option value="Đặt Máy Đo Màu Tại Điểm Đo">Đặt Máy Đo Màu Tại Điểm Đo</option>
                                            </select>
                                            <div class="mtop10">
                                                <ul id="color_process_sortable_<?= $product_id ?>" class="list-group sortable color_process_sortable mbot0" style="min-height: 20px; border: 1px dashed #ddd; padding: 5px; border-radius: 4px; background: #fafafa;">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tieu_chuan_do_mau_<?= $product_id ?>" class="control-label bold">6. Tiêu Chuẩn Đo Màu</label>
                                            <select name="products[<?= $product_id ?>][tieu_chuan_do_mau]" id="tieu_chuan_do_mau_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="Delta E &lt; 1.5, A, B Càng Về 0" <?= (isset($existing['tieu_chuan_do_mau']) && $existing['tieu_chuan_do_mau'] == 'Delta E < 1.5, A, B Càng Về 0') ? 'selected' : '' ?>>Delta E &lt; 1.5, A, B Càng Về 0</option>
                                                <option value="Light-box (D65°/45°)" <?= (isset($existing['tieu_chuan_do_mau']) && $existing['tieu_chuan_do_mau'] == 'Light-box (D65°/45°)') ? 'selected' : '' ?>>Light-box (D65°/45°)</option>
                                                <option value="Density CMYK" <?= (isset($existing['tieu_chuan_do_mau']) && $existing['tieu_chuan_do_mau'] == 'Density CMYK') ? 'selected' : '' ?>>Density CMYK</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="dia_chi_giao_hang_<?= $product_id ?>" class="control-label bold">7. Địa Chỉ Giao Hàng</label>
                                            <textarea name="products[<?= $product_id ?>][dia_chi_giao_hang]" id="dia_chi_giao_hang_<?= $product_id ?>" class="form-control" rows="2"><?= isset($existing['dia_chi_giao_hang']) ? html_escape($existing['dia_chi_giao_hang']) : html_escape($prod['delivery_address']) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tem_dong_goi_<?= $product_id ?>" class="control-label bold">14. Tem đóng gói</label>
                                            <select name="products[<?= $product_id ?>][tem_dong_goi]" id="tem_dong_goi_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="In tờ lẻ" <?= (isset($existing['tem_dong_goi']) && $existing['tem_dong_goi'] == 'In tờ lẻ') ? 'selected' : '' ?>>In tờ lẻ</option>
                                                <option value="In tờ cố định" <?= (isset($existing['tem_dong_goi']) && $existing['tem_dong_goi'] == 'In tờ cố định') ? 'selected' : '' ?>>In tờ cố định</option>
                                                <option value="In tem size" <?= (isset($existing['tem_dong_goi']) && $existing['tem_dong_goi'] == 'In tem size') ? 'selected' : '' ?>>In tem size</option>
                                                <option value="In theo yêu cầu khách" <?= (isset($existing['tem_dong_goi']) && $existing['tem_dong_goi'] == 'In theo yêu cầu khách') ? 'selected' : '' ?>>In theo yêu cầu khách</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="dong_kien_<?= $product_id ?>" class="control-label bold">15. Đóng kiện</label>
                                            <input type="text" name="products[<?= $product_id ?>][dong_kien]" id="dong_kien_<?= $product_id ?>" class="form-control" value="<?= isset($existing['dong_kien']) ? html_escape($existing['dong_kien']) : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="carton_<?= $product_id ?>" class="control-label bold">16. Carton</label>
                                            <select name="products[<?= $product_id ?>][carton]" id="carton_<?= $product_id ?>" class="form-control selectpicker-tags" data-live-search="true">
                                                <option value="3 lớp" <?= (isset($existing['carton']) && $existing['carton'] == '3 lớp') ? 'selected' : '' ?>>3 lớp</option>
                                                <option value="5 lớp" <?= (isset($existing['carton']) && $existing['carton'] == '5 lớp') ? 'selected' : '' ?>>5 lớp</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">B. Thông Tin Báo Giá Sản Phẩm</h4>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">1. Phân loại khách hàng</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['client_classify']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">2. Mã Nhóm Khách Hàng - Brand</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['brand_code']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">3. Tên Nhóm Khách Hàng - Brand</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['brand_name']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">4. Mã Khách Hàng</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['client_code']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">5. Tên Khách Hàng</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['client_name']) ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">6. Mã Thành phẩm</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['product_code']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">7. Tên Thành phẩm</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['product_name']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">8. Đơn Vị</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['unit_name']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">9. Height - Cao (cm)</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['product_height']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">10. Width - Ngang (cm)</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['product_width']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">11. Chừa Biên (cm)</label>
                                            <input type="text" class="form-control" value="<?= html_escape($prod['product_margin']) ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">C. Nguyên Phụ Liệu</h4>
                                        <table class="table table-bordered ptm-table">
                                            <thead>
                                                <tr class="active">
                                                    <th style="width: 50px;">STT</th>
                                                    <th>Mã NPL</th>
                                                    <th>Tên Nguyên Phụ Liệu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($prod['materials'])) { ?>
                                                    <?php foreach ($prod['materials'] as $idx => $mat) { ?>
                                                        <tr>
                                                            <td><?= $idx + 1 ?></td>
                                                            <td><?= html_escape($mat['code']) ?></td>
                                                            <td><?= html_escape($mat['name']) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Không có nguyên phụ liệu trong báo giá</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">D. Công Đoạn Sản Phẩm</h4>
                                        <table class="table table-bordered ptm-table">
                                            <thead>
                                                <tr class="active">
                                                    <th style="width: 50px;">Thứ tự</th>
                                                    <th>Tên Công đoạn Sản Xuất</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($prod['stages'])) { ?>
                                                    <?php foreach ($prod['stages'] as $stg) { ?>
                                                        <tr>
                                                            <td><?= html_escape($stg['number']) ?></td>
                                                            <td><?= html_escape($stg['stage_name']) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">Không có công đoạn sản xuất trong báo giá</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Lưu thông tin</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        init_datepicker();
        
        // Handle tagging support for spec options
        $('.selectpicker-tags').each(function() {
            var $this = $(this);
            $this.selectpicker({
                liveSearch: true,
                tags: true
            }).addClass('form-control');
        });

        // Initialize sortable & selection logic for color process on each product
        $('.quy_trinh_do_mau_select').each(function() {
            var $select = $(this);
            var product_id = $select.data('product-id');
            var $sortable = $('#color_process_sortable_' + product_id);
            var $hidden = $('#quy_trinh_do_mau_' + product_id);
            
            // Populate initially selected items if they exist
            var existing_val = $hidden.val();
            if (existing_val) {
                var items = existing_val.split(', ');
                var selected_vals = [];
                $.each(items, function(idx, item) {
                    var clean_name = item.replace(/^\d+\.\s*/, '');
                    selected_vals.push(clean_name);
                });
                
                $select.val(selected_vals);
                
                $sortable.empty();
                $.each(selected_vals, function(idx, val) {
                    $sortable.append('<li class="list-group-item ui-sortable-handle" data-value="' + val + '" style="padding: 5px 10px; margin-bottom: 2px; cursor: move;"><i class="fa fa-bars mright10"></i> ' + val + '</li>');
                });
            }
            
            $select.selectpicker('refresh');
            
            $sortable.sortable({
                update: function(event, ui) {
                    updateColorProcessInput(product_id);
                }
            });
            
            $select.on('change', function() {
                var selected = $(this).val();
                
                // Get currently rendered items to preserve their order
                var current_items = [];
                $sortable.find('li').each(function() {
                    current_items.push($(this).data('value'));
                });
                
                $sortable.empty();
                if (selected && selected.length > 0) {
                    var final_items = [];
                    $.each(current_items, function(i, val) {
                        if ($.inArray(val, selected) !== -1) {
                            final_items.push(val);
                        }
                    });
                    $.each(selected, function(i, val) {
                        if ($.inArray(val, final_items) === -1) {
                            final_items.push(val);
                        }
                    });
                    
                    $.each(final_items, function(i, val) {
                        $sortable.append('<li class="list-group-item ui-sortable-handle" data-value="' + val + '" style="padding: 5px 10px; margin-bottom: 2px; cursor: move;"><i class="fa fa-bars mright10"></i> ' + val + '</li>');
                    });
                }
                updateColorProcessInput(product_id);
            });
        });

        function updateColorProcessInput(product_id) {
            var items = [];
            $('#color_process_sortable_' + product_id + ' li').each(function(index) {
                items.push((index + 1) + '. ' + $(this).data('value'));
            });
            $('#quy_trinh_do_mau_' + product_id).val(items.join(', '));
        }

        // Ajax submission to save form and reload datatable
        $('#ptm-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();
            
            $.post(url, data).done(function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    alert_float('success', res.message);
                    $('#tnhModal').modal('hide');
                    if ($.fn.DataTable.isDataTable('.table-ptm')) {
                        $('.table-ptm').DataTable().ajax.reload(null, false);
                    }
                    if ($.fn.DataTable.isDataTable('.table-orders')) {
                        $('.table-orders').DataTable().ajax.reload(null, false);
                    }
                } else {
                    alert_float('danger', res.message);
                }
            }).fail(function() {
                alert_float('danger', 'Có lỗi xảy ra khi lưu thông tin.');
            });
        });
    });
</script>
