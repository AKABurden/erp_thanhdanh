<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" style="background: #f5f5f5; min-height: 100vh;">
        <div style="max-width: 1000px; margin: 0 auto; padding: 20px 0;">
            
            <!-- Sticky Header -->
            <div class="panel_s" style="position: sticky; top: 50px; z-index: 100; margin-bottom: 20px;">
                <div class="panel-body" style="padding: 15px 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <a href="<?php echo admin_url('audit_management/config'); ?>" class="btn btn-default btn-sm" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h3 class="tw-mt-0 tw-mb-0 tw-font-bold">Chỉnh sửa Phần</h3>
                                <p class="text-muted tw-mb-0" style="font-size: 13px;">Cập nhật câu hỏi và điều kiện hiển thị</p>
                            </div>
                        </div>
                        <button type="button" onclick="saveSection()" class="btn btn-primary">
                            <i class="fa fa-save"></i> Lưu Thay đổi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section Title -->
            <div class="panel_s" style="margin-bottom: 20px;">
                <div class="panel-body">
                    <label class="control-label tw-font-bold">Tiêu đề Phần (Section)</label>
                    <input type="text" id="section-title" class="form-control input-lg" 
                           style="font-weight: bold; color: #1565c0; border-bottom: 2px solid #e3f2fd;"
                           value="<?php echo htmlspecialchars($section['title']); ?>">
                </div>
            </div>

            <!-- Direct Items -->
            <?php if (isset($section['items'])): ?>
            <div class="panel_s" style="margin-bottom: 20px;">
                <div class="panel-body">
                    <h4 class="tw-font-bold tw-mb-3" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-clipboard-check" style="color: #1976d2;"></i>
                        Câu hỏi chung
                    </h4>
                    
                    <div id="direct-items-container">
                        <?php foreach ($section['items'] as $idx => $item): ?>
                        <?php echo render_item_editor($item, 'direct', $idx); ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" onclick="addDirectItem()" class="btn btn-default btn-block" style="margin-top: 10px; border: 1px dashed #bbb;">
                        <i class="fa fa-plus"></i> Thêm câu hỏi
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Subsections -->
            <?php if (isset($section['subsections'])): ?>
            <div id="subsections-container">
                <?php foreach ($section['subsections'] as $subIdx => $subsection): ?>
                <?php echo render_subsection_editor($subsection, $subIdx, $section['displayCondition']); ?>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="addSubsection()" class="btn btn-default btn-block" style="padding: 15px; border: 2px dashed #bbb; font-weight: bold;">
                <i class="fa fa-plus"></i> Thêm Nhóm Mới
            </button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
var sectionData = <?php echo json_encode($section); ?>;
var sectionIndex = <?php echo $section_index; ?>;

function addDirectItem() {
    var html = createItemEditor({id: 'new_' + Date.now(), text: '', critical: false}, 'direct', $('#direct-items-container .item-row').length);
    $('#direct-items-container').append(html);
}

function removeItem(btn) {
    if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
        $(btn).closest('.item-row').remove();
    }
}

function addSubsection() {
    var html = createSubsectionEditor({
        id: 'sub_new_' + Date.now(),
        title: 'Nhóm Mới',
        items: [],
        displayCondition: 'ALWAYS',
        forDept: ''
    }, $('#subsections-container > .panel_s').length);
    
    $('#subsections-container').append(html);
}

function removeSubsection(btn) {
    if (confirm('Bạn có chắc muốn xóa nhóm này?')) {
        $(btn).closest('.panel_s').remove();
    }
}

function addSubsectionItem(btn) {
    var $container = $(btn).closest('.panel_s').find('.subsection-items-container');
    var html = createItemEditor({id: 'new_' + Date.now(), text: '', critical: false}, 'subsection', $container.find('.item-row').length);
    $container.append(html);
}

function createItemEditor(item, type, index) {
    var criticalChecked = item.critical ? 'checked' : '';
    var criticalClass = item.critical ? 'text-danger' : 'text-muted';
    
    return `
        <div class="item-row" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <button type="button" class="btn btn-sm ${item.critical ? 'btn-danger' : 'btn-default'}" 
                    onclick="toggleCritical(this)" title="Critical" style="width: 40px;">
                <i class="fa fa-exclamation-triangle ${criticalClass}"></i>
                <input type="checkbox" ${criticalChecked} style="display: none;">
            </button>
            <input type="text" class="form-control" placeholder="Nội dung kiểm tra..." value="${item.text || ''}">
            <button type="button" class="btn btn-sm btn-default" onclick="removeItem(this)" style="width: 40px;">
                <i class="fa fa-times text-muted"></i>
            </button>
        </div>
    `;
}

function createSubsectionEditor(subsection, index) {
    var itemsHtml = '';
    if (subsection.items) {
        subsection.items.forEach(function(item, idx) {
            itemsHtml += createItemEditor(item, 'subsection', idx);
        });
    }

    var deptSelector = '';
    if (sectionData.displayCondition === 'DYNAMIC') {
        deptSelector = `
            <div class="col-md-6">
                <label class="control-label" style="font-size: 11px; font-weight: bold; color: #666;">Áp dụng cho Phòng:</label>
                <select class="form-control subsection-dept">
                    <option value="">-- Chọn Phòng --</option>
                    <option value="SEC" ${subsection.forDept == 'SEC' ? 'selected' : ''}>1. BẢO VỆ</option>
                    <option value="IT" ${subsection.forDept == 'IT' ? 'selected' : ''}>2. IT</option>
                    <option value="MAIN" ${subsection.forDept == 'MAIN' ? 'selected' : ''}>3. BẢO TRÌ</option>
                    <option value="INFRA" ${subsection.forDept == 'INFRA' ? 'selected' : ''}>4. HẠ TẦNG</option>
                    <option value="LOG" ${subsection.forDept == 'LOG' ? 'selected' : ''}>5. KHO</option>
                    <option value="PUR" ${subsection.forDept == 'PUR' ? 'selected' : ''}>6. MUA SẮM</option>
                    <option value="ACC" ${subsection.forDept == 'ACC' ? 'selected' : ''}>8. KẾ TOÁN</option>
                </select>
            </div>
        `;
    }

    return `
        <div class="panel_s" style="margin-bottom: 15px; position: relative;">
            <div class="panel-body">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSubsection(this)" 
                        style="position: absolute; top: 10px; right: 10px; opacity: 0.7;" 
                        onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                    <i class="fa fa-trash"></i>
                </button>

                <div class="row" style="margin-bottom: 15px; padding: 15px; background: #fafafa; border-radius: 6px;">
                    <div class="col-md-6">
                        <label class="control-label" style="font-size: 11px; font-weight: bold; color: #666;">Tên Nhóm / Phòng Ban</label>
                        <input type="text" class="form-control subsection-title" value="${subsection.title || ''}" style="font-weight: bold;">
                    </div>
                    ${deptSelector}
                </div>

                <div class="subsection-items-container" style="padding-left: 15px; border-left: 2px solid #e0e0e0;">
                    ${itemsHtml}
                </div>

                <button type="button" onclick="addSubsectionItem(this)" class="btn btn-default btn-sm" style="margin-top: 10px; border: 1px dashed #bbb;">
                    <i class="fa fa-plus"></i> Thêm tiêu chí
                </button>
            </div>
        </div>
    `;
}

function toggleCritical(btn) {
    var $btn = $(btn);
    var $checkbox = $btn.find('input[type="checkbox"]');
    var $icon = $btn.find('i');
    
    $checkbox.prop('checked', !$checkbox.prop('checked'));
    
    if ($checkbox.prop('checked')) {
        $btn.removeClass('btn-default').addClass('btn-danger');
        $icon.removeClass('text-muted').addClass('text-white');
    } else {
        $btn.removeClass('btn-danger').addClass('btn-default');
        $icon.removeClass('text-white').addClass('text-muted');
    }
}

function saveSection() {
    // Build section data from form
    var newSectionData = {
        id: sectionData.id,
        title: $('#section-title').val(),
        displayCondition: sectionData.displayCondition
    };

    // Collect direct items
    if (sectionData.items !== undefined) {
        newSectionData.items = [];
        $('#direct-items-container .item-row').each(function() {
            var text = $(this).find('input[type="text"]').val();
            if (text.trim()) {
                newSectionData.items.push({
                    id: 'c' + (newSectionData.items.length + 1),
                    text: text,
                    critical: $(this).find('input[type="checkbox"]').prop('checked')
                });
            }
        });
    }

    // Collect subsections
    if (sectionData.subsections !== undefined) {
        newSectionData.subsections = [];
        $('#subsections-container > .panel_s').each(function(subIdx) {
            var $subsection = $(this);
            var subsectionData = {
                id: sectionData.subsections[subIdx]?.id || ('sub_' + (subIdx + 1)),
                title: $subsection.find('.subsection-title').val(),
                displayCondition: sectionData.displayCondition === 'DYNAMIC' ? 'DYNAMIC' : 'ALWAYS',
                items: []
            };

            // Get forDept if exists
            var forDept = $subsection.find('.subsection-dept').val();
            if (forDept) {
                subsectionData.forDept = forDept;
            }

            // Get items
            $subsection.find('.subsection-items-container .item-row').each(function() {
                var text = $(this).find('input[type="text"]').val();
                if (text.trim()) {
                    subsectionData.items.push({
                        id: subsectionData.id + '_' + (subsectionData.items.length + 1),
                        text: text,
                        critical: $(this).find('input[type="checkbox"]').prop('checked')
                    });
                }
            });

            if (subsectionData.items.length > 0 || subsectionData.title.trim()) {
                newSectionData.subsections.push(subsectionData);
            }
        });
    }

    // Send to server
    $.ajax({
        url: '<?php echo admin_url("audit_management/saveSection"); ?>',
        type: 'POST',
        data: {
            section_index: sectionIndex,
            section_data: JSON.stringify(newSectionData)
        },
        dataType: 'json',
        success: function(response) {
            if (response.result == 1) {
                alert_float('success', response.message);
                setTimeout(function() {
                    window.location.href = '<?php echo admin_url("audit_management/config"); ?>';
                }, 1000);
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', 'Có lỗi xảy ra!');
        }
    });
}
</script>

<style>
.item-row {
    transition: background-color 0.2s;
}

.item-row:hover {
    background-color: #f9f9f9;
    border-radius: 4px;
}
</style>

<?php
function render_item_editor($item, $type, $index) {
    $criticalChecked = isset($item['critical']) && $item['critical'] ? 'checked' : '';
    $criticalClass = isset($item['critical']) && $item['critical'] ? 'btn-danger' : 'btn-default';
    $iconClass = isset($item['critical']) && $item['critical'] ? 'text-white' : 'text-muted';
    $text = isset($item['text']) ? htmlspecialchars($item['text']) : '';
    
    $html = '<div class="item-row" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">';
    $html .= '<button type="button" class="btn btn-sm ' . $criticalClass . '" onclick="toggleCritical(this)" title="Critical" style="width: 40px;">';
    $html .= '<i class="fa fa-exclamation-triangle ' . $iconClass . '"></i>';
    $html .= '<input type="checkbox" ' . $criticalChecked . ' style="display: none;">';
    $html .= '</button>';
    $html .= '<input type="text" class="form-control" placeholder="Nội dung kiểm tra..." value="' . $text . '">';
    $html .= '<button type="button" class="btn btn-sm btn-default" onclick="removeItem(this)" style="width: 40px;">';
    $html .= '<i class="fa fa-times text-muted"></i>';
    $html .= '</button>';
    $html .= '</div>';
    
    return $html;
}

function render_subsection_editor($subsection, $index, $parentDisplayCondition) {
    $itemsHtml = '';
    if (isset($subsection['items'])) {
        foreach ($subsection['items'] as $idx => $item) {
            $itemsHtml .= render_item_editor($item, 'subsection', $idx);
        }
    }

    $deptSelector = '';
    if ($parentDisplayCondition === 'DYNAMIC') {
        $departments = [
            'SEC' => '1. BẢO VỆ',
            'IT' => '2. IT',
            'MAIN' => '3. BẢO TRÌ',
            'INFRA' => '4. HẠ TẦNG',
            'LOG' => '5. KHO',
            'PUR' => '6. MUA SẮM',
            'ACC' => '8. KẾ TOÁN'
        ];
        
        $deptSelector = '<div class="col-md-6">';
        $deptSelector .= '<label class="control-label" style="font-size: 11px; font-weight: bold; color: #666;">Áp dụng cho Phòng:</label>';
        $deptSelector .= '<select class="form-control subsection-dept">';
        $deptSelector .= '<option value="">-- Chọn Phòng --</option>';
        
        foreach ($departments as $id => $name) {
            $selected = (isset($subsection['forDept']) && $subsection['forDept'] == $id) ? 'selected' : '';
            $deptSelector .= '<option value="' . $id . '" ' . $selected . '>' . $name . '</option>';
        }
        
        $deptSelector .= '</select>';
        $deptSelector .= '</div>';
    }

    $html = '<div class="panel_s" style="margin-bottom: 15px; position: relative;">';
    $html .= '<div class="panel-body">';
    $html .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeSubsection(this)" style="position: absolute; top: 10px; right: 10px; opacity: 0.7;" onmouseover="this.style.opacity=\'1\'" onmouseout="this.style.opacity=\'0.7\'">';
    $html .= '<i class="fa fa-trash"></i>';
    $html .= '</button>';
    
    $html .= '<div class="row" style="margin-bottom: 15px; padding: 15px; background: #fafafa; border-radius: 6px;">';
    $html .= '<div class="col-md-6">';
    $html .= '<label class="control-label" style="font-size: 11px; font-weight: bold; color: #666;">Tên Nhóm / Phòng Ban</label>';
    $html .= '<input type="text" class="form-control subsection-title" value="' . htmlspecialchars($subsection['title']) . '" style="font-weight: bold;">';
    $html .= '</div>';
    $html .= $deptSelector;
    $html .= '</div>';
    
    $html .= '<div class="subsection-items-container" style="padding-left: 15px; border-left: 2px solid #e0e0e0;">';
    $html .= $itemsHtml;
    $html .= '</div>';
    
    $html .= '<button type="button" onclick="addSubsectionItem(this)" class="btn btn-default btn-sm" style="margin-top: 10px; border: 1px dashed #bbb;">';
    $html .= '<i class="fa fa-plus"></i> Thêm tiêu chí';
    $html .= '</button>';
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}
?>
