<style>
	#side-menu li:nth-child(2):before, #side-menu li:nth-child(3):before, #side-menu li:nth-child(4):before {
		content: attr(data-content);
		font-family: Verdana, serif;
	    position: absolute;
	    padding: 0px 5px;
	    background: #ff6f00;
	    z-index: 9;
	    color: #fff;
	    top: 7px;
	    right: 10px;
	    border-radius: 50%;
	}
</style>
<script>
$(document).ready(function() {
	var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'warranty/count_status', data).done(function(response){
    	response = JSON.parse(response);
    	var menu_li = $('ul[id="side-menu"]');
    	if(response.dataWarranty_receive > 0) {
    		menu_li.find('li:eq(1)').attr('data-content',response.dataWarranty_receive);
    	}
    	if(response.dataWarranty > 0) {
    		menu_li.find('li:eq(2)').attr('data-content',response.dataWarranty);
    	}
    	if(response.dataWarranty_export_supplies > 0) {
    		menu_li.find('li:eq(3)').attr('data-content',response.dataWarranty_export_supplies);
    	}
    });
});
</script>