<table id="tb-dashboard-timekeeping" class="table dataTable tb-add-timekeeping-new" style="width: 100%;">
    <thead>
        <?= $tHead ?>
    </thead>
    <tbody>
        <?= $html ?>
    </tbody>
    <tfoot></tfoot>
</table>
<ul class="pagination" style="float: right;">
    <?php if(!empty($totalPage)): ?>
    <?php for($i = 1; $i <= $totalPage; $i++) { ?>
    <li class="<?= $page == $i ? 'active' : '' ?>"><a href="javascript:void(0)"
            onclick="addPage(<?= $i  ?>)"><?= $i ?></a></li>
    <?php } ?>
    <?php endif; ?>
</ul>
<script>
function addPage(cPage) {
    $('#page').val(cPage);
    loadDashBoardTimekeeping();
}
var dtHistoryJob;

height_body_vs1 = height_body.replace('px', '');
height_body_vs1 = intVal(height_body_vs1);
height_body_new = height_body_vs1 - 200;
height_body_new = height_body_new+'px';

$(document).ready(function() {
    dtHistoryJob = $('#tb-dashboard-timekeeping').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        fixedColumns: {
            leftColumns: 4,
            rightColumns: 0
        },
        // scrollY: '450px',
        scrollY: height_body_new,
        scrollX: true,
        'searching': false,
        'ordering': false,
        'paging': false,
        "info": false,
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {

        },
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
        "footerCallback": function(row, data, start, end, display) {}
    });
    // $('select.select-custom').selectpicker();
});
</script>