<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){
    init_relation_tasks_table(array( 'data-new-rel-id'=>$client->userid,'data-new-rel-type'=>'customer'));
} ?>
