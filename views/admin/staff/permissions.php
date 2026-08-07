<style>
   .parent-permission {
      background: #ffeabc87;
   }
   .padding-child {
          padding: 10px 10px 5px 40px !important;
   }
   .permission-container {
      width: 100%;
   }
   .permission-left {
      border: 1px solid #dcdcdc;
      border-right: 0;
      padding: 10px;
      width: 60%;
   }
   .permission-right {
      border: 1px solid #dcdcdc;
      padding: 10px;
      width: 40%;
   }
   .parent-permission {
      border: 1px solid #dcdcdc;
   }
   .body-permission-p-left {
      padding: 10px;
      width: 60%;
   }
   .body-permission-p-right {
      border-left: 1px solid #dcdcdc;
      padding: 10px;
      width: 40%;
   }
   .child-permission {
      border: 1px solid #dcdcdc;
   }
   .body-permission-c-left {
      padding: 10px 10px 10px 30px;
      width: 60%;
   }
   .body-permission-c-right {
      border-left: 1px solid #dcdcdc;
      padding: 10px;
      width: 40%;
   }
   .js_child {
      max-height: 0px;
      overflow: hidden;
      transition: max-height 0.25s ease-in;
   }
   .full_height {
      max-height: 10000px;
      transition: max-height 0.25s ease-in;
   }
</style>

<div class="permission-container">
   <div class="permission-content">
      <div class="permission-header">
         <div class="permission-left pull-left">Feature</div>
         <div class="permission-right pull-left">Capabilities</div>
         <div class="clearfix"></div>
      </div>
      <div class="permission-body">
         <?php
            if(isset($member)){
               $is_admin = is_admin($member->staffid);
            }
            foreach(get_available_staff_permissions() as $feature => $permission) { ?>
            <!-- xuất hiện 2 tab cố định, chưa xử lý đc nên bỏ qua -->
            <?php
               if($feature == 'goals' || $feature == 'surveys') {
                  continue;
               }
            ?>
            <!-- end -->
            <div class="parent-permission" data-name="<?php echo $feature; ?>">
               <div class="body-permission-p-left pull-left">
                  <b><?php echo $permission['name']; ?></b>
               </div>
               <div class="body-permission-p-right pull-left">
                  <div class="checkbox checkbox-primary">
                     <?php
                        $checked = '';
                        if(isset($roleid)) {
                           if(in_array($feature, $role)) {
                              $checked = 'checked';
                           }
                        }
                        if(isset($staffid)) {
                           if(in_array($feature, $staff)) {
                              $checked = 'checked';
                           }
                        }
                     ?>
                     <input type="checkbox" class="js_permission_parent" name="permission[<?=$feature?>][parent]" <?= $checked ?>>
                     <label for="js_permission_parent"></label>
                  </div>
               </div>
               <div class="clearfix"></div>
            </div>
            <div class="js_child" data-parent="<?php echo $feature; ?>">
               <?php foreach ($permission['child'] as $key_child => $value_child) { ?>
                  <div class="child-permission">
                     <div class="body-permission-c-left pull-left">
                        <b><?php echo $value_child['name']; ?></b>
                     </div>
                     <div class="body-permission-c-right pull-left">
                        <?php foreach ($value_child['permissions'] as $key_permissions => $value_permissions) { ?>
                           <?php
                              $checked = '';
                              //hỗ trợ role
                              if(isset($roleid)) {
                                 $can_permission = 'can_'.$key_permissions;
                                 $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2',array('id_role'=>$roleid, 'obj_permission'=>$key_child, $can_permission=>1),'','row');
                                 if($checkTrue_permission) {
                                    $checked = 'checked';
                                 }
                              }
                              //hỗ trợ staff
                              if(isset($staffid)) {
                                 $can_permission = 'can_'.$key_permissions;
                                 $checkTrue_permission = get_table_where('tbl_staff_child_permission_v2',array('id_staff'=>$staffid, 'obj_permission'=>$key_child, $can_permission=>1),'','row');
                                 if($checkTrue_permission) {
                                    $checked = 'checked';
                                 }
                              }
                           ?>

                           <div class="checkbox checkbox-primary">
                              <input type="checkbox" class="permission_child" name="permission[<?=$feature?>][child][<?=$key_child?>][<?=$key_permissions?>]" data-child="<?=$key_child?>" data-can="<?=$key_permissions?>" <?= $checked ?>>
                              <label for="permission_child"><?=$value_permissions?></label>
                           </div>
                        <?php } ?>
                     </div>
                     <div class="clearfix"></div>
                  </div>
               <?php } ?>
            </div>
         <?php } ?>
      </div>
   </div>
</div>

<script>
   function show_permission() {
      var all_parent = $('.parent-permission');
      $.each(all_parent, function(i,v){
         var parent = $(this).attr('data-name');
         if($(this).find('.js_permission_parent').is(':checked')) {
            $('div[data-parent='+parent+']').addClass('full_height');
         }
         else {
            $('div[data-parent='+parent+']').removeClass('full_height');
         }
      });
   }
    $(document).on('change','.js_permission_parent', function (e) {
      var parent = $(this).parents('.parent-permission').attr('data-name');
      if($(this).prop('checked')) {
         $('div[data-parent='+parent+']').addClass('full_height');
         $('div[data-parent='+parent+']').find('input.permission_child').prop('checked', true);
         $('div[data-parent='+parent+']').find('input.permission_child[data-can="view_own"]').prop('checked', false);
         var permission_child = $('div[data-parent='+parent+']').find('.permission_child').trigger('change');
         $.each(permission_child, function(i, v) {
            $(v).parents('.child-permission').find('input[data-can="view_own"]').attr('disabled',true);
         })
      }
      else {
         $('div[data-parent='+parent+']').removeClass('full_height');
         $('div[data-parent='+parent+']').find('input.permission_child').prop('checked', false);
         $('div[data-parent='+parent+']').find('.permission_child').trigger('change');
      }
   });

   $(document).on('change','.permission_child', function (e) {
      if($(this).prop('checked')) {
         if($(this).attr('data-can') == 'view') {
            $(this).parents('.body-permission-c-right').find('input.permission_child[data-can="view_own"]').attr('disabled',true);
         }
         else if($(this).attr('data-can') == 'view_own') {
            $(this).parents('.body-permission-c-right').find('input.permission_child[data-can="view"]').attr('disabled',true);
         }
      }
      else {
         if($(this).attr('data-can') == 'view') {
            $(this).parents('.body-permission-c-right').find('input.permission_child[data-can="view_own"]').attr('disabled',false);
         }
         else if($(this).attr('data-can') == 'view_own') {
            $(this).parents('.body-permission-c-right').find('input.permission_child[data-can="view"]').attr('disabled', false);
         }
      }
   });
</script>