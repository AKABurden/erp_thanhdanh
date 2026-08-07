<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_creted') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhân viên') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_id']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mã vị trí') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['code_role']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phòng ban') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['name_department']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_branch'] ?></div>
                        </div>
                        <?php if ($this->type == 1 || $this->type == 3){ ?>
                        <div class="row-contro">
                            <div><?= lang('Ngày bắt đầu thử việc') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['date_start_probationary']) ? _dhau($dtData['date_start_probationary']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Ngày kết thúc thử việc') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['date_end_probationary']) ? _dhau($dtData['date_end_probationary']) : '' ?></div>
                        </div>
                        <?php } ?>
                        <div class="row-contro">
                            <div><?= lang('Quản lý trực tiếp') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_manager']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Trưởng phòng nhân sự') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_manager_hr']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="mtop10">
                    <div class="col-md-12">
                        <table id="tb-evaluation-criteria" class="table dataTable">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 150px"><?= lang('Tiêu chí đánh giá') ?></th>
                                <th class="text-center"><?= lang('Nội dung đánh giá') ?></th>
                                <th class="text-center"><?= lang('Bản thân đánh giá') ?></th>
                                <th class="text-center"><?= lang('TP.Nhân sự') ?></th>
                                <th class="text-center"><?= lang('Quản lý trực tiếp') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dtItems)){ ?>
                                <?php foreach($dtItems as $key => $value){ ?>
                                        <?php
                                        if ($value['type'] == 1){
                                            $name_evaluation_criteria = getListFiveCoreValue($value['evaluation_criteria_id'])['name'];
                                        } elseif ($value['type'] == 2){
                                            $name_evaluation_criteria = $value['name_evaluation_criteria'];
                                        } elseif ($value['type'] == 3){
                                            $name_evaluation_criteria = getListFollow($value['evaluation_criteria_id'])['name'];
                                        }
                                        $optionResult = '<option></option>';
                                        if (!empty($dtResult)){
                                            foreach ($dtResult as $kk => $vv){
                                                $optionResult .= '<option '.($vv['id'] == $value['result'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                            }
                                        }

                                        $optionResultManager = '<option></option>';
                                        if (!empty($dtResult)){
                                            foreach ($dtResult as $kk => $vv){
                                                $optionResultManager .= '<option '.($vv['id'] == $value['result_manager'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                            }
                                        }

                                        $optionResultManagerHr = '<option></option>';
                                        if (!empty($dtResult)){
                                            foreach ($dtResult as $kk => $vv){
                                                $optionResultManagerHr .= '<option '.($vv['id'] == $value['result_manager_hr'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                            }
                                        }

                                        ?>
                                        <tr>
                                            <td><?= $value['name'] ?></td>
                                            <td><?= $name_evaluation_criteria ?></td>
                                            <td>
                                                <div>
                                                    <select class="result" style="width: 100%;" onchange="changeResult(this,<?= $value['id'] ?>,1)"  data-placeholder="<?= lang('Kết quả') ?>">
                                                        <?= $optionResult ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <select class="result_manager" style="width: 100%;" onchange="changeResult(this,<?= $value['id'] ?>,2)"  data-placeholder="<?= lang('Kết quả') ?>">
                                                        <?= $optionResultManager ?>
                                                    </select>
                                                    <?php if ($value['result_manager'] == 2){ ?>
                                                        <div class="text-center mtop5"><a target="_blank" href="<?=  base_url('admin/production_report/detail?object_id=' . $value['id'] . '&object_type=probationary_evaluate_item') ?>" class="btn btn-info">Báo cáo không phù hợp</a></div>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <select class="result_manager_hr" style="width: 100%;" onchange="changeResult(this,<?= $value['id'] ?>,3)"  data-placeholder="<?= lang('Kết quả') ?>">
                                                        <?= $optionResultManagerHr ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($dtData['updated_by']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($dtData['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a data-tnh="modal" class="tnh-modal hide click1"
               href=" <?= base_url() ?>admin/probationary_evaluate/view/<?= $dtData['id'] ?>" data-toggle="modal"
               data-target="#myModal"></a>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    function changeResult(_this,id,type){
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/probationary_evaluate/changeResult',
            data: {
                id:id,
                result_id: $(_this).val(),
                type: type,
                csrf_token_name:hash
            },
            dataType: "json",
            success: function (response) {
                $(".click1")[0].click();
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }
    $(document).ready(function() {
        $(".result").select2()
        $(".result_manager").select2()
        $(".result_manager_hr").select2()
    })
</script>