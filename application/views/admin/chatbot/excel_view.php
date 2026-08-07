<b>📊 Dữ liệu được trích xuất từ file Excel:</b>
<button id="btnExpandExcel" style="margin-left: 12px; padding: 6px 12px;">Phóng to</button>
<button id="btnSaveExcel" style="margin-left: 12px; padding: 6px 12px;">💾 Lưu dữ liệu</button>
<h4 class="SaveExcelSuccess hide" style="color:red;">Lưu Đơn Hàng Thành Công</h4>
<div id="excelTableGPT" style="margin-top: 16px;"></div>

<!-- Modal Fullscreen -->
<div id="excelFullscreenModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:white; z-index:9999; padding:20px; box-sizing:border-box;">
    <button onclick="closeFullscreenExcel()" style="float:right; padding:6px 12px;">Đóng</button>
    <div id="excelFullscreenTable" style="height: calc(100vh - 60px); margin-top:20px;"></div>
</div>
<!-- Handsontable CDN -->
<!-- Bản đầy đủ Handsontable có cả helpers -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css">
<script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.min.css" />
<script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.min.js"></script>
<script>
    const rawData = <?= json_encode($_data, JSON_UNESCAPED_UNICODE) ?>;
    const colHeaders = ["Ngày", "Khách hàng", "Người liên hệ", "Địa chỉ giao hàng", "Chi nhánh", "Tiền tệ", "Tỉ giá VND", "Loại đơn", "Loại sản phẩm", "Trạng thái đơn", "Nhân viên", "Thuế", "Phí giao hàng", "Bên vận chuyển", "Bên chịu phí", "Ghi chú", "Mã hàng", "Tên hàng", "Tên SP theo KH", "Đơn vị", "Ngày giao hàng", "Mã đơn hàng", "Lệnh sản xuất", "Số lượng đặt", "SL lỗi", "SL mẫu", "Tổng SL", "Đơn giá", "Thành tiền", "Ngày giao hàng dự kiến", "Chi tiết giao hàng", "Ghi chú SP", "Số SO", "Số PI", "Mã PO/Style", "Mã tem"];
    const columnHeaders = ["date", "customer", "person_contact", "address_delivery", "id_branch", "currencies", "amount_to_vnd", "type_orders", "type_items", "status_orders", "employees", "tax", "cost_delivery", "transporters", "charge_party", "note", "item_code", "item_name", "product_name_customer", "unit", "date_ship", "order_code", "command", "quantity_put", "quantity_loss", "sample_quantity_item", "total_quantity_item", "price", "amount", "date_delivery", "detail_delivery", "note_item", "so", "pi", "po_style", "item_code_tem"];
    const excelMatrix = rawData.map(row => columnHeaders.map(k => row[k] ?? ''));

    let hotMain, hotFullscreen;
    const dateColumns = ['Ngày', 'Ngày giao hàng', 'Ngày giao hàng dự kiến'];
    const customerColumnIndex = columnHeaders.indexOf("Khách hàng");

    const columns = colHeaders.map((header, index) => {
        if (dateColumns.includes(header)) {
            return {
                type: 'date',
                dateFormat: 'DD/MM/YYYY',
                correctFormat: true
            };
        }

        if (header === 'Khách hàng') {
            return {
                type: 'autocomplete',
                source: function(query, callback) {
                    fetch('<?= admin_url() ?>/chatbot/get_customers?q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            // Nếu helper chưa được gắn, tự gắn thủ công
                            if (!Handsontable.helper) {
                                Handsontable.helper = {};
                            }
                            callback(data);
                        })
                        .catch(() => callback([]));
                },
                strict: false,
                filter: true,
                allowInvalid: false,
                allowHtml: false // 👈 Thêm dòng này để tránh lỗi sanitize
            };
        }

        return {
            type: 'text'
        };
    });
    const commonOptions = {
        data: excelMatrix,
        colHeaders: colHeaders,
        rowHeaders: true,
        stretchH: 'all',
        manualColumnResize: true,
        manualRowResize: true,
        licenseKey: 'non-commercial-and-evaluation',
        columns: columns,
    };

    function renderMainTable() {
        const container = document.getElementById('excelTableGPT');
        if (hotMain) hotMain.destroy();
        hotMain = new Handsontable(container, {
            ...commonOptions,
            height: 400,
            afterChange: function(changes, source) {
                if (source === 'syncFromFullscreen') return;
            }
        });
    }

    function renderFullscreenTable() {
        const container = document.getElementById('excelFullscreenTable');
        if (hotFullscreen) hotFullscreen.destroy();
        hotFullscreen = new Handsontable(container, {
            ...commonOptions,
            data: hotMain.getData(), // clone lại từ main
            height: window.innerHeight - 100,
            afterChange: function(changes, source) {
                if (source !== 'loadData' && changes) {
                    changes.forEach(([row, col, oldVal, newVal]) => {
                        hotMain.setDataAtCell(row, col, newVal, 'syncFromFullscreen');
                    });
                }
            }
        });
    }

    // Initial render
    setTimeout(() => renderMainTable(), 100);

    // Phóng to
    document.getElementById('btnExpandExcel').addEventListener('click', () => {
        document.getElementById('excelFullscreenModal').style.display = 'block';
        setTimeout(renderFullscreenTable, 100);
    });

    // Đóng modal
    function closeFullscreenExcel() {
        document.getElementById('excelFullscreenModal').style.display = 'none';
        const newData = hotFullscreen.getData();
        hotMain.loadData(newData); // đồng bộ lại
    }
    document.getElementById('btnSaveExcel').addEventListener('click', () => {
        const data = hotMain.getData();
        const payload = data.map(row => {
            const obj = {};
            columnHeaders.forEach((key, index) => {
                obj[key] = row[index];
            });
            return obj;
        });

        const formData = new FormData();
        formData.append('data', JSON.stringify(payload));
        formData.append(csrf_token_name, hash); // CSRF ở đây
        fetch('<?= admin_url() ?>/chatbot/save_excel_data', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.result === 1) {
                    document.getElementById('btnSaveExcel').disabled = true;
                    $('.SaveExcelSuccess').removeClass('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Tạo đơn hàng thành công!',
                        showConfirmButton: true,
                        confirmButtonText: 'Xem đơn hàng',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showOrderModal(res.order_id);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: res.message || 'Có lỗi xảy ra',
                        html: res.errors || '',
                        customClass: {
                            title: 'swal-title-lg',
                            htmlContainer: 'swal-html-lg',
                            popup: 'swal-popup-lg'
                        }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể lưu dữ liệu. Vui lòng thử lại.'
                });
            });
    });

    function showOrderModal(order_id) {
        const link = document.createElement('a');
        link.href = `<?= admin_url() ?>orders/view_order/${order_id}`;
        link.className = 'tnh-modal';
        link.dataset.tnh = 'modal';
        link.dataset.toggle = 'modal';
        link.dataset.target = '#myModal';
        document.body.appendChild(link);
        link.click();
        link.remove(); // xoá sau khi click
    }
</script>