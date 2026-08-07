<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>
            function get_total_limit() {
                var userid = $('input[name="userid"]').val();
                var search_date = $('input[name="search_date"]').val();
                dataString = {search_date:search_date,[csrfData['token_name']] : csrfData['hash']};
                jQuery.ajax({
                    type: "post",
                    url: "<?=admin_url()?>clients/count_all/"+userid,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                      data = JSON.parse(data);
                      console.log(data);
                      $('.count_all').html(data.count_all);   
                      $('.total').html(data.total);   
                      $('.pay').html(data.pay); 
                      }
                });
            }
        get_total_limit();
        var CustomersServerParams = {
          'search_date' : '[name="search_date"]',
        };
        $.each(CustomersServerParams, function(filterIndex, filterItem){
          $('' + filterItem).on('change', function(){
                get_total_limit();
                    if($.fn.DataTable.isDataTable('.table-orders_singer_client')) {
                        $('.table-orders_singer_client').DataTable().ajax.reload();
                    }
                    if($.fn.DataTable.isDataTable('.table-estimates-single-client')) {
                        $('.table-estimates-single-client').DataTable().ajax.reload();
                    }
                    if($.fn.DataTable.isDataTable('.table-contracts-single-client')) {
                        $('.table-contracts-single-client').DataTable().ajax.reload();
                    }
                    if($.fn.DataTable.isDataTable('.table-deliveries-single-client')) {
                        $('.table-deliveries-single-client').DataTable().ajax.reload();
                    }
                    if($.fn.DataTable.isDataTable('.table-vouchers-coupon-single-client')) {
                        $('.table-vouchers-coupon-single-client').DataTable().ajax.reload();
                    }
                    if($.fn.DataTable.isDataTable('.table-other-payslips-coupon-single-client')) {
                        $('.table-other-payslips-coupon-single-client').DataTable().ajax.reload();
                    }

          });
        });
    $(document).on('click', '._delete_shipping', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            $('.table-shipping_client').DataTable().ajax.reload();
        }, 'json');
    }
    return false;
    });
Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();
var vallRules = {
    company: 'required',
    company_short: 'required',
    fullname: 'required',

};
if(typeof(is_required_client) != 'undefined')
{
    $.each(is_required_client, function(i,v){
        vallRules[v] = 'required';
    })
}


$(function() {
    if ($('#client-attachments-upload').length > 0) {
        new Dropzone('#client-attachments-upload', appCreateDropzoneOptions({
            paramName: "file",
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    // Save button not hidden if passed from url ?tab= we need to re-click again
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }

    $('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        contact(customer_id, contact_id);
    }

    // consents=CONTACT_ID
    var consents = get_url_param('consents');
    if(consents){
        view_contact_consent(consents);
    }

    // If user clicked save and add new contact
    if (get_url_param('new_contact')) {
        contact(customer_id);
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });

    $('.customer-form-submiter').on('click', function() {       
        var form = $('.client-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
        checkValidateForm();
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                saveCustomerProfileExternalFile(files, 'dropbox');
            },
            linkType: "preview",
            extensions: app.options.allowed_files.split(','),
        }));
    }

    /* Customer profile tickets table */
    $('.table-tickets-single').find('#th-submitter').removeClass('toggleable');

    var tickets = initDataTable('.table-tickets-single', admin_url + 'tickets/index/false/' + customer_id, undefined, undefined, 'undefined', [$('table thead .ticket_created_column').index(), 'desc']);
    // if($.fn.DataTable.isDataTable('.table-contracts-single-client')) {
    //     tickets.columns(1).visible(false, false);
    // }
    /* Customer profile contracts table */
    var contracts = initDataTable('.table-contracts-single-client', admin_url + 'contracts/table_single_client/' + customer_id, undefined,undefined, CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-contracts-single-client')) {
        contracts.columns(1).visible(false, false);
    }
    /* Customer profile deliveries table */
    var deliveries = initDataTable('.table-deliveries-single-client', admin_url + 'releases/table_single_client/' + customer_id, undefined,undefined, CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-deliveries-single-client')) {
        deliveries.columns(2).visible(false, false);
    }
    /* Customer profile vouchers coupon table */
    var vouchers = initDataTable('.table-vouchers-coupon-single-client', admin_url + 'vouchers_coupon/table_single_client/' + customer_id, undefined,undefined, CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-vouchers-coupon-single-client')) {
        vouchers.columns(2).visible(false, false);
    }
    /* Customer profile vouchers coupon table */
    var payslips = initDataTable('.table-other-payslips-coupon-single-client', admin_url + 'other_payslips_coupon/table_single_client/' + customer_id, undefined,undefined, CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-other-payslips-coupon-single-client')) {
        payslips.columns(3).visible(false, false);
        payslips.columns(2).visible(false, false);
    }
    /* Custome profile contacts table */
    var contactsNotSortable = [];
    <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        contactsNotSortable.push($('#th-consent').index());
    <?php } ?>
    _table_api = initDataTable('.table-contacts', admin_url + 'clients/contacts/' + customer_id, contactsNotSortable, contactsNotSortable);
    if(_table_api) {
          <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        _table_api.on('draw', function () {
            var tableData = $('.table-contacts').find('tbody tr');
            $.each(tableData, function() {
                $(this).find('td:eq(1)').addClass('bg-light-gray');
            });
        });
        <?php } ?>
    }
    /* Customer profile invoices table */
    initDataTable('.table-invoices-single-client',
        admin_url + 'invoices/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'desc'],
            [0, 'desc']
        ]);

   initDataTable('.table-credit-notes', admin_url+'credit_notes/table/'+customer_id, ['undefined'], ['undefined'], undefined, [0, 'desc']);

    /* Customer profile Estimates table */
    var estimates = initDataTable('.table-estimates-single-client',
        admin_url + 'estimates/table_single_client/' + customer_id,
        'undefined',
        'undefined',
        CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-estimates-single-client')) {
        estimates.columns(3).visible(false, false);
    }
    /* Customer profile orders table */
    var orders_singer_client = initDataTable('.table-orders_singer_client',
        admin_url + 'orders/table_single_client/' + customer_id,
        'undefined',
        'undefined',
        CustomersServerParams, []);
    if($.fn.DataTable.isDataTable('.table-orders_singer_client')) {
        orders_singer_client.columns(2).visible(false, false);
    }
    /* Customer profile payments table */
    initDataTable('.table-payments-single-client',
        admin_url + 'payments/table/' + customer_id, undefined, undefined,
        'undefined', [0, 'desc']);

    /* Customer profile reminders table */
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'customer', undefined, undefined, undefined, [1, 'asc']);

    /* Customer profile expenses table */
    initDataTable('.table-expenses-single-client',
        admin_url + 'expenses/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'desc']);

    /* Customer profile proposals table */
    initDataTable('.table-proposals-client-profile',
        admin_url + 'proposals/proposal_relations/' + customer_id + '/customer',
        'undefined',
        'undefined',
        'undefined', [6, 'desc']);

    /* Custome profile projects table */
    initDataTable('.table-projects-single-client', admin_url + 'projects/table/' + customer_id, undefined, undefined, 'undefined', <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array(5,'asc'))); ?>);
    /* coupon support single client table */
    tAPI = initDataTable('.table-coupon-support-single-client',
        admin_url + 'coupon_support/table_client/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [0, 'desc']);


    var vRules = {};
    if (app.options.company_is_required == 1) {
        vRules = {
            company: 'required',
            'branch[]': 'required',
            zcode:
            {   
                remote: {
                    url: admin_url + "misc/client_code_exists",
                    type: 'post',
                    data: {
                        code: function() {
                            return $('.client-form input[name="zcode"]').val();
                        },
                        id: function() {
                            return $('body').find('input[id="id_client_ch"]').val();
                        },
                        [csrfData['token_name']] : csrfData['hash']
                        },
                    },

            },
            // company: {
            //     remote: {
            //         url: admin_url + "misc/client_company_exists",
            //         type: 'post',
            //         data: {
            //             code: function() {
            //                 return $('.client-form input[name="company"]').val();
            //             },
            //             id: function() {
            //                 return $('body').find('input[id="id_client_ch"]').val();
            //             },
            //             [csrfData['token_name']] : csrfData['hash']
            //         },
            //     },
            //
            // },
            company_short: 'required',
            // vat: {
            //     remote: {
            //         url: admin_url + "misc/client_vat_exists",
            //         type: 'post',
            //         data: {
            //             vat: function() {
            //                 return $('.client-form input[name="vat"]').val();
            //             },
            //             id: function() {
            //                 return $('body').find('input[id="id_client_ch"]').val();
            //             },
            //             [csrfData['token_name']] : csrfData['hash']
            //         },
            //     },

            // },
            // email_client: {
            //     remote: {
            //         url: admin_url + "misc/client_email_client_exists",
            //         type: 'post',
            //         data: {
            //             email_client: function() {
            //                 return $('.client-form input[name="email_client"]').val();
            //             },
            //             id: function() {
            //                 return $('body').find('input[id="id_client_ch"]').val();
            //             },
            //             [csrfData['token_name']] : csrfData['hash']
            //         },
            //     },
            // },
            // phonenumber: {
            //     remote: {
            //         url: admin_url + "misc/client_phonenumber_exists",
            //         type: 'post',
            //         data: {
            //             phonenumber: function() {
            //                 return $('.client-form input[name="phonenumber"]').val();
            //             },
            //             id: function() {
            //                 return $('body').find('input[id="id_client_ch"]').val();
            //             },
            //             [csrfData['token_name']] : csrfData['hash']
            //         },
            //     },
            // },
        }
        if(typeof(is_required_client) != 'undefined')
        {
            $.each(is_required_client, function(i,v){
                vRules[v] = 'required';
            })
        }
    }
    if(typeof(_is_required_client) != 'undefined')
    {
        $.each(_is_required_client, function(i){
            if (app.options.company_is_required == 1) {
                vallRules['contacts['+i+'][firstname]'] =  vRules['contacts['+i+'][firstname]']  = 'required';
                vallRules['contacts['+i+'][email]']  =  vRules['contacts['+i+'][email]'] =  {
                    email:true,
                    <?php if(hooks()->apply_filters('contact_email_unique', "true") === "true"){ ?>
                    remote: {
                        url: admin_url + "misc/contact_email_toclient_exists",
                        type: 'post',
                        data: {
                            [csrfData['token_name']] : csrfData['hash'],
                            ['contacts['+i+'][email]'] : function() {
                                return $('input[name="contacts['+i+'][email]"]').val();
                            },
                            ['contacts['+i+'][id]'] : function() {
                                return $('body').find('input[name="contacts['+i+'][id]"]').val();
                            }
                        }
                    }
                    <?php } ?>
                };

            }
        })
        appValidateForm($('.client-form'), vallRules);
    }

    appValidateForm($('.client-form'), vRules);

    if(typeof(customer_id) == 'undefined'){
        $('#company').on('blur', function() {
            var company = $(this).val();
            var $companyExistsDiv = $('#company_exists_info');

            if(company == '') {
                $companyExistsDiv.addClass('hide');
                return;
            }
            var data = {company:company};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url+'clients/check_duplicate_customer_name', data)
            .done(function(response) {
                if(response) {
                    response = JSON.parse(response);
                    if(response.exists == true) {
                        $companyExistsDiv.removeClass('hide');
                        $companyExistsDiv.html('<div class="info-block mbot15">'+response.message+'</div>');
                    } else {
                        $companyExistsDiv.addClass('hide');
                    }
                }
            });
        });
    }

    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.client-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });

});

function delete_contact_profile_image(contact_id) {
    requestGet('clients/delete_contact_profile_image/'+contact_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src', '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
    });
}

function customerGoogleDriveSave(pickData) {
    saveCustomerProfileExternalFile(pickData, 'gdrive');
}

function saveCustomerProfileExternalFile(files, externalType) {
    $.post(admin_url + 'clients/add_external_attachment', {
        files: files,
        clientid: customer_id,
        external: externalType
    }).done(function() {
        window.location.reload();
    });
}

function validate_contact_form() {
    appValidateForm('#contact-form', {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {

                    var $sentSetPassword = $('input[name="send_set_password_email"]');

                    if ($('#contact input[name="contactid"]').val() == '' && $sentSetPassword.prop('checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            <?php if(hooks()->apply_filters('contact_email_unique', "true") === "true"){ ?>
            remote: {
                url: admin_url + "misc/contact_email_exists",
                type: 'post',
                data: {
                    [csrfData['token_name']] : csrfData['hash'],
                    email: function() {
                        return $('#contact input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="contactid"]').val();
                    }
                }
            }
            <?php } ?>
        }
    }, contactFormHandler);
}

function contactFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);

    $("#contact input[type=file]").each(function() {
        if($(this).val() === "") {
            $(this).prop('disabled', true);
        }
    });

    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
           response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }
            }

            if ($.fn.DataTable.isDataTable('.table-contacts')) {
                $('.table-contacts').DataTable().ajax.reload(null,false);
            } else if ($.fn.DataTable.isDataTable('.table-all-contacts')) {
                $('.table-all-contacts').DataTable().ajax.reload(null,false);
            }

            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#contact').modal('hide');
            }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    requestGet('clients/form_contact/' + client_id + '/' + contact_id).done(function(response) {
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_contact_form();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}


function update_all_proposal_emails_linked_to_contact(contact_id) {
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post(admin_url + 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }
        contacts_shared_ids.selectpicker('refresh');
        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post(admin_url + 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function save_longitude_and_latitude(clientid) {
    var data = {};
    data.latitude = $('#latitude').val();
    data.longitude = $('#longitude').val();
    $.post(admin_url + 'clients/save_longitude_and_latitude/'+clientid, data).done(function(response) {
       if(response == 'success') {
            alert_float('success', "<?php echo _l('updated_successfully', _l('client')); ?>");
       }
        setTimeout(function(){
            window.location.reload();
        },1200);
    }).fail(function(error) {
        alert_float('danger', error.responseText);
    });
}

function fetch_lat_long_from_google_cprofile() {
    var data = {};
    data.address = $('#long_lat_wrapper').data('address');
    data.city = $('#long_lat_wrapper').data('city');
    data.country = $('#long_lat_wrapper').data('country');
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post(admin_url + 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>");
            } else {
                alert_float('danger', data.response.status + ' - ' + data.response.error_message);
            }
        }
    });
}

$('body').on('change', '#city', function(e){
    var id_city = $(this).val();
    $('#district').html("<option></option>").selectpicker('refresh');
    var data = {id_province:id_city};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'clients/get_district', data, function(data){
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i,v){
            option += '<option value="'+v.districtid+'">'+v.name+'</option>';
        })
        $('#district').html(option).selectpicker('refresh');
    })
})

$('body').on('change', '#district', function(e){
    var id_district = $(this).val();
    var data = {id_district:id_district};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $('#ward').html("<option></option>").selectpicker('refresh');
    $.post(admin_url+'clients/get_ward',data, function(data){
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i,v){
            option += '<option value="'+v.wardid+'">'+v.name+'</option>';
        })
        $('#ward').html(option).selectpicker('refresh');
    })
})

$('body').on('change', '#country', function(e){
    var id_country = $(this).val();
    $('#city').html("<option></option>").selectpicker('refresh');
    var data = {id_country:id_country};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'clients/get_province', data, function(data){
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i,v){
            option += '<option value="'+v.provinceid+'">'+v.name+'</option>';
        })
        $('#city').html(option).selectpicker('refresh');
    })
})



$('body').on('click','a[aria-controls="billing_and_shipping"]', function(e){

    initDataTable('.table-shipping_client', admin_url + 'clients/table_shipping/' + customer_id , undefined, undefined, 'undefined', [1, 'desc']);
})


var i = $('#div_contacts').find('.items_contact').length;
$('body').on('click', '.add-contacts', function(e){
    var kt_contact = $('#div_contacts').find('.items_contact').length;
    $('#div_contacts').append('<div class="col-md-6 items_contact">'+
    '                               <h5 class="mtop20"><?=_l('cong_contacts')?></h5>'+
    '                               <p class="mborder"></p>'+
    '                               <div class="pborder">'+
    '                                   <div class="text-right">'+
    '                                       <a class="remove_contact_panel text-right pointer text-danger" title="Xóa"><i class="fa fa-trash gf-icon-hover"></i></a>'+
    '                                    </div>'+
    '                                   <div class="col-md-6 mtop10">'+
    '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][firstname]">'+
    '                                           <label for="contacts['+i+'][firstname]" class="control-label"> <?=_l('cong_last_firstname')?></label>'+
    '                                            <input type="text" name="contacts['+i+'][firstname]" id="contacts['+i+'][firstname]"  tabindex='+(1*(i+1))+'  class="form-control" autofocus="1" value="">'+
    '                                        </div>'+
    '                                        <div class="form-group" app-field-wrapper="contacts['+i+'][title]">'+
    '                                            <label for="contacts['+i+'][title]" class="control-label"> <?=_l('cong_title')?></label>'+
    '                                           <input type="text" name="contacts['+i+'][title]" id="contacts['+i+'][title]" tabindex='+(3*(i+1))+' class="form-control" autofocus="1" value="">'+
    '                                        </div>'+
    '                                        <div class="form-group" app-field-wrapper="contacts['+i+'][phonenumber]">'+
    '                                             <label for="contacts['+i+'][phonenumber]" class="control-label"> <?=_l('cong_phonenumber')?></label>'+
    '                                             <input type="text" name="contacts['+i+'][phonenumber]" id="contacts['+i+'][phonenumber]" tabindex='+(5*(i+1))+' class="form-control phonenumber_contacts" autofocus="1" value="">'+
    '                                       </div>'+
    '                                       <div class="client_password_set_wrapper">'+
    '                                           <label for="password" class="control-label">'+
    '                                                <?=_l('cong_password')?>'+
    '                                           </label>'+
    '                                           <div class="input-group">'+
    '                                               <input type="password" class="form-control password" name="contacts['+i+'][password]" autocomplete="false">'+
    '                                               <span class="input-group-addon">'+
    '                                                   <a href="#" class="show_password" onclick="showPassword(\'contacts['+i+'][password]\'); return false;"><i class="fa fa-eye"></i></a>'+
    '                                               </span>'+
    '                                               <span class="input-group-addon">'+
    '                                                   <a href="#" class="generate_password" onclick="generatePasswordContact(this);return false;"><i class="fa fa-refresh"></i></a>'+
    '                                               </span>'+
    '                                           </div>'+
    '                                       </div>'+
    '                                       <div class="checkbox checkbox-primary">'+
    '                                           <input type="checkbox" id="contacts['+i+'][is_primary]" class="is_primary" name="contacts['+i+'][is_primary]"  '+(kt_contact==0 ? 'checked' : '')+' value="1">'+
    '                                           <label for="" data-toggle="tooltip" data-title=""><?=_l('cong_contact_primary')?></label>'+
    '                                       </div>'+
    '                                   </div>'+
    '                                   <div class="col-md-6 mtop10">'+
    '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][email]">'+
    '                                           <label for="contacts['+i+'][email]" class="control-label"> <?=_l('cong_email')?></label>'+
    '                                           <input type="text" id="contacts['+i+'][email]" tabindex='+(7*(i+1))+'  name="contacts['+i+'][email]" class="form-control" autofocus="1" value="">'+
    '                                       </div>'+
    '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][birtday]">'+
    '                                           <label for="contacts['+i+'][birtday]" class="control-label"> Sinh nhật</label>'+
    '                                           <div class="input-group date">'+
    '                                               <input type="text" id="contacts['+i+'][birtday]" name="contacts['+i+'][birtday]" class="datepicker form-control" tabindex="'+(6*(i+1))+'" autofocus="1" value="">'+
    '                                               <div class="input-group-addon">'+
    '                                                   <i class="fa fa-calendar calendar-icon"></i>'+
    '                                               </div>'+
    '                                           </div>'+
    '                                       </div>'+
    '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][note]">'+
    '                                           <label for="contacts['+i+'][note]" class="control-label"><?=_l('cong_note')?></label>'+
    '                                           <textarea id="contacts['+i+'][note]" '+(6*(i+1))+' name="contacts['+i+'][note]" class="form-control" rows="4"></textarea>'+
    '                                       </div>'+
    '                                   </div>'+
    '                                   <div class="clearfix"></div>'+
    '                               </div>'+
    '                           </div>');

                                console.log(i,123);
    
    if (app.options.company_is_required == 1) {
        vallRules['contacts['+i+'][firstname]']  = 'required';
        vallRules['contacts['+i+'][email]']  = {
                email:true,
                <?php if(hooks()->apply_filters('contact_email_unique', "true") === "true"){ ?>
                remote: {
                    url: admin_url + "misc/contact_email_toclient_exists",
                        type: 'post',
                        data: {
                            [csrfData['token_name']] : csrfData['hash'],
                            ['contacts['+i+'][email]'] : function() {
        console.log(i,1234);
                                
                                return $('input[name="contacts['+i+'][email]"]').val();
                            },
                            ['contacts['+i+'][id]'] : function() {
                                return $('body').find('input[name="contacts['+i+'][id]"]').val();
                            }
                    }
                }
            <?php } ?>
        };
    }

    appValidateForm($('.client-form'), vallRules);
    init_datepicker();
    i++;
})

$('body').on('click', '.remove_contact_panel', function(e){
    $(this).parents('.items_contact').remove();
})


function RemoteEmailContact(i)
{
    $.ajax({
        url: admin_url + "misc/contact_email_toclient_exists",
            type:
        'post',
        data: {
            email: function () {
                return $('input[name="contacts[' + i + '][email]"]').val();
            }
        ,
            id: function () {
                return $('body').find('input[name="contacts[' + i + '][id]"]').val();
            }
        }
    })
}
function generatePasswordContact(field) {
    var length = 12,
        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    $(field).parent().parent().find('input.password').val(retVal);
}


$('body').on('change','input.is_primary',function(){
    $('input.is_primary').prop('checked',false);
    $(this).prop('checked',true);
})


$('body').on('click', '.removeImg', function(e){
    if(confirm('<?=_l('you_must_delete_img')?>'))
    {
        $('.input_upload').removeClass('hide');
        $(this).remove();
        $('.imgClient').remove();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var img = $(this).attr('name_img');
        var userid = $('input[name="userid"]').val();
        if(userid != "")
        {
            $.post(admin_url+'clients/unlinkImg/'+userid+'/'+img, data, function(data){

            })
        }
    }
})


function AddAdvisory(id = "")
{
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    data['client'] = customer_id;
    if($.isNumeric(id))
    {
        data['id'] = id;

    }
    $.post(admin_url+'clients/view_modal_advisory', data, function(data)
    {
        $('#div_modal_advisory_client').find('#advisory-client-modal').html(data);
        $('#advisory-client-modal').modal('show');
        $('#advisory-client-modal').find('.selectpicker').selectpicker('refresh');
        init_datepicker();
        var vallAdvisory = {};
        vallAdvisory['type']  = 'required';
        vallAdvisory['cycle']  = 'required';
        appValidateForm($('#form-advisory-client'), vallAdvisory);
    })
}

$('body').on('click', '.rating_client', function(e){
    var id_star = $(this).attr('id-star');
    var div_rating = $(this).parents('#div_rating_client');
    div_rating.find('.rating_client').removeClass('checked');
    $(this).addClass('checked');
    for(var i = 1;i < id_star; i++)
    {
        div_rating.find('.rating_client[id-star="'+i+'"]').addClass('checked');
    }
    $('input[name="vip_rating"]').val(id_star);
})

function change_type() {
    $('.customer-view').addClass('hide');
    $('.customer-edit').removeClass('hide');
    reSizeHeight();
}
$(document).ready(function() {
    reSizeHeight();
});
function reSizeHeight() {
    // var Height = $(".wap-left").height();
    // Height = Number(Height) - 3;
    // var right = document.getElementsByClassName("wap-right");
    // if(right.length)
    // {
    //     right[0].style.height = Height+"px";
    // }
}
<?php if($this->input->get('edit') == 1){?>
    change_type();
<?php }?>
var search_daterangepicker = () => {
   $('input[name="search_date"]').daterangepicker({
      opens: 'left',
      autoUpdateInput: false, 
      isInvalidDate: false,
      "locale": {
         "format": "DD/MM/YYYY",
         "separator": " - ",
         "applyLabel": lang_daterangepicker.applyLabel,
         "cancelLabel": lang_daterangepicker.cancelLabel,
         "fromLabel": lang_daterangepicker.fromLabel,
         "toLabel": lang_daterangepicker.toLabel,
         "customRangeLabel": lang_daterangepicker.customRangeLabel,
         "daysOfWeek": lang_daterangepicker.daysOfWeek,
         "monthNames": lang_daterangepicker.monthNames
      },
   }, function(start, end, label) {
   });
   $('input[name="search_date"]').val('').datepicker("refresh");
   $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
      $( "#search_date" ).trigger( "change" );
   });
   $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
      $( "#search_date" ).trigger( "change" );
   });
};
search_daterangepicker();

</script>


