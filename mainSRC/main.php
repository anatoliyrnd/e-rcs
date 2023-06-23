<?php

namespace mainSRC;

use mainSRC\dataBase\PDODB;

class main
{
    /**
     * @var PDODB
     */
    protected PDODB $DB;
    private $debug_path;
    private $user_id;
    private $user_name;
    private $user_level;
    private $user_nacl;
    protected array $key_staff_status_type = [" онлайн", " по телефону", " в телеграм"];
    protected $logSave;

    public function __construct()
    {
        $this->DB = PDODB::getInstance();
        $this->debug_path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR;
        $this->logSave = new logSave();
    }

    protected function errorCallId()
    {
        $this->logSave('не передан id заявки', 'checkCallId', log_path);
        $this->echoJSON(array('status' => 'error', 'message' => 'Не передан ID заявки'));
    }

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

    public function echoJSON($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }

    public function nacl($user_id): string
    {
        $query = "select last_login from lift_users where user_id = :user_id LIMIT 1";
        $user_hash = $this->DB->single($query, array("user_id" => $user_id));
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

    public function checkSession()
    {
        $this->session();
        $this->session_read();
    }

    /**
     * @param $text
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

    public function checkUser($id = null, $nacl = null)
    {
        $id = $id ?? $this->user_id;
        $nacl = $nacl ?? $this->user_nacl;
        return (($this->nacl($id) === $nacl));
    }

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
        //если админ или пользователю разрешено редктирование  объектов
        if ($userdata['user_localadmin'] || $userdata['user_edit_obj']) {
            $edit_obj_link = true;
        }
//если админ или пользователю разрешено Управление пользователями
        if ($userdata['user_localadmin'] || $userdata['user_edit_user']) {
            $edit_user_link = true;
        }
//если диспетчер  или пользователю разрешено редктирование заявок
        if ($userdata['user_disppermission'] || ($userdata['user_level'] == 3)) {
            $edit_call = true;//1
            $close_call = true;//2
        }
        // диспетчер  или пользователю разрешено редктирование заявок
        if ($userdata['user_disppermission'] || $userdata['user_localadmin'] || ($userdata['user_level'] == 3)) {
            $read_all_calls = true;//0
        }
//если диспетчер  или пользователю разрешено создание заявок
        if ($userdata['user_add_call'] || ($userdata['user_level'] == 3)) {
            $add_call_permission = true;
        }
        return [$this->user_id, $read_all_calls, $edit_call, $close_call, $note_call, $add_call_permission, $edit_user_link, $edit_obj_link];
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param $index // порядковый номер метки времени для срока ремонта
     * @return false|int возвращет время срока ремонта по порядковому номеру
     */
    public function repairTimeUnix($index)
    {
        $arrTime = json_decode($this->DB->single("SELECT option_value FROM lift_options WHERE option_name='repair_time'"), true);
        if (empty($arrTime)) {
            $this->logSave("error read repair time $index", 'main', "main");
            return false;
        }
        return strtotime(array_keys($arrTime)[$index]);

    }


    /**
     * @param $index
     * @return mixed времени ремонта по его индексу
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

    public function getMinValueIfShorter($str)
    {
        $min_length = $this->DB->single("SELECT option_value FROM lift_options WHERE option_name='min_length_text'");
        if (iconv_strlen($str) < $min_length) {
            return $min_length;
        } else {
            return false;
        }

    }
    public function getHostURL(){
        $protocol = (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])?"https://":"http://");
        return $protocol. $_SERVER["SERVER_NAME"];

    }
}