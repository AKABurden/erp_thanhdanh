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
    
    /* Styling labels */
    .form-group label {
        font-weight: 600 !important;
        color: #374151;
        margin-bottom: 6px;
        font-size: 13px;
    }
    
    /* Static values card styling */
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
            <div class="pull-left">
                <h4 class="modal-title">Chi Tiết Phiếu Yêu Cầu Phát Triển Mẫu (PTM) - <?= html_escape($ptm_no) ?></h4>
            </div>
            <div class="pull-right" style="margin-right: 15px;">
                <?php if (is_admin() || has_permission('ptm', '', 'edit')) { ?>
                    <a data-tnh="modal" class="tnh-modal btn btn-primary btn-xs" href="<?= admin_url('ptm/create_modal/' . $order_id) ?>" data-toggle="modal" data-target="#myModal" style="margin-right: 5px;"><i class="fa fa-pencil-square-o"></i> Chỉnh Sửa</a>
                <?php } ?>
                <?php if (is_admin() || has_permission('ptm', '', 'export')) { ?>
                    <a href="<?= admin_url('ptm/export_excel/' . $id) ?>" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Xuất File Excel</a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-body" style="max-height: 85vh; overflow-y: auto;">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Số Phiếu YCPTM</label>
                        <div class="form-control-static bold text-danger"><?= html_escape($ptm_no) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Ngày Lập Phiếu</label>
                        <div class="form-control-static"><?= _d($date) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Đơn hàng gốc</label>
                        <div class="form-control-static bold text-primary"><?= html_escape($order_ref) ?></div>
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
                                <a href="#view_tab_product_<?= $product_id ?>" aria-controls="view_tab_product_<?= $product_id ?>" role="tab" data-toggle="tab">
                                    <span class="bold text-primary"><?= html_escape($prod['product_code']) ?></span>
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
                            $ptm = $prod['ptm'];
                        ?>
                            <div role="tabpanel" class="tab-pane <?= $active_class ?>" id="view_tab_product_<?= $product_id ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">A. Yêu Cầu Từ Khách Hàng</h4>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">1. Mẫu</label>
                                            <div class="form-control-static"><?= !empty($ptm['mau']) ? html_escape($ptm['mau']) : '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">2. Yêu Cầu Đặc Biệt</label>
                                            <div class="form-control-static"><?= !empty($ptm['yeu_cau_dac_biet']) ? html_escape($ptm['yeu_cau_dac_biet']) : '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">3. Tiêu Chuẩn Test</label>
                                            <div class="form-control-static"><?= !empty($ptm['tieu_chuan_test']) ? html_escape($ptm['tieu_chuan_test']) : '-' ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">4. Cách Xem Mẫu</label>
                                            <div class="form-control-static"><?= !empty($ptm['cach_xem_mau']) ? html_escape($ptm['cach_xem_mau']) : '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">5. Quy Trình Đo Màu</label>
                                            <div class="form-control-static">
                                                <?php 
                                                if (!empty($ptm['quy_trinh_do_mau'])) {
                                                    $processes = explode(', ', $ptm['quy_trinh_do_mau']);
                                                    echo '<ul style="padding-left: 15px; margin-bottom: 0;">';
                                                    foreach ($processes as $p) {
                                                        echo '<li>' . html_escape($p) . '</li>';
                                                    }
                                                    echo '</ul>';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">6. Tiêu Chuẩn Đo Màu</label>
                                            <div class="form-control-static"><?= !empty($ptm['tieu_chuan_do_mau']) ? html_escape($ptm['tieu_chuan_do_mau']) : '-' ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label bold">7. Địa Chỉ Giao Hàng</label>
                                            <div class="form-control-static"><?= !empty($ptm['dia_chi_giao_hang']) ? nl2br(html_escape($ptm['dia_chi_giao_hang'])) : '-' ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">14. Tem đóng gói</label>
                                            <div class="form-control-static"><?= !empty($ptm['tem_dong_goi']) ? html_escape($ptm['tem_dong_goi']) : '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">15. Đóng kiện</label>
                                            <div class="form-control-static"><?= !empty($ptm['dong_kien']) ? html_escape($ptm['dong_kien']) : '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">16. Carton</label>
                                            <div class="form-control-static"><?= !empty($ptm['carton']) ? html_escape($ptm['carton']) : '-' ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h4 class="ptm-section-header">B. Thông Tin Báo Giá Sản Phẩm</h4>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">1. Phân loại khách hàng</label>
                                            <div class="form-control-static"><?= html_escape($prod['client_classify']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">2. Mã Nhóm Khách Hàng - Brand</label>
                                            <div class="form-control-static"><?= html_escape($prod['brand_code']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">3. Tên Nhóm Khách Hàng - Brand</label>
                                            <div class="form-control-static"><?= html_escape($prod['brand_name']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">4. Mã Khách Hàng</label>
                                            <div class="form-control-static"><?= html_escape($prod['client_code']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label bold">5. Tên Khách Hàng</label>
                                            <div class="form-control-static"><?= html_escape($prod['client_name']) ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label bold">6. Mã Thành phẩm</label>
                                            <div class="form-control-static"><?= html_escape($prod['product_code']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label bold">7. Tên Thành phẩm</label>
                                            <div class="form-control-static"><?= html_escape($prod['product_name']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label bold">8. Đơn Vị</label>
                                            <div class="form-control-static"><?= html_escape($prod['unit_name']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label bold">9. Height - Cao (cm)</label>
                                            <div class="form-control-static"><?= html_escape($prod['product_height']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label bold">10. Width - Ngang (cm)</label>
                                            <div class="form-control-static"><?= html_escape($prod['product_width']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label bold">11. Chừa Biên (cm)</label>
                                            <div class="form-control-static"><?= html_escape($prod['product_margin']) ?></div>
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
            
            <div class="row mtop15" style="border-top: 1px solid #ddd; padding-top: 15px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Người tạo</label>
                        <div class="form-control-static"><?= html_escape($creator_name) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Ngày tạo</label>
                        <div class="form-control-static"><?= _d($date_created) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label bold">Ngày cập nhật gần nhất</label>
                        <div class="form-control-static"><?= !empty($date_updated) ? _d($date_updated) : '-' ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>
