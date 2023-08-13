<?php

namespace mainSRC\setting;

use mainSRC\dataBase\PDODB;
use mainSRC\logSave;
use mainSRC\main;

/*add id-parent id
'{"name":"121121","action":"addAddress","type":"object","userId":"1","nacl":"7d0bad4acbb63f60d2185b5198860cf3","id":"122"}'
edit id- editable id
{"name":"п. 1","vis":true,"action":"editAddress","type":"object","userId":"1","nacl":"7d0bad4acbb63f60d2185b5198860cf3","id":"324"}
*/

class address
{
    protected $typeSQL = array(
        'object' => array(
            'parentSQL' => ' AND home_id=:parent_id',
            'parentId' => 'home_id',
        ),
        'home' => array(
            'parentSQL' => ' AND street_id=:parent_id',
            'parentId' => 'street_id',
        ),
        'street' => array(
            'parentSQL' => 'AND city_id=:parent_id',
            'parentId' => 'city_id',
        ),
        'city' => array(
            'parentSQL' => '',
            'parentId' => 'id',
        )
    );
    //  protected $typeAddress = array('lift_city' => '', 'lift_street' => '', 'lift_home' => '', 'lift_object' => 'лифт');
    //  protected $parentSQL=array('object'=>' AND home_id=:id','home'=>' AND street_id=:id','street'=>' AND city_id=:id','city'=>'');
    //  protected $parentId=array('object'=>' home_id','home'=>' street_id','street'=>' city_id','city'=>'id');
    protected $name;
    protected $parent;
    protected $id;
    protected $vis;
    protected $error = [];
    protected $result = [];
    protected $type = false;
    protected $new = false;
    protected $DB;
    private $test;
    private $testMessage = [];

    public function __construct($test = false)
    {
        $this->DB = PDODB::getInstance();
        $this->test = $test;
    }

    protected function checkData($data)
    {//проверим переданные данные
        $data['type'] ? $this->type = trim($data['type'], "\t\n\r\0\x0B") : $this->error[] = "Нарушена целостность данных type";
        $data['name'] ? $this->name = trim($data['name'], "\t\n\r\0\x0B") : $this->error[] = "Нарушена целостность данных name";
        if ($this->new) {
            isset($data['id']) ? $this->parent = (int)$data['id'] : $this->error[] = "Нарушена целостность данных parentId " . $data['id'];
        } else {
            isset($data['id']) ? $this->id = (int)$data['id'] : $this->error[] = "Нарушена целостность данных elementId";
            isset($data['vis']) ? $this->vis = (int)$data['vis'] : $this->error[] = "Нарушена целостность данных visual";;
        }
        if(count($this->error))$this->echoResult("error",$this->error);
    }

    /**
     * @return bool
     * проверяем наличие  базе объекта с указанным именеми и принадлежащий указанооому родителю
     */
    protected function checkDuplicateName()
    {
        $nameForSearch = str_replace([' ', '.'], '', $this->name);//уберем все пробелы и запятые исключим их из поиска проверки дубликатов
        if ($this->type) {
            $this->type == 'city' ? $arr = array('name' => $nameForSearch) : $arr = array('name' => $nameForSearch, 'parent_id' => $this->parent);
            if(!$this->new){
                $arr['id']=$this->id;
                $addIgnoreId=' AND id!=:id ';
            }else{
                $addIgnoreId='';
            }
            $querySearchName = "SELECT id FROM lift_" . $this->type . " WHERE REPLACE(REPLACE(TRIM(" . $this->type . "_name), ' ', ''),'.','') =:name ".$addIgnoreId . $this->typeSQL[$this->type]['parentSQL'];

            $resultSearch = $this->DB->single($querySearchName, $arr);
            if ($this->test) $this->testMessage['querySearchName'] = $querySearchName;
            if ($this->test) $this->testMessage['resultSearch'] = $resultSearch;
            if ($resultSearch) {
                $this->error[] = "Объект с именем <b>" . $this->name . "</b> <br>Уже есть у родителя с ID<b>" . $this->parent . "</b> и записан в базе под ID=" . $resultSearch;
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function editAddress($data)
    {
        $this->checkData($data);
        $parentQuery = "SELECT " . $this->typeSQL[$this->type]['parentId'] . " FROM lift_" . $this->type . " WHERE id=:id";// id parent
        $this->parent = $this->DB->single($parentQuery, array('id' => $this->id));
        if ($this->checkDuplicateName()) {
            $tableName="lift_".$this->type;
            $updateData[$this->type."_name"]=$this->name;
            $updateData['vis']=$this->vis;
            $updateWhere['id']=$this->id;
           $update=$this->DB->update($tableName,$updateData,$updateWhere);
            if ($update) $message[] = "Изменено на " . $this->name . " успешно";
            $status = "ok";
        } else {
            $status = "error";
            $message = $this->error;
        }
        if ($this->test) {
            $this->testMessage['status'] = $status;
            $this->testMessage['message'] = $message;

        } else {
            $this->echoResult($status, $message);
        }
    }

    public function addAddress($data)
    {
        $this->new = true;
        $this->checkData($data);
        $type_name=$this->type."_name";
        $insertData[$type_name]=$this->name;
        $table_Name="lift_".$this->type;
       if( $this->type!= 'city') {
           $insertData[$this->typeSQL[$this->type]['parentId']]=$this->parent;
       }
        if ($this->checkDuplicateName() ) {
            $resultInsert=$this->DB->insert($table_Name,$insertData);
          if( $resultInsert){
              $status='ok';
              $message="объект с именем ".$this->name." успешно дабавлен";
          }
         else{
             $status='error';
             $message="Произошла ошибка при записи данных в базу";
         }

        } else {
            $status = "error";
            $message = $this->error;
        }
        if ($this->test) {
            $this->testMessage[]="Result insert =".$resultInsert."<br>";
            $this->testMessage[] = $this->error;
            $this->testMessage[] = "<br>" . $table_Name;
            $this->testMessage[]= $insertData;
            return $this->testMessage;
        }
$this->echoResult($status,$message);
    }

    protected function echoResult($status, $message)
    {
        header('Content-type: application/json');
        echo json_encode(array("status" => $status, "message" => $message));
        exit();

    }
}