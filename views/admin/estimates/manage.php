<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
	   <div class="panel-body _buttons">
	    <?php $this->load->view('admin/estimates/estimates_top_stats');
	    ?>
	    <?php if(has_permission('estimates','','create')){ ?>
	     <a href="<?php echo admin_url('estimates/estimate'); ?>" class="btn btn-info pull-left new new-estimate-btn H_action_button">
	     	<i class="lnr lnr-plus-circle" aria-hidden="true"></i>
	     	<?php echo _l('create_add_new'); ?>
	     </a>
	     <div class="line-sp"></div>
	   	<?php } ?>
	   	<div class="display-block text-right">
	     <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
	      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	        <i class="fa fa-filter" aria-hidden="true"></i>
	      </button>
	      <ul class="dropdown-menu width300">
	       <li>
	        <a href="#" data-cview="all" onclick="dt_custom_view('','.table-estimates',''); return false;">
	          <?php echo _l('estimates_list_all'); ?>
	        </a>
	      </li>
	      <li class="divider"></li>
	      <li class="<?php if($this->input->get('filter') == 'not_sent'){echo 'active'; } ?>">
	        <a href="#" data-cview="not_sent" onclick="dt_custom_view('not_sent','.table-estimates','not_sent'); return false;">
	          <?php echo _l('not_sent_indicator'); ?>
	        </a>
	      </li>
	      <li>
	        <a href="#" data-cview="invoiced" onclick="dt_custom_view('invoiced','.table-estimates','invoiced'); return false;">
	          <?php echo _l('estimate_invoiced'); ?>
	        </a>
	      </li>
	      <li>
	        <a href="#" data-cview="not_invoiced" onclick="dt_custom_view('not_invoiced','.table-estimates','not_invoiced'); return false;"><?php echo _l('estimates_not_invoiced'); ?></a>
	      </li>
	      <li class="divider"></li>
	      <?php foreach($estimate_statuses as $status){ ?>
	        <li class="<?php if($this->input->get('status') == $status){echo 'active';} ?>">
	          <a href="#" data-cview="estimates_<?php echo $status; ?>" onclick="dt_custom_view('estimates_<?php echo $status; ?>','.table-estimates','estimates_<?php echo $status; ?>'); return false;">
	            <?php echo format_estimate_status($status,'',false); ?>
	          </a>
	        </li>
	      <?php } ?>
	      <div class="clearfix"></div>

	      <?php if(count($estimates_sale_agents) > 0){ ?>
	        <div class="clearfix"></div>
	        <li class="divider"></li>
	        <li class="dropdown-submenu pull-left">
	          <a href="#" tabindex="-1"><?php echo _l('sale_agent_string'); ?></a>
	          <ul class="dropdown-menu dropdown-menu-left">
	           <?php foreach($estimates_sale_agents as $agent){ ?>
	             <li>
	              <a href="#" data-cview="sale_agent_<?php echo $agent['sale_agent']; ?>" onclick="dt_custom_view(<?php echo $agent['sale_agent']; ?>,'.table-estimates','sale_agent_<?php echo $agent['sale_agent']; ?>'); return false;"><?php echo $agent['full_name']; ?>
	            </a>
	          </li>
	        <?php } ?>
	      </ul>
	    </li>
	  	<?php } ?>
	  	<div class="clearfix"></div>
	  	<?php if(count($estimates_years) > 0){ ?>
	    	<li class="divider"></li>
	    	<?php foreach($estimates_years as $year){ ?>
	      		<li class="active">
	        	<a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-estimates','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
	      	</a>
	    	</li>
	  	<?php } ?>
		<?php } ?>
		</ul>
		</div>
		<a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view('.table-estimates','#estimate'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
		<a href="#" class="btn btn-default btn-with-tooltip estimates-total" onclick="slideToggle('#stats-top'); init_estimates_total(true); return false;" data-toggle="tooltip" title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
		</div>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<?php $this->load->view('admin/estimates/list_template'); ?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>var hidden_columns = [2,5,6,8,9];</script>
<?php init_tail(); ?>
<script>
	$(function(){
		init_estimate();
	});
</script>
</body>
</html>
