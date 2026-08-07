<div class="text-center" id="customer-info">
    <button class="btn btn-info mright10 btn_add_data" id-data="lead"  type="button"><?=_l('cong_add_lead_short')?></button>
    <button class="btn btn-success btn_add_data" id-data="client"  type="button"><?=_l('cong_add_client_short')?></button>
</div>
<script>
    $(function() {
        $('.profile_staff_assigned').addClass('hide');
        $('#browsers_staff_assigned').selectpicker('val',[]);
    })
</script>