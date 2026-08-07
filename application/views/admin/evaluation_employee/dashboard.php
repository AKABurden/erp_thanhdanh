<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .card {
        background: #fff;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }

    .kpi-title {
        color: #777;
        font-size: 13px;
    }

    .kpi-value {
        font-size: 26px;
        font-weight: bold;
    }

    .kpi-note {
        font-size: 12px;
        margin-top: 5px;
    }

    .red { color: #e74c3c; }
    .green { color: #27ae60; }
    .blue { color: #3498db; }
    .yellow { color: #f1c40f; }

    /* Chart */
    .bar-row {
        margin-bottom: 12px;
    }

    .bar-label {
        font-size: 13px;
        margin-bottom: 3px;
    }

    .bar-bg {
        background: #eaeaea;
        height: 16px;
        border-radius: 4px;
    }

    .bar-fill {
        height: 16px;
        border-radius: 4px;
    }

    .bar-green { background: #2ecc71; }
    .bar-yellow { background: #f1c40f; }
    .bar-red { background: #e74c3c; }

    /* Alert list */
    .alert-box {
        border-left: 5px solid;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .alert-green {
        background: #ecf9f1;
        border-color: #2ecc71;
    }

    .alert-yellow {
        background: #fff8e1;
        border-color: #f1c40f;
    }

    .alert-title {
        font-weight: bold;
    }

    .score {
        font-size: 18px;
        font-weight: bold;
        float: right;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= base_url('admin/personnel_assessment/importExcel') ?>" class="tnh-modal hide pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddPersonnelAssessment): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/personnel_assessment/detail') ?>" class="btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
        <div class="col-md-12">
            <div class="col-sm-3">
                <div class="card">
                    <div class="kpi-title">Trung bình toàn công ty</div>
                    <div class="kpi-value">3.2/5.0</div>
                    <div class="kpi-note yellow">Cần cải thiện năng lực quản trị</div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card">
                    <div class="kpi-title">Đơn vị yếu kém nhất</div>
                    <div class="kpi-value red">P. Bảo Trì</div>
                    <div class="kpi-note red">Rủi ro hệ thống cao</div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card">
                    <div class="kpi-title">Đơn vị xuất sắc nhất</div>
                    <div class="kpi-value green">P. KSNB</div>
                    <div class="kpi-note green">Giữ vững vai trò kiểm soát</div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card">
                    <div class="kpi-title">Tỷ lệ hoàn thành đánh giá</div>
                    <div class="kpi-value blue">85%</div>
                    <div class="kpi-note blue">Đang tiến hành đúng tiến độ</div>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="col-md-12">

            <!-- Chart -->
            <div class="col-sm-7">
                <div class="card">
                    <h4><b>Xếp Hạng Năng Lực Các Phòng Ban</b></h4>

                    <div class="bar-row">
                        <div class="bar-label">P.HCNS</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-green" style="width:90%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">P.KinhDoanh</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-yellow" style="width:76%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">P.KSNB</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-green" style="width:90%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">P.KT</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-yellow" style="width:50%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">P.Sản Xuất</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-green" style="width:82%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">P.Bảo Trì</div>
                        <div class="bar-bg">
                            <div class="bar-fill bar-red" style="width:30%"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Alerts -->
            <div class="col-sm-5">
                <div class="card">
                    <h4><b>⚠️ Cảnh Báo & Hành Động (Tự động)</b></h4>

                    <div class="alert-box alert-green">
                        <span class="alert-title">P.HCNS</span>
                        <span class="score">4.5/5.0</span>
                        <div>Tốt. Duy trì và phát huy.</div>
                    </div>

                    <div class="alert-box alert-yellow">
                        <span class="alert-title">P.KinhDoanh</span>
                        <span class="score">3.8/5.0</span>
                        <div>Lưu ý: Cần đào tạo bổ sung kỹ năng thiếu hụt.</div>
                    </div>

                    <div class="alert-box alert-green">
                        <span class="alert-title">P.KSNB</span>
                        <span class="score">4.5/5.0</span>
                        <div>Tốt. Duy trì và phát huy.</div>
                    </div>

                    <div class="alert-box alert-yellow">
                        <span class="alert-title">P.KT</span>
                        <span class="score">2.5/5.0</span>
                        <div>Cần cải thiện năng lực nghiệp vụ.</div>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

</script>