<?php
namespace mainSRC;
class loadStartData extends main{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $read_All
     * @return array
     */
    public function loadAddress($read_All=false)
    {
        $this->checkSession();
        $this->checkUser();
        $city_query="SELECT id, city_name  FROM lift_city WHERE vis=0 ORDER BY city_name ";
        $street_query="SELECT id,street_name,city_id FROM lift_street WHERE vis=0  ORDER BY street_name ";
        $home_query="SELECT id,home_name,street_id FROM lift_home WHERE vis=0 ORDER BY  home_name";
        $lift_query= "SELECT id,object_name,home_id FROM lift_object WHERE vis=0 ORDER BY object_name";
        if ($read_All){
            $city_query="SELECT *  FROM lift_city WHERE 1 ORDER BY city_name ";
            $street_query="SELECT * FROM lift_street WHERE 1  ORDER BY street_name ";
            $home_query="SELECT * FROM lift_home WHERE 1 ORDER BY  home_name";
            $lift_query= "SELECT * FROM lift_object WHERE 1 ORDER BY object_name";
        }
        $city = $this->DB->query($city_query);
        $street = $this->DB->query($street_query);
        $home = $this->DB->query($home_query);
        $lift = $this->DB->query($lift_query);
        $result['city'] = $city;
        $result['street'] = $street;
        $result["home"] = $home;
        $result['object'] = $lift;

        return $result;
    }
    public function configData(){
        $this->checkSession();
        $this->checkUser();
        $nav = $this->getUserPermission();
        $res = array();
        $res['nav'] = $nav;
        $res['department'] = $this->queryarr("1");
        $res['request'] = $this->queryarr("2");
        $res['group'] = $this->queryarr("3");
        $res['repair_time'] = $this->repairTimeArr();
        $res['staff'] = $this->queryarr("5");
        $data['status'] = 'ok';
        $data['message'] = $res;
        $this->echoJSON($data);
    }
    private function repairTimeArr(){
        return json_decode($this->DB->single("SELECT option_value FROM lift_options WHERE option_name='repair_time'"), true);

    }
    private function queryarr($type = "0")
{

    if ($type == "1" or $type == "2" or $type == "3") {
        //получим типы заявок 1-отдел, 2- уровни заявкиб 3- группа заявки
        $query = "select type_id,type_name from lift_types where type=" . $type . " order by type_name;";
        $id = "type_id";
        $name = "type_name";
    } else {
    if ($type == "4") {
        //получаем список городов
        $query = "SELECT city_name, id from lift_city WHERE 1 order by city_name";
        $id = "id";
        $name = "city_name";
    } else {
        //получаем список сотрудников
        $query = "select user_id,user_name from lift_users where (user_level=2 OR user_level=0) AND user_block<>1 order by user_name;";
        $id = "user_id";
        $name = "user_name";

    }
}
$item = array();
$result = $this->DB->query($query);
foreach ($result as $element) {
    $item[$element[$id]] = $element[$name];//пересоберем результат в ассоциативный массив ключом которого будет id а значением собственно значение
}
return $item;
}
}
