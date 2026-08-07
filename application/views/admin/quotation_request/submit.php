<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .sidebar {
        display: none;
    }
</style>
<?php echo form_open('admin/quotation_request/submit/' . ($id ?? '') . '', array('id' => 'submit_form')); ?>
<div>
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td style="width: 15%;">
                                    <?= lang('Mã Số Phiếu', 'code') ?>
                                </td>
                                <td style="width: 35%;">
                                    <div class="form-group">
                                        <input type="text" name="code" class="form-control" id="code" value="<?= $value['code'] ?? 'Tự động hệ thống' ?>" readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('Ngày lập phiếu', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date', (!empty($value['date']) ? _dt($value['date']) : date('d/m/Y H:i')), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <!-- <td><?= lang('Tên Brand') ?></td>
                                <td colspan="1"><?= render_select('brand_ids[]', $arrBrand, ['id', 'code', 'name'], '', '', ['multiple'=>true, 'data-actions-box' => true], [], '', '', false) ?></td> -->
                                <td><?= lang('Khách Hàng', 'client_id') ?></td>
                                <td>
                                    <input type="text" name="client_id" id="client_id" class="client_id" data-placeholder="<?= lang('Mã Khách Hàng') ?>" style="width: 100%;" value="<?= (!empty($value['client_id']) ? 'customers__'.$value['client_id'] : '') ?>" title="">
                                </td>
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="1">
                                    <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($value) ? $value['note'] : '' ?></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div>
                    <label for="item"><?= lang('Sản phẩm') ?></label>
                    <input type="text" name="item" id="item" style="width: 100%;" data-placeholder="<?= lang('Sản phẩm') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th><?= lang('Tên Nhóm SP') ?></th>
                                <th><?= lang('Tên Chủng Loại') ?></th>
                                <th><?= lang('ĐV Tính SP') ?></th>
                                <th><?= lang('Height') ?></th>
                                <th><?= lang('Width') ?></th>
                                <th><?= lang('ĐV Đo SP') ?></th>
                                <th><?= lang('Mã Thành Phẩm') ?></th>
                                <th><?= lang('Tên Thành Phẩm') ?></th>
                                <th><?= lang('Brand') ?></th>
                                <th><?= lang('Tiêu Chuẩn Đóng Gói') ?></th>
                                <th><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                <th><?= lang('Thời Gian Tồn Kho') ?></th>
                                <th><?= lang('Định Mức Thời Gian') ?></th>
                                <th><?= lang('Hình Ảnh SP') ?></th>
                                <th><?= lang('Tác Vụ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0; if(!empty($value['items'])) {
                                foreach($value['items'] as $itemRow => $itemValue) {
                                    $imageUrl = base_url('assets/images/tnh/no_image.png');
                                    if (!empty($itemValue['image'])) {
                                        $imageUrl = base_url($itemValue['image']);
                                    }
                                     ?>
                                    <tr>
                                        <td class="text-center"><div class="stt"><?= (++$itemRow) ?></div></td>
                                        <td><div class="category_item"><?= $itemValue['category_name'] ?? '' ?></div></td>
                                        <td><div class="specie_item"><?= $itemValue['specie_name'] ?? '' ?></div></td>
                                        <td><div class="unit_item"><?= $itemValue['unit_name'] ?? '' ?></div></td>
                                        <td><div class="height_item"><?= $itemValue['height'] ?? '' ?></div></td>
                                        <td><div class="wide_item"><?= $itemValue['wide'] ?? '' ?></div></td>
                                        <td><div class="unit_measure_item"><?= $itemValue['unit_measure'] ?? '' ?></div></td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id" value="<?= $itemValue['item_id'].'__'.$itemValue['item_type'] ?>">
                                                <input type="hidden" name="item_row_id[<?= $counter ?>]" class="item_row_id" value="<?= $itemValue['id'] ?>">
                                                <?= $itemValue['item_code'] ?>
                                            </div>
                                        </td>
                                        <td><div class="name_item"><?= $itemValue['item_name'] ?></div></td>
                                        <td><div class="brand_item">
                                            <select name="brand_ids[<?= $counter ?>][]" class="selectpicker brand_ids" multiple="1" data-actions-box="1" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                <?php foreach($arrBrand as $brand){ ?>
                                                    <option value="<?= $brand['id'] ?>" data-subtext="<?= $brand['name'] ?>" <?= (in_array($brand['id'], $itemValue['brand_ids']) ? 'selected' : '') ?>><?= $brand['code'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div></td>
                                        <td><div class="packing_item"><?= $itemValue['packing'] ?? '' ?></div></td>
                                        <td><div class="quantity_max_item text-right"><?= (!empty($itemValue['quantity_max']) ? formatNumber($itemValue['quantity_max']) : 0) ?></div></td>
                                        <td><div class="time_inventory_item text-right"><?= (!empty($itemValue['time_inventory']) ? formatNumber($itemValue['time_inventory']) : 0) ?></div></td>
                                        <td><div class="quota_time_change_one_item text-right"><?= (!empty($itemValue['quota_time_change_one']) ? formatNumber($itemValue['quota_time_change_one']) : 0) ?></div></td>
                                        
                                        <td><div class="td-image">
                                            <div class="preview_image" style="width: auto;">
                                                <div class="display-block contract-attachment-wrapper img">
                                                    <div style="width:45px;">
                                                        <a href="<?= $imageUrl ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                            <div class="">
                                                                <img src="<?= $imageUrl ?>" style="border-radius: 50%">
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                                <?php $counter++; }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right" style="width: 100%">
            <button type="submit" class="btn btn-info only-save btn-submit">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var counter = <?= $counter ?>;
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    $(document).ready(function() {
        ajaxSelectParamsCallback('#client_id', 'admin/clients/searchCustomers/', $('#client_id').val(), false, true);
        ajaxSelectParamsCallback($('#item'), 'admin/quotation_request/searchProductsSelect2', 0);
    });

    $("#client_id").change(function (){
        clients = $(this).select2('data');
        console.log(clients);
    })

    $("#item").change(function (){
        dtItems = $(this).select2('data');
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function loadItem(item = {}){
        // console.log(dtItems);return;
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="item_id[${counter}]" class="item_id" value="${item.id}">
        ${item.item_code}
        </div>`;
        if (item.images) {
            images = site.base_url+item.images;
        } else {
            images = site.base_url+'assets/images/tnh/no_image.png';
        }
        tdImages = `<div class="td-image">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:45px;">
                                <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                    <div class="">
                                        <img src="${images}" style="border-radius: 50%">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
            </div>`;
        tdName = `<div class="name_item">${item.item_name}</div>`;
        tdBrand = `<div class="brand_item">
            <select name="brand_ids[${counter}][]" class="selectpicker brand_ids" multiple="1" data-actions-box="1" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                <?php foreach($arrBrand as $brand){ ?>
                    <option value="<?= $brand['id'] ?>" data-subtext="<?= $brand['name'] ?>"><?= $brand['code'] ?></option>
                <?php } ?>
            </select>
        </div>`;
        // tdCode = `<div class="code_item">${item.item_code}</div>`;
        tdUnit = `<div class="unit_item">${item.unit_name}</div>`;
        tdCategory = `<div class="category_item">${item.category_name}</div>`;
        tdSpecie = `<div class="specie_item">${(item.specie_name ?? '')}</div>`;
        tdHeight = `<div class="height_item">${item.height}</div>`;
        tdWide = `<div class="wide_item">${item.wide}</div>`;
        tdUnit_measure = `<div class="unit_measure_item">${(item.unit_measure ?? '')}</div>`;
        tdPacking = `<div class="packing_item">${item.packing}</div>`;
        tdQuantity_max = `<div class="quantity_max_item text-right">${formatNumber(item.quantity_max)}</div>`;
        tdTime_inventory = `<div class="time_inventory_item text-right">${formatNumber(item.time_inventory)}</div>`;
        tdQuota_time_change_one = `<div class="quota_time_change_one_item text-right">${formatNumber(item.quota_time_change_one)}</div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCategory}</td>
            <td>${tdSpecie}</td>
            <td>${tdUnit}</td>
            <td>${tdHeight}</td>
            <td>${tdWide}</td>
            <td>${tdUnit_measure}</td>
            <td>${tdCode}</td>
            <td>${tdName}</td>
            <td>${tdBrand}</td>
            <td>${tdPacking}</td>
            <td>${tdQuantity_max}</td>
            <td>${tdTime_inventory}</td>
            <td>${tdQuota_time_change_one}</td>
            <td>${tdImages}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        init_selectpicker();
        counter ++;
        getTotal();
    }

    function removeRow(el)
    {
        $(el).closest('tr').remove();
        getTotal();
    }

    function getTotal(){
        tb = '#tb-purchases tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
        }
    }

    appValidateForm($('#submit_form'), {
        reference_no: 'required',
        date: 'required',
        client_id: 'required',
    }, submit);

    function submit(form) {
        // if (count_errors > 0) {
        //     alert_float('danger', lang_core['check_date_enter']);
        //     return;
        // }

        $('.btn-submit').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
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
            url : url,
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
        .done(function(data) {
            console.log(data);
            if (data.result) {
                alert_float('success', data.message);
                window.location.href = site.base_url+'admin/quotation_request';
                window.location.href = "<?= $breadcrumb[0]['link'] ?>";
            } else {
                alert_float('danger', data.message);
                $('.btn-submit').removeAttr('disabled', 'disabled');
            }
        })
        .fail(function() {
            alert_float('danger', lang_core['errors']);
            $('.btn-submit').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>
<?php //$this->load->view('admin/stage_control_galvanize/script_js.php') ?>
