<style>
.table-image {
    height: 120px !important;
    width: 250px;
}
</style>
<div class="modal-dialog modal-check_hour">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Thông tin check in / check out') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $timekeeping_detail = get_table_where('tbl_timekeeping_detail',['id'=>$timekeeping_detail_id],'','row_array');
                        $staff_name = '';
                        // $type = '';
                        $date = '';
                        if(!empty($timekeeping_detail)){
                            // $type = $timekeeping_detail_hour['type'];
                            $date = date("d/m", strtotime($timekeeping_detail['date']));
                            $staff = get_table_where('tbl_personnel',['id'=>$timekeeping_detail['staff_id']],'','row_array');
                            $staff_name = $staff['fullname'];
                        }
                    ?>
                    <div class="col-md-12">
                        <h4 class="text-danger text-center" style="text-transform: uppercase;"></h4>
                    </div>
                    <br>
                    <?php if($type == 1){ ?>
                    <div class="col-md-6 col-md-offset-3">
                        <div class="text-center"> GIỜ VÀO : <span
                                class="text-danger"><?= $timekeeping_detail_hour['hour']; ?> (<?= $date ?>)</span>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <?php $images = ($timekeeping_detail_hour['image'] != null) ? base_url() . "uploads/timekeeping_staffs/".$timekeeping_detail_hour['image'].'?' : base_url() . "assets/images/tnh/no_image.png"; ?>
                            <div class="preview_image" style="width: auto;">
                                <div class="center display-block contract-attachment-wrapper">
                                    <div>
                                        <a href="<?= $images ?>" data-lightbox="customer-profile"
                                            class="display-block mbot5">
                                            <div class="table-image">
                                                <img src="<?=$images?>"
                                                    class="<?= ($timekeeping_detail_hour['image'] != null) ? 'image_timekeeping' : ''  ?>"
                                                    style="border-radius: 50%;width: 50%;height: 100%;" />
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } elseif($type == 2){ ?>
                    <?php 
                        $date_in = date("d/m", strtotime($timekeeping_detail_in['date']));
                    ?>
                    <div class="col-md-6">
                        <div class="text-center"> GIỜ VÀO : <span
                                class="text-danger"><?= $timekeeping_detail_hour['hour']; ?> (<?= $date_in ?>)</span>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <?php $images = ($timekeeping_detail_hour['image'] != null) ? base_url() . "uploads/timekeeping_staffs/".$timekeeping_detail_hour['image'].'?' : base_url() . "assets/images/tnh/no_image.png"; ?>
                            <div class="preview_image" style="width: auto;">
                                <div class="center display-block contract-attachment-wrapper">
                                    <div>
                                        <a href="<?= $images ?>" data-lightbox="customer-profile"
                                            class="display-block mbot5">
                                            <div class="table-image">
                                                <img src="<?=$images?>"
                                                    class="<?= ($timekeeping_detail_hour['image'] != null) ? 'image_timekeeping' : ''  ?>"
                                                    style="border-radius: 50%;width: 50%;height: 100%;" />
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $timekeeping_detail_hour_out = get_table_where('tbl_timekeeping_detail_hour',['id'=>$timekeeping_detail_hour_id],'','row_array');  ?>
                    <div class="col-md-6">
                        <div class="text-center"> GIỜ RA : <span
                                class="text-danger"><?= $timekeeping_detail_hour_out['hour']; ?> (<?= $date ?>)</span>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <?php $images = ($timekeeping_detail_hour_out['image'] != null) ? base_url() . "uploads/timekeeping_staffs/".$timekeeping_detail_hour_out['image'].'?' : base_url() . "assets/images/tnh/no_image.png"; ?>
                            <div class="preview_image" style="width: auto;">
                                <div class="center display-block contract-attachment-wrapper">
                                    <div>
                                        <a href="<?= $images ?>" data-lightbox="customer-profile"
                                            class="display-block mbot5">
                                            <div class="table-image">
                                                <img src="<?=$images?>"
                                                    class="<?= ($timekeeping_detail_hour_out['image'] != null) ? 'image_timekeeping' : ''  ?>"
                                                    style="border-radius: 50%;width: 50%;height: 100%;" />
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
// var element = $('.image_timekeeping');

// function setTransform(transform, element) {
//     element.css('-ms-transform', transform);
//     element.css('-webkit-transform', transform);
//     element.css('-moz-transform', transform);
//     element.css('transform', transform);
// }

// $(".image_timekeeping").click(function() {
//     elementNew = $('.lb-image');
//     setTransform('rotate(180deg)', elementNew);
// });

// setTransform('rotate(180deg)', element);
</script>