<div class="modal-dialog modal-lg" style="width: 100%; max-width: 70vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="closeModal('chModal_dashboard')" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="type" id="type" value="<?= $type ?>">
            <table class="table table_ksnb_nang_suat dataTable" id="table_ksnb_nang_suat">
                <thead>
                <tr>
                    <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Ngày')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Mã')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Mã SP')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Tên SP')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Năng suất')); ?></th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th colspan="5" class="text-right"><?= ucwords(_l('Tổng năng suất')); ?></th>
                    <th class="text-center total_nang_suat" style="text-align: right"></th>
                  </tr>
                </tfoot>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('chModal_dashboard')">Đóng</button>
        </div>
    </div>
</div>

<script>
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    }
    $(document).ready(function() {
        var fnserverparams = {
            'type': '#type',
        };
        oTable = initDataTable_ch('.table_ksnb_nang_suat', '<?= base_url('dashboard_srceen_office/getDetailModalKsnbNangSuat?csrf_protection=true') ?>', [0], [0], fnserverparams, [0, 'desc']);

        $(".table_ksnb_nang_suat").on('draw.dt', function() {
            var total_nang_suat = 0;
            $('.table_ksnb_nang_suat tbody tr').each(function() {
                var nang_suat = intVal($(this).find('td').eq(5).find('div').text());
                if (!isNaN(nang_suat)) {
                    total_nang_suat += nang_suat;
                }
            });
            $('.total_nang_suat').text(formatNumber(total_nang_suat));
        });
    });
</script>