<?php init_head(); ?>
<style>
    .border-button{
        border: 1px solid #bfcbd9;
    }
    .aPressed{
        border: 1px black dotted;
        padding: 10px;
        border-radius: 3%;
        margin-bottom: 10px;
    }
    .aReplyPressed{
        border: 1px black dotted;
        padding: 10px;
        border-radius: 3%;
        margin-bottom: 10px;
        width: 30%;
        margin-top: 10px;
    }
    .aReplyInput{
        width: 68%;
        display: inline;
    }
    a.aPressed{
        color: #03a9f4;
    }
    .oPenLeft{
        border: 0px;
        background: #337ab7;
    }
    .aPressed_active{
        background-color: #d6d6d6;
        color: black;
        border: 1px black dotted;
        padding: 10px;
        border-radius: 3%;
        margin-bottom: 10px;
    }
    .aPressed_active > a {
        color: black;
    }
    .aReplyInput{
        margin-top: 10px;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'orders-form', 'class' => '_transaction_form orders-form')); ?>
            <div>
                <div class="panel_s">
                    <div class="additional"></div>
                    <div class="panel-body">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 content-items_panel">
                            <?php
                                $html_popver = "<div>";
                                $html_popver .= "    <div class='panel panel-primary'>";
                                $html_popver .= "        <div class='panel-heading'>Edit Button</div>";
                                $html_popver .= "        <div class='panel-body'>";
                                $html_popver .= "            <div class='form-group' app-field-wrapper='title'>";
                                $html_popver .= "                <label for='title' class='control-label'>Title</label>";
                                $html_popver .= "                <input type='text' id='title' name='title' class='form-control title_button' value='Button #0'>";
                                $html_popver .= "            </div>";
                                $html_popver .= "            <div>";
                                $html_popver .= "               <h4>When pressed</h4>";
                                $html_popver .= "               <div class='aPressed newReply'><a class='open'>create new reply</a></div>";
                                $html_popver .= "               <div class='clearfix'></div>";
                                $html_popver .= "               <div class='aPressed'><a class='openWebsite'>Open website</a></div>";
                                $html_popver .= "               <div class='clearfix'></div>";
                                $html_popver .= "               <div class='aPressed'><a class='openCall'>Call number</a></div>";
                                $html_popver .= "            </div>";
                                $html_popver .= "        </div>";
                                $html_popver .= "        <div class='panel-footer'>";
                                $html_popver .= "           <button type='button' class='btn btn-default removeReply'>Delete</button>";
                                $html_popver .= "           <button type='button' class='btn btn-info pull-right Complete'>Complete</button>";
                                $html_popver .= "        </div>";
                                $html_popver .= "    </div>";
                                $html_popver .= "</div>";
                            ?>

                            <?php
                                $html_reply = "<div>";
                                $html_reply .= "    <div class='panel panel-primary'>";
                                $html_reply .= "        <div class='panel-heading'>Quick reply</div>";
                                $html_reply .= "        <div class='panel-body'>";
                                $html_reply .= "            <div>";
                                $html_reply .= "               <h4>When pressed</h4>";
                                $html_reply .= "               <div class='aPressed newReply'><a class='open'>create new reply</a></div>";
                                $html_reply .= "               <div class='clearfix'></div>";
                                $html_reply .= "               <div class='aPressed'><a class='openWebsite'>Open website</a></div>";
                                $html_reply .= "               <div class='clearfix'></div>";
                                $html_reply .= "               <div class='aPressed'><a class='openCall'>Call number</a></div>";
                                $html_reply .= "            </div>";
                                $html_reply .= "        </div>";
                                $html_reply .= "        <div class='panel-footer'>";
                                $html_reply .= "           <button type='button' class='btn btn-default removeReply'>Delete</button>";
                                $html_reply .= "           <button type='button' class='btn btn-info pull-right Complete'>Complete</button>";
                                $html_reply .= "        </div>";
                                $html_reply .= "    </div>";
                                $html_reply .= "</div>";
                            ?>
                            <div class="panel items-panel">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <?=_l('lead_general_info')?>
                                    </div>
                                    <div class="panel-body body_content">
                                        <div class="itemEvent item_0" id-data-item="0" id-orders-item="0">
                                            <textarea class="form-control"></textarea>
                                            <div class="clearfix"></div>
                                            <div class="buttonEvent">
                                                <button type="button" id-data="0" id-orders="0" class="btn form-control border-button btnPopover DataEvent" href="#" data-toggle="popover" data-html="true" data-content="<?=$html_popver?>">
                                                    Button #0
                                                </button>
                                            </div>
                                            <a type="button" class="btn form-control aPressed" onclick="createEventButton(this)">
                                                +Add Button
                                            </a>

                                            <div class="DivQickReply">
                                                <span class="SpanReply">
                                                    <input class="form-control aReplyInput btnPopover"  id-data="0" id-orders="0" data-toggle="popover" data-html="true" data-content="<?=$html_reply?>">
                                                </span>
                                                <button type="button" class="btn form-control aReplyPressed pull-right">
                                                    + Quick reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                    <button class="btn btn-info only-save customer-form-submiter">
                        <?php echo _l( 'submit'); ?>
                    </button>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="divPopover hide"></div>


<?php init_tail(); ?>
<script>
    //Tạo button
    function createEventButton(_this)
    {
        $(_this).button({loadingText: '<?=_l('cong_please_wait')?>'});
        $(_this).button('loading');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var DivParent = $(_this).parent('div');
        var id_orders_item = DivParent.attr('id-orders-item');
        var id_data_item = DivParent.attr('id-data-item');
        data['id_orders_item'] = id_orders_item;
        data['id_data_item'] = id_data_item;
        $.post(admin_url+'bot_fanpage/addButtonEvent', data, function(data){
            $(_this).parents('.itemEvent').find('.buttonEvent').append(data);
            $('.divPopover').append(data);
            DivParent.attr('id-orders-item', parseInt(id_orders_item)+1);
        }).always(function() {
            $(_this).button('reset');
        });
    }

    //Đối tiêu đề button
    $('body').on('keyup', '.title_button', function(e){
        var idpopover = $(this).parents('.popover').attr('id');
        var title =  $(this).val();
        $('button[aria-describedby="'+idpopover+'"]').html(title);
    })

    //Đổi popver kho đóng popver
    $('body').on('hide.bs.popover', ".btnPopover[data-toggle='popover']", function(){
        var aria = $(this).attr('aria-describedby');
        var popover_content = $('#'+aria).find('.popover-content');
        popover_content.find('.title_button').attr('value', popover_content.find('.title_button').val());
        popover_content.find('.inputOpenWebsite').attr('value', popover_content.find('.inputOpenWebsite').val());
        popover_content.find('.inputOpenCall').attr('value', popover_content.find('.inputOpenCall').val());
        $(this).attr('data-content', popover_content.html());
    });

    //Thêm trả lời tin nhắn sau khi click
    $('body').on('click', '.newReply', function(e){
        var button = $(this);
        var Atext = button.find('a').html();
        button.addClass('aPressed_active');
        button.removeClass('aPressed');
        button.find('a').before('<a class="text-danger pull-right removeReply">X</a>');
        var div = button.parent('div');
        div.find('.aPressed').addClass('hide');

        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        var idPopover = button.parents('.popover').attr('id');
        if($('button[aria-describedby="'+idPopover+'"]').length)
        {
            var id_orders = $('button[aria-describedby="'+idPopover+'"]').attr('id-orders');
            var id_data = $('button[aria-describedby="'+idPopover+'"]').attr('id-data');
        }
        else if($('input[aria-describedby="'+idPopover+'"]').length)
        {
            var id_orders = $('input[aria-describedby="'+idPopover+'"]').attr('id-orders');
            var id_data = $('input[aria-describedby="'+idPopover+'"]').attr('id-data');
        }
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_orders'] = id_orders;
        data['id_data'] = id_data;
        $.post(admin_url+'bot_fanpage/reply_step', data, function(data){

            $('.items-panel').addClass('hide');
            if($('button[aria-describedby="'+idPopover+'"]').length)
            {
                $('button[aria-describedby="'+idPopover+'"]').parents('.content-items_panel').append(data);
            }
            else if($('input[aria-describedby="'+idPopover+'"]').length)
            {
                $('input[aria-describedby="'+idPopover+'"]').parents('.content-items_panel').append(data);
            }
            button.attr('id-data', parseInt(id_data)+1);
            button.attr('id-orders', parseInt(id_orders));
            // $('.DataEvent[id-data="'+(parseInt(id_data)+1)+'"][id-orders="'+(id_orders)+'"]').parents('.items-panel').removeClass('hide');
            button.removeClass('newReply');
            button.addClass('OpenReply');
        }).always(function() {
            button.button('reset');
        });
    })

    //Mở tab nút trả lời tin nhắn
    $('body').on('click', '.OpenReply a.open', function(e){
        var divOpenReply = $(this).parent('.OpenReply');
        var Popover = divOpenReply.parents('.popover');
        var idPopover = Popover.attr('id');
        // var buttonPopover = $('button[aria-describedby="'+idPopover+'"]');
        var id_data = divOpenReply.attr('id-data');
        var id_orders = divOpenReply.attr('id-orders');
        $('.items-panel').addClass('hide');
        $('.DataEvent[id-data="'+(parseInt(id_data))+'"][id-orders="'+(id_orders)+'"]').parents('.items-panel').removeClass('hide');
    })

    //thêm nhập website khi nhấn
    $('body').on('click', '.aPressed a.openWebsite', function(e){
        var aPressed = $(this).parents('.aPressed');
        aPressed.addClass('aPressed_active');
        aPressed.removeClass('aPressed');
        aPressed.find('a').before('<a class="text-danger pull-right removeReply">X</a>');
        var inputWebsite = $('<input class="form-control inputOpenWebsite">');
        aPressed.append(inputWebsite);
        aPressed.parents('.popover').find('.aPressed').addClass('hide');
    })

    //thêm nhập gọi điện khi nhấn
    $('body').on('click', '.aPressed a.openCall', function(e){
        var aPressed = $(this).parents('.aPressed');
        aPressed.addClass('aPressed_active');
        aPressed.removeClass('aPressed');
        aPressed.find('a').before('<a class="text-danger pull-right removeReply">X</a>');
        var inputWebsite = $('<input class="form-control inputOpenCall">');
        aPressed.append(inputWebsite);
        aPressed.parents('.popover').find('.aPressed').addClass('hide');
    })

    //Lui lại khi tạo cuộc trả lời tin nhắn mới
    $('body').on('click', '.oPenLeft', function(e){
        var button = $(this);
        var id_data = button.attr('id-data-left');
        var id_orders = button.attr('id-orders-left');
        $('.items-panel').addClass('hide');
        if($('.DataEvent[id-data="'+id_data+'"][id-orders="'+id_orders+'"]').length)
        {
            $('.DataEvent[id-data="'+id_data+'"][id-orders="'+id_orders+'"]').parents('.items-panel').removeClass('hide');
        }
        else if($('input[id-data="'+id_data+'"][id-orders="'+id_orders+'"]').length)
        {
            $('input[id-data="'+id_data+'"][id-orders="'+id_orders+'"]').parents('.items-panel').removeClass('hide');
        }
    })

    $('body').on('click', '.removeReply', function(e){
        var Popover = $(this).parents('.popover');
        var idPopover = Popover.attr('id');
        var buttonPopover = $('button[aria-describedby="'+idPopover+'"]');
        var id_data = buttonPopover.attr('id-data');
        var id_orders = buttonPopover.attr('id-orders');
        Popover.find('.aPressed_active').addClass('aPressed').removeClass('aPressed_active');
        Popover.find('.OpenReply').addClass('newReply').removeClass('OpenReply');
        Popover.find('.inputOpenCall').remove();
        Popover.find('.inputOpenWebsite').remove();

        Popover.find('a.removeReply').remove();
        Popover.find('.aPressed').removeClass('hide');
        var i = id_data;
        var y = 1;
        while (y == 1)
        {
            i++;
            if($('.itemEvent[id-orders-item="'+id_orders+'"][id-data-item="'+i+'"]').length > 0)
            {
                $('.itemEvent[id-orders-item="'+id_orders+'"][id-data-item="'+i+'"]').parents('.items-panel').remove();
            }
            else
            {
                y = 0;
            }
        }
    })

    $('body').on('click', '.aReplyPressed', function(e){
        var button = $(this);
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var DivParent = button.parents('div.itemEvent');
        var id_orders_item = DivParent.attr('id-orders-item');
        var id_data_item = DivParent.attr('id-data-item');
        data['id_orders_item'] = id_orders_item;
        data['id_data_item'] = id_data_item;
        $.post(admin_url+'bot_fanpage/addButtonInput', data, function(data){
            button.parents('.DivQickReply').find('.SpanReply').append(data);
            $('.divPopover').append(data);
            DivParent.attr('id-orders-item', parseInt(id_orders_item)+1);
        }).always(function() {
            button.button('reset');
        });
    })

    $('body').on('click', '.Complete', function(e){
        var id = $(this).parents('.popover').attr('id');
        console.log($('[aria-describedby="'+id+'"]'))
        $('[aria-describedby="'+id+'"]').click();
    })
</script>

