<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<?php echo form_open('admin/manufactures/detail_productions_orders', array('id' => 'detail-productions-orders')); ?>
<style>
    #table-detail-productions-orders_wrapper .buttons-collection {
        display: none;
    }

    #table-detail-productions-orders tr td {
        vertical-align: top !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body p0">
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('date') ?>: </div>
                                    <div class="ml-at t-bold"><?= _d($productions_orders['date']) ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('orders') ?>: </div>
                                    <div class="ml-at t-bold" style="position: relative;">
                                        <?php $ctOrders = count($orders); ?>
                                        <?php if (!empty($orders)) : ?>
                                            <?php foreach ($orders as $key => $value) : ?>
                                                <?php
                                                if ($key == 3) {
                                                    echo '<a class="accordion-toggle collapsed" data-toggle="collapse" style="position: absolute;
                                                        top: 0px; right: -15px;" href="#collapseOrders" role="button" aria-controls="collapseOrders"></a>';
                                                    echo '<div id="collapseOrders" class="collapse">';
                                                }
                                                ?>
                                                <span><?= $value['reference_no'] ?>(<?= $value['company'] ?>)</span></br>
                                                <?php
                                                if ($ctOrders - 1 == $key && $key > 2) {
                                                    echo '</div>';
                                                }
                                                ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_branch') ?>: </div>
                                    <div class="ml-at t-bold"><?= $branch['name'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_reference_productions_orders') ?>: </div>
                                    <div class="ml-at t-bold"><?= $productions_orders['reference_no'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('business_plan') ?>: </div>
                                    <div class="ml-at t-bold" style="position: relative;">
                                        <?php $ctBusinessPlan = count($business_plan); ?>
                                        <?php if (!empty($business_plan)) : ?>
                                            <?php foreach ($business_plan as $key => $value) : ?>
                                                <?php
                                                if ($key == 3) {
                                                    echo '<a class="accordion-toggle collapsed" data-toggle="collapse" style="position: absolute;
                                                        top: 0px; right: -15px;" href="#collapseBusinessPlan" role="button" aria-controls="collapseBusinessPlan"></a>';
                                                    echo '<div id="collapseBusinessPlan" class="collapse">';
                                                }
                                                ?>
                                                <span><?= $value['reference_no'] ?></span></br>
                                                <?php
                                                if ($ctBusinessPlan - 1 == $key && $key > 2) {
                                                    echo '</div>';
                                                }
                                                ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('productions_plan_acronym') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php
                                            $this->db->select('
                                                GROUP_CONCAT(distinct tbl_productions_plan.reference_no) as reference_plan
                                            ', false);
                                            $this->db->from('tbl_productions_orders_items');
                                            $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_orders_items.plan_id ');
                                            $this->db->where('tbl_productions_orders_items.productions_orders_id', $id);
                                            $productions_plan = $this->db->get()->row_array();
                                            echo $productions_plan['reference_plan'];
                                        ?>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_created_by') ?>: </div>
                                    <div class="ml-at t-bold"><?= $created_by ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="flex pull-right hide" style="position: relative;">
                                    <?php if($productions_orders['status_orders']): ?>
                                        <span style="cursor: default;" class="btn-danger-cs btn-sm-custom pull-right uppercase">
                                            <span class="fa fa-remove"></span>
                                            <?= lang('tnh_end_production') ?>
                                        </span>
                                    <?php else: ?>
                                        <span onclick="showPannel(this)" class="btn-primary-cs btn-sm-custom pull-right uppercase">
                                            <span class="fa fa-check"></span>
                                            <?= lang('tnh_finished_multiple') ?>
                                        </span>
                                    <?php endif; ?>
                                    <ul class="dropdown-menu dropdown-menu-right-cs animated fadeIn hide" style="position: absolute; display: none; border-radius: 0px; width: 800px; max-width: 800px;" id="custom-pannel">
                                        <?php $this->load->view('admin/manufactures/view_finished') ?>
                                    </ul>
                                </div>
                                <div class="mtop20 flex">
                                    <?php
                                    $barcode = $productions_orders['reference_no'];
                                    ?>
                                    <div style="margin: auto;"><img src="<?= base_url('admin/products/gen_barcode/' . $barcode) ?>"></div>
                                </div>
                                <div class="row-contro">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body p0-l-r">
                        <div class="col-md-12">
                            <table id="table-detail-productions-orders" class="table dataTable">
                                <thead>
                                    <tr class="hide">
                                        <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center" colspan="3"><?= lang('items') ?></th>
                                        <!-- <th class="text-center"><?= lang('items') ?></th> -->
                                        <!-- <th class="text-center"><?= lang('quantity') ?></th> -->
                                        <th class="text-center"><?= lang('tnh_process') ?></th>
                                        <th class="text-center"><?= lang('comment_string') ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="productions_orders_id" id="productions_orders_id" class="form-control" value="<?= $id ?>">
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script>
    var fnserverparamsDetail = {
        productions_orders_id: '#productions_orders_id'
    };
    var oTableDetail = '';

    function showHideObject(_this, c_object_id) {
        var tr = $(_this).closest('tr');
        var row = oTableDetail.row(tr);
        return;

        isShown = tr.find('.fa-caret-down');
        if (isShown.length > 0) {
            $(_this).removeClass('fa-caret-down');
            $(_this).addClass('fa-caret-right');
        } else {
            $(_this).addClass('fa-caret-down');
            $(_this).removeClass('fa-caret-right');
        }
    }

    function showHideProcess(_this, c_productions_orders_items_id) {
        var li = $('.coll-' + c_productions_orders_items_id);
        console.log(li);
        if ($(_this).hasClass('collapsed')) {
            // li.hide();
            li.slideUp();
        } else {
            // li.show();
            li.show("slow");
        }
    }

    $(document).ready(function() {
        oTableDetail = tnhInitDataTable('#table-detail-productions-orders', '<?= site_url('admin/manufactures/getDetailProductionsOrders') ?>', {
            'searching': false,
            'ordering': false,
            // 'fixedHeader': {
            //     header: true,
            // },
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getDetailProductionsOrders') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsDetail) {
                        d[key] = $(fnserverparamsDetail[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 0,
                    'width': '50px',
                },
                {
                    "targets": 1,
                    'width': '80px'
                },
                {
                    "targets": 2,
                    'width': '150px'
                },
                {
                    "targets": 3,
                    'width': '80px'
                },
            ],
            "createdRow": function(row, data, index) {
                $(row).attr('data-title', data.Data_Title);
                $(row).attr('data-toggle', data.Data_Toggle);

                if (data[1] === 'group') {
                    $('td:eq(1)', row).attr('colspan', 5);
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    this.api().cell($('td:eq(1)', row)).data(data[2]);
                    $(row).addClass('bg-group bold');
                } else if (data[1] === 'child') {
                    $('td:not(.row-child):eq(0)', row).attr('colspan', 6);
                    $('td:not(.row-child):eq(1)', row).css('display', 'none');
                    $('td:not(.row-child):eq(2)', row).css('display', 'none');
                    $('td:not(.row-child):eq(3)', row).css('display', 'none');
                    $('td:not(.row-child):eq(4)', row).css('display', 'none');
                    $('td:not(.row-child):eq(5)', row).css('display', 'none');
                    this.api().cell($('td:not(.row-child):eq(0)', row)).data(data[0]);
                }
                $(row).addClass('shown');
            },
        });
    });
</script>

<script>
    function view_feedback(id = '', _this) {
        $(_this).parent('.eventP').html('<a onclick="hide_feedback(' + id + ', this)">Ẩn bớt</a>');
        $('.rehide_' + id + ':not(.num)').removeClass('hide');
    }

    function hide_feedback(id = '', _this) {
        $(_this).parent('.eventP:not(.num)').html('<a onclick="view_feedback(' + id + ', this)">Xem thêm</a>');
        $('.rehide_' + id + ':not(.num)').addClass('hide');
    }

    function showPannel(_this) {
        // custom-pannel
        if ($('#custom-pannel').hasClass('collapsed')) {
            $('#custom-pannel').removeClass('collapsed');
            $('#custom-pannel').slideUp();
        } else {
            $('#custom-pannel').addClass('collapsed');
            $('#custom-pannel').show("slow");
        }
    }
</script>