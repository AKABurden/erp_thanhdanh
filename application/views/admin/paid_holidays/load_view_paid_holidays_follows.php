<table id="table-paid-holiday-follow" class="table dataTable" style="width: 100%;">
    <thead>
    <?= $tHead ?>
    </thead>
    <tbody>
    <?= $html ?>
    </tbody>
    <tfoot><?= $tfoot ?></tfoot>
</table>
<script>
    var dtHistoryJob;

    $(document).ready(function () {

        dtHistoryJob = $('#table-paid-holiday-follow').DataTable({
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [{
                    text: 'Excel',
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
            }, ],
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {

            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function (row, data, start, end, display) {
            }
        });
    });
</script>