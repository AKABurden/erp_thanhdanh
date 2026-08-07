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
                    <span class="book-title"><?php echo _l('Kiểm tra tính bảng lương'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php if ($check == 2) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div style="font-weight: bold;margin-top:10px;font-size: 18px;text-transform: uppercase"
                                     class="text-danger text-center">
                                    BẢNG LƯƠNG THÁNG <?= $month ?> NĂM <?= $year ?> ĐÃ ĐƯỢC TÍNH.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div style="text-align:center;"  class="hide"><a
                                    href="<?= base_url('admin/payroll/editPayroll?month=' . $month . '&year=' . $year . '&branch='.$branch_search.'') ?>"
                                    target="_blank" class="btn btn-info">Đến bảng lương</a>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                            </div>

                        </div>
                    <?php } elseif ($check == 1) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div style="font-weight: bold;margin-top:10px;font-size: 18px;text-transform: uppercase"
                                     class="text-danger text-center">
                                    THÁNG <?= $month ?> NĂM <?= $year ?> CHƯA CÓ CHẤM CÔNG.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                        </div>
                    <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>