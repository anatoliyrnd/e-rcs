<?php
function echojson($data)
{
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}
function logsave($text, $type="defaultlog"){
$text=date('Y-m-d H:m:s')." - ". $_SERVER['REMOTE_ADDR']." - ".$text;
$file = date('Y-m-d').$type;
file_put_contents("logs/". $file.".txt", $text . PHP_EOL, FILE_APPEND);
}

//v.2.0.1
class DB
{/* получение одной записи из базы
  $item = DB::getRow("SELECT * FROM `category` WHERE `id` = ?", 1);
// Или
$item = DB::getRow("SELECT * FROM `category` WHERE `id` = :id", array('id' => 1));
Получение нескольких записей из БД
$items = DB::getAll("SELECT * FROM `category` WHERE `id` > 2")
Получения значения
$value = DB::getValue("SELECT `name` FROM `category` WHERE `id` = 2");

Получения значений колонки
$values = DB::getColumn("SELECT `name` FROM `category`");

Добавление в БД
Метод возвращает ID вставленной записи.

$insert_id = DB::add("INSERT INTO `category` SET `name` = ?", 'Яблоки');

се остальные запросы
Выполняет запросы в БД, такие как DELETE, UPDATE, CREATE TABLE и т.д. В случаи успеха возвращает true.

DB::set("DELETE FROM `category` WHERE `id` > ? AND `parent` > ?", array(123, 0));

установк имни базы
DB::$dsn = 'mysql:dbname=newtable;host=127.0.0.1';
  */

	public static $dsn = 'mysql:dbname=table;host=localhost';
	public static $user = 'root';
	public static $pass = 'password';
 
	/**
	 * Объект PDO.
	 */
	public static $dbh = null;
 
	/**
	 * Statement Handle.
	 */
	public static $sth = null;
 
	/**
	 * Выполняемый SQL запрос.
	 */
	public static $query = '';
 
	/**
	 * Подключение к БД.
	 */
	public static function getDbh()
	{	
		if (!self::$dbh) {
			try {
				self::$dbh = new PDO(
					self::$dsn, 
					self::$user, 
					self::$pass, 
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
				);
				self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			} catch (PDOException $e) {
        $new_str = 'Error connecting to database: ' . $e->getMessage()." - ".date("Y-m-d H:i:s ")." user id=";
        $url=$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  $filename =".../../logs/pdo_error_".date('m_y').".txt";
 
  file_put_contents($filename, $new_str . PHP_EOL,FILE_APPEND);
  
				exit('Error connecting to database: ' . $e->getMessage());
			}
		}
 
		return self::$dbh; 
	}
	
	/**
	 * Закрытие соединения.
	 */
	public static function destroy()
	{	
		self::$dbh = null;
		return self::$dbh; 
	}
 
	/**
	 * Получение ошибки запроса.
	 */
	public static function getError()
	{
		$info = self::$sth->errorInfo();
		return (isset($info[2])) ? 'SQL: ' . $info[2] : null;
	}
 
	/**
	 * Возвращает структуру таблицы в виде ассоциативного массива.
	 */
	public static function getStructure($table)
	{
		$res = array();
		foreach (self::getAll("SHOW COLUMNS FROM {$table}") as $row) {
			$res[$row['Field']] = (is_null($row['Default'])) ? '' : $row['Default'];
		}
 
		return $res;
	}
 
	/**
	 * Добавление в таблицу, в случаи успеха вернет вставленный ID, иначе 0.
	 */
	public static function add($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		return (self::$sth->execute((array) $param)) ? self::getDbh()->lastInsertId() : 0;
	}
	
	/**
	 * Выполнение запроса.
	 */
	public static function set($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		return self::$sth->execute((array) $param);
	}
	
	/**
	 * Получение строки из таблицы.
	 */
	public static function getRow($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetch(PDO::FETCH_ASSOC);		
	}
	
	/**
	 * Получение всех строк из таблицы.
	 */
	public static function getAll($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetchAll(PDO::FETCH_ASSOC);	
	}
	
	/**
	 * Получение значения.
	 */
	public static function getValue($query, $param = array(), $default = null)
	{
		$result = self::getRow($query, $param);
		if (!empty($result)) {
			$result = array_shift($result);
		}
 
		return (empty($result)) ? $default : $result;	
	}
	
	/**
	 * Получение столбца таблицы.
	 */
	public static function getColumn($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetchAll(PDO::FETCH_COLUMN);	
	}
}

function staff_call_new($userid, $link) {
  DB::$dsn = db_PDO;
  DB::$user = db_user;
  DB::$pass = db_password;
//проверим есть ли у пользователя телеграм 
$queryuser="SELECT user_telegram FROM lift_users WHERE user_id =:ID LIMIT 1";//получим имя ответсвенного по его id
$user_telegram =DB::getValue($queryuser, array('ID' => $userid));
//$user_telegram = $sths->fetchColumn();
$filename ="../logs/telega".date('m_y').".txt";
file_put_contents($filename, $user_telegram . PHP_EOL, FILE_APPEND);
 
if (!$user_telegram){
  //запишим логи

  $new_str = "not telegam id ".date("Y-m-d H:i:s ")." user id=".$userid;
  

  file_put_contents($filename, $new_str . PHP_EOL, FILE_APPEND);
  return 0;}
  $adjdate=date("d-m-Y H:i ");
$text_telega=$adjdate." -Вы назначены ответсвенным по заявке!";
$but=array(
  'inline_keyboard' => array(
    array(
          array(
          'text' => 'Подробнее!',
          'url' =>$link,
        ),
    ),
     
  ),
);
$replay=json_encode($but);
//запишим в логи
  message_to_telegram(TOKEN_TELEGRAM,$user_telegram, $text_telega,$replay);
  $new_str = "Уведмление ответсвенного отправлено".date("Y-m-d H:i:s ")."-".$text_telega."-".$replay;
 
 
  file_put_contents($filename, $new_str . PHP_EOL, FILE_APPEND);
  
}
//функция генерации авторизационного токена
function nacl($user_id){
  
    try {
      $dbh = new PDO(db_PDO, db_user, db_password);
      $dbh->exec("set names utf8");
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      die();
    }
    $query="select last_login from lift_users where user_id = :user_id;";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_hash = $stmt->fetchColumn();
    $stmt=null;
    $dbh=null;
    $nacl= md5(AUTH_KEY.$user_hash);
    return $nacl;
}
//проверка пароля
function checkpwd($password,$user_login) {
	
	$hasher = password_hash($password, PASSWORD_DEFAULT);
	$stored_hash = "*";
  try{
  $dbh = new PDO(db_PDO, db_user, db_password);
  $dbh->exec("set names utf8");
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage();
  die();
}
	
	if (ENCRYPTED_PASSWORD) {
	//if encryption is ON
  $query="SELECT user_password from lift_users WHERE user_login = :name OR user_email = :name LIMIT 1;";
  $stmt = $dbh->prepare($query);
  $stmt->bindParam(':name', $user_login);
  $stmt->execute();
  $stored_hash = $stmt->fetchColumn();
    
  if(OLDHASH){
    include("includes/PasswordHash.php");
	  $check = new PasswordHash(8, false);
   return $check->CheckPassword($password, $stored_hash);
  }
 
		if ($hasher===$stored_hash) {
			$return_value = TRUE;
		}else{
			$return_value = FALSE;
		}
    $stmt=null;
    $dbh=null;
	//if encryption is OFF
	}else{
		$query="SELECT count(user_id) FROM lift_users WHERE (user_login = :name OR user_email = :name) AND user_password = BINARY :pass AND user_pending = 0 LIMIT 1";
		$stmt = $dbh->prepare($query);
    $stmt->bindParam(':name', $user_login);
    $stmt->bindParam(':pass', $password);
    $stmt->execute();
    $num=$stmt->fetchColumn();
    $stmt=null;
    $dbh=null;
    if ($num == 1) {
			$return_value = TRUE;
		}else{
			$return_value = FALSE;
		}
	}

return $return_value;
}
// функция отправки сообщени в от бота в диалог с юзером
function message_to_telegram($bot_token, $chat_id, $text, $reply_markup = '')
{
  $ch = curl_init();
  $ch_post = [
    CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_POSTFIELDS => [
      'chat_id' => $chat_id,
      'parse_mode' => 'HTML',
      'text' => $text,
      'reply_markup' => $reply_markup,
    ]
  ];

  curl_setopt_array($ch, $ch_post);
  curl_exec($ch);
}


function queryarr($type="0"){
  if($type=="1" OR $type =="2" OR $type=="3")
  {
    //получим типы заявок 1-отдел, 2- уровни заявкиб 3- группа заявки
    $query="select type_id,type_name from lift_types where type=".$type." order by type_name;";
    $id="type_id";
    $name="type_name";
  }else{
    if($type=="4"){
     //получаем список городов
     $query="SELECT city_name, id from lift_city WHERE 1 order by city_name";
     $id="id";
     $name="city_name";
    }else {
      //получаем список сотрудников
    $query="select user_id,user_name from lift_users where user_level<>1 order by user_name;";
    $id="user_id";
    $name="user_name";
    
    }
  }

  try {
    $dbh = new PDO(db_PDO, db_user, db_password);
    $dbh->exec("set names utf8");
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
  }
  $sth = $dbh->query($query);
  //print_r($sth);
$res=array();
  while ($row = $sth->fetch(PDO::FETCH_ASSOC))
  {
  //type_id type_name
  
  $res[$row[$id]]=$row[$name];
  }
 
 
  $sth=null;

  return $res;
}
//функция проверки злоупотреблением capslook
function magicLower($text)
{
 $txtL=mb_strlen(preg_replace("/[^а-яёa-z]+/u","",$text));
 $txtU=mb_strlen(preg_replace("/[^А-ЯЁA-Z]+/u","",$text));
 if(!$txtL) $txtL=0.01;
 //print "LowerCase: $txtL,  Upper case: $txtU  rate:".($txtU/$txtL)."\n";
 if($txtU/$txtL<0.1) return $text;
 return preg_replace_callback("/(?<=[A-ZА-ЯЁ])([A-ZА-ЯЁ\s]+)/u",
          function($match) { return mb_convert_case($match[0], MB_CASE_LOWER, "UTF-8"); },$text);
}
function repairtime($time)
{
  switch ($time) {
     case "0":
     $repair=strtotime("+30 minutes");
          break;
    case "123":
        $repair=strtotime(date("Y-m-d 23:59:59 "));
        break;
    case "1":
        $repair=strtotime(date("Y-m-d 23:59:59 ")."+1 day");
        break;
    case "2":
        $repair=strtotime(date("Y-m-d 23:59:59")."+2 day");;
         break;
    case "3":
        $repair=strtotime(date("Y-m-d 23:59:59 ")."+3 day");
        break;
    case "7":
        $repair=strtotime(date("Y-m-d 23:59:59 ")."+7 day");
        break;
    case "10":
        $repair=strtotime(date("Y-m-d 23:59:59 ")."+10 day");
        break;
    case "30":
        $repair=strtotime(date("Y-m-d 23:59:59 ")."+1 month");
        break;
    default:
         $repair=0;

        break;
}
return $repair;
}
?>
