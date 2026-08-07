<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/categories_other/handlingRelate/0/' . $status) ?>" class="btn btn-info H_action_button tnh-modal">
                    <?php echo _l('add'); ?>
                </a>
                <a href="<?= base_url('admin/categories_other/export_excel') ?>" class="btn btn-info pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('EXPORT EXCEL'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-relate" class="table table-hover table-relate dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="relate"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"><?= lang('Mã liên quan') ?></th>
                                        <th class="text-center"><?= lang('Tên liên quan') ?></th>
                                        <th class="text-center"><?= lang('items') ?></th>
                                        <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
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
<input type="hidden" name="status_search" id="status_search" class="form-control" value="<?= $status ?>">

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_search: "#status_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-relate', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/categories_other/getRelate') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                },
                {
                    "targets": [3],
                    "searchable": false,
                    "orderable": false,
                }, {
                    "targets": 5,
                    'sortable': false,
                    'searchable': false,
                    'visible': false,
                }
            ],
        });
    });
    $('#table-relate tbody').on('click', 'td .rows-child', function() {
        var id = $(this).data('id');
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {

            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadSubItems(row.data(), id)).show();
            tr.addClass('shown');
            $(`.table.table-child-one-${id}`).DataTable({
                filter: false,
                deferRender: false,
                scroller: false,
                order : false,
                searching : false,
                paging : false,
                info : false,
                "columnDefs": []
            });
            console.log($(`.table.table-child-one-${id}`))
            $(`.table.table-child-one-${id}`).DataTable().column(3).visible( false );
            $('.table-loading').removeClass('table-loading');
        }
    });

    function loadSubItems(cData, id = 0) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        items = cData[5];
        cHtml = '';
        
        // <td class="bold text-center"><?= lang('STT') ?></td>
        tr1 = `<tr class="success">
            <th class="bold text-center"><?= lang('Mã liên quan') ?></td>
            <th class="bold text-center"><?= lang('Tên liên quan') ?></td>
            <th class="bold text-center" style="width: 100px;"><?= lang('actions') ?></td>
            <th class="bold text-center"></td>
        </tr>`;
        tablechild = `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">
            <table class="table table-child-one-${id} table-bordered dataTable" style="width: 95% !important; float: right;">
                <thead>${tr1}</thead>
                <tbody>
                    ${items}
                </tbody>
            </table>
        </div>`;
        return tablechild;
    }
</script>