<?php 
include("./include/session.php");
include("./include/checksession.php");
include("./include/ldisp_config.php");
include("./include/function.php");
include("./include/static_data.php");
require_once("./include/PDO.class.php");
if (isset($user_id)) {
    $nacl    = nacl($user_id);
    $user_id = (int) $user_id;
    if ($nacl != $user_nacl) {
        $log = " (nacl)$nacl - (user-nacl) " . $user_nacl . " ";
        logsave($log, "editObjControl_php_error");
        $response['status']  = 'error';
        $response['message'] .= "ошибка авторизации";
        echojson($response);
    }
} else {
    $log = " user_id error ";
    logsave($log, "editObjControl_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не получен User ID";
    echojson($response);
}
$DB              = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$queryuser       = $DB->row("SELECT user_level, user_name, user_localadmin FROM lift_users WHERE user_id=$user_id LIMIT 1");
$user_level      = (int) $queryuser['user_level'];
$user_localadmin = (int) $queryuser['user_localadmin'];
if ($user_level and !$user_localadmin) {
    $log = " user_id доступ запрещен id $user_id  level $user_level admin $user_localadmin имя " . $queryuser['user_name'];
    logsave($log, "editObjControl_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не достаточный уровень доступа";
    echojson($response);
}

?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Website</title>
    <link rel="stylesheet" href="./scss/setting.css">
  
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
</head>
<body>
    <h1>Управление системой</h1>
    <div class="tabs">
        <input type="radio" id="tab1" name="tab-control" checked>
        <input type="radio" id="tab2" name="tab-control">
        <input type="radio" id="tab3" name="tab-control">
        <input type="radio" id="tab4" name="tab-control">
        <ul class="tabs_head">
            <li title="Просмотр логов и ошибок"><label for="tab1" role="button"><svg viewBox="0 0 24 24">
                        <path
                            d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" />
                    </svg><br><span>Просмотр логов и ошибок</span></label></li>
            <li title="Адреса объектов"><label for="tab2"
                    role="button"><!-- icon666.com - MILLIONS vector ICONS FREE --><svg
                        xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 512 512">
                        <path
                            d="M469.333,234.667A21.334,21.334,0,0,0,448,256a191.433,191.433,0,0,1-4.943,42.667H350.552q.7-10.274,1.068-20.594a21.339,21.339,0,0,0-20.583-22.062c-11.583-.109-21.651,8.807-22.062,20.583-.266,7.614-.693,14.938-1.219,22.073H204.208c-.992-13.459-1.542-27.695-1.542-42.667s.549-29.208,1.542-42.667H256a21.333,21.333,0,0,0,0-42.667H209.219C220.135,103.124,242.25,64,256,64q4.844,0,9.63.234A21.332,21.332,0,0,0,267.7,21.62q-5.812-.281-11.7-.286C126.6,21.334,21.333,126.6,21.333,256S126.6,490.667,256,490.667,490.667,385.4,490.667,256A21.334,21.334,0,0,0,469.333,234.667ZM302.76,341.334C291.5,410.725,268.5,448,256,448c-13.75,0-35.865-39.124-46.781-106.667ZM161.484,298.667H68.943a186.626,186.626,0,0,1,0-85.333h92.542C160.5,227.432,160,241.725,160,256S160.5,284.569,161.484,298.667ZM192.771,74.919c-12.38,25.9-21.328,59.248-26.708,95.747H84.26A192.732,192.732,0,0,1,192.771,74.919ZM84.26,341.334h81.8c5.38,36.5,14.328,69.842,26.708,95.747A192.732,192.732,0,0,1,84.26,341.334Zm235.427,95.579c12.352-26.065,21.083-59.512,26.328-95.579H427.74A192.727,192.727,0,0,1,319.688,436.913Z" />
                        <path
                            d="M371.6,262.693a21.336,21.336,0,0,0,24.8,0c3.411-2.437,83.6-60.687,83.6-145.359a96,96,0,1,0-192,0C288,202.005,368.188,260.255,371.6,262.693ZM384,64a53.4,53.4,0,0,1,53.333,53.333c0,45.4-34.333,82.969-53.375,100.313C364.9,200.375,330.667,163,330.667,117.334A53.4,53.4,0,0,1,384,64Z" />
                        <circle cx="384" cy="117.334" r="21.333" />
                    </svg>
                    <br><span>Адреса объектов</span></label></li>
            <li title="Отчеты и статистика"><label for="tab3"
                    role="button"><!-- icon666.com - MILLIONS vector ICONS FREE --><svg
                        xmlns="http://www.w3.org/2000/svg" id="outline_2" data-name="outline 2" viewBox="0 0 64 64">
                        <g id="_27-Report_copy" data-name="27-Report copy">
                            <path
                                d="M54.74,13.33l-10-11A1,1,0,0,0,44,2H12A3,3,0,0,0,9,5V59a3,3,0,0,0,3,3H52a3,3,0,0,0,3-3V14A1,1,0,0,0,54.74,13.33ZM45,5.59,51.74,13H46a1,1,0,0,1-1-1ZM52,60H12a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1H43v8a3,3,0,0,0,3,3h7V59A1,1,0,0,1,52,60ZM28.91,22.14a1,1,0,0,0-1-1.14H22V14a1,1,0,0,0-1-1,8,8,0,1,0,7.91,9.14ZM15,21a6,6,0,0,1,5-5.92V22a1,1,0,0,0,1,1h5.65A6,6,0,0,1,15,21ZM25,11a1,1,0,0,0-1,1v6a1,1,0,0,0,1,1h4.9a1,1,0,0,0,1-.79A5.77,5.77,0,0,0,31,17,6,6,0,0,0,25,11Zm1,6V13.13A4,4,0,0,1,29,17ZM22,45H18a1,1,0,0,0-1,1v6a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V46A1,1,0,0,0,22,45Zm-1,6H19V47h2Zm9-9H26a1,1,0,0,0-1,1v9a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V43A1,1,0,0,0,30,42Zm-1,9H27V44h2Zm9-13H34a1,1,0,0,0-1,1V52a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V39A1,1,0,0,0,38,38ZM37,51H35V40h2Zm9-17H42a1,1,0,0,0-1,1V52a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V35A1,1,0,0,0,46,34ZM45,51H43V36h2Zm5-32a1,1,0,0,1-1,1H35a1,1,0,0,1,0-2H49A1,1,0,0,1,50,19Zm0,4a1,1,0,0,1-1,1H35a1,1,0,0,1,0-2H49A1,1,0,0,1,50,23Zm0,4a1,1,0,0,1-1,1H35a1,1,0,0,1,0-2H49A1,1,0,0,1,50,27Z" />
                        </g>
                    </svg><br><span>Отчеты и статистика</span></label></li>
            <li title="Настройки системы"><label for="tab4" role="button"><svg xmlns="http://www.w3.org/2000/svg"
                        id="OBJECT" viewBox="0 0 32 32">
                        <path
                            d="M28,12H26.25l-.18-.42,1.24-1.24a3,3,0,0,0,0-4.24L25.9,4.69a3.06,3.06,0,0,0-4.24,0L20.42,5.93,20,5.75V4a3,3,0,0,0-3-3H15a3,3,0,0,0-3,3V5.75l-.42.18L10.34,4.69a3.06,3.06,0,0,0-4.24,0L4.69,6.1a3,3,0,0,0,0,4.24l1.24,1.24L5.75,12H4a3,3,0,0,0-3,3v2a3,3,0,0,0,3,3H5.75l.18.42L4.69,21.66a3,3,0,0,0,0,4.24L6.1,27.31a3.06,3.06,0,0,0,4.24,0l1.24-1.24.42.18V28a3,3,0,0,0,3,3h2a3,3,0,0,0,3-3V26.25l.42-.18,1.24,1.24a3.06,3.06,0,0,0,4.24,0l1.41-1.41a3,3,0,0,0,0-4.24l-1.24-1.24.18-.42H28a3,3,0,0,0,3-3V15A3,3,0,0,0,28,12Zm1,5a1,1,0,0,1-1,1H25.54a1,1,0,0,0-.95.7A10.55,10.55,0,0,1,24,20.16a1,1,0,0,0,.18,1.17l1.74,1.74a1,1,0,0,1,.29.71,1,1,0,0,1-.29.71L24.49,25.9a1,1,0,0,1-1.42,0l-1.74-1.74A1,1,0,0,0,20.16,24a10.55,10.55,0,0,1-1.46.61,1,1,0,0,0-.7.95V28a1,1,0,0,1-1,1H15a1,1,0,0,1-1-1V25.54a1,1,0,0,0-.7-.95A10.55,10.55,0,0,1,11.84,24a.93.93,0,0,0-.46-.12,1,1,0,0,0-.71.3L8.93,25.9a1,1,0,0,1-1.42,0L6.1,24.49a1,1,0,0,1-.29-.71,1,1,0,0,1,.29-.71l1.74-1.74A1,1,0,0,0,8,20.16a10.55,10.55,0,0,1-.61-1.46,1,1,0,0,0-1-.7H4a1,1,0,0,1-1-1V15a1,1,0,0,1,1-1H6.46a1,1,0,0,0,1-.7A10.55,10.55,0,0,1,8,11.84a1,1,0,0,0-.18-1.17L6.1,8.93a1,1,0,0,1-.29-.71,1,1,0,0,1,.29-.71L7.51,6.1a1,1,0,0,1,1.42,0l1.74,1.74A1,1,0,0,0,11.84,8a10.55,10.55,0,0,1,1.46-.61,1,1,0,0,0,.7-1V4a1,1,0,0,1,1-1h2a1,1,0,0,1,1,1V6.46a1,1,0,0,0,.7,1A10.55,10.55,0,0,1,20.16,8a1,1,0,0,0,1.17-.18L23.07,6.1a1,1,0,0,1,1.42,0L25.9,7.51a1,1,0,0,1,.29.71,1,1,0,0,1-.29.71l-1.74,1.74A1,1,0,0,0,24,11.84a10.55,10.55,0,0,1,.61,1.46,1,1,0,0,0,.95.7H28a1,1,0,0,1,1,1Z" />
                        <path
                            d="M16,8.5A7.5,7.5,0,1,0,23.5,16,7.5,7.5,0,0,0,16,8.5Zm0,13A5.5,5.5,0,1,1,21.5,16,5.51,5.51,0,0,1,16,21.5Z" />
                        <path
                            d="M17.29,14l-1.78,1.79-.8-.81a1,1,0,0,0-1.42,0,1,1,0,0,0,0,1.41L14.8,18a1.05,1.05,0,0,0,.71.29,1,1,0,0,0,.71-.29l2.49-2.5A1,1,0,0,0,17.29,14Z" />
                    </svg><br><span>Настройки</span></label></li>
        </ul>

        <div class="slider">
            <div class="indicator"></div>
        </div>
        <div class="content">
            <section>
                <h2>Логи и ошибки</h2>Здесь будут отчеты по работе системы
            </section>
            <section id="object">
               
            </section>
            <section>
                <h2>Отчеты и статистика </h2>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam nemo ducimus eius, magnam error quisquam
                sunt voluptate labore, excepturi numquam! Alias libero optio sed harum debitis! Veniam, quia in eum.
            </section>
            <section>
                <h2>Настройки</h2>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa
                dicta vero rerum? Eaque repudiandae
                architecto libero reprehenderit aliquam magnam ratione quidem? Nobis doloribus molestiae enim deserunt
                necessitatibus eaque quidem incidunt.<br>
            </section>
        </div>
    </div>
    <dialog class="confirm">
    <div class='content_dialog'>
      <section class="top-nav" id="menu_madal">
        <div class='title_dialog' id="title_dialog"></div>
        <!-- <input id="menu_dialog-modal" type="checkbox" /> -->
        <label class='menu_dialog-button-container' for="menu_dialog-modal">
          <div class='menu_dialog-button'></div>
        </label>

      </section>
      <div class='body_dialog ' id="body_dialog"></div>
    </div><div class="modal_but">
    <button id='close' data-type='close'>Закрыть</button>
    <button id='save' data-type='confirm' disabled>Сохранить </button></div>
  </dialog>
    <script type="module" src="./js/setting.js"></script>
</body>

</html>