<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();?>
<?php
    $CI->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
    $CI->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
    $departments_tasks_name = $CI->db->get_where('tbltask_department', ['task_id' => $task['id']])->row('list_name');
?>
<li data-task-id="<?php echo $task['id']; ?>" class="task<?php if ($task['current_user_is_assigned']) {
	echo ' current-user-task';
}
if ((!empty($task['duedate']) && strtotime($task['duedate']) < strtotime(date('Y-m-d H:i:s'))) && $task['status'] != Tasks_model::STATUS_COMPLETE) {
	echo ' overdue-task';
} ?><?php if (!$task['current_user_is_assigned'] && $task['current_user_is_creator'] == '0' && !is_admin()) {
	echo ' not-sortable';
} ?>">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 task-name">
                <a href="<?php echo admin_url('tasks/view/' . $task['id']); ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                    <span class="inline-block full-width mtop10 mbot10"><?php echo $task['name']; ?></span>
                </a>
				<?php if(!empty($task['category_tasks'])) {
					$category_tasks = get_table_where('tblcategory_tasks', ['id' => $task['category_tasks']], '', 'row');
				}?>
                <div><i><b style="text-transform: capitalize">Mã công việc:</b></i> <?=!empty($category_tasks) ? ($category_tasks->code) : ''?> <br/><?=!empty($category_tasks) ? ('<i>'.$category_tasks->content.'</i>') : ''?></div>
                <div><i><b style="text-transform: capitalize">Ngày bắt đầu:</b></i> <?=$task['startdate']?></div>
				<?php
//				$task_rel_value_list = get_table_where('tbldepartments_tasks', ['id' => $task['id_list_object']], '', 'row');
				$task_rel_data = get_relation_data($task['rel_type'], $task['rel_id']);
				$task_rel_value = get_relation_values($task_rel_data, $task['rel_type']);
				$row_QL = '';
                if(!empty($departments_tasks_name)) {
					$row_QL .= $departments_tasks_name;
				}
				if(!empty($task_rel_value['type'])) {
                    if(!empty($row_QL)) {
						$row_QL .= ', <br/><div class="col-md-1"></div>';
                    }

					$row_QL .= _l('c_tasks_' . $task_rel_value['type']) . ' <a target="_blank href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
				}
				?>
				<?php if(!empty($row_QL)) {?>
                    <div><b style="text-transform: capitalize">Liên quan đến</b> <i><?=$row_QL?></i></div>
				<?php } ?>
                <?php
				    $rowDepartments = '';
                    $CI->db->select('tbldepartments.*');
				    $CI->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
                    $departments = $CI->db->get_where('tbltask_department', ['task_id' => $task['id']])->result_array();
                    if(!empty($departments)) {
						$rowDepartments .= '<div style="margin-left: -5px;">';
                        foreach($departments as $k => $v) {
							$rowDepartments .= '<span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:'.(!empty($color_department[$k]) ? $color_department[$k] : 'black').';border:1px solid '.(!empty($color_department[$k]) ? $color_department[$k] : '').'">'.$v['name'].'</span>';
                        }
						$rowDepartments .= '</div>';
                    }
                ?>
                <?php if(!empty($rowDepartments)) {?>
                    <div><b style="text-transform: capitalize">Phòng ban</b> <span><?=$rowDepartments?></span></div>
                <?php } ?>
                <?php
				$content = strip_tags(($task['description']));
				if(mb_strlen($content, 'UTF-8') >= 150) {
					$content = '<span class="show_more pointer mbot5"><t>'.mb_substr($content, 0, 150, 'UTF-8').'... </t></span>';
				}
                ?>
                <hr class="mtop5 mbot5"/>
                <div><b style="text-transform: capitalize">Mô tả:</b> <span><?=$content?></span></div>
            </div>
            <div class="col-md-6 text-muted">
				<?php
				echo format_members_by_ids_and_names($task['assignees_ids'], $task['assignees'], false, 'staff-profile-image-xs');
				?>
            </div>
            <div class="col-md-6 text-right text-muted">
				<?php if ($task['total_checklist_items'] > 0) { ?>
                    <span class="mright5 inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_checklist_items'); ?>">
          <i class="fa fa-check-square-o" aria-hidden="true"></i>
          <?php echo $task['total_finished_checklist_items']; ?>
          /
          <?php echo $task['total_checklist_items']; ?>
        </span>
				<?php } ?>
                <span class="mright5 inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_comments'); ?>">
        <i class="fa fa-comments"></i> <?php echo $task['total_comments']; ?>
      </span>
                <span class="inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_view_attachments'); ?>">
       <i class="fa fa-paperclip"></i>
       <?php echo $task['total_files']; ?>
     </span>
            </div>
			<?php $tags = get_tags_in($task['id'], 'task');
			if (count($tags) > 0) { ?>
                <div class="col-md-12">
                    <div class="mtop5 kanban-tags">
						<?php echo render_tags($tags); ?>
                    </div>
                </div>
			<?php } ?>
        </div>
    </div>
</li>
