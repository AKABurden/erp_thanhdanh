
$(".next").click(function(e){
	$('.event_tab').removeClass('active');
	$('.fieldset').removeClass('active');

	var currentE = $(e.currentTarget);
	var number_tab = currentE.parent().parent().parent().attr('role-fieldset');
	var stt_next = currentE.attr('data-stt');
	if(!stt_next) {
		stt_next = 1;
	}
	var next_tab = Number(number_tab)+Number(stt_next);
	$('[active-tab="'+next_tab+'"]').addClass('active');
	$('[role-fieldset="'+next_tab+'"]').addClass('active');
});

$(".previous").click(function(e){
	$('.event_tab').removeClass('active');
	$('.fieldset').removeClass('active');

	var currentE = $(e.currentTarget);
	var number_tab = currentE.parent().parent().parent().attr('role-fieldset');
	var stt_next = currentE.attr('data-stt');
	if(!stt_next) {
		stt_next = 1;
	}
	var next_tab = Number(number_tab)-Number(stt_next);
	$('[active-tab="'+next_tab+'"]').addClass('active');
	$('[role-fieldset="'+next_tab+'"]').addClass('active');
});

$(".event_tab").click(function(e){
	var currentE = $(e.currentTarget);
	$('.event_tab').removeClass('active');
	currentE.addClass('active');
	currentE.removeClass('validateForm-error');
	var active_tab = currentE.attr('active-tab');

	$('.fieldset').removeClass('active');
	$('[role-fieldset="'+active_tab+'"]').addClass('active');
});

//Hoàng CRM - check ValidateForm
function checkValidateForm() {
    var template_count = $('.has-error');
    $.each(template_count, function(i,v) {
        var curent_number = $(this).parents('.fieldset').attr('role-fieldset');
        $('[active-tab="'+curent_number+'"]').addClass('validateForm-error');
    });
}
function checkValidateForm_suppliers() {
    var template_count = $('.has-error');
    $.each(template_count, function(i,v) {
        var curent_number = $(this).parents('.fieldset').attr('role-fieldset');
        $('[active-tab="'+curent_number+'"]').addClass('validateForm-error');
    });
}
//end