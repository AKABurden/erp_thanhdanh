<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    th {
        background: #047dde !important;
    }

    .select2-choice {
        height: auto !important;;
        min-height: 35px !important;;
    }

    .dz-success-mark {
        display: none;
    }

    .dz-error-mark {
        display: none;
    }

    .dz-details {
        height: unset;
    }

    .dz-details,
    .dz-details:hover {
        height: unset;
    }

    .dropzoneDragArea {
        padding: 40px;
    }
</style>
<div id="wrapper">
    <div class="content">
        <h4 class="bold font-medium">
			<?php echo $title; ?>
        </h4>
        <div class="row">
			<?php echo form_open_multipart($this->uri->uri_string(), array(
				'id' => 'suggestion-form',
				'class' => 'suggestion-form',
				'style' => 'min-height:auto;background-color:#fff;'
			)); ?>
            <div class="col-md-12">
				<?php $this->load->view('admin/suggestion/template'); ?>
            </div>
			<?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).on('change', '.custom_item_select', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var id = $(currentQuantityInput).val();
        if (id == '') {} else {
            // var type = $('option:selected', currentQuantityInput).attr('data-id');
            var type = currentQuantityInput.select2('data').type;
            $.post(admin_url + 'import/get_items/' + id + '/' + type, {
                [csrfData['token_name']]: csrfData['hash']
            }, function (item) {
                var item = JSON.parse(item);
                currentQuantityInput.parents('tr').find('td.unit_name').html(item.unit_name);
                currentQuantityInput.parents('tr').find('td.mode_name').html(item.mode);
                currentQuantityInput.parents('tr').find('input.type_item').val(type);
            });
        }
    });
    $(document).on('change', 'input#price', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#price', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('click', 'input#price', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.quanliti').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#quanliti', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('click', 'input#quanliti', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('change', 'input#quanliti', function (e) {
        var total = unformat_number($(this).val());
        var tax_id = $(this).val();
        var quanliti = unformat_number($(this).parents('tr').find('input.price').val());
        if (isNaN(quanliti)) quanliti = 0;
        subtotal = Number(total) * Number(quanliti);
        $(this).parents('tr').find('td.subtotalss').html(formatNumber(subtotal));
        $('.total').html(formatNumber(subtotal));
        getTotalPrice();
    });
    $(document).on('keyup', 'input#discount', function (e) {
        getTotalPrice();
    });
    $(document).on('change', '#d', function (e) {
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
        totalvat = (totalPrice - discount) * vat / 100;
        total_vat = (totalPrice - discount) + (totalPrice - discount) * vat / 100;
        $('.quantili_all').text(formatNumber(totalQuantity));
        $('.total_novat').text(formatNumber(totalPrice));
        $('.vat').text(formatNumber(totalvat));
        $('.total_all').text(formatNumber(total_vat));
        $('.price_total').val(formatNumber(total_vat));
    }

    getTotalPrice();

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
    _validate_form($('.suggestion-form'), {
        type: "required",
        status: "required",
        staffid: "required",
        price: "required",
        id_payment_modes: "required",
    }, add_db);

    function add_db(form) {
        var type = $('#type').val();
        if (type == 1) {
            var items = $('table.invoice-items-table tbody tr');
            if (items.length == 0) {
                alert_float('danger', '<?= _l('Vật tư không được để rỗng') ?>');
                return;
            }
        } else {
            var staffid = $('#staffid').val();
            var price = $('#price').val();
            if (staffid == '' || staffid == 0) {
                alert_float('danger', '<?= _l('Vui lòng chọn người để xuất') ?>');
                return;
            }
            if (price == '') {
                alert_float('danger', '<?= _l('Vui lòng nhập số tiền đề xuất') ?>');
                return;
            }
        }
        // var data = $(form).serialize(),
        var url = form.action;
        // var data = $(form).serialize();
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();
        $.each(expenseDropzone.files, function (index, value) {
            formData.append('file[]', value);
        })
        $.each(formParams, function (i, val) {
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
        .done(function (form) {
            alert_float(form.alert_type, form.message);
            if (form.success) {
                window.location.href = site.base_url + 'admin/suggestion';
            }
        })
        .fail(function () {
            alert_float('danger', 'error');
        });
        return false;
        // return $.post(action, formData).done(function(form) {
        //     form = JSON.parse(form),
        //         alert_float(form.alert_type, form.message);
        //     if (form.success) {
        //         window.location.href = site.base_url + 'admin/suggestion';
        //     }
        // }), !1
    }

    Dropzone.options.expenseForm = false;
    if ($('#dropzoneFeedback').length > 0) {
        var expenseDropzone = new Dropzone('#suggestion-form', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '#dropzoneFeedback',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneFeedback',
            init: function () {
                // this.on("queuecomplete", function(file) {
                //     if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                //         $('.icon_upload_img').removeClass('hide');
                //     } else {
                //         $('.icon_upload_img').addClass('hide');
                //     }
                //     alert(123)
                // });
                this.on("addedfile", function () {
                    $('.icon_upload_img').addClass('hide');
                });
                this.on("removedfile", function () {
                    var fil = this.files;
                    if (fil.length == 0) {
                        $('.icon_upload_img').removeClass('hide');
                    }
                });
                this.on("maxfilesexceeded", function () {
                    var fil = this.files;
                    if (fil.length > 1) {
                        for (let index = 1; index < fil.length; index++) {
                            this.removeFile(this.files[index]);
                        }
                    }
                });
            },
            accept: function (file, done) {
                done();
            },
            success: function (file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }

    function ajaxSelectCallBacks_items(element, urls, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: urls + '/' + id + '/' + types,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: urls,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: urls + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [
                                    {
                                        code_client: '',
                                        id: '',
                                        text: 'No Match Found'
                                    }
                                ]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }

    var base_url = '<?= base_url() ?>';

    function repoFormatSelection(state) {
        if (!state.id) return state.text;
        return state.text + ' - ' + '(' + state.code + ')';
    }

    countrow_main();

    function countrow_main() {
        var items = $('table.invoice-items-table tbody').find('tr');
        $.each(items, (index, value) => {
            var type = $(value).find('td:nth-child(2)').find('input.type_item').val();
            var id = $('#custom_item_select_' + index).attr('data-id');
            ajaxSelectCallBacks_items($('#custom_item_select_' + index), "<?= admin_url('suggestion/SearchItems_ch') ?>", id, type);
        });
    }

    function delete_file(key, id) {
        if (confirm('Bạn có chắc muốn xóa file, file sẽ bị xóa khỏi dữ liệu của bạn')) {
            $('.file_' + key).remove();
			<?php if ((isset($invoice)) && is_numeric($invoice->id)) { ?>
            $.get(admin_url + 'suggestion/delele_file/' + id + '/', function (data) {})
			<?php } ?>
        }
    }

    function delete_file_images(key, id) {
        if (confirm('Bạn có chắc muốn xóa file, file sẽ bị xóa khỏi dữ liệu của bạn')) {
            $('.id_images' + key).remove();
			<?php if ((isset($invoice)) && is_numeric($invoice->id)) { ?>
            $.get(admin_url + 'suggestion/delele_file/' + id + '/', function (data) {})
			<?php } ?>
        }
    }
</script>
</body>
</html>