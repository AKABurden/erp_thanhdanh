<div class="table-responsive">
    <table class="display nowrap table" id="data_table" style="width: 100%">
        <thead>
            <th class="text-center"><?= _l('Thông tin') ?></th>
            <?php foreach ($recommended_list as $key => $value) { ?>
                <th class="text-center"><?= $value['name'] ?></th>
            <?php } ?>
        </thead>
        <tbody>
            <?= $tableBody ?>
        </tbody>
    </table>
</div>