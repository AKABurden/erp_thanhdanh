<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg" style="width:50%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <input type="hidden" name="staff_id_table" id="staff_id_table" class="staff_id_table" value="<?= $staff_id ?>">
                    <input type="hidden" name="month_table" id="month_table" class="month_table" value="<?= $month ?>">
                    <input type="hidden" name="year_table" id="year_table" class=year_table value="<?= $year ?>">
                    <input type="hidden" name="precious_table" id="precious_table" class=precious_table value="<?= $precious ?>">
                    <div class="table-responsive">
                        <table id="table_detail_production_report" class="table table-hover dataTable dont-responsive-table" style="width: 100%;">
                            <thead>
                            <tr style="">
                                <th style="width: 30px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                <th style="width: 200px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Ngày') ?></th>
                                <th style="background-color: #D9F5D6 !important;" class="text-center"><?= lang('Số Phiếu') ?></th>
                                <th style="width: 200px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Tên Phiếu') ?></th>
                                <th style="width: 100px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Mục tiêu') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var oTable;
    var fnserverparams  = {
        'staff_id': '#staff_id_table',
        'month': '#month_table',
        'year': '#year_table',
        'precious': '#precious_table',
    };
    oTable = tnhInitDataTable('#table_detail_production_report', '<?= site_url('admin/kpi/getDetailProductionReportVuot') ?>', {
        'order': [
            [0, 'desc']
        ],
        "ajax": {
            "url": '<?= site_url('admin/kpi/getDetailProductionReportVuot') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                return json.aaData;
            }
        },
        "columnDefs": [],
    });
</script>
