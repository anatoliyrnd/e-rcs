<?php
namespace mainSRC\calls;
use mainSRC\main;
const name_file_close="close_call";
class closeCall extends main{
    protected $close_all ;// разрешено закрывать любые заявки
    protected $closure;
    protected $call_id;
    protected $staff_id;
    public function __construct()
    {
        parent::__construct();
        $this->checkSession();
        $this->close_all=$this->getUserPermission()[2];
        $this->closure=new closureCall();

 }

    public function callClose($data)
    {

        empty($data['call_id'])?$this->errorCallId():$this->call_id=(int)$data['call_id'];
        $this->closure->setCallId($this->call_id);

        if( $this->closure->checkCallClosed())//проверим не закрыта ли заявка
        {
          $close_info=$this->closure->callClosedInfo();
            $response['status']  = 'error';
            $response['message'] = $close_info;
            $this->echoJSON($response);
        }

       if ( !$this->close_all){// если пользователю не разрешено закрытие любой заявки проверим является ли он ответсвеннным по ней
          $staff_id=(int)$this->DB->single("SELECT call_staff FROM lift_calls WHERE call_id=:id LIMIT 1",array("id"=>$this->call_id));
          if ($staff_id!==$this->getUserId()){
              $this->logSave->logSave("Ошибка прав при закрытие заявки staff=$staff_id, user-".$this->getUserId()." call - $this->call_id",name_file_close,log_path);
              $response['status']  = 'error';
              $response['message'] = "Ошибка доступа. Недостаточно прав для данного действия";
              $this->echoJSON($response);
          }
       }
            if (isset($data['call_close'])) {
            $solution = $this->magicLower($data['call_close']);//исправим в случае набора текста при включенном капслок
            $this->closure->setSolution($solution);
        } else {
            $response['status']  = 'error';
            $response['message'] = "Не передано решение по заявке";
            $this->echojson($response);
        }
        $this->closure->setClosedUserName($this->getUserName());
            $this->closure->setApprovalClosure(true);
   $result_closure=$this->closure->closureCall();
        if ($result_closure) {
            $text="Заявка закрыта ";
           $result_archive_history=$this->closure->addHistoryArchive();
           $result_archive_note=$this->closure->addNoteArchive();
            $result_archive_history? $history=" История перенесена в архив ": $history=" , но произошла ошибка при переносе в архив истории ";
          $result_archive_note?$note=" Заметки перенесены в архив":$note=", но произошла ошибка при переносе в архив заметок";
          $text.=$history.$note;
                $response['status']  = 'ok';
                $response['message'] = $text;
                $this->echojson($response);
            }
        else {
            $log = " SC-1 call close $result_closure ";
            $this->logSave->logsave($log, name_file_close,log_path);
            $response['status']  = 'error';
            $response['message'] = "При записи в базу данных при закрытие заявки произошла ошибка!";
           $this-> echojson($response);
        }

    }

 }
