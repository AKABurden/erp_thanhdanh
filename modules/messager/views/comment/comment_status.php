<?php
    $VersionAppFB   =   get_option('VersionAppFB');
    $IdAppFB        =   get_option('IdAppFB');
    $basePath       =   module_dir_url('messager', 'uploads/');
    $baseAssets     =   module_dir_url('messager', 'assets/');
?>



<div class="chat-area">
    <?php $this->load->view('messagers/manage/include/loader')?>
    <div class="tab-content chat-area-body content-status" id="chat_content_body">
        <!-- nội dung khi mới vào -->
        <div class="comment-empty">
        	<div class="features-comment-empty">
        		<p>Quản lý tất cả tin nhắn, bình luận của các trang một cách dễ dàng và hiệu quả nhất.</p>
        	</div>
        	<img src="<?=$basePath.'empty_foso.png';?>">
        	<p class="action-comment">» Chọn một cuộc hội thoại và trải nghiệm ngay thôi!</p>
        </div>

<!--        <div class="fb-post" data-href="https://www.facebook.com/20531316728/posts/10154009990506729/">-->
<!---->
<!--        </div>-->
<!--        <div class="fb-comment-embed"-->
<!--             data-href="https://www.facebook.com/zuck/posts/10102735452532991?comment_id=1070233703036185"-->
<!--             data-width="500"></div>-->

			<!-- nội dung status -->
    </div>
    <div class="chat-area-reply">
        <div class="reply-box-container">
            <img src="<?=$basePath.'itachi.jpg';?>">
            <textarea class="replyTextarea" id="replyMessager" placeholder="Viết trả lời..." type="text" wrap="hard"></textarea>
            <div class="action-profile send-action">
                Gửi
            </div>
        </div>
    </div>
</div>