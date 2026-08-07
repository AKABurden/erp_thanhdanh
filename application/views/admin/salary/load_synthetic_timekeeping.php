<style>
#tb-synthetic-timekeeping>thead>tr:nth-child(3)>th {
    border-bottom: unset !important;
    border-top: unset !important;
}

#tb-synthetic-timekeeping>thead>tr:nth-child(4)>th {
    border-bottom: unset !important;
    border-top: unset !important;
}
</style>

<table id="tb-synthetic-timekeeping" class="table dataTable" style="width: 100%;">
    <thead>
        <?= $tHead ?>
    </thead>
    <tbody>
        <?= $html ?>
    </tbody>
    <tfoot></tfoot>
</table>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script>
var dtHistoryJob;
$(document).ready(function() {
    dtHistoryJob = $('#tb-synthetic-timekeeping').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        fixedColumns: {
            leftColumns: 3,
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
        scrollY: '450px',
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
    dtHistoryJob.draw('page');
});
</script>