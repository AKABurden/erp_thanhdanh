<?php $this->load->view('comment/include/header')?>
<div class="side-bar">
    <div>
        <a href="#" data-toggle="tooltip" data-placement="right" title="Tất cả">
            <i class="fa fa-list"></i>
        </a>
    </div>
    <div>
        <a href="#" data-toggle="tooltip" data-placement="right" title="Bình luận chưa phản hồi">
            <i class="fa fa-commenting-o"></i>
        </a>
    </div>
</div>
<div class="clearfix"></div>
<div class="app-content">
    <?php $this->load->view('comment/list_status')?>
    <?php $this->load->view('comment/comment_status')?>
    <?php $this->load->view('comment/pos')?>
</div>
<?php $this->load->view('comment/include/footer')?>

<?php $this->load->view('comment/comment_js')?>
