<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_creted') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Lý do ra vào cổng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note_in_out'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số điện thoại liên hệ') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['phone'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Nhân viên') ?>: </div>
                            <div class="ml-at t-bold"><?= staff_profile_image($dtData['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                            'data-toggle' => 'tooltip',
                                                            'data-title' => get_staff_full_name($dtData['id_staff'])
                                                        )) . get_staff_full_name($dtData['id_staff']); ?>
                            </div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Vị trí') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['name_roles']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chức vụ') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['name_departments']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian ra cổng') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtData['time_out']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian vào cổng') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtData['time_in']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="wap-content mtop20">
                        <table class="table dataTable table-hau">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="text-center">STT</th>
                                    <th class="text-center" style="width: 100px"><?= lang('Chứng từ ra vào cổng') ?></th>
                                    <th class="text-center" style="width: 250px"><?= lang('Danh Mục Hàng Hóa Ra Cổng') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Bảo Vệ Xác Nhận') ?></th>
                                    <th class="text-center" style="width: 60px">Đạt</th>
                                    <th class="text-center" style="width: 80px">Không đạt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Pre-fetch Production Report Data
                                $CI = &get_instance();
                                $itemIds = [];
                                if (!empty($dtItems)) {
                                    $itemIds = array_column($dtItems, 'id');
                                }
                                
                                $reportsMap = [];
                                $uncompletedMap = [];
                                
                                if (!empty($itemIds)) {
                                    // 1. Fetch Reports
                                    $CI->db->where_in('in_and_out_of_work_item', $itemIds);
                                    $reports = $CI->db->get('tblproduction_report')->result();
                                    
                                    $reportIds = [];
                                    foreach ($reports as $r) {
                                        $reportsMap[$r->in_and_out_of_work_item] = $r;
                                        $reportIds[] = $r->id;
                                    }
                                    
                                    // 2. Fetch Completion Status
                                    if (!empty($reportIds)) {
                                        $CI->db->select('production_report_id, COUNT(id) as count');
                                        $CI->db->where('staff_process', 0);
                                        $CI->db->where_in('production_report_id', $reportIds);
                                        $CI->db->group_by('production_report_id');
                                        $procs = $CI->db->get('tbl_process_production_report')->result();
                                        foreach ($procs as $p) {
                                            $uncompletedMap[$p->production_report_id] = $p->count;
                                        }
                                    }
                                }
                                ?>
                                <?php if (!empty($dtItems)) { ?>
                                    <?php foreach ($dtItems as $key => $value) { 
                                        $report = isset($reportsMap[$value['id']]) ? $reportsMap[$value['id']] : null;
                                        $hasReport = $report ? 1 : 0;
                                        $reportObjId = $report ? $report->id : '';
                                        $reportRef = $report ? $report->reference_no : '';
                                        $isCompleted = 1; 
                                        if ($report) {
                                            if (isset($uncompletedMap[$report->id]) && $uncompletedMap[$report->id] > 0) {
                                                $isCompleted = 0;
                                            }
                                        }
                                        // Generate Link URL
                                        $reportUrl = $report ? admin_url('production_report/modal/' . $report->id) : '';
                                    ?>
                                        <tr class="<?= (!empty($value['status']) && $value['status']=='no') ? 'has-no-status' : '' ?>"
                                            data-id="<?= $value['id'] ?>"
                                            data-status="<?= !empty($value['status']) ? $value['status'] : '' ?>"
                                            data-has-report="<?= $hasReport ?>"
                                            data-report-id="<?= $reportObjId ?>"
                                            data-report-ref="<?= htmlspecialchars($reportRef) ?>"
                                            data-report-completed="<?= $isCompleted ?>"
                                            data-report-url="<?= $reportUrl ?>">
                                            <td class="text-center"><?= (++$key) ?></td>
                                            <td><?= $value['detail_reference_no'] ?></td>
                                            <td><?= $value['detail_items'] ?></td>
                                            <td><?= $value['detail_security'] ?></td>
                                            <td class="text-center">
                                                <button type="button" class="audit-status-btn btn-yes <?= (!empty($value['status']) && $value['status']=='yes') ? 'active' : '' ?>" data-index="<?= $key-1 ?>" data-status="yes">Đạt</button>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="audit-status-btn btn-no <?= (!empty($value['status']) && $value['status']=='no') ? 'active' : '' ?>" data-index="<?= $key-1 ?>" data-status="no">Không đạt</button>
                                                <span class="report-action"></span>
                                            </td>
                                        </tr>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtData['staff_create']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_create']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['staff_update']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($dtData['staff_update']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($dtData['date_update']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<style>
.audit-status-btn{padding:6px 12px;border:1px solid #e0e0e0;background:#fff;color:#333;font-weight:700;border-radius:6px;cursor:pointer}
.audit-status-btn.btn-yes.active{background:#10b981;color:#fff;border-color:#059669}
.audit-status-btn.btn-no.active{background:#ef4444;color:#fff;border-color:#dc2626}
tr.has-no-status{background:#fff5f5}
.btn-create-report {padding:6px 12px;border:1px solid #f59e42;background:#fbbf24;color:#fff;font-weight:700;border-radius:6px;cursor:pointer;transition:background 0.2s,border 0.2s;}
.btn-create-report:hover {background:#f59e42;border-color:#d97706;color:#fff;}
</style>
<script type="text/javascript">
function updateRowAccess() {
    var $rows = $('.table-hau tbody tr');
    var foundCurrent = false;
    var currentIndex = -1;

    $rows.each(function(idx){
        var $row = $(this);
        var status = $row.data('status');
        var id = $row.attr('data-id');
        
        // Report data
        var hasReport = $row.attr('data-has-report') == '1';
        var reportCompleted = $row.attr('data-report-completed') == '1';
        var reportRef = $row.attr('data-report-ref');
        var reportUrl = $row.attr('data-report-url');
        
        var $reportAction = $row.find('.report-action');
        $reportAction.html('');

        // Always enable buttons (user request)
        $row.find('.btn-yes, .btn-no').prop('disabled', false).removeAttr('disabled');

        if (!foundCurrent) {
            if (!status || status=='') {
                // This is the current step to act on
                currentIndex = idx;
                foundCurrent = true;
            } else if (status=='no') {
                if (!hasReport) {
                    // Status NO, no report -> Current step (create report)
                    currentIndex = idx;
                    $reportAction.html('<div><br><a href="<?= admin_url('production_report/detail?in_and_out_of_work_item=') ?>'+id+'&in_and_out_of_work=<?= $dtData['id'] ?>" class="btn btn-info btn-icon mbot10 create-report-btn" target="_blank" data-item-id="'+id+'">Tạo phiếu báo cáo</a><div>');
                    foundCurrent = true;
                    // Note: User can still change Yes/No here if they want, but logic implies they should fix this step. 
                    // If they click Yes/No on this step, it's valid.
                } else {
                    // Has report
                    var badge = reportCompleted ? 
                        '<span class="label label-success mleft5">Hoàn thành</span>' : 
                        '<span class="label label-warning mleft5">Chưa hoàn thành</span>';
                    
                    $reportAction.html('<div class="mtop5"><a href="'+reportUrl+'" class="c_modal btn-xs">' + reportRef + '</a>' + badge + '</div>');

                    if (!reportCompleted) {
                        // Report incomplete -> Block here
                        currentIndex = idx;
                        foundCurrent = true;
                    }
                }
            }
        } else {
            // Logic for future steps (already handled by foundCurrent check for identifying valid index)
            // Just show report link if it somehow exists (legacy data?)
            if (status=='no' && hasReport) {
                var badge = reportCompleted ? 
                        '<span class="label label-success mleft5">Hoàn thành</span>' : 
                        '<span class="label label-warning mleft5">Chưa hoàn thành</span>';
                $reportAction.html('<div class="mtop5"><a href="'+reportUrl+'" class="c_modal btn-xs">' + reportRef + '</a>' + badge + '</div>');
            }
        }
    });

    // Store the valid index on the table for the click handler
    if (!foundCurrent) {
        // All completed?
        currentIndex = $rows.length; // Or logic for finished
    }
    $('.table-hau').data('current-action-index', currentIndex);
}

$(document).off('click', '.audit-status-btn').on('click', '.audit-status-btn', function(e){
    e.preventDefault();
    var $btn = $(this);
    var $row = $btn.closest('tr');
    var rowIndex = $row.index();
    var validIndex = $('.table-hau').data('current-action-index');

    // Allow re-clicking completed items? Usually Audit logic allows editing previous items?
    // User request: "không được nhảy bước hay lùi bước" -> "don't jump steps or step back".
    // This implies EXACTLY following the order.
    // If rowIndex < validIndex: It's a previous step. User said "don't step back".
    // If rowIndex > validIndex: It's a future step. User said "don't jump steps".
    // So only rowIndex == validIndex is allowed?
    
    // HOWEVER, typically you can edit previous answers. 
    // "không được nhảy bước hay lùi bước" might mean "Sequential order must be respected".
    // If I change a previous NO to YES, that might invalidate subsequent logic, so maybe blocking back-stepping is desired here?
    // Let's assume strict sequential: Can only edit the CURRENT actionable item.
    
    // Wait, if I finished item 1 (Yes), item 2 is current.
    // If I want to change item 1 to No, that should probably be allowed? 
    // "Lùi bước" usually means "Don't go back". 
    // Let's implement strict check based on usage: 
    // If user says "don't jump steps or step back", I will strictly block anything != validIndex.
    
    // Exception: If validIndex is -1 (all done), maybe allow?
    // But usually foundCurrent logic handles unfinished. 
    
    if (rowIndex !== validIndex) {
        if (rowIndex > validIndex) {
            alert_float('warning', 'Bạn vui lòng thực hiện theo thứ tự!');
        } else {
            alert_float('warning', 'Bạn không thể thay đổi bước đã hoàn thành!'); 
        }
        return;
    }

    var status = $btn.data('status');
    var id = $row.data('id');
    $.ajax({
        url: '<?= admin_url('in_and_out_of_work/update_detail_status?csrf_protection=true') ?>',
        type: 'POST',
        data: {id: id, status: status},
        dataType: 'json',
        success: function(res){
            if(res.result){
                $row.data('status', status); // Update data in DOM
                $row.attr('data-status', status); // Ensure attr is updated for selectors
                
                $row.find('.audit-status-btn').removeClass('active');
                $btn.addClass('active');
                if(status==='no'){
                    $row.addClass('has-no-status');
                } else {
                    $row.removeClass('has-no-status');
                }
                updateRowAccess();
            }else{
                alert(res.message||'Lỗi cập nhật trạng thái!');
            }
        },
        error: function(){alert('Lỗi kết nối!');}
    });
});

$(document).off('click', '.btn-create-report').on('click', '.btn-create-report', function(){
   // ...
});

$(function(){ updateRowAccess();
    setTimeout(function(){
        console.log("Chạy updateRowAccess lần 2 (delayed)...");
        updateRowAccess();
    }, 300);
 });
</script>