<div class="pos">
    <div class="tab">
        <button class="tablinks customer-tab active" id="customer-tab">
            <span><?=_l('cong_profile_client')?></span>
            <span class="span_type_client"></span>
        </button>
        <button class="tablinks order-tab" id="order-tab">
            <span><?=_l('cong_order_and_care_of')?></span>
        </button>
    </div>
    <div class="tab-content-customer">
        <div class="search-profile form-group mtop10">
            <div class="search-profile-text">
                <input id="search_customer" autocomplete="off" list="browsers_list_from" class="form-control" placeholder="<?=_l('cong_input_name_phone')?>">
                <datalist id="browsers_list_from"></datalist>
            </div>
            <div class="search-profile-icon">
                <i id="search" class="fa fa-search"></i>
            </div>
        </div>
        <div id="content_customer"></div>
        <!-- check có sản phẩm hay chưa -->
        <!-- nếu có vào đây -->
        <div class="pos-container hide">
            <div class="summary-inserted" id="summary-inserted">
                <div class="each-data">
                    <div class="icon-each-data">
                        <i class="fa fa-file-o"></i>
                    </div>
                    <div class="value">1</div>
                </div>
                <div class="each-data">
                    <div class="icon-each-data">
                        <i class="fa fa-money"></i>
                    </div>
                    <div class="value">12.000</div>
                </div>
                <div class="each-data">
                    <div class="icon-each-data">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="value">09:20</div>
                </div>
            </div>
            <div class="each-list-container" id="each-list-container">
                <div class="item-list">
                    <div>
                        <u>Hàng đã mua</u>
                    </div>
                    <div class="wrapper-list">
                        <!-- vòng lặp item -->
                        <div class="each-item-name"></div>
                        <!-- end -->
                    </div>
                </div>
                <div class="order-list">
                    <div>
                        <u>Danh sách đơn hàng</u>
                    </div>
                    <!-- vòng lặp item -->
                    <div class="each-order-item-list"></div>
                    <!-- end -->
                </div>
            </div>
        </div>
    </div>
    <div class="order hide">
        <div class="panel-body">
            <ul class="list-group">
                <li class="list-group-item" data-toggle="collapse" data-target="#list_order">
                    <i class="fa fa-shopping-cart font-share-advisory" aria-hidden="true"></i>
                    <?=_l('cong_orders')?>
                    <span class="badge countOrder bg-danger">0</span>
                </li>
            </ul>

            <div id="list_order" class="collapse listCollapse mbot30">

            </div>

            <ul class="list-group">
                <li class="list-group-item" data-toggle="collapse" data-target="#list_advisory">
                    <i class="fa fa-share-alt font-share-advisory" aria-hidden="true"></i>
                    <?=_l('cong_kb_care_of')?>
                    <span class="badge countAdvisory bg-danger">0</span>
                </li>
            </ul>
            <div id="list_advisory" class="collapse listCollapse emtry-advisory">

            </div>
        </div>
    </div>
</div>
