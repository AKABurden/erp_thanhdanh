<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body onload="init()">
  <div id="sample" style="position: relative;">
    <!-- <div class="markwater" style="position: absolute;height: 75px;width: 96px;background: #f5f5f5;z-index: 9;top: 5px;left: 8px;"></div> -->
    <div style="width: 100%; height: 300px; display: flex; justify-content: space-between">
      <div id="myPaletteDiv" style="width: 105px; margin-right: 2px; background-color: whitesmoke; border: solid 1px black"></div>
      <div id="myDiagramDiv" style="flex-grow: 1; border: solid 1px black"></div>
    </div>
    <div>
      <div class="mtop10">
        <button class="btn btn-success" id="SaveButton" onclick="save()" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_saving')?>"><?=_l('cong_save')?></button>
        <button class="btn btn-info" onclick="load()"><?=_l('cong_load')?></button>
        <button class="btn btn-danger" onclick="ClearFlowChart()"><?=_l('cong_clear')?></button>
      </div>
        <hr/>
        <div class="hide">
          <textarea id="FlowChart_Dashboard">
                <?php $flowChart_Client = get_option('flowChart_Client');?>
              <?php if(!empty($flowChart_Client)){?>
                  <?=$flowChart_Client?>
              <?php } else {?>
              { "class": "go.GraphLinksModel",
                "linkFromPortIdProperty": "fromPort",
                "linkToPortIdProperty": "toPort",
                "nodeDataArray": [
                    {"text":"Start", "figure":"Circle", "fill":"#00AD5F", "key":-1, "loc":"-550 -250"},
                    {"text":"DB", "figure":"Database", "fill":"lightgray", "key":-4, "loc":"-910 -160"}
                ],
                "linkDataArray": []
               }
              <?php } ?>

          </textarea>
        </div>
    </div>
  </div>
</body>
