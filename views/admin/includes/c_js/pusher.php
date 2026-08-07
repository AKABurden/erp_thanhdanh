<script>
    <?php $pusher_options = hooks()->apply_filters('pusher_options', array());
    if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
        $pusher_options['cluster'] = get_option('pusher_cluster');
    }
    if(!empty(get_option('pusher_realtime_notifications'))) { ?>
    var c_pusher_options = <?php echo json_encode($pusher_options); ?>;
    var c_pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", c_pusher_options);
    var channel_feed_back_purchase_order = c_pusher.subscribe('event_purchase_order');
    channel_feed_back_purchase_order.bind('feed_back', function(data) {
        fetch_notifications();
        if(data.html) {
            if($('div[data-feed-back-purchase-order="' + data.id + '"]').length == 0) {
                $('.data-feed-back-purchase-order-' + data.id_purchase_order).prepend(data.html);
            }
        }
    });

    channel_feed_back_purchase_order.bind('remove_feed_back', function(data) {
        fetch_notifications();
        if(data.id) {
            $('div[data-feed-back-purchase-order="' + data.id + '"]').remove();
        }
    });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    var channel_feed_back_purchases = c_pusher.subscribe('event_purchases');
    channel_feed_back_purchases.bind('feed_back', function(data) {
        fetch_notifications();
        if(data.html) {

            if($('div[data-feed-back-purchases="' + data.id + '"]').length == 0) {
                $('.data-feed-back-purchases-' + data.id_purchases).prepend(data.html);
            }
        }
    });

    channel_feed_back_purchases.bind('remove_feed_back', function(data) {
        fetch_notifications();
        if(data.id) {
            $('div[data-feed-back-purchases="' + data.id + '"]').remove();
        }
    });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





    var channel_feed_back_import = c_pusher.subscribe('event_import');
    channel_feed_back_import.bind('feed_back', function(data) {
        fetch_notifications();
        if(data.html) {
            if($('div[data-feed-back-import="' + data.id + '"]').length == 0) {
                $('.data-feed-back-import-' + data.id_import).prepend(data.html);
            }
        }
    });

    channel_feed_back_import.bind('remove_feed_back', function(data) {
        fetch_notifications();
        console.log(data);
        if(data.id) {
            $('div[data-feed-back-import="' + data.id + '"]').remove();
        }
    });



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    var channel_feed_back_orders = c_pusher.subscribe('event_orders');
    channel_feed_back_orders.bind('feed_back', function(data) {
        fetch_notifications();
        if(data.html) {
            if($('div[data-feed-back="' + data.id + '"]').length == 0) {
                $('.feed-back-data-' + data.id_orders).prepend(data.html);
            }
        }
    });

    channel_feed_back_orders.bind('remove_feed_back', function(data) {
        fetch_notifications();
        if(data.id) {
            $('div[data-feed-back="' + data.id + '"]').remove();
        }
    });


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    var channel_feed_back_production_details = c_pusher.subscribe('event_order_production_details');
    channel_feed_back_production_details.bind('feed_back', function(data) {
        console.log(data)
        fetch_notifications();
        if(data.html) {
            if($('div[data-feed-back-order_production_details="' + data.id + '"]').length == 0) {
                $('.data-feed-back-order_production_details-' + data.id_orders).prepend(data.html);
            }
        }
    });

    channel_feed_back_production_details.bind('remove_feed_back', function(data) {
        fetch_notifications();
        if(data.id) {
            $('div[data-feed-back-order_production_details="' + data.id + '"]').remove();
        }
    });
    <?php }?>



</script>