<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }
    .progressbar li {
        list-style-type: none;
        width: 16%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }
    .progressbar li:before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }
    .progressbar li:after {
        width: 100%!important;
        height: 2px!important;
        content: ''!important;
        position: absolute!important;
        background-color: #7d7d7d!important;
        top: 4px!important;
        left: -50%!important;
        z-index: -1!important;
    }
    .progressbar li:first-child:after {
        content: none;
        display: none;
    }
    .progressbar li.active {
        color: green;
    }
    .progressbar li.active:before {
        border-color: #55b776;
    }
    .progressbar li.active + li:after {
        background-color: #55b776!important;
    }
    .font11
    {
        font-size: 11px;
    }
    .btn-info.active, .btn-info:active{
        background-color: #094865;
    }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="left">
                <span class="bold uppercase fsize18"><?=$title?></span>
            </div>
            <hr class="hr-panel-heading mtop0"/>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="clearfix mtop20"></div>
<?php 
    // $string_Row = '<ul class="progressbar">';
    // $string_Row.=process_purchases_down(22,2);
    // $string_Row.='</ul>';
    var_dump(process_purchases_down(22,2,''));die;
?>
<?php echo  $string_Row?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>