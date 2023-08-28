<?php

namespace mainSRC\setting;

use mainSRC\main;

class reports extends main
{
    protected $user_id;
    protected $permissionAll = false;
    private $data = [];

    public function __construct($userId = null)
    {
        parent::__construct();
        $this->checkSession();
        $this->checkUser();
        if ($this->getUserPermission()[1] || $this->getUserPermission()[8]) {
            $this->permissionAll = true;
        }//если разрешено чтение всех заявок или админ то разрешаем отчет по всем заявкам иначе только по своим
        $this->user_id = $userId ?? $this->getUserId();
    }

    public function mainForm()
    {
        $data['select_responsible_person'] = [array('user_id' => $this->user_id, 'user_name' => $this->getUserName())];
        if ($this->permissionAll) {
            $query_person = "select user_id,user_name from lift_users where (user_level=2 OR user_level=0)  order by user_name;";
            $data['select_responsible_person'] = $this->DB->query($query_person);
        }
        $data['addressList'] = $this->addressList();

        return $data;
    }

    private function addressList($readAll = false)
    {
        $list = '';
        $readAll ? $vis = 1 : $vis = 0;
        $city_query = "SELECT id, city_name  FROM lift_city WHERE vis=$vis ORDER BY city_name ";
        $city_arr = $this->DB->query($city_query);
        foreach ($city_arr as $item) {
            $city = $item['city_name'];
            $city_id = $item['id'];
            $list .= "<optgroup label='$city'>";
            $street_query = "SELECT id,street_name,city_id FROM lift_street WHERE vis=$vis AND city_id=$city_id ORDER BY street_name ";
            $street_arr = $this->DB->query($street_query);
            foreach ($street_arr as $item_street) {
                $street = $item_street['street_name'];
                $street_id = $item_street['id'];

                $home_query = "SELECT id,home_name,street_id FROM lift_home WHERE vis=$vis AND street_id=$street_id ORDER BY  home_name";
                $home_arr = $this->DB->query($home_query);

                if (count($home_arr)) $list .= "<optgroup label='$city - $street'>";
                foreach ($home_arr as $item_home) {
                    $home = "Дом№ " . $item_home['home_name'];
                    $home_id = $item_home['id'];
                    $lift_query = "SELECT id,object_name,home_id FROM lift_object WHERE vis=$vis AND home_id=$home_id ORDER BY object_name";
                    $object_arr = $this->DB->query($lift_query);
                    if (count($object_arr) > 1) {
                        $list .= "<option class='ful' value='home_$home_id'>ПОЛНОСТЬЮ $home </option>";
                        foreach ($object_arr as $item_obj) {
                            $object = $item_obj['object_name'];
                            $object_id = $item_obj['id'];
                            $list .= "<option value=' $object_id'>$home - $object</option>";
                        }
                    } else {
                        $name = $home . '->' . $object_arr[0]['object_name'];
                        $obj_id = $object_arr[0]['id'];
                        $list .= "<option value='$obj_id'>$name</option>";
                    }

                }

                $list . "</optgroup>";
            }


            $list . "</optgroup>";
        }

        return $list;


    }

}
