<style type="text/css">
	#mobile-search {
		display: none;
	}
	#search_input {
		display: none;
	}
	#header {
		display: none;
	}
	.content-sub {
		color: #8c8c8c;
	}
	.process_mobile_purchase_order {
		width: 100%;
	}
	.wap-step {
		color: #8c8c8c;
	}
	.is-step:not(:last-child) {

	}
	.is-step {
		position: relative;
		float: left;
		height: 18px;
    	width: calc(20% - 5px);
    	background: #dedede;
	}
	.is-step-import {
		position: relative;
		float: left;
		height: 18px;
    	width: calc(50% - 5px);
    	background: #dedede;
	}
	.is-step.active, .is-step-import.active {
    	background: #78fd88;
	}
	.is-step.select-step, .is-step-import.select-step {
    	background: #308cc3;
	}
	.is-step.cancel-step, .is-step-import.cancel-step {
    	background: #ff3e3e;
	}
	.is-step:before, .is-step-import:before {
	    content: "";
	    position: absolute;
	    width: 18px;
	    height: 16px;
	    left: -10px;
	    background: #fff;
	    transform: rotate(40deg);
	}
	.is-step::after, .is-step-import::after {
	    content: "";
	    position: absolute;
	    width: 12px;
	    height: 13px;
	    right: -5px;
	    top: 2px;
	    background: #dedede;
	    transform: rotate(40deg);
	    z-index: 9;
	}
	.is-step.active::after, .is-step-import.active::after {
	    background: #78fd88;
	}
	.is-step.select-step::after, .is-step-import.select-step::after {
	    background: #308cc3;
	}
	.is-step.cancel-step::after, .is-step-import.cancel-step::after {
	    background: #ff3e3e;
	}
	.mright5 {
		margin-right: 5px;
	}
	.image-small-mobile {
		height: 25px;
	    width: 25px;
	    border-radius: 50%;
	}
</style>