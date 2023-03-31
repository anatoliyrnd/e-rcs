<?php
//файл управления пользователями v2.0
//(С) Замотаев Анатолий Николаевич
include("include/session.php");
include("include/checksession.php");
include("include/ldisp_config.php");
include("include/function.php");
try {
  $dbh = new PDO(db_PDO, db_user, db_password);
  $dbh->exec("set names utf8");
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage();
  die();
}
if (isset($user_id)) {
  $nacl    = nacl($user_id);
  $user_id = (int) $user_id;
  if ($nacl != $user_nacl) {
    echo "Ошибка авторизации";
    exit;
  }
  $q        = "SELECT `user_edit_user`, `user_localadmin`,`user_level`  FROM `lift_users` WHERE `user_id`=$user_id ";
  $user     = $dbh->query($q);
  $userdata = $user->fetch(PDO::FETCH_ASSOC);
  if (!$userdata) {
    echo "Я Вас не знаю!";
    exit();
  }
  $user = null;
  if ($userdata['user_localadmin'] || !$userdata['user_level']) {
    $useradmin = true;
  }
  if (!$useradmin && !$userdata['user_edit_user']) {
    header('Content-type: application/json');
    echo '{"status":"Вам запрещен доступ к реактированию пользователей"}';
    exit;

  }
} else {
  echo "Я Вас не знаю";
  exit();
}


$q      = "SELECT * FROM `lift_users`";
$stuser = $dbh->query($q);
while ($userdata = $stuser->fetch(PDO::FETCH_ASSOC)) {
  $edit = "'" . $userdata['user_name'] . "','" . $userdata['user_login'] . "','" . $userdata['user_phone'] . "'," . $userdata['user_localadmin'] . "," . $userdata['user_edit_obj'] . "," . $userdata['user_edit_user'] . "," . $userdata['user_disppermission'] . "," . $userdata['user_add_call'] . "," . $userdata['user_read_all_calls'] . "," . $userdata['user_protect_edit'] . "," . $userdata['user_id'] . "," . $userdata['user_level'] . "," . $userdata['user_block'];
  $tr .= "<tr style='cursor:pointer;' onclick=\"modal('edit',[$edit])\"><td><span class='tab'>Имя</span>";
  $tr .= "<span >" . $userdata['user_name'] . "</span></td>";
  $tr .= "<td> <span  class='tab'>Логин</span>" . $userdata['user_login'] . "</td>";
  $tr .= "<td><span  class='tab'>Телефон</span>" . $userdata['user_phone'] . "</td>";
  $tr .= "<td><span  class='tab'>Информация</span>";
  if ($userdata['user_telegram']) {
    $tr .= "<img   src='ico/telegram.png'>";
  }
  if ($userdata['user_edit_obj']) {
    $tr .= "<img  src='ico/building.png'>";
  }
  if ($userdata['user_edit_user']) {
    $tr .= "<img   src='ico/users.png'>";
  }
  if ($userdata['user_disppermission']) {
    $tr .= "<img   src='ico/edit.png'>";
  }
  if ($userdata['user_add_call']) {
    $tr .= "<img   src='ico/add-new.png'>";
  }
  if ($userdata['user_localadmin']) {
    $tr .= "<img   src='ico/settings.png'>";
  }
  if ($userdata['user_protect_edit']) {
    $tr .= "<img   src='ico/no-document.png'>";
  }
  if ($userdata['user_read_all_calls']) {
    $tr .= "<img   src='ico/book.png'>";
  }
  if ($userdata['user_block']) {
    $tr .= "<img   src='ico/block_user.png'>";
  }
  $tr .= "</td></tr>";

}

?>
<!DOCTYPE html>
<html lang="ru">
<!-- <?php //echo $nacl." - ".$user_id. "name ".$user_name." - ".$user_level;  
?> -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=.8">
  <title>Управление пользователями</title>
  <meta name="description"
    content="Электронный журнал заявок по ремнту лифтов ->Интерфейс диспетчера -><?php echo $user_name; ?>">
  <meta name="author" content="Zamotaev Anatoliy">
  <meta name="robots" contents="noindex">
  <link href="/css/disp.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="favicon.ico">
  <style>
    .card_content {
      margin: 5px;
      padding: 10px;
      border-radius: 5px;
      border: 2px solid #4FC3F7;
    }

    * {
      box-sizing: border-box;
      font-family: "Avenir", "Helvetica", sans-serif;
    }

    body {
      padding: 10px;

      position: relative;
    }

    body {
      background-color: #f9f9f9;
    }


    table {
      border-collapse: collapse;
      text-align: left;
      width: 100%;
    }

    table tr {
      background: white;
      border-bottom: 1px solid
    }

    table th,
    table td {
      padding: 10px 20px;
    }

    table td .tab {
      background: #eee;
      color: dimgrey;
      display: none;
      font-size: 10px;
      font-weight: bold;
      padding: 5px;
      position: absolute;
      text-transform: uppercase;
      top: 0;
      left: 0;
    }
.card_content{
  width:300px;
}
    /* Simple CSS for flexbox table on mobile */
    @media(max-width: 800px) {
      table thead {
        left: -9999px;
        position: absolute;
        visibility: hidden;
      }

      table tr {
        border-bottom: 0;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        margin-bottom: 40px;
      }

      table td {
        border: 1px solid;
        margin: 0 -1px -1px 0;
        padding-top: 35px;
        position: relative;
        width: 50%;
      }

      table td .tab {
        display: block;
      }
    }


    /*  check box*/
    .user_checkbox {
      width: 300px;
      height: 26px;
      background: #333;
      margin: 20px auto;
      position: relative;
      border-radius: 50px;
      box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.5), 0px 1px 0px rgba(255, 255, 255, 0.2);
    }

    .user_checkbox:after {
      content: 'НЕТ';
      color: red;
      position: absolute;
      right: 10px;
      z-index: 0;
      font: 12px/26px Arial, sans-serif;
      font-weight: bold;
      text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.15);
    }

    .user_checkbox:before {
      content: 'ДА';
      color: #27ae60;
      position: absolute;
      left: 10px;
      z-index: 0;
      font: 12px/26px Arial, sans-serif;
      font-weight: bold;
    }

    .user_checkbox label {
      padding-left: 5px;
      display: block;
      width: 250px;
      height: 20px;
      cursor: pointer;
      position: absolute;
      top: 3px;
      left: 3px;
      z-index: 1;

      background: linear-gradient(#fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
      border-radius: 50px;
      transition: all 0.4s ease;
      box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.3);
    }

    .user_checkbox input[type=checkbox] {
      visibility: hidden;
    }

    .user_checkbox input[type=checkbox]:checked+label {
      left: 43px;
    }
  </style>
</head>

<body>

  <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" onclick="modal('new')">
    Добавить пользователя
  </button>
  <br><br>
  <table>
    <thead>
      <tr>
        <th>Имя </th>
        <th>Логин</th>
        <th>Телефон</th>
        <th>Информация</th>

      </tr>
    </thead>
    <tbody>
      <?php echo $tr; ?>
    </tbody>
  </table>
  <img id='telega' src='ico/telegram.png'> - Телеграм<br>
  <img id='ico_edit_obj' src='ico/building.png'>-Управление объектами<br>
  <img id='ico_edit_user' src='ico/users.png'> - Управление пользователями<br>
  <img id='ico_disppermission' src='ico/edit.png'> -Редактировать заявки <br>
  <img id='ico_add_call' src='ico/add-new.png'> -Создавать заявки<br>
  <img id='ico_localadmin' src='ico/settings.png'> - Администратор<br>
  <img id='ico_protect_edit' src='ico/no-document.png'> - Защищен от изменений<br>
  <img id='ico_read_all_calls' src='ico/book.png'> - разрешен просмотр всех заявок<br>
  <img id='ico_user_block' src='ico/block_user.png'> - Пользователь заблокирован<br>
  Для изменения пользователя кликните по его имени.
  <!-- <div class="mdl-tooltip" for="telega">Пользователь получает <br> уведомления в Телеграмм</div>
<div class="mdl-tooltip" for="ico_edit_obj">Пользователь имеет права <br>на редактирование объектов</div>
<div class="mdl-tooltip" for="ico_edit_user">Пользователь имеет права <br> на управление пользователями</div>
<div class="mdl-tooltip" for="ico_disppermission">Пользователь имеет доступ <br>к редактиованию открытых заявок</div>
<div class="mdl-tooltip" for="ico_add_call">Пользователь может <br>создавать новые заявки</div>
<div class="mdl-tooltip" for="ico_localadmin">Пользователь является администратором</div>
<div class="mdl-tooltip mdl-tooltip--top" for="ico_protect_edit">Пользователь защещен от изменений</div>
<div class="mdl-tooltip mdl-tooltip--top" for="ico_read_all_calls">Пользователю разрешен просмотр <br> всех открытых и закрытых заявок</div>
<div class="mdl-tooltip mdl-tooltip--top" for="ico_user_block">Пользователь заблокирован</div> -->

  <dialog class="mdl-dialog  " style="width:500px;">
    <div id='titlemodal' class='title-modal'>Управление пользователями</div>
    <div class="grid_cantainer">
      <div class="grid_item">
        <div id='form'>
          <form action="#">
            <div class="card">
              <input class="card_content" minlength="6" type="text" id="name">
              <label class="card_label" for="name">Ф.И.О.</label>
            </div>
            <div class="card">
              <input class="card_content" minlength="4" type="text" id="login">
              <label class="card_label" for="login">Логин</label>
            </div>
            <div class="card">
              <input class="card_content" minlength="4" type="text" id="pass">
              <label class="card_label" for="pass">Пароль</label>
            </div>
            <div class="card">
              <input class="card_content" data-phone-pattern data-phone-clear="false" type="phone" required
                title="+7(000) 000-00-00" id="phone">
              <label class="card_label" for="phone">Телефон</label>
              <br>
            </div>
            <div class="card">

              <div class="card_content">
                <select id='role'>
                  <option value='1'> Клиент</option>
                  <option value='2' selected>Сотрудник (механик)</option>
                  <option value='3'> Диспетчер</option>
                </select>
                <label class="card_label" for="role"> Укажите роль пользователя.</label>
              </div>


              Дополнительные права:
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="admin" />
                <label for="admin">Администратор</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="edit_obj" />
                <label for="edit_obj">Управление объектами</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="edit_user" />
                <label for="edit_user">Управление пользователями</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="disppermission" />
                <label for="disppermission">Редактировать заявки</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="add_call" />
                <label for="add_call">Создавать заявки</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="read_all_calls" />
                <label for="read_all_calls">Смотреть все заявки</label>
              </div>
              <div class="user_checkbox">
                <input type="checkbox" value="None" id="user_block" />
                <label for="user_block" style="color:red">Блокировать пользователя</label>
              </div>

            </div>



        </div>
        <div class="mdl-dialog__actions">
          <div id="psave" class="" hidden></div>

          <button type="button" class="mdl-button save" type="submit" id='save_modal'>Сохранить</button>
          <button type="button" class="mdl-button close">Отмена (Выйти)</button>
        </div>
  </dialog>

  <script>
    let datasend = {};
    function modal(type, data = {}) {
      datasend = {};
      console.log(data);
      const form = document.getElementById('form');
      // почистим поля формы и навесим обработчик изменеия полей ввода
      form.querySelectorAll('input').forEach((el) => {
        el.removeAttribute("disabled");
      })
      form.querySelectorAll('.input_user').forEach((el) => {
        el.value = '';
        el.addEventListener('input', function (event) {
          datasend[el.id] = el.value;

        })
      })

      form.querySelectorAll('checkbox').forEach((el) => {
        el.checked=false;
      })



      if (type === "new") {
        document.getElementById('titlemodal').innerHTML = "Добавить пользователя";
        datasend = { type: "new" };

      } else {
        datasend = { type: "edit" };
        datasend["user_edit_id"] = data[10];
        if (data[9] === 1) {
          form.querySelectorAll('input').forEach((el) => {
            el.setAttribute("disabled", true);
          })
        }

        document.getElementById('titlemodal').innerHTML = "Редактировать пользователя";
        document.getElementById('name').value = data[0]; //вставим в поле ФИО
        document.getElementById('name').parentNode.classList.add("is-dirty");
        document.getElementById('login').value = data[1];//вставим в поле значение логина
        document.getElementById('login').parentNode.classList.add('is-dirty');
        let pattern = /(\+7|8)[\s(]?(\d{3})[\s)]?(\d{3})[\s-]?(\d{2})[\s-]?(\d{2})/g;    // паттерн с проставленными скобками
        let phone = data[2].replace(pattern, '+7 ($2) $3-$4-$5');
        document.getElementById('phone').value = phone;
        document.getElementById('phone').parentNode.classList.add('is-dirty');
        if (data[3] === 1) {
          //если админ
          document.getElementById('admin').checked=true;
        }
        if (data[4] === 1) {
          //если есть разрешение на редактирование объектов
          document.getElementById('edit_obj').checked=true;;
        }
        if (data[5] === 1) {
          //если есть разрешение на редактирование пользователя
          document.getElementById('edit_user').checked=true;;
        }
        if (data[6] === 1) {
          //если есть разрешение на изменение заявок
          document.getElementById('disppermission').checked=true;;
        }
        if (data[7] === 1) {
          //если есть разрешение на создание заявки
          document.getElementById('add_call').checked=true;;
        }
        if (data[8] === 1) {
          //если есть разрешение на создание заявки
          document.getElementById('read_all_calls').checked=true;;
        }
        if (data[12] === 1) {
          //user blocking
          document.getElementById('user_block').checked=true;;
        }
        let index = data[11] - 1;

        document.getElementById('role').selectedIndex = index;

      }
      dialog.showModal();//вызываем подготовленное модальное окно  
    }
    let dialog = document.querySelector('dialog');

    if (!dialog.showModal) {
      dialogPolyfill.registerDialog(dialog);
    }
    // клик по кнопе отмена модального окна
    dialog.querySelector('.close').addEventListener('click', function () {

      dialog.close();
    });

    //функция по нажатию ок в модалке
    dialog.querySelector('.save').addEventListener('click', function () {//
datasend["pass"]=document.getElementById('pass').value;
      datasend["admin"] = document.getElementById('admin').checked;
      datasend["edit_obj"] = document.getElementById('edit_obj').checked;
      datasend["edit_user"] = document.getElementById('edit_user').checked;
      datasend["disppermission"] = document.getElementById('disppermission').checked;
      datasend["add_call"] = document.getElementById('add_call').checked;
      datasend["read_all_calls"] = document.getElementById('read_all_calls').checked;
      datasend["user_block"] = document.getElementById('user_block').checked;

      datasend["user_level"] = document.getElementById('role').value;
      datasend["nacl"] = "<?php echo $nacl; ?>";
      datasend["user_id"] = "<?php echo $user_id; ?>";
      datasend["user_name"] = "<?php echo $user_name; ?>";

      console.log(datasend);
      save('save_useredit.php', datasend);
    })
    document.addEventListener("DOMContentLoaded", function () {
      //шаблон ввода номера телефона. 
      let eventCalllback = function (e) {
        let el = e.target,
          clearVal = el.dataset.phoneClear,
          pattern = el.dataset.phonePattern,
          matrix_def = "+7(___) ___-__-__",
          matrix = pattern ? pattern : matrix_def,
          i = 0,
          def = matrix.replace(/\D/g, ""),
          val = e.target.value.replace(/\D/g, "");
        if (clearVal !== 'false' && e.type === 'blur') {
          if (val.length < matrix.match(/([\_\d])/g).length) {
            e.target.value = '';
            return;
          }
        }
        if (def.length >= val.length) val = def;
        e.target.value = matrix.replace(/./g, function (a) {
          return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
        });
      }
      let phone_inputs = document.querySelectorAll('[data-phone-pattern]');
      for (let elem of phone_inputs) {
        for (let ev of ['input', 'blur', 'focus']) {
          elem.addEventListener(ev, eventCalllback);
        }
      }

      //end phone


    });

    async function save(url, datasave) {
      //Функция сохранения изменений
      console.log(datasave);
      console.log(JSON.stringify(datasave));
      document.getElementById('psave').hidden = false;
      document.getElementById('save_modal').hidden = true;
      const response = await fetch(url, {
        //передаем через пост в теле массив данных 
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(datasave)
      });
      const json = await response.json();
      if (!response.ok) {
        throw new Error('Ошибка.');
      }
      if (json.status == 'ok') {
        alert('изменения сохранены');
        window.location.href = '<?php echo $link; ?>';
      } else {
        alert(json.status);
        document.getElementById('psave').hidden = true;
        document.getElementById('save_modal').hidden = false;

      }
    };     
  </script>

</body>

</html>