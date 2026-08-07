<div class="col-md-6 hide">
    <div class="div-box-sub-menu">
        <div class="mtop20"></div>
        <?php $troubleViolationList = getTroubleViolationList($filter); ?>
        <div class="uppercase h4 text-center">VI PHẠM</div>

        <table class="table table-hover dataTable dont-responsive-table">
            <thead>
                <tr>
                    <th class="text-center">Vi phạm</th>
                    <th class="text-center">Số phiếu</th>
                    <th class="text-center">Điểm trừ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_count_trouble_violation = 0;
                $total_point = 0;
                foreach ($troubleViolationList as $tr => $rowData) { ?>
                    <tr>
                        <td style="background-color: <?= $rowData['color'] ?>"><?= $rowData['name'] ?></td>
                        <td class="text-center"><?= !empty($rowData['count_trouble_violation']) ? $rowData['count_trouble_violation'] : 0 ?> phiếu</td>
                        <td class="text-center"><?= !empty($rowData['point']) ? $rowData['point'] : 0 ?></td>
                    </tr>
                <?php
                    $total_count_trouble_violation += (!empty($rowData['count_trouble_violation']) ? $rowData['count_trouble_violation'] : 0);
                    $total_point += (!empty($rowData['point']) ? $rowData['point'] : 0);
                } ?>
            </tbody>
            <tfoot>
                <tr class="bold">
                    <td class="text-center">TỔNG</td>
                    <td class="text-center"><?= $total_count_trouble_violation ?> PHIẾU</td>
                    <td class="text-center"><?= $total_point ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="col-md-6">
    <div class="mtop20"></div>
    <?php $productionReport = getProductionReport($filter); ?>
    <div class="uppercase h4 text-center">KPI</div>

    <table class="table table-hover dataTable dont-responsive-table">
        <tbody>
            <?php foreach ($productionReport as $th => $thData) { ?>
                <tr>
                    <th><?= $th ?></th>
                </tr>
                <?php foreach ($thData as $trData) {
                    $strTypeKpi = '';
                    $point_kpi = $trData['point_kpi'];
                    if ($trData['object_type'] == '1') {
                        $img = staff_profile_image($trData['object_id'], ['staff-profile-image-small']);
                        $strTypeKpi = '<div class="col-md-2 text-right"><span class="label label-success">' . lang('staff') . '</span></div>';
                    } else {
                        $img = '<img src="' . base_url('assets/images/hierarchy.png') . '" style="width: 32px"></img>';
                        $strTypeKpi = '<div class="col-md-2 text-right"><span class="label label-primary">' . lang('department') . '</span></div>';
                    }
                ?>
                    <tr>
                        <td>
                            <div class="col-md-10">
                                <div class="col-md-10"><?= $img . ' ' . $trData['object_name'] . '</div><div class="col-md-2 text-right bold" style="color:red; font-size: 20px">' . formatMoney($point_kpi) . '</div></div> ' . $strTypeKpi ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>