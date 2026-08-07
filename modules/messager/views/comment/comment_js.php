<script>
$('body').on('click','.content-profile',function(e){
    $('.content-profile').removeClass('active');
    $(this).addClass('active');
    reloader();
})
</script>