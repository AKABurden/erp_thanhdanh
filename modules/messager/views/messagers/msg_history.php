          <?php echo form_hidden('idd',$idd); ?>
          <div class="msg_history">
            
            <div class="messages-container">
            <?php
             foreach ($msg_history as $key => $value) {
              echo $this->load->view("messager/msg_history_each", array('value' => $value,'idd'=>$idd), true);
             } ?>
           </div>
          </div>
          <div class="type_msg">
            <div class="input_msg_write">
              <input type="text" class="write_msg" placeholder="Type a message" />
              <button class="msg_send_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
            </div>
          </div>
          <script type="text/javascript">
            
            var textArea = document.getElementById('msg_history');
            textArea.scrollTop = textArea.scrollHeight;
            $('.msg_send_btn').on('click', (e)=>{
                  var idd = $('[name="idd"]').val();
                  var msg_send_btn = $('.write_msg').val();
                      dataString={recipient:{id:idd},message:{text:msg_send_btn}};
                      jQuery.ajax({
                        type: "post",
                        url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
                        data: dataString,
                        cache: false,
                        success: function (response) {
                          data={idd:idd,msg_send_btn:msg_send_btn};
                            jQuery.ajax({
                                type: "post",
                                url:"<?=base_url()?>messager/note_messager",
                                data: data,
                                cache: false,
                                success: function (response) {
                                    response = JSON.parse(response);
                          $('.messages-container').append(response.message);
                          $('.write_msg').val('');
                          $('.write_msg').change();
                                }
                            });

                        }
                        });
            });
            $('.write_msg').on('change', (e)=>{
              var idd = $('[name="idd"]').val();
              if($('.write_msg').val() == '')
              {
         dataString={recipient:{id:idd},sender_action:"typing_off"};
          jQuery.ajax({
            type: "post",
            url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
            data: dataString,
            cache: false,
            success: function (response) {
            }
            });
              }else{
          dataString={recipient:{id:idd},sender_action:"typing_on"};
          jQuery.ajax({
            type: "post",
            url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
            data: dataString,
            cache: false,
            success: function (response) {
            }
            });
          }
          });
            $('.write_msg').on('keyup', (e)=>{
              var idd = $('[name="idd"]').val();
              if($('.write_msg').val() == '')
              {
         dataString={recipient:{id:idd},sender_action:"typing_off"};
          jQuery.ajax({
            type: "post",
            url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
            data: dataString,
            cache: false,
            success: function (response) {
            }
            });
              }else{
          dataString={recipient:{id:idd},sender_action:"typing_on"};
          jQuery.ajax({
            type: "post",
            url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
            data: dataString,
            cache: false,
            success: function (response) {
            }
            });
          }
          });    
          $('.write_msg').on('click', (e)=>{
              var idd = $('[name="idd"]').val();
              
         dataString={recipient:{id:idd},sender_action:"mark_seen"};
          jQuery.ajax({
            type: "post",
            url:"https://graph.facebook.com/v3.1/me/messages?access_token=EAABzh5syFH8BADZCjZAUWid2q9Ef2wnxgCEA7pG7yDPrwEl34upolkTlHg2v4dhlnMz7QcoIydb0OSM4B4rxPchnZBG1ixfhNVVFZAQj4l0wJDWoZAWF6ULpVdTi3w6yPjTZB1lCNuT4Kg7PJ0Xk7ftaNqzg6pU7TwMLZBcZA56PhMO1MoOoi6c86vMdliVf3EMo2WtfZBSfZA0wZDZD",
            data: dataString,
            cache: false,
            success: function (response) {
            }
            });
              
          });        
          </script>