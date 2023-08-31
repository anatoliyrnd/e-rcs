<?php

namespace mainSRC;

use mainSRC\dataBase\PDODB;

class main
{
    /**
     * @var PDODB
     */
    const OLDHASH=true;
    public PDODB $DB;
    private $debug_path;
    private $user_id;
    private $user_name;
    private $user_level;
    private $user_nacl;
    protected array $key_staff_status_type = [" онлайн", " по телефону", " в телеграм"];
    protected $logSave;
    protected array $repair_time_name=["не указан","30 мин","Сегодня","Завтра","Три дня","7 дней","10 дней","15 дней","1 месяц","3 месяца"];
  protected array $repair_time=[ "0","+30 minutes" ,"today 23:00","today +1day 23:00","today +3day 23:00","today+7day","today+10day","today+15day", "today+1month","today+3month"];
protected $repair_time_index;

    public function __construct()
    {
        $this->DB = PDODB::getInstance();
        $this->debug_path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR;
        $this->logSave = new logSave();
    }

    /**
     * @return string
     */
    public function KeyStaffStatusType($id=0): string
    {
        if(!array_key_exists($id,$this->key_staff_status_type))return 'не известно';
        return $this->key_staff_status_type[$id];
    }

    protected function errorCallId()
    {
        $this->logSave('не передан id заявки', 'checkCallId', log_path);
        $this->echoJSON(array('status' => 'error', 'message' => 'Не передан ID заявки'));
    }

    /**
     * @description запись логов
     * @param $text string  текст
     * @param $type string of log (name file)
     * @param $path string (папка с логами)
     * @return void
     */
    public function logSave($text, $type = "default_log", $path = null)
    {
        $this->logSave->logSave($text, $type, $path);
    }

    public function debug($text, $type = "default_debug", $path = null)
    {
        $path_debug = $this->debug_path . $path;
        if (!is_dir($path_debug)) {
            mkdir($path_debug, 0750, true);
        }
        $path_debug .= DIRECTORY_SEPARATOR;
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        $file = date('Y-m-d') . $type;
        file_put_contents($path_debug . $file . ".txt", $text . PHP_EOL, FILE_APPEND);
    }

    /**
     * @description Вывод массива в JSON формате
     * @param $data array
     * @return void
     */
    public function echoJSON($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * @description поглучение уникального авторизационного ключа пользователя
     * @param $user_id int
     * @return string
     */
    public function nacl($user_id_in='0')
    {
        $user_id=$user_id_in??$this->user_id;
        $query = "SELECT last_login FROM lift_users WHERE user_id = :user_id LIMIT 1";
        $user_hash =$this->DB->single($query,array("user_id" => $user_id));
        $auth_key=$this->DB->single("SELECT option_value FROM lift_options WHERE option_name='authorizationKey' LIMIT 1");
        return md5($auth_key . $user_hash);
    }
    private function session()
    {
        header("Content-type: text/html; charset=utf-8");
        //'cookie_lifetime' => 86400,
        session_start([
            'read_and_close' => true,
        ]);
        if (isset($_GET['e'])) {
            $logout = trim($_GET['e']);
            if ($logout == "3") {
                session_destroy();
                header("Location: " . dirname($_SERVER['REQUEST_URI'], 2) . "../index.php?loggedout=yes");
                exit;
            }
        }
    }

    private function session_read()
    {
        if (isset($_SESSION['user_id'])) {
            $this->user_name = $_SESSION['user_name'];
            $this->user_id = (int)$_SESSION['user_id'];
            $this->user_level = $_SESSION['user_level'];
            $this->user_nacl = $_SESSION['user_nacl'];
        } else {
            header("Location: " . dirname($_SERVER['REQUEST_URI'], 2) . "../index.php");
            exit;
        }
    }

    /**
     * проверка сесии пользователя
     * @return void
     */
    public function checkSession()
    {
        $this->session();
        $this->session_read();
    }

    /**
     * @param $text string
     * @return array|mixed|string|string[]|null
     * @description возвращает отформатированный текст если при вводе текста был включен капслок
     */
    public function magicLower($text)
    {

        $txtL = mb_strlen(preg_replace("/[^а-яёa-z]+/u", "", $text));
        $txtU = mb_strlen(preg_replace("/[^А-ЯЁA-Z]+/u", "", $text));
        if (!$txtL) $txtL = 0.01;
        //print "LowerCase: $txtL,  Upper case: $txtU  rate:".($txtU/$txtL)."\n";
        if ($txtU / $txtL < 0.1) return $text;
        return preg_replace_callback("/(?<=[A-ZА-ЯЁ])([A-ZА-ЯЁ\s]+)/u",
            function ($match) {
                return mb_convert_case($match[0], MB_CASE_LOWER, "UTF-8");
            }, $text);
    }

    /**
     * @description проверка пользователя
     * @param $id int если не указать будет получен из массвиа Сессии
     * @param $nacl string если не указать будет получен из массвиа Сессии
     * @return bool если ошибка проверки то FALSE
     */
    public function checkUser($id = null, $nacl = null)
    {
        $id = $id ?? $this->user_id;
        $nacl = $nacl ?? $this->user_nacl;
        return (($this->nacl($id) === $nacl));
    }

    /**
     * @description  возвращает массив уровней доступа пользоватиеля
     * @param $user_id int если не указать будет получен из массива сессии
     * @return array
     */
    public function getUserPermission($user_id = NULL)
    {
        /*
        * 1-read all calls
        * 2-edit_call
        * 3-close_call
        * 4-note_call
        * 5-add_call_permission
        * 6-edit_user_link
        * 7-edit_obj_link
         * 8 админ
        */
        $user_id = $user_id ?? $this->user_id;
        $query = "SELECT `user_add_call`, `user_localadmin`,`user_edit_obj`, `user_read_all_calls`, `user_edit_user`, `user_level`, `user_disppermission` FROM `lift_users` WHERE `user_id`=:userId LIMIT 1";

        $userdata = $this->DB->row($query, array("userId" => $user_id));

        $read_all_calls = false; //1 разрешено чтение всех заявок
        $edit_call = false;//2
        $close_call = false;//3
        $note_call = true;//4
        $add_call_permission = false;//5
        $edit_user_link = false;//6
        $edit_obj_link = false;//7
        $user_local_admin=(bool)$userdata['user_localadmin'];//8 права администратора

        //если админ или пользователю разрешено редктирование  объектов
        if ($user_local_admin || $userdata['user_edit_obj']) {
            $edit_obj_link = true;
        }
//если админ или пользователю разрешено Управление пользователями
        if ($user_local_admin || $userdata['user_edit_user']) {
            $edit_user_link = true;
        }
//если диспетчер  или пользователю разрешено редктирование заявок
        if ($userdata['user_disppermission'] || ($userdata['user_level'] == 3)) {
            $edit_call = true;//1
            $close_call = true;//2
        }
        // диспетчер  или пользователю разрешено чтение всех заявок
        if ($userdata['user_disppermission'] || $user_local_admin || ($userdata['user_level'] == 3)) {
            $read_all_calls = true;//0
        }
//если диспетчер  или пользователю разрешено создание заявок
        if ($userdata['user_add_call'] || ($userdata['user_level'] == 3)) {
            $add_call_permission = true;
        }
        return [$this->user_id, $read_all_calls, $edit_call, $close_call, $note_call, $add_call_permission, $edit_user_link, $edit_obj_link,$user_local_admin];
    }

    /**
     * @description  получить id пользователя из текущей сессии
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @description  получить имя пользователя из текущей сессии
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     *  @return int возвращет метку времени  срока ремонта по порядковому номеру
     */
    public function repairTimeUnix($i=null)
    {
         $index=$i??$this->repair_time_index;
         return strtotime($this->repair_time[$index]);

    }


    /**
     * @param $index
     * @return string времени ремонта по его индексу
     */
    public function repairTime($index)
    {
        $arrTime = json_decode($this->DB->single("SELECT option_value FROM lift_options WHERE option_name='repair_time'"), true);
        if (empty($arrTime)) {
            $this->logSave("error read repair time $index", 'main', "main");
            return false;
        }

        return array_values($arrTime)[$index];

    }

    /**
     * @description  проверка минимальной длины строки
     * @param $str string
     * @return false|int если меньше заданной то возвращает минимальную длину, если больше то FALSE
     */
    public function getMinValueIfShorter($str)
    {
        $min_length = $this->DB->single("SELECT option_value FROM lift_options WHERE option_name='min_length_text'");
        if (iconv_strlen($str) < $min_length) {
            return $min_length;
        } else {
            return false;
        }

    }

    /**
     * @description  возвращает полный урл с учетом типа протокола
     * @return string
     */
    public function getHostURL(){
        $protocol = (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])?"https://":"http://");
        return $protocol. $_SERVER["SERVER_NAME"];

    }
    public function replaceArrayKey($array, $oldKey, $newKey){
        //If the old key doesn't exist, we can't replace it...
        if(!isset($array[$oldKey])){
            return $array;
        }
        //Get a list of all keys in the array.
        $arrayKeys = array_keys($array);
        //Replace the key in our $arrayKeys array.
        $oldKeyIndex = array_search($oldKey, $arrayKeys);
        $arrayKeys[$oldKeyIndex] = $newKey;
        //Combine them back into one array.
       return   array_combine($arrayKeys, $array);

    }

}