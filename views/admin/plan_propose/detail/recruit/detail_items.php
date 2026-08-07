<div class="table-responsive">
    <table id="tb-detail-train" class="dt-tnh dataTable tnh-table table-bordered table-hover" style="width: 1600px;">
        <thead>
            <tr>
                <th class="text-center" style="width: 58px;" rowspan="1" colspan="1">
                    <a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
                </th>
                <th style="width: 250px;" class="text-center"><?= lang('Thiết Bị /Công Đoạn') ?></th>
                <th style="width: 250px;" class="text-center"><?= lang('Trình Độ') ?></th>
                <th style="width: 250px;" class="text-center"><?= lang('Chuyên Môn') ?></th>
                <th style="width: 250px;" class="text-center"><?= lang('Tiêu Chuẩn') ?></th>
                <th style="width: 120px;" class="text-center"><?= lang('Số Lượng') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('Từ Ngày') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('Tới Ngày') ?></th>
                <th style="width: 200px;" class="text-center"><?= lang('Nghiệm Thu - Bàn Giao') ?></th>
                <th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
            </tr>
        </thead>
        <tbody class="tbody">
            <?= $tbodytrain ?>
        </tbody>
    </table>
</div>
<script>
    var dt = '';
    var lang_purchase = <?= json_encode(['tnh_please_chosen_warehouse' => lang('tnh_please_chosen_warehouse')]) ?>;
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var counter = <?= $counter; ?>;
    var count_errors_time = 0;
    var locations = '';
    var edit_train = <?= $edit_train; ?>
</script>
<?= $this->load->view('admin/plan_propose/detail/recruit/train_js.php'); ?>