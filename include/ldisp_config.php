<?php
$protocol = (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])?"https://":"http://");
$HOSTURL= $protocol. $_SERVER["SERVER_NAME"];

define ('ENCRYPTED_PASSWORD', true);// хранить пароль в зашифровоном виде
define ('OLDINTERFACE',false);//работа со старым интерфейсом
define ('OLDHASH',true);//старая версия хэш пароля
define ('CITY_SEP','#');//азделитель названия города в данных из SPult
define ('CITY_DEFAULT','г.Ростов-на-Дону');// название города по умолчанию, если не получен разделитель из данных XML при отправки заявки из SPult
define ('XML_ADD',true);//Разрешить вносить адреса полученные при ормировании заявки SPult в базу объектов
define ('DISP_ADD_OBJECT',1);//1 - разрешить диспетчеру добавлять оъекты (улицы,дома,лифты) 0-запретить (до версии 2)
define ('TOKEN_TELEGRAM','5305000587:AAGY-B9CzWJVgOLR96Ek1XYqRaqU0sW9vS4');// токен апи телеграм бота

define ('DEMO',false); // замените true на false для рабочего ваианта
/* The base configurations of Free Help Desk.
/** The name of the database - create this first*/
/** MySQL database username */
define('db_user', 'root');

/** MySQL database password */
define('db_password', 'root');
define('DBPort',"3306");
define('db_name', 'tm');
define('db_host','localhost');
define('db_PDO','mysql:host=localhost;dbname=tm');

/** adjust the time display in hours */
define('FHD_TIMEADJUST', '+3');

/** ключ аторизации для большей секретности.*/
define('AUTH_KEY','change this key');

/** Максимальное количество раз ошибки авторизации(session only)*/
define('LOGIN_TRIES',100);

/** email address to send new ticket and registration notices FROM, etc  */
define('FROM_EMAIL','zamotaev@list.ru');

/** email address to send new ticket and registration notices TO, etc  */
define('TO_EMAIL','zamotaev@list.ru');

/** Allow registrations yes or no */
define('ALLOW_REGISTER','no');

/** Use CAPTCHA with registration? yes or no */
define('CAPTCHA_REGISTER','yes');

/** Use CAPTCHA with the forgot password form? yes or no */
define('CAPTCHA_RESET_PASSWORD','yes');

/** All registrations need to be approved by admin yes or no */
define('REGISTER_APPROVAL','yes');

/** Allow unregistered users to submit requests yes/no  */
define('ALLOW_ANY_ADD','no');

/** Enter the organization title **/
define('FHD_TITLE', "Название компании");

/** Allow Uploads ** yes or no */
define('FHD_UPLOAD_ALLOW', "no");
define('UPLOAD_KEY','change this key)');
//SET WHAT FILE EXTENSIONS ARE ALLOWED TO BE UPLOADED (comma seperated list "txt","pdf")
$allowedExts = array("jpg","jpeg","gif","png","doc","docx","wpd","xls","xlsx","pdf","txt","pps","pptx","pub");
