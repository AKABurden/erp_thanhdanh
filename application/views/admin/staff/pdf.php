<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
    $CI = &get_instance();
    $CI->load->library('ciqrcode');
?>
<style>
    @media print{
        .graph-image img{
            display:inline;
            background-image: url(<?=base_url('uploads/staffs/background_personnel2.png?v1')?>);
        }
    }
</style>
<?php if(!empty($staff)) {
    foreach($staff as $key => $value) {?>
        <div class="graph-image img" style="width: 5.7cm;
                height:9cm;
                background-image: url(<?=base_url('uploads/staffs/background_personnel2.png?v1')?>);
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                text-align: center;
                float: left;
                margin-right: 1cm;
                margin-top: 2px;
                ">
            <div>
                <div>
                    <img src="<?=$value['images']?>"
                         style="height: 3.3cm;
                         width: 3.3cm;
                         margin-left: -1%;
                         margin-top: 34%;
                         border-radius: 50%;
                    ">
                </div>
                <div style="margin-top: 9px;"><b style="text-align: center;margin-top: 13%;font-size: 13px;"><?=$value['fullname']?></b></div>
                <div  style="margin-top: 8px;"><b style="text-align: center;margin-top: -1%;font-size: 8px;"><?=!empty($value['role_name']) ? $value['role_name'] : '-'?></b></div>
                <hr style="
                        margin-top: 5px;
                        margin-bottom: 5px;
                        width: 87%;
                        background: #d27b4a;
                    ">
				<?php
                    $code = $value['codes'];
				    $qr = vn_to_str(str_replace('||', '__', $code));
				    $params['data'] = $code;
                    $params['level'] = 'H';
                    $params['size'] = 20;
                    $params['savename'] = FCPATH . 'uploads/staffs/qrcode/' . $qr . '.png';
                    $this->ciqrcode->generate($params);
                    $img = file_get_contents(FCPATH . 'uploads/staffs/qrcode/' . $qr . '.png');
				?>
                <div style="float:left;width: 40%;">
                    <img src="data:image/png;base64,<?=base64_encode($img)?>" style="width: 2cm;margin-left: 18%;margin-top: 3%;"/>
                </div>
                <div style="float:left;width: 60%;">
                    <div style="font-size: 12px;padding-top: 15px;"><b>MÃ NV</b></div>
                    <div style="padding-bottom: 5px;font-size: 12px"><b><?=$value['code']?></b></div>
                    <div style="padding-bottom: 5px;font-size: 12px"><b><?=$value['branch_name']?></b></div>
                </div>
            </div>
        </div>

	<?php }
}?>




