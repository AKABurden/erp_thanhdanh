</div>

    <?php $this->load->view('messagers/manage/script_js')?>

    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <?php $this->load->view('messagers/manage/pos_js'); ?>
    <?php $this->load->view('messagers/manage/include/main_js_fb'); ?>
<div id="modalOne"></div>
</body>
</html>
