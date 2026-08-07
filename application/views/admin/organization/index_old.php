<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    #wrapper {
        min-height: calc(100vh - 65px);
    }
    /* 1. Reset & Wrapper */
    .organization-wrapper {
        padding: 40px 10px;
        background: #ffffff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
    }

    .organization ul.ul_organization {
        padding-top: 20px;
        position: relative;
        display: flex;
        justify-content: flex-start;
        min-width: max-content;
        list-style-type: none;
        margin: 0;
        padding-left: 0;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
    }

    /* FIX LỖI TRIỆT ĐỂ: Ẩn dấu gạch dọc dư thừa ở trên đầu nút GỐC CẤP 0 */
    .organization > ul.ul_organization > li::before,
    .organization > ul.ul_organization > li::after {
        display: none !important;
    }

    .organization > ul.ul_organization::before {
        display: none !important;
    }

    .organization > ul.ul_organization > li {
        padding-top: 0 !important;
    }

    .organization ul.ul_organization li {
        text-align: center;
        position: relative;
        padding: 20px 5px 0 5px;
        transition: all 0.3s;
    }

    /* 4. Vẽ đường kẻ kết nối */
    .organization ul.ul_organization li::before,
    .organization ul.ul_organization li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #ccc;
        width: 50%;
        height: 20px;
    }
    .organization ul.ul_organization li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #ccc;
    }

    /* Loại bỏ đường kẻ cho nút đơn lẻ hoặc nút đầu/cuối */
    .organization ul.ul_organization li:only-child::after,
    .organization ul.ul_organization li:only-child::before {
        display: none;
    }
    .organization ul.ul_organization li:only-child {
        padding-top: 0;
    }
    .organization ul.ul_organization li:first-child::before,
    .organization ul.ul_organization li:last-child::after {
        border: 0 none;
    }
    .organization ul.ul_organization li:last-child::before {
        border-right: 2px solid #ccc;
        border-radius: 0 5px 0 0;
    }
    .organization ul.ul_organization li:first-child::after {
        border-radius: 5px 0 0 0;
    }

    /* Đường kẻ dọc nối từ cha xuống tập hợp con */
    .organization ul.ul_organization::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #ccc;
        width: 0;
        height: 20px;
    }

    /* 5. Thiết kế Nút (Node Item) */
    .node-item {
        border: 1px solid #333;
        background: #fff;
        padding: 12px;
        display: inline-block;
        border-radius: 10px;
        position: relative;
        z-index: 10;
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        /*min-height: 120px;*/
        width: 150px;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .lvl-0 { border-top: 6px solid #ff4757; }
    .lvl-1 { border-top: 6px solid #2ed573; }
    .lvl-2 { border-top: 6px solid #1e90ff; }

    .node-label {
        font-weight: bold;
        display: block;
        color: #2f3542;
        font-size: 13px;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn-action {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        padding: 0;
    }

    .btn-add { color: #2ed573; border-color: #2ed573; }
    .btn-add:hover { background: #2ed573; color: white; }
    .btn-del { color: #ff4757; border-color: #ff4757; }
    .btn-del:hover { background: #ff4757; color: white; }

    .title-organization {
        padding: 5px;
        border-radius: 5px;
        text-align: center;
        font-size: 25px;
        text-transform: uppercase;
        font-weight: 500;
    }
    .btn-edit {
        color: #1e90ff;
        border-color: #1e90ff;
    }

    .btn-edit:hover {
        background: #1e90ff;
        color: white;
    }
    .node-item .btn-group {
        opacity: 0;
        visibility: hidden;
        transform: translateY(5px);
        transition: all 0.25s ease;
    }

    /* Khi hover vào node thì hiện nút */
    .node-item:hover .btn-group {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
</style>

<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="title-organization"><?= $title ?></div>
                <div class="organization-wrapper">
                    <div class="organization" id="treeContent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script>
    let orgData = [];

    function loadData(){
        $.ajax({
            type: 'POST',
            url: admin_url+'organization/loadData',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
            },
            dataType: "JSON",
            success: function (response) {
                orgData = [...response.data];
                $("div#treeContent").html("<ul class='ul_organization'>" + renderOrg(orgData[0]) + "</ul>");
            }
        });
    }
    $(document).ready(function(){
        loadData();
    });

    function renderOrg(node) {

        if (node.length === 0) return '';
        let html = `<li>
            <div class="node-item level-${node.level}" style="border-top: 6px solid ${node.color};">
                <div>
                    <div>${node.object_type}</div>
                    <span class="node-label">${node.name} <a class="tnh-modal hide" href="<?= base_url('admin/organization/detail/') ?>${node.id}"><span style="cursor: pointer"><i class="fa fa-edit"></i></span></a></span>
                    <div style="font-style:italic;font-size:11px;margin-bottom:10px">${node.object_name}</div>
                </div>
                <div class="btn-group">
                    <a type="button" class="btn-action btn-add tnh-modal" href="<?= base_url('admin/organization/detail/0/') ?>${node.id}" title="Thêm">+</a>
                    <a type="button" class="btn-action btn-edit tnh-modal" href="<?= base_url('admin/organization/detail/') ?>${node.id}" title="Sửa"><i class="fa fa-pencil"></i></a>
                    ${node.level != 0 ? `<button type="button" onclick="removeItem(${node.id})" class="btn-action btn-del" title="Xóa">×</button>` : ''}
                </div>
            </div>`;

        if (node.children && node.children.length > 0) {
            html += "<ul class='ul_organization'>";
            node.children.forEach(child => {
                html += renderOrg(child);
            });
            html += "</ul>";
        }
        html += "</li>";
        return html;
    }

    function removeItem(id) {
        bootbox.confirm("Bạn có muốn xóa không", function (result) {
            if (result) {
                $.ajax({
                    url: site_url + 'admin/organization/removeItem',
                    type: 'POST',
                    data: {
                        organization_id: id,
                        [csrf_token_name]: hash
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            loadData()
                        } else {
                            alert_float('danger', data.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert_float('danger', jqXHR.responseText);
                    }
                });
            }
        });
    }

    const container = document.querySelector('.organization-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    // Khi nhấn chuột vào phần tử, bắt đầu kéo
    container.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - container.offsetLeft; // Lấy vị trí bắt đầu
        scrollLeft = container.scrollLeft; // Lấy vị trí cuộn hiện tại
        container.style.cursor = 'grabbing'; // Thay đổi con trỏ thành grabbing khi kéo
    });

    // Khi chuột ra khỏi phần tử, ngừng kéo
    container.addEventListener('mouseleave', () => {
        isDown = false;
        container.style.cursor = 'grab'; // Đổi lại con trỏ khi không kéo
    });

    // Khi người dùng nhả chuột, ngừng kéo
    container.addEventListener('mouseup', () => {
        isDown = false;
        container.style.cursor = 'grab'; // Đổi lại con trỏ khi không kéo
    });

    // Khi di chuyển chuột, cuộn nội dung nếu đang kéo
    container.addEventListener('mousemove', (e) => {
        if (!isDown) return; // Nếu không đang kéo thì không làm gì
        e.preventDefault();
        const x = e.pageX - container.offsetLeft; // Lấy vị trí chuột
        const walk = (x - startX) * 2; // Tính độ nhạy của cuộn
        container.scrollLeft = scrollLeft - walk; // Thay đổi vị trí cuộn của container
    });
</script>