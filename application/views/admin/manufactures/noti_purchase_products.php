<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_warning_warehouses') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <span><?= lang('Để bỏ hoàn thành tại công đoạn '.$stage['stage_name'].' cần đảm bảo đủ kho BTP và TP chưa hoàn thiện để giảm lại kho') ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-noti-pp" class="table table-hover dataTable">
                        <thead>
                            <tr>
                                <th class="text-center"><?= lang('image') ?></th>
                                <th class="text-center"><?= lang('name') ?> - <?= lang('code') ?></th>
                                <th class="text-center"><?= lang('tnh_warehouses') ?></th>
                                <th class="text-center"><?= lang('tnh_quantity_missing') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td></td>
                                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
			</div>
		</div>
		<div class="modal-footer">
            <input type="hidden" id="pod_id_pp" class="form-control" value="<?= $pod_id ?>">
            <input type="hidden" id="pois_id_pp" class="form-control" value="<?= $pois_id ?>">
            <input type="hidden" id="cqi_id_pp" class="form-control" value="<?= $cqi_id ?>">
            <input type="hidden" id="stage_name" class="form-control" value="<?= $stage['stage_name'] ?>">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script>
    var oTableNotiPP = '';
    var fnserverparamsPP = {
        pod_id_pp: '#pod_id_pp',
        pois_id_pp: '#pois_id_pp',
        cqi_id_pp: '#cqi_id_pp',
        stage_name: '#stage_name',
    };

    $(document).ready(function () {
        oTableNotiPP = tnhInitDataTable('#tb-noti-pp', '<?= site_url('admin/manufactures/getNotiPurchaseProducts') ?>', {
            'order': [
                [1, 'asc']
            ],
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getNotiPurchaseProducts') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPP) {
                        d[key] = $(fnserverparamsPP[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    $('#tb-noti-pp tfoot tr td:nth-child(4)').html(`<div class="text-center">${tnhFormatNumber(json.quantity_export)}</div>`);
                    return json.aaData;
                }
            },
        });
    });
</script>
