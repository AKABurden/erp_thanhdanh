<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php //if ($this->perAddOrders): 
            ?>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/evaluate/add?type='.$_GET['type']) ?>" class="btn btn-info H_action_button tnh-modal">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <?php //endif 
            ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                            id="start_date_search" autocomplete="off" class="start_date_search datepicker form-control"
                            style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                            id="end_date_search" autocomplete="off" class="end_date_search datepicker form-control" style="width: 100%;"
                            value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-evaluate" class="table dt-tnh table-hover table-evaluate" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="orders-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_date_evaluate') ?></th>
                                        <?php if ($_GET['type'] == 'evaluate'){ ?>
                                            <th class="text-center"><?= lang('Nhóm đánh giá') ?></th>
                                            <th class="text-center"><?= lang('tnh_type_evaluate') ?></th>
                                        <?php } elseif ($_GET['type'] == 'educate'){ ?>
                                            <th class="text-center"><?= lang('tnh_type_evaluate') ?></th>
                                        <?php } ?>
                                        <th class="text-center"><?= lang('tnh_code_evaluate') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_evaluate') ?></th>
                                        <th class="text-center"><?= lang('tnh_content_evaluate') ?></th>
                                        <th class="text-center"><?= lang('file') ?></th>
                                        <th class="text-center"><?= lang('status') ?></th>
                                        <th class="text-center"><?= lang('Ngày tái đánh giá') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<input type="hidden" name="type_search" id="type_search" class="form-control" value="<?= $_GET['type'] ?>">

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        type_search: "#type_search",
        start_date_search: "#start_date_search",
        end_date_search: "#end_date_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-evaluate', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/evaluate/getEvaluate') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    // if (table.attr('data-last-order-identifier')) {
                    //     d['last_evaluate_identifier'] = table.attr('data-last-order-identifier');
                    // }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return `<div class="checkbox">
                            <input type="checkbox" name="evaluate[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        </div>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
            ],
        });

        $(document).on('click', '#agree-evaluate', function(event) {
            event.preventDefault();
            index = this;
            evaluate_id = $(this).attr('evaluate_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (evaluate_id) {
                $.ajax({
                        url: site.base_url + 'admin/evaluate/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            evaluate_id: evaluate_id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    })
                    .fail(function(data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $(document).on('change', '#start_date_search, #end_date_search', function(event) {
            oTable.draw();
        });
    });
</script>