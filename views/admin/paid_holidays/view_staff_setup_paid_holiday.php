<style>
    #tb-payment-methods-new-vs1 > thead > tr > th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }
    .table-responsive {
        max-height: 400px;
    }
</style>
<div class="modal fade" id="view_staff_setup_paid_holiday" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?= $title ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bold">Năm : <span><?= $paidholiday['year'] ?></span></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="margin-top: 10px">
                        <div class="form-group">
                            <input type="text" class="form-control staff_search_new" id="staff_search_new" placeholder="nhập nhân viên cần tìm kiếm">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table " style="margin-top: 10px" id="tb-payment-methods-new-vs1">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 30px;">
                                        <?= lang('STT') ?>
                                    </th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Nhân viên') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Phép năm') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($paidholidayDetai)) { ?>
                                    <?php foreach ($paidholidayDetai as $key => $value) { ?>
                                        <?php
                                        $this->db->select('
                                            tblstaff.staffid as staffid,
                                            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name,
                                            tblroles.name as name_roles
                                        ');
                                        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role',
                                            'left');
                                        $this->db->from('tblstaff');
                                        $this->db->where('tblstaff.staffid', $value['staff_id']);
                                        $staff = $this->db->get()->row_array();
                                        $name_roles = '';
                                        if (!empty($staff['name_roles'])) {
                                            $name_roles = '(' . $staff['name_roles'] . ')';
                                        }
                                        ?>
                                        <tr>
                                            <td class="stt text-center"><?= (++$key) ?></td>
                                            <td style="width: 300px">
                                                <?= $staff['staff_name'] . $name_roles ?>
                                            </td>
                                            <td class="text-center" style="width: 100px">
                                                <?= formatNumber($value['number_day']) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        searchTableCustom('#tb-payment-methods-new-vs1', '#staff_search_new', '.tpagination');
    });
</script>
