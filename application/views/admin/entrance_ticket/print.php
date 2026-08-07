<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$status = (int)($dtData['status'] ?? 0);
$statusLabels = [
    0 => 'Chờ duyệt',
    1 => 'QA duyệt đi',
    2 => 'BV xác nhận ra',
    3 => 'BV xác nhận về',
    4 => 'Hoàn tất',
    -1 => 'Bị từ chối'
];
$status_text = $statusLabels[$status] ?? 'Đang xử lý';

$current_handler = '...';
if ($status == 0) $current_handler = 'QA';
elseif ($status == 1) $current_handler = 'Bảo vệ (Ra)';
elseif ($status == 2) $current_handler = 'Bảo vệ (Về)';
elseif ($status == 3) $current_handler = 'QA (Hoàn tất)';
elseif ($status == 4) $current_handler = 'Đã hoàn tất';

$priority = ($dtData['priority'] ?? '') == 'URGENT' ? 'Khẩn cấp' : 'Bình thường';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'In Phiếu' ?></title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .logo-box {
            width: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .doc-title {
            text-align: center;
            flex: 1;
            margin-top: 5px;
        }

        .doc-title h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .doc-title p {
            margin: 2px 0;
            font-size: 10px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }

        .bg-blue {
            background-color: #D9EAF7;
            font-weight: bold;
            width: 20%;
        }

        .val-cell {
            width: 30%;
        }

        .sec-title {
            background-color: #F3F6F9;
            font-weight: bold;
            text-align: left;
        }

        .chb {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 9px;
            margin-right: 4px;
            vertical-align: middle;
        }

        .checkbox-item {
            margin-bottom: 2px;
        }

        .step-table th {
            background-color: #1F4E79;
            color: #FFF;
            font-weight: bold;
            text-align: center;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .step-table .bg-light-blue {
            background-color: #EAF2F8;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .violation-box {
            border: 1px solid #000;
            padding: 3px 5px;
            margin-bottom: 6px;
            font-size: 10px;
        }

        .sig-table {
            text-align: center;
        }

        .sig-table th {
            background-color: #D9EAF7;
            font-weight: normal;
            font-size: 11px;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .sig-table td {
            height: 50px;
            vertical-align: bottom;
            padding-bottom: 3px;
            font-style: italic;
            font-size: 10px;
        }

        @media print {

            .bg-blue,
            .bg-light-blue,
            .sec-title,
            .sig-table th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .step-table th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">

        <div class="header">
            <div class="logo-box"><img src="<?= base_url('uploads/logo_thanh_danh.png') ?>"></div>
            <div class="doc-title">
                <h1>PHIẾU XUẤT / NHẬP CỔNG</h1>
                <p>Luồng chuẩn: QA duyệt đi → BV xác nhận ra → BV xác nhận về → QA hoàn tất</p>
            </div>
        </div>

        <table>
            <tr>
                <td class="bg-blue">Mã phiếu</td>
                <td class="val-cell"><strong><?= $dtData['reference_no'] ?? '' ?></strong></td>
                <td class="bg-blue">Ngày lập</td>
                <td class="val-cell"><?= !empty($dtData['date']) ? _dt($dtData['date']) : '' ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Trạng thái</td>
                <td class="val-cell"><?= $status_text ?></td>
                <td class="bg-blue">Người đang xử lý</td>
                <td class="val-cell"><?= $current_handler ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Người lập / Bộ phận</td>
                <td class="val-cell"><?= $dtData['fullname'] ?? '' ?> / <?= $dtData['name_departments'] ?? '' ?></td>
                <td class="bg-blue">Priority</td>
                <td class="val-cell"><?= $priority ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Đối tác / KH / NCC</td>
                <td class="val-cell"><?= $dtData['partner_name'] ?? '' ?></td>
                <td class="bg-blue">Người thực hiện / SĐT</td>
                <td class="val-cell"><?= $dtData['executor_name'] ?? '' ?> <?= !empty($dtData['executor_phone']) ? ' - ' . $dtData['executor_phone'] : '' ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Loại hàng / Mã hàng</td>
                <td class="val-cell"><?= $dtData['item_type'] ?? '' ?> / <?= $dtData['item_code_name'] ?? '' ?></td>
                <td class="bg-blue">Số lượng / Kiện / Kg</td>
                <td class="val-cell"><?= $dtData['quantity'] ?? 0 ?> / <?= $dtData['package_count'] ?? 0 ?> / <?= $dtData['kg_weight'] ?? 0 ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Phương tiện / Công ty</td>
                <td class="val-cell"><?= $dtData['vehicle_type'] ?? '' ?></td>
                <td class="bg-blue">Biển số / Tài xế</td>
                <td class="val-cell"><?= $dtData['license_plate'] ?? '' ?> <?= !empty($dtData['driver_name']) ? ' - ' . $dtData['driver_name'] : '' ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Lộ trình</td>
                <td class="val-cell"><?= $dtData['route'] ?? '' ?></td>
                <td class="bg-blue">Chi phí / Bảng giá</td>
                <td class="val-cell"><?= !empty($dtData['route_price']) ? number_format($dtData['route_price']) . ' VNĐ' : '............................' ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Ngày đi (KH)</td>
                <td class="val-cell"><?= !empty($dtData['planned_date_out']) ? _d($dtData['planned_date_out']) : '............................' ?></td>
                <td class="bg-blue">Ngày về (KH)</td>
                <td class="val-cell"><?= !empty($dtData['planned_date_return']) ? _d($dtData['planned_date_return']) : '............................' ?></td>
            </tr>
            <tr>
                <td class="bg-blue">Địa chỉ giao hàng</td>
                <td class="val-cell"><?= $dtData['delivery_address'] ?? '............................' ?></td>
                <td class="bg-blue">Người nhận / SĐT</td>
                <td class="val-cell"><?= $dtData['receiver_info'] ?? '............................' ?></td>
            </tr>
        </table>

        <!-- CHỨNG TỪ + GHI CHÚ ĐIỀU PHỐI -->
        <table>
            <tr>
                <td width="50%" class="sec-title">CHỨNG TỪ</td>
                <td width="50%" class="sec-title">GHI CHÚ ĐIỀU PHỐI</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <div class="checkbox-item"><span class="chb"><?= !empty($dtData['doc_delivery_signed']) ? 'X' : '' ?></span> Phiếu giao hàng có ký KH</div>
                    <div class="checkbox-item"><span class="chb"><?= !empty($dtData['doc_invoice']) ? 'X' : '' ?></span> Hóa đơn (HĐĐ)</div>
                    <div class="checkbox-item"><span class="chb"><?= !empty($dtData['doc_handover']) ? 'X' : '' ?></span> Biên bản giao nhận / gia công</div>
                    <div class="checkbox-item"><span class="chb"></span> Hàng hóa nhận về có chứng từ</div>
                </td>
                <td style="vertical-align: top;">
                    <div style="margin-bottom: 2px;">- Map bảng giá theo lộ trình: .......................................</div>
                    <div style="margin-bottom: 2px;">- Tải trọng / điều kiện PT: ...........................................</div>
                    <div>- Ghi chú giao nhận: <?= !empty($dtData['note_reason']) ? $dtData['note_reason'] : '.........................................' ?></div>
                </td>
            </tr>
        </table>

        <!-- BẢNG CÁC BƯỚC DUYỆT -->
        <table class="step-table">
            <tr>
                <th width="20%">Bước duyệt</th>
                <th width="40%">Checklist</th>
                <th width="20%">Kết quả</th>
                <th width="20%">Ký/Thời gian</th>
            </tr>
            <tr>
                <td class="bg-light-blue">1. QA duyệt đi</td>
                <td>Thông tin & chứng từ hợp lệ<br>Cho phép xuất</td>
                <td style="text-align: center;"><span class="chb"><?= $status >= 1 && isset($dtData['qa_out_valid']) && $dtData['qa_out_valid'] ? 'X' : '' ?></span> YES &nbsp;&nbsp; <span class="chb"></span> NO</td>
                <td>Ký: ............<br>Giờ: ............</td>
            </tr>
            <tr>
                <td class="bg-light-blue">2. BV xác nhận ra</td>
                <td>Đúng người / xe / hàng</td>
                <td style="text-align: center;"><span class="chb"><?= $status >= 2 && isset($dtData['bv_out_match']) && $dtData['bv_out_match'] ? 'X' : '' ?></span> YES &nbsp;&nbsp; <span class="chb"></span> NO</td>
                <td>Giờ ra: ........<br>Ký: ............</td>
            </tr>
            <tr>
                <td class="bg-light-blue">3. BV xác nhận về</td>
                <td>Hàng quay về đúng<br>Đủ chứng từ<br>Đúng số lượng</td>
                <td style="text-align: center;"><span class="chb"><?= $status >= 3 && isset($dtData['bv_return_goods_ok']) && $dtData['bv_return_goods_ok'] ? 'X' : '' ?></span> YES &nbsp;&nbsp; <span class="chb"></span> NO</td>
                <td>Giờ vào: ........<br>Ký: ............</td>
            </tr>
            <tr>
                <td class="bg-light-blue">4. QA hoàn tất</td>
                <td>Xác nhận đủ hàng<br>Đủ chứng từ<br>Đúng số lượng<br>Đóng phiếu</td>
                <td style="text-align: center;"><span class="chb"><?= $status >= 4 && isset($dtData['qa_close_goods_ok']) && $dtData['qa_close_goods_ok'] ? 'X' : '' ?></span> YES &nbsp;&nbsp; <span class="chb"></span> NO</td>
                <td>Ký: ............<br>Giờ: ............</td>
            </tr>
        </table>

        <div class="violation-box">
            <strong>VI PHẠM & GHI CHÚ:</strong> Nếu checklist có NO → bắt buộc lập biên bản vi phạm.<br>
            Mã vi phạm: ........................................ Ghi chú: ..........................................................................................................
        </div>

        <table class="sig-table">
            <tr>
                <th width="20%">Người lập</th>
                <th width="20%">QA (đi)</th>
                <th width="20%">BV ra</th>
                <th width="20%">BV về</th>
                <th width="20%">QA (hoàn tất)</th>
            </tr>
            <tr>
                <td><?= $dtData['fullname'] ?? '' ?><br><br><br>(Ký, ghi rõ họ tên)</td>
                <td><?= !empty($dtData['qa_approve_staff']) ? get_staff_full_name($dtData['qa_approve_staff']) : '' ?><br><br><br>(Ký, ghi rõ họ tên)</td>
                <td><?= !empty($dtData['bv_out_staff']) ? get_staff_full_name($dtData['bv_out_staff']) : '' ?><br><br><br>(Ký, ghi rõ họ tên)</td>
                <td><?= !empty($dtData['bv_in_staff']) ? get_staff_full_name($dtData['bv_in_staff']) : '' ?><br><br><br>(Ký, ghi rõ họ tên)</td>
                <td><?= !empty($dtData['qa_done_staff']) ? get_staff_full_name($dtData['qa_done_staff']) : '' ?><br><br><br>(Ký, ghi rõ họ tên)</td>
            </tr>
        </table>

    </div>
</body>

</html>