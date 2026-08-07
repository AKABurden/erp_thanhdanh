<div class="col-md-12 mtop10">
    <div class="tabset">
        <!-- Tab 1 -->
        <input checked type="radio" name="tabset" id="tab2" aria-controls="detail_items_view">
        <label for="tab2"><?= lang('Thông tin chi tiết') ?></label>
      
        <div class="tab-panels">
            <section id="detail_items_view" class="tab-panel">
                <div class="table-responsive">
                    <table id="table-items-train" class="table table-hover dont-responsive-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 58px;">STT </th>
                                <th style="width: 250px;" class="text-center"><?= lang('Thiết Bị /Công Đoạn') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Trình Độ') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Chuyên Môn') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Tiêu Chuẩn') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Số Lượng') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Từ Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tới Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Nghiệm Thu - Bàn Giao') ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php foreach ($train as $key => $value) {
                                $infotb_1 = $this->plan_propose_model->infotb($value['items_id']);
                                $costs = $this->plan_propose_model->infocost($value['costs']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= ($key + 1) ?></td>
                                    <td class=""><?= ($infotb_1['name']) ?></td>
                                    <td class="text-center"><?= ($value['level']) ?></td>
                                    <td class="text-center"><?= ($value['specialize']) ?></td>
                                    <td class="text-center"><?= ($value['standard']) ?></td>
                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                    <td class="text-center"><?= _d($value['date_finish']) ?></td>
                                    <td class="text-center"><?= _d($value['date_from']) ?></td>
                                    <td class="text-center"><?= _d($value['date_to']) ?></td>
                                    <td class="text-center"><?= ($value['acceptance']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>