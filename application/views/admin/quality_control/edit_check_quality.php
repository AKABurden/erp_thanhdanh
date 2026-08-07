<?php init_head(); ?>
<style>
.select2-container {
    /*height: 30px;*/
}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/quality_control/edit_check_quality/'.$id, array('id' => 'check_quality')); ?>
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
                                        <?= lang('Số QC', 'reference_no') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="reference_no" class="form-control"
                                                id="reference_no" value="<?= $checkQuality['reference_no'] ?>"
                                                readonly="" aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('date', 'date') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input('date', set_value('date') ? set_value('date') : _d($checkQuality['date']),
                                        'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Chi nhánh xưởng', '') ?></td>
                                    <td>
                                        <?php $branchs = get_table_where('tblbranch',['id !='=> 1],'','result_array');  
                                        $branch_id = 0;
                                        $staff = get_table_where('tblstaff',['staffid'=>get_staff_user_id()],'','row_array');
                                        $branch_id = $staff['id_branch'];
                                        ?>
                                        <select name="id_branch" id="id_branch"
                                            class="id_branch"
                                            required="required" data-placeholder="<?= lang('Chi nhánh xưởng') ?>"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <?php if(!empty($branchs)) {?>
                                            <?php foreach($branchs as $key => $value){?>
                                            <option <?= $checkQuality['id_branch'] == $value['id'] ? 'selected' : '' ?>
                                                value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><?= lang('note', 'note') ?></td>
                                    <td>
                                        <textarea name="note" id="note" class="form-control note"
                                            rows="3"><?= $checkQuality['note'] ?></textarea>
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
                <div class="tabset">
                    <!-- Tab 1 -->
                    <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
                    <label for="tab1"><?= lang('Theo lệnh tổng') ?></label>

                    <input type="radio" name="tabset" id="tab2" aria-controls="marzen">
                    <label for="tab2"><?= lang('tnh_items') ?></label>

                    <div class="tab-panels">
                        <?php 
                            $stages = get_table_where('tbl_stages',['status_qc'=>1],'','result_array');
                        ?>
                        <section id="tab1" class="tab-panel">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="productions" id="productions"
                                        data-placeholder="<?= lang('Lệnh tổng') ?>" class="productions modal-select2"
                                        value="" style="width: 100%;">
                                </div>
                                <div class="col-md-4 hide">
                                    <select class="stage_all modal-select2" style="width: 100%;" name="stage_all"
                                        data-placeholder="<?= lang('Công đoạn cần QC') ?>">
                                        <option></option>
                                        <?php if(!empty($stages)){ ?>
                                        <?php foreach($stages as $key => $value){ ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="refershTable()"
                                        class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                                </div>
                            </div>
                        </section>
                        <section id="tab2" class="tab-panel">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="items" id="items"
                                        data-placeholder="<?= lang('tnh_items') ?>" class="items modal-select2" value=""
                                        style="width: 100%;">
                                </div>
                                <div class="col-md-4">
                                    <select class="stage_all modal-select2" style="width: 100%;" name="stage_all"
                                        data-placeholder="<?= lang('Công đoạn cần QC') ?>">
                                        <option></option>
                                        <?php if(!empty($stages)){ ?>
                                        <?php foreach($stages as $key => $value){ ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning ev-all-chonse hide"
                                        onclick="addItemQc()"><?= lang('chọn') ?></button>
                                    <button type="button" onclick="refershTable()"
                                        class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                                </div>
                            </div>
                        </section>
                        <div class="tb-height mtop10">
                            <div class="table-responsive">
                                <table id="tb-check-quality" class="dt-tnh table table-hover" style="width: 1500px">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;">
                                                #
                                            </th>
                                            <th class="text-center" style="width: 50px"><?= lang('tnh_images') ?>
                                            </th>
                                            <th class="text-center" style="width: 150px;"><?= lang('Mã') ?>
                                            </th>
                                            <th class="text-center" style="width: 130px;"><?= lang('Thông tin') ?>
                                            </th>
                                            <th class="text-center" style="width: 100px;">
                                                <?= lang('Công đoạn cần QC') ?>
                                            </th>
                                            <th class="text-center" style="width: 80px;"><?= lang('SL kiểm tra') ?>
                                            </th>
                                            <th class="text-center" style="width: 100px;"><?= lang('SL lỗi') ?>
                                            </th>
                                            <!-- <th class="text-center" style="width: 100px;"><?= lang('SL phế') ?></th> -->
                                            <th class="text-center" style="width: 80px;"><?= lang('SL đạt') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('Kết quả') ?>
                                            <th class="text-center" style="width: 100px;">
                                                <?= lang('Tỉ lệ % không đạt') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Tỉ lệ % đạt') ?>
                                            </th>
                                            <th class="text-center" style="width: 50px;"><?= lang('actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="edit" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
var dt = '';
var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
var hash = "<?= $this->security->get_csrf_hash() ?>";
var edit = 1;
var counter = "<?= $counter ?>"
var count_errors = 0;
var delivery_id = 0;
var locations = '';
var productions_order_id = '';
var pod_id = "<?= $checkQuality['pod_id'] ?>";
var stage_text = "<?= $stage_text ?>";
</script>

<script type="text/javascript" src="<?= js('check_quality.js?vs=1.7') ?>"></script>