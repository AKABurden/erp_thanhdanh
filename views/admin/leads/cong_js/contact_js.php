<!--lead-->
<script>

    function contactLead(leadid, contact_id) {
        if (typeof(contact_id) == 'undefined') {
            contact_id = '';
        }
        requestGet('leads/form_contact/' + leadid + '/' + contact_id).done(function(response) {
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
            validate_contact_lead_form();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    function validate_contact_lead_form() {
        appValidateForm('#contact-lead-form', {
            firstname: 'required',
            phonenumber: 'required',
            email: 'required'
        }, contactLeadFormHandler);
    }

    function contactLeadFormHandler(form) {
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
                if ($.fn.DataTable.isDataTable('.table-contacts_lead')) {
                    $('.table-contacts_lead').DataTable().ajax.reload(null,false);
                } else if ($.fn.DataTable.isDataTable('.table-all-contacts_lead')) {
                    $('.table-all-contacts_lead').DataTable().ajax.reload(null,false);
                }
                $('#contact').modal('hide');
            }
            alert_float(response.alert_type, response.message);

        }).fail(function(error){
            alert_float('danger', JSON.parse(error.responseText));
        });
        return false;
    }
</script>