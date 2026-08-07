<style>
    .fimo-chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        display: flex;
        align-items: flex-end;
        gap: 8px;
        font-family: 'Arial', sans-serif;
    }

    .chat-bubble {
        position: relative;
        margin-bottom: 50px;
        background-color: #a9cdff;
        padding: 10px;
        border-radius: 16px;
        border: 1px solid #a9cdff;
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .chat-bubble::after {
        content: "";
        position: absolute;
        bottom: 8px;
        right: -10px;
        /* đẩy ra ngoài khung */
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-left: 10px solid #a9cdff;
        /* cùng màu với bubble */
        border-bottom: 8px solid transparent;
    }

    .fimo-avatar {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .fimo-avatar img {
        width: 70px;
        height: 70px;
        object-fit: cover;
    }

    .fimo-avatar button {
        margin-top: 0px;
        background-color: #4793ff;
        color: white;
        border: none;
        border-radius: 16px;
        padding: 2px 12px;
        font-size: 13px;
        cursor: pointer;
    }

    .typing-cursor {
        display: inline-block;
        width: 4px;
        height: 14px;
        background-color: #555;
        margin-left: 2px;
        animation: blink 1s step-start infinite;
        vertical-align: middle;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
    }



    .typing-dots {
        display: inline-flex;
        gap: 2px;
        margin-left: 4px;
    }

    .dot {
        opacity: 0.2;
        animation: wave 1.2s infinite ease-in-out;
        font-weight: bold;
        font-size: 18px;
        line-height: 1;
    }

    .dot1 {
        animation-delay: 0s;
    }

    .dot2 {
        animation-delay: 0.2s;
    }

    .dot3 {
        animation-delay: 0.4s;
    }

    @keyframes wave {

        0%,
        80%,
        100% {
            opacity: 0.2;
            transform: translateY(0);
        }

        40% {
            opacity: 1;
            transform: translateY(-3px);
        }
    }
</style>
<div class="fimo-chat-widget">
    <div class="chat-bubble" id="fimoText">
        <span id="typing"></span>
        <span class="typing-dots" id="dots">
            <span class="dot dot1">.</span>
            <span class="dot dot2">.</span>
            <span class="dot dot3">.</span>
        </span>
    </div>
    <?php if (is_admin()) { ?>
        <a class="fimo-avatar" href="<?= admin_url('chatbot') ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= base_url('uploads/dashboard/ai_thanhdanh.png') ?>" alt="Trợ lý AI" />
            <button>Trợ lý AI</button>
        </a>
    <?php
    } else { ?>
        <div class="fimo-avatar">
            <img src="<?= base_url('uploads/dashboard/ai_thanhdanh.png') ?>" alt="Trợ lý AI" />
            <button disabled>Trợ lý AI</button>
        </div>
    <?php } ?>
</div>
<script>
    const text = "Xin chào, AI có thể hỗ trợ gì cho bạn.";
    const typingSpan = document.getElementById("typing");
    const dots = document.querySelector(".typing-dots");
    const chatBubble = document.getElementById("fimoText");
    let index = 0;

    function resetTyping() {
        index = 0;
        typingSpan.textContent = "";
        dots.style.display = "inline-flex";
        chatBubble.style.display = "flex";

        // Bắt đầu bằng . . . trước trong 1.2s rồi mới chạy chữ
        setTimeout(() => {
            typeWriter();
        }, 1200);
    }

    function typeWriter() {
        if (index < text.length) {
            typingSpan.textContent += text.charAt(index);
            index++;
            requestAnimationFrame(() => setTimeout(typeWriter, 70));
        } else {
            dots.style.display = 'none';
            // Sau khi gõ xong 2.5s thì ẩn bubble, rồi 1s sau hiện lại
            setTimeout(() => {
                chatBubble.style.display = "none";
                setTimeout(resetTyping, 1000);
            }, 2500);
        }
    }

    window.addEventListener("DOMContentLoaded", resetTyping);
</script>