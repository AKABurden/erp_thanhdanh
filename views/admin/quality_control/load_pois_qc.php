<script>
$(document).ready(function() {
    location_id = "<?= isset($location_id) ? $location_id : 0?>";
    $("#id_branch").select2("val", location_id).trigger("change");
    if (product_manu.length > 0) {
        $.each(product_manu, function(key, val) {
            createdRowItem(val, counter);
            counter++;
        });
    }

    if (product_manu_detail.length > 0) {
        $.each(product_manu_detail, function(key, val) {
            createdRowItem(val, counter);
            counter++;
        });
    }
});
</script>