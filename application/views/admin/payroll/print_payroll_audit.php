<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In phiếu lương</title>
    <style type="text/css">
        html,
        body {
            /*height: 100%;*/
            background: #FFF;
            color: black;
        }

        p {
            margin: 0px;
            padding: 0px;
        }

        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        thead th {
            text-align: center !important;
        }

        body:before,
        body:after {
            display: none !important;
        }

        .table th {
            text-align: center;
            padding: 5px;
        }

        .table td {
            padding: 4px;
        }

        .contai_p {
            width: 60%;
            float: left;
            font-family: Arial;
        }

        .contai_m {
            width: 31.6%;
            float: left;
            font-family: Arial;
        }

        .HT {
            padding: 7px;
            font-family: Arial;
        }

        .DC {
            padding: 7px;
            font-family: Arial;
        }

        .ST {
            padding: 7px;
            font-family: Arial;
        }

        .BC {
            padding: 7px;
            font-family: Arial;
        }

        .KT {
            padding: 7px;
            font-family: Arial;
        }

        .TD {
            font-family: Arial;

        }

        .PT {
            font-family: Arial;
        }

        .custom-table {
            margin: 30px;
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 350px;

        }


        table tr th,
        #CompTable tr td {
            /*border: 1px solid black;*/
            border-bottom: 1px solid #ddd;
            /*border-right: 1px solid black;*/
            /*border-top: 1px solid black;*/
            padding: 5px;
        }

        .inline-block {
            display: inline-block;
        }

        .image {
            max-width: 80px;
            height: auto;
        }

        .info-right {
            position: absolute;
            top: 1px;
            right: 0;
        }

        .row {
            position: relative;
        }

        strong {
            font-weight: 700;
            font-size: 13px;
        }

        .title-right-top {
            text-align: end;
        }

        .title-right-top strong {
            border: 1px solid;
            padding: 3px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right !important;
        }

        .center h1 {
            font-size: 24px;
        }

        .magin > .a {
            margin-top: 5px;
        }

        .info-right1 {
            position: absolute;
            border: 1px solid #777;
            padding: 15px;
            top: 10px;
            right: 0;
        }

        .left {
            text-align: left !important;
        }


        /* custom */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom tr td {
            border: 1px solid black;
        }

        .bold {
            font-weight: bold;
        }

        .table-brackground {
            background-color: #d8da5c;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        @media print {
            * {
                color-adjust: exact;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tab-wrapper {
                width: auto !important;
            }

            h3 {
                margin-top: 0;
            }

            .table-brackground {
                background-color: #d8da5c;
            }

            .container p,
            .pagination,
            .well {
                display: none;
            }
        }
    </style>
</head>

<body onload="">
<div class="tab-wrapper">
    <div class="well center" style="height: 55px;">
            <span style="margin-top:15px; display: block;">
                <div class="btn-group">
                    <a class="btn btn-primary"
                       style="color: #fff;background-color: #428bca; border-color: #357ebd; padding: 10px;" href="#"
                       onclick="window.print();">
                        <i class="fa fa-print"></i>
                        In phiếu lương                    </a>
                </div>
            </span>
    </div>
    <div id="wrap" style="padding:1px;">
        <?php $check_key_new = 0;
        $stt = 1;
        if (!empty($personnel)) { ?>
            <?php foreach ($personnel as $key => $value) { ?>
                <?php
                $check_key_new ++;
                if ($check_key_new == 1) {
                    echo '<div style="display: flex; margin-bottom: 100px;justify-content: space-between">';
                }
                $sttNew = 1;
                ?>
                <table class="table-custom" style="margin-right: 5px;width: 49.5%">
                    <tr>
                        <td style="width: 50%;" class="bold"><?= $value['code']  ?></td>
                        <td class="right"><?= ($stt) ?></td>
                    </tr>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Họ tên</td>
                        <td class="bold"><?= $value['fullname'] ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Lương vị trí (LCB)</td>
                        <td class="right"><?= formatMoney($value['salary_bhxh']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Lương năng lực (chức vụ)</td>
                        <td class=" right"><?= formatMoney($value['salary_position']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Lương năng lực (T.Nhiệm)</td>
                        <td class=" right"><?= formatMoney($value['salary_responsibility']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Số giờ công</td>
                        <td class=" right"><?= $value['day_number'] ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Số ngày công</td>
                        <td class=" right"><?= $value['day_number_new'] ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Ngày nghỉ phép năm</td>
                        <td class=" right"><?= $value['day_holiday'] ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">(<?= $sttNew ?>) Ngày nghỉ lễ tết</td>
                        <td class=" right"><?= $value['day_lt'] ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Thu nhập</td>
                        <td class="bold right"><?= formatMoney($value['salary_income']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">Phụ cấp</td>
                        <td class="bold right"></td>
                    </tr>
                    <?php if (!empty($dtAllowance)){ ?>
                        <?php foreach ($dtAllowance as $kk => $vv){?>
                            <?php $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',['category_id' => $vv['id'],'payroll_item_id' => $value['id'],'staff_id' => $value['staff_id'],'type' => 1],'','row_array'); ?>
                            <tr style="width: 50%;">
                                <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<?= $sttNew ?>) <?= $vv['name'] ?></td>
                                <td class=" right"><?= !empty($dtAllowanceReduce['amount']) ? formatMoney($dtAllowanceReduce['amount']) : 0 ?></td>
                            </tr>
                            <?php $sttNew++; ?>
                        <?php } ?>
                    <?php } ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày cơm hành chính</td>
                        <td class=" right"><?= formatMoney($value['allowance_rice']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày cơm tăng ca</td>
                        <td class=" right"><?= formatMoney($value['allowance_rice_tc']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Tiền cơm</td>
                        <td class=" right"><?= formatMoney($value['allowance_rice_money']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Tổng phụ cấp</td>
                        <td class="bold right"><?= formatMoney($value['total_allowance_other']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">Số tiếng tăng ca</td>
                        <td class="bold right"></td>
                    </tr>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày thường(1.5)</td>
                        <td class=" right"><?= (!empty($value['total_weekday']) ? $value['total_weekday'] : 0) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Chủ nhật(2.0)</td>
                        <td class=" right"><?= (!empty($value['total_sunday']) ? $value['total_sunday'] : 0) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày lễ tết(3.0)</td>
                        <td class=" right"><?= (!empty($value['total_holiday']) ? $value['total_holiday'] : 0) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày lễ tết(<?= get_option('coefficient_default_night') ?>)</td>
                        <td class=" right"><?= (!empty($value['total_weekday_night']) ? $value['total_weekday_night'] : 0) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Ngày lễ tết(<?= get_option('coefficient_sunday_night') ?>)</td>
                        <td class=" right"><?= (!empty($value['total_sunday_night']) ? $value['total_sunday_night'] : 0) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Tổng tiền tăng ca</td>
                        <td class="bold right"><?= formatMoney($value['allowance_business_fee']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">Khấu trừ</td>
                        <td class="bold right"></td>
                    </tr>
                    <?php if (!empty($dtReduce)){ ?>
                        <?php foreach ($dtReduce as $kk => $vv){?>
                            <?php $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',['category_id' => $vv['id'],'payroll_item_id' => $value['id'],'staff_id' => $value['staff_id'],'type' => 2],'','row_array'); ?>
                            <tr style="width: 50%;">
                                <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) <?= $vv['name'] ?></td>
                                <td class=" right"><?= !empty($dtAllowanceReduce['amount']) ? formatMoney($dtAllowanceReduce['amount']) : 0 ?></td>
                            </tr>
                            <?php $sttNew++; ?>
                        <?php } ?>
                    <?php } ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) BHXH(8%)</td>
                        <td class=" right"><?= formatMoney($value['deduct_bhxh']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) BHYT(1.5%)</td>
                        <td class=" right"><?= formatMoney($value['deduct_bhyt']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) BHTN(1%)</td>
                        <td class=" right"><?= formatMoney($value['deduct_bhtn']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;">
                        <td class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?= $sttNew ?>) Đoàn phí(1%)</td>
                        <td class=" right"><?= formatMoney($value['deduct_union']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="">
                        <td class="bold">(<?= $sttNew ?>) Khấu trừ tạm ứng</td>
                        <td class=" right"><?= formatMoney($value['deduct_advance']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Trừ thuế TNCN</td>
                        <td class="bold right"><?= formatMoney($value['total_vat']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Tổng khấu trừ</td>
                        <td class="bold right"><?= formatMoney($value['total_reduce_other']) ?></td>
                    </tr>
                    <?php $sttNew++; ?>
                    <tr style="width: 50%;" class="table-brackground">
                        <td class="bold">(<?= $sttNew ?>) Tổng thực lãnh</td>
                        <td class="bold right"><?= formatMoney($value['total_real']) ?></td>
                    </tr>
                </table>
                <?php
                if ($check_key_new == 2 || (count($personnel) - 1 == $key)) {
                    echo '</div>';
                    $check_key_new = 0;
                }
                ?>
                <?php $stt++; } ?>
        <?php } ?>
    </div>
</div>
</div>
</body>

</html>