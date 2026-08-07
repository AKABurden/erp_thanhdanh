<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    th {
        background: #047dde !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'service-form', 'class' => '_transactionss_form service-form'));
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <?php $this->load->view('admin/service/template'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    function ajaxSelectCallBack_servicess(element, url, id, types = '') {

        if (id > 0) {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id,
                        quietMillis: 15,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/',
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    $('._transactionss_form').on('submit', (e) => {
        var items = $('table.invoice-items-table tbody tr');
        if (items.length == 0) {
            alert_float('danger', '<?= _l('Bạn chưa điền nội dung phát sinh') ?>');
            return;
        }
        var a = confirm("<?= _l('ch_you_want_update') ?>");
        if (a === false) {
            e.preventDefault();
        } else {
            $('#export-form').submit();
        }
    });
    _validate_form($('.service-form'), {
        clientid: "required",
        number: "required",
    });

    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    $(function() {
        // validate_invoice_form();
        // Init accountacy currency symbol
        // init_currency();
        // Project ajax search
        $('select.tax_ch').change();
        init_ajax_project_search_by_customer_id();
        // Maybe items ajax search
        init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
    });
    <?php if(empty($invoice)){ ?>
    change_item_load_v3();
    <?php } ?>
    // $('#clientid').change(function(e) {
    //     // se_contract();
    //     change_item_load_v3();
    // });
    $(document).on('change', 'select.tax_ch', function(e) {
        var tax_id = $(this).val();
        var tax_rate = parseInt($(this).find('option:selected').attr('data-taxrate'));
        var current_row = $(this).parents('tr');
        if (isNaN(tax_rate)) tax_rate = 0;
        $(this).parents('tr').find('input.tax_rate').val(tax_rate);
        getTotalPrice();
    });
    $(document).on('change', 'input#price', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#price', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('click', 'input#price', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#quanliti', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('click', 'input#quanliti', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('change', 'input#quanliti', function(e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal =Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#discount', function(e) {
        getTotalPrice();
    });
    $(document).on('change', '#d', function(e) {
        $('.discount').val(0);
        getTotalPrice();
    });
    function getTotalPrice() {
        var items = $('table.invoice-items-table tbody').find('tr');
        var totalQuantity = 0;
        var totalPrice = 0;
        var discount = 0;
        var vat = $('.tax_rate').val();
        if (isNaN(vat)) vat = 0;
        $.each(items, (index, value) => {
            totalQuantity += parseFloat($(value).find('.quanliti').val().replace(/\,/g, ''));
            totalPrice += parseFloat($(value).find('.subtotalss').text().replace(/\,/g, ''));
        });
        var discount_value = parseFloat($('.discount').val().replace(/\,/g, ''));
        if ($('input#d').is(':checked')) {
            discount = discount_value;
        }else
        {
            discount = totalPrice*discount_value/100;
        }
        console.log(discount);
        totalvat = (totalPrice - discount)*vat/100;
        total_vat = (totalPrice - discount) + (totalPrice - discount)*vat/100;
        $('.quantili_all').text(formatNumber(totalQuantity));
        $('.total_novat').text(formatNumber(totalPrice));
        $('.vat').text(formatNumber(totalvat));
        $('.total_all').text(formatNumber(total_vat));
    }
    getTotalPrice();
</script>
</body>

</html>