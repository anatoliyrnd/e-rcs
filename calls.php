<?php
require_once("./include/autoload.php");
$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$action_get=$_REQUEST['data']??null;
$action_body=$input['action']??null;
if ($action_get){
    $period=$_REQUEST['period']??null;// интервал вывода заявок
    $read_calls=new mainSRC\calls\readCalls();
    $calls=$read_calls->getCalls($action_get,$period);
    $calls?$read_calls->echoJSON($calls):$read_calls->echoJSON(array('status'=>'error','message'=>'ошибка чтения данных'));
}
if ($action_body){

    switch ($action_body) {
        case 'callclose':
            $action=new \mainSRC\calls\closeCall();
            $action->callClose($input);
            break;
        case 'calledit':
            $action=new \mainSRC\calls\editCall();
            $action->editedCall($input);
            break;
        case 'callnew':
            $action=new \mainSRC\calls\newCall($input);
            $action->newCallAdd();
            break;
        case 'callnote':
            $action= new \mainSRC\calls\note();
            $action->addNote($input);
            break;
        default:
            $action=new \mainSRC\main();
            $action->checkSession();
            $action->logSave($action->getUserId()."   - unknown action -" . print_r($input['action'],true));
           $action->echoJSON(array('status'=>'error','message'=>'Не известный идентификатор данных'));
           break;
    }
}


