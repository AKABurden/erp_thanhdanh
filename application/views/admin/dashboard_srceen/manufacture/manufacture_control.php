<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Manufacture Control Panel</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #eee;
        }

        button {
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>⚙️ Manufacture Control Panel</h2>
    <button onclick="addOrder()">+ Thêm Order mới</button>

    <table>
        <thead>
            <tr>
                <th>Lệnh SX</th>
                <th>Mã SP</th>
                <th>Công đoạn</th>
                <th>Kế hoạch</th>
                <th>Hoàn thành</th>
                <th>Còn lại</th>
                <th>%</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="control-body">
            <?php foreach ($rows as $r): ?>
                <tr data-id="<?= $r['order_code'] ?>">
                    <td><?= $r['order_code'] ?></td>
                    <td><?= $r['sku'] ?></td>
                    <td><?= $r['stage'] ?></td>
                    <td><?= number_format($r['qty_plan']) ?></td>
                    <td><?= number_format($r['qty_done']) ?></td>
                    <td><?= number_format($r['qty_todo']) ?></td>
                    <td><?= $r['percent'] ?>%</td>
                    <td>
                        <button onclick="increase('<?= $r['order_code'] ?>')">+ Tiến độ</button>
                        <button onclick="del('<?= $r['order_code'] ?>')">❌ Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Tạo HTML cho 1 dòng control
        function rowTpl(r) {
            return `
      <tr class="row-item" data-id="${r.order_code}">
        <td>${r.order_code}</td>
        <td>${r.sku}</td>
        <td>${r.stage}</td>
        <td class="num">${Number(r.qty_plan).toLocaleString()}</td>
        <td class="num">${Number(r.qty_done).toLocaleString()}</td>
        <td class="num">${Number(r.qty_todo).toLocaleString()}</td>
        <td class="pct">${Number(r.percent)}%</td>
        <td>
          <button onclick="increase('${r.order_code}')">+ Tiến độ</button>
          <button onclick="del('${r.order_code}')">❌ Xóa</button>
        </td>
      </tr>
    `;
        }

        // Append một dòng mới nếu chưa tồn tại
        function appendRow(r) {
            const $tbody = $("#control-body");
            if ($tbody.find(`tr[data-id="${r.order_code}"]`).length) return;
            $tbody.append(rowTpl(r));

            // highlight nhẹ
            const $row = $tbody.find(`tr[data-id="${r.order_code}"]`);
            $row.css({
                background: '#fff3cd'
            });
            setTimeout(() => $row.css({
                background: ''
            }), 600);
        }

        // Cập nhật dữ liệu một dòng đang có trên DOM
        function updateRowDom(r) {
            const $row = $(`#control-body tr[data-id="${r.order_code}"]`);
            if (!$row.length) {
                // nếu chưa có thì thêm mới
                appendRow(r);
                return;
            }
            const $tds = $row.find('td');
            // thứ tự cột: 0 code,1 sku,2 stage,3 plan,4 done,5 todo,6 percent,7 actions
            $tds.eq(1).text(r.sku);
            $tds.eq(2).text(r.stage);
            $tds.eq(3).text(Number(r.qty_plan).toLocaleString());
            $tds.eq(4).text(Number(r.qty_done).toLocaleString());
            $tds.eq(5).text(Number(r.qty_todo).toLocaleString());
            $tds.eq(6).text(Number(r.percent) + '%');

            // highlight
            $row.css({
                background: '#d1e7dd'
            });
            setTimeout(() => $row.css({
                background: ''
            }), 600);
        }

        // Xóa một dòng theo order_code
        function removeRow(order_code) {
            const $row = $(`#control-body tr[data-id="${order_code}"]`);
            if ($row.length) {
                $row.fadeOut(150, function() {
                    $(this).remove();
                });
            }
        }

        // ==== Actions ====

        function addOrder() {
            $.getJSON("<?= site_url('admin/dashboard_srceen/addOrder') ?>", function(res) {
                if (res && res.success && res.newRow) {
                    appendRow(res.newRow);
                } else {
                    alert('Thêm thất bại');
                }
            }).fail(() => alert('Lỗi kết nối addOrder'));
        }

        function increase(id) {
            $.getJSON("<?= site_url('admin/dashboard_srceen/increase') ?>/" + encodeURIComponent(id), function(res) {
                if (res && res.success) {
                    if (res.updatedRow) updateRowDom(res.updatedRow);
                    if (res.removed === true && res.updatedRow) {
                        // Backend đã mark done -> ẩn khỏi control
                        removeRow(res.updatedRow.order_code);
                    }
                } else {
                    alert(res && res.message ? res.message : 'Cập nhật thất bại');
                }
            }).fail(() => alert('Lỗi kết nối increase'));
        }

        function del(id) {
            $.getJSON("<?= site_url('admin/dashboard_srceen/delete') ?>/" + encodeURIComponent(id), function(res) {
                if (res && res.success) {
                    // Backend trả deleted_id
                    removeRow(res.deleted_id || id);
                } else {
                    alert('Xóa thất bại');
                }
            }).fail(() => alert('Lỗi kết nối delete'));
        }
    </script>

</body>

</html>