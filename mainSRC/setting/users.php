<?php
namespace mainSRC\setting;
use mainSRC\dataBase\PDODB;
use mainSRC\logSave;
use mainSRC\main;
use mainSRC\PasswordHash;

class users{
    protected $DB;
    /**
     * @var array
     */
    private $newUser=false;
    protected $error=[];
    protected $query;
    protected $data=[];
    protected $pwdHash;
    protected $nameLength=array("user_login"=>"логина", "user_password"=>"пароля","user_name"=>"имени","user_phone"=>"телефона",);
protected $result_message=[];
protected $booleanData=["user_hiden",'user_localadmin','user_edit_obj','user_edit_user','user_disppermission','user_add_call','user_read_all_calls','user_block'];
//protected $userRow=["user_login",  "user_password",  "user_name",  "user_phone",  "user_telegram",  "user_hiden",  "user_localadmin",  "user_edit_obj",  "user_edit_user",  "user_disppermission",  "user_add_call",  "user_read_all_calls",  "user_block",  "user_level",  "user_address",  "user_city",  "user_state",  "user_zip",  "user_country",  "user_email"];
    public function __construct()
    {
        $this->pwdHash=new PasswordHash(8, false);
        $this->log_save=new logSave();
        $this->DB=PDODB::getInstance();
    }

    /**
     * @param $str
     * @param $txt
     * @return bool
     */
    public function checkLength ($str, $txt){
          if (mb_strlen($str)<=5){
           $this->error[]="Длина $txt слишком короткая";
           return false;
       } else{
           return true;
       }
    }
    protected function userControl($key){

        $result=false;
$this->data[$key]?$result=$this->checkLength($this->data[$key],$this->nameLength[$key]):null;

return $result;
    }

    /**
     * @return void
     */
    public function boolean()
    {
        foreach ( $this->booleanData as $value) {
            $this->data[$value]=(int)$this->data[$value];
       }
    }
    public function editUser($data){
        if((int)$data['id']===null || (int)$data['id']<=0){
            $this->error[]="Ошибка данных. Не верный идентификатор";

        }
        $this->data=$data;
        $this->newUser=false;
        $this->saveUser();
    }
    protected function saveUser(){
        $this->data['user_login']= preg_replace('/[^ a-z\d]/ui', '',$this->data['user_login'] );
        $message[]="Информация";
        $check_key_array=array_keys($this->nameLength);
        $this->boolean();// приведем логические значение в числовые для записи в базу данных
        $save_data=array();//новые данные для записи
        $select=implode(", ",$check_key_array).",".implode(", ",$this->booleanData);
        $query_old_data="SELECT $select FROM lift_users WHERE user_id=:id"; //запрос на получение старых данных из базы по пользователю
       if($this->newUser){
          $query_check_login="SELECT user_id FROM lift_users WHERE user_login=:ul LIMIT 1";
          $data_query_check_login=array("ul"=> $this->data['user_login']);
       }else {
           $check_user_protected=$this->DB->single("SELECT user_protect_edit FROM lift_users WHERE  user_id=:id LIMIT 1",array("id"=>$this->data['id']));
           if( $check_user_protected)$this->error[]="Пользователь защещен от изменения";
           $query_check_login="SELECT user_id FROM lift_users WHERE user_login=:ul AND user_id!=:id LIMIT 1";
           $data_query_check_login=array("ul"=> $this->data['user_login'],"id"=>$this->data['id']);
           $oldValue = $this->DB->row($query_old_data, array("id" => $this->data['id']));// если редактируем пользователя то получим его предидущие значения

       }
        $check_login=$this->DB->single($query_check_login,$data_query_check_login);
        if($check_login)$this->error[]="Пользователь с логином ".$this->data['user_login']." сущетвует";
        foreach ( $check_key_array as $type_key) {
            if($this->userControl( $type_key) &&( $this->newUser || $oldValue[$type_key]!==$this->data[$type_key]  )) {
                $this->result_message[]="изменение ".$this->nameLength[$type_key]." успешно , новое значение ".$this->data[$type_key] ;
                $type_key=="user_password"? $save_data[$type_key] = $this->pwdHash->HashPassword($this->data[$type_key]):$save_data[$type_key] = $this->data[$type_key];
            }
        }
        $key_result_message=array_key_last( $this->result_message)+1;
        foreach ( $this->booleanData as $type_key) {
            if(isset($this->data[ $type_key]) &&( $this->newUser || $oldValue[$type_key]!==$this->data[$type_key]  )) {
                $this->result_message[ $key_result_message]="Права пользователя были изменены";
                       $save_data[ $type_key]=$this->data[ $type_key];
            }

        }
        if (count($this->result_message)>1 && $this->newUser) {
            $this->result_message = [];
            $this->result_message[]="Пользователь ". $save_data['user_name']." Успешно добавлен";
        }

if(count($this->error)) {
    $status = "error";
    $message=$this->error;
}else {
    if(count($this->result_message)) {

        if ($this->newUser) {
            $SQLResult = $this->DB->insert("lift_users", $save_data);
        } else {
            //$save_data['id'] = (int)$this->data['id'];
            $SQLResult = $this->DB->update("lift_users", $save_data, array("user_id" => $this->data['id']));
        }
        if ($SQLResult){//
            $status = "ok";
            $message=$this->result_message;
        }else{
            $status = "error";
            $message[]="Ошибк записи данных в базу";
        }
    }else{
       $status="ok";
       $message[]="Переданы предидущие данные!";
    }


      //$message=$this->result_message;
}
//$this->echoJson($this->data);
        $result=implode("<br>",$message);


    $this->echoJson(array("status"=>$status,"message"=>$result));


    }

    public function addUser($data){
if($data['user_password'] === null || trim($data['user_password']) === ''){
    $this->error[]="Не указанн пароль";
}
if ($data['user_login'] === null || trim($data['user_login']) === ''){
    $this->error[]="Не указанн логин";
    }
if ((int)$data['user_login'] === null || (int)trim($data['user_login']) === 0){
    $this->error[]="Не указанн телефон";
}
        $this->data=$data;
$this->newUser=true;
$this->saveUser();

    }
    private function query(){

    }
    public function echoJson($data){
        header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }
    public function checkpwd($password,$user_login,$checkusing) {

        $hasher = password_hash($password, PASSWORD_DEFAULT);
        $stored_hash = "*";

        //if encryption is ON
        $query="SELECT user_password from lift_users WHERE $checkusing = :name LIMIT 1;";
        $stored_hash = $this->DB->single($query,array('name'=>$user_login));

        if(OLDHASH){
            include("includes/PasswordHash.php");

            return $this->pwdHash->CheckPassword($password, $stored_hash);
        }

        if ($hasher===$stored_hash) {
            return TRUE;
        }else{
            return FALSE;
        }

    }

}
