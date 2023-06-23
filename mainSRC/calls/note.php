<?php
namespace mainSRC\calls;
use mainSRC\main;
class note extends main{

public function __construct()
{
    parent::__construct();
    $this->checkSession();
}
public function addNote($data)
{
    $call_id=0;
    empty($data['call_id']) ? $this->errorCallId() : $call_id = (int)$data['call_id'];
    $note_body = $data['note'] ?? ' ';
    $note_body = $this->magicLower($note_body);
    $length = $this->getMinValueIfShorter($note_body);
    if (!empty($length)) $this->echoJSON(array("status" => "error", "message" => "Длина заметки короче $length символов"));
    $note_post_date = strtotime(date('Y-m-d H:i:s '));
    $note_add_query = "INSERT INTO lift_notes SET note_type=1,note_title='Заметка',note_body=:note_body, note_relation=:call_id, note_post_date=$note_post_date, note_post_ip=:IP,note_post_user=:user_id ";
    $result = $this->DB->query($note_add_query, array('note_body' => $note_body, 'call_id' => $call_id, 'user_id' => $this->getUserId(), 'IP' => $_SERVER['REMOTE_ADDR']));
    if ($result) {
        $this->echoJSON(array('status' => 'ok', 'message' => 'Заметка добавена'));
    } else {
        $this->logSave("error sql add note $note_add_query -> $result ->callId=$call_id",'noteAdd','calls');
        $this->echoJSON(array('status' => 'error', 'message' => 'Произошла ошибка базы'));
    }
}

}