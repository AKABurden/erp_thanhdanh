<style type="text/css">
.spinner {
  /*position: absolute;*/
  position: fixed;
  /*top: 45%;
  left: 45%;
  z-index: 1;
  height: 40px;
  width: 40px;*/
  top: 40%;
  left: 50%;
  z-index: 30000;
  /*height: 40px;*/
  /*width: 40px;*/
  transform: translate(-50%, -50%);
}
[class^="ball-"] {
  position: fixed;
  display: block;
  left: 30px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  transition: all 0.5s;
  animation: circleRotate 4s both infinite;
  transform-origin: 0 250% 0;
}
@keyframes circleRotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(1440deg);
  }
}
.ball-1 {
  z-index: -1;
  background-color: #2196f3;
  animation-timing-function: cubic-bezier(0.5, 0.3, 0.9, 0.9);
}
.ball-2 {
  z-index: -2;
  background-color: #03a9f4;
  animation-timing-function: cubic-bezier(0.5, 0.6, 0.9, 0.9);
}
.ball-3 {
  z-index: -3;
  background-color: #00bcd4;
  animation-timing-function: cubic-bezier(0.5, 0.9, 0.9, 0.9);
}
.ball-4 {
  z-index: -4;
  background-color: #009688;
  animation-timing-function: cubic-bezier(0.5, 1.2, 0.9, 0.9);
}
.ball-5 {
  z-index: -5;
  background-color: #4caf50;
  animation-timing-function: cubic-bezier(0.5, 1.5, 0.9, 0.9);
}
.ball-6 {
  z-index: -6;
  background-color: #8bc34a;
  animation-timing-function: cubic-bezier(0.5, 1.8, 0.9, 0.9);
}
.ball-7 {
  z-index: -7;
  background-color: #cddc39;
  animation-timing-function: cubic-bezier(0.5, 2.1, 0.9, 0.9);
}
.ball-8 {
  z-index: -8;
  background-color: #ffeb3b;
  animation-timing-function: cubic-bezier(0.5, 2.4, 0.9, 0.9);
}
</style>
<div class="spinner hide" id="loader">
  <span class="ball-1"></span>
  <span class="ball-2"></span>
  <span class="ball-3"></span>
  <span class="ball-4"></span>
  <span class="ball-5"></span>
  <span class="ball-6"></span>
  <span class="ball-7"></span>
  <span class="ball-8"></span>
</div>