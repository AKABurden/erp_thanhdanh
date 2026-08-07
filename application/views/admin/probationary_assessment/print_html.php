<?php
function cb($v) { return $v ? '☑' : '☐'; }
?>

<style>
    body { font-family: dejavusans; font-size: 9px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 4px;line-height: 2.0; vertical-align: middle; }
    .center { text-align: center; }
    .right { text-align: right; }
    .bold { font-weight: bold; }
    .section { background: #eee; font-weight: bold; }
</style>

<h2 align="center">
    <?php if ($type == 1){
        echo "PHIẾU YÊU CẦU ĐÁNH GIÁ THỬ VIỆC<br>
    NÂNG BẬC THEO THANG SỰ NGHIỆP";
    } else {
        echo "PHIẾU YÊU CẦU ĐÁNH GIÁ";
    }
    ?>
</h2>
<p align="center"><i>(Theo SOP – KPI – Audit – BCKPH – Thang sự nghiệp)</i></p>

<!-- INFO -->
<table>
    <tr>
        <td width="15%">Họ tên</td>
        <td width="35%"><?= $dtData['firstname'].' '.$dtData['lastname']?></td>
        <td width="15%">Vị trí</td>
        <td width="35%"><?= $dtData['name_role'] ?? '' ?></td>
    </tr>
    <tr>
        <td>Phòng ban</td>
        <td><?= $dtData['name_room'] ?? '' ?></td>
        <td>Level mục tiêu</td>
        <td>
            <?php foreach ($levelChecklist as $lv): ?>
                <?= cb(($dtData['level_target'] ?? null) == $lv['id']) ?>
                <?= $lv['code'] ?>&nbsp;
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td>Thử việc từ</td>
        <td><?= _dhau($dtData['date_start']) ?? '' ?></td>
        <td>Đến ngày</td>
        <td><?= _dhau($dtData['date_end']) ?? '' ?></td>
    </tr>
</table>

<br>

<!-- A -->
<table>
    <tr><td colspan="4" class="section">A. <?= getTypeCheckList('A')['name'] ?? '' ?></td></tr>
    <tr class="bold center">
        <td width="50%">Điều kiện bắt buộc</td>
        <td width="10%">YES</td>
        <td width="10%">NO</td>
        <td width="30%">Ghi chú</td>
    </tr>

    <?php foreach ($checkList['A'] as $v):
        $s = $mappedItems['A'][$v['id']] ?? [];
        ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td class="center"><?= cb(($s['gate'] ?? 0) == 1) ?></td>
            <td class="center"><?= !empty($s['gate']) ? cb(($s['gate'] ?? 0) == 0) : '☐' ?></td>
            <td><?= $s['note'] ?? '' ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<br>

<!-- B -->
<table>
    <tr><td colspan="4" class="section">B. <?= getTypeCheckList('B')['name'] ?? '' ?></td></tr>
    <tr class="bold center">
        <td>Tiêu chí</td>
        <td>Chuẩn</td>
        <td>Thực tế</td>
        <td>Điểm</td>
    </tr>

    <?php $sumB = 0; foreach ($checkList['B'] as $v):
        $s = $mappedItems['B'][$v['id']] ?? [];
        $sumB += $v['point'];
        $dtOpera = getTypeOperation($v['operation']);
        ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td><?= $dtOpera['name'] ?? '' ?> <?= $v['conditions'] ?> <?= $v['prefix'] ?></td>
            <td class="center"><?= !empty($s['percent']) ? $s['percent'] : '' ?></td>
            <td class="center"><?= !empty($s['point']) ? $s['point'] :  '' ?> / <?= $v['point'] ?></td>
        </tr>
    <?php endforeach; ?>

    <tr class="bold">
        <td colspan="3" class="right">Tổng điểm Phần B</td>
        <td class="center"><?= !empty($dtData['point_b']) ? $dtData['point_b'] : '' ?> / <?= $sumB ?></td>
    </tr>
</table>

<br>

<!-- C -->
<table>
    <tr><td colspan="4" class="section">C. <?= getTypeCheckList('C')['name'] ?? '' ?></td></tr>

    <?php $sumC = 0; foreach ($checkList['C'] as $v):
        $s = $mappedItems['C'][$v['id']] ?? [];
        $sumC += $v['point'];
        $dtOpera = getTypeOperation($v['operation']);
        ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td><?= $dtOpera['name'] ?? '' ?> <?= $v['conditions'] ?> <?= $v['prefix'] ?></td>
            <td class="center"><?= !empty($s['percent'] ) ? $s['percent']  : '' ?></td>
            <td class="center"><?= !empty($s['point']) ? $s['point'] : '' ?> / <?= $v['point'] ?></td>
        </tr>
    <?php endforeach; ?>

    <tr class="bold">
        <td colspan="3" class="right">Tổng điểm Phần C</td>
        <td class="center"><?= !empty($dtData['point_c']) ? $dtData['point_c'] : '' ?> / <?= $sumC ?></td>
    </tr>
</table>

<br>

<!-- D -->
<table>
    <tr><td colspan="2" class="section">D. <?= getTypeCheckList('D')['name'] ?? '' ?></td></tr>

    <?php $sumD = 0; foreach ($checkList['D'] as $v):
        $s = $mappedItems['D'][$v['id']] ?? [];
        $sumD += $v['point'];
        ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td class="center"><?= !empty($s['point']) ? $s['point'] : '' ?> / <?= $v['point'] ?></td>
        </tr>
    <?php endforeach; ?>

    <tr class="bold">
        <td class="right">Tổng điểm Phần D</td>
        <td class="center"><?= !empty($dtData['point_d']) ? $dtData['point_d'] : '' ?> / <?= $sumD ?></td>
    </tr>
</table>

<br>

<!-- E -->
<table>
    <tr><td colspan="2" class="section">E. ĐỐI CHIẾU THANG SỰ NGHIỆP</td></tr>
    <?php foreach ($levelChecklist as $lv): ?>
        <tr>
            <td width="20%" class="center"><?= $lv['code'] ?></td>
            <td  width="80%"><?= $lv['name'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<br>

<!-- F -->
<table>
    <tr>
        <td colspan="2" class="section">F. <?= getTypeCheckList('F')['name'] ?? 'TỔNG HỢP & KẾT LUẬN' ?></td>
    </tr>

    <!-- Tổng hợp điểm -->
    <tr class="bold center">
        <td width="70%">Hạng mục</td>
        <td width="30%">Điểm</td>
    </tr>

    <tr>
        <td><?= getTypeCheckList('B')['name_result'] ?? 'KPI P2' ?></td>
        <td class="center"><?= !empty($dtData['point_b']) ? $dtData['point_b'] : '' ?> / <?= $sumB ?></td>
    </tr>

    <tr>
        <td><?= getTypeCheckList('C')['name_result'] ?? 'Tuân thủ' ?></td>
        <td class="center"><?= !empty($dtData['point_c']) ? $dtData['point_c'] : '' ?> / <?= $sumC ?></td>
    </tr>

    <tr>
        <td><?= getTypeCheckList('D')['name_result'] ?? 'Năng lực' ?></td>
        <td class="center"><?= !empty($dtData['point_d']) ? $dtData['point_d'] : '' ?> / <?= $sumD ?></td>
    </tr>

    <tr class="bold">
        <td class="right">TỔNG CỘNG</td>
        <td class="center">
            <?=  !empty($dtData['point']) ? $dtData['point'] : '' ?> /
            <?= ($sumB + $sumC + $sumD) ?>
        </td>
    </tr>
</table>
<br>

<!-- ĐỀ XUẤT -->
<table>
    <tr>
        <td colspan="2" class="section">ĐỀ XUẤT</td>
    </tr>

    <?php foreach ($resultChecklist as $r): ?>
        <?php
        $checked = (!empty($dtData) && $dtData['rating_list'] == $r['id']);
        $failTxt = $r['check_fail_gate'] ? ' hoặc Fail Gate' : '';
        ?>
        <tr>
            <td width="5%" class="center"><?= cb($checked) ?></td>
            <td width="95%">
                <strong><?= $r['name'] ?></strong>
                (<?= $r['point_start'] ?> - <?= $r['point_end'] ?><?= $failTxt ?>)
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<br><br>

<table>
    <tr>
        <td width="50%" class="center">
            <strong>NGƯỜI ĐÁNH GIÁ</strong><br><br><br>

        </td>
        <td width="50%" class="center">
            <strong>TRƯỞNG BỘ PHẬN</strong><br><br><br>

        </td>
    </tr>
</table>
