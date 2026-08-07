<style>
    /* --- CSS THUẦN (KHÔNG DÙNG BOOTSTRAP) --- */

    /* 1. Modal Overlay & Container */
    #eprofile_modal {
        display: none; /* Ẩn mặc định */
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        box-sizing: border-box;
    }

    #eprofile_modal .modal-title {
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
    }

    #eprofile_modal * {
        box-sizing: border-box;
    }

    #eprofile_modal .modal-content {
        background-color: #fefefe;
        margin: 2% auto;
        border: 1px solid #888;
        width: 95%;
        max-width: 1100px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
        animation: slideDown 0.3s;
    }

    @keyframes slideDown {
        from {top: -50px; opacity: 0;}
        to {top: 0; opacity: 1;}
    }

    /* 2. Header & Footer */
    #eprofile_modal .modal-header {
        padding: 15px 25px;
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        color: #fff;
    }

    #eprofile_modal .modal-title {
        /*margin: 0;*/
        /*font-size: 1.25rem;*/
        /*color: #0d6efd;*/
        /*font-weight: 700;*/
    }

    #eprofile_modal .close-btn {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
        border: none;
        background: none;
    }

    #eprofile_modal .close-btn:hover {
        color: black;
    }

    #eprofile_modal .modal-body {
        padding: 20px;
        background-color: #fff;
        max-height: 80vh;
        overflow-y: auto;
    }

    #eprofile_modal .modal-footer {
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        text-align: right;
        border-radius: 0 0 8px 8px;
    }

    /* 3. Layout Grid System (Custom) */
    #eprofile_modal .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -10px;
        margin-left: -10px;
    }

    #eprofile_modal .col-3,
    #eprofile_modal .col-4,
    #eprofile_modal .col-6,
    #eprofile_modal .col-9,
    #eprofile_modal .col-12,
    #eprofile_modal .col {
        padding-right: 10px;
        padding-left: 10px;
        margin-bottom: 15px;
    }

    #eprofile_modal .col-2 { width: 17%; }
    #eprofile_modal .col-3 { width: 25%; }
    #eprofile_modal .col-4 { width: 33.3333%; }
    #eprofile_modal .col-6 { width: 50%; }
    #eprofile_modal .col-9 { width: 75%; }
    #eprofile_modal .col-10 { width: 83%; }
    #eprofile_modal .col-12 { width: 100%; }

    /* Responsive Mobile */
    @media (max-width: 768px) {
        #eprofile_modal .col-2,
        #eprofile_modal .col-3,
        #eprofile_modal .col-4,
        #eprofile_modal .col-6,
        #eprofile_modal .col-10,
        #eprofile_modal .col-9 {
            width: 100%;
        }
    }

    /* 4. Form Elements */
    #eprofile_modal .form-group {
        margin-bottom: 0;
    }

    /*#eprofile_modal label {*/
    /*    display: block;*/
    /*    margin-bottom: 5px;*/
    /*    font-weight: 600;*/
    /*    color: #343a40;*/
    /*    font-size: 0.9rem;*/
    /*}*/

    #eprofile_modal .text-muted { color: #6c757d; font-weight: normal; font-size: 0.85rem;}
    #eprofile_modal .text-danger { color: #dc3545; }

    #eprofile_modal input[type="text"],
    #eprofile_modal input[type="email"],
    #eprofile_modal input[type="tel"],
    #eprofile_modal input[type="date"],
    #eprofile_modal input[type="number"],
    #eprofile_modal select,
    #eprofile_modal textarea {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out;
    }

    #eprofile_modal input:focus,
    #eprofile_modal select:focus,
    #eprofile_modal textarea:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    /* 5. Custom Components */
    #eprofile_modal .section-title {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 10px 15px;
        border-left: 5px solid #0d6efd;
        font-weight: bold;
        margin-bottom: 20px;
        font-size: 1rem;
        text-transform: uppercase;
    }

    #eprofile_modal .card-panel {
        background: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    #eprofile_modal .card-bg-light {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    /* Avatar Upload */
    #eprofile_modal .avatar-wrapper {
        width: 140px;
        height: 140px;
        border: 2px dashed #ccc;
        border-radius: 50%;
        margin: 0 auto 10px;
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #eprofile_modal .avatar-wrapper:hover { border-color: #0d6efd; }
    #eprofile_modal .avatar-preview {
        width: 100%; height: 100%; object-fit: cover; display: none; position: absolute; top:0; left:0;
    }
    #eprofile_modal .avatar-placeholder { text-align: center; color: #999; font-size: 12px; }

    /* Skill Matrix Checkboxes */
    #eprofile_modal .skill-group {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
    #eprofile_modal .skill-label {
        width: 25%;
        font-weight: bold;
        color: #6c757d;
        text-align: center;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding-right: 15px;
        border-right: 2px solid #eee;
    }
    #eprofile_modal .skill-options { width: 75%; padding-left: 20px; }
    #eprofile_modal .checkbox-grid { display: flex; flex-wrap: wrap; }
    #eprofile_modal .checkbox-item { width: 33.33%; margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center;}
    #eprofile_modal .checkbox-item input { margin-right: 8px; width: auto; }
    #eprofile_modal .checkbox-item label { margin-bottom: 0; font-weight: normal; cursor: pointer;}

    /* Buttons */
    #eprofile_modal .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        padding: 8px 16px;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 4px;
        transition: all 0.15s;
    }
    #eprofile_modal .btn-secondary { color: #fff; background-color: #6c757d; border-color: #6c757d; }
    #eprofile_modal .btn-secondary:hover { background-color: #5a6268; }
    #eprofile_modal .btn-primary { color: #fff; background-color: #0d6efd; border-color: #0d6efd; }
    #eprofile_modal .btn-primary:hover { background-color: #0b5ed7; }

    /* Input Group Mockup */
    #eprofile_modal .input-group { display: flex; }
    #eprofile_modal .input-group input { border-top-right-radius: 0; border-bottom-right-radius: 0; flex: 1;}

    #eprofile_modal .input-group-addon {
        background-color: #f8f9fc;
        border: 1px solid #d1d3e2;
        border-left: none;
        color: #6e707e;
        border-radius: 0 4px 4px 0;
        font-weight: 600;
        font-size: 11px;
        padding: 10px 10px;
        width: auto!important;
    }

</style>
<style>
    /* Vùng thả file */
    .file-upload-box {
        display: flex;
        justify-content: center;
        align-items: center;
        border: 2px dashed #0d6efd;
        border-radius: 8px;
        padding: 20px;
        background: #f8fbff;
        cursor: pointer;
        transition: all 0.2s;
        min-height: 120px;
        margin-bottom: 10px;
    }

    /* Hiệu ứng khi kéo file vào */
    .file-upload-box.dragover {
        background: #e7f1ff;
        border-color: #0044cc;
        transform: scale(0.99);
    }

    /* Danh sách file */
    .file-preview-item {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .file-preview-icon {
        font-size: 24px;
        margin-right: 12px;
    }

    .file-preview-info {
        flex: 1;
        overflow: hidden; /* Tránh vỡ layout nếu tên file dài */
    }

    .file-preview-name {
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-remove {
        color: #dc3545;
        cursor: pointer;
        padding: 5px 10px;
        font-size: 18px;
        margin-left: 10px;
        transition: background 0.2s;
        border-radius: 4px;
    }
    .file-remove:hover {
        background-color: #ffe6e6;
    }
    .isrow {
        margin-right: -10px;
        margin-left: -10px;
    }
</style>


<!-- === MODAL CONTAINER === -->
<div id="eprofile_modal"  class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 65%;">
        <div class="modal-content">
                <?php
                    echo form_open(admin_url('recruitment/detail_eprofile/' . ($eprofile['id'] ?? '')), array('id' => 'eprofile-form', 'enctype' => 'multipart/form-data'));
                    if(!empty($id_requirements)) {
                        echo form_hidden('id_requirements', $id_requirements ?? '');
                    }
                ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h5 class="modal-title"><i class="fas fa-user-plus mright5"></i> <?=$title ?? '' ?></h5>
                </div>
                <div class="modal-body">
                    <div class="card-panel" style="border-left: 4px solid #0d6efd;">
                        <div class="section-title" style="margin-top:0;">I. Phân Loại Ứng Viên</div>
                        <div class="row">
                            <?php $colOne = 'col-6'?>
                            <?php
                                if(empty($id_requirements) && empty($eprofile)) {?>
                                    <div class="col-4">
                                        <label>Phiếu yêu cầu tuyển dụng</label>
                                        <select name="id_requirements" id="id_requirements">
                                            <?php
                                                if(!empty($list_requirements)) {
                                                    foreach($list_requirements as $requirement) {
                                                        echo '<option value="'.$requirement['id'].'" data-role_level="'.$requirement['role_level'].'">'.$requirement['code'].' - '.$requirement['name'].'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php $colOne = 'col-4'?>
                                <?php }
                            ?>
                            <div class="<?=$colOne?>">
                                <?php $value = !empty($eprofile['role_level']) ? $eprofile['role_level'] : ''?>
                                <label>Cấp bậc (Role Level)</label>
                                <select id="role_level" disabled>
                                    <?php
                                        foreach($this->list_role_level as $role_level) {
                                            $selected = ($value == $role_level['id'] || (!empty($requirements) && $requirements->role_level == $role_level['id'])) ? 'selected' : '';
                                            if(!empty($selected)) {
                                                $id_role_level = $role_level['id'];
                                            }
                                            echo '<option value="'.$role_level['id'].'" '.$selected.'>'.$role_level['name'].'</option>';
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="role_level" value="<?=!empty($id_role_level) ? $id_role_level : 0?>">
                            </div>
                            <div class="<?=$colOne?>">
                                <label>Nguồn ứng viên</label>
                                <?php $value = !empty($eprofile['source']) ? $eprofile['source'] : ''?>
                                <select name="source">
                                    <?php foreach($this->list_source as $source) {
                                        $selected = ($value == $source['id']) ? 'selected' : '';
                                        echo '<option value="'.$source['id'].'" '.$selected.'>'.$source['name'].'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 2. THÔNG TIN CÁ NHÂN -->
                    <div class="card-panel">
                        <div class="section-title">II. Thông Tin Ứng Viên</div>
                        <div class="row">
                            <!-- Cột Avatar -->
                            <div class="col-3" style="text-align: center; border-right: 1px solid #eee;">
                                <label class="avatar-wrapper" for="avatar_input">
                                    <div class="avatar-placeholder" style="<?=!empty($eprofile['avatar']) ? 'display: none;' : ''?>">
                                        <i class="fas fa-camera fa-2x"></i><br>Tải ảnh 3x4
                                    </div>
                                    <img id="avatar_preview" class="avatar-preview" src="<?=!empty($eprofile['avatar']) ? base_url($eprofile['avatar']) : ''?>"  style="<?=!empty($eprofile['avatar']) ? 'display: inline;' : ''?>";>
                                </label>
                                <input type="file" id="avatar_input" name="avatar" accept="image/*" style="display:none;">
                                <div style="font-size: 12px; color: #888;">Click để chọn ảnh</div>
                            </div>

                            <!-- Cột Thông tin -->
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-6">
                                        <?php $value = !empty($eprofile['full_name']) ? $eprofile['full_name'] : ''?>
                                        <label for="full_name">Họ và tên</label>
                                        <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Họ và tên" value="<?=$value?>">
                                    </div>
                                    <div class="col-6">
                                        <?php $value = !empty($eprofile['phone_number']) ? $eprofile['phone_number'] : ''?>
                                        <label for="phone_number">Số điện thoại</label>
                                        <input type="tel" name="phone_number" placeholder="Số điện thoại" value="<?=$value?>">
                                    </div>
                                    <div class="col-6">
                                        <?php $value = !empty($eprofile['email']) ? $eprofile['email'] : ''?>
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?=$value?>">
                                    </div>
                                    <div class="col-6">
                                        <?php $value = !empty($eprofile['date_of_birth']) ? _dC($eprofile['date_of_birth']) : ''?>
                                        <?=render_date_input('date_of_birth', 'Ngày sinh', $value)?>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="gender">Giới tính</label>
                                            <div class="clearfix"></div>
                                            <?php $value = !empty($eprofile['gender']) ? $eprofile['gender'] : 'male'?>
                                            <div class="col-md-6 form-check">
                                                <input class="form-check-input" type="radio" name="gender" value="male" id="gender_male" <?=$value == 'male' ? 'checked' : ''?>>
                                                <label class="form-check-label" for="gender_male">NAM</label>
                                            </div>
                                            <div class="col-md-6 form-check">
                                                <input class="form-check-input" type="radio" name="gender" value="female" id="gender_female" <?=$value == 'female' ? 'checked' : ''?>>
                                                <label class="form-check-label" for="gender_female">NỮ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <?php $value = !empty($eprofile['marital_status']) ? $eprofile['marital_status'] : ''?>
                                        <label for="marital_status">Tình trạng hôn nhân</label>
                                        <select name="marital_status" id="marital_status" data-placeholder="<?= lang('tnh_marital_status') ?>" class="marital_status" style="width: 100%;">
                                            <option value=""></option>
                                            <?php foreach($this->list_marital_status as $marital_status) {
                                                $selected = ($value == $marital_status['id']) ? 'selected' : '';
                                                echo '<option value="'.$marital_status['id'].'" '.$selected.'>'.$marital_status['name'].'</option>';
                                            }?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <?php $value = !empty($eprofile['current_address']) ? $eprofile['current_address'] : ''?>
                                        <?=render_textarea('current_address', 'Địa chỉ thường trú', $value)?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-panel">
                        <div class="section-title">III. Thông tin chi tiết</div>
                        <div class="row">
                            <div class="col-6">
                                <?php $value = !empty($eprofile['id_card']) ? $eprofile['id_card'] : ''?>
                                <?=render_input('id_card', 'CMND/CCCD', $value, 'number')?>
                            </div>

                            <div class="col-6">
                                <?php $value = !empty($eprofile['date_of_issue']) ? _dC($eprofile['date_of_issue']) : ''?>
                                <?=render_date_input('date_of_issue', 'Ngày cấp', $value)?>
                            </div>

                            <div class="col-6">
                                <?php $value = !empty($eprofile['educational']) ? $eprofile['educational'] : ''?>
                                <div class="form-group">
                                    <label for="educational" class="control-label">Trình độ học vấn</label>
                                    <select name="educational" id="educational">
                                        <option value="">-- Chọn trình độ học vấn --</option>
                                        <?php foreach ($this->list_educational as $key => $item): ?>
                                            <option value="<?= $item['id'] ?>"
                                                <?= ($value == $item['id'] ? 'selected' : '') ?>>
                                                <?= $item['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                            <div class="col-6">
                                <?php $value = !empty($eprofile['training_school']) ? $eprofile['training_school'] : ''?>
                                <?=render_input('training_school', 'Truờng đào tạo', $value)?>
                            </div>
                            <?php $value = !empty($eprofile['academic_ranking']) ? $eprofile['academic_ranking'] : ''?>
                            <div class="col-6 div_academic_ranking <?=($value == 'other' || $value =='no_highschool' || $value =='student') ? 'hide' : ''?>">
                                <div class="form-group">
                                    <label for="academic_ranking">Xếp loại</label>
                                    <select name="academic_ranking" id="academic_ranking">
                                        <?php foreach ($this->list_academic_ranking as $item): ?>
                                            <option value="<?= $item['id'] ?>"
                                                <?= ($value == $item['id'] ? 'selected' : '') ?>>
                                                <?= $item['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <?=render_input('years_of_experience', 'Tổng số năm kinh nghiệm', $eprofile['years_of_experience'] ?? '', 'number')?>
                            </div>
                        </div>
                    </div>

                    <div class="card-panel">
                        <div class="section-title">IV. Thông Tin Chức Vụ Và Vị Trí Đã Làm</div>
                        <div id="experience-container">
                            <div class=" border p-3 mb-3 position-relative">
                                <div class="isrow row-experience">
                                    <?php if(!empty($eprofile_job)) { ?>
                                        <?php foreach($eprofile_job as $key => $value) { ?>
                                            <div class="experience-item isrow">
                                                <div class="col-md-11">
                                                    <div class="col-md-6 col">
                                                        <div class="form-group">
                                                            <label for="the_company_did" class="control-label">Công ty đã làm</label>
                                                            <input type="text"  name="the_company_did[]" class="form-control" required="required" value="<?=($value['the_company_did'] ?? '')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col">
                                                        <div class="form-group">
                                                            <label for="job_title" class="control-label">Chức danh</label>
                                                            <input type="text" name="job_title[]" class="form-control" required="required" value="<?=($value['job_title'] ?? '')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col">
                                                        <div class="form-group">
                                                            <label for="year_job[]" class="control-label">Số năm đã làm</label>
                                                            <input type="text" id="year_job[]" name="year_job[]" class="form-control" required="required" value="<?=($value['year_job'] ?? '')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col">
                                                        <div class="form-group">
                                                            <label for="achievements[]" class="control-label">Thành tựu nổi bật</label>
                                                            <input type="text" id="achievements[]" name="achievements[]" class="form-control" required="required" value="<?=($value['achievements'] ?? '')?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn-icon btn btn-danger btn-sm remove-exp mtop20 pull-right">Xóa</button>
                                                </div>
                                                <div class="clearfix"></div>
                                                <hr/>
                                            </div>
                                        <?php }?>
                                    <?php } else {?>
                                        <div class="experience-item isrow">
                                            <div class="col-md-11">
                                                <div class="col-md-6 col">
                                                    <div class="form-group">
                                                        <label for="the_company_did[]" class="control-label">Công ty đã làm</label>
                                                        <input type="text" id="the_company_did[]" name="the_company_did[]" class="form-control" required="required" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col">
                                                    <div class="form-group">
                                                        <label for="job_title[]" class="control-label">Chức danh</label>
                                                        <input type="text" id="job_title[]" name="job_title[]" class="form-control" required="required" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col">
                                                    <div class="form-group">
                                                        <label for="year_job[]" class="control-label">Số năm đã làm</label>
                                                        <input type="text" id="year_job[]" name="year_job[]" class="form-control" required="required" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col">
                                                    <div class="form-group">
                                                        <label for="achievements[]" class="control-label">Thành tựu nổi bật</label>
                                                        <input type="text" id="achievements[]" name="achievements[]" class="form-control" required="required" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn-icon btn btn-danger btn-sm remove-exp mtop20 pull-right">Xóa</button>
                                            </div>
                                            <div class="clearfix"></div>
                                            <hr/>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="add-experience" class="btn btn-icon btn-success btn-sm mt-2">
                                    <i class="fa fa-plus"></i> Thêm kinh nghiệm làm việc
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <div class="col-12">
                            <?php $value = (!empty($eprofile['info_other']) ? $eprofile['info_other'] : '')?>
                            <?=render_textarea('info_other', 'Thông tin khác', $value)?>
                        </div>
                    </div>

                    <!-- 4. LƯƠNG & FILE -->
                    <div class="card-panel" style="background-color: #fdfdfe; border: 2px dashed #b0b8d1;">
                        <div class="row">
                            <div class="col-6">
                                <label for="expected_salary" style="color:#0d6efd;">Mức lương mong muốn</label>
                                <div class="input-group">
                                    <?php $value = (!empty($eprofile['expected_salary']) ? number_format_data($eprofile['expected_salary']) : '')?>
                                    <input type="text" id="expected_salary" name="expected_salary" class="currency-input" value="<?=$value?>" placeholder="0" style="text-align: right; font-weight: bold; color: #0d6efd;">
                                    <div class="input-group-addon">VNĐ</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label style="font-weight: 600;">Hồ sơ đính kèm (CV/Portfolio) <span class="text-danger">*</span></label>

                                <div class="upload-container" style="margin-top: 5px;">

                                    <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xlsx,.png,.jpg" style="display: none;">

                                    <label for="attachments" class="file-upload-box" id="dropArea">
                                        <div class="upload-content text-center">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: #0d6efd; margin-bottom: 10px;"></i>
                                            <p style="margin: 0; font-size: 14px;">Kéo thả file hoặc <span style="color:#0d6efd; font-weight:bold;">Click để chọn</span></p>
                                            <small style="color: #6c757d;">Hỗ trợ: PDF, DOC, DOCX, Ảnh</small>
                                        </div>
                                    </label>

                                    <div class="file-preview-list" id="filePreviewList"></div>
                                    <div class="hide" id="FileDelete"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <?php $value = !empty($eprofile['cv_link']) ? $eprofile['cv_link'] : ''?>
                                <?=render_input('cv_link', 'Link CV Online', $value)?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12" style="padding:0">
                        <?php $value = !empty($eprofile['hr_note']) ? $eprofile['hr_note'] : ''?>
                        <label>Ghi chú HR</label>
                        <textarea name="hr_note" id="hr_note" rows="3" placeholder="Ghi chú sơ loại..."><?=$value?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close mleft5"  class="close" data-dismiss="modal" aria-label="Close">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Lưu Hồ Sơ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#role_level').change(function() {
            var selectedRoleLevel = $(this).val();
            $('input[name="role_level"]').val(selectedRoleLevel);
        })

        $('#id_requirements').change(function() {
            var selectedRoleLevel = $('#id_requirements').find('option:selected').attr('data-role_level');
            console.log(selectedRoleLevel);
            $('input[name="role_level"]').val(selectedRoleLevel);
            $('#role_level').val(selectedRoleLevel);
        })


        // --- 1. MODAL LOGIC (Pure JS) ---
       $('#eprofile_modal').modal('show');

        // --- 2. PREVIEW AVATAR ---
        $("#avatar_input").change(function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatar_preview').attr('src', e.target.result).show();
                    $('.avatar-placeholder').hide();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // --- 3. FORMAT TIỀN TỆ ---
        $('.currency-input').on('keyup', function() {
            var val = $(this).val().replace(/\D/g, "");
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });

        // --- 4. SUBMIT FORM ---
        // $('#eprofile-form').on('submit', function(e) {
        //     e.preventDefault();
        //     var btn = $('#submitBtn');
        //     var originalText = btn.html();
        //
        //     btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');
        //
        //     // Lấy lương dạng số raw
        //     var salaryRaw = $('input[name="salary"]').val().replace(/,/g, '');
        //     console.log("Salary Raw:", salaryRaw);
        //
        //     setTimeout(function() {
        //         alert("Đã lưu thành công! Kiểm tra console.");
        //         btn.prop('disabled', false).html(originalText);
        //         // modal.fadeOut(); // Đóng modal nếu cần
        //     }, 1000);
        // });
    });

    $(document).ready(function() {
        // 1. Thêm hàng mới
        $('#add-experience').click(function() {
            // Clone hàng đầu tiên
            let newRow = $('.experience-item').first().clone();

            // Reset giá trị trong các ô input mới
            newRow.find('input').val('');

            // Append vào container
            $('.row-experience').append(newRow);
        });

        // 2. Xóa hàng (Dùng delegation vì hàng được thêm động)
        $('#eprofile_modal').on('click', '.remove-exp', function() {
            if ($('.experience-item').length > 1) {
                $(this).closest('.experience-item').remove();
            } else {
                alert("Phải có ít nhất một thông tin kinh nghiệm!");
            }
        });
    });

    $(function() {
        init_selectpicker();
        init_datepicker();



        // Format tiền tệ khi nhập liệu
        // $('input[data-type="currency"]').on({
        //     keyup: function() {
        //         formatCurrency($(this));
        //     },
        //     blur: function() {
        //         formatCurrency($(this), "blur");
        //     }
        // });

        // Validate form
        var ListValidate = {
            full_name: "required",
            current_address: "required",
            phone_number: "required",
            email: "required",
            id_card: "required",
            date_of_issue: "required",
            educational: "required",
            training_school: "required",
            date_of_birth: "required",
            "the_company_did[]": "required",
            "job_title[]": "required",
        };
        <?php
            if(!empty($eprofile['id'])) {?>
                ListValidate['attachments'] = "required";
            <?php }
        ?>
        _validate_form($('#eprofile-form'), ListValidate, uploadForm);

        function validateRequiredFields(form) {
            let isValid = true;

            // reset lỗi cũ
            $(form).find('.form-group').removeClass('has-error');
            $(form).find('.form-group').find('.error').remove();

            $(form).find('input[required], select[required], textarea[required]').each(function () {
                const $field = $(this);
                const type = $field.attr('type');
                let hasError = false;

                if (type === 'radio' || type === 'checkbox') {
                    hasError = !$('input[name="' + this.name + '"]:checked').length;
                } else {
                    hasError = !$field.val() || !$field.val().trim();
                }

                if (hasError) {
                    isValid = false;
                    $field.closest('.form-group').addClass('has-error');
                    $field.closest('.form-group').append(`<p class="error text-danger">Hãy nhập.</p>`);
                }
            });

            return isValid;
        }

        function uploadForm(form) {
            if (!validateRequiredFields(form)) {
                alert_float('danger', 'Vui lòng nhập đầy đủ các trường');
                return false;
            }

            var ktName = isValidVietnameseName($('#full_name').val());
            if(!ktName) {
                return false;
            }
            var ktPhone = isValidVNPhone($('input[name="phone_number"]').val());
            if(!ktPhone) {
                return false;
            }
            var ktCCCD = isValidCCCD($('input[name="id_card"]').val());
            if(!ktCCCD) {
                return false;
            }

            if($('.file-preview-item').length == 0) {
                alert_float('danger', 'Vui lòng upload lên ít nhất 1 file CV');
                return false;
            }

            var submitBtn = $(form).find('button[type="submit"]');
            submitBtn.button('loading');
            // tinymce.get('note').save();
            // var data = $(form).serialize();

            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            var url = $(form).attr('action');
            $.ajax({
                url : url,
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
            .done(function(response) {
                alert_float(response.alert_type, response.message);
                if (response.success) {
                    if(typeof tAPI !== 'undefined'){ tAPI.draw('page'); }
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                    $('#eprofile_modal').modal('hide');
                }
                return false;
            }).always(function() {
                submitBtn.button('reset');
            })
        }

        $('#educational').change(function() {
            if($(this).val() == 'no_highschool' || $(this).val() == 'other' || $(this).val() == 'student') {
                $('.div_academic_ranking').addClass('hide');
                $('#academic_ranking').val('');
            }
            else {
                $('.div_academic_ranking').removeClass('hide');
            }
        })

        function isValidVietnameseName(name) {
            name = name.trim();
            const regex = /^[A-Za-zÀ-ỹ]+(?:\s[A-Za-zÀ-ỹ]+)*$/u;
            if(!regex.test(name)) {
                alert_float('danger', 'Tên không hợp lệ');
                return false;
            }
            return true;
        }

        function isValidVNPhone(phone) {
            phone = phone.trim();
            const regex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
            if(!regex.test(phone)) {
                alert_float('danger', 'Số điện thoại không hợp lệ');
                return false;
            }
            return true;
        }

        function isValidCCCD(cccd) {
            cccd = cccd.trim();
            const regex =  /^[0-9]{12}$/;
            if(!regex.test(cccd)) {
                alert_float('danger', 'Số CCCD không hợp lệ');
                return false;
            }
            return true;

        }

        $('#full_name').change(function() {
            isValidVietnameseName($(this).val());
        })
        $('input[name="phone_number"]').change(function() {
            isValidVNPhone($(this).val());
        })
        $('input[name="id_card"]').change(function() {
            isValidCCCD($(this).val());
        })

    });
</script>

<script>
    // document.addEventListener("DOMContentLoaded", function() {

    $(document).ready(function() {
        const fileInput = document.getElementById('attachments');
        const dropArea = document.getElementById('dropArea');
        const previewList = document.getElementById('filePreviewList');

        // Mảng chứa các file hiện tại (để xử lý xóa mà không mất file khác)
        let currentFiles = [];

        // --- 1. XỬ LÝ SỰ KIỆN CHỌN FILE (CLICK) ---
        fileInput.addEventListener('change', function(e) {
            // Gộp file mới vào danh sách cũ (hoặc thay thế tùy logic bạn muốn)
            // Ở đây mình làm logic: Chọn thêm là thêm vào danh sách
            const newFiles = Array.from(e.target.files);

            newFiles.forEach(file => {
                // Kiểm tra trùng lặp nếu cần
                if(!currentFiles.some(f => f.name === file.name && f.size === file.size)){
                    currentFiles.push(file);
                }
            });

            updateInputAndPreview();
        });

        // --- 2. XỬ LÝ KÉO THẢ (DRAG & DROP) ---
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Hiệu ứng visual
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        // Xử lý khi thả file
        dropArea.addEventListener('drop', function(e) {

            // console.log(11);
            const dt = e.dataTransfer;
            const newFiles = Array.from(dt.files);

            newFiles.forEach(file => {
                if(!currentFiles.some(f => f.name === file.name && f.size === file.size)){
                    currentFiles.push(file);
                }
            });

            updateInputAndPreview();
        });

        // --- 3. HÀM CẬP NHẬT GIAO DIỆN & INPUT ---
        function updateInputAndPreview() {
            // Cập nhật lại FileList cho input (để khi submit form có dữ liệu)
            const dataTransfer = new DataTransfer();
            currentFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
            $(previewList).find('.new').remove();
            // Render lại giao diện
            // previewList.innerHTML = '';
            // console.log(22);
            // Render file mới chọn
            currentFiles.forEach((file, index) => {
                renderOneFile(file, index, false);
            });
        }

        function renderOneFile(file, index, isExisting) {
            let icon = '📄';
            if(file.type.includes('pdf')) icon = '📕';
            if(file.type.includes('word') || file.name.includes('.doc')) icon = '📘';
            if(file.type.includes('image')) icon = '🖼️';
            if(file.type.includes('sheet') || file.name.includes('.xls')) icon = '📗';
            // console.log(123);
            const div = document.createElement('div');
            div.className = 'file-preview-item new';
            div.innerHTML = `
                <div class="file-preview-icon">${icon}</div>
                <div class="file-preview-info">
                    <div class="file-preview-name" title="${file.name}">${file.name}</div>
                    <div class="file-preview-size">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                </div>
                <div class="file-remove" onclick="removeFile(${index})">&times;</div>
            `;
            previewList.appendChild(div);
        }

        // --- 4. HÀM XÓA FILE ---
        // Gán vào window để gọi được từ HTML onclick
        window.removeFile = function(index) {
            currentFiles.splice(index, 1); // Xóa khỏi mảng
            updateInputAndPreview();       // Render lại
        }
    });
</script>

<?php if (!empty($attachments)): ?>
    <script>
        $(document).ready(function() {
            const previewList = document.getElementById('filePreviewList');
            const existingFiles = <?= json_encode($attachments) ?>;

            existingFiles.forEach(file => {
                const ext = (file.external || '').toLowerCase();
                let icon = '📄';
                let label = ext.toUpperCase();
                // console.log(ext);
                switch (ext) {
                    case 'pdf':
                        icon = '📕';
                        break;
                    case 'doc':
                    case 'docx':
                        icon = '📘';
                        break;
                    case 'xls':
                    case 'xlsx':
                        icon = '📗';
                        break;
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                        icon = '🖼️';
                        break;
                }
                const div = document.createElement('div');
                div.className = 'file-preview-item';
                div.style.background = '#f0f0f0'; // Màu khác để phân biệt
                div.innerHTML = `
                    <div class="file-preview-icon">${icon}</div>
                    <div class="file-preview-info">
                        <div class="file-preview-name"><a href="<?= base_url() ?>${file.external_link}" target="_blank">${file.file_name}</a></div>
                        <div class="file-preview-size"><small>Đã tải lên</small></div>
                    </div>
                    <div class="file-remove" onclick="removeFileIsset(${file.id}, this)">×</div>
                `;
                previewList.appendChild(div);
            });
        });

        function removeFileIsset(id = '', _this) {
            $('#FileDelete').append(`<input type="hidden" name="file_delete[]" value="` + id + `">`);
            $(_this).parents('.file-preview-item').remove();
        }


    </script>
<?php endif; ?>
