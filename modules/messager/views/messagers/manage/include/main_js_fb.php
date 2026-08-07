<script type="text/javascript">
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    function reloader() {
    	$('#loader').removeClass('hide');
    	$('.tab-content').addClass('hide');
    	$('.chat-area-reply').addClass('hide');
		setTimeout(function(){
			$('#loader').addClass('hide');
			$('.tab-content').removeClass('hide');
			$('.chat-area-reply').removeClass('hide');
		}, 1500);
	}
</script>