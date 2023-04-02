<?php
//(C)Замотаев Анатолий Николаевич
include("include/session.php");
$_SESSION['auth'] = md5(uniqid(microtime()));
?>

<!DOCTYPE html>
<html lang="ru">
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="fz39nNuPLyVKzX2hFqOZOQp69sksn2UWrQsBgqmR" />
<title>Журнал по учету заявок на ремонт лифтов.</title>
<meta name="description" content="Электронный журнал заявок по ремнту лифтов ->" />
<meta name="author" content="Zamotaev Anatoliy" />
<script type="text/javascript" src="js/mobile-detect.min.js"></script>
<link rel="stylesheet" href="./css/index.css?v1.001" /> 
<style>
  * {
    box-sizing: border-box;
  }

 



  
  /* =========================================
Spinner
========================================= */

  #circle1 {
    animation: circle1 4s linear infinite, circle-entry 6s ease-in-out;
    background: #888;
    border-radius: 50%;
    border: 10px solid #00a4a2;
    box-shadow: 0 0 0 2px black, 0 0 0 6px #00fffc;
    height: 400px;
    width: 400px;
    position: absolute;
    top: 20px;
    left: 50%;
    margin-left: -200px;
    overflow: hidden;
    opacity: 0.4;
    z-index: -3;
  }

  #inner-cirlce1 {
    background: #888;
    border-radius: 50%;
    border: 36px solid #00fffc;
    height: 360px;
    width: 360px;
    margin: 10px;
  }

  #inner-cirlce1:before {
    content: " ";
    width: 240px;
    height: 380px;
    background: #888;
    position: absolute;
    top: 0;
    left: 0;
  }

  #inner-cirlce1:after {
    content: " ";
    width: 380px;
    height: 240px;
    background: #888;
    position: absolute;
    top: 0;
    left: 0;
  }

 
  .button--loading .button__text {
    visibility: hidden;
    opacity: 0;
  }

  .button--loading::after {
    cursor: not-allowed;
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    border: 4px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
  }

  @keyframes button-loading-spinner {
    from {
      transform: rotate(0turn);
    }

    to {
      transform: rotate(1turn);
    }
  }
</style>

</head>

<body>
    <div class="container">
        <div class="form">
            <div class="title shimmer">Safety House</div>
            <div class="input-container ic1">
              <input  name="username" id="username" class="input" type="text" placeholder=" " />
              <div class="cut"></div>
              <label for="username" class="placeholder">Логин</label>
            </div>
            <div class="input-container ic2">
              <input id="pass" class="input" type="password" placeholder=" " />
              <div class="cut"></div>
              <label for="pass" class="placeholder">Пароль</label>
            </div>
           
            <button type="text" id="login" class="submit" disabled>Войти</button>

            <div class="inputGroup">
                <input id="autoLogin" name="option1" type="checkbox"/>
                <label for="autoLogin">Входить автоматически</label>
              </div>
          </div>
    </div>
    <canvas id="glcanvas" width="0" height="0"></canvas>
<script src="./js/index.js"></script>
</body>
<!-- (c) Zamotaev Anatoliy Nikolaevich -->
</html>