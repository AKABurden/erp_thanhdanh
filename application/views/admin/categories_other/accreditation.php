<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
                <?php if(has_permission('accreditation', '', 'create')) {?>
                    <a href="<?= base_url('admin/categories_other/handlingaccreditation') ?>" class="btn btn-info H_action_button c_modal">
                        <?php echo _l('add'); ?>
                    </a>
                    <a href="<?= base_url('admin/categories_other/modal_import_accreditation') ?>" class="btn btn-info H_action_button c_modal">
                        <?php echo _l('Import'); ?>
                    </a>
                <?php } ?>
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
                            <table id="table-accreditation" class="table table-hover table-accreditation dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="accreditation"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('Mã khu vực Test/Kiểm định') ?></th>
                                        <th class="text-center"><?= lang('Tên khu vực Test/Kiểm định') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                                        <th class="text-center"><?= lang('Chi tiết danh mục kiểm tra') ?></th>
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
        var filterList = {
            'datestart' : '[name="date_start"]',
        };
        $(function(){
            oTable = initDataTable('#table-accreditation', admin_url + '/categories_other/getaccreditation', [0], [0], filterList, [0, 'desc']);
        });
    
        $.each(filterList, function(i, filter){
            $(filter).on('change', function(e){
                if($('#table-accreditation').hasClass('dataTable')) {
                    $('#table-accreditation').DataTable().ajax.reload();
                }
            })
        })
        
        //oTable = tnhInitDataTable('#table-accreditation', '', {
        //    'order': [
        //        [1, 'desc']
        //    ],
        //    'fixedHeader': {
        //        header: true,
        //    },
        //    'responsive': true,
        //    "ajax": {
        //        "url": '<?//= site_url('admin/categories_other/getaccreditation') ?>//',
        //        "type": "POST",
        //        "data": function(d) {
        //            if (typeof(csrfData) !== 'undefined') {
        //                d[csrfData['token_name']] = csrfData['hash'];
        //            }
        //            for (var key in fnserverparams) {
        //                d[key] = $(fnserverparams[key]).val();
        //            }
        //        },
        //        "dataSrc": function(json) {
        //            return json.aaData;
        //        }
        //    },
        //    "columnDefs": [
        //        {
        //            "targets": [0],
        //            "orderable": false,
        //        },
        //        {
        //            "targets": [3],
        //            "searchable": false,
        //            "orderable": false,
        //        }
        //    ],
        //});
    });
</script>