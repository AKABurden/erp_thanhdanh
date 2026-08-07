<?php       
  if($value->from->id == $idd)
      { ?>
            <div class="incoming_msg">
              <?php if($v != 1){?>
              <div class="incoming_msg_img"> <img src="https://graph.facebook.com/<?=$idd?>/picture?height=100&amp;width=100&amp;access_token=<?=$token?>" alt="sunil"> </div>
              <?php $v = 1; }?>
              <div class="received_msg">
                <div class="received_withd_msg">
                  <p><?= $value->message ?></p>
                  <span class="time_date"><?php echo time_ago($value->created_time); ?></span></div>
              </div>
            </div>
            <?php }else{ 
              $v = 2;
              ?>
            <div class="outgoing_msg">
              <div class="sent_msg">
                <p><?= $value->message ?></p>
                <span class="time_date"><?php echo time_ago($value->created_time); ?></span> </div>
            </div>

    <?php }  ?>