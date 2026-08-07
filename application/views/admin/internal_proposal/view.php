<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
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
                                        <div>Ngày đề xuất:</div>
                                        <div class="ml-at t-bold"><?= _dt_new($internal_proposal->date) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Mã ĐX:</div>
                                        <div class="ml-at t-bold"><?= $internal_proposal->code ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Loại kế hoạch:</div>
                                        <div class="ml-at t-bold"><?= $internal_proposal->type_plan_propose ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Loại phiếu yêu cầu:</div>
                                        <div class="ml-at t-bold"><?= $internal_proposal->name_category_recommended ?></div>
                                    </div>
                                    <?php
                                    echo form_open(admin_url('internal_proposal/update_recommended_list/' . $internal_proposal->id), array('id' => 'update_recommended_list-form'));
                                    ?>
                                    <div class="row-contro">
                                        <div>Loại ĐX:</div>
                                        <div class="ml-at t-bold">

                                            <div class="form-group" app-field-wrapper="id_board_search">
                                                <div class="dropdown bootstrap-select bs3" style="">
                                                    <?php
                                                    $dtRecommendedListG = $this->recommended_list_model->getRecommendedListParent([0], 1);
                                                    ?>
                                                    <select name="recommended_list_group_id_new" id="recommended_list_group_id_new" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Nhóm đề xuất') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($dtRecommendedListG)) : ?>
                                                            <?php foreach ($dtRecommendedListG as $key => $value) : ?>
                                                                <option data-bonus="<?= $value['type_bonus'] ?>" <?= ((!empty($internal_proposal->recommended_list_group_id) && $internal_proposal->recommended_list_group_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Nhóm đề xuất:</div>
                                        <div class="ml-at t-bold">

                                            <div class="form-group" app-field-wrapper="id_board_search">
                                                <div class="dropdown bootstrap-select bs3" style="">
                                                    <?php
                                                    $dtRecommendedList = null;
                                                    if (!empty($internal_proposal->recommended_list_group_id)) {
                                                        $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$internal_proposal->recommended_list_group_id]);
                                                    }
                                                    ?>
                                                    <select name="recommended_list_id_new" id="recommended_list_id_new" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết đề xuất') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($dtRecommendedList)) : ?>
                                                            <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                                <option <?= ((!empty($internal_proposal->recommended_list_id) && $internal_proposal->recommended_list_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Chi tiết đề xuất:</div>
                                        <div class="ml-at t-bold">

                                            <div class="form-group" app-field-wrapper="id_board_search">
                                                <div class="dropdown bootstrap-select bs3" style="">
                                                    <?php
                                                    $internal_proposal->recommended_list = get_table_where('tblinternal_proposal_recommended', ['id_internal_proposal' => $internal_proposal->id]);
                                                    $dtRecommendedList = null;
                                                    if (!empty($internal_proposal->recommended_list_id)) {
                                                        $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$internal_proposal->recommended_list_id]);
                                                    }
                                                    $arrId = [];
                                                    if (!empty($internal_proposal) && !empty($internal_proposal->recommended_list)) {
                                                        foreach ($internal_proposal->recommended_list as $kk => $vv) {
                                                            $arrId[] = $vv['recommended_list_detail_id'];
                                                        }
                                                    }
                                                    ?>
                                                    <select name="recommended_list_detail_id_new[]" id="recommended_list_detail_id_new" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết đề xuất') ?>" class="selectpicker" multiple>
                                                        <option value=""></option>
                                                        <?php if (!empty($dtRecommendedList)) : ?>
                                                            <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                                <option <?= ((!empty($arrId) && in_array($value['id'], $arrId)) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button style="float: right;" type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                                    </form>
                                </div>
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div>Chi nhánh:</div>
                                        <div class="ml-at t-bold"><?= (!empty($internal_proposal->id_branch) ? get_table_where('tblbranch', ['id' => $internal_proposal->id_branch], '', 'row')->name : '') ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Bộ phận:</div>
                                        <div class="ml-at t-bold"><?= $internal_proposal->department_name ?></div>
                                    </div>
                                    <!-- $process[1] = 'Người lập đề xuất';
		$process[2] = 'Người duyệt đề xuất';
		$process[3] = 'Người duyệt thực thi';
		$process[4] = 'Người hoàn thành 1';
		$process[5] = 'Người hoàn thành 2';
		$process[6] = 'Người hoàn thành 3';
		$process[7] = 'Người hoàn thành 4';
		$process[8] = 'Người kiểm soát hoàn thành';
		$process[9] = 'Người kiểm toán hoàn thành'; -->
                                    <div class="row-contro">
                                        <div>Người lập đề xuất:</div>
                                        <div class="ml-at t-bold">
                                            <?= staff_profile_image($internal_proposal->staff, array('staff-profile-image-small mright5'), 'small', array(
                                                'data-toggle' => 'tooltip',
                                                'data-title' => get_staff_full_name($internal_proposal->staff)
                                            )) . get_staff_full_name($internal_proposal->staff) ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người duyệt đề xuất:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->assigned)) {
                                                foreach ($internal_proposal->assigned as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    )) . get_staff_full_name($value['id_staff']);;
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người duyệt thực thi:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->staff_pod)) {
                                                foreach ($internal_proposal->staff_pod as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    )) . get_staff_full_name($value['id_staff']);
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người hoàn thành 1:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->manager_id)) {
                                                echo staff_profile_image($internal_proposal->manager_id, array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($internal_proposal->manager_id)
                                                )) . get_staff_full_name($internal_proposal->manager_id);;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người hoàn thành 2:</div>
                                        <div class="ml-at t-bold">
                                        <?php
                                            if (!empty($internal_proposal->head_of_department)) {
                                                foreach ($internal_proposal->head_of_department as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    )) . get_staff_full_name($value['id_staff']);
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người hoàn thành 3:</div>
                                        <div class="ml-at t-bold">
                                            <?php if (!empty($internal_proposal->staff_controller_completes)) { ?>
                                                <?= staff_profile_image($internal_proposal->staff_controller_completes, array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($internal_proposal->staff_controller_completes)
                                                )) . get_staff_full_name($internal_proposal->staff_controller_completes) ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người hoàn thành 4:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->staff_auditor_completes)) {
                                                echo staff_profile_image($internal_proposal->staff_auditor_completes, array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($internal_proposal->staff_auditor_completes)
                                                )) . get_staff_full_name($internal_proposal->staff_auditor_completes);;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người kiểm soát hoàn thành:</div>
                                        <div class="ml-at t-bold">
                                            <?php if (!empty($internal_proposal->auditor_id)) { ?>
                                                <?= staff_profile_image($internal_proposal->auditor_id, array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($internal_proposal->auditor_id)
                                                )) . get_staff_full_name($internal_proposal->auditor_id) ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người kiểm toán hoàn thành:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->monitor)) {
                                                foreach ($internal_proposal->monitor as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    )) . get_staff_full_name($value['id_staff']);;
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Phiếu yêu cầu mua hàng:</div>
                                        <?php if (!empty($internal_proposal->code_purchase)) { ?>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_purchase) ? $internal_proposal->code_purchase : '') ?></div>
                                        <?php } ?>
                                        <?php if ($internal_proposal->id_purchases == -1) { ?>
                                            <?php
                                            $text = '';
                                            $check_purchase = get_table_where('tblinternal_proposal_purchase', array('id_internal_proposal' => $internal_proposal->id));
                                            foreach ($check_purchase as $kk => $vv) {
                                                $check_purchase_detail = get_table_where('tblpurchases', array('id' => $vv['id_purchases']), '', 'row');
                                                $text .= $check_purchase_detail->prefix . $check_purchase_detail->code . ', ';
                                            }
                                            ?>
                                            <div class="ml-at t-bold"><?= trim($text, ', ') ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="row-contro">
                                        <div>Phiếu dịch vụ:</div>
                                        <?php if (!empty($internal_proposal->code_services)) { ?>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_services) ? $internal_proposal->code_services : '') ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="row-contro">
                                        <div>Phiếu yêu cầu:</div>
                                        <?php if (!empty($code_suggest)) { ?>
                                            <div class="ml-at t-bold"><?= $code_suggest['reference_no'] ?> <?= !empty($code_suggest['staff_suggest_name']) ? '(' . $code_suggest['staff_suggest_name'] . ')' : '' ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="row-contro">
                                        <div>Số tiền:</div>
                                        <div class="ml-at t-bold"><?= number_format_data($internal_proposal->money) ?></div>
                                    </div>
                                    <div class="row-contro hide">
                                        <div>Mã công việc:</div>
                                        <div class="ml-at t-bold"><?= ($internal_proposal->code_category) ?></div>
                                    </div>
                                    <div class="row-contro hide">
                                        <div>Nội dung công việc:</div>
                                        <div class="ml-at t-bold"><?= ($internal_proposal->content_category) ?></div>
                                    </div>
                                    <?php if (!empty($internal_proposal->code_purchase_order)) { ?>
                                        <div class="row-contro">
                                            <div>Phiếu mua hàng (PO):</div>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_purchase_order) ? $internal_proposal->code_purchase_order : '') ?></div>
                                        </div>
                                    <?php } ?>

                                </div>
                                <div class="row-contro">
                                    <div>Nội dung đề xuất:</div>
                                    <div class="ml-at t-bold"><?= ($internal_proposal->content) ?></div>
                                </div>

                                <div class="clearfix"></div>

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
                                                                <a href="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                    <div class="">
                                                                        <img src="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>" style="max-height: 100px">
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php } else { ?>
                                                <div class="mtop5 mbot5 rowData">
                                                    <a target="_blank" href="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a>
                                                    <a class="pull-right text-danger" onclick="removeFile(<?= $valFile->id ?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php }
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($internal_proposal->excel_data)) { ?>
                                <div class="mtop15">
                                    <div class="bold mbot10">Chi tiết yêu cầu:</div>
                                    <div class="table-responsive" style="max-height: 400px; overflow: auto; width: 100%; max-width: 0; min-width: 100%;">
                                        <?php 
                                            $rows_data = json_decode($internal_proposal->excel_data, true);
                                            if (is_array($rows_data) && !empty($rows_data)) {
                                                $headers = array_keys($rows_data[0]);
                                                echo '<table class="table table-bordered table-striped" style="margin-bottom: 0; white-space: nowrap; width: 100%;">';
                                                echo '<thead><tr class="success">';
                                                foreach ($headers as $header) {
                                                    echo '<th class="text-center bold" style="min-width: 150px; white-space: nowrap;">' . htmlspecialchars($header) . '</th>';
                                                }
                                                echo '</tr></thead>';
                                                echo '<tbody>';
                                                foreach ($rows_data as $row) {
                                                    echo '<tr>';
                                                    foreach ($headers as $header) {
                                                        echo '<td>' . htmlspecialchars($row[$header] ?? '') . '</td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                                echo '</tbody>';
                                                echo '</table>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php } else if (!empty($internal_proposal->code_purchase) || ($internal_proposal->id_purchases == -1)) { ?>
                                <div class="">
                                    <div>Chi tiết yêu cầu:</div>
                                    <table id="tb-items-internal" class="dt-tnh table item-purchases table-bordered table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="200" class="text-left"></i> <?php echo _l('ch_items_name_t'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng đề xuất'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV chuẩn'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV kho'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV thanh toán'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Tồn được duyệt'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Tồn sẵn'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng được duyệt'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Đơn giá'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thuế'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thành tiền'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhóm nhà cung cấp'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhà cung cấp'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Ghi chú đề xuất'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="table_purchase">
                                            <?php $i = 0;
                                            $tbody = '';
                                            $total = 0;
                                            foreach ($items as $key => $value) {
                                                $purchase_items = get_table_where('tblpurchases_items', array('id' => $value['id_purchases_items']), '', 'row');
                                                $purchase = get_table_where('tblpurchases', array('id' => $value['id_purchases']), '', 'row');
                                                $text = '';
                                                if (!empty($purchase)) {
                                                    $text = '<br><span class="label label-danger pull-left mtop5 text-center">' . $purchase->prefix . $purchase->code . '</span>';
                                                }
                                                $this->db->select('tblsuppliers_groups.name as name_category,tblsuppliers.company as company');
                                                $this->db->from('tblsuppliers');
                                                $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');
                                                $this->db->where('tblsuppliers.id', $value['suppliers_id']);
                                                $supplier = $this->db->get()->row();
                                                $unit_name = $value['unit_name'];
                                                if ($value['unit_name'] == null) {
                                                    $unit_name = '';
                                                }
                                                $unit_name_payment = $value['unit_name_payment'];
                                                if ($value['unit_name_payment'] == null) {
                                                    $unit_name_payment = '';
                                                }
                                                $unit_name_stock = $value['unit_name_stock'];
                                                if ($value['unit_name_stock'] == null) {
                                                    $unit_name_stock = '';
                                                }
                                                $tbody .= '<tr>';
                                                $tbody .= '<td>
                                                ' . $value['name_item'] . ' (' . $value['code_item'] . ')' . $text . '
                                                </td>';
                                                $tbody .= '<td class="text-center sldx">' . formatNumber($purchase_items->quantity) . '</td>';
                                                $tbody .= '<td><span class="text_mainquantity_stock text-center">' . formatNumber($value['quantity']) . '</span><span class="unit_name">/' . $unit_name . '</span></td>';
                                                $tbody .= '<td class="text-center"><span class="text_mainquantity_stock text-center">' . formatNumber($value['quantity_stock']) . '</span><span class="unit_name_stock">/' . $unit_name_stock . '</span></td>';
                                                $tbody .= '<td class="text-center"><span class="text_mainquantity_payment">' . formatNumber($value['quantity_payment']) . '</span><span class="unit_name_payment">/' . $unit_name_payment . '</span></td>';
                                                $this->db->select('COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity');
                                                $this->db->where('tblwarehouse_items.type_items', $purchase_items->type);
                                                $this->db->where('tblwarehouse_items.id_items', $purchase_items->product_id);
                                                $this->db->where_not_in('tblwarehouse_items.warehouse_id', explode(',', WAREHOUSES_SYSTEM));
                                                $ton = $this->db->get('tblwarehouse_items')->row_array();
                                                $tdd = 0;
                                                if ($purchase_items->type == 'nvl') {
                                                    $this->db->select('tbl_materials.quantity_minimum');
                                                    $this->db->where('tbl_materials.id', $purchase_items->product_id);
                                                    $materials = $this->db->get('tbl_materials')->row_array();
                                                    if (!empty($materials)) {
                                                        $tdd = $materials['quantity_minimum'];
                                                    }
                                                }
                                                $tbody .= '<td class="text-center tdd">' . formatNumber($tdd) . '</td>';
                                                $tbody .= '<td class="text-center slts">' . formatNumber($ton['product_quantity']) . '</td>';
                                                $tbody .= '<td class="text-center sldx">' . formatNumber($purchase_items->quantity_net) . '</td>';


                                                $tbody .= '<td class="text-right">' . formatNumber($value['price']) . '</td>';
                                                $tbody .= '<td class="text-center">' . ($value['tax_rate']) . '%</td>';
                                                $tbody .= '<td class="text-right">' . number_format_data(($value['price'] * $value['quantity_payment']) * (1 + $value['tax_rate'] / 100)) . '</td>';
                                                $tbody .= '<td style="width:150px">' . (!empty($supplier->name_category) ? $supplier->name_category : '') . '</td>';
                                                $tbody .= '<td style="width:150px">' . $supplier->company . '</td>';
                                                $tbody .= '<td>' . $purchase_items->note . '</td>';
                                                $tbody .= '</tr>';
                                                $i++;
                                                $total += ($value['price'] * $value['quantity_payment']) * (1 + $value['tax_rate'] / 100);
                                            } ?>
                                            <?php echo $tbody ?>
                                        </tbody>
                                        <tfoot>
                                            <td>Tổng</td>
                                            <td class="tfood_sldx text-center"></td>
                                            <td class="tfood_slc text-center"></td>
                                            <td class="tfood_slk text-center"></td>
                                            <td class="tfood_slp text-center"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="tfood_total text-right"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php } ?>
                            <div style="margin-top: 20px">
                                <?= !empty($html) ? '<div class="bold">Chi tiết đánh giá KPI</div>' : '' ?>
                                <?= $html ?>
                            </div>
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
            $.get(admin_url + 'internal_proposal/removeFile/' + id, function(result) {
                result = JSON.parse(result);
                if (result.success) {
                    $(_this).parents('.rowData').remove();
                }
            })
        }
    }

    $(document).ready(function() {
        init_selectpicker();

        var dtItemsInternal = $('#tb-items-internal').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                $('#tb-items-internal_wrapper').find('.btn-dt-reload').hide();
            },
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
            buttons: get_datatable_buttons($('#tb-items-internal')),
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalAmount = api
                    .column(10, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(10).footer()).html('<div class="text-right">' + tnhFormatMoney(pageTotalAmount) + '</div>');
            }
        });
    });
    $('#recommended_list_group_id_new').change(function() {
        var recommended_list_group_id = $(this).val();
        type_bonus = $('option:selected', this).attr("data-bonus");
        if (type_bonus == 1) {
            $(".html_decision").removeClass('hide');
        } else {
            $(".html_decision").addClass('hide');
        }
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;

        $.post(admin_url + 'production_report/getRecommendedListByParent', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_id_new').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_id_new').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_id_new').selectpicker('refresh');
            $('#recommended_list_id_new').change();

        });
    });
    $('#recommended_list_id_new').change(function() {
        var recommended_list_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_id;

        $.post(admin_url + 'production_report/getRecommendedListByParent', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_detail_id_new').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_detail_id_new').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_detail_id_new').selectpicker('refresh');
        });
    });

    function validate_form() {
        _validate_form($('#update_recommended_list-form'), {}, add_update_recommended_list);
    }
    $(function() {
        validate_form();
    });

    function add_update_recommended_list(form) {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            var data = $(form).serialize(),
                action = form.action;
            return $.post(action, data).done(function(form) {
                form = JSON.parse(form),
                    alert_float(form.alert_type, form.message);
                if (form.result) {
                    oTable.draw('page');
                    $('#view_modal').modal('hide');
                }
            }), !1
        }
    }
</script>