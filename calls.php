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
sleep(5);
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
        case 'loadStartData':
            $action= new \mainSRC\loadStartData();
            $action->configData();
            break;
        case 'loadAddress':
            $action= new \mainSRC\loadStartData();
            $data=$action->loadAddress();
            $action->echoJSON(array("status"=>"ok","action"=>"loadAddress","message"=>$data));
            break;
            case 'checkNewConfig':
           $action=new  \mainSRC\loadStartData();
           $data=$action->checkDataUpdate();
           $action->echoJSON(array("status"=>"ok","message"=>$data));
        default:
            $action=new \mainSRC\main();
            $action->checkSession();
            $action->logSave($action->getUserId()."   - unknown action -" . print_r($input['action'],true));
           $action->echoJSON(array('status'=>'error','message'=>'Не известный идентификатор данных'));
           break;
    }
}


