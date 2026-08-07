<?php

$warehouses = $this->stock_model->getWarehouses(false, []);
$stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', 'name, type, status_qc');

$tbPOStages = "(
    SELECT
        tbl_productions_orders_items_stages.id as pois_id,
        tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
        tbl_productions_orders_items_stages.number as number,
        tbl_productions_orders_items_stages.active
    FROM tbl_productions_orders_items_stages
    WHERE tbl_productions_orders_items_stages.productions_orders_id = '$productions_orders_id' AND tbl_productions_orders_items_stages.stage_id = '$stage_id' AND tbl_productions_orders_items_stages.active = 0
) tb_po_stages";

$this->db->select('
        tbl_productions_orders_details.id as pod_id,
        tbl_productions_orders_items.id as poi_id,
        tb_po_stages.pois_id as pois_id,
        tbl_products.images as images,
        tbl_products.code as item_code,
        tbl_products.name as item_name,
        tbl_productions_orders_items.quantity as quantity,
        tblsize.name as size_name,
        tb_po_stages.number as number,
        tbl_productions_orders_details.object_type as object_type,
        tbl_productions_orders_details.object_id as object_id,
    ');
$this->db->from('tbl_productions_orders_details');
$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
$this->db->join($tbPOStages, 'tb_po_stages.poi_id = tbl_productions_orders_items.id');
$this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
$this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
$this->db->order_by('tbl_productions_orders_details.object_id ASC');
$productions_orders_details = $this->db->get()->result_array();

if (!empty($productions_orders_details)) {
    $this->db->select('count(tbl_productions_orders_items_stages.id) as ct', false);
    $this->db->from('tbl_productions_orders_items_stages');
    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_details[0]['poi_id']);
    $this->db->where('tbl_productions_orders_items_stages.number <', $productions_orders_details[0]['number']);
    $this->db->where_in('tbl_stages.type', [2]);
    $ct = $this->db->get()->row_array()['ct'];
    if ($ct) {
        $stage['type'] = 3;
    }
}

// qc return
$this->db->select('
    tbl_productions_orders_details.id as pod_id,
    tbl_productions_orders_items.id as poi_id,
    tbl_check_quality_items_stage.pois_id as pois_id,
    tbl_products.images as images,
    tbl_products.code as item_code,
    tbl_products.name as item_name,
    tbl_check_quality_items.quantity_recycling as quantity,
    tblsize.name as size_name,
    tbl_check_quality_items_stage.number as number,
    tbl_check_quality_items_stage.type as type,
    tbl_check_quality_items_stage.id as cqis_id,
    tbl_productions_orders_details.object_type as object_type,
    tbl_productions_orders_details.object_id as object_id,
', false);
$this->db->from('tbl_check_quality_items_stage');
$this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_check_quality_items_stage.pod_id');
$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
$this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
$this->db->where('tbl_check_quality_items_stage.stage_id', $stage_id);
$this->db->where('tbl_check_quality_items_stage.po_id', $productions_orders_id);
$this->db->where('tbl_check_quality_items_stage.active', 0);
$this->db->order_by('tbl_productions_orders_details.object_id ASC');
// print_arrays($this->db->get_compiled_select());
$qcItems = $this->db->get()->result_array();
if (!empty($qcItems) && empty($productions_orders_details)) {
    // $stage['type'] = $qcItems[0]['type'];
    $stage['type'] = 0;
}

$arrObject = [];
?>
<!-- panel-body p-0 -->
<div class="panel panel-default ac-stages" style="height: 430px; position: relative;">
    <div class="" style="margin-bottom: 0; padding: 7px;">
        <div class="row" style="height: 390px; overflow: auto;">
            <div class="col-md-12">
                <div class="<?= $stage['type'] == 1 ? 'hide' : '' ?>">
                <?php if ($stage['type'] == 1 || $stage['type'] == 2 || $stage['type'] == 3) : ?>
                    <?php
                        $titleWarehouse = $stage['type'] == 1 ? lang('tnh_warehouses_semi_product') : lang('tnh_warehouses_products');
                    ?>
                    <!-- <div class="bold" style="float: left;"><?= ''//lang('tnh_chonse_warehouse_tp_btp') ?></div> -->
                    <div class="bold" style="float: left;"><?= lang('Chọn kho thành phẩm') ?></div>
                    <div class="form-group pull-right">
                        <select name="warehouse_import" id="warehouse_import" data-placeholder="<?= $titleWarehouse ?>" class="modal-select2" style="width: 180px;">
                            <option value=""></option>
                            <?php if (!empty($warehouses)) : ?>
                                <?php foreach ($warehouses as $key => $value) : ?>
                                    <option <?= ($stage['type'] == 1 && WAREHOUSES_CAPACITY == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?>
                </div>
                <br>
                <br>
                <div class="show-warehouses-errors">

                </div>
            </div>
            <div class="col-md-12 mtop5">
                <div class="bold"><?= lang('items') ?></div>
            </div>
            <?php
            $typePre = 0;
            $arrPOIS = [];
            $isItems = 0;
            $isQC = false;
            ?>
            <?php if (!empty($productions_orders_details)) : ?>
                <?php foreach ($productions_orders_details as $key => $value) : ?>
                    <?php
                    $pod_id = $value['pod_id'];
                    $pois_id = $value['pois_id'];
                    $poi_id = $value['poi_id'];
                    $images = $value['images'];
                    $number = $value['number'];
                    $object_type = $value['object_type'];
                    $object_id = $value['object_id'];
                    $qc = $this->manufactures_model->isQCPre($pod_id, $poi_id, $pois_id, $number, true);
                    if (!empty($images)) {
                        $images = base_url('uploads/products/' . $images);
                    } else {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }
                    $isItems = true;

                    $referenceOrder = '';
                    if ($object_type == "orders") {
                        $order = get_table_where('tbl_orders', ['id' => $object_id], '', 'row_array', '', 'reference_no');
                        $referenceOrder = $order['reference_no'];
                    } else {
                        $business = get_table_where('tbl_business_plan', ['id' => $object_id], '', 'row_array', '', 'reference_no');
                        $referenceOrder = $business['reference_no'];
                    }

                    ?>
                    <div class="col-md-12 mtop5 parent-div object-<?= $object_id ?>__<?= $object_type ?>">
                        <div onclick="clickCheckBox(this)" style="cursor: pointer;">
                            <div style="float: left; padding-right: 10px;">
                                <div class="td-image">
                                    <div class="preview_image" style="width: auto;">
                                        <div class="display-block contract-attachment-wrapper img">
                                            <div style="width:45px;"><a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                    <div class=""><img src="<?= $images ?>" style="border-radius: 50%"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bold" style="position: relative;">
                                <a target="_blank" href="<?= base_url('admin/manufactures/detail_productions/' . $pod_id) ?>"><?= $value['item_name'] ?>(<?= $value['item_code'] ?>)</a>
                                <div class="checkbox checkbox-info checkbox-circle checkbox-cs" style="position: absolute; top: 0; right: 0;">
                                    <input type="checkbox" class="pointer ck-stages-finished" checked value="<?= $pois_id ?>" id="ck_<?= $value['pois_id'] ?>">
                                    <label for="ck_<?= $value['pois_id'] ?>"></label>
                                </div>
                            </div>
                            <div><?= lang('size') ?>: <?= $value['size_name'] ?></div>
                            <div><?= lang('quantity') ?>: <?= formatNumber($value['quantity']) ?></div>
                            <div style="padding-left: 55px;"><?= lang('tnh_dhb_khbtp') ?>: <?= $referenceOrder ?></div>
                        </div>
                        <input type="hidden" name="isQC[]" class="form-control isQC" value="<?= $qc ?>">
                        <?php if($qc == 1): ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-success"><?= lang('tnh_da_qc_trc') ?></span></div>
                        <?php elseif($qc == 2 || $qc == 3): ?>
                        <?php 
                            if (empty($arrObject[$object_id.'__'.$object_type])) {
                                $arrObject[$object_id.'__'.$object_type] = 1;
                            }
                            $isQC = true; 
                        ?>
                        <div class="mtop5" style="padding-left: 55px;"><span class="label label-warning"><?= lang('Chưa QC hoặc QC chưa đạt') ?></span></div>
                        <?php elseif($qc == 3): ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-danger"><?= lang('tnh_co_qc_chua_dat') ?></span></div>
                        <?php endif; ?>
                        
                        <div class="text-danger show-errors" style="padding-left: 55px;"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($qcItems)) : ?>
                <?php foreach($qcItems as $key => $value): ?>
                    <?php
                        $pod_id = $value['pod_id'];
                        $pois_id = $value['pois_id'];
                        $poi_id = $value['poi_id'];
                        $images = $value['images'];
                        $number = $value['number'];
                        $object_type = $value['object_type'];
                        $object_id = $value['object_id'];
                        $cqis_id = $value['cqis_id'];
                        $qc = $this->manufactures_model->isQCPre($pod_id, $poi_id, $pois_id, $number, true);
                        if (!empty($images)) {
                            $images = base_url('uploads/products/' . $images);
                        } else {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }
                        $isItems = true;

                        $referenceOrder = '';
                        if ($object_type == "orders") {
                            $order = get_table_where('tbl_orders', ['id' => $object_id], '', 'row_array', '', 'reference_no');
                            $referenceOrder = $order['reference_no'];
                        } else {
                            $business = get_table_where('tbl_business_plan', ['id' => $object_id], '', 'row_array', '', 'reference_no');
                            $referenceOrder = $business['reference_no'];
                        }
                    ?>
                    <div class="col-md-12 mtop5 parent-div object-<?= $object_id ?>__<?= $object_type ?>">
                        <div onclick="clickCheckBoxReturn(this)" style="cursor: pointer;">
                            <div style="float: left; padding-right: 10px;">
                                <div class="td-image">
                                    <div class="preview_image" style="width: auto;">
                                        <div class="display-block contract-attachment-wrapper img">
                                            <div style="width:45px;"><a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                    <div class=""><img src="<?= $images ?>" style="border-radius: 50%"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bold" style="position: relative;">
                                <a target="_blank" href="<?= base_url('admin/manufactures/detail_productions/' . $pod_id) ?>"><?= $value['item_name'] ?>(<?= $value['item_code'] ?>)</a>
                                <div class="checkbox checkbox-info checkbox-circle checkbox-cs" style="position: absolute; top: 0; right: 0;">
                                    <input type="checkbox" class="pointer ck-stages-finished-return" checked value="<?= $value['cqis_id'] ?>" id="ck_return<?= $value['cqis_id'] ?>">
                                    <label for="ck_return<?= $value['cqis_id'] ?>"></label>
                                </div>
                            </div>
                            <div><?= lang('size') ?>: <?= $value['size_name'] ?></div>
                            <div><?= lang('quantity') ?>: <?= formatNumber($value['quantity']) ?></div>
                            <div style="padding-left: 55px;"><?= lang('tnh_dhb_khbtp') ?>: <?= $referenceOrder ?></div>
                        </div>
                        <input type="hidden" name="isQC[]" class="form-control isQC" value="<?= $qc ?>">
                        <?php if($qc == 1): ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-success"><?= lang('tnh_da_qc_trc') ?></span> <span class="label label-danger"><?= lang('tnh_remake') ?></div>
                        <?php elseif($qc == 2 || $qc == 3): ?>
                            <?php 
                                if (empty($arrObject[$object_id.'__'.$object_type])) {
                                    $arrObject[$object_id.'__'.$object_type] = 1;
                                }
                                $isQC = true; 
                            ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-warning"><?= lang('Chưa QC hoặc QC chưa đạt') ?></span> <span class="label label-danger"><?= lang('tnh_remake') ?></div>
                        <?php elseif($qc == 3): ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-danger"><?= lang('tnh_co_qc_chua_dat') ?></span> <span class="label label-danger"><?= lang('tnh_remake') ?></div>
                        <?php else: ?>
                            <div class="mtop5" style="padding-left: 55px;"><span class="label label-danger"><?= lang('tnh_remake') ?></div>
                        <?php endif; ?>
                        <div class="text-danger show-errors" style="padding-left: 55px;"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!$isItems) : ?>
                <div class="col-md-12">
                    <div class="dataTables_empty"><?= lang('Không có mặt hàng chưa hoàn thành') ?></div>
                </div>
            <?php endif; ?>
        </div>
        <?php
            $createQC = '';
            // if(!empty($arrPOIS)) {
            //     $createQC =  '<a href="'.base_url('admin/quality_control/add_check_quality?pois='.implode(',', $arrPOIS)).'" class="btn-warning btn-sm">'.lang('tnh_create_qc').'</a>';
            // }
            if (!empty($isQC)) {
                $createQC =  '<a href="javascript:void(0)" onclick="clickAllQC(this)" class="btn-danger btn-sm mright5 hide">'.lang('Chọn tất cả chưa QC').'</a><a href="javascript:void(0)" onclick="clickPostQC(this)" class="btn-warning btn-sm">'.lang('tnh_create_qc').'</a>';
            }
        ?>
        <?php if ($stage['type'] == 0 && $typePre == 0) : ?>
            <div class="mtop5" style="text-align: center; bottom: 1px; right: 1px;">
                <?= $createQC ?>
                <span href="javascript:void(0)" onclick="agreeProcessFinished()" class="btn-primary-cs btn-sm"><?= lang('finished') ?></span>
            </div>
        <?php elseif ($stage['type'] == 1 || $stage['type'] == 2 || $stage['type'] == 3) : ?>
            <div class="text-center" style="margin-bottom: 5px;">
                <div class="">
                    <div class="form-group">
                        <?= $createQC ?>
                        <span href="javascript:void(0)" onclick="agreeProcessProduct('<?= $stage['type'] ?>')" class="btn-primary-cs btn-sm"><?= lang('finished') ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="hide" id="show-form"></div>
<script>
    var arrObject = <?= !empty($arrObject) ? json_encode($arrObject) : '{}' ?>;
    
    function loadCheckQC() {
        if (typeof arrObject !== "undefined") {
            $.each(arrObject, function (i, va) { 
                $('.object-'+i).find('.ck-stages-finished').prop('checked', false);
                $('.object-'+i).find('.ck-stages-finished-return').prop('checked', false);
            });
        }
    }

    function clickAllQC() {
        ck_stages_finished = $('.ck-stages-finished');
        if (typeof ck_stages_finished !== "undefined" && ck_stages_finished.length > 0) {
            $.each(ck_stages_finished, function(index, el) {
                parent_div = $(el).closest('.parent-div');
                isQC = parent_div.find('.isQC').val();
                c_pois_id = $(el).val();
                if (isQC == 2 || isQC == 3) {
                    $(el).prop('checked', true)
                }
            });
        }

        ck_stages_finished_return = $('.ck-stages-finished-return');
        if (typeof ck_stages_finished_return !== "undefined" && ck_stages_finished_return.length > 0) {
            $.each(ck_stages_finished_return, function(index, el) {
                parent_div = $(el).closest('.parent-div');
                isQC = parent_div.find('.isQC').val();
                if (isQC == 2 || isQC == 3) {
                    $(el).prop('checked', true);
                }
            });
        }
    }

    function clickPostQC(_this) {
        clickAllQC();
        var url = site.base_url+'admin/quality_control/add_check_quality';
        var inputs = '';
        inputs+= `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        arrPOIS = [];
        ck_stages_finished = $('.ck-stages-finished');
        if (typeof ck_stages_finished !== "undefined" && ck_stages_finished.length > 0) {
            $.each(ck_stages_finished, function(index, el) {
                parent_div = $(el).closest('.parent-div');
                isQC = parent_div.find('.isQC').val();
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    if (isQC == 2 || isQC == 3) {
                        arrPOIS.push(c_pois_id);
                    }
                }
            });
        }

        arrCQIS = [];
        ck_stages_finished_return = $('.ck-stages-finished-return');
        if (typeof ck_stages_finished_return !== "undefined" && ck_stages_finished_return.length > 0) {
            $.each(ck_stages_finished_return, function(index, el) {
                parent_div = $(el).closest('.parent-div');
                isQC = parent_div.find('.isQC').val();
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    if (isQC == 2 || isQC == 3) {
                        arrCQIS.push(c_pois_id);
                    }
                }
            });
        }

        isQCC = false;
        if (arrPOIS.length > 0) {
            $.each(arrPOIS, function(i, e) {
                inputs+= `<input type="hidden" name="arrPOIS[]" value="${e}">`;
            });
            isQCC = true;
        }

        if (arrCQIS.length > 0) {
            $.each(arrCQIS, function(i, e) {
                inputs+= `<input type="hidden" name="arrCQIS[]" value="${e}">`;
            });
            isQCC = true;
        }

        if (isQCC == false) {
            bootbox.alert('Vui lòng chọn mặt hàng loại QC hoặc QC chưa đạt');
            return;
        }

        $("#show-form").append('<form action="'+url+'" method="post" id="poster">'+inputs+'</form>');
        $("#poster").submit();
    }

    function agreeProcessFinished() {
        arrPOIS = [];
        ck_stages_finished = $('.ck-stages-finished');
        if (typeof ck_stages_finished !== "undefined" && ck_stages_finished.length > 0) {
            $.each(ck_stages_finished, function(index, el) {
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    arrPOIS.push(c_pois_id);
                }
            });
        }

        arrCQIS = [];
        ck_stages_finished_return = $('.ck-stages-finished-return');
        if (typeof ck_stages_finished_return !== "undefined" && ck_stages_finished_return.length > 0) {
            $.each(ck_stages_finished_return, function(index, el) {
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    arrCQIS.push(c_pois_id);
                }
            });
        }

        if (arrPOIS.length > 0 || arrCQIS.length > 0) {
            cTitle = '<?= lang('Bạn có muốn duyệt hoàn thành giai đoạn này ?') ?>';
            bootbox.confirm({
                message: cTitle,
                buttons: {
                    confirm: {
                        label: '<?= lang('tnh_update') ?>',
                        className: 'btn-primary'
                    },
                    cancel: {
                        label: '<?= lang('close') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result == true) {
                        $.ajax({
                            type: "POST",
                            url: site.base_url + 'admin/manufactures/agreeProcessMultiple',
                            data: {
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                arrPOIS: arrPOIS,
                                arrCQIS: arrCQIS,
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.result) {
                                    alert_float('success', response.message);
                                    $('.task-info').find('.active').click();
                                } else {
                                    alert_float('danger', response.message);
                                }
                                if (typeof oTableDetail != "undefined") {
                                    oTableDetail.draw();
                                }
                            }
                        });
                    }
                }
            });
        } else {
            alert_float('danger', '<?= lang('Vui lòng chọn mặt hàng muốn hoàn thành.') ?>');
        }
    }

    function clickCheckBox(_this) {
        cDivC = $(_this).closest('div.col-md-12');
        if (cDivC.find('.ck-stages-finished').prop('checked')) {
            cDivC.find('.ck-stages-finished').prop('checked', false);
        } else {
            cDivC.find('.ck-stages-finished').prop('checked', true);
        }
    }

    function clickCheckBoxReturn(_this) {
        cDivC = $(_this).closest('div.col-md-12');
        if (cDivC.find('.ck-stages-finished-return').prop('checked')) {
            cDivC.find('.ck-stages-finished-return').prop('checked', false);
        } else {
            cDivC.find('.ck-stages-finished-return').prop('checked', true);
        }
    }

    function showViewSemiProducts(c_pois_id, c_pod_id) {
        $.ajax({
                url: site.base_url + 'admin/manufactures/handlingSemiProduct',
                type: 'POST',
                dataType: 'html',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    pod_id: c_pod_id,
                    type: 1,
                    pois_id: c_pois_id,
                    actions: 'view'
                },
            })
            .done(function(data) {
                $('#tnhModal').html(data);
            })
            .fail(function() {
                console.log("error");
            });
        $('#tnhModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    function agreeProcessProduct(c_type) {
        arrPOIS = [];
        ck_stages_finished = $('.ck-stages-finished');
        if (typeof ck_stages_finished !== "undefined" && ck_stages_finished.length > 0) {
            $.each(ck_stages_finished, function(index, el) {
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    arrPOIS.push(c_pois_id);
                }
            });
        }

        arrCQIS = [];
        ck_stages_finished_return = $('.ck-stages-finished-return');
        if (typeof ck_stages_finished_return !== "undefined" && ck_stages_finished_return.length > 0) {
            $.each(ck_stages_finished_return, function(index, el) {
                if ($(el).prop('checked')) {
                    c_pois_id = $(el).val();
                    arrCQIS.push(c_pois_id);
                }
            });
        }

        warehouse_import = $('#warehouse_import').val();
        if (!warehouse_import) {
            alert_float('danger', '<?= lang('Vui lòng chọn kho BTP/TP') ?>');
            return;
        }

        linkActive = '';
        if (c_type == 1) {
            linkActive = site.base_url + 'admin/manufactures/agreeProcessSemiProductNew';
        } else if (c_type == 2) {
            linkActive = site.base_url + 'admin/manufactures/agreeProcessProduct';
        } else if (c_type == 3) {
            linkActive = site.base_url + 'admin/manufactures/agreeProcessProductStep';
        }

        if (!linkActive) {
            alert_float('danger', '<?= lang('Lỗi giai đoạn') ?>');
            return;
        }

        if (arrPOIS.length > 0 || arrCQIS.length > 0) {
            // if (c_type == 1) {
            //     dataPOST = {};
            //     dataPOST[csrfData['token_name']] = csrfData['hash'];
            //     dataPOST['type'] = c_type;
            //     dataPOST['arrPOIS'] = arrPOIS;
            //     dataPOST['stage_id'] = '<?= $stage_id ?>';
            //     dataPOST['po_id'] = '<?= $productions_orders_id ?>';
            //     dataPOST['warehouse_import'] = warehouse_import;
            //     showModalCustom(site.base_url+'admin/manufactures/showSemiProductsMultiple', '#tnhModal', false, dataPOST);
            // } else {
                cTitle = '<?= lang('Bạn có muốn duyệt hoàn thành giai đoạn này ?') ?>';
                bootbox.confirm({
                    message: cTitle,
                    buttons: {
                        confirm: {
                            label: '<?= lang('tnh_update') ?>',
                            className: 'btn-primary'
                        },
                        cancel: {
                            label: '<?= lang('close') ?>',
                            className: 'btn-danger'
                        }
                    },
                    callback: function(result) {
                        if (result == true) {
                            $.ajax({
                                type: "POST",
                                url: linkActive,
                                data: {
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                    arrPOIS: arrPOIS,
                                    arrCQIS: arrCQIS,
                                    type: c_type,
                                    warehouse_import: warehouse_import,
                                },
                                dataType: "json",
                                success: function(response) {
                                    if (response.result == 1) {
                                        alert_float('success', response.message);
                                        $('.task-info').find('.active').click();
                                        if (typeof oTableDetail != "undefined") {
                                            oTableDetail.draw();
                                        }
                                    } else if (response.result == 2) {
                                        if (response.errorsWarehouses) {
                                            divShowWarehousesErrors = '<div class="bold text-danger"><?= lang('NVL hoặc BTP không đủ để xuất kho') ?></div>';
                                            $.each(response.errorsWarehouses, function(i, v) {
                                                divShowWarehousesErrors+= `
                                                <div>
                                                    <div style="float: left; padding-right: 10px;">
                                                        <div class="td-image">
                                                            <div class="preview_image" style="width: auto;">
                                                                <div class="display-block contract-attachment-wrapper img">
                                                                    <div style="width:30px;"><a href="${v.images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                                            <div class=""><img src="${v.images}" style="border-radius: 50%"></div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bold" style="position: relative; font-size: 11px; padding-left: 40px;">
                                                        ${v.item_name}(${v.item_code})
                                                    </div>
                                                    <div style="font-size: 11px; padding-left: 40px;"><?= lang('tnh_warehouses') ?>: ${v.warehouse_name} - ${v.location_name}</div>
                                                    <div style="font-size: 11px; padding-left: 40px;"><?= lang('tnh_quantity_lack') ?>: ${tnhFormatNumber(intVal(v.quantity_primary) - intVal(v.product_quantity))}</div>
                                                </div>
                                                `;
                                            });
                                            $('.show-warehouses-errors').css({
                                                'border': '1px dashed #ea152b',
                                                'padding': '5px 2px'
                                            });
                                            $('.show-warehouses-errors').html(divShowWarehousesErrors);
                                            // bootbox.alert(response.errorsWarehouses);
                                        }
                                        if (response.arrErrors) {
                                            $.each(response.arrErrors, function(i, v) {
                                                cDiv12 = $('.ck-stages-finished[value="' + v['pois_id'] + '"]').closest('div.col-md-12');
                                                cDiv12.find('.show-errors').html(`<div onclick="showViewSemiProducts('${v.pois_id}', '${v.pod_id}')" class="label label-danger border-radius-10px pointer">${v.message}</div>`);
                                            });
                                        }
                                        alert_float('danger', response.message);
                                    } else {
                                        alert_float('danger', response.message);
                                    }
                                }
                            });
                        }
                    }
                });
            // }
        } else {
            alert_float('danger', '<?= lang('Vui lòng chọn mặt hàng muốn hoàn thành') ?>')
        }
    }

    // function agreeProcessProduct() {
    //     arrPOIS = [];
    //     ck_stages_finished = $('.ck-stages-finished');
    //     if (typeof ck_stages_finished !== "undefined" && ck_stages_finished.length > 0) {
    //         $.each(ck_stages_finished, function(index, el) {
    //             if ($(el).prop('checked')) {
    //                 c_pois_id = $(el).val();
    //                 arrPOIS.push(c_pois_id);
    //             }
    //         });
    //     }

    //     warehouse_import = $('#warehouse_import').val();
    //     if (!warehouse_import) {
    //         alert_float('danger', '<?= lang('Vui lòng chọn kho thành phẩm') ?>');
    //         return;
    //     }

    //     if (arrPOIS.length > 0) {
    //         cTitle = '<?= lang('Bạn có muốn duyệt hoàn thành giai đoạn này ?') ?>';
    //         bootbox.confirm({
    //             message: cTitle,
    //             buttons: {
    //                 confirm: {
    //                     label: '<?= lang('tnh_update') ?>',
    //                     className: 'btn-primary'
    //                 },
    //                 cancel: {
    //                     label: '<?= lang('close') ?>',
    //                     className: 'btn-danger'
    //                 }
    //             },
    //             callback: function(result) {
    //                 if (result == true) {
    //                     $.ajax({
    //                         type: "POST",
    //                         url: site.base_url + 'admin/manufactures/agreeProcessProduct',
    //                         data: {
    //                             "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
    //                             arrPOIS: arrPOIS,
    //                             warehouse_import: warehouse_import,
    //                         },
    //                         dataType: "json",
    //                         success: function(response) {
    //                             // if (response.result == 1) {
    //                             //     alert_float('success', response.message);
    //                             //     $('.task-info').find('.active').click();
    //                             // } else if (response.result == 2) {
    //                             //     if (response.errorsWarehouses) {
    //                             //         bootbox.alert(response.errorsWarehouses);
    //                             //     }
    //                             //     if (response.arrErrors) {
    //                             //         $.each(response.arrErrors, function (i, v) { 
    //                             //             cDiv12 = $('.ck-stages-finished[value="'+v['pois_id']+'"]').closest('div.col-md-12');
    //                             //             cDiv12.find('.show-errors').html(`<div onclick="showViewSemiProducts('${v.pois_id}', '${v.pod_id}')" class="label label-danger border-radius-10px pointer">${v.message}</div>`);
    //                             //         });
    //                             //     }
    //                             //     alert_float('danger', response.message);
    //                             // } else {
    //                             //     alert_float('danger', response.message);
    //                             // }
    //                             alert_float('danger', response.message);
    //                         }
    //                     });
    //                 }
    //             }
    //         });
    //     } else {
    //         alert_float('danger', '<?= lang('Vui lòng chọn mặt hàng muốn hoàn thành') ?>')
    //     }
    // }

    $(document).ready(function() {
        $('#warehouse_import').select2();
        loadCheckQC();
    });
</script>