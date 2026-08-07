<?php
// Lấy cấu hình từ controller, fallback mặc định là bật (1)
$cfg = isset($sinh_phieu_config) ? $sinh_phieu_config : [];
$sw_red    = isset($cfg['sw_canh_bao_do'])   ? (int)$cfg['sw_canh_bao_do']   : 1;
$sw_repeat = isset($cfg['sw_canh_bao_lap'])  ? (int)$cfg['sw_canh_bao_lap']  : 1;
$sw_budget = isset($cfg['sw_vuot_ngan_sach']) ? (int)$cfg['sw_vuot_ngan_sach'] : 1;
?>

<div class="space-y-6">
    <!-- Toast thông báo -->
    <div id="sp-toast"
        style="display:none;position:fixed;top:1.25rem;right:1.25rem;z-index:9999;
                padding:.65rem 1.1rem;border-radius:.6rem;font-size:.85rem;font-weight:500;
                color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.15);transition:opacity .3s;">
    </div>

    <!-- Cấu hình -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Cấu hình Tự động sinh phiếu (BCKPH)</h3>
        </div>
        <div class="p-6 space-y-4">

            <!-- Switch 1: Cảnh báo đỏ -->
            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-lg">
                <div>
                    <h4 class="font-medium text-slate-900">Sinh phiếu khi cảnh báo nghiêm trọng (RED)</h4>
                    <p class="text-sm text-slate-500">Tự động tạo BCKPH khi có cảnh báo mức RED hoặc không xử lý đúng hạn SLA.</p>
                </div>
                <div class="onoffswitch">
                    <input type="checkbox"
                        name="onoffswitch"
                        class="onoffswitch-checkbox sp-toggle"
                        id="sp_sw_canh_bao_do"
                        data-key="sw_canh_bao_do"
                        <?= $sw_red ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="sp_sw_canh_bao_do"></label>
                </div>
            </div>

            <!-- Switch 2: Cảnh báo lặp lại -->
            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-lg">
                <div>
                    <h4 class="font-medium text-slate-900">Sinh phiếu khi cảnh báo lặp lại</h4>
                    <p class="text-sm text-slate-500">Tự động tạo phiếu khi cảnh báo lặp lại từ 2 kỳ trở lên.</p>
                </div>
                <div class="onoffswitch">
                    <input type="checkbox"
                        name="onoffswitch"
                        class="onoffswitch-checkbox sp-toggle"
                        id="sp_sw_canh_bao_lap"
                        data-key="sw_canh_bao_lap"
                        <?= $sw_repeat ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="sp_sw_canh_bao_lap"></label>
                </div>
            </div>

            <!-- Switch 3: Vượt 90% ngân sách -->
            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-lg">
                <div>
                    <h4 class="font-medium text-slate-900">Sinh phiếu khi vượt 90% ngân sách</h4>
                    <p class="text-sm text-slate-500">Tự động tạo phiếu khi chi phí vượt 90% ngân sách hoặc có rủi ro tài chính.</p>
                </div>
                <div class="onoffswitch">
                    <input type="checkbox"
                        name="onoffswitch"
                        class="onoffswitch-checkbox sp-toggle"
                        id="sp_sw_vuot_ngan_sach"
                        data-key="sw_vuot_ngan_sach"
                        <?= $sw_budget ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="sp_sw_vuot_ngan_sach"></label>
                </div>
            </div>

        </div>
    </div>

    <!-- Danh sách phiếu -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">Danh sách Phiếu / BCKPH tự động sinh</h3>
            <span class="text-xs text-slate-400">Lọc: audit_id > 0 | id_tasks_process > 0 | entrance_ticket_id > 0 | id_quotes > 0</span>
        </div>
        <div class="p-4 overflow-x-auto">
            <table id="sp-datatable" class="w-full" style="width:100%">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Ngày tạo</th>
                        <th>Nguồn</th>
                        <th>Nội dung</th>
                        <th>Người phụ trách</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CDN (chỉ load khi chưa có jQuery/DT từ admin layout) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
    (function checkDT() {
        function loadScript(src, cb) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = cb;
            document.head.appendChild(s);
        }

        function initDT() {
            if ($.fn.DataTable.isDataTable('#sp-datatable')) return;
            $('#sp-datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '<?= site_url('admin/RiskDashboard/get_sinh_phieu_table') ?>',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'reference_no',
                        render: function(d, t, row) {
                            var url = '<?= admin_url('production_report/detail/') ?>' + row.id;
                            return '<a href="' + url + '" target="_blank" class="font-medium" style="color:#2563eb">' + (d || '') + '</a>';
                        }
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'source_label',
                        render: function(d) {
                            var color = '#6366f1';
                            return '<span style="display:inline-block;padding:2px 10px;border-radius:9999px;font-size:.75rem;font-weight:500;background:#eef2ff;color:' + color + '">' + (d || '') + '</span>';
                        },
                        width: '400px'
                    },
                    {
                        data: 'detail_tasks',
                        render: function(d) {
                            return d ? d.substring(0, 60) + (d.length > 60 ? '…' : '') : '—';
                        }
                    },
                    {
                        data: 'staff_name',
                        defaultContent: '—'
                    },
                    {
                        data: 'status_label',
                        render: function(d, t, row) {
                            var map = {
                                'Chưa xử lý': 'background:#fefce8;color:#854d0e;border:1px solid #fde047',
                                'Đang xử lý': 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe',
                                'Hoàn thành': 'background:#f0fdf4;color:#166534;border:1px solid #86efac',
                            };
                            var s = map[d] || 'background:#f1f5f9;color:#475569';
                            return '<span style="display:inline-block;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:500;' + s + '">' + (d || '—') + '</span>';
                        }
                    }
                ],
                language: {
                    processing: 'Đang tải...',
                    search: 'Tìm kiếm:',
                    lengthMenu: 'Hiển thị _MENU_ dòng',
                    info: 'Hiển thị _START_ - _END_ / _TOTAL_ bản ghi',
                    infoEmpty: 'Không có dữ liệu',
                    zeroRecords: 'Không tìm thấy kết quả',
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹'
                    }
                },
                order: [
                    [1, 'desc']
                ],
                pageLength: 15,
            });
        }

        if (typeof $ === 'undefined') {
            loadScript('https://code.jquery.com/jquery-3.7.1.min.js', function() {
                loadScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', initDT);
            });
        } else if (typeof $.fn.DataTable === 'undefined') {
            loadScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', initDT);
        } else {
            initDT();
        }
    })();
</script>

<script>
    (function() {
        var AJAX_URL = '<?= site_url('admin/RiskDashboard/save_sinh_phieu_config') ?>';

        function showToast(msg, ok) {
            var t = document.getElementById('sp-toast');
            t.textContent = msg;
            t.style.background = ok ? '#16a34a' : '#dc2626';
            t.style.display = 'block';
            t.style.opacity = '1';
            setTimeout(function() {
                t.style.opacity = '0';
                setTimeout(function() {
                    t.style.display = 'none';
                }, 300);
            }, 2500);
        }

        // Lấy CSRF token từ cookie CI3
        function getCsrfToken() {
            var name = '<?= $this->security->get_csrf_token_name() ?>';
            var hash = '<?= $this->security->get_csrf_hash() ?>';
            return name + '=' + hash;
        }

        document.querySelectorAll('.sp-toggle').forEach(function(el) {
            el.addEventListener('change', function() {
                var key = this.dataset.key;
                var value = this.checked ? 1 : 0;
                var self = this;

                var xhr = new XMLHttpRequest();
                xhr.open('POST', AJAX_URL, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (res.status === 'success') {
                                showToast(res.message || 'Đã lưu cấu hình!', true);
                            } else {
                                showToast(res.message || 'Lưu thất bại!', false);
                                self.checked = !self.checked; // rollback
                            }
                        } catch (e) {
                            showToast('Lỗi phản hồi server!', false);
                            self.checked = !self.checked;
                        }
                    }
                };
                xhr.send('key=' + encodeURIComponent(key) + '&value=' + value + '&' + getCsrfToken());
            });
        });
    })();
</script>