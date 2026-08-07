<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="horizontal-scrollable-tabs">
    <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <?php

            foreach (filter_client_visible_tabs($customer_tabs) as $key => $tab) {
                $count = 0; 
                if(!empty($tab['table'])&&$tab['table']=='tbl_contracts_sales')
                {
                    $count = get_table_where_select('COUNT(*) as count','tbl_contracts_sales',array('customer_id'=>$client->userid),'','row')->count;
                }
                if(!empty($tab['table'])&&$tab['table']=='tbl_quotes')
                {
                    $count = get_table_where_select('COUNT(*) as count','tbl_quotes',array('customer_id'=>$client->userid),'','row')->count;
                }
                if(!empty($tab['table'])&&$tab['table']=='tbl_deliveries')
                {
                    $count = get_table_where_select('COUNT(*) as count','tbl_deliveries',array('customer_id'=>$client->userid),'','row')->count;
                }
                if(!empty($tab['table'])&&$tab['table']=='tbl_orders')
                {
                    $count = get_table_where_select('COUNT(*) as count','tbl_orders',array('customer_id'=>$client->userid),'','row')->count;
                }
                if(!empty($tab['table'])&&$tab['table']=='tblvouchers_coupon')
                {
                    $count = get_table_where_select('COUNT(*) as count','tblvouchers_coupon',array('customer'=>$client->userid),'','row')->count;
                }
                if(!empty($tab['table'])&&$tab['table']=='tblother_payslips_coupon')
                {
                    $count = get_table_where_select('COUNT(*) as count','tblother_payslips_coupon',array('objects'=>1,'objects_id'=>$client->userid),'','row')->count;
                }
                ?>
                <li class="<?php if ($key == 'profile') { echo 'active '; } ?>customer_tab_<?php echo $key; ?>">
                    <a data-group="<?php echo $key; ?>"
                       href="<?php echo admin_url('clients/client/' . $client->userid . '?group=' . $key); ?>">
                        <?php if (!empty($tab['icon'])) { ?>
                            <i class="<?php echo $tab['icon']; ?> menu-icon" aria-hidden="true"></i>
                        <?php } ?>
                        <?php echo $tab['name']; ?> <?php if($count > 0){ ?><span class="count_ch"><?=$count?></span><?php } ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>