// PD Approval AJAX - Reuses ajax_pdg_list endpoint
var PDA = {
    page: 1, perPage: 20, debounceTimer: null,
    statusLabels: {
        'all': {label:'Tất cả', css:'bg-sky-50 text-sky-700 border-sky-200'},
        '0': {label:'Chờ HCNS', css:'bg-slate-50 text-slate-700 border-slate-200'},
        '1': {label:'Chờ KTNB', css:'bg-yellow-50 text-yellow-700 border-yellow-200'},
        '2': {label:'Chờ KSRR', css:'bg-blue-50 text-blue-700 border-blue-200'},
        '3': {label:'Chờ BOD', css:'bg-purple-50 text-purple-700 border-purple-200'},
        '4': {label:'Hoàn tất', css:'bg-emerald-50 text-emerald-700 border-emerald-200'},
        '-1': {label:'Từ chối', css:'bg-red-50 text-red-700 border-red-200'}
    },
    statusBadge: function(s) {
        var m = {'-1':['Từ chối','bg-red-100 text-red-700'],'0':['Chờ HCNS','bg-amber-100 text-amber-700'],'1':['Chờ KTNB','bg-yellow-100 text-yellow-700'],'2':['Chờ KSRR','bg-blue-100 text-blue-700'],'3':['Chờ BOD','bg-purple-100 text-purple-700']};
        if (parseInt(s)>=4) return '<span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-700 whitespace-nowrap">Hoàn tất</span>';
        var d = m[String(s)] || ['?','bg-slate-100 text-slate-700'];
        return '<span class="px-2 py-0.5 rounded text-[10px] font-medium '+d[1]+' whitespace-nowrap">'+d[0]+'</span>';
    },
    fmtDate: function(d) { if (!d) return '-'; var p = d.split('-'); return p[2]+'/'+p[1]+'/'+p[0]; },
    esc: function(s) { var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; },
    getKpiProposal: function(point) {
        var p = parseFloat(point); if (isNaN(p)) return '<span class="text-slate-300 text-xs">—</span>';
        if (p < 75) return '<div class="text-xs leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-[10px] text-red-500 leading-tight mt-0.5">Xem xét lại</div>';
        if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[10px] text-orange-500 leading-tight mt-0.5">Cảnh báo, đào tạo lại</div>';
        if (p >= 80 && p < 90) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[10px] text-blue-500 leading-tight mt-0.5">Duy trì</div>';
        if (p >= 90 && p <= 100) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[10px] text-emerald-500 leading-tight mt-0.5">Đánh giá P3, nâng cấp</div>';
        if (p > 100) return '<div class="text-xs leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-[10px] text-purple-500 leading-tight mt-0.5">Đánh giá P3, thăng chức</div>';
        return '<span class="text-slate-300">-</span>';
    },

    load: function(pg) {
        if (pg) PDA.page = pg;
        var params = new URLSearchParams({
            page: PDA.page, per_page: PDA.perPage,
            search: document.getElementById('pd-search').value,
            room: document.getElementById('pd-room').value,
            status: document.getElementById('pd-status-filter').value,
            year: document.getElementById('pd-year').value,
            month: document.getElementById('pd-month').value,
            ky: document.getElementById('pd-ky').value
        });
        var tbody = document.getElementById('pd-tbody');
        tbody.innerHTML = '<tr><td colspan="11" style="text-align:center;padding:32px;color:#94a3b8"><div class="animate-pulse text-sm font-bold uppercase tracking-wide">Đang tải dữ liệu...</div></td></tr>';

        fetch(PD_BASE + 'ajax_pdg_list?' + params.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(function(r){return r.json()})
        .then(function(res){
            if (!res.success) { tbody.innerHTML='<tr><td colspan="11" style="text-align:center;padding:32px;color:#ef4444">Lỗi tải dữ liệu</td></tr>'; return; }
            PDA.render(res.data, res);
            PDA.renderPagination(res);
            PDA.renderStatusTabs(res.status_counts);
            document.getElementById('pd-count').textContent = res.total + ' phiếu';
        })
        .catch(function(e){ console.error(e); tbody.innerHTML='<tr><td colspan="11" style="text-align:center;padding:32px;color:#ef4444">Lỗi kết nối</td></tr>'; });
    },

    render: function(data, res) {
        var tbody = document.getElementById('pd-tbody');
        if (!data || !data.length) {
            tbody.innerHTML = '<tr><td colspan="11" style="text-align:center;color:#94a3b8;padding:48px 0"><div>Không có phiếu nào.</div></td></tr>';
            return;
        }
        var startIdx = (res.page - 1) * res.per_page;
        var html = '';
        var BASE = PD_BASE.replace('dashboardKpi/','');
        data.forEach(function(f, i) {
            var idx = startIdx + i + 1;
            var st = parseInt(f.approval_status||0);
            var typeKi = parseInt(f.type_ki || 0);
            var displayKy = '-';
            if (typeKi === 1 && f.ki) displayKy = 'Tuần ' + f.ki;
            else if (typeKi === 2 && f.ki) displayKy = 'Kỳ ' + f.ki + ' tháng';

            var pType = parseInt(f.type||1);
            var typeLabel = pType === 2 ? 'Chính thức' : 'Thử việc';
            var typeCss = pType === 2 ? 'bg-sky-100 text-sky-700 border-sky-200' : 'bg-orange-100 text-orange-700 border-orange-200';

            html += '<tr class="border-b border-slate-50 hover:bg-slate-50">';
            // Mã phiếu
            if (parseInt(f.id) > 0) {
                html += '<td class="px-3 py-2 border-b border-slate-100"><a href="'+BASE+'DashboardKpi/index/phieu_danh_gia_detail?id='+f.id+'" class="font-mono text-violet-600 hover:text-violet-800 text-xs font-medium">'+PDA.esc(f.code)+'</a></td>';
            } else {
                html += '<td class="px-3 py-2 border-b border-slate-100"><span class="text-slate-300">—</span></td>';
            }
            // Nhân sự
            html += '<td class="px-3 py-2 border-b border-slate-100"><div class="font-medium text-slate-900 text-sm">'+(PDA.esc(f.staff_name)||'-')+'</div><div class="text-[10px] text-slate-400">'+PDA.esc(f.role_name||'')+'</div></td>';
            // Bộ phận
            html += '<td class="px-3 py-2 text-xs font-semibold text-slate-600 border-b border-slate-100">'+PDA.esc(f.room_name||'-')+'</td>';
            // Loại
            html += '<td class="px-3 py-2 text-center border-b border-slate-100"><span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold border '+typeCss+'">'+typeLabel+'</span></td>';
            // Kỳ đánh giá
            html += '<td class="px-3 py-2 text-center border-b border-slate-100"><span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium bg-violet-50 text-violet-700 border border-violet-200">'+PDA.esc(displayKy)+'</span></td>';
            // Ngày BĐ
            html += '<td class="px-3 py-2 text-center font-mono text-xs text-slate-500 border-b border-slate-100">'+PDA.fmtDate(f.date_start)+'</td>';
            // Ngày KT
            html += '<td class="px-3 py-2 text-center font-mono text-xs text-slate-500 border-b border-slate-100">'+PDA.fmtDate(f.date_end)+'</td>';
            // Tổng điểm
            html += '<td class="px-3 py-2 text-right font-bold text-sm border-b border-slate-100" style="color:'+(f.rating_color||'#94a3b8')+'">'+(f.point && f.point != 0 ? parseFloat(f.point).toFixed(1) : '-')+'</td>';
            // Kết quả KPI
            html += '<td class="px-3 py-2 text-center border-b border-slate-100">' + PDA.getKpiProposal(f.point) + '</td>';
            // Trạng thái
            html += '<td class="px-3 py-2 text-center border-b border-slate-100">'+PDA.statusBadge(st)+'</td>';
            // Thao tác
            html += '<td class="px-3 py-2 text-right whitespace-nowrap border-b border-slate-100"><div class="inline-flex items-center justify-end gap-1.5">';
            if (parseInt(f.id) > 0) {
                html += '<a href="'+BASE+'DashboardKpi/index/phieu_danh_gia_detail?id='+f.id+'" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded text-xs transition-colors shadow-sm border border-blue-200 bg-white" title="Xem chi tiết"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>';
                if (st >= 0 && st < 4) {
                    html += '<button onclick="actPdApprove('+f.id+',\'approved\')" class="px-2 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors shadow-sm">Duyệt</button>';
                    html += '<button onclick="actPdApprove('+f.id+',\'rejected\')" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition-colors shadow-sm">Từ chối</button>';
                } else if (st == -1) {
                    if (f.audit_id) {
                        html += '<a href="'+BASE+'audit_management?open_audit='+f.audit_id+'" target="_blank" class="px-2 py-1 text-xs bg-violet-100 text-violet-700 rounded font-medium hover:bg-violet-200 transition-colors shadow-sm">'+PDA.esc(f.audit_code)+'</a>';
                    } else {
                        html += '<button onclick="openPdAuditModal('+f.id+','+(f.room_id||0)+')" class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded font-medium hover:bg-orange-200 transition-colors shadow-sm">Tạo phiếu audit</button>';
                    }
                } else {
                    html += '<span class="text-[10px] text-slate-400 ml-1">Đã xử lý</span>';
                }
            }
            html += '</div></td>';
            html += '</tr>';
        });
        tbody.innerHTML = html;
    },

    renderPagination: function(res) {
        var el = document.getElementById('pd-pagination');
        var info = document.getElementById('pd-page-info');
        var tp = res.total_pages || 1; var cp = res.page;
        var start = (cp-1)*res.per_page+1; var end = Math.min(cp*res.per_page, res.total);
        info.textContent = 'Hiển thị '+start+'-'+end+' / '+res.total+' phiếu';
        if (tp <= 1) { el.innerHTML = ''; return; }
        var h = ''; var btnCls = 'px-3 py-1.5 text-xs rounded-lg border transition-all font-medium ';
        h += '<button onclick="PDA.load('+(cp-1)+')" '+(cp<=1?'disabled':'')+' class="'+btnCls+(cp<=1?'opacity-30 cursor-not-allowed border-slate-200 text-slate-400':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">‹</button>';
        var pages = [];
        for (var i=1;i<=tp;i++) { if (i==1||i==tp||Math.abs(i-cp)<=2) pages.push(i); else if (pages[pages.length-1]!=='...') pages.push('...'); }
        pages.forEach(function(p){ if (p==='...') { h+='<span class="px-2 text-xs text-slate-400">…</span>'; return; } var active = p==cp; h += '<button onclick="PDA.load('+p+')" class="'+btnCls+(active?'bg-violet-600 text-white border-violet-600 shadow-sm':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">'+p+'</button>'; });
        h += '<button onclick="PDA.load('+(cp+1)+')" '+(cp>=tp?'disabled':'')+' class="'+btnCls+(cp>=tp?'opacity-30 cursor-not-allowed border-slate-200 text-slate-400':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">›</button>';
        el.innerHTML = h;
    },

    renderStatusTabs: function(counts) {
        var container = document.getElementById('pd-status-tabs');
        var current = document.getElementById('pd-status-filter').value;
        var h = '';
        Object.keys(PDA.statusLabels).forEach(function(k){
            var s = PDA.statusLabels[k]; var cnt = counts[k]||0; var active = k===current;
            h += '<button type="button" onclick="setPdStatusFilter(\''+k+'\')" class="px-4 py-2 rounded-lg text-xs font-semibold border transition-all flex items-center gap-2 '+(active?'ring-2 ring-violet-500 ring-offset-1 shadow-sm ':'')+s.css+'">';
            h += s.label+' <span class="px-1.5 py-0.5 rounded text-[10px] bg-white/60">'+cnt+'</span></button>';
        });
        container.innerHTML = h;
    }
};

function filterPdTable() {
    clearTimeout(PDA.debounceTimer);
    PDA.debounceTimer = setTimeout(function(){ PDA.page=1; PDA.load(); }, 300);
}
function setPdStatusFilter(st) {
    document.getElementById('pd-status-filter').value = st;
    PDA.page = 1; PDA.load();
}
function onPdYearMonthChange() {
    document.getElementById('pd-ky').value = '';
    var container = document.getElementById('pd-ky-tabs');
    if (!container) { PDA.load(1); return; }
    var periods = [{value:'',label:'Tất cả',icon:'layers'}];
    ['Tuần 1','Tuần 2','Tuần 3','Tuần 4'].forEach(function(t){periods.push({value:t,label:t,icon:'calendar'});});
    ['3 tháng','6 tháng','9 tháng','12 tháng'].forEach(function(t){periods.push({value:t,label:'Kỳ '+t,icon:'calendar-check'});});
    var html = '';
    periods.forEach(function(p, idx){
        var active = idx===0;
        var style = active ? 'background:#7c3aed;color:#fff;border-color:#7c3aed;box-shadow:0 1px 3px rgba(124,58,237,0.3);' : 'background:#fff;color:#475569;border-color:#e2e8f0;';
        html += '<button type="button" onclick="selectPdKyTab(this,\''+p.value+'\')" class="pd-ky-tab" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;border:1px solid;cursor:pointer;transition:all .15s;white-space:nowrap;'+style+'"><i data-lucide="'+p.icon+'" style="width:15px;height:15px"></i>'+p.label+'</button>';
    });
    container.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
    PDA.load(1);
}
function selectPdKyTab(btn, value) {
    document.getElementById('pd-ky').value = value;
    document.querySelectorAll('.pd-ky-tab').forEach(function(b){b.style.background='#fff';b.style.color='#475569';b.style.borderColor='#e2e8f0';b.style.boxShadow='none';});
    btn.style.background='#7c3aed';btn.style.color='#fff';btn.style.borderColor='#7c3aed';btn.style.boxShadow='0 1px 3px rgba(124,58,237,0.3)';
    PDA.page = 1; PDA.load();
}
