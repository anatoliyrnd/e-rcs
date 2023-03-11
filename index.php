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
<style>
  * {
    box-sizing: border-box;
  }

  html {
    background: #888;
    background-size: cover;
    font-size: 8px;
    height: 100%;
    overflow: hidden;
    position: absolute;
    text-align: center;
    width: 100%;
  }



  #logo {
    animation: logo-entry 4s ease-in;
    width: 350px;
    margin: 0 auto;
    position: relative;
    z-index: 40;
  }

  h1 {
    animation: text-glow 2s ease-out infinite alternate;
    font-family: "Ubuntu", sans-serif;
    color: #00a4a2;
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    text-shadow: 0 0 10px #ff3465, 0 0 20px #fe#0c6125, 0 0 30px #ff3465, 0 0 40px #00a4a2,
      0 0 50px #ff3465, 0 0 60px #777, 0 0 70px #23feff;
    top: 50px;
  }

  h1::before {
    animation: before-glow 2s ease-out infinite alternate;
    border-left: 300px solid transparent;
    border-bottom: 10px solid #00a4a2;
    content: " ";
    height: 0;
    position: absolute;
    right: -74px;
    top: -10px;
    width: 0;
  }

  h1::after {
    animation: after-glow 2s ease-out infinite alternate;
    border-left: 100px solid transparent;
    border-top: 16px solid #00a4a2;
    content: " ";
    height: 0;
    position: absolute;
    right: -85px;
    top: 24px;
    transform: rotate(-47deg);
    width: 0;
  }

  /* =========================================
Log in form
========================================= */
  li::marker {
    color: #888;
  }

  input:-webkit-autofill,
  input:-webkit-autofill:hover,
  input:-webkit-autofill:focus,
  textarea:-webkit-autofill,
  textarea:-webkit-autofill:hover,
  textarea:-webkit-autofill:focus,
  select:-webkit-autofill,
  select:-webkit-autofill:hover,
  select:-webkit-autofill:focus {
    border: 1px solid #00fffc;
    -webkit-text-fill-color: #efe;
    border-radius: 5px;
    -webkit-box-shadow: 0 0 0px 1000px #0b4252 inset;
    transition: background-color 5000s ease-in-out 0s;
    animation: scaleUp ease 2.87s infinite;
  }

  @keyframes scaleUp {
    0% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.01);
    }

    100% {
      transform: scale(1);
    }
  }

  #fade-box {
    animation: input-entry 3s ease-in;
    z-index: 4;
  }

  .lift-login form {
    animation: form-entry 3s ease-in-out;
    background: linear-gradient(#004746e3, #363636d9);
    border: 6px solid #00a4a2;
    box-shadow: 0 0 15px #00fffd;
    border-radius: 5px;
    display: inline-block;
    height: 220px;
    margin: 160px auto 0;
    position: relative;
    z-index: 4;
    width: 350px;
    transition: 1s all;
  }

  .lift-login form:hover {
    border: 6px solid #00fffd;
    box-shadow: 0 0 25px #00fffd;
    transition: 1s all;
  }

  input {
    background: #222;
    background: linear-gradient(#333, #222);
    border: 1px solid #444;
    border-radius: 5px;
    box-shadow: 0 2px 0 #000;
    color: #888;
    display: block;
    font-family: "Cabin", helvetica, arial, sans-serif;
    font-size: 13px;
    font-size: 1.3rem;
    height: 40px;
    margin: 20px auto 10px;
    padding: 0 10px;
    text-shadow: 0 -1px 0 #000;
    width: 300px;
    font-size: 16px;
  }

  input:focus {
    animation: box-glow 1s ease-out infinite alternate;
    background: #0b4252;
    background: linear-gradient(#333933, #222922);
    border-color: #00fffc;
    box-shadow: 0 0 5px rgba(0, 255, 253, 0.2),
      inset 0 0 5px rgba(0, 255, 253, 0.1), 0 2px 0 #000;
    color: #efe;
    outline: none;
  }

  input:invalid {
    border: 2px solid red;
    box-shadow: 0 0 5px rgba(255, 0, 0, 0.2),
      inset 0 0 5px rgba(255, 0, 0, 0.1), 0 2px 0 #000;
  }

  button {
    animation: input-entry 3s ease-in;
    background: #222;
    background: linear-gradient(#333, #222);
    box-sizing: content-box;
    border: 1px solid #444;
    border-left-color: #000;
    border-radius: 5px;
    box-shadow: 0 2px 0 #000;
    color: #fff;
    display: block;
    font-family: "Cabin", helvetica, arial, sans-serif;
    font-size: 13px;
    font-weight: 400;
    height: 40px;
    line-height: 40px;
    margin: 20px auto;
    padding: 0;
    position: relative;
    text-shadow: 0 -1px 0 #000;
    width: 300px;
    transition: 1s all;
  }

  button:hover,
  button:focus {
    background: #0c6125;
    background: linear-gradient(#393939, #292929);
    color: #00fffc;
    outline: none;
    transition: 1s all;
  }

  button:active {
    background: #292929;
    background: linear-gradient(#393939, #292929);
    box-shadow: 0 1px 0 #000, inset 1px 0 1px #222;
    top: 1px;
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

  /* =========================================
Hexagon Mesh
========================================= */
  .hexagons {
    animation: logo-entry 4s ease-in;
    color: #888;
    font-size: 52px;

    letter-spacing: -0.2em;
    line-height: 0.7;
    position: absolute;
    text-shadow: 0 0 6px #00fffc;
    top: 310px;
    width: 100%;

    z-index: -3;
  }

  @media only screen and (min-width: 500px) {
    .hexagons {
      font-size: 5.1rem;
      transform: perspective(300px) rotateX(60deg) scale(1.4);
    }

  }

  @media only screen and (max-width: 500px) {
    .hexagons {
      font-size: 3.1rem;

    }
  }

  /* =========================================
Animation Keyframes
========================================= */

  @keyframes logo-entry {
    0% {
      opacity: 0;
    }

    80% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  @keyframes circle-entry {
    0% {
      opacity: 0;
    }

    20% {
      opacity: 0;
    }

    100% {
      opacity: 0.4;
    }
  }

  @keyframes input-entry {
    0% {
      opacity: 0;
    }

    90% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  @keyframes form-entry {
    0% {
      height: 0;
      width: 0;
      opacity: 0;
      padding: 0;
    }

    20% {
      height: 0;
      border: 1px solid #00a4a2;
      width: 0;
      opacity: 0;
      padding: 0;
    }

    40% {
      width: 0;
      height: 220px;
      border: 6px solid #00a4a2;
      opacity: 1;
      padding: 0;
    }

    100% {
      height: 220px;
      width: 350px;
    }
  }

  @keyframes box-glow {
    0% {
      border-color: #00b8b6;
      box-shadow: 0 0 5px rgba(0, 255, 253, 0.2),
        inset 0 0 5px rgba(0, 255, 253, 0.1), 0 2px 0 #000;
    }

    100% {
      border-color: #00fffc;
      box-shadow: 0 0 20px rgba(0, 255, 253, 0.6),
        inset 0 0 10px rgba(0, 255, 253, 0.4), 0 2px 0 #000;
    }
  }

  @keyframes text-glow {
    0% {
      color: #00a4a2;
      text-shadow: 0 0 10px #bbb, 0 0 20px #aaa, 0 0 30px #999,
        0 0 40px #888, 0 0 50px #777, 0 0 60px #777, 0 0 70px #555;
    }

    100% {
      color: #00fffc;
      text-shadow: 0 0 20px rgba(0, 255, 253, 0.6),
        0 0 10px rgba(0, 255, 253, 0.4), 0 2px 0 #000;
    }
  }

  @keyframes before-glow {
    0% {
      border-bottom: 10px solid #00a4a2;
    }

    100% {
      border-bottom: 10px solid #00fffc;
    }
  }

  @keyframes after-glow {
    0% {
      border-top: 16px solid #00a4a2;
    }

    100% {
      border-top: 16px solid #00fffc;
    }
  }

  @keyframes circle1 {
    0% {
      -moz-transform: rotate(0deg);
      -ms-transform: rotate(0deg);
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -moz-transform: rotate(360deg);
      -ms-transform: rotate(360deg);
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  .fontred,
  .fontred:hover,
  .fontred:focus {
    color: red;
    cursor: not-allowed;
  }

  .fontgreen,
  .fontgreen:hover,
  .fontgreen:focus {
    color: rgb(81, 248, 15);
    cursor: not-allowed;
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
  <div id="logo">
    <h1><i> Kompany Name</i></h1>
  </div>
  <section class="lift-login">
    <form action="" method="">
      <div id="fade-box">
        <input type="text" name="username" id="username" placeholder="Имя пользователя" required />
        <input type="password" id="pass" placeholder="Пароль" required />
        <button id="login">Войти</button>
      </div>
    </form>
    <div class="hexagons">
      <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span>
      <span>&#x2B22;</span> <br />
      <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span>
      <br />
      <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span>
      <span>&#x2B22;</span> <br />
      <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span>
      <br />
      <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span> <span>&#x2B22;</span>
      <span>&#x2B22;</span>
    </div>
  </section>

  <div id="circle1">
    <div id="inner-cirlce1">
      <h2></h2>
    </div>
  </div>

  <ul>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
  </ul>
  <script>
    const params = new Proxy(new URLSearchParams(window.location.search), {
      get: (searchParams, prop) => searchParams.get(prop),
    });
    // Get the value of "some_key" in eg "https://example.com/?some_key=some_value"
    let tokenAuth = params.e; // "some_value"
    if (tokenAuth) {
      localData("exit");
    }
    let detect = new MobileDetect(window.navigator.userAgent);
    let mobile = detect.mobile();
    let localSt = localData();
    if (localSt) {
      //если есть данные для автоматического входа отправим их на сервер
      fetch("login2.php", {
        method: "post",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        //make sure to serialize your JSON body
        body: JSON.stringify({
          id: localSt['id'],
          token: localSt['token'],
          mobile: mobile
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          button.classList.remove("button--loading");
          console.log(data);
          if (data.status === "error") {
            let txtold = button.innerText;
            buttonalert(data.message, 'fontred');
            setTimeout(buttonalert, 5000, txtold);
            const storage = window['localStorage']
            storage.removeItem('token');
          }
          if (data.status === "ok") {
            let txtold = button.innerText;
            buttonalert(data.message, 'fontgreen');
            setTimeout(buttonalert, 2000, txtold);
            console.log(data.url);
            // перейдем на основную страницу
            window.location.replace(data.url);
          }

        })
        .catch((error) => {
          button.classList.remove("button--loading");
          console.error("Error:", error);
        });
    }


    console.log(mobile);
    const button = document.getElementById("login");
    button.addEventListener("click", login);
    function login(Event) {
      Event.preventDefault();
      let username = document.getElementById("username").value;
      let pass = document.getElementById("pass").value;
      button.classList.add("button--loading");
      button.disabled = true;
      fetch("login2.php", {


        method: "post",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        //make sure to serialize your JSON body
        body: JSON.stringify({
          name: username,
          pname: pass,
          mobile: mobile,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          button.classList.remove("button--loading");
          if (data.status === "error") {
            let txtold = button.innerText;
            buttonalert(data.message, 'fontred');
            setTimeout(buttonalert, 5000, txtold);
          }
          if (data.status === "ok") {
            let txtold = button.innerText;
            buttonalert(data.message, 'fontgreen');
            setTimeout(buttonalert, 2000, txtold);
            console.log('save', data.url);
            if (data.token.length != 32) {
              console.log('save',);
              window.location.replace(data.url);
              // если нет токена то переходим дальше 
            } else {
              localData('save', data.token, data.id, data.userName)
              window.location.replace(data.url);
              // сохранимся в локальном хранилище
            }
          }

        })
        .catch((error) => {
          button.classList.remove("button--loading");
          let txtold = button.innerText;
          buttonalert('Ошибка сервера', 'fontred');
          setTimeout(buttonalert, 5000, txtold);
          console.error("Error:", error);
        });
    }
    function buttonalert(txt, act = false) {
      if (act) {
        button.innerText = txt;
        button.classList.add(act);
      } else {
        button.innerText = txt;
        button.className = '';
        button.disabled = false;
      }
    }

    function storageAvailable(type) {
      try {
        let storage = window[type],
          x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
      } catch (e) {
        return false;
      }
    }
    function localData(type = '', token = '', id = '', name = '') {
      if (type === "exit") {
        if (storageAvailable('localStorage')) {
          // localStorage работает
          let storage = window['localStorage']


          login['token'] = storage.getItem('token', token);
          if (login['token']?.length > 0) {
            storage.removeItem("token");
          }
          login['id'] = storage.getItem('id', id);
          login['username'] = storage.getItem('name', name);
          return login;

        } else {
          console.warn("localStorage не работает");
          return false;
        }
      }
      if (type === "save") {
        if (storageAvailable('localStorage')) {
          // localStorage работает
          let storage = window['localStorage']
          storage.setItem('token', token);
          storage.setItem('id', id);
          storage.setItem('name', name);

        } else {
          console.warn("localStorage не работает");
          return;
        }
      } else {
        if (storageAvailable('localStorage')) {
          // localStorage работает
          let storage = window['localStorage']
          let login = [];

          login['token'] = storage.getItem('token', token);
          if (login['token']?.length != 32) {
            return false;
          }
          login['id'] = storage.getItem('id', id);
          login['username'] = storage.getItem('name', name);
          return login;

        } else {
          console.warn("localStorage не работает");
          return false;
        }
      }
    }
  </script>
</body>
<!-- (c) Zamotaev Anatoliy Nikolaevich -->
</html>