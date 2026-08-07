<div id="list_other_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <?php echo form_open(admin_url('list_other/detail_join/' . $type . '/' . (!empty($list_join) ? $list_join->id : '')),
            ['id' => 'from_list_other']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        <?php $inputSelect = [];?>
                        <?php if(!empty($colums_edit)) {?>
                            <?php foreach($colums_edit  as $k => $v) {?>
                                <tr>
                                    <td style="width: 30%;">
                                        <?= lang($v['label'], $k) ?>
                                    </td>
                                    <td style="width: 70%;">
                                        <?php if($v['type'] == 'input_select') {?>
                                            <input type="text" name="<?=$k?>" id="<?=$k?>" style="width: 100%;" value="<?= !empty($list_join) ? $list_join->{$k} : '' ?>" data-placeholder="<?= lang($v['label']) ?>">
                                            <?php $inputSelect[] = [
                                                    'id' => $k,
                                                    'url_select' => $v['url_select'],
                                                    'product_customer' => !empty($v['product_customer']) ? true : false
                                            ];?>
                                        <?php }
                                        else if($v['type'] == 'select') {
                                            $dataSelect = $this->db->get($v['table'])->result_array();
                                            $value = !empty($list_join) ? $list_join->{$k} : '';
                                            echo render_select($k, $dataSelect, $v['option'], '', $value);
                                        }
                                        else if($v['type'] == 'text') {?>
                                            <div class="form-group">
                                                <input type="text" name="<?=$k?>" class="form-control" id="<?=$k?>"
                                                       value="<?= !empty($list_join) ? $list_join->{$k} : '' ?>">
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#list_other_modal').modal('show');
    init_selectpicker();
    <?php if(!empty($inputSelect)) {?>
        <?php foreach($inputSelect as $key => $value) {?>
            <?php if(!empty($value['product_customer'])) {?>
                ajaxSelectProductCustomerFormatTableCallBack('#<?=$value['id']?>', '<?=$value['url_select']?>', $('#<?=$value['id']?>').val());
            <?php } else {?>
                ajaxSelectParamsCallback('#<?=$value['id']?>', '<?=$value['url_select']?>', $('#<?=$value['id']?>').val());
            <?php }?>
        <?php }?>
    <?php } ?>
    appValidateForm($('#from_list_other'), {
        íd:'required',
        code:'required',
        name: 'required',
    }, addFrom);

    function addFrom(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serializeArray();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        })
        .done(function(data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                $('#list_other_modal').modal('hide');
                $('.add').removeAttr('disabled', 'disabled');
            } else {
                alert_float('danger', data.message);
                $('.add').removeAttr('disabled', 'disabled');
            }
        })
        .fail(function() {
            console.log("error");
        });
        return false;
    }


    function formatProductCustomer(result)
    {
        if (!result.id) return result.text; // optgroup
        tr = '';
        if (result) {
            tr+= '<td style="width: 33%;">'+result.code+'</td>';
            tr+= '<td style="width: 33%;">'+result.text+'</td>';
            tr+= '<td style="width: 33%;">'+result.company+'</td>';
        }
        tableSelect = `<table class="tnh-table table-bordered dont-responsive-table">
                        <thead>
                            <tr>
                                <th>Mã thành phẩm</th>
                                <th>Tên thành phẩm</th>
                                <th>Khách hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tr}
                        </tbody>
                    </table>`;
        return tableSelect;
    }

    function ajaxSelectProductCustomerFormatTableCallBack(element, url, id)
    {
        if (id)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                formatResult: formatProductCustomer,
                escapeMarkup: function(m) {
                    return m;
                },
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
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
                formatResult: formatProductCustomer,
                // formatSelection: formatTable,
                escapeMarkup: function(m) {
                    return m;
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
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