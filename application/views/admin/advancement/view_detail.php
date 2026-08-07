<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #wrapper{
        min-height: calc(100vh - 100px);
    }
    .title_header_item{
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 8px 10px;
        background: #d2e9fd;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }
</style>
<?php echo form_open('admin/roadmap_advancement/detail/' . $id . '',
    array('id' => 'roadmap_advancement')); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel_s mbot10 H_scroll" id="">
                <div class="panel-body _buttons">
                    <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="col-md-5">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?= $title ?></div>
                            <div class="panel-body">
                                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                    <tbody>
                                    <tr>
                                        <td style="width: 25%;">
                                            <label for="number" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('Mã lộ trình'); ?>
                                            </label>
                                        </td>
                                        <td style="width: 75%;">
                                            <div class="form-group">
                                                <input type="text" name="code" value="<?=!empty($dtData) ? $dtData['code'] : ''?>" placeholder="<?= lang('Mã lộ trình') ?>" class="form-control">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <label for="room_id">Phòng ban</label>
                                        </td>
                                        <td >
                                            <?php $valueRoom = !empty($dtData) ? $dtData['room_id'] : 0; ?>
                                            <div class="form-group">
                                                <select class="room_id none-event" name="room_id" id="room_id" style="width: 100%">
                                                    <option value=""></option>
                                                    <?php if (!empty($dtRoom)){ ?>
                                                        <?php foreach ($dtRoom as $key => $value){ ?>
                                                            <option value="<?= $value['id'] ?>" <?= $value['id'] == $valueRoom ? 'selected' : '' ?>><?= $value['name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <label for="link_tranning"><?= lang('Link đào tạo') ?></label>
                                        </td>
                                        <td>
                                            <input type="text" name="link_tranning" value="<?=!empty($dtData) ? $dtData['link_tranning'] : ''?>" placeholder="<?= lang('Link đào tạo') ?>" class="form-control link_tranning">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <label for="note">Ghi chú</label>
                                        </td>
                                        <td>
                                            <textarea name="note" cols="4" rows="4" class="form-control link_tranning"><?=!empty($dtData) ? $dtData['note'] : ''?></textarea>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?= _l('Thông tin chi tiết') ?></div>
                            <div class="panel-body" style="margin-bottom: 25px">
                                <div class="col-md-12 table-responsive">
                                    <?php if (!empty($dtItems)){ ?>
                                    <?php foreach ($dtItems as $key => $value){ ?>
                                         <div class="title_header_item"><div><?= ++$key ?>. <?= $value['name_position_from'] ?> -> <?= $value['name_position_to'] ?></div><div  style="cursor: pointer;display: none;"><i
                                                     class="fa fa-plus"></i></div></div>
                                            <input type="hidden" name="advancement_item[]" value="<?= $value['id'] ?>">
                                            <?php
                                                $dtItemsChildNew = $dtItemsChild[$value['id']] ?? [];
                                                $dtItemsChildKpiNew = $dtItemsChildKpi[$value['id']] ?? [];
                                            ?>
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a data-toggle="tab" href="#tab_info_other_<?= $value['id'] ?>">Điều kiện năng lực</a></li>
                                                <li><a data-toggle="tab" href="#tab_info_kpi_<?= $value['id'] ?>">Điều kiện KPI</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div id="tab_info_other_<?= $value['id'] ?>" class="tab-pane fade in active">
                                                    <table id="tb-item-child-<?= $value['id'] ?>" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 30px;"><i style="cursor: pointer;" onclick="addItemChild(this,<?= $value['id'] ?>)"
                                                                    class="fa fa-plus"></i></th>
                                                            <th style=""><?= lang('Tiêu chí') ?></th>
                                                            <th style="width: 50px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                         <?php if (!empty($dtItemsChildNew)){ ?>
                                                            <?php foreach ($dtItemsChildNew as $k => $v){ ?>
                                                                 <tr>
                                                                     <td>
                                                                         <div class="stt text-center"><?= (++$k) ?></div>
                                                                     </td>
                                                                     <td>
                                                                         <div class="code_item">
                                                                             <input type="hidden" name="advancement_item_child_id[<?= $value['id'] ?>][]" class="advancement_item_child_id" value="<?= $v['id'] ?>">
                                                                             <input type="hidden" name="type_child[<?= $value['id'] ?>][]" class="type_child" value="<?= $v['type'] ?>">
                                                                         </div>
                                                                         <input type="text" name="name_child[<?= $value['id'] ?>][]"
                                                                                class="name_child form-control"
                                                                                value="<?= $v['name'] ?>">
                                                                     </td>
                                                                     <td>
                                                                         <div class="td-actions text-center"><a onclick="removeRowChild(this,<?= $value['id'] ?>)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></span>
                                                                         </div>
                                                                     </td>
                                                                 </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="tab_info_kpi_<?= $value['id'] ?>" class="tab-pane fade in">
                                                    <table id="tb-item-child-kpi-<?= $value['id'] ?>" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 30px;"><i style="cursor: pointer;" onclick="addItemChildKPI(this,<?= $value['id'] ?>)"
                                                                                                            class="fa fa-plus"></i></th>
                                                            <th style=""><?= lang('Tiêu chí') ?></th>
                                                            <th style="width: 50px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php if (!empty($dtItemsChildKpiNew)){ ?>
                                                            <?php foreach ($dtItemsChildKpiNew as $k => $v){ ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt text-center"><?= (++$k) ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="code_item">
                                                                            <input type="hidden" name="advancement_item_child_kpi_id[<?= $value['id'] ?>][]" class="advancement_item_child_kpi_id" value="<?= $v['id'] ?>">
                                                                            <input type="hidden" name="type_child_kpi[<?= $value['id'] ?>][]" class="type_child_kpi" value="<?= $v['type'] ?>">
                                                                        </div>
                                                                        <input type="text" name="name_child_kpi[<?= $value['id'] ?>][]"
                                                                               class="name_child_kpi form-control"
                                                                               value="<?= $v['name'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-actions text-center"><a onclick="removeRowChildKPI(this,<?= $value['id'] ?>)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <input type="hidden" name="view_detail" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        ajaxSelectCallBack($('#role_id'), `admin/roadmap_advancement/searchRoleByRoom`, 0);
        $("#room_id").select2({
            placeholder: "Chọn phòng ban",
        });
        _validate_form($('#roadmap_advancement'), {
            code: "required",
            room_id: "required",
        },db);
    })

    function db(form) {

        $('.add').attr('disabled', 'disabled');
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
                    window.location.href = site.base_url+'admin/roadmap_advancement';
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
    function addItemChild(_this, id){
        trItem = `<tr>
                <td>
                    <div class="stt text-center"></div>
                </td>
                <td>
                   <div class="code_item">
                       <input type="hidden" name="advancement_item_child_id[${id}][]" class="advancement_item_child_id" value="0">
                       <input type="hidden" name="type_child[${id}][]" class="type_child" value="1">
                   </div>
                    <input type="text" name="name_child[${id}][]"
                           class="name_child form-control"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><a onclick="removeRowChild(this,${id})" href="javascript:void(0)" class="fa fa-remove remove-row"></a></span>
                    </div>
                </td>
            </tr>
        `;
        $(`#tb-item-child-${id} tbody`).append(trItem);
        totalItemChild(id);
    }
    function removeRowChild(_this,id) {
        tr = $(_this).closest('tr');
        tr.remove();
        totalItemChild(id);
    }
    function totalItemChild(id)
    {
        tb = `#tb-item-child-${id} tbody tr:not("[class^=not-tr]")`;
        var n = $(tb).length;
        var stt = 0;
        for (i = 0; i < n; i++)
        {
            stt++;
            element = $(tb)[i];
            $(element).find('.stt').html(stt);
        }
    }

    function addItemChildKPI(_this, id){
        trItem = `<tr>
                <td>
                    <div class="stt text-center"></div>
                </td>
                <td>
                   <div class="code_item">
                       <input type="hidden" name="advancement_item_child_kpi_id[${id}][]" class="advancement_item_child_kpi_id" value="0">
                       <input type="hidden" name="type_child_kpi[${id}][]" class="type_child_kpi" value="2">
                   </div>
                    <input type="text" name="name_child_kpi[${id}][]"
                           class="name_child_kpi form-control"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><a onclick="removeRowChildKPI(this,${id})" href="javascript:void(0)" class="fa fa-remove remove-row"></a></span>
                    </div>
                </td>
            </tr>
        `;
        $(`#tb-item-child-kpi-${id} tbody`).append(trItem);
        totalItemChildKPI(id);
    }
    function removeRowChildKPI(_this,id) {
        tr = $(_this).closest('tr');
        tr.remove();
        totalItemChildKPI(id);
    }
    function totalItemChildKPI(id)
    {
        tb = `#tb-item-child-kpi-${id} tbody tr:not("[class^=not-tr]")`;
        var n = $(tb).length;
        var stt = 0;
        for (i = 0; i < n; i++)
        {
            stt++;
            element = $(tb)[i];
            $(element).find('.stt').html(stt);
        }
    }

    function ajaxSelectCallBack(element, url, id,text = '', types = '')
    {
        if (id != 0)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function (element, callback) {
                    if (id && text) {
                        callback({
                            id: id,
                            text: text
                        });
                    } else {
                        $.ajax({
                            type: "get", async: false,
                            url: site.base_url + url + '/' + $(element).val(),
                            dataType: "json",
                            success: function (data) {
                                callback(data.row);
                            }
                        });
                    }
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            room_id: $("#room_id").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            room_id: $("#room_id").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }
</script>
</body>
</html>