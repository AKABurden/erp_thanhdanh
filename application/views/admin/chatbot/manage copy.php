<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trợ lý AI GPT + SQL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <?php init_head_noMenu(); ?>
    <?php $this->load->view('admin/chatbot/styles'); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
            height: 100vh;
            padding: 20px;
        }

        .sidebar-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 15px;
            color: #333;
            cursor: pointer;
        }

        .sidebar-item:hover {
            font-weight: bold;
        }

        .main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .chat-header {
            padding: 16px;
            background-color: #00bfa5;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .chat-body {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f9fbfd;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
            background: #fff;
            display: flex;
            align-items: center;
        }

        .chat-footer textarea {
            width: 100%;
            height: 80px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            resize: none;
            flex: 1;
        }

        .chat-footer button {
            margin-left: 10px;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background-color: #00bfa5;
            color: white;
            cursor: pointer;
            flex-shrink: 0;
        }

        .chat-bubble {
            max-width: 80%;
            margin-bottom: 10px;
            padding: 10px 15px;
            background: #e1f0fa;
            border-radius: 15px;
            white-space: pre-line;
        }

        .chat-bubble.ai {
            background: #d1ecf1;
            align-self: flex-start;
        }

        .chat-bubble.user {
            background: #d4edda;
            align-self: flex-end;
        }

        .chat-stream {
            display: flex;
            flex-direction: column;
        }

        .suggestion-box {
            background: #eaf5ef;
            padding: 12px;
            margin: 8px 4px;
            border-radius: 10px;
            max-width: 280px;
            min-width: 200px;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-size: 14px;
        }

        .suggestion-box b {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #222;
        }

        .suggestion-box button {
            width: 100%;
            text-align: left;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 6px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .suggestion-box button:hover {
            background-color: #f2fdf9;
            border-color: #00bfa5;
        }

        .suggestion-box button i {
            color: #00bfa5;
        }

        .suggestion-box select {
            width: 100%;
            padding: 8px 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 13px;
            background-color: #fff;
        }

        .suggestion-box .submit-button {
            background-color: #00bfa5;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
        }

        .suggestion-box .submit-button:hover {
            background-color: #009e8a;
        }

        .chat-suggestion-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 10px 0;
        }

        #historyModal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 75%;
            height: 80%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            z-index: 999;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 16px;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
            font-weight: bold;
            font-size: 16px;
            flex-shrink: 0;
            /* Ngăn header co lại */
            position: sticky;
            /* Cố định header */
            top: 0;
            z-index: 1;
        }

        .modal-body {
            flex: 1;
            /* Chiếm toàn bộ không gian còn lại */
            overflow-y: auto;
            /* Cuộn dọc khi nội dung vượt quá */
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: 78%;

            /* Đảm bảo body cuộn được trên một số trình duyệt */
        }

        .modal-footer {
            padding: 12px 16px;
            border-top: 1px solid #eee;
            background: #f9f9f9;
            text-align: right;
            flex-shrink: 0;
            /* Ngăn footer co lại */
            position: sticky;
            /* Cố định footer */
            bottom: 0;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            z-index: 1;
        }



        #historyModal h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        #historyContent {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        #historyModal .chat-bubble {
            max-width: 100%;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 12px;
            line-height: 1.4;
            white-space: pre-wrap;
            word-break: break-word;
        }

        #historyModal .chat-bubble.user {
            align-self: flex-end;
            background: #d4edda;
        }

        #historyModal .chat-bubble.bot {
            align-self: flex-start;
            background: #d1ecf1;
        }

        .modal-footer button {
            padding: 6px 12px;
            font-size: 13px;
            border: none;
            background-color: #00bfa5;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .modal-footer button:hover {
            background-color: #009e8a;
        }

        .gpt-result-box {
            background: white;
            border-radius: 10px;
            /* max-width: 70%; */
            padding: 16px;
            margin: 10px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .gpt-result-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .gpt-result-box th,
        .gpt-result-box td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .gpt-result-box canvas {
            margin-top: 20px;
            max-width: 100%;
        }

        .gpt-html-output {
            margin: 16px 0 24px 40px;
            padding: 16px 20px;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            max-width: 90%;
            font-size: 14px;
            line-height: 1.6;
        }

        .gpt-html-output table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .gpt-html-output th,
        .gpt-html-output td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .gpt-html-output canvas {
            margin-top: 16px;
            max-width: 100%;
            height: auto;
        }

        .typing-indicator {
            display: inline-block;
            margin-left: 4px;
        }

        .typing-indicator span {
            display: inline-block;
            width: 6px;
            height: 6px;
            margin: 0 2px;
            background-color: #00bfa5;
            border-radius: 50%;
            animation: bounce 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-8px);
            }
        }

        .loading-icon {
            animation: spin 1s linear infinite;
            margin-right: 8px;
            color: #00bfa5;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .suggestion-box span {
            cursor: pointer;
        }

        .suggestion-box u {
            text-decoration: underline;
            color: #007bff;
        }

        .custom-upload {
            display: inline-block;
            background-color: #00bfa5;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            margin-top: 6px;
            transition: background 0.3s;
        }

        .custom-upload:hover {
            background-color: #009e8a;
        }

        .custom-upload i {
            margin-right: 6px;
        }
    </style>
    <!-- NHÚNG CỐ ĐỊNH -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.min.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <a class="sidebar-item" href="<?php echo base_url('admin'); ?>">
                <div class="icon-circle"><i class="fas fa-arrow-left"></i></div>
                <span>THÀNH DANH</span>
            </a>
            <div class="sidebar-item active">
                <div class="icon-circle"><i class="fas fa-robot"></i></div>
                <span>Trợ lý AI</span>
            </div>
        </div>
        <div class="sidebar-body">
            <?php foreach ($sessions as $s): ?>
                <div class="sidebar-item child" id="session-<?= $s->id ?>">
                    <div class="icon-circle"><i class="fas fa-comments"></i></div>
                    <span onclick="viewHistory(<?= $s->id ?>)">
                        Phiên <?= $s->id ?><br>
                        <small><?= date('d/m H:i', strtotime($s->created_at)) ?></small>
                    </span>
                    <i class="fas fa-trash-alt" onclick="deleteSession(<?= $s->id ?>)" style="margin-left:auto; color:#d9534f; cursor:pointer;"></i>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="sidebar-footer">
            <a class="sidebar-item" href="<?= admin_url('chatbot') ?>">
                <div class="icon-circle"><i class="fas fa-plus-circle"></i></div>
                <span>Tạo phiên mới</span>
            </a>
        </div>
    </div>
    <div class="main">
        <div class="chat-header">💬 AI Assistant</div>
        <div class="chat-body">
            <div id="chatStream" class="chat-stream">
            </div>
        </div>
        <div class="chat-footer">
            <textarea id="prompt" placeholder="Nhập câu hỏi..."></textarea>
            <button onclick="sendChat()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <div id="historyModal" style="display:none; ">
        <div class="modal-header">
            <h3>Lịch sử phiên</h3>
        </div>
        <div id="historyContent" class="modal-body">
            <div id="chat-history-container">
                <!-- Nơi sẽ append nội dung từng phiên GPT -->
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeHistory()">Đóng</button>
        </div>
    </div>
    <!-- jQuery (nếu chưa có thì giữ lại) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- moment.js (bắt buộc cho daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>


    <?php init_tail(); ?>
    <!-- daterangepicker.js và CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.min.js"></script>
    <script>
        const sessionId = <?= json_encode($session_id) ?>;
        var token = "<?= $this->security->get_csrf_token_name() ?>";
        var hash = "<?= $this->security->get_csrf_hash() ?>";

        function setPrompt(text) {
            document.getElementById('prompt').value = text;
        }

        function sendChat() {
            alert('Tính năng đang phát triển, Vui lòng chọn các câu trên');
            return false;
            const promptInput = document.getElementById('prompt');
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            const stream = document.getElementById('chatStream');
            const userBubble = document.createElement('div');
            userBubble.className = 'chat-bubble user';
            userBubble.innerText = prompt;
            stream.appendChild(userBubble);
            setTimeout(() => {
                scrollToBottom();
            }, 100); // hoặc 300ms nếu render lớn
            const aiBubble = document.createElement('div');
            aiBubble.className = 'chat-bubble ai';
            aiBubble.innerHTML = `🤖 Đang xử lý <span class="typing-indicator"><span></span><span></span><span></span></span>`;
            stream.appendChild(aiBubble);
            setTimeout(() => {
                scrollToBottom();
            }, 100); // hoặc 300ms nếu render lớn
            promptInput.value = '';
            fetch('<?= admin_url("chatbot/process_question") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId,
                        question_text: prompt,
                        staff_names: '' // không cần chọn, nhưng vẫn truyền
                    })
                })
                .then(res => res.json())
                .then(data => {
                    aiBubble.innerHTML = `<i class="fas fa-spinner loading-icon"></i>📊 Đang phân tích dữ liệu...`;
                    setTimeout(() => {
                        renderGPTReply(data);
                        if (typeof initExcelTableGPT === 'function') {
                            initExcelTableGPT();
                        }
                    }, 1000);
                });
        }

        function splitIntroAndContent(reply) {
            if (typeof reply !== 'string') return {
                intro: '',
                content: ''
            };
            const parts = reply.split('<!--gpt-output-->');
            if (parts.length < 2) {
                return {
                    intro: reply,
                    content: ''
                };
            }
            return {
                intro: parts[0],
                content: parts.slice(1).join('<!--gpt-output-->')
            };
        }

        function decodeHTML(html) {
            const txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }
        // Thêm sự kiện nhấn Enter
        document.getElementById('prompt').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // Ngăn tạo dòng mới
                sendChat();
            }
        });

        function viewHistory(sessionId) {
            fetch('<?= admin_url("chatbot/get_session_messages") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('historyContent');
                    container.innerHTML = '';

                    data.messages.forEach(msg => {
                        if (msg.sender === 'bot' && msg.message.includes('<!--gpt-output-->')) {
                            const [desc, output] = msg.message.split('<!--gpt-output-->');

                            // ✅ Tạo một box hiển thị toàn bộ nội dung GPT output
                            const fullOutput = document.createElement('div');
                            fullOutput.className = 'gpt-html-output';
                            fullOutput.innerHTML = desc + output;
                            container.appendChild(fullOutput);

                            // ✅ Chạy lại các script trong output nếu có
                            fullOutput.querySelectorAll("script").forEach(oldScript => {
                                const newScript = document.createElement("script");
                                if (oldScript.src) {
                                    newScript.src = oldScript.src;
                                } else {
                                    newScript.textContent = oldScript.textContent;
                                }
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });

                        } else {
                            // Các tin nhắn user hoặc bot khác thì vẫn tạo bubble như cũ
                            const bubble = document.createElement('div');
                            bubble.className = 'chat-bubble ' + msg.sender;
                            bubble.innerHTML = msg.message;
                            container.appendChild(bubble);
                        }
                    });

                    document.getElementById('historyModal').style.display = 'block';
                });
        }

        function closeHistory() {
            document.getElementById('historyModal').style.display = 'none';
        }
    </script>
    <script>
        function addBotBubble(html, isSuggestion = false) {
            const div = document.createElement('div');
            div.innerHTML = html;
            div.className = isSuggestion ? 'suggestion-box' : 'chat-bubble ai';
            document.getElementById('chatStream').appendChild(div);
            setTimeout(() => {
                scrollToBottom();
            }, 100); // hoặc 300ms nếu render lớn
        }

        function addUserBubble(text) {
            const div = document.createElement('div');
            div.className = 'chat-bubble user';
            div.innerText = text;
            document.getElementById('chatStream').appendChild(div);
            setTimeout(() => {
                scrollToBottom();
            }, 100); // hoặc 300ms nếu render lớn
        }

        function startFlow() {
            // ✅ Xoá các phần tử suggestion-box cũ và huỷ select2 nếu có
            document.querySelectorAll('.suggestion-box').forEach(el => el.remove());
            try {
                $('.select2').select2('destroy');
            } catch (e) {}

            fetch('<?= admin_url("chatbot/fetch_greeting_flow") ?>')
                .then(res => res.json())
                .then(data => {
                    let html = `<div class=""><b>${data.bot}</b>`;
                    data.modules.forEach(m => {
                        html += `<button onclick="selectModule(${m.id}, '${m.name}')">
                    <span>${m.name}</span><i class="fas fa-arrow-right"></i>
                </button>`;
                    });
                    html += `</div>`;
                    addBotBubble(html, true);

                    // ✅ Nếu có select nào trong suggestion-box thì khởi tạo select2
                    setTimeout(() => {
                        $('.suggestion-box select').select2({
                            placeholder: "Chọn nhân viên",
                            width: 'resolve',
                            dropdownParent: $('.suggestion-box').last()
                        });
                    }, 0);
                });
        }

        function selectModule(id, name) {
            addUserBubble(name);

            // Gửi phân hệ được chọn vào DB như 1 tin nhắn
            fetch('<?= admin_url("chatbot/send_message") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    csrf_token_name: hash,
                    session_id: sessionId,
                    message: '[Chọn phân hệ] ' + name
                })
            });

            // Tiếp tục gọi nhóm câu hỏi
            fetch('<?= admin_url("chatbot/fetch_groups_questions") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        module_id: id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const rowDiv = document.createElement('div');
                    rowDiv.className = 'chat-suggestion-row';

                    data.groups.forEach(g => {
                        let html = `<b>📂 ${g.name}</b>`;
                        g.questions.forEach(q => {

                            // html += `<button onclick="selectQuestion(${q.id}, '${q.content}')">
                            //             <span>${q.content}</span><i class="fas fa-arrow-right"></i>
                            //         </button>`;
                            html += `<button data-id="${q.id}" data-content="${q.content}" onclick="handleSelectQuestion(this)">
                                        ${truncateText(q.content, 200)}
                                    </button>`;
                        });

                        const groupBox = document.createElement('div');
                        groupBox.className = 'suggestion-box';
                        groupBox.innerHTML = html;
                        rowDiv.appendChild(groupBox);
                    });

                    // Gắn hàng suggestion-box nhóm câu hỏi vào chat
                    document.getElementById('chatStream').appendChild(rowDiv);
                });
        }

        function truncateText(text, limit = 60) {
            return text.length > limit ? text.slice(0, limit) + '... ' : text;
        }

        function toggleFullText(el) {
            const parent = el.parentNode;
            const short = parent.querySelector('.short-text');
            const full = parent.querySelector('.full-text');
            if (short && full) {
                short.style.display = short.style.display === 'none' ? '' : 'none';
                full.style.display = full.style.display === 'none' ? '' : 'none';
            }
        }

        function handleSelectQuestion(btn) {
            const id = btn.getAttribute('data-id');
            const content = btn.getAttribute('data-content');
            selectQuestion(id, content);
        }

        function selectQuestion(id, content) {
            addUserBubble(content); // hiển thị câu hỏi như chat
            // Lưu vào session
            fetch('<?= admin_url("chatbot/send_message") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    csrf_token_name: hash,
                    session_id: sessionId,
                    message: '[Hỏi] ' + content
                })
            });
            fetch('<?= admin_url("chatbot/fetch_requirements") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        question_id: id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const hasStaff = data.require_staff;
                    const hasDate = data.require_date;
                    const hasDetail = data.require_detail;
                    const hasFile = data.require_file;
                    const staffList = data.staff || [];

                    let html = `<div class="">`;

                    if (hasStaff) {
                        html += `<b>👤 Vui lòng chọn nhân viên:</b>
        <select class="select2" multiple id="staffSelect" style="width:100%; margin-top:5px;">`;
                        staffList.forEach(s => {
                            html += `<option value="${s.staffid}">${s.firstname} ${s.lastname}</option>`;
                        });
                        html += `</select><br/><br/>`;
                    }

                    if (hasDate) {
                        html += `<b>📅 Chọn khoảng thời gian:</b><br/>
        <input type="text" id="dateRange" style="padding: 6px; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-top:5px;" /><br/><br/>`;
                    }
                    if (hasDetail) {
                        html += `<b>📅 Chọn chi tiết phiếu:</b><br/>
        <input data-placeholder="<?= _l('Chi tiết phiếu') ?>" name="detail_id" id="detail_id" class="detail_id modal-select2" style="width: 100%"><br/><br/>`;
                    }
                    const uniqueSuffix = Date.now(); // hoặc Math.random().toString(36).substr(2, 5)
                    const inputId = `uploadFile_${id}_${uniqueSuffix}`;
                    const displayId = `fileNameDisplay_${id}_${uniqueSuffix}`;
                    const uniqueUploadId = `uploadFile_${id}_${uniqueSuffix}`;
                    if (hasFile) {
                        html += `<b>📎 Tải file Excel:</b><br/>
<label for="${inputId}" class="custom-upload">
  <i class="fas fa-upload"></i> Chọn file Excel
</label>
<input type="file" id="${inputId}" accept=".xls,.xlsx" style="display:none;">
<div id="${displayId}" style="font-size:13px; margin-top:6px; color:#555;"></div><br/>`;
                    }
                    if (hasStaff || hasDate || hasDetail || hasFile) {
                        html += `<button data-content="${content}" data-id="${id}" data-uploadid="${uniqueUploadId}" class="submit-button" onclick="handleSubmitQuestion(this,${hasStaff})">Thực hiện</button></div>`;
                        addBotBubble(html, true);
                    }

                    // Khởi tạo select2 và daterangepicker nếu có
                    setTimeout(() => {
                        if (hasStaff) {
                            $('#staffSelect').select2({
                                placeholder: "Chọn nhân viên",
                                width: 'resolve',
                                dropdownParent: $('.suggestion-box').last()
                            });
                        }
                        if (hasDetail) {
                            ajaxSelectCallBack_detail($('#detail_id'), "<?= admin_url('suggest_questions/SearchDetail') ?>", 0, id);
                        }
                        if (hasDate) {
                            $('#dateRange').daterangepicker({
                                locale: {
                                    format: 'DD/MM/YYYY',
                                    applyLabel: 'Chọn',
                                    cancelLabel: 'Thoát',
                                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                                    monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                                    firstDay: 1
                                },
                                opens: 'right',
                                autoUpdateInput: true
                            });
                        }
                        if (hasFile) {
                            const fileInput = document.getElementById(inputId);
                            const fileNameDisplay = document.getElementById(displayId);
                            if (fileInput && fileNameDisplay) {
                                fileInput.addEventListener("change", function() {
                                    const file = fileInput.files[0];
                                    fileNameDisplay.innerText = file ? `📁 ${file.name}` : "";
                                });
                            }
                        }
                        // Nếu không cần gì thì submit luôn
                        if (!hasStaff && !hasDate && !hasDetail && !hasFile) {
                            submitQuestion(content, false, id);
                        }
                    }, 0);
                });
        }

        function handleSubmitQuestion(btn, hasStaff) {
            const content = btn.dataset.content.replace(/\\n/g, '\n');
            const id = btn.dataset.id;
            const uploadFileId = btn.getAttribute('data-uploadid'); // lấy từ attribute
            submitQuestion(content, hasStaff, id, uploadFileId);
        }

        function submitQuestion(content, withStaff, id = 0, uploadFileId = '') {
            if (id != 1) {
                alert('Tính năng đang phát triển, Vui lòng chọn câu Đơn đặt hàng khách hàng');
                return false;
            }

            let staffNames = '';
            if (withStaff) {
                const selectedOptions = $('#staffSelect').val();
                if (!selectedOptions || selectedOptions.length === 0) {
                    alert('Vui lòng chọn ít nhất 1 nhân viên.');
                    return;
                }
                const select = document.getElementById('staffSelect');
                const selected = [...select.selectedOptions].map(o => o.text);
                staffNames = selected.join(', ');
                addUserBubble('Áp dụng cho: ' + staffNames);

                fetch('<?= admin_url("chatbot/send_message") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId,
                        message: '[Nhân viên] ' + staffNames
                    })
                });
            }

            const dateRange = document.getElementById('dateRange');
            const requireDate = dateRange !== null;
            let startDate = '',
                endDate = '';

            if (requireDate) {
                const dateVal = dateRange.value;
                if (!dateVal || !dateVal.includes(' - ')) {
                    alert('Vui lòng chọn khoảng thời gian.');
                    return;
                }
                const parts = dateVal.split(' - ');
                startDate = moment(parts[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
                endDate = moment(parts[1], 'DD/MM/YYYY').format('YYYY-MM-DD');

                addUserBubble(`Khoảng thời gian: ${parts[0]} → ${parts[1]}`);

                fetch('<?= admin_url("chatbot/send_message") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId,
                        message: '[Thời gian] ' + parts[0] + ' đến ' + parts[1]
                    })
                });
            }

            const detail_id = document.getElementById('detail_id');
            const requireDetail = detail_id !== null;
            let DetailVal = 0;

            if (requireDetail) {
                DetailVal = detail_id.value;
                if (!DetailVal) {
                    alert('Vui lòng chọn Chi tiết phiếu.');
                    return;
                }
                addUserBubble(`Lọc theo: ${DetailVal}`);

                fetch('<?= admin_url("chatbot/send_message") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId,
                        message: '[Lọc theo] ' + DetailVal
                    })
                });
            }

            // 👇 Thêm phần xử lý file
            const fileInput = document.getElementById(uploadFileId);
            const hasFile = fileInput !== null;
            let formData;

            if (hasFile && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                formData = new FormData();
                formData.append('csrf_token_name', hash);
                formData.append('session_id', sessionId);
                formData.append('question_text', content);
                formData.append('id', id);
                formData.append('staff_names', staffNames);
                formData.append('start_date', startDate);
                formData.append('end_date', endDate);
                formData.append('DetailVal', DetailVal);
                formData.append('upload_file', file);
            }

            document.querySelectorAll('.suggestion-box button').forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = 0.5;
                btn.style.cursor = 'not-allowed';
            });

            const stream = document.getElementById('chatStream');
            const aiBubble = document.createElement('div');
            aiBubble.className = 'chat-bubble ai';
            aiBubble.innerHTML = `🤖 Đang xử lý <span class="typing-indicator"><span></span><span></span><span></span></span>`;
            stream.appendChild(aiBubble);

            fetch('<?= admin_url("chatbot/process_question") ?>', {
                    method: 'POST',
                    headers: formData ? undefined : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData || new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: sessionId,
                        question_text: content,
                        id: id,
                        staff_names: staffNames,
                        start_date: startDate,
                        end_date: endDate,
                        DetailVal: DetailVal
                    })
                })
                .then(res => res.json())
                .then(data => {
                    aiBubble.innerHTML = `<i class="fas fa-spinner loading-icon"></i>📊 Đang phân tích dữ liệu...`;
                    setTimeout(() => {
                        renderGPTReply(data);
                        const continueBox = document.createElement('div');
                        continueBox.className = 'suggestion-box';
                        continueBox.innerHTML = `<b>Bạn muốn tiếp tục?</b><button onclick="startFlow()">🔁 Quay lại bước chọn phân hệ</button>`;
                        document.getElementById('chatStream').appendChild(continueBox);
                    }, 1000);
                });
        }
        document.addEventListener("DOMContentLoaded", function() {
            startFlow(); // gợi ý phân hệ khi load
        });

        function deleteSession(id) {
            if (!confirm('Bạn có chắc chắn muốn xoá phiên này?')) return;

            fetch('<?= admin_url("chatbot/delete_session") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        csrf_token_name: hash,
                        session_id: id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('session-' + id)?.remove();
                    }
                });
        }
        document.querySelectorAll('.suggestion-box button').forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = 0.5;
            btn.style.cursor = 'not-allowed';
        });

        function decodeGPTHTML(str) {
            return str
                .replace(/<br\s*\/?>/gi, '')
                .replace(/&nbsp;/g, ' ');
        }

        function runScriptsFromHTML(container) {
            const scripts = container.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                } else {
                    newScript.textContent = oldScript.textContent;
                }
                oldScript.replaceWith(newScript);
            });
        }

        function renderGPTReply(data) {
            const {
                intro,
                content
            } = splitIntroAndContent(data.reply);

            const chatItem = document.createElement("div");
            chatItem.className = "chat-bubble ai";
            chatItem.innerHTML = intro;
            document.querySelector("#chatStream").appendChild(chatItem);

            if (content) {
                const resultBox = document.createElement("div");
                resultBox.className = "gpt-result-box";
                resultBox.innerHTML = decodeGPTHTML(content);
                document.querySelector("#chatStream").appendChild(resultBox);
                runScriptsFromHTML(resultBox);
                setTimeout(() => {
                    scrollToBottom();
                }, 100); // hoặc 300ms nếu render lớn
            }
        }

        function ajaxSelectCallBack_detail(element, url, id, types = '') {
            if (id > 0) {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    initSelection: function(element, callback) {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: url + '/' + id,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0]);
                            }
                        });
                    },
                    ajax: {
                        url: url,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: types,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return {
                                    results: data.results
                                };
                            } else {
                                return {
                                    results: [{
                                        id: '',
                                        text: 'No Match Found'
                                    }]
                                };
                            }
                        }
                    },
                    formatResult: repoFormatSelectionContract,
                    formatSelection: repoFormatSelectionContract,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            } else {
                $(element).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    ajax: {
                        url: url + '/' + $(element).val(),
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: types,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return {
                                    results: data.results
                                };
                            } else {
                                return {
                                    results: [{
                                        code_client: '',
                                        id: '',
                                        text: 'No Match Found'
                                    }]
                                };
                            }
                        }
                    },
                    formatResult: repoFormatSelectionContract,
                    formatSelection: repoFormatSelectionContract,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            }
        }

        function repoFormatSelectionContract(state) {
            return state.text;
        }

        function scrollToBottom() {
            const chatContainer = document.getElementById('chatStream');
            if (chatContainer && chatContainer.lastElementChild) {
                chatContainer.lastElementChild.scrollIntoView({
                    behavior: 'smooth',
                    block: 'end'
                });
            }
        }
    </script>

</body>

</html>