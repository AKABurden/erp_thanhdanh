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
                                $object = '';
                                $name = '';
                                if ($plan_propose->type_plan_propose == 'vouchers_coupon') {
                                    $name = 'Danh Mục HĐ Thu';
                                    $object = 'Khách Hàng';
                                }
                                if ($plan_propose->type_plan_propose == 'pay_slip') {
                                    $name = 'Danh Mục HĐ Chi';
                                    $object = 'Nhà Cung Cấp';
                                }
                                ?>
                                <th class="text-center" style="width: 58px;">STT </th>
                                <th style="width: 250px;" class="text-center"><?= $object ?></th>
                                <th style="width: 250px;" class="text-center"><?= $name ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Đơn Vị Tiền') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Thành Tiền') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Từ Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tới Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Về Kho') ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php foreach ($train as $key => $value) {
                                $costs = $this->plan_propose_model->infocost($value['costs']);
                                $unitscost = $this->plan_propose_model->infounitcost($value['units_cost']);
                                $object = $this->plan_propose_model->infoobject($value['object']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= ($key + 1) ?></td>
                                    <td class=""><?= ($object['text']) ?></td>
                                    <td class=""><?= ($costs['name']) ?></td>
                                    <td class="text-center"><?= ($unitscost['name']) ?></td>
                                    <td class="text-right"><?= formatNumber($value['price']) ?></td>
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