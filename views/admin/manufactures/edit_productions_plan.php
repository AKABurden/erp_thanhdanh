<?php echo form_open('admin/manufactures_temp/edit_productions_plan/' . $id, array('id' => 'edit_productions_plan')); ?>
<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_edit_productions_plan'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_reference_productions_plan', 'reference_no') ?>
                        <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $dtProductionsPlan['reference_no'] ?>" readonly="" aria-invalid="false">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', _d($dtProductionsPlan['date']), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_branch', 'id_branch') ?>
                        <select name="id_branch" id="id_branch" class="id_branch modal-select2" required="required" style="width: 100%;" data-placeholder="<?= lang('tnh_branch') ?>">
                            <option value=""></option>
                            <?php if (!empty($branch)) : ?>
                                <?php foreach ($branch as $key => $value) : ?>
                                    <option <?= $value['id'] == $dtProductionsPlan['id_branch'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <textarea name="note" id="note" class="form-control" placeholder="<?= lang('note') ?>" rows="3"><?= $dtProductionsPlan['note'] ?></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home-orders" aria-controls="home-orders" role="tab" data-toggle="tab"><?= lang('Thành phẩm đơn hàng') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-preventive" aria-controls="tab-preventive" role="tab" data-toggle="tab"><?= lang('Thành phẩm dự phòng') ?></a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#tab-bom" aria-controls="tab-bom" role="tab" data-toggle="tab"><?= lang('Tổng hợp NPL') ?></a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home-orders">
                                <table id="table-plan" class="table table-hover dataTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 120px;" class="text-center"><?= lang('tnh_sales_orders') ?>/<?= lang('tnh_business_plan') ?></th>
                                            <th style="width: 120px;" class="text-center"><?= lang('tnh_product_code') ?></th>
                                            <th style="width: 120px;" class="text-center"><?= lang('tnh_product_name') ?></th>
                                            <th style="width: 100px;" class="text-center"><?= lang('BOM') ?></th>
                                            <th style="width: 100px;" class="text-center"><?= lang('stages') ?></th>
                                            <th style="width: 80px;" class="text-center"><?= lang('tnh_conversion_unit') ?></th>
                                            <th style="width: 100px;" class="text-center"><?= lang('tnh_quantity_need') ?></th>
                                            <th style="width: 100px;" class="text-center"><?= lang('tnh_expected_delivery') ?></th>
                                            <th style="width: 80px;" class="text-center"><?= lang('actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $this->db->select('
                                                tbl_productions_plan_items.*,
                                                tbl_products.code as item_code,
                                                tbl_products.name as item_name,
                                                tblunits.unit as unit
                                            ');
                                            $this->db->from('tbl_productions_plan_items');
                                            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id');
                                            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
                                            $this->db->where('tbl_productions_plan_items.productions_plan_id', $id);
                                            $this->db->where('tbl_productions_plan_items.is_preventive', 0);
                                            $items = $this->db->get()->result_array();
                                        ?>
                                        <?php if(!empty($items)): ?>
                                            <?php foreach($items as $key => $value): ?>
                                                <?php
                                                    $productions_plan_item_id = $value['id'];
                                                    $products_id = $value['product_id'];
                                                    $type_object = $value['type_object'];    
                                                    $object_id = $value['object_id'];
                                                    $item_object_id = $value['item_object_id'];
                                                    $strObject = '';
                                                    if ($type_object == "orders") {
                                                        $dtOrder = $this->orders_model->rowOrderById($object_id);
                                                        $strObject = $dtOrder['reference_no'];
                                                    } else if ($type_object == "business_plan") {
                                                        $dtBusinessPlan = $this->business_plan_model->rowBusinessPlanById($object_id);
                                                        $strObject = $dtBusinessPlan['reference_no'];
                                                    }

                                                    $temp_str_object = $type_object.'___'.$item_object_id;
                                                    $versions = $value['versions'];
                                                    $versions_stage = $value['versions_stage'];

                                                    $this->db->select('tbl_product_versions.versions as versions');
                                                    $this->db->from('tbl_product_versions');
                                                    $this->db->where('tbl_product_versions.product_id', $products_id);
                                                    $product_verions = $this->db->get()->result_array();
                                                    $optionsVersions = "<option></option>";
                                                    if (!empty($product_verions)) {
                                                        foreach ($product_verions as $k => $val) {
                                                            $selected = ($versions == $val['versions']) ? 'selected' : '';
                                                            $optionsVersions.= '<option '.$selected.' value="'.$val['versions'].'">'.$val['versions'].'</option>';
                                                        }
                                                    }

                                                    //stages
                                                    $this->db->select('tbl_product_stages.versions as versions');
                                                    $this->db->from('tbl_product_stages');
                                                    $this->db->where('tbl_product_stages.product_id', $products_id);
                                                    $product_verions_stages = $this->db->get()->result_array();
                                                    $optionsVersionsStages = "<option></option>";
                                                    if (!empty($product_verions_stages)) {
                                                        foreach ($product_verions_stages as $k => $val) {
                                                            $selected = ($versions_stage == $val['versions']) ? 'selected' : '';
                                                            $optionsVersionsStages.= '<option '.$selected.' value="'.$val['versions'].'">'.$val['versions'].'</option>';
                                                        }
                                                    }

                                                    $this->db->select('
                                                        tbl_productions_plan_details.date
                                                    ', false);
                                                    $this->db->from('tbl_productions_plan_details');
                                                    $this->db->where('tbl_productions_plan_details.productions_plan_item_id', $productions_plan_item_id);
                                                    $productionsPlanDetails = $this->db->get()->row_array();
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td class="text-center"><?= $strObject ?></td>
                                                    <td class="text-center"><?= $value['item_code'] ?></td>
                                                    <td class="text-center"><?= $value['item_name'] ?></td>
                                                    <td>
                                                        <input type="hidden" name="productions_plan_items[<?= $temp_str_object ?>]" class="form-control" value="<?= $value['id'] ?>">
                                                        <input type="hidden" name="cs_product_id[<?= $temp_str_object ?>]" class="form-control cs_product_id" value="<?= $products_id ?>">
                                                        <input type="hidden" name="poduct_id_css[<?= $temp_str_object ?>]" class="form-control poduct_id_css" value="<?= $products_id ?>">

                                                        <input type="hidden" name="type_object[<?= $temp_str_object ?>]" class="form-control type_object" value="<?= $type_object ?>">
                                                        <input type="hidden" name="object_id[<?= $temp_str_object ?>]" class="form-control object_id" value="<?= $object_id ?>">
                                                        <input type="hidden" name="item_object_id[<?= $temp_str_object ?>]" class="form-control item_object_id" value="<?= $item_object_id ?>">

                                                        <select name="versions[<?= $temp_str_object ?>]" onchange="totalBOM()" data-placeholder="BOM" class="versions" style="width: 100%;">
                                                            <?= $optionsVersions ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="versions_stage[<?= $temp_str_object ?>]" data-placeholder="Công đoạn" class="stages" style="width: 100%;">
                                                            <?= $optionsVersionsStages ?>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= $value['unit'] ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="quantity[<?= $temp_str_object ?>]" onchange="totalBOM()" class="form-control quantity number-format" style="width: 100%;" value="<?= formatNumber($value['quantity_total_details']) ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <?= _d($productionsPlanDetails['date']) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" onclick="removeProductionsPlanEdit(this)" class="fa fa-remove text-danger"></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-preventive">
                                <table id="table-plan-preventive" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                            <th class="text-center"><?= lang('tnh_products') ?></th>
                                            <th class="text-center"><?= lang('BOM') ?></th>
                                            <th class="text-center"><?= lang('stages') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_conversion_unit') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_quantity_reserve') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-bom">
                                <table id="tb-bom" class="table table-hover table-bordered dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_materials_code') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_materials_name') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('type') ?></th>
                                            <th class="text-center"><?= lang('unit_bom') ?></th>
                                            <th class="text-center"><?= lang('tnh_quantity_use') ?></th>
                                            <th class="text-center"><?= lang('tnh_quantity_compensation') ?></th>
                                            <th class="text-center"><?= lang('Tổng số lượng (ĐV kho)') ?></th>
                                            <th class="text-center"><?= lang('tnh_quantity_inventory') ?></th>
                                            <th class="text-center"><?= lang('status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary edit-productions_plan"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var arrIdPreventive = [];
    var arrIdBOM = {};

    function changeQuantityPreventive(_this) {
        trItems = $(_this).closest('tr');
        product_id_preventive = trItems.find('.product_id_preventive').val();
        str_product_id_preventive = 'products__'+product_id_preventive;
        quantity_preventive = intVal($(_this).val());
        arrIdPreventive[str_product_id_preventive] = quantity_preventive;

        totalBOM();
    }

    function removeProductionsPlanEdit(_this) {
        $(_this).closest('tr').remove();
        loadProductsPreventive();
        totalBOM();
    }

    async function loadProductsPreventive() {
        var aoDataPOST = {};
        aoDataPOST["<?= $this->security->get_csrf_token_name() ?>"] = "<?= $this->security->get_csrf_hash() ?>";

        cs_product_id = [];
        $.each($('#table-plan tbody tr'), function (index, value) { 
            cur_product_id = $(value).find('.cs_product_id').val();
            cs_product_id.push(cur_product_id);
        });

        aoDataPOST['cs_product_id'] = cs_product_id;
        aoDataPOST['productions_plan_id'] = '<?= $id ?>';

        await $.ajax({
            'dataType': 'json',
            'type': 'POST',
            'url': site.base_url+'admin/manufactures_temp/loadProductsPreventiveEdit',
            'data': aoDataPOST,
            success: function (response) {
                trHtml = '';
                if (typeof response.arrProducts !== 'undefined' && response.arrProducts.length) {
                    var stt = 0;
                    $.each(response.arrProducts, function (index, value) { 
                        stt++;
                        tdNumber = `<td class="text-center td-numbers">${stt}</td>`;
                        tdProduct = `<td>
                            <input type="hidden" name="productions_plan_items_id_preventive[]" class="form-control productions_plan_items_id_preventive" value="${value.id}">
                            <input type="hidden" name="productions_plan_items_id_item_object_id_preventive[]" class="form-control productions_plan_items_id_item_object_id_preventive" value="${value.item_object_id}">
                            <input type="hidden" name="product_id_preventive[]" class="form-control product_id_preventive" value="${value.product_id}">
                            ${value.item_name}(${value.item_code})
                        </td>`;
                        tdBOM = `<td>
                            <select name="versions_perventive[]" onchange="totalBOM()" data-placeholder="BOM" class="versions_perventive" style="width: 100%;">
                                ${value.optionsVersions}
                            </select>
                        </td>`;
                        tdStages = `<td>
                            <select name="versions_stages_perventive[]" data-placeholder="Công đoạn" class="versions_stages_perventive" style="width: 100%;">
                                ${value.optionsVersionsStages}
                            </select>
                        </td>`;
                        tdUnits = `<td class="text-center">${value.unit_name}</td>`;
                        quantity_preventive = 0;
                        if (typeof arrIdPreventive['products__'+value.product_id] !== 'undefined') {
                            quantity_preventive = intVal(arrIdPreventive['products__'+value.product_id]);
                        } else {
                            quantity_preventive = intVal(value.quantity);
                        }

                        var readonly = '';
                        if(value.is_no_stock == 1){
                            readonly = 'readonly';
                        }
                        tdQuantity = `<td class="">
                            <input type="hidden" name="is_no_stock[]" class="form-control is_no_stock" value="${value.is_no_stock}">
                            <input type="text" ${readonly} name="quantity_preventive[]" onchange="changeQuantityPreventive(this)" class="form-control quantity_preventive number-format" value="${tnhFormatNumber(quantity_preventive)}">
                        </td>`;

                        trHtml+= `<tr>
                            ${tdNumber}
                            ${tdProduct}
                            ${tdBOM}
                            ${tdStages}
                            ${tdUnits}
                            ${tdQuantity}
                        </tr>`;
                    });
                }
                $('#table-plan-preventive tbody').html(trHtml);
                $('.versions_perventive').select2();
                $('.versions_stages_perventive').select2();
            }
        });
    }

    function isStatusW() {
        tb = '#tb-bom tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        is_errors = 0;
        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];

            b_standard_unit = intVal($(element).find('.standard_unit'));
            b_exchange_standard_unit = intVal($(element).find('.exchange_standard_unit').val());
            b_quantity_exchange = intVal($(element).find('.quantity_exchange').val());
            b_exchange_unit = intVal($(element).find('.exchange_unit').val());
            b_quantity = intVal($(element).find('.quantity').val());
            b_quantity_warehouse = intVal($(element).find('.quantity_warehouse').val());
            b_quantity_compensation = intVal($(element).find('.quantity_compensation').val());
            b_conversion_quantity_unit = intVal($(element).find('.conversion_quantity_unit').val());
            item_type = ($(element).find('.item_type').val());

            b_quantity_need = b_quantity + b_quantity_compensation;
            b_quantity_primary = b_quantity_need * b_quantity_exchange / b_exchange_unit;
            if (item_type == "materials") {
                // b_quantity_convert_warehouse = tnhToFixedNumber(b_quantity_primary / b_exchange_standard_unit * b_exchange_unit, 0);
                b_quantity_convert_warehouse = Math.ceil(b_quantity_primary / b_exchange_standard_unit * b_exchange_unit);

                
            } else {
                // b_quantity_convert_warehouse = tnhToFixedNumber(b_quantity_primary * b_conversion_quantity_unit, 0);
                b_quantity_convert_warehouse = Math.ceil(b_quantity_primary * b_conversion_quantity_unit);
            }


            b_strStatus = '';
            if (b_quantity_convert_warehouse > b_quantity_warehouse) {
                b_strStatus = '<span class="label label-danger"><?= lang('Chưa đủ kho') ?></span>';
                is_errors++;
            } else {
                b_strStatus = '<span class="label label-success"><?= lang('Đã đủ kho') ?></span>';
            }

            $(element).find('.quantity_convert_warehouse').html(tnhFormatNumber(b_quantity_convert_warehouse));
            $(element).find('.td-status').html(b_strStatus);
        }

        if (is_errors) {
            $('a[aria-controls="tab-bom"]').closest('li').css('background', '#ff00003b');
        } else {
            $('a[aria-controls="tab-bom"]').closest('li').css('background', 'unset');
        }
    }

    function changeQuantityCompensation(_this) {
        trItems = $(_this).closest('tr');
        _item_id_bom = trItems.find('.item_id').val();
        _item_type_bom = trItems.find('.item_type').val();

        str_item_bom = _item_type_bom+'__'+_item_id_bom;
        quantity_bom_compensation = intVal($(_this).val());
        arrIdBOM[str_item_bom] = quantity_bom_compensation;

        isStatusW();
    }

    async function totalBOM() {
        var form = $('#edit_productions_plan'), formData = new FormData(), formParams = form.serializeArray();
        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });
        formData.append('productions_plan_id', '<?= $id ?>');

        if (arrIdBOM) {
            for( var key in arrIdBOM ) {
                formData.append(key, arrIdBOM[key]);
            }
        }

        var url = form.action;
        await $.ajax({
            url : site.base_url+'admin/manufactures_temp/loadBOMPPEdit',
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
        .done(function(data) {
            console.log(data);
            $('#tb-bom tbody').html(data.trItems);
            isStatusW();
        })
        .fail(function() {
        });
        return false;
    }

    async function loadAsyncData() {
        await loadProductsPreventive();
        await totalBOM();
    }  

    $(function() {
        init_datepicker();
        $('#id_branch').select2();
        $('select.versions').select2();
        $('select.stages').select2();

        // loadProductsPreventive();
        // $(document).ready(function () {
        //     totalBOM();
        // });

        loadAsyncData();


        appValidateForm($('#edit_productions_plan'), {
            'date': 'required',
            'id_branch': 'required',
        }, edit_productions_plan);

        function edit_productions_plan(form) {
            $('.edit-productions_plan').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw(false);
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.edit-productions_plan').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.edit-productions_plan').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>