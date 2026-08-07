<style type="text/css">
    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
</style>
<div class="modal fade in payroll_empty" id="payroll_empty" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('Kiểm tra tính công tác phí'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="font-weight: bold;margin-top:10px;font-size: 18px;text-transform: uppercase"
                                 class="text-danger text-center">
                                BẢNG CÔNG TÁC PHÍ THÁNG <?= $month ?> NĂM <?= $year ?> ĐÃ ĐƯỢC
                                TÍNH.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div style="text-align:center;"><a
                                    href="<?= base_url('admin/business_fee_other/edit_business_fee_boiler_calculate?month=' . $month . '&year=' . $year.'&branch='.$branch) ?>"
                                    target="_blank" class="btn btn-info">Đến bảng tính</a>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>