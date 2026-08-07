<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('user_widget'); ?>">
   <div class="panel_s user-data">
      <div class="panel-body">
         <div class="widget-dragger"></div>
         <div class="panel panel-primary">
            <div class="panel-heading"><?=_l('sales_customer')?></div>
            <div class="panel-body scroll_list">
               <!-- Vòng lặp -->
               <?php
                  $this->db->select('SUM(grand_total) as total_grand, customer_id');
                  $this->db->where('status', 'approved');
                  $this->db->group_by('customer_id');
                  $this->db->order_by('total_grand DESC');
                  $this->db->limit(5);
                  $result = $this->db->get('tbl_orders')->result_array();
               ?>
               <?php if($result) { ?>
                  <?php foreach ($result as $key => $value) { ?>
                     <?php $client = get_table_where('tblclients',array('userid'=>$value['customer_id']),'','row'); ?>
                     <div class="content-list-client">
                        <div class="img-client">
                           <img src="<?=base_url('uploads/Capture9_03.png')?>">
                        </div>
                        <div class="name-client">
                           <?=$client->company?>
                        </div>
                        <div class="type-client">
                           <?=number_format($value['total_grand'])?>
                        </div>
                        <div class="clearfix"></div>
                     </div>
                  <?php } ?>
               <?php } else { ?>
                  <div class="content-list-client">
                     <div>Không có dữ liệu về doanh số khách hàng!</div>
                  </div>
               <?php } ?>
               <!-- end -->
            </div>
         </div>
      </div>
   </div>
</div>
