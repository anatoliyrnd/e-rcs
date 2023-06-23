<?php
namespace includes;
use database\PDODB;
class mainConfig {
    private $db_user='root';
    private $db_password='root';
    private $DBPort='3306';
    private $db_name='tm';
    private $db_host='localhost';
    public $DB;
    protected $user_id;
    protected $user_name;
    protected $user_level;
    protected $user_nacl;
    protected $save_data_call;
    protected $call_id;
    protected $repair_time_unix;
    protected $telegram_token;
    protected $staff_id;

    protected array $key_staff_status_type = [" онлайн", " по телефону", " в телеграм"];
protected  $REPAIR_TIME=array(
0=>"не указан",
1=> "30 мин",
2=> "Сегодня",
3=> "Завтра",
4=> "Три дня",
5=> "7 дней",
6=> "10 дней",
7=> "15 дней",
8=> "1 месяц",
9=> "3 месяца",
);
protected $REPAIR_TIME_ARR;
    protected logSave $logSave;
    protected echoJson $echoJson;

    public function __construct($telegram_token=false)
    {
       $this->telegram_token=$telegram_token;
        $this->session();
        $this->check_session();
        $this->REPAIR_TIME_ARR== array(
            0,
            strtotime("+30 minutes"),
            strtotime(date("Y-m-d 23:59:59 ")),
            strtotime(date("Y-m-d 23:59:59 ")."+1 day"),
            strtotime(date("Y-m-d 23:59:59 ")."+3 day"),
            strtotime(date("Y-m-d 23:59:59 ")."+7 day"),
            strtotime(date("Y-m-d 23:59:59 ")."+10 day"),
            strtotime(date("Y-m-d 23:59:59 ")."+15 day"),
            strtotime(date("Y-m-d 23:59:59 ")."+1 month"),
            strtotime(date("Y-m-d 23:59:59 ")."+3 month")
        );
        $this->DB = new PDODB($this->db_host, $this->DBPort, $this->db_name, $this->db_user, $this->db_password);
        $path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0750, true);
        }
       // $this->telegram = new telegram_class($telegram_token, $PDO);
        $this->logSave = new logSave($path);
        $this->echoJson = new echoJson();
    }
    public function echoJSON($data)
    {
        $this->echoJson->echoJSON($data);
    }
    public function logSave($text, $type = "defaultlog")
    {
        $this->logSave->logSave($text, $type);
    }

    /**
     * @return PDODB
     */
    public function getDB()
    {
        return $this->DB;
    }

    /**
     * @param mixed $telegram_token
     */


    public function nacl($user_id=0)
    {
        ($user_id==0)?:$user_id=$this->user_id;
        $query = "select last_login from lift_users where user_id = :user_id;";
        $user_hash = $this->DB->single($query, array("user_id" => $user_id));
        $nacl = md5(AUTH_KEY . $user_hash);
        return $nacl;
    }
    public function getUserId()
    {
        return $this->user_id;
    }


    public function magicLower($text)
    {
        //проверка на капслок
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
    protected function session()
    {
        header("Content-type: text/html; charset=utf-8");
        session_start();
        if (isset($_GET['e'])) {
            $logout = trim($_GET['e']);
            if ($logout == "3") {
                session_destroy();
                header("Location: " . dirname(dirname($_SERVER['REQUEST_URI'])) . "../index.php?loggedout=yes");
                exit;
            }
        }
    }
    protected function check_session()
    {
        $this->session();
        if (isset($_SESSION['user_id'])) {
            $this->user_name = $_SESSION['user_name'];
            $this->user_id = (int)$_SESSION['user_id'];
            $this->user_level = $_SESSION['user_level'];
            $this->user_nacl = $_SESSION['user_nacl'];
        } else {
            $href = "../../../index.php";
            header('Location: ' . $href);
            exit;
        }
    }
    public function checkStrLen($str)
    {
        return iconv_strlen($str) < 5;
    }

    /**
     * @return mixed
     */
    public function getRepairTimeUnix()
    {
        return $this->repair_time_unix;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    public function setTelegramToken($telegram_token)
    {
        $this->telegram_token = $telegram_token;
    }
    /**
     * @return mixed
     */
    public function getTelegramToken()
    {
        return $this->telegram_token;
    }
    public function getUserName()
    {
        return $this->user_name;
    }

    public function getUserLevel()
    {
        return $this->user_level;
    }
    public function getUserNacl()
    {
        return $this->user_nacl;
    }

    /**
     * @return array
     */
    public function getKeyStaffStatusType(): array
    {
        return $this->key_staff_status_type;
    }
}