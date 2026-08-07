<table id="tb-add-timekeeping" class="table dataTable tb-add-timekeeping-new" style="width: 100%;">
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
var dtHistoryJob;

function addPage(cPage) {
    $('#page').val(cPage);
    loadPersonnelTimekeeping();
}
height_body_vs1 = height_body.replace('px', '');
height_body_vs1 = intVal(height_body_vs1);
height_body_new = height_body_vs1 - 100;
height_body_new = height_body_new+'px';

$(document).ready(function() {
    dtHistoryJob = $('#tb-add-timekeeping').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        fixedColumns: {
            leftColumns: 5,
            rightColumns: 0
        },
        // 'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
        // buttons: [{
        //     text: 'Excel',
        //     extend: 'excelHtml5',
        //     exportOptions: {
        //         columns: ':visible'
        //     },
        // }, ],
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
        rowsGroup: [
            0, 1,2,3
        ],
        "footerCallback": function(row, data, start, end, display) {}
    });
    // $('select.select-custom').selectpicker();
});
</script>