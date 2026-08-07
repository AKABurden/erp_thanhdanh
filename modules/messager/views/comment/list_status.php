
<style>
    .border0{
        border-radius:0px!important;
    }
</style>
<?php
    $VersionAppFB   =   get_option('VersionAppFB');
    $IdAppFB        =   get_option('IdAppFB');
    $basePath       =   module_dir_url('messager', 'uploads/');
    $baseAssets     =   module_dir_url('messager', 'assets/');
?>

<div class="list">
    <div class="filter-search">
        <div class="filter-by-name title">
            Tất cả bài viết
        </div>
    </div>
    <?php
        $senders = json_decode($list_comment)->data;
    ?>
    <div class="list-profile">
    	<?php foreach($senders as $key => $value){?>
            <?php
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://graph.facebook.com/".$VersionAppFB."/".$value->id."?access_token=".$_COOKIE['access_token_page_active'].'&fields=object_id',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Cache-Control: no-cache",
                        "Connection: keep-alive",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $data_messager = curl_error($curl);
                curl_close($curl);

                $response   =   json_decode($response);
            ?>




            <div class="content-profile" id_object="<?=$response->object_id?>" id_post="<?=$value->id?>" >
                <div class="img-info">
                    <img class="border0" src="https://graph.facebook.com/<?=$response->object_id?>/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">
                </div>
                <div class="some-info">
                    <div class="name-profile">
                       Bài viết trên Trang Facebook
                    </div>
                    <div class="chat-profile">
                        <?php echo (!empty($value->comments->data[0]->message) ? $value->comments->data[0]->message : 'Chưa có bình luận') ?>
                    </div>
                </div>
                <div class="time-info">
                    T3
                </div>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
	    <!-- end -->
    </div>
</div>