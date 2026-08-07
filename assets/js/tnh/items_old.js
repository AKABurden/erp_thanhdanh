function getUnits(units, select_id = false)
{
	var options = '<option></option>';
	$.each(units, function(index, el) {
		selected = el.unitid == select_id ? 'selected' : '';
		options+= '<option '+selected+' value="'+ el.unitid +'">'+el.unit+'</option>';
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

$(document).ready(function() {
	$('.btn-add-items').click(function(event) {
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
                        <input type="number" name="number_exchange[]" id="number_exchange[]" class="form-control" value="0" min="0"  step="0.1">\
                    </td>';
		tr_html += '<td>\
						<div class="text-center"><i class="btn btn-danger fa fa-remove remove-exchange"></i></div>\
					</td>';
		tr_html += '</tr>';

		$('.table-exchange tbody').append(tr_html);
		$('.unit_exchange').selectpicker();
        totalExchange();
	});


	$('.modal').on('click', '.remove-exchange', function(e) {
		e.preventDefault();
		$(this).closest('tr').remove();
		totalExchange();
	});
});