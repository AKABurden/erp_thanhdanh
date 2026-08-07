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
                            <div><?= lang('tnh_date_creted') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người đánh giá') ?>:</div>
                            <div class="ml-at t-bold"><?= !empty($dtData['staff_evaluate']) ? get_staff_full_name($dtData['staff_evaluate']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Loại đánh giá') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['name_type_evaluate'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">

                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?>
                        </label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items"
                                           class="table dt-tnh table-condensed table-cs-border">
                                        <thead>
                                        <tr>
                                            <th class="text-center"
                                                style="width: 40px;"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('Mã đánh giá') ?></th>
                                            <th><?= lang('Tên đánh giá') ?></th>
                                            <th><?= lang('Chi tiết đánh giá') ?></th>
                                            <th><?= lang('Hiện trạng thực tế') ?></th>
                                            <th><?= lang('Kết quả') ?></th>
                                            <th><?= lang('Tiêu chuẩn/ quy định') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($dtDataItems)) { ?>
                                            <?php foreach ($dtDataItems as $key => $value) { ?>
                                                <?php
                                                $child = $value['child'];
                                                ?>
                                                <tr class="bg-danger">
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td>
                                                        <div class="code_item">
                                                            <?= $value['name'] ?>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-left"></td>
                                                    <td class="text-left"></td>
                                                    <td class="text-left"></td>
                                                </tr>
                                                <?php if (!empty($child)){ ?>
                                                    <?php foreach ($child as $k => $v){ ?>
                                                        <?php
                                                        $optionResult = '<option></option>';
                                                        if (!empty($dtResult)){
                                                            foreach ($dtResult as $kk => $vv){
                                                                $optionResult .= '<option '.($vv['id'] == $v['result_id'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"></td>
                                                            <td>
                                                                <div class="code_item">
                                                                    <?= $v['code_evaluate'] ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="name_item"><?= $v['name_evaluate'] ?></div>
                                                            </td>
                                                            <td>
                                                                <div><?= $v['content'] ?></div>
                                                            </td>
                                                            <td class="text-left">
                                                                <div class="actual_situation"><?= $v['actual_situation'] ?></div>
                                                            </td>
                                                            <td class="text-left" style="width: 100px">
                                                                <div>
                                                                    <select class="result" style="width: 100%;" onchange="changeResult(this,<?= $v['id'] ?>)"  data-placeholder="<?= lang('Kết quả') ?>">
                                                                        <?= $optionResult ?>
                                                                    </select>
                                                                </div>
                                                                <?php if ($v['result_id'] == 2){ ?>
                                                                    <div class="text-center mtop5"><a target="_blank" href="<?=  base_url('admin/production_report/detail?object_id=' . $v['id'] . '&object_type=suggest_evaluate_item') ?>" class="btn btn-info">Báo cáo không phù hợp</a></div>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-left">
                                                                <div class="standard"><?= $v['standard'] ?></div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($dtData['id'],
                                            'suggest_evaluate');
                                    ?>
                                    <?php if (!empty($history)) : ?>
                                        <?php foreach ($history as $key => $value) : ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                <div class="activity-text">
                                                    ' . staff_profile_image($value['staff_id'],
                                                    array('staff-profile-image-small'),
                                                    'small') . '' . $value['staff_name'] . '
                                                </div>
                                                <div class="activity-time">
                                                    ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                </div>
                                                <div>
                                                    ' . $value['content'] . '
                                                </div>
                                            </div>';
                                            ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>
                                    : <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>
                                        : <?= get_staff_full_name($dtData['updated_by']) ?></div>
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
               href=" <?= base_url() ?>admin/suggest_evaluate/view/<?= $dtData['id'] ?>" data-toggle="modal"
               data-target="#myModal"></a>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">

    function changeResult(_this,id){
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/suggest_evaluate/changeResult',
            data: {
                id:id,
                result_id: $(_this).val(),
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
        var dtItems = $('#table-items').DataTable({
            "ordering": false,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
            }
        });
    });
</script>