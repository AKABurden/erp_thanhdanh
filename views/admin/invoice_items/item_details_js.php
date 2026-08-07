<?php
/**
 * Included in application/views/admin/invoice_items/item_details.php
 */
?>
<script>
    
$(document).ready(()=>{
    var createSelect = (id_category = 0, select) => {
        if(id_category == 0 || typeof(id_category) == 'undefined' || typeof(select) == 'undefined') {
            return;
        }
        $.ajax({
            url: '<?php echo admin_url('invoice_items/get_categories/') ?>' + id_category,
            dataType: 'json',
        }).done((data)=>{
            select.parent().parent().show();
            select.find('option').remove();
            select.append('<option value></option>');
            $.each(data, (index,value) => {
                select.append('<option value="' + value.id + '">' + value.category + '</option>');
            });
            select.selectpicker('refresh');
        });
    };
    $('select[name=category_id\\[\\]]').on('change', (e)=>{  
        // 
        $(e.currentTarget).parents('div.form-group').nextAll().find('select[name=category_id\\[\\]] option:gt(0)').remove();
        $(e.currentTarget).parents('div.form-group').nextAll().find('select[name=category_id\\[\\]]').selectpicker('refresh');
        // Hide
        if($(e.currentTarget).val() != '' && $(e.currentTarget).val() != 0)
            $(e.currentTarget).parents('div.form-group').next().nextAll().find('select[name=category_id\\[\\]]').parents('div.form-group').hide();
        else
            $(e.currentTarget).parents('div.form-group').nextAll().find('select[name=category_id\\[\\]]').parents('div.form-group').hide();
        
        createSelect($(e.currentTarget).val(), $(e.currentTarget).parents('div.form-group').next().find('select[name=category_id\\[\\]]'));
    });
});
$(function() {

    $('.customer-form-submiter').on('click', function(e) {
        // Customer
        let category_level_2 = $('[name="category_id[]"]:eq(1)');
        // if(!category_level_2.val()) {
        //     alert_float('danger', 'Vui lòng chọn danh mục cấp 2!');
        //     category_level_2.focus();
        //     category_level_2.selectpicker('toggle');
        //     e.preventDefault();
        // }
    });
});
    function delete_supplier(id)
    { 
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.post(admin_url + 'suppliers/delete_items/'+id,{[csrfData['token_name']] : csrfData['hash']}, function(data){
            var data = JSON.parse(data);
            alert_float(data.alert_type, data.message);
            $('.table-productid_items').DataTable().ajax.reload();
        })
        }
    } 
</script>