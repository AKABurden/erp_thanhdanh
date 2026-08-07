<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="title-modal">
                            <h3>Thông tin</h3>
                        </div>
                        <div class="body-modal">
                            <div class="row-modal">
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div>Mã phiếu kế hoạch:</div>
                                        <div class="ml-at t-bold"><?= $plan_propose->code ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Ngày chứng từ:</div>
                                        <div class="ml-at t-bold"><?= _dC($plan_propose->date) ?></div>
                                    </div>

                                    <div class="row-contro">
                                        <div>Người tạo kế hoạch:</div>
                                        <div class="ml-at t-bold">
                                            <?= staff_profile_image($plan_propose->staff, array('staff-profile-image-small mright5'), 'small', array(
                                                'data-toggle' => 'tooltip',
                                                'data-title' => get_staff_full_name($plan_propose->staff)
                                            )) . get_staff_full_name($plan_propose->staff) ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Tổng ngân sách:</div>
                                        <div class="ml-at t-bold"><?= number_format_data($plan_propose->money) ?></div>
                                    </div>
                                </div>
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div>Mã công việc:</div>
                                        <div class="ml-at t-bold"><?= ($plan_propose->code_category) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Nội dung công việc:</div>
                                        <div class="ml-at t-bold"><?= ($plan_propose->content_category) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người duyệt:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($plan_propose->assigned)) {
                                                foreach ($plan_propose->assigned as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    ));
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Chi nhánh:</div>
                                        <div class="ml-at t-bold"><?= (!empty($plan_propose->id_branch) ? get_table_where('tblbranch', ['id' => $plan_propose->id_branch], '', 'row')->name : '') ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Loại kế hoạch:</div>
                                        <div class="ml-at t-bold"><?= $type_plan_propose ?></div>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div>Nội dung kế hoạch:</div>
                                    <div class="ml-at t-bold"><?= ($plan_propose->content) ?></div>
                                </div>
                                <div class="clearfix"></div>
                                <?php if (!empty($files)) { ?>
                                    <h4 class="mtop30">Tập tin đính kèm</h4>
                                    <div class="clearfix"></div>
                                    <div class="fild-content mtop10">
                                        <?php foreach ($files as $keyFile => $valFile) { ?>
                                            <?php if (explode('/', $valFile->filetype)[0] == 'image') { ?>
                                                <div class="mtop5 mbot5 rowData">
                                                    <div class="preview_image" style="width: auto;">
                                                        <div class="display-block contract-attachment-wrapper img">
                                                            <a class="pull-right text-danger" onclick="removeFile(<?= $valFile->id ?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                            <div style="width:150px;">
                                                                <a href="<?= base_url('uploads/plan_propose/' . $plan_propose->id . '/' . $valFile->file_name) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                    <div class="">
                                                                        <img src="<?= base_url('uploads/plan_propose/' . $plan_propose->id . '/' . $valFile->file_name) ?>" style="max-height: 100px">
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php } else { ?>
                                                <div class="mtop5 mbot5 rowData">
                                                    <a target="_blank" href="<?= base_url('uploads/plan_propose/' . $plan_propose->id . '/' . $valFile->file_name) ?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a>
                                                    <a class="pull-right text-danger" onclick="removeFile(<?= $valFile->id ?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php }
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </div>
                            <?php
                            if ($plan_propose->type_plan_propose == 'train') {
                                echo $this->load->view('admin/plan_propose/detail/train/view.php');
                            }
                            if ($plan_propose->type_plan_propose == 'repair' || $plan_propose->type_plan_propose == 'quality' || $plan_propose->type_plan_propose == 'calibration' || $plan_propose->type_plan_propose == 'replace' || $plan_propose->type_plan_propose == 'check') {
                                echo $this->load->view('admin/plan_propose/detail/repair/view.php');
                            }
                            if ($plan_propose->type_plan_propose == 'npl' || $plan_propose->type_plan_propose == 'tools' || $plan_propose->type_plan_propose == 'sanxuat') {
                                echo $this->load->view('admin/plan_propose/detail/items/view.php');
                            }
                            if ($plan_propose->type_plan_propose == 'vouchers_coupon' || $plan_propose->type_plan_propose == 'pay_slip') {
                                echo $this->load->view('admin/plan_propose/detail/payment/view.php');
                            }
                            if ($plan_propose->type_plan_propose == 'recruit') {
                                echo $this->load->view('admin/plan_propose/detail/recruit/view.php');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#view_modal').modal('show');

    function removeFile(id, _this) {
        if (confirm('Bạn có chắc muốn xóa file?')) {
            $.get(admin_url + 'plan_propose/removeFile/' + id, function(result) {
                result = JSON.parse(result);
                if (result.success) {
                    $(_this).parents('.rowData').remove();
                }
            })
        }
    }
    $(document).ready(function() {

        $('#table-items-time').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            "responsive": true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });
        $('#table-items-train').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            "responsive": true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });
    });
</script>