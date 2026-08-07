<div class="col-md-12 mtop10">
    <div class="tabset">
        <!-- Tab 1 -->
        <input checked type="radio" name="tabset" id="tab2" aria-controls="detail_items_view">
        <label for="tab2"><?= lang('Thông tin chi tiết') ?></label>
        <!-- Tab 2 -->
        <input type="radio" name="tabset" id="tab3" aria-controls="detail_time_view">
        <label for="tab3"><?= lang('Thông tin thời gian') ?></label>
        <div class="tab-panels">
            <section id="detail_items_view" class="tab-panel">
                <div class="table-responsive">
                    <table id="table-items-train" class="table table-hover dont-responsive-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 58px;">STT </th>
                                <th style="width: 250px;" class="text-center"><?= lang('Thiết bị') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Danh Mục Sửa Chữa/Bảo Trì') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Vật Tư Thay Thế') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Số Lượng') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Đơn Giá') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Thành Tiền') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Đơn Vị Làm') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Từ Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tới Ngày') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Tiêu Chuẩn Đạt') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Định Mức Thay Thế') ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php foreach ($train as $key => $value) {
                                $infotb_1 = $this->plan_propose_model->infotb($value['items_id']);
                                $infotb_2 = $this->plan_propose_model->infotb($value['items_replace_id']);
                                $costs = $this->plan_propose_model->infocost($value['costs']);
                                $substitutequota[1] = 'Vượt';
                                $substitutequota[2] = 'Kém';
                                $substitutequota[3] = 'Đạt';
                            ?>
                                <tr>
                                    <td class="text-center"><?= ($key + 1) ?></td>
                                    <td class=""><?= ($infotb_1['name']) ?></td>
                                    <td class=""><?= ($costs['name']) ?></td>
                                    <td class=""><?= ($infotb_2['name']) ?></td>
                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                    <td class="text-right"><?= formatNumber($value['price']) ?></td>
                                    <td class="text-right"><?= formatNumber($value['quantity'] * $value['price']) ?></td>
                                    <td class="text-center"><?= ($value['workunit']) ?></td>
                                    <td class="text-center"><?= _d($value['date_finish']) ?></td>
                                    <td class="text-center"><?= _d($value['date_from']) ?></td>
                                    <td class=""><?= _d($value['date_to']) ?></td>
                                    <td class="text-center"><?= ($value['standardpass']) ?></td>
                                    <td class="text-center"><?= (!empty($value['substitutequota']))  ? ($substitutequota[$value['substitutequota']]) : '' ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section id="detail_items_view" class="tab-panel">
                <div class="table-responsive">
                    <table id="table-items-time" class="table table-hover dont-responsive-table" style="width: 100%;">
                        <!-- <table id="view-enquiry" class="tnh-table" style="width: 100%;min-width: 1600px;"> -->
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 58px;">STT </th>
                                <th style="width: 250px;" class="text-center"><?= lang('Mã TB') ?></th>
                                <th style="width: 250px;" class="text-center"><?= lang('Nhân viên/ Đơn vị SC') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('Giờ bắt đầu') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('Giờ kết thúc') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('Tổng giờ') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('TG kế hoạch') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('Đánh giá chất lượng') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('TG Hoàn Thành Vượt Định Mức') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('TG Hoàn Thành Kém Định Mức') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Bàn Giao Nghiệm Thu') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('TG Bảo Hành') ?></th>
                                <th style="width: 200px;" class="text-center"><?= lang('Ký tên') ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php foreach ($time as $key => $value) {
                                $infotb = $this->plan_propose_model->infotb($value['items_id_time']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= ($key + 1) ?></td>
                                    <td class=""><?= ($infotb['name']) ?></td>
                                    <td class=""><?= ($value['staff']) ?></td>
                                    <td class="text-center"><?= ($value['timestart']) ?></td>
                                    <td class="text-center"><?= ($value['timeend']) ?></td>
                                    <td class="text-center"><?= ($value['alltime']) ?></td>
                                    <td class="text-center"><?= ($value['allplan']) ?></td>
                                    <td class=""><?= ($value['evaluate']) ?></td>
                                    <td class="text-center"><?= ($value['exceededthequota']) ?></td>
                                    <td class="text-center"><?= ($value['underperformingthenorm']) ?></td>
                                    <td class=""><?= ($value['handoverdesk']) ?></td>
                                    <td class=""><?= ($value['warranty']) ?></td>
                                    <td class=""><?= ($value['sign']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>