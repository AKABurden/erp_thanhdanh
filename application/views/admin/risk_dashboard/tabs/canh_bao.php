<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Tổng cảnh báo mới</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900" id="alerts_count">0</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Severity 3-4 (Nghiêm trọng)</h3>
            <div class="text-3xl font-bold tracking-tight text-red-600" id="sev34_count">0</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Cảnh báo lặp lại</h3>
            <div class="text-3xl font-bold tracking-tight text-orange-500" id="repeat_alerts">0</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Tổng Impact (VNĐ)</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900" id="total_impact">0</div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Danh sách Cảnh báo tự động</h3>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khởi phát</th>
                        <th>Phòng ban</th>
                        <th>Mức độ</th>
                        <th>Nội dung</th>
                        <th>Impact (VNĐ)</th>
                        <th>Đã có mã phiếu YC</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="canh_bao_table_body">
                    <tr>
                        <td colspan="8" class="text-center py-4">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const formatMoney = (amount) => {
            return new Intl.NumberFormat('vi-VN').format(amount);
        };

        const categoryLabels = {
            'quy_tien_mat': { label: 'Quỹ tiền mặt', cls: 'bg-purple-100 text-purple-700' },
            'thue_vat':      { label: 'Thuế VAT',      cls: 'bg-orange-100 text-orange-700' },
            'ke_toan_audit': { label: 'KT→Audit',      cls: 'bg-blue-100 text-blue-700'   },
            'ngan_sach':     { label: 'Ngân sách',     cls: 'bg-red-100 text-red-700'     },
            'thiet_bi':      { label: 'Thiết bị',      cls: 'bg-slate-100 text-slate-700' },
            'khach_hang':    { label: 'Khách hàng',    cls: 'bg-pink-100 text-pink-700'   },
            'nha_cung_cap':  { label: 'Nhà CC',        cls: 'bg-teal-100 text-teal-700'   },
            'nhan_su':       { label: 'Nhân sự',       cls: 'bg-indigo-100 text-indigo-700' },
        };

        fetch("<?= site_url('admin/RiskDashboard/get_canh_bao_dashboard_data') ?>")
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    const data = res.data;
                    document.getElementById('alerts_count').textContent  = data.alerts_count  || 0;
                    document.getElementById('sev34_count').textContent   = data.sev34_count   || 0;
                    document.getElementById('repeat_alerts').textContent = data.repeat_alerts || 0;
                    document.getElementById('total_impact').textContent  = formatMoney(data.total_impact || 0);

                    const tbody = document.getElementById('canh_bao_table_body');
                    tbody.innerHTML = '';

                    if (data.warnings && data.warnings.length > 0) {
                        data.warnings.forEach(w => {
                            const catInfo = categoryLabels[w.category] || { label: w.category || '—', cls: 'bg-slate-100 text-slate-600' };
                            const catBadge = w.category
                                ? `<span class="ml-1 px-1.5 py-0.5 rounded text-xs font-medium ${catInfo.cls}">${catInfo.label}</span>`
                                : '';

                            const ycBadge = w.has_yc_code
                                ? `<span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800">✓ Đã có</span>`
                                : `<span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-500">Chưa có</span>`;

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="font-medium text-slate-900">${w.id}</td>
                                <td class="text-slate-500">${w.time}</td>
                                <td>${w.department}</td>
                                <td><span class="px-2.5 py-1 rounded-md text-xs font-semibold ${w.severity_class}">${w.severity}</span></td>
                                <td>${w.content}${catBadge}</td>
                                <td class="font-mono text-slate-900">${w.impact > 0 ? formatMoney(w.impact) : '-'}</td>
                                <td class="text-center">${ycBadge}</td>
                                <td><span class="px-2.5 py-1 rounded-md text-xs font-medium ${w.status_class}">${w.status}</span></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-slate-500">Không có cảnh báo tự động.</td></tr>';
                    }
                }
            })
            .catch(err => {
                console.error("Error fetching cảnh báo:", err);
                document.getElementById('canh_bao_table_body').innerHTML = '<tr><td colspan="8" class="text-center py-4 text-red-500">Lỗi khi tải dữ liệu</td></tr>';
            });
    });
</script>
