<div class="modal-dialog modal-lg" style="width: 60%;">
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
                            <div><?= lang('Loại định mức') ?>: </div>
                            <div class="ml-at t-bold"><div class="label" style="color: <?= $dtData['color'] ?>;border:1px solid <?= $dtData['color'] ?>"><?= ($dtData['name_type_bonus_discipline']) ?></div></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Loại đối tượng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['object_type'] == 'staff' ? 'Cá nhân' : 'Bộ phận - Phòng ban' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đối tượng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['object_name'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Phiếu yêu cầu khen thưởng-kỷ luật') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no_suggest'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phiếu KPI') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['reference_no_kpi']) ? $dtData['reference_no_kpi'] : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Định mức khen thưởng-kỷ luật') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_quota_bonus_discipline'] ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row-contro">
                        <div><?= lang('Lý do') ?>: </div>
                        <?php
                        $note = $dtData['note'];
                        $note = str_replace('{object_type}',$dtData['object_type'] == 'staff' ? 'Cá nhân' : 'Bộ phận - Phòng ban',$note);
                        $note = str_replace('{object_name}', $dtData['object_name'],$note);
                        $note = str_replace('{code_decision}', $dtData['reference_no'],$note);
                        ?>
                        <div class="ml-at t-bold"><?= $note ?></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row-contro">
                        <div><?= lang('File đính kèm') ?>: </div>
                        <div class="ml-at t-bold">
                            <?php
                            $htmlFile1 = '';
                            $htmlFile2 = '';
                            $htmlFile3 = '';
                            $fileResult = $this->db->get_where('tblfiles',
                                ['rel_type' => 'decision_bonus', 'rel_id' => $dtData['id']])->result_array();
                            usort($fileResult, ch_make_cmp(['filetype' => "asc"]));
                            if (!empty($fileResult)) {
                                foreach ($fileResult as $kk => $vv) {
                                    $ktFile = explode('/', $vv['filetype']);
                                    if ($ktFile[0] == 'image'){
                                        $htmlFile1 .= '<div style="margin-bottom: 5px;margin-top: 5px;margin-left: 5px">'.ViewHtmlImagesDt(base_url('uploads/decision_bonus_discipline/'.$vv['rel_id'].'/'.urlencode($vv['file_name']))).'</div>';
                                    } elseif($ktFile[0] == 'video'){
                                        $htmlFile2 .= '<div style="margin-bottom: 5px;margin-top: 5px;margin-left: 5px"><video width="150px" height="100px" autoplay="true" controls><source src="'.base_url('uploads/decision_bonus_discipline/'.$vv['rel_id'].'/'.urlencode($vv['file_name'])).'"></video></div>';
                                    } else {
                                        $htmlFile3 .= '<div style="margin-bottom: 5px;margin-top: 5px"><a target="_blank" href="' . base_url('uploads/decision_bonus_discipline/' . $vv['rel_id'] . '/' . urlencode($vv['file_name'])) . '" >' . $vv['file_name'] . '</a></div>';
                                    }
                                }
                            }

                            echo '<div style=";margin-left: 5px;">'.$htmlFile3.'</div>';
                            echo '<div style="display: flex;margin-left: 5px; flex-wrap: wrap;">'.$htmlFile1.'</div>';
                            echo '<div style="display: flex;margin-left: 5px; flex-wrap: wrap;">'.$htmlFile2.'</div>';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
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
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
</script>