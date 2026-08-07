<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <link href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: "Inter", -apple-system, sans-serif; padding: 20px; color: #1e293b; }
        .checklist-container { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; max-width: 1000px; margin: 0 auto; }
        .header { background: #7c2d12; color: #fff; padding: 25px 30px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
        .header-meta { margin-top: 10px; opacity: 0.8; font-size: 13px; display: flex; gap: 20px; }
        
        .content { padding: 30px; }
        .section-title { font-size: 14px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        
        .item-row { display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
        .item-row:last-child { border-bottom: none; }
        .item-text { font-size: 14px; color: #334155; font-weight: 500; flex: 1; }
        
        .status-badge { padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .status-yes { background: #dcfce7; color: #166534; }
        .status-no { background: #fee2e2; color: #991b1b; }
        .status-na { background: #f1f5f9; color: #475569; }

        .footer-action { padding: 20px 30px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: right; }
        .btn-print { background: #fff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-print:hover { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="checklist-container">
        <div class="header">
            <h1><i class="fa fa-clipboard-check"></i> <?= $audit->subject ?></h1>
            <div class="header-meta">
                <span><i class="fa fa-user"></i> Người thực hiện: <b><?= $audit->created_user_name ?></b></span>
                <span><i class="fa fa-calendar"></i> Ngày tạo: <b><?= _d($audit->created_at) ?></b></span>
                <span><i class="fa fa-building"></i> Phòng ban: <b><?= $audit->department_name ?></b></span>
            </div>
        </div>

        <div class="content">
            <div class="section-title">Danh sách tiêu chí kiểm tra</div>
            
            <?php if(isset($details) && !empty($details)): ?>
                <?php foreach($details as $item): ?>
                <div class="item-row">
                    <div class="item-text"><?= $item['item_text'] ?></div>
                    <div class="item-status">
                        <?php if($item['status'] == 'yes'): ?>
                            <span class="status-badge status-yes">Đạt (Yes)</span>
                        <?php elseif($item['status'] == 'no'): ?>
                            <span class="status-badge status-no">Không đạt (No)</span>
                        <?php else: ?>
                            <span class="status-badge status-na">Chưa Check</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted py-4">Không có dữ liệu chi tiết cho phiếu này.</p>
            <?php endif; ?>
        </div>

        <div class="footer-action">
            <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> In kết quả</button>
        </div>
    </div>
</body>
</html>
