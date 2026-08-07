<div class="modal-dialog modal-lg" style="width: 70%;">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        /* Custom Header Modal */
        .modal-header {
            background: linear-gradient(to right, #2196F3, #00BCD4);
            color: white;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .modal-header .close { color: white; opacity: 1; }

        /* Section Styling */
        .section-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #2196F3;
            margin-bottom: 15px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 13px;
        }

        .info-label { color: #777; font-weight: 500; font-size: 12px; }
        .info-value { color: #333; font-weight: 600; font-size: 13px; margin-bottom: 10px; }

        .row-flex { display: flex; flex-wrap: wrap; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }

        /* Badge Status */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .bg-success-light { background-color: #dff0d8; color: #3c763d; }

        /* Star Rating */
        .text-warning { color: #f39c12 !important; }

        .panel-custom { border: 1px solid #ddd; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .modal-dialog { width: 95%; margin: 10px auto; }
        }
    </style>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?? '' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <!-- PHẦN 1: THÔNG TIN YÊU CẦU (Từ Ảnh 1) -->
                <div class="col-md-12">
                    <div class="section-title">1. Thông tin phiếu yêu cầu</div>
                    <div class="panel panel-default panel-custom">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-label">Số phiếu:</div>
                                    <div class="info-value text-primary"><?=$dtData['reference_no'] ?? ''?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Ngày yêu cầu:</div>
                                    <div class="info-value"><?=_dt($dtData['date'])?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Người đề xuất:</div>
                                    <div class="info-value"><?=!empty($dtData['employees']) ? get_staff_full_name($dtData['employees']) : ''?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Mức độ ưu tiên:</div>
                                    <div class="info-value"><span class="label label-info"><?=$dtData['name_priority']?></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Thiết bị:</div>
                                    <div class="info-value"><?= $dtData['code_machines'] ?> (<?= $dtData['name_machines'].'['.$dtData['name_cost'].']' ?>)</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Loại yêu cầu:</div>
                                    <div class="info-value"><?=$dtData['name_type_repair'] ?? ''?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Chi tiết sự cố:</div>
                                    <div class="info-value"><?=$dtData['detailed'] ?? ''?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- PHẦN 2: KIỂM TRA ĐÁNH GIÁ (Từ Ảnh 2) -->
                <div class="col-md-12">
                    <div class="section-title">2. Kiểm tra & Đánh giá</div>
                    <div class="panel panel-default panel-custom" style="border-left: 4px solid #f39c12;">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="info-label">Người kiểm tra:</div>
                                    <div class="info-value"><?=!empty($dtData['staff_inspector']) ? get_staff_full_name($dtData['staff_inspector']) : ''?></div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="info-label">Ngày kiểm tra:</div>
                                    <div class="info-value"><?=_dt($dtData['date_inspector'])?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="info-label">Đánh giá sự cố:</div>
                                    <div class="info-value"><?=$dtData['incident'] ?? ''?></div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="info-label">Loại xử lý:</div>
                                    <div class="info-value"><?=$dtData['name_type_processing'] ?? ''?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="info-label">Lý do:</div>
                                    <div class="info-value"><?=$dtData['reason'] ?? ''?></div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="info-label">Chi tiết bảo dưỡng/ sửa chữa:</div>
                                    <div class="info-value"><?=$dtData['detail_repair'] ?? ''?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="info-label">Thời gian ảnh hưởng:</div>
                                    <div class="info-value">
                                        <div class="info-value"><?=_dt($dtData['date_start'])?> - <?=_dt($dtData['date_end'])?></div>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="info-label">Dự toán linh kiện:</div>
                                    <div class="info-value">
                                        <span class="text-danger"><?=number_format_data($dtData['expense'])?> VNĐ</span> (Số lượng: <?=number_format_data($dtData['number_components'])?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

                <!-- PHẦN 3: XỬ LÝ NỘI BỘ (Từ Ảnh 3) -->
                <?php if(!empty($dtData['type_processing']) && $dtData['type_processing'] == 1) {?>
                    <div class="col-md-12 <?=empty($dtData['step'][2]) ? 'hide' : ''?>">
                        <div class="section-title">3. Quá trình xử lý (<?=$dtData['name_type_processing'] ?? ''?>)</div>
                        <div class="panel panel-default panel-custom" style="border-left: 4px solid #5cb85c;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Người thực hiện:</div>
                                        <div class="info-value"><?=!empty($dtData['staff_performing']) ? get_staff_full_name($dtData['staff_performing']) : ''?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Kết quả:</div>
                                        <div class="info-value text-warning"><?=$dtData['name_is_result'] ?? ''?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Bắt đầu thực tế:</div>
                                        <div class="info-value"><?=_dt($dtData['date_performing'])?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Kết thúc dự kiến -  thực tế:</div>
                                        <div class="info-value"><?=_dt($dtData['date_expected'])?> (Thực tế: <?=_dt($dtData['date_success'])?>)</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="info-label">Chi tiết thực hiện:</div>
                                        <div class="info-value"><?=$dtData['code_category_tasks'] ?? ''?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="info-label">Chi phí thực tế:</div>
                                        <div class="info-value">
                                            <span class="text-success"><?=number_format_data($dtData['amount'] ?? 0)?> VNĐ</span> (Số lượng: <?=number_format_data($dtData['quantity'] ?? 0)?>)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="col-md-12">
                        <div class="section-title">3. Quá trình xử lý (<?=$dtData['name_type_processing'] ?? ''?>)</div>
                        <div class="panel panel-default panel-custom" style="border-left: 4px solid #5cb85c;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Nhà Cung Cấp:</div>
                                        <div class="info-value"><?=$dtData['company_supp'] ?? ''?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Nhà Cung Cấp Số Hợp Đồng/PO:</div>
                                        <div class="info-value text-warning"><?=$dtData['code_purchase_order'] ?? ''?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Ngày Ký Hợp Đồng:</div>
                                        <div class="info-value"><?=_dC($dtData['date_contract'])?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Chi tiết thực hiện:</div>
                                        <div class="info-value"><?=$dtData['code_category_tasks'] ?? ''?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Đơn vị Sửa Chữa:</div>
                                        <div class="info-value"><?=$dtData['unit_repair'] ?? ''?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Đơn giá - Số Lượng:</div>
                                        <div class="info-value"><span class="text-danger"><?=number_format_data($dtData['price'] ?? 0)?> VNĐ</span> (Số lượng: <?=number_format_data($dtData['quantity'] ?? 0)?>)</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Ngày Bắt Đầu:</div>
                                        <div class="info-value"><?=_dt($dtData['date_performing'])?></div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="info-label">Thành tiền:</div>
                                        <div class="info-value">
                                            <span class="text-success"><?=number_format_data($dtData['amount'] ?? 0)?> VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="info-label">Hoàn Thành dự kiến:</div>
                                        <div class="info-value"><?=_dt($dtData['date_expected'])?></div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="info-label">Hoàn Thành thực tế:</div>
                                        <div class="info-value"><?=_dt($dtData['date_success'])?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- PHẦN 4: NGHIỆM THU (Từ Ảnh 4) -->
                <div class="col-md-12">
                    <div class="section-title">4. Nghiệm thu & Thanh toán</div>
                    <div class="panel panel-default panel-custom" style="border-left: 4px solid #9c27b0;">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-label">Người nghiệm thu:</div>
                                    <div class="info-value"><?=!empty($dtData['staff_acceptance']) ? get_staff_full_name($dtData['staff_acceptance']) : ''?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Ngày nghiệm thu:</div>
                                    <div class="info-value"><?=_dt($dtData['date_acceptance'])?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Kết quả:</div>
                                    <div class="info-value text-success">
                                        <?=$dtData['name_result_acceptance'] ?? ''?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Đánh giá chất lượng:</div>
                                    <div class="info-value">
                                        <i class="glyphicon glyphicon-star<?=$dtData['star'] >= 1 ? '' : '-empty'?> text-warning"></i>
                                        <i class="glyphicon glyphicon-star<?=$dtData['star'] >= 2 ? '' : '-empty'?> text-warning"></i>
                                        <i class="glyphicon glyphicon-star<?=$dtData['star'] >= 3 ? '' : '-empty'?> text-warning"></i>
                                        <i class="glyphicon glyphicon-star<?=$dtData['star'] >= 4 ? '' : '-empty'?> text-warning"></i>
                                        <i class="glyphicon glyphicon-star<?=$dtData['star'] >= 5 ? '' : '-empty'?> text-warning"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Đánh giá đơn vị:</div>
                                    <div class="info-value italic">"<?=$dtData['star_unit_repair'] ?? 'Chưa có đánh giá'?>"</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Mã ngân sách:</div>
                                    <div class="info-value small text-muted"><?=$dtData['code_costs'] ?? ''?></div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <div style="margin: 0;">
                                        <label class="<?=!empty($dtData['payment']) ? 'text-success' : 'text-danger'?>"><strong><?=!empty($dtData['payment']) ? 'Đã hoàn thành thanh toán' : 'Chưa hoàn thành thanh toán'?></strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
</script>