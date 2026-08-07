function totalBusinessPlan()
{
    tb = '#tb-business-plan tbody tr:not("[class^=not-tr]")';
    var table = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    count_errors = 0;
    for (ii = 0; ii < table; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        quantity = intVal($(element).find('.quantity').val());
        quantity_sub = 0;
        $.each($(element).find('.quantity_sub'), function(index, el) {
            quantity_sub+= intVal($(el).val());
        });

        if (quantity_sub > quantity) {
            $(element).find('.show-errors').html(lang_core['total_quantity_less']+ formatNumberTnh(quantity));
            count_errors++;
        } else {
            $(element).find('.show-errors').html('');
        }

        total_quantity+= quantity;
    }
    $('.th-total-quantity').html(formatNumberTnh(total_quantity));
}

function addRowShipping(counter, _this)
{
    var div = $(_this).closest('.td-date');

    html = '<div class="sb">'+
                '<div class="col-md-7" style="padding: 0px;"><input type="text" name="date_sub['+counter+'][]" id="input" class="form-control datepicker date_sub" autocomplete="off" placeholder="'+lang_core['date']+'" value="" style="width: 100%;" title=""></div>'+
                '<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub['+counter+'][]" id="input" class="form-control quantity_sub" value="0" title=""></div>'+
                '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>'+
            '</div>';
    div.find('.sub').append(html);
    totalBusinessPlan();
    init_datepicker();
}

$(document).ready(function() {
	init_editor('textarea[name="note"]');
    $('#id_branch').select2({'allowClear': true});

	var dt = $('#tb-business-plan').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        // scrollY: true,
		// scrollY: '450px',
		// scrollX: true,
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        },
	});

	$('.add-row').on('click', function(event) {
		event.preventDefault();
		td1 = '<div class="stt text-center"></div>';
		td2 = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">'+
            '<select name="items_id[]" id="items_'+counter+'" data-live-search="true" data-none-selected-text="'+ lang_core['choose'] +'" data-none-selected-text="" class="form-control items_id"><option value=""></option></select>';
        td3 = '<div class="td-image">'+
                    '<div class="preview_image" style="width: auto;">'+
                        '<div class="display-block contract-attachment-wrapper img">'+
                            '<div style="width:45px;">'+
                                '<a href="'+site.base_url+'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">'+
                                    '<div class="">'+
                                        '<img src="'+site.base_url+'assets/images/tnh/no_image.png" style="border-radius: 50%">'+
                                    '</div>'+
                                '</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
            '</div>';
        td4 = '<div class="td-item-name">'+lang_core['product_name']+'</div>';
        tdUnit = `<div class="td-unit"></div>`;
        td5 = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="0"></div>';

        // '<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub['+counter+'][]" id="input" class="form-control quantity_sub" value="0" title=""></div>'+

        td6 = '<div class="td-date">'+
                    '<div class="sub">'+
                    '<div class="sb">'+
                        '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub['+counter+'][]" id="input" class="form-control datepicker date_sub" autocomplete="off" placeholder="'+lang_core['date']+'" value="" style="width: 100%;" title=""></div>'+
                    '</div>'+
                '</div>'+
            '</div>';
        td7 = '<div class="td-note"><textarea name="note_items[]" id="note_items[]" class="form-control" rows="3"></textarea></div>';
		td8 = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

		dt.row.add( [
            td1,
            td2,
            td3,
            td4,
            tdUnit,
            td5,
            td6,
            td7,
            td8
        ] ).draw( false );

        selectAjax($('select#items_'+ counter +''), false, 'admin/products/searchProducts', false, attrs = 'products');
        counter++;
        totalBusinessPlan();
        init_datepicker();
	});

    $(document).on('changed.bs.select', 'select.items_id', function(event) {
        event.preventDefault();
        sl = this;
        item_id = $(sl).val();
        if (item_id) {
            tr = $(sl).closest('tr');
            option_select = $(sl).find('option:selected');
            subtext = option_select.attr('data-subtext');
            images = option_select.attr('data-image');
            var unit_name_manu = option_select.attr('data-unit_name_manu');
            if (images) {
                tr.find('.td-image a').attr('href', site.base_url+images);
                tr.find('.td-image img').attr('src', site.base_url+images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            }
            tr.find('.td-item-name').html(subtext);
            tr.find('.td-unit').html(unit_name_manu);

            lastrow = $('#tb-business-plan tbody tr')[$('#tb-business-plan tbody tr').length - 1];
            if ($(lastrow).find('select.items_id').val() > 0) {
                $('.add-row').click();
            }
        }
    });

    $(document).on('change', '.quantity, .quantity_sub', function(event) {
        totalBusinessPlan();
    });


	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
		dt.row( $(this).parents('tr') ).remove().draw();
		totalBusinessPlan();
	});

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.sb').remove();
        totalBusinessPlan();
    });


    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        $('.add-row').click();
    });

	if (edit == 0) {
		$('.add-row').click();
	}

    if (edit == 0) {
    	$(document).on('click', '.referesh-reference', function(event) {
    		event.preventDefault();
    		$.ajax({
    			url: site.base_url+'admin/business_plan/refereshReference',
    			type: 'GET',
    			dataType: 'JSON',
    			data: {
    				token: hash,
    				'referesh': 1
    			},
    		})
    		.done(function(data) {
    			if (data) {
    				$('#reference_no').val(data.reference_no);
    				alert_float('success', data.message);
    			} else {
    				alert_float('danger', 'fail');
    			}
    		})
    		.fail(function() {
    			console.log("error");
    		});
    	});
    }

	appValidateForm($('#add-business-plan'), {
		reference_no: 'required',
       	date: 'required',
        planning_cycle: 'required',
        departments: 'required',
       	plan_name: 'required',
        id_branch: 'required'
    }, db);


    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', lang_core['check_date_enter']);
            return;
        }
    	$('.add').attr('disabled', 'disabled');
        tinymce.get('note').save();
        // var data = $(form).serialize();
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
        //
        var url = form.action;
        $.ajax({
            // url : site.base_url+'admin/business_plan/add',
        	url : url,
        	type : 'POST',
        	dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
        	data: formData,
        })
        .done(function(data) {
        	if (data.result) {
        		alert_float('success', data.message);
        		window.location.href = site.base_url+'admin/business_plan';
        	} else {
        		alert_float('danger', data.message);
        		$('.add').removeAttr('disabled', 'disabled');
        	}
        })
        .fail(function() {
            alert_float('danger', 'error');
        	$('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
});

$(document).ready(function() {
    $('input.datepicker').attr('autocomplete', 'off');
    // $('.dataTables_scrollBody').on('show.bs.dropdown', function () {
    //    $('.dataTables_scrollBody').css( "overflow", "inherit" );
    // });

    // $('.dataTables_scrollBody').on('hide.bs.dropdown', function () {
    //    $('.dataTables_scrollBody').css( "overflow", "auto" );
    // })
});