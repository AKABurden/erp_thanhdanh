<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
       <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="line-sp"></div>
            <a href="#"  data-toggle="modal" data-target="#payment_mode_modal"  class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?></a>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<p class="text-warning mtop5"><?php echo _l('payment_modes_add_edit_announcement'); ?></p>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('payment_modes_dt_name'),
							_l('Loại'),
							_l('opening_balance'),
							_l('payment_modes_dt_description'),
							_l('payment_modes_dt_active'),
							_l('options')
							),'payment-modes'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view('admin/paymentmodes/paymentmode'); ?>
	<?php init_tail(); ?>
	<script>
		$(function(){
			initDataTable('.table-payment-modes', window.location.href, [3], [3]);
		});
	</script>
</body>
</html>
