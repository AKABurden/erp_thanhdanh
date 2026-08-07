<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'KPI Đánh giá'; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(15, 23, 42, .08); padding: 20px; margin-bottom: 20px; }
        .header { display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-success { background: #059669; color: white; }
        .btn-muted { background: #e5e7eb; color: #111827; }
        .grid { display: grid; gap: 12px; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        label { display: block; font-size: 14px; margin-bottom: 6px; }
        input, select, textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .muted { color: #6b7280; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .badge-pass { background: #dcfce7; color: #166534; }
        .badge-fail { background: #fee2e2; color: #991b1b; }
        .badge-ated { background: #dcfce7; color: #166534; }
        .badge-watch { background: #fef3c7; color: #92400e; }
        .badge-low { background: #dbeafe; color: #1d4ed8; }
        .badge-med { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .section-title { margin: 0 0 14px; font-size: 18px; }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="container">
    <div class="card header">
        <div>
            <h1 style="margin:0;"><?php echo isset($title) ? $title : 'KPI Đánh giá'; ?></h1>
            <p class="muted" style="margin:6px 0 0;">Gộp toàn bộ giao diện vào một controller và một view index.php</p>
        </div>
        <div class="actions">
            <a class="btn btn-success" href="<?php echo site_url('kpidanhgia/download_template'); ?>">Tải file mẫu</a>
            <a class="btn btn-primary" href="#form-section">Tạo đánh giá mới</a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-error"><?php echo $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="card" id="form-section">
        <h2 class="section-title">Form đánh giá KPI</h2>
        <form method="post" action="<?php echo site_url('kpidanhgia/store'); ?>">
            <div class="grid grid-3">
                <div>
                    <label>Nhân sự</label>
                    <select name="nhan_su_id" required>
                        <option value="">Chọn nhân sự</option>
                        <?php foreach ($nhan_su_list as $ns): ?>
                            <option value="<?php echo html_escape($ns['id']); ?>"><?php echo html_escape($ns['ho_ten'] . ' - ' . $ns['ma_nhan_vien']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Loại đánh giá</label>
                    <select name="loai_danh_gia" required>
                        <?php foreach ($loai_danh_gia_list as $loai): ?>
                            <option value="<?php echo html_escape($loai); ?>"><?php echo html_escape($loai); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Kỳ đánh giá</label>
                    <input type="text" name="ky_danh_gia" placeholder="VD: 2024-M01" required>
                </div>
            </div>

            <div class="grid grid-3" style="margin-top:12px;">
                <label><input type="checkbox" name="ho_so_day_du" value="1" checked> Hồ sơ đầy đủ</label>
                <label><input type="checkbox" name="training_completed" value="1" checked> Training completed</label>
                <label><input type="checkbox" name="sop_compliance" value="1" checked> SOP compliance</label>
            </div>

            <div class="grid grid-3" style="margin-top:12px;">
                <div>
                    <label>P2 Raw</label>
                    <input type="number" name="p2_raw" min="0" max="100" step="0.1" value="0">
                </div>
                <div>
                    <label>Compliance Raw</label>
                    <input type="number" name="compliance_raw" min="0" max="100" step="0.1" value="0">
                </div>
                <div>
                    <label>P3 Raw</label>
                    <input type="number" name="p3_raw" min="0" max="100" step="0.1" value="0">
                </div>
            </div>

            <div style="margin-top:12px;">
                <label>Ghi chú</label>
                <textarea name="ghi_chu" rows="3"></textarea>
            </div>

            <div class="actions" style="margin-top:16px;">
                <button type="submit" class="btn btn-primary">Tính & Lưu</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 class="section-title">Import dữ liệu</h2>
        <form method="post" action="<?php echo site_url('kpidanhgia/import'); ?>" enctype="multipart/form-data">
            <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
            <div class="actions" style="margin-top:12px;">
                <button type="submit" class="btn btn-success">Import file</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 class="section-title">Danh sách đánh giá</h2>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nhân sự</th>
                        <th>Loại đánh giá</th>
                        <th>Kỳ</th>
                        <th>Gate 1</th>
                        <th>Tổng điểm</th>
                        <th>Xếp loại</th>
                        <th>Quyết định</th>
                        <th>Risk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danh_gia_list)): ?>
                        <?php foreach ($danh_gia_list as $item): ?>
                            <tr>
                                <td><?php echo html_escape($item['ho_ten'] . ' - ' . $item['ma_nhan_vien']); ?></td>
                                <td><?php echo html_escape($item['loai_danh_gia']); ?></td>
                                <td><?php echo html_escape($item['ky_danh_gia']); ?></td>
                                <td><span class="badge <?php echo $item['gate_1_result'] === 'PASS' ? 'badge-pass' : 'badge-fail'; ?>"><?php echo html_escape($item['gate_1_result']); ?></span></td>
                                <td><?php echo number_format((float) $item['tong_diem'], 2); ?></td>
                                <td><span class="badge <?php echo $item['xep_loai'] === 'Cần giám sát' ? 'badge-watch' : 'badge-pass'; ?>"><?php echo html_escape($item['xep_loai']); ?></span></td>
                                <td><?php echo html_escape($item['quyet_dinh']); ?></td>
                                <td><span class="badge <?php echo $item['risk_level'] === 'Low' ? 'badge-low' : ($item['risk_level'] === 'Medium' ? 'badge-med' : 'badge-high'); ?>"><?php echo html_escape($item['risk_level']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="muted">Chưa có dữ liệu đánh giá</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
