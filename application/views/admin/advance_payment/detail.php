<div class="modal fade" id="advance_payment" role="dialog">
 <div class="modal-dialog modal-lg">
  <!-- Modal content-->
  <div class="modal-content">
        <?php 
        $disabled = array();
        if(isset($items))
        {
        $disabled = array('disabled'=>true);
        }
        echo form_open(admin_url('advance_payment/add/'), array('id' => 'payment-form'));
         ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('Phiếu tạm ứng / sec'); ?> </span>
            </h4>
          </div>
        <div class="modal-body" style="height:auto">
            <?php
            if(isset($items))
            {
            ?>
            <input type="text" name="id" class="hide" value="<?=$items->id?>">
            <?php 
            }
            ?>
            <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                <tbody>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_code_p'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
                                <?php $value = (isset($items) ? $items->code : $code); ?>
                                <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?=$value?>">
                            </div>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_date_p'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                            <?php echo render_date_input('date','',$value); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;" class="hide">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_objects'); ?>
                            </label>
                        </td>
                        <td class="hide">
                            <?php $list_objects = array(
                                array('id'=>1,
                                      'name'=>_l('ch_IN_client')),
                                array('id'=>2,
                                      'name'=>_l('ch_IN_suppliers')),
                                array('id'=>3,
                                      'name'=>_l('ch_IN_staff')),
                                array('id'=>4,
                                      'name'=>_l('ch_IN_other')),
                            ); ?>
                            <?php echo render_select('objects',$list_objects,array('id','name'),'',3,$disabled); ?>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger hide ch_list_objects">* </small>
                                <?php echo _l('ch_list_objects'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="append_id_object">
                                <input data-placeholder="<?=_l('ch_list_objects')?>" name="objects_id" style="width: 100%" id="objects_id">
                            </div>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_costs'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $id_costs = (isset($items) ? $items->id_costs : ''); ?>
                            <?php echo render_select('id_costs',$costs,array('id','name'),'',$id_costs); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('Phương thức thanh toán chuyển'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $paymode_c = (isset($items) ? $items->paymode_c : ''); ?>
                            <?php echo render_select('paymode_c',$payment_modes,array('id','name'),'',$paymode_c); ?>
                        </td>
                        <td style="width: 17%;">
                            <label for="date" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('Phương thức thanh toán nhận'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $paymode_n = (isset($items) ? $items->paymode_n : ''); ?>
                            <?php echo render_select('paymode_n',$payment_modes,array('id','name'),'',$paymode_n); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('expense_add_edit_amount'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                            <input type="text" id="votes_total" onchange="formatNumBerKeyUp(this)" name="total" class="form-control " value="<?=$total?>">
                        </td>
                        <td style="width: 17%;">
                            <label for="number" class="control-label">
                                <?php echo _l('note'); ?>
                            </label>
                        </td>
                        <td>
                            <?php $notes = (isset($items) ? $items->note : ''); ?>
                            <textarea rows="3" id="note" name="note" class="form-control" value=""><?=$notes?></textarea>
                        </td>
                        
                    </tr>
                </tbody>
            </table>
        <div class="clearfix">  </div>
    </div>
    <div class="modal-footer">
        <!-- data-loading-text="<?=_l('wait_text')?>" -->
        <button type="submit" class="btn btn-info"  id="submit" autocomplete="off"><?=_l('submit')?></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
        <!--  -->
    </div>
</form>
</div>
</div>
</div>

<script type="text/javascript">
    function validate_form() {
    _validate_form($('#payment-form'), {
        code_vouchers: "required",
        date: "required",
        objects: "required",
        paymode_n: "required",
        paymode_c: "required",
        payment: "required",
        objects_id: "required",
        id_costs: "required",
        total: "required",
    },add_payment);
    }
    $(function(){
        validate_form();
    });    
    <?php
    if(!empty($items))
    {?>
        $('#objects').change();
    <?php 
    }
    ?>   
    function add_payment(form) {
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        var type_vouchers = $('#type_vouchers').val();
        var id_vouchers_ar = 1;
        var total = unformat_number($('#votes_total').val());
        if(Number(total) <= 0)
        {
            $('#submit').button('reset');
            alert('<?=_l('Giá trị không hợp lệ')?>');return;
        }
        $('#submit').button('loading');
        var data = $(form).serialize(),
             action = form.action;
        return $.post(action, data).done(function(form) {
             form = JSON.parse(form),
             alert_float(form.alert_type, form.message);
             if(form.success)
             {
                 tAPI.draw('page');
                 $('#advance_payment').modal('hide');
             }

        }), !1
    }
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
    function unformat_number(number)
    {
        var _number=0;
        if(number)
        {
            _number=number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
        function ajaxSelectCallBack(element, url, id, types = '')
            {
               
                if (id > 0)
                {
                    $(element).val(id).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: true,
                        initSelection: function (element, callback) {
                            $.ajax({
                                type: "get", async: false,
                                url: url + '/' + id+'/'+$('#objects').val(),
                                dataType: "json",
                                success: function (data) {
                                    callback(data.results[0]);
                                }
                            });
                        },
                        ajax: {
                            url: url,
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#objects').val(),
                                    types: types,
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
                        },
                            formatResult: repoFormatSelection,
                            formatSelection: repoFormatSelection,
                            dropdownCssClass: "bigdrop",
                            escapeMarkup: function (m) { return m; }
                    });
                } else {
                    $(element).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: true,
                        ajax: {
                            url: url + '/',
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#objects').val(),
                                    types: types,
                                    term: term,
                                    limit: 50
                                };
                            },
                            results: function (data, page) {
                                if(data.results != null) {
                                    return { results: data.results };
                                } else {
                                    return { results: [{code_client:'',id: '', text: 'No Match Found'}]};
                                }
                            }
                        },
                        formatResult: repoFormatSelection,
                        formatSelection: repoFormatSelection,
                        dropdownCssClass: "bigdrop",
                        escapeMarkup: function (m) { return m; }
                    });
                }
            }
    $(function(e){
    <?php
    if(empty($items))
    {?>
        ajaxSelectCallBack($('#objects_id'), "<?=admin_url('other_payslips/SearchClient')?>", 0);
    <?php 
    }else{ ?>
        ajaxSelectCallBack($('#objects_id'), "<?=admin_url('other_payslips/SearchClient')?>", <?=$items->staff?>);
    <?php }
    ?>
    })

    function repoFormatSelection(state) {
        var id = $('#objects').val();
        if(id == 3)
        {
        return state.text;
        }
        return '['+state.code_client+'] ' + state.text;
    }

</script>