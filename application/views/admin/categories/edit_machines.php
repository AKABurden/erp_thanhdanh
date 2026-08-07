<?php echo form_open(
    'admin/categories/edit_capacity/' . $machines['id'],
    array('id' => 'edit-machines', 'enctype' => 'multipart/form-data')
); ?>
<div class="modal-dialog modal-lg" style="min-width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_edit_machines'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('dt_category_machines', 'category_machine_id') ?>
                        <select name="category_machine_id" id="category_machine_id" data-none-selected-text="<?= lang('dt_category_machines') ?>" class="form-control category_machine_id">
                            <option value=""></option>
                            <?php if (!empty($dtCategoryMachines)) { ?>
                                <?php foreach ($dtCategoryMachines as $key => $value) : ?>
                                    <option <?= (!empty($machines) && $machines['category_machine_id'] == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>" data-content-name="<?= $value['name'] ?>" data-subtext="<?= $value['name'] ?>"><?= $value['code'] ?></option>
                                <?php endforeach ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Mã Thiết Bị/Công Việc', 'code') ?>
                        <?php echo form_input(
                            'code',
                            (isset($_POST['code']) ? $_POST['code'] : $machines['code']),
                            'placeholder="' . lang('Mã Thiết Bị/Công Việc') . '" id="code" required class="form-control input-tip"'
                        ); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Tên Thiết Bị/Công Việc', 'name') ?>
                        <?php echo form_input(
                            'name',
                            (isset($_POST['name']) ? $_POST['name'] : $machines['name']),
                            'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'
                        ); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_operating_gauge', 'operating_gauge') ?>
                                <?php echo form_input(
                                    'operating_gauge',
                                    (isset($_POST['operating_gauge']) ? $_POST['operating_gauge'] : $machines['operating_gauge']),
                                    'placeholder="' . lang('tnh_operating_gauge') . '" id="operating_gauge" class="form-control input-tip"'
                                ); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_standard', 'standard') ?>
                                <?php $value_standard = (isset($_POST['standard']) ? $_POST['standard'] : $machines['standard']); ?>
                                <select name="standard" id="standard" data-live-search="true" data-none-selected-text="<?= lang('Tiêu chuẩn') ?>" class="form-control standard">
                                    <option value=""></option>
                                    <?php if (!empty($standard)) { ?>
                                        <?php foreach ($standard as $key => $value) : ?>
                                            <option value="<?= $value['id'] ?>" <?= $value_standard == $value['id'] ? 'selected' : '' ?> data-content-name="<?= $value['name'] ?>"><?= $value['code'] ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                                <!--						--><?php //echo form_input('standard', (isset($_POST['standard']) ? $_POST['standard'] : $machines['standard']), 'placeholder="' . lang('tnh_standard') . '" id="standard" class="form-control input-tip"'); 
                                                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_pp_measure', 'pp_measure') ?>
                                <?php echo form_input(
                                    'pp_measure',
                                    (isset($_POST['pp_measure']) ? $_POST['pp_measure'] : $machines['pp_measure']),
                                    'placeholder="' . lang('tnh_pp_measure') . '" id="pp_measure" class="form-control input-tip"'
                                ); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_quota_productivity', 'quota_productivity') . '/h' ?>
                                <?php echo form_input(
                                    'quota_productivity',
                                    (isset($_POST['quota_productivity']) ? $_POST['quota_productivity'] : formatNumber($machines['quota_productivity'])),
                                    'placeholder="' . lang('tnh_quota_productivity') . '" id="quota_productivity" class="form-control number-format input-tip"'
                                ); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Ngày Bắt Đầu Bảo Trì', 'day_operation') ?>
                                <?php echo render_date_input(
                                    'day_operation',
                                    '',
                                    (isset($_POST['day_operation']) ? $_POST['day_operation'] : _d($machines['day_operation']))
                                ); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Số ngày cần bảo trì', 'number_day_operation') ?>
                                <?php echo render_input(
                                    'number_day_operation',
                                    '',
                                    (isset($_POST['number_day_operation']) ? $_POST['number_day_operation'] : $machines['number_day_operation'])
                                ); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Trạng Thái', 'status') ?>
                                <select name="status" id="status" data-none-selected-text="<?= lang('tnh_status') ?>" class="form-control status" required="required">
                                    <option value=""></option>
                                    <?php foreach (status_machine_new() as $key => $value) : ?>
                                        <option <?= $machines['status'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Định Mức Năng Suất/Tháng', 'product_in_month') ?>
                                <?php echo form_input(
                                    'product_in_month',
                                    (isset($_POST['product_in_month']) ? $_POST['product_in_month'] : number_format($machines['product_in_month'])),
                                    'placeholder="' . lang('tnh_product_in_month') . '" id="product_in_month" onkeyup="formatNumBerKeyUpCus(this)" class="form-control input-tip"'
                                ); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Thời Gian Chuẩn Bị (Giờ)', 'preparation_time') ?>
                                <?php echo render_input(
                                    'preparation_time',
                                    '',
                                    (isset($_POST['preparation_time']) ? $_POST['preparation_time'] : number_format_data($machines['preparation_time'])),
                                    'text',
                                    ['onchange' => 'formatNumBerKeyUpCus(this)']
                                ); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Định Mức Thời Gian Duyệt Màu', 'product_color') ?>
                                <?php echo form_input(
                                    'product_color',
                                    (isset($_POST['product_color']) ? $_POST['product_color'] : number_format_data($machines['product_color'])),
                                    'placeholder="' . lang('Định mức thời gian duyệt màu') . '" id="product_in_month" onkeyup="formatNumBerKeyUpCus(this)" class="form-control input-tip"'
                                ); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('NPL canh bài', 'soup_ingredients') ?>
                                <input type="text" name="soup_ingredients" id="soup_ingredients" class="form-control soup_ingredients number-format" value="<?= $machines['soup_ingredients'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_time_change_size', 'time_change_size') ?>
                                <input type="text" name="time_change_size" id="time_change_size" class="form-control time_change_size number-format" value="<?= $machines['time_change_size'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Định Mức Khấu Hao', 'depreciation_rates') ?>
                                <input type="text" name="depreciation_rates" id="depreciation_rates" class="form-control time_change_size number-format" value="<?= $machines['depreciation_rates'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Định Mức Thời Gian Khấu Hao', 'depreciation_period') ?>
                                <input type="text" name="depreciation_period" id="depreciation_period" class="form-control depreciation_period number-format" value="<?= $machines['depreciation_period'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Định Mức Thời Gian Sử Dụng', 'used_time') ?>
                                <input type="text" name="used_time" id="used_time" class="form-control used_time number-format" value="<?= $machines['used_time'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Model', 'model') ?>
                                <input type="text" name="model" id="model" class="form-control model" value="<?= $machines['model'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Xuất Xứ', 'origin') ?>
                                <input type="text" name="origin" id="origin" class="form-control origin" value="<?= $machines['origin'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Năm Sản Xuất', 'year_manu') ?>
                                <input type="text" name="year_manu" id="year_manu" class="form-control year_manu" value="<?= $machines['year_manu'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Hiệu Suất', 'performance') ?>
                                <input type="text" name="performance" id="performance" class="form-control performance" value="<?= $machines['performance'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Kỹ Thuật Ghi Bản', 'recording_technique') ?>
                                <input type="text" name="recording_technique" id="recording_technique" class="form-control recording_technique" value="<?= $machines['recording_technique'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Đặc Điểm Vật Lý', 'physical_characteristics') ?>
                                <input type="text" name="physical_characteristics" id="physical_characteristics" class="form-control physical_characteristics" value="<?= $machines['physical_characteristics'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Khổ Giấy(Max)', 'paper_size_max') ?>
                                <input type="text" name="paper_size_max" id="paper_size_max" class="form-control paper_size_max" value="<?= $machines['paper_size_max'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Khổ Giấy(Min)', 'paper_size_min') ?>
                                <input type="text" name="paper_size_min" id="paper_size_min" class="form-control paper_size_min" value="<?= $machines['paper_size_min'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Điện Áp', 'voltage') ?>
                                <input type="text" name="voltage" id="voltage" class="form-control voltage" value="<?= $machines['voltage'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= lang('Tốc Độ', 'speed') ?>
                                <input type="text" name="speed" id="speed" class="form-control speed" value="<?= $machines['speed'] ?>">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_procedure">Quy Trình</a></li>
                        <li><a data-toggle="tab" href="#tab_maintenance">Bảo Trì</a></li>
                        <li><a data-toggle="tab" href="#tab_cleaning_5s">Thiết bị 5S</a></li>
                        <li><a data-toggle="tab" href="#tab_pccc">Thiết bị PCCC</a></li>
                        <li><a data-toggle="tab" href="#tab_accreditation">Thiết bị Test/Kiểm Định</a></li>
                        <li><a data-toggle="tab" href="#machines_maintenance">Bộ phận thiết bị</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_procedure" class="tab-pane fade in active">
                            <table id="tb-process" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addProcess()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th><?= lang('tnh_process') ?><span class="text-danger">*</span></th>
                                        <th>File đính kèm</th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $machines_process = $this->category_model->getMachinesProcess($machines['id']);
                                    if (!empty($machines_process)) {
                                        foreach ($machines_process as $key => $value) {
                                            $tdNumbers = '<td class="text-center td-numbers">' . (++$key) . '</td>';
                                            $tdProcess = '<td>
                                                    <input type="hidden" name="machines_process_id[]" class="form-control machines_process_id" value="' . $value['id'] . '">
                                                    <input type="text" name="process[]" class="form-control process" value="' . $value['process'] . '" placeholder="' . lang('tnh_process') . '">
                                                </td>';

                                            $getFile = get_table_where(
                                                'tblfiles',
                                                ['rel_type' => 'rel_process', 'rel_id' => $value['id']]
                                            );
                                            $viewFile = '';
                                            foreach ($getFile as $k => $v) {
                                                if (!empty($getFile)) {
                                                    if (explode('/', $v['filetype'])[0] == 'image') {
                                                        $viewFile .= '<div class="url_file" style="margin-bottom:5px; margin-top:5px;">
                                                                            <div class="preview_image" style="width: auto;margin-bottom: 5px; margin-top: 5px;">		
                                                                                <div class="display-block contract-attachment-wrapper img">
                                                                                    <span class="float-left">
                                                                                        <a href="' . base_url('uploads/machines/' . $value['id'] . '/' . $v['file_name']) . '" data-lightbox="customer-profile" class="display-block mbot5 col-md-9">	
                                                                                            <div class="">		                     
                                                                                               <i class="fa fa-file-image-o" aria-hidden="true"></i> ' . $v['file_name'] . '
                                                                                            </div>		                             
                                                                                        </a>
                                                                                        <a class="btn-icon col-md-2 text-center">
                                                                                            <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileProcess(' . $v['id'] . ', this)"></i>
                                                                                        </a>		          
                                                                                    </span>	
                                                                                </div>		           
                                                                            </div>
                                                                            <hr class="mtop5 mbot5"/>
                                                                        </div>';
                                                    } else {
                                                        $viewFile .= '<div class="url_file">
                                                                            <a class="col-md-9" target="_blank" href="' . base_url('uploads/machines/' . $value['id'] . '/' . $v['file_name']) . '"><i class="fa fa-file-archive-o" aria-hidden="true"></i> ' . $v['file_name'] . '</a> 
                                                                            <a class="btn btn-icon col-md-2">
                                                                                <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileProcess(' . $v['id'] . ', this)"></i>
                                                                            </a>
                                                                            <div class="clearfix"></div>
                                                                            <hr class="mtop5 mbot5"/>
                                                                         </div>';
                                                    }
                                                }
                                            }

                                            $tdFile = '<td>' . $viewFile . '
                                                                <input type="file" name="file[' . ($key - 1) . ']" class="form-control" value="">
                                                            </td>';

                                            $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>';

                                            echo '<tr>
                                                        ' . $tdNumbers . '
                                                        ' . $tdProcess . '
                                                        ' . $tdFile . '
                                                        ' . $tdActions . '
                                                    </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_maintenance" class="tab-pane fade in">
                            <table id="tb-maintenance" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addMaintenance()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th>Bộ phận máy móc<span class="text-danger">*</span></th>
                                        <th>Số ngày cần bảo trì<span class="text-danger">*</span></th>
                                        <th>Ghi chú cách thức bảo trì</th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $machines_maintenance = get_table_where(
                                        'tbl_machines_maintenance',
                                        ['machines_id' => $machines['id']]
                                    ); ?>
                                    <?php if (!empty($machines_maintenance)) {
                                        foreach ($machines_maintenance as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center td-numbers-maintenance"><?= ($key + 1) ?></td>
                                                <td>
                                                    <input type="hidden" name="id_maintenance[<?= $key ?>]" class="form-control" value="<?= $value['id'] ?>">
                                                    <input type="text" name="maintenance[<?= $key ?>]" required class="form-control" value="<?= $value['name'] ?>" placeholder="Bộ phận" aria-invalid="false">
                                                </td>
                                                <td>
                                                    <input type="text" name="month[<?= $key ?>]" class="form-control" value="<?= $value['month'] ?>" placeholder="Số ngày cần bảo trì" aria-invalid="false">
                                                </td>

                                                <?php
                                                $getFile = get_table_where(
                                                    'tblfiles',
                                                    ['rel_type' => 'rel_main', 'rel_id' => $value['id']]
                                                );
                                                $viewFile = '';
                                                foreach ($getFile as $k => $v) {
                                                    if (!empty($getFile)) {
                                                        if (explode('/', $v['filetype'])[0] == 'image') {
                                                            $viewFile .= '<div class="url_file" style="margin-bottom:5px; margin-top:5px;">
                                                                                <div class="preview_image" style="width: auto;margin-bottom: 5px; margin-top: 5px;">		
                                                                                    <div class="display-block contract-attachment-wrapper img">
                                                                                        <span class="float-left">
                                                                                            <a href="' . base_url('uploads/machines_maintenance/' . $value['id'] . '/' . $v['file_name']) . '" data-lightbox="customer-profile" class="display-block mbot5 col-md-9">	
                                                                                                <div class="">		                     
                                                                                                   <i class="fa fa-file-image-o" aria-hidden="true"></i> ' . $v['file_name'] . '
                                                                                                </div>		                             
                                                                                            </a>
                                                                                            <a class="btn-icon col-md-2 text-center">
                                                                                                <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileMain(' . $v['id'] . ', this)"></i>
                                                                                            </a>		          
                                                                                        </span>	
                                                                                    </div>		           
                                                                                </div>
                                                                                <hr class="mtop5 mbot5"/>
                                                                            </div>';
                                                        } else {
                                                            $viewFile .= '<div class="url_file">
                                                                                <a class="col-md-9" target="_blank" href="' . base_url('uploads/machines_maintenance/' . $value['id'] . '/' . $v['file_name']) . '"><i class="fa fa-file-archive-o" aria-hidden="true"></i> ' . $v['file_name'] . '</a> 
                                                                                <a class="btn btn-icon col-md-2">
                                                                                    <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileMain(' . $v['id'] . ', this)"></i>
                                                                                </a>
                                                                                <div class="clearfix"></div>
                                                                                <hr class="mtop5 mbot5"/>
                                                                             </div>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <td>
                                                    <textarea type="text" name="note_main[<?= $key ?>]" class="form-control note mbot10" placeholder="Ghi chú cách thức bảo trì" aria-invalid="false"><?= $value['note_main'] ?></textarea>
                                                    <input type="file" name="file_main[<?= $key ?>][]" class="form-control file_main mbot10" multiple value="" placeholder="<?= lang('File') ?>">
                                                    <?= $viewFile ?>
                                                </td>
                                                <td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeMaintenance(this)"></i></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="machines_maintenance" class="tab-pane fade in">
                            <table id="tb-maintenanceh" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addMaintenanceh()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th>Tên<span class="text-danger">*</span></th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $machines_maintenance_h = get_table_where(
                                        'tbl_machines_maintenance_h',
                                        ['machines_id' => $machines['id']]
                                    ); ?>
                                    <?php if (!empty($machines_maintenance_h)) {
                                        foreach ($machines_maintenance_h as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center td-numbers-maintenance"><?= ($key + 1) ?></td>
                                                <td>
                                                    <input type="hidden" name="refrigeration[<?= $key ?>][id]" class="form-control" value="<?= $value['id'] ?>">
                                                    <input type="text" name="refrigeration[<?= $key ?>][name]" required class="form-control" value="<?= $value['name'] ?>" placeholder="Bộ phận" aria-invalid="false">
                                                </td>
                                                <td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeMaintenance(this)"></i></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_cleaning_5s" class="tab-pane fade in">
                            <table id="tb-cleaning_5s" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addCleaning_5s()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th><?= lang('Danh mục kiểm tra') ?><span class="text-danger">*</span></th>
                                        <th><?= lang('Tiêu Chuẩn 5S') ?></th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $machines_5s = get_table_where('tbl_machines_5s', ['machines_id' => $machines['id'], 'ballot_type' => 0], '');
                                    if (!empty($machines_5s)) {
                                        foreach ($machines_5s as $key => $value) {
                                            $tdNumbers = '<td class="text-center stt">' . ($key + 1) . '</td>';
                                            $td5s = '<td>
                                                               <input type="hidden" name="items_5s[' . $key . '][id]" class="form-control machines_5s_id" value="' . $value['id'] . '">
                                                               <input type="text" name="items_5s[' . $key . '][name]" class="form-control items_5s" value="' . $value['name'] . '" placeholder="' . lang('Bộ phận') . '">
                                                          </td>';

                                            $viewFile = !empty($value['img']) ? ViewHtmlImagesDt(base_url($value['img'])) : '';
                                            $tdFile = '<td>
                                                                 <textarea name="items_5s[' . ($key) . '][note]" class="form-control mbot10">' . (!empty($value['note']) ? $value['note'] : '') . '</textarea>
                                                                ' . $viewFile . '
                                                                    <input type="file" name="file_5s[' . ($key) . ']" class="form-control mtop5" value="">
                                                                </td>';

                                            $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>';
                                            echo '<tr>
                                                            ' . $tdNumbers . '
                                                            ' . $td5s . '
                                                            ' . $tdFile . '
                                                            ' . $tdActions . '
                                                        </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_pccc" class="tab-pane fade in">
                            <table id="tb-pccc" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addPCCC()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th><?= lang('Danh mục kiểm tra') ?><span class="text-danger">*</span></th>
                                        <th><?= lang('Tiêu Chuẩn PCCC') ?></th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $machines_5s = get_table_where('tbl_machines_5s', ['machines_id' => $machines['id'], 'ballot_type' => 1], '');
                                    if (!empty($machines_5s)) {
                                        foreach ($machines_5s as $key => $value) {
                                            $tdNumbers = '<td class="text-center stt">' . ($key + 1) . '</td>';
                                            $td5s = '<td>
                                                               <input type="hidden" name="items_pccc[' . $key . '][id]" class="form-control machines_5s_id" value="' . $value['id'] . '">
                                                               <input type="text" name="items_pccc[' . $key . '][name]" class="form-control items_pccc" value="' . $value['name'] . '" placeholder="' . lang('Bộ phận') . '">
                                                          </td>';

                                            $viewFile = !empty($value['img']) ? ViewHtmlImagesDt(base_url($value['img'])) : '';
                                            $tdFile = '<td>
                                                                 <textarea name="items_pccc[' . ($key) . '][note]" class="form-control mbot10">' . (!empty($value['note']) ? $value['note'] : '') . '</textarea>
                                                                ' . $viewFile . '
                                                                    <input type="file" name="file_pccc[' . ($key) . ']" class="form-control mtop5" value="">
                                                                </td>';

                                            $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removepccc(this)"></i></td>';
                                            echo '<tr>
                                                            ' . $tdNumbers . '
                                                            ' . $td5s . '
                                                            ' . $tdFile . '
                                                            ' . $tdActions . '
                                                        </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_accreditation" class="tab-pane fade in">
                            <table id="tb-accreditation" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addAccreditation()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th><?= lang('Danh mục kiểm tra') ?><span class="text-danger">*</span></th>
                                        <th><?= lang('Tiêu Chuẩn Test/Kiểm Định') ?></th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $machines_5s = get_table_where('tbl_machines_5s', ['machines_id' => $machines['id'], 'ballot_type' => 2], '');
                                    if (!empty($machines_5s)) {
                                        foreach ($machines_5s as $key => $value) {
                                            $tdNumbers = '<td class="text-center stt">' . ($key + 1) . '</td>';
                                            $td5s = '<td>
                                                               <input type="hidden" name="items_accreditation[' . $key . '][id]" class="form-control machines_5s_id" value="' . $value['id'] . '">
                                                               <input type="text" name="items_accreditation[' . $key . '][name]" class="form-control items_accreditation" value="' . $value['name'] . '" placeholder="' . lang('Bộ phận') . '">
                                                          </td>';

                                            $viewFile = !empty($value['img']) ? ViewHtmlImagesDt(base_url($value['img'])) : '';
                                            $tdFile = '<td>
                                                                 <textarea name="items_accreditation[' . ($key) . '][note]" class="form-control mbot10">' . (!empty($value['note']) ? $value['note'] : '') . '</textarea>
                                                                ' . $viewFile . '
                                                                    <input type="file" name="file_accreditation[' . ($key) . ']" class="form-control mtop5" value="">
                                                                </td>';

                                            $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeaccreditation(this)"></i></td>';
                                            echo '<tr>
                                                            ' . $tdNumbers . '
                                                            ' . $td5s . '
                                                            ' . $tdFile . '
                                                            ' . $tdActions . '
                                                        </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_specifications', 'specifications') ?>
                        <?php echo form_textarea(
                            'specifications',
                            (isset($_POST['specifications']) ? $_POST['specifications'] : $machines['specifications']),
                            'placeholder="' . lang('tnh_specifications') . '" id="specifications" class="form-control input-tip tinymce"'
                        ); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea(
                            'note',
                            (isset($_POST['note']) ? $_POST['note'] : $machines['note']),
                            'placeholder="' . lang('note') . '" id="note" class="form-control input-tip tinymce"'
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('tnh_edit') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var countItems = <?= !empty($machines_process) ? count($machines_process) : 0 ?>;
    var countItemsMain = <?= !empty($machines_maintenance) ? count($machines_maintenance) : 0 ?>;
    var countItems5S = <?= !empty($machines_5s) ? count($machines_5s) : 0 ?>;
    $(function() {
        init_datepicker();
        appValidateForm($('#edit-machines'), {
            code: 'required',
            name: 'required',
            category_machine_id: 'required',
            status: 'required'
        }, addMachines);

        function addMachines(form) {
            $('.add').attr('disabled', 'disabled');
            tinymce.get('note').save();
            tinymce.get('specifications').save();

            var formParams = $('#edit-machines').serializeArray();

            var formData = new FormData();
            // return

            $.each($('#edit-machines').find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });


            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                    url: site.base_url + 'admin/categories/edit_machines/<?= $machines['id'] ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                }).done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    console.log("error");
                });

            return false;
        }

        $('.status').selectpicker();
        $('.standard').selectpicker();
        $('.category_machine_id').selectpicker();
        init_editor('textarea[name="note"]');
        init_editor('textarea[name="specifications"]');

        $('#standard').change(function() {
            var content = $(this).find(':selected').data('content-name');
            $('#pp_measure').val(content);
        })

    })
</script>