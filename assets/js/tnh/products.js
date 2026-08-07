function getUnits(units, select_id = false)
{
	var options = '<option></option>';
	$.each(units, function(index, el) {
		selected = el.unitid == select_id ? 'selected' : '';
		options+= '<option '+selected+' value="'+ el.unitid +'">'+el.unit+'</option>';
	});
	return options;
}

function getProcedureDetail(procedure_detail, select_id = false)
{
	var options = '<option></option>';
	$.each(procedure_detail, function(index, el) {
		selected = el.id == select_id ? 'selected' : '';
		options+= '<option '+selected+' value="'+ el.id +'">'+el.name+'</option>';
	});
	return options;
}

function getWarehouses(warehouses, select_id = false)
{
	var options = '<option></option>';
	$.each(warehouses, function(index, el) {
		selected = el.id == select_id ? 'selected' : '';
		options+= '<option '+selected+' value="'+ el.id +'">'+el.name+'</option>';
	});
	return options;
}

function totalExchange()
{
    var table_stage = $('.table-exchange tbody tr').length;
    var stt = 0;
    for (ii = 0; ii < table_stage; ii++)
    {
        stt++;
        element = $('.table-exchange tbody tr')[ii];
        $(element).find('.stt').html(stt);
    }
}

function totalSupplies()
{
	// var tableSup = $('#tb-suppliers tbody tr').length;
 //    var stt = 0;
 //    for (ii = 0; ii < tableSup; ii++)
 //    {
 //        stt++;
 //        element = $('#tb-suppliers tbody tr')[ii];
 //        $(element).find('.td-number').html(stt);
 //    }
}

function removeStageSubb(el)
{
	$(el).closest('tr').remove();
}

function removeWarehouse(el)
{
	$(el).closest('tr').remove();
}

function removeSuppliers(el) {
	$(el).closest('tr').remove();
}

function addStageSub(el, counterCur)
{
    tbb = $(el).closest('.tb-subb');
    tdNumberSub = '<td class="td-number-sub text-center"></td>';
    tdStageSub = '<td class="td-stage-sub ">'+
    	'<select name="procedure['+counterCur+'][]" id="procedure" data-placeholder="'+lang_core['choose']+'" class="procedure modal-select2" style="width: 100%;">'+getProcedureDetail(procedure_detail)+'</select>'+
    '</td>';
    tdSequenceSub = '<td>'+
    	'<input type="number" name="sequence['+counterCur+'][]" id="sequence" class="form-control sequence" value="1">'+
    '</td>';
    tdNumberDateSub = '<td>'+
    	'<input type="number" name="number_date['+counterCur+'][]" id="number_date" class="form-control number_date" value="1">'+
    '</td>';
    tdRemove = '<td class="text-center">'+
    	'<a class="fa fa-remove" onclick="removeStageSubb(this)"></a>'+
    '</td>';
    trSubb = '<tr>'+
    	tdNumberSub+
    	tdStageSub+
    	tdSequenceSub+
    	tdNumberDateSub+
    	tdRemove+
    '</tr>';

    tbb.find('tbody').append(trSubb);
    $('select.procedure').select2();
}

function addTbWarehouse(el)
{
	tbw = $(el).closest('#tb-warehouse');
	tdNumberWs = '<td class="td-number-ws text-center"></td>';
	tdWarehouse = '<td>'+
		'<select onchange="changeWarehouse(this)" name="warehouses[]" id="warehouses" data-placeholder="'+lang_core['choose']+'" class="warehouses modal-select2" style="width: 100%;">'+getWarehouses(warehouses)+'</select>'+
	'</td>';
	tdLocation = '<td>'+
		'<select name="location[]" id="location" data-placeholder="'+lang_core['choose']+'" class="location modal-select2" style="width: 100%;"></select>'+
	'</td>';
	tdRemove = '<td class="text-center">'+
    	'<a class="fa fa-remove" onclick="removeWarehouse(this)"></a>'+
    '</td>';
    trW = '<tr>'+
    	tdNumberWs+
    	tdWarehouse+
    	tdLocation+
    	tdRemove+
    '</tr>';
    tbw.find('tbody').append(trW);
    $('select.warehouses').select2();
    $('select.location').select2();
}

function changeWarehouse(el)
{
	tRow = $(el).closest('tr');
	warehouse_id = $(el).val();
	tRow.find('select.location').html('');
	if (warehouse_id) {
		$.ajax({
			url: site.base_url+'admin/items/getLocationWarehouses',
			type: 'GET',
			dataType: 'json',
			data: {
				warehouse_id: warehouse_id
			},
		})
		.done(function(data) {
			if (data) {
				tRow.find('select.location').html(data.options);
				tRow.find('select.location').val('').trigger('change');
			}
		})
		.fail(function() {
			console.log("error");
		});
	}
}

function autoCode()
{
	category = $('#category').select2().find(':selected').data('code');
	species = $('#species').select2().find(':selected').data('code');

	mode_product = $('#mode_product').val().trim();
	longs_product = intVal($('#longs').val().trim());
	wide_product = intVal($('#wide').val().trim());

	if (empty(category)) category = '';
	if (empty(species)) species = '';
	if (empty(longs_product)) longs_product = '';
	if (empty(wide_product)) wide_product = '';

	long_wide = '';
	if (longs_product != '' || wide_product != '') {
		long_wide = longs_product+'x'+wide_product;
	}

	aCode =  category+'-'+species+'-'+long_wide+'-'+mode_product;
	hand_input_code = $('#hand_input_code').prop('checked');
	if (hand_input_code == false || hand_input_code === 'false') {
		$('#code').val(aCode);
	}

	aCodeBom = category+'-'+species+'-'+long_wide;
	$('#code_bom').val(aCodeBom);
}

$(document).ready(function() {

	if (edit == 1) {
		for (i = 0; i < counter; i++) {
			ajaxSelectParams('#suppliers_'+i+'', 'admin/items/searchSuppliers', $('#suppliers_'+i+'').val());
		}
		$('select.procedure').select2();
		$('select.warehouses').select2();
		// $('select.location').select2();
	}

	$('#category, #species, #mode_product, #hand_input_code, #longs, #wide').change(function(event) {
		autoCode();
	});

	$('#customer').change(function(event) {
		dataCustomer = event.added;

		$('.div-separate-guest').hide();
		if (typeof dataCustomer !== 'undefined') {
			is_separate_guest = dataCustomer.is_separate_guest;
			if (is_separate_guest > 0) {
				$('.div-separate-guest').show();
			} else {
				$('.div-separate-guest').hide();
			}
		}
	});

	$('.btn-add-itemss').click(function(event) {
		event.preventDefault();
		tr_html = '';
		tr_html += '<tr>';
		tr_html += '<td class="stt text-center"></td>';

		tr_html += '<td>\
                        <select name="unit_exchange[]"  data-live-search="true" id="unit_exchange" class="form-control unit_exchange">\
                            '+getUnits(units, 0)+'\
                        </select>\
                    </td>';
        tr_html += '<td>\
                        <input type="number" name="number_exchange[]" id="number_exchange[]" class="form-control" value="0">\
                    </td>';
		tr_html += '<td>\
						<div class="text-center"><i class="btn btn-danger fa fa-remove remove-exchange"></i></div>\
					</td>';
		tr_html += '</tr>';

		$('.table-exchange tbody').append(tr_html);
		$('.unit_exchange').selectpicker();
        totalExchange();
	});

	$('.add-supplies').click(function(event) {
		event.preventDefault();
		tdNumber = '<td class="td-number text-center"></td>';
		tdSuppliers = '<td class="td-suppliers ">'+
			'<input type="hidden" name="counter[]" id="counter" class="form-control counter" value="'+counter+'">'+
			'<input type="text" name="suppliers['+counter+']" data-placeholder="'+lang_core['choose']+'" id="suppliers_'+counter+'" class="suppliers modal-select2" style="width: 100%;" value="">'+
		'</td>';
		tdLeadTime = '<td class="td-leadtime">'+
			'<table class="tnh-table tb-subb">'+
				'<thead>'+
					'<th style="width: 50px;" class="text-center">'+
						'<i onclick="addStageSub(this, '+counter+')" class="fa fa-plus btn btn-success add-sub-st"></i>'+
					'</th>'+
					'<th style="width: 150px;">'+lang_product['tnh_stage']+'</th>'+
					'<th style="width: 150px;">'+lang_product['tnh_sequence']+'</th>'+
					'<th>'+lang_product['tnh_number_date']+'</th>'+
					'<th class="text-center" style="width: 50px;"><i class="fa fa-trash" class="remove-sub"></i></th>'+
				'</thead>'+
				'<tbody></tbody>'+
			'</table>'+
		'</td>';
		tdActions = '<td class="td-actions text-center"><i class="btn btn-danger fa fa-remove remove-suppliers" onclick="removeSuppliers(this)"></i></td>';

		trSuppliers = '<tr>'+
			tdNumber+
			tdSuppliers+
			tdLeadTime+
			tdActions+
		'</tr>';
		$('#tb-suppliers tbody.t-body').append(trSuppliers);
		// ajaxSelectParams('#suppliers_'+counter+'', 'admin/items/searchSuppliers', 0);
		ajaxSelectParams('#suppliers_'+counter+'', 'admin/items/searchSuppliers', 0);
		totalSupplies();
		counter++;
	});

	$('#type_products').change(function(event) {
		type_products = $(this).val();
		if (type_products == "products") {
			// $('.col-cs-3').show();
		} else {
			// $('.col-cs-3').hide();
		}
	})


	$('.modal').on('click', '.remove-exchange', function(e) {
		e.preventDefault();
		$(this).closest('tr').remove();
		totalExchange();
	});
});