<div class="table-responsive">
    <table id="tb-detail-time" class="dt-tnh dataTable tnh-table table-bordered table-hover" style="min-width: 1600px;;width: 100%;">
        <thead>
            <tr>
                <th class="text-center" style="width: 58px;">
                    <a class="btn btn-info btn-icon add-row-time"><i class="fa fa-plus"></i></a>
                </th>
                <th style="width: 250px;" class="text-center"><?= lang('Mã TB') ?></th>
                <th style="width: 250px;" class="text-center"><?= lang('Nhân viên/ Đơn vị SC') ?></th>
                <th style="width: 100px;" class="text-center"><?= lang('Giờ bắt đầu') ?></th>
                <th style="width: 100px;" class="text-center"><?= lang('Giờ kết thúc') ?></th>
                <th style="width: 100px;" class="text-center"><?= lang('Tổng giờ') ?></th>
                <th style="width: 100px;" class="text-center"><?= lang('TG kế hoạch') ?></th>
                <th style="width: 100px;" class="text-center"><?= lang('Đánh giá chất lượng') ?></th>
                <th style="width: 120px;" class="text-center"><?= lang('TG Hoàn Thành Vượt Định Mức') ?></th>
                <th style="width: 120px;" class="text-center"><?= lang('TG Hoàn Thành  Kém Định Mức') ?></th>
                <th style="width: 120px;" class="text-center"><?= lang('Bàn Giao Nghiệm Thu') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('TG Bảo Hành') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('KÝ TÊN') ?></th>
                <th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
            </tr>
        </thead>
        <tbody class="tbody">
            <?= $tbodytime ?>
        </tbody>
    </table>
</div>
<script>
    var dt_time = '';
    var lang_purchase = <?= json_encode(['tnh_please_chosen_warehouse' => lang('tnh_please_chosen_warehouse')]) ?>;
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var counter_time = <?= $counter_time; ?>;
    var count_errors_time = 0;
    var locations = '';
    var edit_time = <?= $edit_time; ?>
</script>
<?= $this->load->view('admin/plan_propose/detail/repair/time_js.php'); ?>