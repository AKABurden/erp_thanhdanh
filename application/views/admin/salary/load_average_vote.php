<style>
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
        // fixedColumns: {
        //     leftColumns: 0,
        //     rightColumns: 0
        // },
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