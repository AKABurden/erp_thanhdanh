<?php
$month = $this->input->post('month');
$year = $this->input->post('year');
$staff = $this->input->post('staff');
$kpi_id = $this->input->post('kpi_id');
$start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')) : null;
$end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')) : null;
$dtStaff = $this->site_model->getStaffByStaffId($staff);

if (empty($start_date) || empty($end_date)) {
    echo 'Vui lòng nhập ngày bắt đầu và ngày kết thúc';
    die;
}

$kpi = $kpi_id ? $this->kpi_model->getKpiById($kpi_id) : NULL;
$not_reached = get_option('not_reached');
$not_reached_from = get_option('not_reached_from');
$not_reached_to = get_option('not_reached_to');
$need_keep_trying = get_option('need_keep_trying');
$need_keep_trying_from = get_option('need_keep_trying_from');
$need_keep_trying_to = get_option('need_keep_trying_to');
$obtain = get_option('obtain');
$obtain_from = get_option('obtain_from');
$obtain_to = get_option('obtain_to');
$pass = get_option('pass');
$pass_from = get_option('pass_from');
$pass_to = get_option('pass_to');
?>

<?php if (!empty($dtStaff)) : ?>
    <?php
    $role_id = $dtStaff['role_id'];
    $departmentid = explode(',', $dtStaff['departmentid']);
    if (empty($departmentid[0])) {
        $departmentid = [0];
    }

    if (empty($role_id)) {
        $role_id = 0;
    }

    // $this->db->select('tbl_kpi_criteria.*');
    // $this->db->from('tbl_kpi_criteria');
    // $this->db->where("
    //     (
    //         exists (
    //             SELECT tbl_kpi_criteria_roles.id
    //             FROM tbl_kpi_criteria_roles
    //             WHERE tbl_kpi_criteria_roles.kpi_criteria_id = tbl_kpi_criteria.id AND tbl_kpi_criteria_roles.role_id = $role_id
    //         )
    //         OR
    //         exists (
    //             SELECT tbl_kpi_criteria_department.id
    //             FROM tbl_kpi_criteria_department
    //             WHERE tbl_kpi_criteria_department.kpi_criteria_id = tbl_kpi_criteria.id AND tbl_kpi_criteria_department.department_id 
    //             IN (".(implode(',', $departmentid)).")
    //         )
    //     )
    // ", false, false);
    // $kpi_criteria = $this->db->get()->result_array();

    $this->db->select('tbl_kpi_criteria.*');
    $this->db->from('tbl_kpi_criteria');
    $this->db->where('tbl_kpi_criteria.staff', $staff);
    $this->db->where('tbl_kpi_criteria.start_date <=', $start_date);
    $this->db->where('tbl_kpi_criteria.end_date >=', $end_date);
    $kpi_criteria = $this->db->get()->result_array();
    ?>
    <?php if ($kpi_criteria) : ?>
        <?php
        $total_weight_number = 0;
        ?>
        <?php foreach ($kpi_criteria as $key => $value) : ?>
            <?php
            $kpi_items = $this->kpi_model->getKpiItems($kpi_id, $value['id']);
            ?>
            <tr>
                <td class="text-center td-numbers"><?= ++$key ?></td>
                <td><?= $value['criteria'] ?></td>
                <td class="text-center"><?= $value['unit'] ?></td>
                <td class="text-center">
                    <input type="hidden" class="form-control target" value="<?= $value['target'] ?>">
                    <span class="txt-target"><?= $value['target'] ?></span>
                </td>
                <td class="text-center">
                    <input type="hidden" class="form-control weight_number" value="<?= $value['weight_number'] ?>">
                    <span class="txt-weight_number"><?= $value['weight_number'] ?></span>
                </td>
                <td class="text-center">
                    <input type="hidden" class="form-control not_reached" value="<?= $value['not_reached'] ?>">
                    <input type="hidden" class="form-control not_reached_from" value="<?= $value['not_reached_from'] ?>">
                    <input type="hidden" class="form-control not_reached_to" value="<?= $value['not_reached_to'] ?>">
                    <?php
                    echo $value['not_reached'] ? calRecipe($value['not_reached']) : '';
                    if (!empty($value['not_reached'])) {
                        echo $value['not_reached_from'] . ($value['not_reached'] == 4 ? ' - ' . $value['not_reached_to'] : '');
                    }
                    ?>
                </td>
                <td class="text-center">
                    <input type="hidden" class="form-control need_keep_trying" value="<?= $value['need_keep_trying'] ?>">
                    <input type="hidden" class="form-control need_keep_trying_from" value="<?= $value['need_keep_trying_from'] ?>">
                    <input type="hidden" class="form-control need_keep_trying_to" value="<?= $value['need_keep_trying_to'] ?>">
                    <?php
                    echo $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
                    if (!empty($value['need_keep_trying'])) {
                        echo ' ' . $value['need_keep_trying_from'] . ($value['need_keep_trying'] == 4 ? ' - ' . $value['need_keep_trying_to'] : '');
                    }
                    ?>
                </td>
                <td class="text-center">
                    <input type="hidden" class="form-control obtain" value="<?= $value['obtain'] ?>">
                    <input type="hidden" class="form-control obtain_from" value="<?= $value['obtain_from'] ?>">
                    <input type="hidden" class="form-control obtain_to" value="<?= $value['obtain_to'] ?>">
                    <?php
                    echo $value['obtain'] ? calRecipe($value['obtain']) : '';
                    if (!empty($value['obtain'])) {
                        echo ' ' . $value['obtain_from'] . ($value['obtain'] == 4 ? ' - ' . $value['obtain_to'] : '');
                    }
                    ?>
                </td>
                <td class="text-center">
                    <input type="hidden" class="form-control pass" value="<?= $value['pass'] ?>">
                    <input type="hidden" class="form-control pass_from" value="<?= $value['pass_from'] ?>">
                    <input type="hidden" class="form-control pass_to" value="<?= $value['pass_to'] ?>">
                    <?php
                    echo $value['pass'] ? calRecipe($value['pass']) : '';
                    if (!empty($value['pass'])) {
                        echo ' ' . $value['pass_from'] . ($value['pass'] == 4 ? ' - ' . $value['pass_to'] : '');
                    }
                    ?>
                </td>
                <!-- <td class="text-center">
                    <?php
                    // $violationRecords = $this->kpi_model->getViolationRecords($value['id'], $month, $year);
                    $result = 0;
                    if (!empty($violationRecords)) {
                        // echo '<div>'.formatNumber($violationRecords['count_violation_records']).'</div>';
                        // $result = $violationRecords['count_violation_records'];
                    }
                    ?>
                </td> -->
                <td class="text-center">
                    <input type="hidden" name="kpi_item_id[]" class="form-control kpi_criteria_id" value="<?= !empty($kpi_items) ? $kpi_items['id'] : 0 ?>">
                    <input type="hidden" name="kpi_criteria_id[]" class="form-control kpi_criteria_id" value="<?= $value['id'] ?>">
                    <input type="text" name="result[]" class="form-control result" onchange="totalKpi()" style="width: 100%;" value="<?= !empty($kpi_items) ? $kpi_items['result'] : $result ?>">
                </td>
                <td>
                    <?= $value['note_criteria'] ?>
                </td>
                <!-- <td class="text-center point-no-coefficient">
                </td>
                <td class="text-center point-with-coefficient">
                </td> -->
            </tr>
            <?php
            $total_weight_number += $value['weight_number'];
            ?>
        <?php endforeach; ?>
        <tfoot>
            <tr class="not-tr bold uppercase">
                <td colspan="4" class="text-center"><?= lang('tnh_total') ?></td>
                <td class="text-center"><?= formatNumber($total_weight_number) ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="txt-total_point_with_coefficient text-center"></td>
                <td></td>
            </tr>
            <tr class="not-tr bold text-danger">
                <td colspan="4" class="text-center"><?= lang('Tổng số') ?></td>
                <td colspan="9" class="txt-point-kpi text-center"></td>
            </tr>
            <tr class="not-tr text-center bold">
                <td colspan="2">
                    <?php
                    echo $not_reached ? calRecipe($not_reached) : '';
                    if (!empty($not_reached)) {
                        echo ' ' . $not_reached_from . ($not_reached == 4 ? ' - ' . $not_reached_to : '');
                    }
                    ?>
                </td>
                <td colspan="3">
                    <?php
                    echo $need_keep_trying ? calRecipe($need_keep_trying) : '';
                    if (!empty($need_keep_trying)) {
                        echo ' ' . $need_keep_trying_from . ($need_keep_trying == 4 ? ' - ' . $need_keep_trying_to : '');
                    }
                    ?>
                </td>
                <td colspan="3">
                    <?php
                    echo $obtain ? calRecipe($obtain) : '';
                    if (!empty($obtain)) {
                        echo ' ' . $obtain_from . ($obtain == 4 ? ' - ' . $obtain_to : '');
                    }
                    ?>
                </td>
                <td colspan="3">
                    <?php
                    echo $pass ? calRecipe($pass) : '';
                    if (!empty($pass)) {
                        echo ' ' . $pass_from . ($pass == 4 ? ' - ' . $pass_to : '');
                    }
                    ?>
                </td>
            </tr>
            <tr class="not-tr text-center bold">
                <td colspan="2"><?= lang('tnh_not_reached') ?></td>
                <td colspan="3"><?= lang('tnh_need_keep_trying') ?></td>
                <td colspan="3"><?= lang('tnh_obtain') ?></td>
                <td colspan="3"><?= lang('tnh_pass') ?></td>
            </tr>
        </tfoot>
    <?php else : ?>
        <tr>
            <td colspan="12"><?= lang('Không tìm thấy tiêu chí KPI cho nhân viên này') ?></td>
        </tr>
    <?php endif; ?>

<?php else : ?>
    <tr>
        <td colspan="12"><?= lang('Không tìm thấy nhân viên') ?></td>
    </tr>
<?php endif; ?>

<script>
    function totalKpi() {
        tb = '#tb-kpi tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;


        total_point_with_coefficient = 0;
        total_weight_number = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            result = intVal($(element).find('.result').val());

            target = $(element).find('.target').val();
            weight_number = intVal($(element).find('.weight_number').val());
            not_reached = $(element).find('.not_reached').val();
            not_reached_from = $(element).find('.not_reached_from').val();
            not_reached_to = $(element).find('.not_reached_to').val();

            need_keep_trying = $(element).find('.need_keep_trying').val();
            need_keep_trying_from = $(element).find('.need_keep_trying_from').val();
            need_keep_trying_to = $(element).find('.need_keep_trying_to').val();

            obtain = $(element).find('.obtain').val();
            obtain_from = $(element).find('.obtain_from').val();
            obtain_to = $(element).find('.obtain_to').val();

            pass = $(element).find('.pass').val();
            pass_from = $(element).find('.pass_from').val();
            pass_to = $(element).find('.pass_to').val();

            point_no_coefficient = 0;
            point_with_coefficient = 0;

            if (not_reached == 1 && result > not_reached_from) {
                point_no_coefficient = point_no_coefficient + 1;
            } else if (not_reached == 2 && result < not_reached_from) {
                point_no_coefficient = point_no_coefficient + 1;
            } else if (not_reached == 3 && result == not_reached_from) {
                point_no_coefficient = point_no_coefficient + 1;
            } else if (not_reached == 4 && result >= not_reached_from && result >= not_reached_to) {
                point_no_coefficient = point_no_coefficient + 1;
            }

            if (need_keep_trying == 1 && result > need_keep_trying_from) {
                point_no_coefficient = point_no_coefficient + 2;
            } else if (need_keep_trying == 2 && result < need_keep_trying_from) {
                point_no_coefficient = point_no_coefficient + 2;
            } else if (need_keep_trying == 3 && result == need_keep_trying_from) {
                point_no_coefficient = point_no_coefficient + 2;
            } else if (need_keep_trying == 4 && result >= need_keep_trying_from && result >= need_keep_trying_to) {
                point_no_coefficient = point_no_coefficient + 2;
            }

            if (obtain == 1 && result > obtain_from) {
                point_no_coefficient = point_no_coefficient + 3;
            } else if (obtain == 2 && result < obtain_from) {
                point_no_coefficient = point_no_coefficient + 3;
            } else if (obtain == 3 && result == obtain_from) {
                point_no_coefficient = point_no_coefficient + 3;
            } else if (obtain == 4 && result >= obtain_from && result >= obtain_to) {
                point_no_coefficient = point_no_coefficient + 3;
            }

            if (pass == 1 && result > pass_from) {
                point_no_coefficient = point_no_coefficient + 4;
            } else if (pass == 2 && result < pass_from) {
                point_no_coefficient = point_no_coefficient + 4;
            } else if (pass == 3 && result == pass_from) {
                point_no_coefficient = point_no_coefficient + 4;
            } else if (pass == 4 && result >= pass_from && result >= pass_to) {
                point_no_coefficient = point_no_coefficient + 4;
            }

            // $(element).find('.point-no-coefficient').html(tnhFormatNumber(point_no_coefficient));
            // point_with_coefficient = point_no_coefficient * weight_number;
            // $(element).find('.point-with-coefficient').html(tnhFormatNumber(point_with_coefficient));
            // total_point_with_coefficient+= point_with_coefficient;

            total_weight_number += weight_number;
            total_point_with_coefficient += result;
        }

        txt_point_kpi = total_point_with_coefficient / total_weight_number;
        $('.txt-point-kpi').html(tnhFormatNumber(txt_point_kpi));
        $('.txt-total_point_with_coefficient').html(tnhFormatNumber(total_point_with_coefficient));
    }

    $(document).ready(function() {
        totalKpi();
    });
</script>