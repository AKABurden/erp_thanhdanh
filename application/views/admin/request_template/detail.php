<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(
    'admin/request_template/detail/' . $id . '',
    array('id' => 'request_template')
); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td style="width: 15%;">
                                        <?= lang('dt_reference_suggest', 'reference_no') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('date', 'date') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date', set_value('date') ? set_value('date') : (!empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i')),
                                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                                    <td colspan="1">
                                        <?php
                                        $branchs = getListBranch();
                                        ?>
                                        <select name="branch_id" id="branch_id" class="branch_id" required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if (!empty($branchs)) { ?>
                                                <?php foreach ($branchs as $key => $value) { ?>
                                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><?= lang('Khách hàng', 'client_id') ?></td>
                                    <td>
                                        <input type="text" name="client_id" id="client_id" class="client_id" data-placeholder="<?= lang('Khách Hàng') ?>" style="width: 100%;" value="<?= (!empty($dtData['client_id']) ? 'customers__' . $dtData['client_id'] : '') ?>" title="">
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Số báo giá', 'id_quotes') ?></td>
                                    <td>
                                        <input type="text" name="id_quotes" id="id_quotes" class="id_quotes" data-placeholder="<?= lang('Số báo giá') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['id_quotes'] : '' ?>" title="">
                                    </td>
                                    <td><?= lang('Chạy mẫu lại', 'is_rerun_sample') ?></td>
                                    <td>
                                        <div class="checkbox checkbox-danger">
                                            <input type="checkbox" name="is_rerun_sample" id="is_rerun_sample" <?= !empty($dtData['is_rerun_sample']) && $dtData['is_rerun_sample'] == 1 ? 'checked' : '' ?> value="1">
                                            <label for="is_rerun_sample"><?= lang('Có') ?></label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('note', 'note') ?></td>
                                    <td colspan="3">
                                        <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div style="margin-bottom: 20px">
                    <label for="items_search"><?= lang('Mặt hàng') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="<?= lang('Mặt hàng') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 2000px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th><?= lang('Tên Nhóm SP') ?></th>
                                <th><?= lang('Tên Chủng Loại') ?></th>
                                <th><?= lang('ĐV Tính SP') ?></th>
                                <th><?= lang('Height') ?></th>
                                <th><?= lang('Width') ?></th>
                                <th><?= lang('ĐV Đo SP') ?></th>
                                <th><?= lang('Mã Thành Phẩm') ?></th>
                                <th><?= lang('Tên Thành Phẩm') ?></th>
                                <th><?= lang('Brand') ?></th>
                                <th><?= lang('Tiêu Chuẩn Đóng Gói') ?></th>
                                <th><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                <th><?= lang('Thời Gian Tồn Kho') ?></th>
                                <th><?= lang('Định Mức Thời Gian') ?></th>
                                <th><?= lang('Hình Ảnh SP') ?></th>
                                <th><?= lang('Ngày Chạy Mẫu') ?></th>
                                <th><?= lang('Ngày Hoàn Thành Mẫu') ?></th>
                                <th><?= lang('Ngày Gửi Mẫu') ?></th>
                                <th><?= lang('Ngày Duyệt Mẫu') ?></th>
                                <th><?= lang('Chạy Hàng Lấy Mẫu') ?></th>
                                <th><?= lang('Ngày Hoàn Thành Mẫu SX') ?></th>
                                <th><?= lang('Tác Vụ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <?php
                                    $item_id = $value['items_id'];
                                    $type_item = $value['type_item'];
                                    $info = null;
                                    $images = base_url('assets/images/tnh/no_image.png');
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProductALL($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/products/' . $info['images']);
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="quote_items_id[<?= $counter ?>]" class="quote_items_id" value="<?= $value['quote_items_id'] ?>">
                                                <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id" value="<?= $value['items_id'] ?>">
                                                <input type="hidden" name="request_template_id[<?= $counter ?>]" class="request_template_id" value="<?= $value['id'] ?>">
                                                <div class="td_mode"><?= $info['category_name'] ?></div>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_unit"><?= $info['species_name'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td_unit"><?= $unit['unit'] ?></div>
                                        </td>
                                        <td class="text-center"><?= ($info['height']) ?></td>
                                        <td class="text-center"><?= ($info['wide']) ?></td>
                                        <td class="text-left"><?= ($info['unit_measure']) ?></td>
                                        <td>
                                            <div class="code_item">
                                                <?= $info['code'] ?>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div><?= $info['name'] ?></div>
                                        </td>
                                        <td class="text-center"><?= ($info['brand_name']) ?></td>
                                        <td class="text-center"><?= ($info['packing']) ?></td>
                                        <td class="text-center"><?= formatNumber($info['quantity_max']) ?></td>
                                        <td class="text-center"><?= formatNumber($info['time_inventory']) ?></td>
                                        <td class="text-center"><?= formatNumber($info['quota_time_change_one']) ?></td>
                                        <td>
                                            <div class="td-image">
                                                <div class="preview_image" style="width: auto;">
                                                    <div class="display-block contract-attachment-wrapper img">
                                                        <div style="width:45px; margin: auto;"><a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <div class=""><img src="<?= $images ?>" style="border-radius: 50%"></div>
                                                            </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="date_run_sample[<?= $counter ?>]" placeholder="<?= lang('Ngày Chạy Mẫu') ?>" class="form-control datepicker" value="<?= !empty($value['date_run_sample']) ? _d($value['date_run_sample']) : '' ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="date_finished[<?= $counter ?>]" placeholder="<?= lang('Ngày Hoàn Thành Mẫu') ?>" class="form-control datepicker" value="<?= !empty($value['date_finished']) ? _d($value['date_finished']) : '' ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="date_request_sample[<?= $counter ?>]" placeholder="<?= lang('Ngày Gửi Mẫu') ?>" class="form-control datepicker" value="<?= !empty($value['date_request_sample']) ? _d($value['date_request_sample']) : '' ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="date_approved_sample[<?= $counter ?>]" placeholder="<?= lang('Ngày Duyệt Mẫu') ?>" class="form-control datepicker" value="<?= !empty($value['date_approved_sample']) ? _d($value['date_approved_sample']) : '' ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="date_runs_sample[<?= $counter ?>]" placeholder="<?= lang('Chạy Hàng Lấy Mẫu') ?>" class="form-control datepicker" value="<?= !empty($value['date_runs_sample']) ? _d($value['date_runs_sample']) : '' ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="date_finished_manufactures[<?= $counter ?>]" placeholder="<?= lang('Ngày Hoàn Thành Mẫu SX') ?>" class="form-control datepicker" value="<?= !empty($value['date_finished_manufactures']) ? _d($value['date_finished_manufactures']) : '' ?>">
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                                <?php $counter++;
                                } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <input type="hidden" name="add" id="" class="form-control" value="1">
            <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var dt = '';
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var edit = <?= !empty($dtData) ? 1 : 0 ?>;
    var counter = <?= $counter ?>;
    var count_errors = 0;
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    var dtCategoryStage = <?= !empty($dtCategoryStage) ? json_encode($dtCategoryStage) : '{}' ?>;
    var dtStaff = <?= !empty($dtStaff) ? json_encode($dtStaff) : '{}' ?>;
    var arrId = <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
    var taxs = <?= !empty($taxs) ? json_encode($taxs) : '{}' ?>;
    $('.tax_id').select2();
</script>
<?php $this->load->view('admin/request_template/script_js.php') ?>