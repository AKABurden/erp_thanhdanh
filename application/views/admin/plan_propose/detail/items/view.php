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
                                <?php
                                $name = '';
                                if ($plan_propose->type_plan_propose == 'npl') {
                                    $name = 'Danh Mục NPL';
                                }
                                if ($plan_propose->type_plan_propose == 'tools') {
                                    $name = 'Danh Mục VPP';
                                }
                                if ($plan_propose->type_plan_propose == 'sanxuat') {
                                    $name = 'Danh Mục Vật Tư Sản Xuất';
                                }
                                ?>
                                <th class="text-center" style="width: 58px;">STT </th>
                                <th style="width: 250px;" class="text-center"><?= $name ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Đơn Vị Tính') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Số Lượng') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Đơn Giá') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Thành Tiền') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tiêu Chuẩn Chất Lượng') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Nhà Cung Cấp Được Duyệt') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Từ Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tới Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Về Kho') ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php foreach ($train as $key => $value) {
                                $infotb_1 = $this->plan_propose_model->infotb($value['items_id']);
                                $infotb_2 = $this->plan_propose_model->infotb($value['items_replace_id']);
                                $costs = $this->plan_propose_model->infocost($value['costs']);
                                $units = $this->plan_propose_model->infounit($value['units']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= ($key + 1) ?></td>
                                    <td class=""><?= ($costs['name']) ?></td>
                                    <td class="text-center"><?= ($units['unit']) ?></td>
                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                    <td class="text-right"><?= formatNumber($value['price']) ?></td>
                                    <td class="text-right"><?= formatNumber($value['quantity'] * $value['price']) ?></td>
                                    <td class="text-center"><?= ($value['workunit']) ?></td>
                                    <td class="text-center"><?= ($value['standardpass']) ?></td>
                                    <td class="text-center"><?= _d($value['date_finish']) ?></td>
                                    <td class="text-center"><?= _d($value['date_from']) ?></td>
                                    <td class="text-center"><?= _d($value['date_to']) ?></td>
                                    <td class="text-center"><?= _d($value['date_warehouse']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>