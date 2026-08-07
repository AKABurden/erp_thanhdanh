<style type="text/css">
  
@import url(https://fonts.googleapis.com/css?family=Open+Sans);
a{text-decoration:none;}
li {list-style-type:none;}

p{font:1em 'Open Sans', sans-serif;}
.tit-nivel3{font:1.5em 'Open Sans', sans-serif;}

ul.tabs {
  overflow: auto;
  height: 300px;
}

ul.tabs li {
  margin: 0;
  cursor: pointer;
  padding: 10px;
  font:1em 'Open Sans', sans-serif;
}

.tab_last {
    background:#900!important;
    margin-top: 50px!important;
    color:#fff!important;
    font:1em 'Open Sans', sans-serif;
}

ul.tabs li:hover {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

ul.tabs li.active {
  background:#bbbbbb2e;
  color:#2885d0;
  border-radius: 5px;
}

.tab_content {
  display: none;
}
.tab_container {
    height: 460px;
  }

.tab_drawer_heading { display: none; }

@media screen and (max-width: 620px) {

  .tab_container {
    width: 100%;
  }

  .tabs {
    display: none;
  }
  .tab_drawer_heading {
    background:#1a1a1a;
    color: #fff;
    border-top: 1px solid #333;
    margin: 0;
    padding: 5px 20px;
    display: block;
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .d_active {
    background-color:#111;
    color: #fff!important;
  }
}
/*hoàng crm bổ xung*/
.panel_box {
  margin: 0;
  box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
}
.center {
  text-align: center;
}
.tab_container i {
  cursor: pointer;
}
.table-scroll {
  max-height: 310px;
  overflow: auto;
}
.wap-right_RFQ {
  height: 400px;
}
.tab-pane{
  display: none;
}
.tab-pane.active{
  display: block;
}
.nav-tabs {
  margin-bottom: 0; 
  background: 0 0; 
  border-radius: 0;
}
.thead-row {
  text-align: center;
  text-transform: uppercase;
  font-weight: 700 !important;
  line-height: 40px;
  background: #3f9ad6;
  color: #fff;
}
.mtop25 {
  margin-top: 25px !important;
}
.padding20 {
  padding: 20px 0 !important;
}
.thead-col {
  text-align: center;
  white-space: unset;
}
.input-col {
  text-align: center;
  border: 0 !important;
  outline: 0 !important;
  border-bottom: 1px solid #9e9e9e !important;
}
.border-bottom {
  border-bottom: 1px solid #9e9e9e !important;
}
.mbottom {
  margin-bottom: 15px;
}
.padding0 {
  padding: 0 !important;
}
.boder-lr {
  border-right: 1px solid #a4a4a4;
  border-left: 1px solid #a4a4a4;
}
.table.table-striped tbody td{
border: 1px solid #f0f0f0;
}
.text-muted {
  color: red !important;
}
td .input-group{
    width: 100%
}
</style>
<div class="modal fade" id="record_reduce" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="book-title"><?php echo $title ?> </span>
                    </h4>
            </div>
            <?php $value = (isset($items) ? $items->id : ''); ?>
            <?php
            echo form_open(admin_url('record_increased/add/'.$value), array('id' => 'record_increased-form', 'class' => '_transaction_form invoice-form'));
            ?>
                <div class="modal-body" style="background: #f1f1f1">
                    <div class="col-md-12">
                        <div class="panel_s panel_box">
                            <div class="panel-body">
                                <div class="tab_container" style="position: relative;">
                                    <div class="col-md-4  pull-left">
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Thông tin</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="well well-sm">
                                                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 40%">
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('Ngày chứng từ'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td >
                                                                        <?php $value = (isset($items) ? _d($items->date_of_recording_increases) : _d(date('Y-m-d'))); ?>
                                                                        <?php echo render_date_input('date_of_recording_increases','',$value); ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td >
                                                                        <label for="date" class="control-label">
                                                                            <small class="req text-danger">* </small>
                                                                            <?php echo _l('Lý do ghi giảm'); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <?php $value = (isset($items) ? $items->note : ''); ?>
                                                                        <?php echo render_textarea('note','',$value); ?>
                                                                    </td>
                                                                </tr> 
                                                            </tbody>
                                                        </table>  
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="height: calc(400px - 0px);overflow: auto;">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-record_increased table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;" class="text-left">
                                                        STT
                                                    </th>
                                                    <th style="width: 250px;"><?=_l('Tài sản')?></th>
                                                    <th style="width: 100px;"><?php echo _l('Đơn vị sử dụng'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('Nguyên giá'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('Giá trị tính khấu hao'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('Hao mòn lũy kế'); ?></th>
                                                    <th style="width: 200px;"><?php echo _l('Giá trị còn lại'); ?></th>
                                                    <th style="width: 50px;"><?php echo _l('Tác vụ'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    <?php $j = 0; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style="position: absolute;bottom: 0;width: 100%;">
                                        <div class="">
                                            <table class="table tnh-tb noMargin table-color_sum">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 30%">
                                                            <span class="bold"><?php echo _l('Tổng phiếu'); ?> :</span>
                                                        </td>
                                                        <td style="width: 20%" class="total_quantity_all">
                                                        </td>
                                                        <td style="width: 30%">
                                                            <span class="bold"><?php echo _l('total_price'); ?> :</span>
                                                        </td>
                                                        <td style="width: 20%"  class="text-right total_price">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <div class="clearfix"></div>            
            <div class="modal-footer">
                <button type="submit" class="btn btn-info"  id="submit" autocomplete="off"><?=_l('submit')?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    //load trang voi action
    <?php if(!empty($items)){ ?>
    appendtype();
    function appendtype()
    {
        var items = $('table.item-record_increased tbody').find('tr.item');
        $.each(items, (index,value)=>{
            var ID = $('#custom_item_select_'+index).attr('data-id');
            ajaxSelectCallBack($('#custom_item_select_'+index), "<?=admin_url('record_increased/SearchItems')?>", ID,'<?=$items->id?>');
            $('#custom_item_select_'+index).trigger('change');
        });
    }
    <?php }?>
    $(function(){
        // validate_invoice_form();
        _validate_form($('#record_increased-form'), {
        date_of_recording_increases: "required",
        units_used: "required",

        property_code: "required",
        asset_name: "required",
        date_depreciation: "required",
        number_used_time: "required",
        original_price: "required",
        value_of_depreciation: "required",
        residual_value: "required",

    },add_quotes_client_s);

            function add_quotes_client_s(form) {
                var residual_value = $('#residual_value').val();
                if(residual_value == 0)
                {
                    alert('Giá trị còn lại không được bằng 0');
                }
               var data = $(form).serialize(),
                   action = form.action;
               return $.post(action, data).done(function(form) {
                    form = JSON.parse(form),
                    alert_float(form.alert_type, form.message);
                    $('#record_increased').modal('hide');
                    tAPI.draw('page');
               }), !1
            }
    });
    $(function(){
        <?php if(empty($items)){ ?>
        createTrItemfist();
        <?php } ?>
        var dt = $('.item-record_increased').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
    //     _validate_form($('#record_increased-form'), {
    //     date: "required",
    //     number: "required",
    //     suppliers_id: "required",
    //     delivery_date: "required",
    // });
    });
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    function countrow()
    {
        if(!$('table.item-record_increased tbody').find('input[value=hau]').length)
        {
            createTrItemfist();
        }
    }
    var button_create = ()=>{
        if(!$('table.item-record_increased tbody').find('input[value=hau]').length)
        {
            createTrItemfist();
        }
    }
    var createTrItem = (item,currentQuantityInput) => {
        if(typeof(item)=='undefined' || item.length==0) return;
        if( ($('table.item-record_increased tbody tr').find('input[value=' + item.id + ']#product_id').length > 0)) {
            alert_float('danger', "Chứng từ này đã được thêm, vui lòng kiểm tra lại!");
            currentQuantityInput.select2('val','');
            return;
        }
        var new_tr = currentQuantityInput.parents('tr');
        var count = new_tr.find('td > input.count').val();
        new_tr.find('td.avatar').html((Number(count)+1)+'<input type="hidden" class="id" id="product_id" name="items[' + count + '][id]" value="'+item.id+'" />');
        new_tr.find('td.date_ch').html(item.date);
        new_tr.find('td.note_ch').html(item.note)
        new_tr.find('td.amount_ch').html(formatNumber(item.total));
        new_tr.find('td.delete').html('<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>');
        getTotalPrice();
        countrow();
    }
    var uniqueArray = <?=$j?>;
    var createTrItemfist = (item) => {
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatar" style="text-align: center;"><input type="hidden"  value="hau" />'+(uniqueArray+1)+'</td>');
        var td2 = $('<td ><input type="hidden" class="count" value="'+uniqueArray+'" /><input style="width:100%;" data-placeholder="<?=_l('dropdown_non_selected_tex')?>" class="custom_item_select" id="custom_item_select_'+uniqueArray+'"  name="items[' + uniqueArray + '][custom_item_select]" name="custom_item_select_'+uniqueArray+'" style="width: 100%"><br><br><div class="color"><div></td>');
        var td3 = $('<td style="text-align: center;" class="date_ch"></td>');
        var td4 = $('<td style="text-align: left;" class="note_ch"></td>');
        var td5 = $('<td style="text-align: right;" class="amount_ch"></td>');

        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append('<td class="delete"></td');
        $('table.item-record_increased tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        <?php if(!empty($items)){ ?>
        ajaxSelectCallBack($('#custom_item_select_'+uniqueArray), "<?=admin_url('record_increased/SearchItems')?>", 0,'<?=$items->id?>');
        <?php }else{?>
        ajaxSelectCallBack($('#custom_item_select_'+uniqueArray), "<?=admin_url('record_increased/SearchItems')?>", 0);
        <?php }?>
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        uniqueArray++;
    }
    function getTotalPrice()
    {   
        var items = $('table.item-record_increased tbody').find('tr.item');
        var totalQuantity = 0;
        var totalQuantityNet = 0;
        var totalPrice = 0;
        $.each(items, (index,value)=>{
            if(!$(value).find('input[value=hau]').length)
            {
            totalQuantity++;
            totalPrice += parseFloat($(value).find('.amount_ch').text().replace(/\,/g, ''));
            }
        });
        $('.total_quantity_all').text(formatNumber(totalQuantity));
        $('#value_of_depreciation').val(formatNumber(totalPrice));
        $('#original_price').val(formatNumber(totalPrice));
        $('#value_of_depreciation').keyup();
        $('.total_price').text(formatNumber(totalPrice));
        residual_value();

    }   
    function ajaxSelectCallBack(element, url, id, types = '')
            {
                if (id > 0)
                {
                    $(element).val(id).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: false,
                        initSelection: function (element, callback) {
                            $.ajax({
                                type: "get", async: false,
                                url: url + '/' + id,
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
                                    type:-1,
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
                        allowClear: false,
                        ajax: {
                            url: url + '/' + $(element).val(),
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:-1,
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
    var base_url = '<?=base_url()?>';
    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        
        return  state.text ;
    }
    $('body').on('hidden.bs.modal', '#record_increased', function() {
            $('#view_new_record_increased').html('');
        });

</script>