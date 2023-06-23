<?php
namespace includes;
class mainControl extends mainConfig {
    protected function check_nacl($nacl,$id)
    {
        if ($this->nacl($id) !== $nacl) {
            $this->echoJson->echoJSON(array('status' => 'error', 'message' => 'Ошибка токена'));
        }
    }
    public function checkUser($nacl=0,$id = 0)
    {
        $id = $id ?: $this->user_id;
        $nacl=$nacl?:$this->user_nacl;
        $this->check_nacl($nacl,$id);
        $user_query = "SELECT user_block, user_read_all_calls, user_localadmin, user_level FROM lift_users WHERE user_id=$id LIMIT 1";
        $user_data = $this->DB->row($user_query);
        if ($user_data['user_block']) {
            $this->echoJson->echoJSON(array('status' => 'error', 'message' => 'Ваш  аккаунт заблокирован'));
        }
    }
    public function getUserPermission()
    {
        /*
        * 0-read all calls
        * 1-edit_call
        * 2-close_call
        * 3-note_call
        * 4-add_call_permission
        * 5-edit_user_link
        * 6-edit_obj_link
        */
        $query = "SELECT `user_add_call`, `user_localadmin`,`user_edit_obj`, `user_read_all_calls`, `user_edit_user`, `user_level`, `user_disppermission` FROM `lift_users` WHERE `user_id`=:userId LIMIT 1";
        $userdata = $this->DB->row($query, array("userId" => $this->user_id));
        $read_all_calls = false; //0 разрешено чтение всех заявок
        $edit_call = false;//1
        $close_call = false;//2
        $note_call = true;//3
        $add_call_permission = false;//4
        $edit_user_link = false;//5
        $edit_obj_link = false;//6
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
        return [$read_all_calls, $edit_call, $close_call, $note_call, $add_call_permission, $edit_user_link, $edit_obj_link];
    }

}
