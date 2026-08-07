// PDG AJAX Pagination
var PDG = {
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
        var m = {'-1':['Từ chối','bg-red-100 text-red-800'],'0':['Chờ HCNS','bg-slate-100 text-slate-800'],'1':['Chờ KTNB','bg-yellow-100 text-yellow-800'],'2':['Chờ KSRR','bg-blue-100 text-blue-800'],'3':['Chờ BOD','bg-purple-100 text-purple-800']};
        if (parseInt(s)>=4) return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">Hoàn tất</span>';
        var d = m[String(s)] || ['?','bg-slate-100'];
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium '+d[1]+'">'+d[0]+'</span>';
    },
    fmtDate: function(d) {
        if (!d) return '-';
        var p = d.split('-'); return p[2]+'/'+p[1]+'/'+p[0];
    },
    fmtDateTime: function(d) {
        if (!d || d === '0000-00-00 00:00:00') return '';
        var parts = d.split(' ');
        var dp = parts[0].split('-');
        var ts = parts.length > 1 ? parts[1].substring(0,5) : '';
        return ts + ' ' + dp[2]+'/'+dp[1]+'/'+dp[0].substring(2);
    },
    esc: function(s) { var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; },
    getProbationProposal: function(point) {
        var p = parseFloat(point);
        if (isNaN(p)) return '-';
        if (p < 75) return '<div class="text-xs leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-[11px] text-red-500 leading-tight mt-0.5">Không Tuyển, Chấm dứt thử việc</div>';
        if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[11px] text-orange-500 leading-tight mt-0.5">Duy trì thử việc, Giám sát đến khi đạt</div>';
        if (p >= 80 && p < 85) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[11px] text-blue-500 leading-tight mt-0.5">Thử việc chính thức</div>';
        if (p >= 85) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[11px] text-emerald-500 leading-tight mt-0.5">Ký HĐ chính thức</div>';
        return '-';
    },
    getKpiProposal: function(point) {
        var p = parseFloat(point);
        if (isNaN(p)) return '-';
        if (p < 75) return '<div class="text-xs leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-[11px] text-red-500 leading-tight mt-0.5">Xem xét lại</div>';
        if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[11px] text-orange-500 leading-tight mt-0.5">Cảnh báo, đào tạo lại ngay</div>';
        if (p >= 80 && p < 90) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[11px] text-blue-500 leading-tight mt-0.5">Duy trì</div>';
        if (p >= 90 && p <= 100) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[11px] text-emerald-500 leading-tight mt-0.5">Đánh giá P3, đào tạo nâng cấp</div>';
        if (p > 100) return '<div class="text-xs leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-[11px] text-purple-500 leading-tight mt-0.5">Đánh giá P3, đào tạo thăng chức</div>';
        return '<span class="text-slate-300">-</span>';
    },

    load: function(pg) {
        if (pg) PDG.page = pg;
        var params = new URLSearchParams({
            page: PDG.page, per_page: PDG.perPage,
            search: document.getElementById('pdg-search').value,
            room: document.getElementById('pdg-room').value,
            status: document.getElementById('pdg-status-filter').value,
            year: document.getElementById('pdg-year').value,
            month: document.getElementById('pdg-month').value,
            ky: document.getElementById('pdg-ky').value
        });
        var tbody = document.getElementById('pdg-tbody');
        tbody.innerHTML = '<tr><td colspan="13" style="text-align:center;padding:32px;color:#94a3b8"><div class="animate-pulse text-sm font-bold uppercase tracking-wide">Đang tải dữ liệu...</div></td></tr>';

        fetch(PD_BASE + 'ajax_pdg_list?' + params.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(function(r){return r.json()})
        .then(function(res){
            if (!res.success) { tbody.innerHTML='<tr><td colspan="13" style="text-align:center;padding:32px;color:#ef4444">Lỗi tải dữ liệu</td></tr>'; return; }
            PDG.render(res.data, res);
            PDG.renderPagination(res);
            PDG.renderStatusTabs(res.status_counts);
            document.getElementById('pdg-count').textContent = res.total + ' phiếu';
            
            // Update "Create New" button with current filters
            var btnCreate = document.getElementById('btn-create-new');
            if (btnCreate) {
                var url = new URL(btnCreate.href);
                url.searchParams.set('year', document.getElementById('pdg-year').value);
                url.searchParams.set('month', document.getElementById('pdg-month').value);
                url.searchParams.set('ky', document.getElementById('pdg-ky').value);
                btnCreate.href = url.toString();
            }
        })
        .catch(function(e){ console.error(e); tbody.innerHTML='<tr><td colspan="13" style="text-align:center;padding:32px;color:#ef4444">Lỗi kết nối</td></tr>'; });
    },

    render: function(data, res) {
        var tbody = document.getElementById('pdg-tbody');
        if (!data || !data.length) {
            tbody.innerHTML = '<tr><td colspan="13" style="text-align:center;color:#94a3b8;padding:48px 0"><div>Không có phiếu nào.</div></td></tr>';
            return;
        }
        var startIdx = (res.page - 1) * res.per_page;
        var html = '';
        var BASE = PD_BASE.replace('dashboardKpi/','');
        data.forEach(function(f, i) {
            var idx = startIdx + i + 1;
            var st = parseInt(f.approval_status||0);
            var stKey = st >= 4 ? 4 : (st==-1 ? -1 : st);
            var p2val = f.weight_p2 && f.weight_p2 > 0 ? (100 - f.weight_p2)+'%' : '100%';
            var ds = f.date_start ? f.date_start.substring(5,7) : '01';
            var dy = f.date_start ? f.date_start.substring(0,4) : new Date().getFullYear();

            html += '<tr class="pdg-row">';
            // #
            html += '<td class="text-center text-slate-400 font-mono text-xs whitespace-nowrap">';
            html += '<button type="button" onclick="toggleProcessRow(this)" class="w-5 h-5 inline-flex items-center justify-center rounded hover:bg-slate-100 mr-1"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition:transform .2s;transform:rotate(0deg)"><polyline points="9 18 15 12 9 6"/></svg></button>';
            html += idx + '</td>';
            // Mã phiếu
            html += '<td><a href="'+BASE+'DashboardKpi/index/phieu_danh_gia_detail?id='+f.id+'" class="font-mono text-violet-600 hover:text-violet-800 font-medium text-xs">'+PDG.esc(f.code)+'</a></td>';
            // NV + CV
            html += '<td class="text-xs text-slate-800" style="min-width:130px"><div class="font-medium">'+(PDG.esc(f.staff_name)||'-')+'</div><div class="text-[10px] text-slate-400 mt-0.5">'+PDG.esc(f.role_name)+'</div></td>';
            // Phòng ban
            html += '<td class="text-xs text-slate-600">'+PDG.esc(f.room_name||'-')+'</td>';
            // Loại
            var typeLabel = parseInt(f.type) === 2 ? 'Chính thức' : 'Thử việc';
            var typeCss = parseInt(f.type) === 2 ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'bg-orange-100 text-orange-700 border border-orange-200';
            html += '<td class="text-center"><span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold ' + typeCss + '">' + typeLabel + '</span></td>';
            // Kỳ
            var typeKi = parseInt(f.type_ki || 0);
            var displayKy = '-';
            if (typeKi === 1 && f.ki) {
                displayKy = 'Tuần ' + f.ki;
            } else if (typeKi === 2 && f.ki) {
                displayKy = 'Kỳ ' + f.ki + ' tháng';
            } else {
                var curKy = document.getElementById('pdg-ky').value;
                displayKy = (curKy && curKy.indexOf('Tuần') !== -1) ? (f.ky_tuan || f.ky_danh_gia) : (f.ky_danh_gia || '-');
            }
            html += '<td class="text-center"><span class="pdg-ky-cell inline-flex items-center px-2 py-1 rounded text-[11px] font-medium bg-violet-50 text-violet-700 border border-violet-200">'+PDG.esc(displayKy)+'</span></td>';
            // Thời gian
            html += '<td class="text-center font-mono text-[10px] text-slate-500 leading-relaxed">'+PDG.fmtDate(f.date_start)+'<br><span class="text-slate-300">→</span> '+PDG.fmtDate(f.date_end)+'</td>';
            // Điểm
            html += '<td class="text-center"><div class="text-sm font-bold" style="color:'+(f.rating_color||'#334155')+'">'+(f.point||'-')+'</div></td>';
            // Kết quả KPI
            var ketQuaHtml = parseInt(f.type) === 2 ? PDG.getKpiProposal(f.point) : PDG.getProbationProposal(f.point);
            html += '<td class="text-center">' + ketQuaHtml + '</td>';
            // %P2
            html += '<td class="text-center text-xs font-mono"><a href="'+BASE+'DashboardKpi/modal_detail_p2/'+f.staff_id+'/'+ds+'/'+dy+'" class="text-blue-600 hover:underline" onclick="openDetailModal(\'P2 - '+PDG.esc(f.staff_name)+'\',this.href);return false;">'+p2val+'</a></td>';
            // Audit
            html += '<td class="text-center">';
            if (f.audit_id) html += '<a href="'+BASE+'audit_management?open_audit='+f.audit_id+'" target="_blank" class="px-2 py-0.5 text-[10px] bg-violet-100 text-violet-700 rounded font-bold hover:bg-violet-200 shadow-sm">'+PDG.esc(f.audit_code)+'</a>';
            else html += '<span class="text-slate-300 text-xs">—</span>';
            html += '</td>';
            // Trạng thái
            html += '<td class="text-center">'+PDG.statusBadge(st)+'</td>';
            // Thao tác
            var editUrl = BASE + 'DashboardKpi/index/phieu_danh_gia_detail?id=' + f.id + '&year=' + (document.getElementById('pdg-year').value) + '&month=' + (document.getElementById('pdg-month').value) + '&ky=' + (document.getElementById('pdg-ky').value);
            html += '<td style="text-align:center"><div style="display:flex;gap:4px;justify-content:center">';
            if (parseInt(f.id) > 0) {
                html += '<a href="' + editUrl + '" style="padding:5px;color:#3b82f6;background:none;border-radius:6px;display:inline-flex" title="Sửa"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>';
                html += '<button type="button" onclick="printInIframe(\''+BASE+'DashboardKpi/print_compact/'+f.id+'?print=1\')" style="padding:5px;color:#10b981;background:none;border-radius:6px;display:inline-flex" title="In"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg></button>';
            }
            html += '</div></td>';
            html += '</tr>';

            // Expand row
            html += '<tr class="pdg-expand-row hidden bg-slate-50/40"><td colspan="13" class="p-0 border-b border-slate-200"><div class="px-8 py-5 w-full">';
            // Detail cards
            var details = [['Công việc',f.total_task,'#6366f1'],['BCKPH cũ',f.count_bckph_old,'#8b5cf6'],['BCKPH',f.count_bckph,'#a78bfa'],['VP cũ',f.violate_old,'#f59e0b'],['Vi phạm',f.violate,'#ef4444'],['Vượt',f.vuot,'#10b981'],['P1',f.violation_p1,'#f97316'],['P2',f.violation_p2,'#e11d48'],['P3',f.violation_p3,'#dc2626']];
            html += '<div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-10 gap-2 mb-4">';
            details.forEach(function(d){
                html += '<div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-white border border-slate-100 shadow-sm"><div class="w-1 h-5 rounded-full" style="background:'+d[2]+'"></div><div><div class="text-[9px] text-slate-400">'+d[0]+'</div><div class="text-xs font-bold text-slate-800">'+(d[1]||0)+'</div></div></div>';
            });
            // Mở P3
            html += '<div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-white border border-slate-100 shadow-sm"><div class="w-1 h-5 rounded-full bg-teal-500"></div><div><div class="text-[9px] text-slate-400">P3</div><div class="text-xs font-bold '+(f.check_p3=='Có'?'text-green-600':'text-slate-400')+'">'+(f.check_p3||'Không')+'</div></div></div>';
            html += '</div>';
            // Bonus/discipline
            if ((f.kpi_bonus&&f.kpi_bonus.length)||(f.kpi_discipline&&f.kpi_discipline.length)) {
                html += '<div class="mb-3 p-2 rounded-lg bg-white border border-slate-100 text-xs">';
                if (f.kpi_bonus) f.kpi_bonus.forEach(function(v){html+='<div class="text-green-700">🎁 '+PDG.esc(v.name)+'</div>';});
                if (f.kpi_discipline) f.kpi_discipline.forEach(function(v){html+='<div class="text-red-700">⚠ '+PDG.esc(v.name)+'</div>';});
                html += '</div>';
            }
            if (f.note) html += '<div class="mb-3 p-2 rounded-lg bg-amber-50 border border-amber-100 text-xs text-slate-700">'+PDG.esc(f.note)+'</div>';
            // Approval stepper
            if (parseInt(f.id) > 0) {
                html += PDG.renderStepper(f);
            } else {
                html += '<div class="mt-4 text-[11px] font-medium text-orange-500 bg-orange-50 px-3 py-2 rounded-lg border border-orange-100 italic inline-flex items-center gap-2"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Chưa có phiếu đánh giá chính thức cho kỳ này. Dữ liệu đang hiển thị là điểm được kế thừa từ kỳ gần nhất.</div>';
            }
            html += '</div></td></tr>';
        });
        tbody.innerHTML = html;
    },

    renderStepper: function(f) {
        var st = parseInt(f.approval_status||0);
        if (st == -1) {
            var h = '<div class="flex items-center gap-2 mt-2"><span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-700 border border-red-200">Bị từ chối</span>';
            if (!f.audit_id) h += '<button type="button" onclick="openCreateAuditModal('+f.id+','+(f.room_id||0)+')" class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-orange-500 text-white hover:bg-orange-600 shadow-sm">Tạo Audit</button>';
            else h += '<a href="'+PD_BASE.replace('dashboardKpi/','')+'audit_management?open_audit='+f.audit_id+'" target="_blank" class="px-3 py-1 rounded-full text-[11px] font-bold bg-violet-100 text-violet-700 border border-violet-200">'+PDG.esc(f.audit_code)+'</a>';
            return h + '</div>';
        }
        var steps = [
            { name: 'HCNS', by: f.hcns_name, date: f.hcns_approve_date },
            { name: 'KTNB', by: f.ktnb_name, date: f.ktnb_approve_date },
            { name: 'KSRR', by: f.ksrr_name, date: f.ksrr_approve_date },
            { name: 'BOD', by: f.bod_name, date: f.bod_approve_date }
        ];
        var h = '<div class="relative flex justify-between items-start w-full max-w-[640px] mt-4 mb-2">';
        h += '<div class="absolute left-[10%] right-[10%] top-[14px] h-[3px] rounded-full bg-slate-200 z-0"></div>';
        var progressWidth = st===0 ? 0 : (st===1 ? 33.3 : (st===2 ? 66.6 : 100));
        h += '<div class="absolute left-[10%] top-[14px] h-[3px] rounded-full bg-emerald-400 z-0 transition-all duration-500" style="width:calc('+progressWidth+'% * 0.8)"></div>';
        
        steps.forEach(function(s, i){
            var past=st>i, cur=st==i;
            var nameStr = (past && s.by && s.by.trim() !== '') ? PDG.esc(s.by) : '';
            var dateStr = past ? PDG.fmtDateTime(s.date) : '';
            
            h += '<div class="relative z-10 flex flex-col items-center" style="width:120px">';
            if (cur) {
                h += '<div class="flex items-center bg-white px-1.5 gap-2 h-[30px] rounded-full ring-4 ring-blue-100 shadow-sm">';
                h += '<button type="button" onclick="actApprove('+f.id+',\'approved\')" class="w-6 h-6 flex items-center justify-center bg-blue-600 text-white rounded-full hover:bg-blue-700 shadow-sm" title="Duyệt"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></button>';
                h += '<button type="button" onclick="actApprove('+f.id+',\'rejected\')" class="w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-full hover:bg-red-600 shadow-sm" title="Từ chối"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
                h += '</div>';
            } else {
                var bg = past ? 'bg-emerald-500 text-white shadow-md ring-4 ring-emerald-50' : 'bg-white text-slate-300 border-2 border-slate-200';
                h += '<div class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold '+bg+'">';
                h += past ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' : (i+1);
                h += '</div>';
            }
            var lc = cur ? 'text-blue-700 font-bold' : (past ? 'text-emerald-700 font-bold' : 'text-slate-400 font-medium');
            h += '<div class="mt-2 text-center text-[11px] leading-tight '+lc+'">'+(i+1)+'. '+s.name+'</div>';
            
            if (nameStr) {
                h += '<div class="mt-1 text-center text-[10px] text-slate-700 font-semibold leading-tight max-w-[110px] truncate" title="'+nameStr+'">'+nameStr+'</div>';
            }
            if (dateStr) {
                h += '<div class="text-center text-[9px] text-slate-400 font-mono mt-0.5">'+dateStr+'</div>';
            } else if (cur) {
                h += '<div class="mt-1 text-center text-[10px] text-blue-500 font-medium animate-pulse">Đang chờ duyệt</div>';
            }
            
            h += '</div>';
        });
        return h + '</div>';
    },

    renderPagination: function(res) {
        var el = document.getElementById('pdg-pagination');
        var info = document.getElementById('pdg-page-info');
        var tp = res.total_pages || 1;
        var cp = res.page;
        var start = (cp-1)*res.per_page+1;
        var end = Math.min(cp*res.per_page, res.total);
        info.textContent = 'Hiển thị '+start+'-'+end+' / '+res.total+' phiếu';
        if (tp <= 1) { el.innerHTML = ''; return; }
        var h = '';
        var btnCls = 'px-3 py-1.5 text-xs rounded-lg border transition-all font-medium ';
        // Prev
        h += '<button onclick="PDG.load('+(cp-1)+')" '+(cp<=1?'disabled':'')+' class="'+btnCls+(cp<=1?'opacity-30 cursor-not-allowed border-slate-200 text-slate-400':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">‹</button>';
        // Pages
        var pages = [];
        for (var i=1;i<=tp;i++) {
            if (i==1||i==tp||Math.abs(i-cp)<=2) pages.push(i);
            else if (pages[pages.length-1]!=='...') pages.push('...');
        }
        pages.forEach(function(p){
            if (p==='...') { h+='<span class="px-2 text-xs text-slate-400">…</span>'; return; }
            var active = p==cp;
            h += '<button onclick="PDG.load('+p+')" class="'+btnCls+(active?'bg-violet-600 text-white border-violet-600 shadow-sm':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">'+p+'</button>';
        });
        // Next
        h += '<button onclick="PDG.load('+(cp+1)+')" '+(cp>=tp?'disabled':'')+' class="'+btnCls+(cp>=tp?'opacity-30 cursor-not-allowed border-slate-200 text-slate-400':'border-slate-200 text-slate-600 hover:bg-violet-50')+'">›</button>';
        el.innerHTML = h;
    },

    renderStatusTabs: function(counts) {
        var container = document.getElementById('pdg-status-tabs');
        var current = document.getElementById('pdg-status-filter').value;
        var h = '';
        Object.keys(PDG.statusLabels).forEach(function(k){
            var s = PDG.statusLabels[k];
            var cnt = counts[k]||0;
            var active = k===current;
            h += '<button type="button" onclick="setPdgStatusFilter(\''+k+'\',this)" data-stat-status="'+k+'" class="pdg-st-btn px-4 py-2 rounded-lg text-xs font-semibold border transition-all flex items-center gap-2 '+(active?'ring-2 ring-violet-500 ring-offset-1 shadow-sm ':'')+s.css+'">';
            h += s.label+' <span class="pdg-stat-status-count px-1.5 py-0.5 rounded text-[10px] bg-white/60">'+cnt+'</span></button>';
        });
        container.innerHTML = h;
    }
};

function filterPdgTable() {
    clearTimeout(PDG.debounceTimer);
    PDG.debounceTimer = setTimeout(function(){ PDG.page=1; PDG.load(); }, 300);
}

function setPdgStatusFilter(st) {
    document.getElementById('pdg-status-filter').value = st;
    PDG.page = 1;
    PDG.load();
}

function onPdgYearMonthChange() {
    document.getElementById('pdg-ky').value = '';
    // Build ky tabs
    var container = document.getElementById('pdg-ky-tabs');
    if (!container) { PDG.load(1); return; }
    var periods = [{value:'',label:'Tất cả',icon:'layers'}];
    ['Tuần 1','Tuần 2','Tuần 3','Tuần 4'].forEach(function(t){periods.push({value:t,label:t,icon:'calendar'});});
    ['3 tháng','6 tháng','9 tháng','12 tháng'].forEach(function(t){periods.push({value:t,label:'Kỳ '+t,icon:'calendar-check'});});
    var html = '';
    periods.forEach(function(p, idx){
        var active = idx===0;
        var style = active ? 'background:#7c3aed;color:#fff;border-color:#7c3aed;box-shadow:0 1px 3px rgba(124,58,237,0.3);' : 'background:#fff;color:#475569;border-color:#e2e8f0;';
        html += '<button type="button" onclick="selectPdgKyTab(this,\''+p.value+'\')" class="pdg-ky-tab" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;border:1px solid;cursor:pointer;transition:all .15s;white-space:nowrap;'+style+'"><i data-lucide="'+p.icon+'" style="width:15px;height:15px"></i>'+p.label+'</button>';
    });
    container.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
    PDG.load(1);
}

function selectPdgKyTab(btn, value) {
    document.getElementById('pdg-ky').value = value;
    document.querySelectorAll('.pdg-ky-tab').forEach(function(b){b.style.background='#fff';b.style.color='#475569';b.style.borderColor='#e2e8f0';b.style.boxShadow='none';});
    btn.style.background='#7c3aed';btn.style.color='#fff';btn.style.borderColor='#7c3aed';btn.style.boxShadow='0 1px 3px rgba(124,58,237,0.3)';
    PDG.page = 1;
    PDG.load();
}
