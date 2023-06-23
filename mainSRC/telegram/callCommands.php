<?php

namespace mainSRC\telegram;

use Imagick;
use mainSRC\calls\closureCall;
use mainSRC\main;
use const mainSRC\calls\name_file_close;

const delete_action_query = "DELETE FROM `lift_telegram` WHERE id=:id_telegram";// запрос в базу для удаления записи последовательност команд телеграмм
const query_check_userId = "SELECT  call_staff FROM lift_calls WHERE call_id=:call_id";// запрос в базу ID  ответственного
class callCommands extends telegram
{
    private $waiting_time;
    private $call_id;
    private $action;
    private $message;
    private $caption;// описание к переданной фото
private $main;
    private $id_telegram_expectation;
    private $closure;

    public function __construct()
    {
        parent::__construct();
        $this->waiting_time = $this->getParameterConfig('waiting_time');
        $this->closure = new closureCall();
        $this->main=new main();
    }
public function readOpenCalls(){
        $user_permissions=$this->main->getUserPermission($this->user_id);
    $read_all=$user_permissions[1];
    $close_all=$user_permissions[3];
    $note_all=$user_permissions[4];
        ($read_all)?$call_staff='':$call_staff="AND call_staff =$this->user_id";// если не разрешено смотреть все заявки, то добавим фильтр по ответственному
        $myquery = "SELECT call_id, call_date, call_adres, call_details, call_request, call_staff_status,call_staff from lift_calls WHERE (call_status = 0)  $call_staff;";
        $lift_calls = $this->DB->query($myquery);
        if (!$lift_calls) {
            return "У тебя нет открытых заявок! \xF0\x9F\x91\x8D";
        }
        foreach ($lift_calls as $call) { //начало цикла формирования заявок
            $call_id = $call['call_id'];
            $call_staff_status = $call['call_staff_status'];
            $call_staff=(int)$call['call_staff'];
            $call_details = $call['call_details'];
            $call_address = $call['call_adres'];
            $call_request = (int)$call['call_request']; //уровень заявки
            $call_date = date("Y-m-d H:i", $call['call_date']);
            ($call_request === 1) ? $alarm = "\xF0\x9F\x9A\xA8 \n" : $alarm = "\xF0\x9F\x8F\xA2\n";
            // ( $call_request==3)?$alarm="\xE2\x98\x95 \n":$alarm="\n";
            if (!$call_staff_status && ($call_staff==$this->user_id)) {// Если отмечена как не переданна и пользователь является ответственным то измененяем состояние на переданна онлайн
                $query_staff_date = "call_staff_date=" . strtotime(date('Y-m-d H:i:s ')) . ',';
                $update_status = "UPDATE lift_calls SET  $query_staff_date call_staff_status=2 WHERE  call_id=$call_id;";
                $this->DB->query($update_status);
                $history_date = strtotime(date('Y-m-d H:i:s '));
                $set_history = "Заявка по адресу - " . $call_address . " Отмечена прочитанной. Прочитана в Телеграм. "; //запись в журнал
                $this->DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, '$set_history',  $call_id );");
            }
            $close_but= array(
                'text' => 'Закрыть',
                'callback_data' => '{"id":"' . $call_id . '","action":"close"}'
            );
            $note_but= array(
                'text' => 'Заметка',
                'callback_data' => '{"id":"' . $call_id . '","action":"note"}',
            );
if(!$close_all && ($call_staff!==$this->user_id)){
                $close_but=array();
            }
            if(!$note_all && ($call_staff!==$this->user_id)){
                $note_but=array();
            }

            $but = array('inline_keyboard' => array(
                array( $close_but, $note_but

                ),
            ),
                'one_time_keyboard' => TRUE,
            );
            $replay = json_encode($but);
            $staff_name='';
            if($call_staff!==$this->user_id){
                $staff_name="\n Ответственный - ";
                $staff_name.=$this->DB->single("SELECT user_name FROM lift_users WHERE user_id=$call_staff");

            }
            $text_return = $alarm . "Дата:	$call_date \nАдрес: $call_address \nОписание: $call_details $staff_name \n \xF0\x9F\x91\x87 \n";
            $this->setMessageTelegramSend($text_return);
            $this->sendToTelegram($replay);
        }
    }

    public function check_action()
    {
        //проверка наличия активных действий и ожидаения сообщения
        if (!$this->user_id) {
            return false;
        }

        $current_timestamp = strtotime("now");
        $query_search_telegram_action = "SELECT id,user_id,call_id,time ,action FROM lift_telegram WHERE user_id=$this->user_id ";
        //проверим есть ли для пользователя ожидания сообщения
        $query_telegram_expectation = $this->DB->row($query_search_telegram_action);
        $this->call_id = $query_telegram_expectation['call_id'];
        $this->action = $query_telegram_expectation['action'];
        $this->message = $this->message_text ?? ' ';// если нет текста, то создадим пустую строку
        $this->id_telegram_expectation = $query_telegram_expectation['id'];
        // $delete_action_query = "DELETE FROM `lift_telegram` WHERE id=$this->id_telegram_expectation";// запрос в базу для удаления записи последовательност команд телеграмм
        if ($query_telegram_expectation) {
            if (($query_telegram_expectation['time'] + $this->waiting_time) < $current_timestamp) {
                $this->DB->query(delete_action_query, array("id_telegram" => $this->id_telegram_expectation));
                $this->setMessageTelegramSend("Время ожидания ответа от Вас истекло.");
                $this->sendToTelegram();
                return false;
            } else {
                //проверим не закрыта ли заявка
                $this->closure->setCallId($this->call_id);
                if ($this->closure->checkCallClosed()) {
                    $this->DB->query(delete_action_query, array("id_telegram" => $this->id_telegram_expectation));
                    $this->setMessageTelegramSend("Заявка уже была закрыта ранее.");
                    $this->sendToTelegram();
                    return false;
                }
                if ($this->action === 'note') {
                    $this->addNote();
                } else if ($this->action === 'close') {
                    // call closed
                    if ($this->closeCall()) {
                        return true;
                    } else {
                        $this->DB->query(delete_action_query, array("id_telegram" => $this->id_telegram_expectation));
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    protected function addNote()

    {
        if ($this->photo) {
            $this->savePhoto();
        } else {
           ($this->saveNote()) ? $this->setMessageTelegramSend("Заметка добавлена") : $this->setMessageTelegramSend("Произошла ошибка");
            $this->sendToTelegram();
            $this->DB->query(delete_action_query, array("id_telegram" => $this->id_telegram_expectation));
        }
    }

    protected function savePhoto()
    {
        if (isset($this->message_photo['caption'])) {
            $this->caption = $this->message_photo['caption'];// получим описание к фото если есть
            unset($this->message_photo['caption']);//удалим из массива описание к фото
        }
        $file_id = array_pop($this->message_photo);
        $path_query = json_decode($this->getPhotoPath($file_id['file_id']));// получаем file_path
        if ($path_query->ok) {
            $path = $path_query->result->file_path;
            $this->setMessageTelegramSend("Получаем картинку ");
            $this->sendToTelegram();
            $this->save_img_note($path, $this->call_id, $this->caption);
        } else {
            $this->log_save->logSave(json_encode($path_query), "getImg", "telegram");
            $this->setMessageTelegramSend("произошла ошибка получения изображения");
            $this->sendToTelegram();
        }
    }

    protected function closeCall()
    {
        //Закрыть заявку
        $user_allow = $this->checkUserPermission();
        if (!$user_allow) {
            return false;
        }
        $this->closureSetParameters();
        $solution = $this->main->magicLower($this->message);
        if (iconv_strlen($solution) < 5) {
            $this->setMessageTelegramSend("Число символов в решении по заявке менее 5. Заявка не закрыта");
            $this->sendToTelegram();
            return false;
        }
        $this->closure->setSolution($solution);
        $this->closure->setApprovalClosure(true);
        $result_closure = $this->closure->closureCall();
        if ($result_closure) {
            $text = "Заявка закрыта ";
            $result_archive_history = $this->closure->addHistoryArchive();
            $result_archive_note = $this->closure->addNoteArchive();
            $result_archive_history ? $history = " " : $history = " , но была ошибка истории ";
            $result_archive_note ? $note = " " : $note = ", но была ошибка заметок";
            $text .= $history . $note;
            $this->setMessageTelegramSend($text);
            $this->sendToTelegram();
            return true;
        } else {
            $log = " SC-1 call close $result_closure ";
            $this->log_save->logsave($log, name_file_close, "telegram");
            $this->setMessageTelegramSend("Произошла ошибка ");
            $this->sendToTelegram();
            return false;
        }

    }

    protected function closureSetParameters()
    {
        if (empty($this->closure->getCallId())) {
            $this->closure->setCallId($this->call_id);
        }
        if (empty($this->closure->getClosedUserName())) {
            $this->closure->setClosedUserName($this->user_name);
        }
    }

    protected function checkUserPermission()
    {
        $user_permission = $this->main->getUserPermission($this->user_id)[2];
        $staff_id = (int)$this->DB->single(query_check_userId, array("call_id" => $this->call_id));//вернет true если пользователь является ответственным
        if (!$user_permission && ($staff_id !== (int)$this->user_id)) {
            $this->setMessageTelegramSend('Произошла непредвиденная ошибка. Среди Ваших заявок не найдена данная открытая заявка');
            $this->sendToTelegram();
            $this->DB->query(delete_action_query, array("id_telegram" => $this->id_telegram_expectation));
            return false;
        }
        return true;
    }

    private function save_img_note($path, $call_id, $caption)
    {
        $image_name = null;
        $path_parts = pathinfo($path);
        $extension = $path_parts['extension'];//расширение
        if (!preg_match('/(jpg|jpeg|png|gif)/', $extension)) {
            $this->setMessageTelegramSend("Разрешены файлы формата jpg|jpeg|png|gif ");
            return false;
        }
        //https://api.telegram.org/file/bot<token>/$path
        $url = "https://api.telegram.org/file/bot" . $this->telegram_token . "/$path";
        //file_put_contents("img.png", file_get_contents($url));
        //return false;
        $temp = tempnam("temp", "img");
        $fileName = "$temp.$extension";
        file_put_contents($fileName, file_get_contents($url));
        try {
            $img = new Imagick($fileName);

            $size_width = $img->getImageWidth();
            $size_height = $img->getImageHeight();
            $image_name = $call_id . "note_img" . rand(10, 2000) . ".$extension";
            if ($size_height > 720 || $size_width > 1280) {
                $img->thumbnailImage(1280, 720, true);
            } else {
                $img->thumbnailImage(($size_width - 10), ($size_height - 10), true);
            }
            $dir_image_note = $_SERVER['DOCUMENT_ROOT'] . "/note_images";
            $dir_image_note_thumb = $_SERVER['DOCUMENT_ROOT'] . "/note_thumb";

            $this->check_directory($dir_image_note);
            $this->check_directory($dir_image_note_thumb);

            $image_big = $img->writeImage($dir_image_note . "/" . $image_name);// сохраним крупное изображение
            $img->thumbnailImage(100, 100, TRUE);
            $image_thumb = $img->writeImage($dir_image_note_thumb . "/" . $image_name);
            if (!$image_big || !$image_thumb) {
                $this->log_save->logSave("ошибка записи изображения", "imgSave", "telegram");
            }
        } catch (\ImagickException $e) {
            $this->log_save->logSave("error  $e ", "imagick", "telegram");
        }
        $error = '';
        if ($img->clear()) {
            //chmod($fileName, 0770);
            unlink($fileName);
            unlink($temp);
        } else {
            $error = "С некоторыми ошибками";
        }

        ($this->saveNote($caption, $call_id, $image_name)) ? $this->setMessageTelegramSend("Изображение сохранено $error и будет привязано к текущей завке.") : $this->setMessageTelegramSend("Произошла ошибка! попробуйте позже!");
        $this->sendToTelegram();
    }

    private function saveNote($message_save = null, $call_id_save = null, $file_name = null)
    {

        $message = $message_save ?? $this->message;
        $call_id = $call_id_save ?? $this->call_id;
        $telegram = "t-" . $this->chat_id;
        $current_timestamp = strtotime("now");
        $type = 1;
        $title = "Заметка";
        if ($file_name) {
            $type = 2;
            $title = "Изображение";
            $file_name = " , img_name='" . $file_name . "'";
        }
        $query_telegram_action = "INSERT INTO `lift_notes` SET note_post_ip=:telegram, note_post_user=$this->user_id, note_title='$title',note_body=:text, note_relation=$call_id , note_type=$type ,note_post_date=$current_timestamp $file_name";
        $add_note = $this->DB->query($query_telegram_action, array("telegram" => $telegram, "text" => $message));
        return $add_note;
    }

    private function check_directory($dir)
    {
        if (!is_dir($dir)) {
            return mkdir($dir, 0750, true);
        }
    }
    public function send_help()
    {
        $text = "Приветсвтую тебя \n Для того что бы отписать свой телеграм от получения новых заявок подай команду /stop \n Чтобы зарегестрировать твой телеграм отправь контактные данные \n Три точки в правом верхнем углу и выбрать  'Отправить свой телефон' затем нажать кнопочку - Поделиться контактом \n Для управления открытми заявками нажми ниже кнопочку 'Открытые заявки'\n(в разработке) Под каждой открытой заявкой кнопочки Закрыть и Заметка ( позваляют собственно выполнить все то что на них написано)  \n Не забывая что врежиме ЗАКРЫТЬ или ЗАМЕТКА любое сообщение отправленное боту будет как соответсвующее сообщение по команде (закрытие  - как решение по заявке, Заметке - как добавление текстовой заметке к заявке) Если команду выполнять не надо, нажми кнопочку ОТМЕНА\n Удачи! \n  (C) Zamotaev A.N. https://e-rcs.ru";
        $this->setMessageTelegramSend( $text);
        $this->sendToTelegram();

    }
}