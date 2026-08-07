<table id="tb-payroll-salary" class="table dataTable tb-payroll-salary-new" style="width: 100%;">
    <thead>
    <?= $tHead ?>
    </thead>
    <tbody>
    <?= $html ?>
    </tbody>
    <tfoot>
    <tr>
    </tr>
    </tfoot>
</table>
<script>
    var dtHistoryJob;
    $(document).ready(function() {
        dtHistoryJob = $('#tb-payroll-salary').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            fixedColumns: {
                leftColumns: 3,
                rightColumns: 0
            },
            scrollY: '430px',
            scrollX: true,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            "drawCallback": function(aoData, settings) {
            },
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {

            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                totalSalary();
                mainWrapperHeightFix();
            },
            "footerCallback": function(nRow, aaData, start, end, display) {

            }
        });

    });

    $(".allowance_other_new").change(function() {
        totalSalary();
    });

    function totalSalary() {
        tb = '#tb-payroll-salary tbody tr';
        var n = $(tb).length;
        count_error = 0;


        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];

            staffid = $(element).find('.allowance_other_new').attr('data-staff-id');

        }


    }
</script>