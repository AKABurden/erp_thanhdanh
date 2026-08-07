<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="mb-5 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Tổng hợp đánh giá KPI</h2>
        <p class="text-xs text-slate-500">Dữ liệu đánh giá từ module hệ thống (Kpi::view_kpi_evaluation)</p>
    </div>
    <button onclick="exportThExcel()" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
        <i data-lucide="download" class="w-3.5 h-3.5"></i> Xuất Excel
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Phòng ban</label>
        <select id="th-department_search" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg select2-th">
            <option value=""></option>
            <?php if (!empty($dtDepartment)) : ?>
                <?php foreach ($dtDepartment as $key => $value) : ?>
                    <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Nhân viên</label>
        <select id="th-staff" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg select2-th">
            <option value=""></option>
            <?php if (!empty($staffs)) : ?>
                <?php foreach ($staffs as $key => $value) : ?>
                    <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Tháng</label>
        <select id="th-filter_month_new" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg select2-th">
            <?php if (!empty(getMonth())) : ?>
                <?php foreach (getMonth() as $key => $value) : ?>
                    <option <?= (date('m') == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Năm</label>
        <select id="th-year" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg select2-th">
            <?php if (!empty(getYear())) : ?>
                <?php foreach (getYear() as $key => $value) : ?>
                    <option <?= (date('Y') == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Quý</label>
        <select id="th-precious" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg select2-th">
            <option value=""></option>
            <?php if (!empty(getPrecious())) : ?>
                <?php foreach (getPrecious() as $key => $value) : ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
</div>

<!-- Table view_violation -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
        <table class="w-full text-left" id="table-th-violation">
            <thead class="bg-slate-50 border-b border-slate-100 relative z-10" style="position: sticky; top: 0;">
                <tr>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center w-[50px] border-r border-slate-200">STT</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider border-r border-slate-200 min-w-[150px]">Nhân viên</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>công việc</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>BCKPH đã có</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>BCKPH</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>VP đã có</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap cursor-pointer hover:bg-slate-200 th-violate" title="Click để sắp xếp">Số phiếu <br>vi phạm ↕</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap cursor-pointer hover:bg-slate-200 th-vuot" title="Click để sắp xếp">Số phiếu <br>vượt ↕</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>BCKPH P1</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>BCKPH P2</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Số phiếu <br>BCKPH P3</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Điểm số</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">Xếp loại</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 min-w-[150px]">Hướng xử lý</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center border-r border-slate-200 whitespace-nowrap">% P2 OKR: <br>Mức độ Tuân thủ</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center whitespace-nowrap">Mở P3</th>
                </tr>
            </thead>
            <tbody id="th_html_view_kpi_evaluation" class="text-xs divide-y divide-slate-100">
                <tr>
                    <td colspan="16" class="text-center py-10 text-slate-400"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2"></i> Đang tải dữ liệu...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    #table-th-violation { width:100%; border-collapse:collapse; font-size:0.8125rem; }
    #table-th-violation thead tr { background:#f8fafc !important; position:sticky; top:0; z-index:2; }
    #table-th-violation th {
        padding:10px 12px !important; font-size:0.7rem !important; font-weight:600 !important;
        text-transform:uppercase; letter-spacing:0.03em; color:#475569 !important;
        background:#f1f5f9 !important; white-space:nowrap;
        border-bottom:2px solid #e2e8f0 !important; border-right:1px solid #e2e8f0;
    }
    #table-th-violation td {
        padding:8px 12px !important; border-bottom:1px solid #f1f5f9;
        border-right:1px solid #f8fafc; color:#334155; vertical-align:middle;
    }
    #table-th-violation tbody tr:hover td { background-color:#faf5ff; }

    .content-text {
        display: block;
        max-height: 35px;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: max-height 0.3s ease;
    }

    /* Tuỳ chỉnh select2 cho giao diện mới */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        height: 34px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        font-size: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }
</style>

<script>
    function view_th_kpi_evaluation() {
        var filter_month = $("#th-filter_month_new").val();
        var year = $("#th-year").val();
        var staff = $("#th-staff").val();
        var precious = $("#th-precious").val();
        var department_search = $("#th-department_search").val();

        if (filter_month && year) {
            $("#th_html_view_kpi_evaluation").html('<tr><td colspan="16" class="text-center py-10 text-slate-400"><svg class="w-6 h-6 animate-spin mx-auto mb-2 text-violet-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang tải dữ liệu...</td></tr>');
            $.ajax({
                type: 'POST',
                url: '<?= admin_url() ?>' + 'DashboardKpi/view_kpi_evaluation',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    filter_month: filter_month,
                    year: year,
                    staff: staff,
                    department_search: department_search,
                    precious: precious,
                },
                dataType: "JSON",
                success: function(response) {
                    $('#th_html_view_kpi_evaluation').html(response.html);
                    lucide.createIcons();
                    $("th.th-violate").each(function() {
                        this.asc = false;
                    });

                    // Đánh lại số thứ tự
                    getTotalAllTh();
                },
                error: function(xhr) {
                    alert('Lỗi tải dữ liệu: ' + xhr.status + ' ' + xhr.statusText + '\n' + xhr.responseText.substring(0, 500));
                    $("#th_html_view_kpi_evaluation").html('<tr><td colspan="16" class="text-center py-10 text-red-500">Có lỗi xảy ra khi tải dữ liệu!</td></tr>');
                }
            });
        }
    }

    function getTotalAllTh() {
        var tb = '#table-th-violation tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        for (var ii = 0; ii < n; ii++) {
            stt++;
            var element = $(tb)[ii];
            $(element).find('.stt_all').html(stt);
        }
    }

    function exportThExcel() {
        var filter_month = $("#th-filter_month_new").val();
        var year = $("#th-year").val();
        var staff = $("#th-staff").val();
        var precious = $("#th-precious").val();
        var department_search = $("#th-department_search").val();

        $.ajax({
            type: "POST",
            url: '<?= admin_url() ?>' + 'DashboardKpi/export_excel_kpi_evaluation',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                filter_month: filter_month,
                year: year,
                staff: staff,
                department_search: department_search,
                precious: precious,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    if (typeof download === 'function') {
                        download(response.filename, response.file);
                    } else {
                        // Fallback download if global download() doesn't exist
                        var a = document.createElement("a");
                        a.href = "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," + response.file;
                        a.download = response.filename;
                        a.click();
                    }
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function toggleContent(_this) {
        var text = $(_this).prev('.content-text');
        var btn = $(_this);
        if (text.css("max-height") === "none") {
            text.css("max-height", "35px");
            btn.text("Xem thêm");
        } else {
            text.css("max-height", "none");
            btn.text("Thu gọn");
        }
    }

    $(document).ready(function() {
        $("#th-filter_month_new").select2({
            width: '100%',
            placeholder: 'Tháng'
        });
        $("#th-year").select2({
            width: '100%',
            placeholder: 'Năm'
        });
        $("#th-precious").select2({
            width: '100%',
            placeholder: 'Quý',
            allowClear: true
        });
        $("#th-staff").select2({
            width: '100%',
            placeholder: 'Nhân viên',
            allowClear: true
        });
        $("#th-department_search").select2({
            width: '100%',
            placeholder: 'Phòng ban',
            allowClear: true
        });

        view_th_kpi_evaluation();

        $(document).on('change', '#th-filter_month_new, #th-year, #th-staff, #th-department_search, #th-precious', function() {
            view_th_kpi_evaluation();
        });

        // Staff by department
        $(document).on('change', '#th-department_search', function(event) {
            var department_search = $(this).val();
            $('#th-staff').val('').trigger('change');
            $.ajax({
                type: 'POST',
                url: '<?= admin_url() ?>' + 'DashboardKpi/get_staff_by_department',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    department_search: department_search,
                },
                dataType: "JSON",
                success: function(response) {
                    var option = '<option value=""></option>';
                    if (response.staffs) {
                        $.each(response.staffs, function(key, value) {
                            option += `<option value="${value.staffid}">${value.firstname}</option>`;
                        });
                    }
                    $('#th-staff').html(option);
                }
            });
        });

        // Sort by Violations
        $('th.th-violate, th.th-vuot').click(function() {
            var table = $(this).closest('table');
            var tbody = table.find('tbody');
            var rows = tbody.find('tr:not(".not-tr")').toArray().sort(comparerTh($(this).index()));
            this.asc = !this.asc;
            if (!this.asc) rows = rows.reverse();
            for (var i = 0; i < rows.length; i++) {
                tbody.append(rows[i]);
            }
            getTotalAllTh();
        });

        function comparerTh(index) {
            return function(a, b) {
                var valA = getCellValueTh(a, index),
                    valB = getCellValueTh(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valB - valA : valA.toString().localeCompare(valB);
            }
        }

        function getCellValueTh(row, index) {
            var v = $(row).children('td').eq(index).find('input.violate_input').val();
            return v ? parseFloat(v) : 0;
        }
    });
</script>