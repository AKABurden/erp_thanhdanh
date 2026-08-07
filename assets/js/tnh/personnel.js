function totalFamily()
{
	tbFamily = '#tb-family tbody tr:not("[class^=not-tr]")';
    var nFamily = $(tbFamily).length;
    var sttFamily = 0;

    for (i = 0; i < nFamily; i++)
    {
    	sttFamily++;
    	element = $(tbFamily)[i];
    	$(element).find('.stt-family').html(sttFamily);
    }
}

function totalLiteracy()
{
	tbLiteracy = '#tb-literacy tbody tr:not("[class^=not-tr]")';
    var nLiteracy = $(tbLiteracy).length;
    var sttLiteracy = 0;

    for (i = 0; i < nLiteracy; i++)
    {
    	sttLiteracy++;
    	element = $(tbLiteracy)[i];
    	$(element).find('.stt-literacy').html(sttLiteracy);
    }
}

function totalDepartConcurrently()
{
	tbConcurrently = '#tb-concurrently tbody tr:not("[class^=not-tr]")';
    var nConcurrently = $(tbConcurrently).length;
    var sttConcurrently = 0;

    for (i = 0; i < nConcurrently; i++)
    {
    	sttConcurrently++;
    	element = $(tbConcurrently)[i];
    	$(element).find('.stt-concurrently').html(sttConcurrently);
    }
}

function totalInsurrance()
{
    tbInsurrance = '#tb-history-insurrance tbody tr:not("[class^=not-tr]")';
    var nInsurrance = $(tbInsurrance).length;
    var sttInsurrance = 0;

    for (i = 0; i < nInsurrance; i++)
    {
        sttInsurrance++;
        element = $(tbInsurrance)[i];
        $(element).find('.td-number').html(sttInsurrance);
    }
}

function getRelationship(select_id)
{
    var option = '<option value=""></option>';
    $.each(relationship, function(index, el) {
        selected = select_id == index ? 'selected' : '';
        option+= '<option value="'+index+'">'+el+'</option>';
    });
    return option;
}

function getLiteracy(select_id)
{
    var option = '<option value=""></option>';
    $.each(literacy, function(index, el) {
        selected = select_id == index ? 'selected' : '';
        option+= '<option value="'+index+'">'+el+'</option>';
    });
    return option;
}

function getClassification(select_id)
{
    var option = '<option value=""></option>';
    $.each(classification, function(index, el) {
        selected = select_id == index ? 'selected' : '';
        option+= '<option value="'+index+'">'+el+'</option>';
    });
    return option;
}

function getLocations(select_id)
{
    var option = '<option value=""></option>';
    $.each(locations, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function getRoles(select_id)
{
    var option = '<option value=""></option>';
    $.each(roles, function(index, el) {
        selected = select_id == el.roleid ? 'selected' : '';
        option+= '<option value="'+el.roleid+'">'+el.name+'</option>';
    });
    return option;
}

function getDeparments(select_id)
{
    var option = '<option value=""></option>';
    $.each(deparments, function(index, el) {
        selected = select_id == el.departmentid ? 'selected' : '';
        option+= '<option value="'+el.departmentid+'">'+el.name+'</option>';
    });
    return option;
}

function getAllowance(select_id)
{
    var option = '<option value=""></option>';
    $.each(allowance, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option data-money="'+el.money+'" value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function getSalaryForm(select_id)
{
    var option = '<option value=""></option>';
    $.each(salaryForm, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option data-money="'+el.money+'" value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function getFormInsurrance(select_id)
{
    var option = '<option value=""></option>';
    $.each(formInsurrance, function(index, el) {
        selected = select_id == index ? 'selected' : '';
        option+= '<option value="'+index+'">'+el+'</option>';
    });
    return option;
}

function removeSalary(_this)
{
    tbSalary = $(_this).closest('.tb-salary');
    tbSalary.remove();
}

function removeAllowance(_this)
{
    trAllowance = $(_this).closest('tr');
    trAllowance.remove();
}

function removeInsurrance(_this)
{
    trInsurrance = $(_this).closest('tr');
    trInsurrance.remove();
}

function addAllowance(_this, counterSalary)
{
    tbSalary = $(_this).closest('.tb-salary');

    tdNameAllowance = '<div class="td-name-allowance text-center"><span class="label label-warning">'+langPersonnel['tnh_allowance']+'</span></div>';

    tdAllowance = '<div class="td-salary-form-allowance"><select name="salary_form_allowance['+counterSalary+'][]" data-placeholder="'+ langPersonnel['tnh_allowance'] +'" id="salary_form_allowance" class="salary_form_allowance" style="width: 100%;">'+getAllowance(0)+'</select></div>';

    tdMoneyAllowance = '<div class="td-money-allowance"><input type="text" name="money_salary_allowance['+counterSalary+'][]" id="money_salary_allowance" class="form-control money_salary_allowance money-format" style="width: 100%;" value="0"></div>';

    tdActionsAllowance = '<div class="td-actions-allowance text-center"><span class="fa fa-remove btn btn-danger" onClick="removeAllowance(this)"></span></div>';

    trAllowance = '<tr>\
        <td class="text-center">'+tdNameAllowance+'</td>\
        <td colspan="3">'+tdAllowance+'</td>\
        <td>'+tdMoneyAllowance+'</td>\
        <td>'+tdActionsAllowance+'</td>\
    </tr>';

    tbSalary.find('tbody').append(trAllowance);
    $('select.salary_form_allowance').select2();
}


function createdSalary(_this)
{

    iCounterSalary = '<input type="hidden" name="counterSalary[]" id="counterSalary" class="form-control counterSalary" value="'+counterSalary+'">';

    tdSalary = '<div class="td-salary text-center"><span class="label label-primary">'+langPersonnel['tnh_salary']+'</span></div>';

    tdFromDate = '<div class="td-from-date"><input type="text" name="from_date_salary['+counterSalary+']" id="from_date_salary" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value=""></div>';

    tdNote = '<div class="td-note"><textarea placeholder="'+langPersonnel['note']+'" name="note_salary['+counterSalary+']" id="input" class="form-control"></textarea></td>';

    tdSalaryForm = '<div class="td-salary-form"><select name="salary_form['+counterSalary+']" data-placeholder="'+ langPersonnel['tnh_salary_form'] +'" id="salary_form" class="salary_form" style="width: 100%;">'+getSalaryForm(0)+'</select></div>';

    tdMoney = '<div class="td-money"><input type="text" name="money_salary['+counterSalary+']" id="money_salary" class="form-control money_salary money-format" style="width: 100%;" value="0"></div>';

    tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger" onClick="removeSalary(this)"></span></div>';

    tableSalary = '\
        <div class="mbot10">\
            <table id="tb-salary-'+counterSalary+'" class="dt-tnh tnh-table table table-bordered table-hover dont-responsive-table dataTable no-footer tb-salary">\
                <thead>\
                    <tr>\
                        <th class="text-center" style="width: 50px;">\
                            <a class="btn btn-info btn-icon add-row-salary" data-toggle="tooltip" title="'+langPersonnel['tnh_allowance']+'" onClick="addAllowance(this, '+counterSalary+')"><i class="fa fa-plus"></i></a>\
                        </th>\
                        <th style="width: 100px;">'+langPersonnel['from_date']+'<span class="red">*</span></th>\
                        <th style="width: 200px;">'+langPersonnel['note']+'</th>\
                        <th style="width: 200px;">'+langPersonnel['tnh_salary_form']+'<span class="red">*</span></th>\
                        <th style="width: 200px;">'+langPersonnel['tnh_amount_of_money']+'</th>\
                        <th style="width: 80px;">'+ langPersonnel['actions']+'</th>\
                    </tr>\
                </thead>\
                <tbody>\
                    <tr>\
                        <td>'+tdSalary+'</td>\
                        <td>'+tdFromDate+''+iCounterSalary+'</td>\
                        <td>'+tdNote+'</td>\
                        <td>'+tdSalaryForm+'</td>\
                        <td>'+tdMoney+'</td>\
                        <td>'+tdActions+'</td>\
                    </tr>\
                </tbody>\
            </table>\
        </div>\
    ';
    counterSalary++;
    $('.append-salary').append(tableSalary);
    init_datepicker();
    $('select.salary_form').select2();
}

function createdHistoryInsurrance(_this)
{
    iCounterInsurrance = '<input type="hidden" name="counter_insurrance[]" id="counter_insurrance" class="form-control counter_insurrance" value="'+counterInsurrance+'">';

    tdNumber = '<div class="text-center td-number"></div>';

    tdMonth = '<div class="td-from-month"><input type="text" name="from_month_insurrance['+counterInsurrance+']" id="from_date_salary" placeholder="mm/yyyy" class="form-control datepicker" style="width: 100%;" value=""></div>';

    tdFormInsurrance = '<div class="td-form-surrance"><select name="form_insurrance['+counterInsurrance+']" data-placeholder="'+ langPersonnel['tnh_hinhthuc'] +'" id="form_insurrance" class="form_insurrance" style="width: 100%;">'+getFormInsurrance(0)+'</select></div>';

    tdInsurrance = '<div class="td-form-surrance"><input type="text" name="insurrance['+counterInsurrance+']" id="insurrance_'+counterInsurrance+'" placeholder="" class="insurrance" style="width: 100%;" value=""></div>';
    tdMoney = '<div class="td-money"><input type="text" name="money_insurrance['+counterInsurrance+']" id="money_insurrance" class="form-control money_insurrance money-format" style="width: 100%;" value="0"></div>';
    tdRateCompany = '<div class="td-rate-company"><input type="text" name="rate_company_insurrance['+counterInsurrance+']" id="rate_company_insurrance" class="form-control rate_company_insurrance" style="width: 100%;" value="0" readonly></div></div>';
    tdRateWorker = '<div class="td-rate-worker"><input type="text" name="rate_worker_insurrance['+counterInsurrance+']" id="rate_worker_insurrance" class="form-control rate_worker_insurrance money-format" style="width: 100%;" value="0" readonly></div></div>';

    tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger" onClick="removeInsurrance(this)"></span></div>';

    trInsurrance = '<tr>\
        <td>'+tdNumber+''+iCounterInsurrance+'</td>\
        <td>'+tdMonth+'</td>\
        <td>'+tdFormInsurrance+'</td>\
        <td>'+tdInsurrance+'</td>\
        <td>'+tdMoney+'</td>\
        <td>'+tdRateCompany+'</td>\
        <td>'+tdRateWorker+'</td>\
        <td>'+tdActions+'</td>\
    </tr>';
    counterInsurrance++;
    $('#tb-history-insurrance tbody').append(trInsurrance);
    $('select.form_insurrance').select2();
    totalInsurrance();
}


$(document).ready(function() {
	$('#gender').select2({allowClear: true});
	$('#marital_status').select2({allowClear: true});
	$('#departments').select2({allowClear: true});
	// $('#role').select2({allowClear: true});
	$('#locations').select2({allowClear: true});
    $('#workplace').select2({allowClear: true});
	$('#province_code').select2({allowClear: true});
    ajaxSelectParams('#signer', 'admin/personnel/searchPersonnel', 0, {});

	var dtFamily = $('#tb-family').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        "initComplete": function(settings, json) {
        	var t = this;
        	t.parents('.table-loading').removeClass('table-loading');
        	t.removeClass('dt-table-loading');
        	mainWrapperHeightFix();
        },
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        },
	});

	$('.add-row-family').on('click', function(event) {
		event.preventDefault();
        iCounterFamily = '<input type="hidden" name="counterFamily[]" id="counterFamily" class="form-control" value="'+counterFamily+'">';

		tdNumber = '<div class="stt-family text-center"></div>';
        tdRelationship = '<div class="td-relationship"><select name="relationship_family['+counterFamily+']" data-placeholder="'+ langPersonnel['tnh_relationship'] +'" id="relationship-family" class="relationship-family" style="width: 100%;">'+getRelationship(0)+'</select></div>';
        tdFullname = '<div class="td-fullname"><input type="text" name="fullname_family['+counterFamily+']" id="fullname_family" placeholder="'+langPersonnel['tnh_fullname']+'" class="form-control fullname_family" style="width: 100%;" value=""></div>';
        tdYearBirthday = '<div class="td-year-birthday"><input type="number" name="year_birthday_family['+counterFamily+']" id="year_birthday_family" placeholder="'+langPersonnel['tnh_year_birthday']+'" class="form-control fullname_family" style="width: 100%;" value=""></div>';
        tdCareer = '<div class="td-career"><input type="text" name="career_family['+counterFamily+']" id="career_family" placeholder="'+langPersonnel['tnh_career']+'" class="form-control career_family" style="width: 100%;" value=""></div>';
        tdAddress = '<div class="td-address"><input type="text" name="address_family['+counterFamily+']" id="address_family" placeholder="'+langPersonnel['tnh_address']+'" class="form-control address_family" style="width: 100%;" value=""></div>';
        tdTelephone = '<div class="td-telephone"><input type="text" name="telephone_family['+counterFamily+']" id="telephone_family" placeholder="'+langPersonnel['tnh_telephone']+'" class="form-control telephone_family" style="width: 100%;" value=""></div>';
		tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-family"></span></div>';

		rowNode = dtFamily.row.add( [
            tdNumber,
            tdRelationship+iCounterFamily,
            tdFullname,
            tdYearBirthday,
            tdCareer,
            tdAddress,
            tdTelephone,
            tdActions
        ] ).draw( false ).node();

        $('select.relationship-family').select2();
        counterFamily++;
        totalFamily();
	});

	$(document).on('click', '.remove-row-family', function(event) {
		event.preventDefault();
        tr = $(this).closest('tr');
		dtFamily.row( $(this).parents('tr') ).remove().draw();
		totalFamily();
	});

	var dtLiteracy = $('#tb-literacy').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        "initComplete": function(settings, json) {
        	var t = this;
        	t.parents('.table-loading').removeClass('table-loading');
        	t.removeClass('dt-table-loading');
        	mainWrapperHeightFix();
        },
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        },
	});


	$('.add-row-literacy').on('click', function(event) {
		event.preventDefault();
        iCounterLiteracy = '<input type="hidden" name="counterLiteracy[]" id="counterLiteracy" class="form-control" value="'+counterLiteracy+'">';

		tdNumber = '<div class="stt-literacy text-center"></div>';

		tdFromDate = '<div class="td-from-date"><input type="text" name="from_date_literacy['+counterLiteracy+']" id="from_date_literacy" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value=""></div>';

		tdToDate = '<div class="td-from-date"><input type="text" name="to_date_literacy['+counterLiteracy+']" id="to_date_literacy" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value=""></div>';

        tdLiteracy = '<div class="td-literacy"><select name="literacy['+counterLiteracy+']" data-placeholder="'+ langPersonnel['tnh_literacy'] +'" id="literacy" class="literacy" style="width: 100%;">'+getLiteracy(0)+'</select></div>';

        tdTrainingPlaces = '<div class="td-training-places"><input type="text" name="training_places_literacy['+counterLiteracy+']" id="training_places_literacy" placeholder="'+langPersonnel['tnh_training_places']+'" class="form-control training_places_literacy" style="width: 100%;" value=""></div>';

        tdSpecialized = '<div class="td-specialized"><input type="text" name="specialized_literacy['+counterLiteracy+']" id="specialized_literacy" placeholder="'+langPersonnel['tnh_training_places']+'" class="form-control specialized_literacy" style="width: 100%;" value=""></div>';

        tdClassification = '<div class="td-classification"><select name="classification_literacy['+counterLiteracy+']" data-placeholder="'+ langPersonnel['tnh_classification'] +'" id="classification-literacy" class="classification_literacy" style="width: 100%;">'+getClassification(0)+'</select></div>';

		tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-literacy"></span></div>';

		rowNode = dtLiteracy.row.add( [
            tdNumber,
            tdFromDate+iCounterLiteracy,
            tdToDate,
            tdLiteracy,
            tdTrainingPlaces,
            tdSpecialized,
            tdClassification,
            tdActions
        ] ).draw( false ).node();

        $('select.literacy').select2();
        $('select.classification_literacy').select2();
        counterLiteracy++;
        totalLiteracy();
        init_datepicker();
	});

	$(document).on('click', '.remove-row-literacy', function(event) {
		event.preventDefault();
        tr = $(this).closest('tr');
		dtLiteracy.row( $(this).parents('tr') ).remove().draw();
		totalLiteracy();
	});

	//

	var dtConcurrently = $('#tb-concurrently').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        "initComplete": function(settings, json) {
        	var t = this;
        	t.parents('.table-loading').removeClass('table-loading');
        	t.removeClass('dt-table-loading');
        	mainWrapperHeightFix();
        },
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        },
	});

	$('.add-row-concurrently').on('click', function(event) {
		event.preventDefault();
        iCounterConcurrently = '<input type="hidden" name="counterConcurrently[]" id="counterConcurrently" class="form-control" value="'+counterConcurrently+'">';

		tdNumber = '<div class="stt-concurrently text-center"></div>';

		tdDeparments = '<div class="td-deparments"><select name="deparments_concurrently['+counterConcurrently+']" data-placeholder="'+ langPersonnel['tnh_depart_concurrently'] +'" id="deparments_concurrently" class="deparments_concurrently" style="width: 100%;">'+getDeparments(0)+'</select></div>';

		tdLocation = '<div class="td-location"><select name="location_concurrently['+counterConcurrently+']" data-placeholder="'+ langPersonnel['tnh_vt'] +'" id="location_concurrently" class="location_concurrently" style="width: 100%;">'+getLocations(0)+'</select></div>';

		tdRole = '<div class="td-role"><select name="role_concurrently['+counterConcurrently+']" data-placeholder="'+ langPersonnel['role'] +'" id="role_concurrently" class="role_concurrently" style="width: 100%;">'+getRoles(0)+'</select></div>';

		tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-concurrently"></span></div>';

		rowNode = dtConcurrently.row.add( [
            tdNumber,
            tdDeparments+iCounterConcurrently,
            tdLocation,
            tdRole,
            tdActions
        ] ).draw( false ).node();

        $('select.deparments_concurrently').select2();
        $('select.location_concurrently').select2();
        $('select.role_concurrently').select2();
        counterConcurrently++;
        totalDepartConcurrently();
	});

	$(document).on('click', '.remove-row-concurrently', function(event) {
		event.preventDefault();
        tr = $(this).closest('tr');
		dtConcurrently.row( $(this).parents('tr') ).remove().draw();
		totalDepartConcurrently();
	});

    $(document).on('change', 'select.salary_form', function(event) {
        event.preventDefault();
        trSalary = $(this).closest('tr');
        moneySalary = intVal($(this).select2().find(':selected').data('money'));
        trSalary.find('.money_salary').val(tnhFormatMoney(moneySalary));
    });

    $(document).on('change', 'select.salary_form_allowance', function(event) {
        event.preventDefault();
        trAllowance = $(this).closest('tr');
        moneyAllowance = intVal($(this).select2().find(':selected').data('money'));
        trAllowance.find('.money_salary_allowance').val(tnhFormatMoney(moneyAllowance));
    });

    $(document).on('change', 'select.form_insurrance', function(event) {
        event.preventDefault();
        formInsurranceId = $(this).val();
        trInsurrance = $(this).closest('tr');
        counter_insurrance = trInsurrance.find('.counter_insurrance').val();
        $('#insurrance_'+counter_insurrance).val(0);
        ajaxSelectParams('#insurrance_'+counter_insurrance, 'admin/categories/searchInsurrance', 0, {'type': formInsurranceId});
    });

    $(document).on('change', 'input.insurrance', function(event) {
        event.preventDefault();
        trInsurrance = $(this).closest('tr');

        data = event.added;
        money = data.money;
        rateCompany = data.rate_company;
        rateWorker = data.rate_worker;

        trInsurrance.find('.money_insurrance').val(tnhFormatMoney(money));
        trInsurrance.find('.rate_company_insurrance').val(rateCompany);
        trInsurrance.find('.rate_worker_insurrance').val(rateWorker);
    });

    $(document).on('change', '#signer', function(event) {
        event.preventDefault();
        data = event.added;
        roleName = data.name_role;
        $('.role_signer').val(roleName);
    });

    $(document).on('change', '#province_code', function(event) {
        event.preventDefault();
        province_code = $(this).val();
        ajaxSelectParams('#hospital_registration', 'admin/categories/searchHospitalInsurrance', 0, {'province_id': province_code});
        $('#hospital_registration').val(0);
    });


	if (edit == 0) {
		$('.add-row-family').click();
		$('.add-row-literacy').click();
		$('.add-row-concurrently').click();
        $('.add-salary').click();
        $('.add-row-history-insurrance').click();
        $('.add-row-salary-new').click();
	} else if (edit == 1) {
        $('select.relationship-family').select2();
        $('select.literacy').select2();
        $('select.classification_literacy').select2();
        $('select.deparments_concurrently').select2();
        $('select.location_concurrently').select2();
        $('select.role_concurrently').select2();
        $('select.salary_form').select2();
        $('select.salary_form_allowance').select2();
        $('select.form_insurrance').select2();
        $('select.month').select2();
        $('select.year').select2();

        for (i = 0; i < counterInsurrance; i++)
        {
            trInsurrance = $('#insurrance_'+i).closest('tr');
            formInsurranceId = trInsurrance.find('.insurrance').val();
            ajaxSelectParams('#insurrance_'+i, 'admin/categories/searchInsurrance', $('#insurrance_'+i).val(), {'type': formInsurranceId});
        }

        province_code = $('#province_code').val();
        ajaxSelectParams('#hospital_registration', 'admin/categories/searchHospitalInsurrance', $('#hospital_registration').val(), {'province_id': province_code});
    }

    // $(".file-image").fileinput({
    //     language: "vi",
    //     allowedFileTypes: ['image'],
    //     showUpload: false,
    //     uploadAsync: false,
    //     // previewSettings: {
    //     //     image: {width: "100px", height: "160px"},
    //     //     other: {width: "100px", height: "160px"}
    //     // }
    // });

    // $(".attachments").fileinput({
    //     showUpload: false,
    //     language: "vi",
    //     uploadUrl: "/file-upload-batch/2",
    //     uploadAsync: false,
    //     previewFileIcon: '<i class="fas fa-file"></i>',
    //     allowedPreviewTypes: null, // set to empty, null or false to disable preview for all types
    //     previewFileIconSettings: {
    //         'docx': '<i class="fa fa-file-word-o text-primary"></i>',
    //         'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
    //         'pptx': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
    //         'jpg': '<i class="fa fa-file-image-o text-warning"></i>',
    //         'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
    //         'zip': '<i class="fa fa-file-zip-o text-muted"></i>',
    //     }
    // });

    // $("#avatar-1").fileinput({
    //     overwriteInitial: true,
    //     // maxFileSize: 1500,
    //     showClose: false,
    //     showCaption: false,
    //     browseLabel: '',
    //     removeLabel: '',
    //     browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
    //     removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
    //     removeTitle: 'Cancel or reset changes',
    //     elErrorContainer: '#kv-avatar-errors-1',
    //     msgErrorClass: 'alert alert-block alert-danger',
    //     defaultPreviewContent: '<img src="'+site.base_url+'assets/images/tnh/default-avatar-male.png" alt="Your Avatar">',
    //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
    //     allowedFileTypes: ['image']
    //     // allowedFileExtensions: ["jpg", "png", "gif"]
    // });

    appValidateForm($('#personnel'), {
        fullname: 'required',
        birthday: 'required',
        gender: 'required',
        telephone: 'required',
        email: 'required',
    }, db);

    //save db
    function db(form) {
        $('.add').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
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
            if (data.result) {
                alert_float('success', data.message);
                window.location.href = site.base_url+'admin/personnel';
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
});


function getMonth(select_id)
{
    var option = '<option value=""></option>';
    $.each(month, function(index, el) {
        if (el != ''){
            selected = select_id == index ? 'selected' : '';
            option+= '<option value="'+index+'">'+el+'</option>';
        }
    });
    return option;
}

function getYear(select_id)
{
    var option = '<option value=""></option>';
    $.each(year, function(index, el) {
        if (el != ''){
            selected = select_id == index ? 'selected' : '';
            option+= '<option value="'+index+'">'+el+'</option>';
        }
    });
    return option;
}

var dtSalaryNew = $('#tb-salary-new').DataTable({
    "language": lang_datatables,
    'searching': false,
    'ordering': false,
    'paging': false,
    "info": false,
    "initComplete": function(settings, json) {
        var t = this;
        t.parents('.table-loading').removeClass('table-loading');
        t.removeClass('dt-table-loading');
        mainWrapperHeightFix();
    },
    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
    },
});

function totalSalaryNew()
{
    tbSalaryNew = '#tb-salary-new tbody tr:not("[class^=not-tr]")';
    var nSalaryNew = $(tbSalaryNew).length;
    var sttSalaryNew = 0;

    for (i = 0; i < nSalaryNew; i++)
    {
        sttSalaryNew++;
        element = $(tbSalaryNew)[i];
        $(element).find('.stt-salary-new').html(sttSalaryNew);
    }
}

$('.add-row-salary-new').on('click', function(event) {
    event.preventDefault();
    iCounterSalaryNew = '<input type="hidden" name="counterSalaryNew[]" id="counterSalaryNew" class="form-control" value="'+counterSalaryNew+'">';

    tdNumber = '<div class="stt-salary-new text-center"></div>';

    tdMonth = `<div class="td-month"><select name="month[${counterSalaryNew}]" id="month" onchange="totalSalaryNew()" class="month" style="width: 100%;">${getMonth(0)}</select></div>`;
    tdYear = `<div class="td-year"><select name="year[${counterSalaryNew}]" id="year" onchange="totalSalaryNew()" class="year" style="width: 100%;">${getYear(0)}</select></div>`;
    tdSalary = '<div class="td-salary"><input type="text" name="salary['+counterSalaryNew+']" id="salary" class="form-control salary number-format" style="width: 100%;" value=""></div>';
    tdActive = `<div style="text-align: center" class="td-active"><input style="width: 20px" type="checkbox" name="active_new[${counterSalaryNew}]" onchange="totalSalaryNew()" class="active hide form-control"></div>`;

    tdActions = '<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-salary-new"></span></div>';

    rowNode = dtSalaryNew.row.add( [
        tdNumber,
        tdMonth+iCounterSalaryNew,
        tdYear,
        tdSalary,
        tdActive,
        tdActions
    ] ).draw( false ).node();

    $('select.month').select2();
    $('select.year').select2();
    counterSalaryNew++;
    totalSalaryNew();
    init_datepicker();
});

$(document).on('click', '.remove-row-salary-new', function(event) {
    event.preventDefault();
    tr = $(this).closest('tr');
    dtSalaryNew.row( $(this).parents('tr') ).remove().draw();
    totalSalaryNew();
});

function changeActive(_this){
    tr = $(_this).closest('tr');
    value = $(_this).prop('checked');
    if (value == true){
        value = 1;
    } else {
        value = 0;
    }
    id_salary_new = tr.find('.id_salary_staff').val();
    staff_id = tr.find('.staff_id').val();
    link = site.base_url+'admin/staff/updateActiveSalary/';
    $.ajax({
        url: link,
        type: 'GET',
        dataType: 'JSON',
        data: {
            token: hash,
            value: value,
            id_salary_new: id_salary_new,
            staff_id: staff_id,
        },
    })
        .done(function(data) {
           if (data.result){
               alert_float('success',data.message);
               $("#salary_bhxh").val(tnhFormatMoney(data.salary));
               // location.reload();
           } else {
               alert_float('danger',data.message);
               $(_this).prop('checked',false);
               // location.reload();
           }
        })
        .fail(function() {
            console.log("error");
        });

}

$(document).ready(function () {
    for (i = 0; i < counterAllowance; i++) {
        ajaxSelectCallBack($('#title_'+ i +''), 'admin/allowance_reduce/searchAllowanceReduce', $('#title_'+ i +'').val(),1);
    }
    for (i = 0; i < countGiamTru; i++) {
        ajaxSelectCallBack($('#title1_'+ i +''), 'admin/allowance_reduce/searchAllowanceReduce', $('#title1_'+ i +'').val(),2);
    }
});

$(document).on('change', '.title', function (
    event) {
    event.preventDefault();
    category_id = $(this).val();
    console.log(category_id)
    var tr = $(this).parents('tr');

    if (category_id) {
        if (jQuery.inArray(category_id, arrIdPc) !== -1) {
            alert_float('danger', 'Tiêu chí này đã tồn tại');
            totalPc();
            tr.remove();
            add_phucap();
            return;
        }
    }
    add_phucap();
});

$(document).on('change', '.title_gt', function (
    event) {
    event.preventDefault();
    category_id = $(this).val();
    var tr = $(this).parents('tr');

    if (category_id) {
        if (jQuery.inArray(category_id, arrIdGt) !== -1) {
            alert_float('danger', 'Tiêu chí này đã tồn tại');
            totalGt();
            tr.remove();
            add_giamtru();
            return;
        }
    }
    add_giamtru();
});

function totalPc(){
    tb = '#table_phucap tbody tr:not("[class^=not-tr]")';
    var n = $(tb).length;
    var stt = 0;
    arrIdPc = [];
    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        category_id = $(element).find('input.title').val();
        if (category_id) {
            index = jQuery.inArray(category_id, arrIdPc);
            if (index !== -1) {
            } else {
                arrIdPc.push(category_id);
            }
        }
    }
}

function totalGt(){
    tb = '#table_giamtru tbody tr:not("[class^=not-tr]")';
    var n = $(tb).length;
    var stt = 0;
    arrIdGt = [];
    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt ++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        category_id = $(element).find('input.title_gt').val();
        if (category_id) {
            index = jQuery.inArray(category_id, arrIdGt);
            if (index !== -1) {
            } else {
                arrIdGt.push(category_id);
            }
        }
    }
}

function add_phucap() {
    var trPC = $('<tr></tr>');
    var td_staff = $('<td class="text-center stt"></td>');
    var td_delete = $('<td class="text-center"></td>');
    var td_title = $('<td></td>');
    var td_amount = $('<td></td>');
    td_delete.append('<a class=""  onclick="removePC(this)"><i class="fa fa-remove btn btn-danger"></i></a>');
    td_title.append('<input type="text" name="title[' + counterAllowance + ']" id="title_' + counterAllowance + '" value="" style="width: 100%;" class="title"><input type="hidden" name="counterAllowance[]" value="' + counterAllowance + '">');
    td_amount.append('<input type="text" name="amount[' + counterAllowance + ']" value="" class="form-control number-format">');

    trPC.append(td_staff);
    trPC.append(td_title);
    trPC.append(td_amount);
    trPC.append(td_delete);
    $('#table_phucap tbody').append(trPC);
    ajaxSelectCallBack($('#title_' + counterAllowance + ''), 'admin/allowance_reduce/searchAllowanceReduce', 0, 1);
    counterAllowance++;
    totalPc();
}

function removePC(_this) {
    var tr = $(_this).parents('tr');
    tr.remove();
    totalPc();
}

function add_giamtru() {
    var trGT = $('<tr></tr>');
    var td_stt = $('<td class="text-center stt"></td>');
    var td_delete = $('<td class="text-center"></td>');
    var td_title = $('<td></td>');
    var td_amount = $('<td></td>');
    td_delete.append('<a class="btn btn-danger btn-icon"  onclick="removePC(this)"><i class="fa fa-remove"></i></a>');
    td_title.append('<input type="text" name="title_gt[' + countGiamTru + ']"  id="title1_' + countGiamTru + '" value="" style="width: 100%;" class="title_gt"><input type="hidden" name="countGiamTru[]" value="' + countGiamTru + '">');
    td_amount.append('<input type="text" name="amount_gt[' + countGiamTru + ']" value="" class="form-control number-format">');

    trGT.append(td_stt);
    trGT.append(td_title);
    trGT.append(td_amount);
    trGT.append(td_delete);
    $('#table_giamtru tbody').append(trGT);
    ajaxSelectCallBack($('#title1_' + countGiamTru + ''), 'admin/allowance_reduce/searchAllowanceReduce', 0, 2);
    countGiamTru++;
    totalGt();
}

function removeGT(_this) {
    var tr = $(_this).parents('tr');
    tr.remove();
    totalGt();
}

function ajaxSelectCallBack(element, url, id, types = '')
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
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