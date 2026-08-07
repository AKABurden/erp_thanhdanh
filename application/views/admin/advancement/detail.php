<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #wrapper{
        min-height: calc(100vh - 100px);
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
                                                <select class="room_id" onchange="changeRoom(this)" name="room_id" id="room_id" style="width: 100%">
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
                                   <table id="tb-item" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                                       <thead>
                                       <tr>
                                           <th class="text-center" style="width: 30px;">
                                               <a class="btn btn-info btn-icon add-item"><i
                                                           class="fa fa-plus"></i></a></th>
                                           <th style="width: 100px;"><?= lang('Vị trí từ') ?></th>
                                           <th style="width: 150px;"><?= lang('Vị trí đến') ?></th>
                                           <th style="width: 100px;"><?= lang('Thời gian tối thiểu(tháng)') ?></th>
                                           <th style="width: 50px;"><?= lang('actions') ?></th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       <?php $counter = 0;
                                       if (!empty($dtItems)){ ?>
                                       <?php foreach ($dtItems as $key => $value){ ?>
                                       <tr>
                                           <td  class="text-center"><?= (++$key) ?></td>
                                           <td>
                                               <div class="code_item">
                                                   <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                   <input type="hidden" name="advancement_item_id[<?= $counter ?>]" class="advancement_item_id" value="<?= $value['id'] ?>">
                                               </div>
                                               <input type="text" name="role_id_from[<?= $counter ?>]" id="role_id_from_<?= $counter ?>" style="width: 100%" class="role_id_from" value="<?= ($value['role_id_from']) ?>">
                                           </td>
                                            <td>
                                                <div class="td-role_id_to">
                                                    <input type="text" name="role_id_to[<?= $counter ?>]" id="role_id_to_<?= $counter ?>" style="width: 100%"  class="role_id_to" value="<?= ($value['role_id_to']) ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="td-min_time_month"><input type="text" name="min_time_month[<?= $counter ?>]" class="min_time_month form-control number-format" value="<?= $value['min_time_month'] ?>"></div>
                                            </td>
                                            <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                       </tr>
                                    <?php $counter++;} ?>
                                <?php } ?>
                                </tbody>
                                </table>
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
    var counter = "<?= $counter ?? 0 ?>";
    <?php if (!empty($dtItems)){ ?>
    <?php foreach ($dtItems as $key => $value){ ?>
        ajaxSelectCallBack($(`#role_id_from_<?= $key ?>`), `admin/roadmap_advancement/searchRoleByRoom`, <?= $value['role_id_from'] ?>,"<?= $value['name_position_from'] ?>");
        ajaxSelectCallBack($(`#role_id_to_<?= $key ?>`), `admin/roadmap_advancement/searchRoleByRoom`, <?= $value['role_id_to'] ?>,"<?= $value['name_position_to'] ?>");
    <?php } ?>
    <?php } ?>

    $('.add-item').on('click', function(event) {
        console.log(counter);
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt text-center"></div>
                </td>
                <td>
                   <div class="code_item">
                       <input type="hidden" name="counter[]" class="counter" value="${counter}">
                       <input type="hidden" name="advancement_item_id[${counter}]" class="advancement_item_id" value="0">
                   </div>
                   <input type="text" name="role_id_from[${counter}]" required id="role_id_from_${counter}" class="role_id_from" value="" style="width: 100%">
                </td>
                <td>
                     <input type="text" name="role_id_to[${counter}]" required id="role_id_to_${counter}" class="role_id_to" value="" style="width: 100%">
                </td>
                <td>
                    <input type="text" name="min_time_month[${counter}]"
                           class="min_time_month form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-item tbody").append(trItem);
        ajaxSelectCallBack($(`#role_id_from_${counter}`), `admin/roadmap_advancement/searchRoleByRoom`, 0);
        ajaxSelectCallBack($(`#role_id_to_${counter}`), `admin/roadmap_advancement/searchRoleByRoom`, 0);
        counter++;
        totalItem();
    });
   function removeRow(_this) {
       tr = $(_this).closest('tr');
       tr.remove();
       totalItem();
   }
    function totalItem()
    {
        tb = '#tb-item tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;

        for (i = 0; i < n; i++)
        {
            stt++;
            element = $(tb)[i];
            $(element).find('.stt').html(stt);
        }
    }

    function changeRoom(_this){
        if ($("#tb-item tbody tr").length > 0) {
            bootbox.confirm("Thay đổi sẽ xóa tất cả thông tin chi tiết", function (result) {
                if (result) {
                    $("#tb-item tbody").html('');
                }
            });
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