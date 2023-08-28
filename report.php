<?php
require_once("./include/autoload.php");
$main= new \mainSRC\main();
$main->checkSession();
$main->checkUser();
$nacl=$_GET['nacl']??0;
$user_id=$_GET['userId']??0;
$action=$_GET['action']??false;
$filterStaff=$_GET['filterStaff']??0;
$staff=$_GET['staff']??0;
$object=$_GET['object']??0;
$startDate=$_GET['startdate']??0;
$endDate=$_GET['enddate']??0;
$startDate=strtotime($startDate);
$endDate=strtotime(date($endDate.' 23:59:59')); //установим конец суток
if($startDate<strtotime('2022-01-01'))$startDate=strtotime('2022-01-01');
if($endDate>strtotime(date('y-m-d 23:59:59' )))$endDate=strtotime(date('y-m-d 23:59:59' ));
if($startDate>$endDate )exit('Проверьте даты !');
if($nacl!=$main->nacl($user_id)) exit('Ошибка авторизации');
if(!$object)exit('Не передан объект');
$readAll=$main->getUserPermission($user_id)[1];//разрешен или нет просмотр всех заявок
if(!$readAll){
    // если читать все заявки не разрешено, то установим фильтр по текущемму пользователю
    $filterStaff=1;
    $staff=$user_id;
}
$queryWhere='';
$whereArr=[];
if($filterStaff==='true') {
    $user_name=$main->DB->single('SELECT user_name FROM lift_users WHERE user_id=?',array($staff));
    if(!$user_name)exit('ответсвенный не найден');
    $queryWhere="AND call_staff=:staff";
    $whereArr['staff']=$staff;
}else{
    $user_name='Фильтр по ответственному отключен';
}
$name_col=array("call_id"=>"№","open"=>"Открыта",
    "close"=>"Закрыта","address"=>"Адрес","details"=>"Описание заявки",
    "staff"=>"Ответсвенный","solution"=>"Решение","note"=>"Коментарии к заявке"
);
$table=new table($name_col);
$whereArr['callStart']=$startDate;
$whereArr['callEnd']=$endDate;
$where_obj=' AND (';
if (substr_count($object,"home_")){
    //поиск по всем лифтам в доме
     $home_id=str_replace("home_",'',$object);
    $query_object_arr="SELECT id FROM lift_object WHERE home_id=?";
    $obj_arr=$main->DB->column($query_object_arr,array($home_id));
    $or='';
    foreach ($obj_arr as $object) {
        $where_obj.="$or address_lift=".$object;
        $or=' OR ';
    }

}else{
    //поиск по конкретному лифту
$where_obj.="  address_lift=:object_id";
$whereArr['object_id']=$object;
}
$where_obj.=")";
$where_obj="";

$queryCallsReports="SELECT *  FROM lift_calls WHERE (call_date>=:callStart AND call_date<=:callEnd ".$queryWhere." )".$where_obj."  LIMIT 500";
$reportCalls=$main->DB->query($queryCallsReports,$whereArr);
foreach ($reportCalls as $callReport) {
    $row=[];
    $row['call_id']=$callReport['call_id'];
   $row['open']=$callReport['call_first_name']."-".date("d-m-Y H:m",$callReport['call_date']);
   ($callReport['call_status'])?$row['close']=$callReport['call_last_name']."-".date("d-m-Y H:m",$callReport['call_date2']):$row['close']="Открыта";
   $row['address']=$callReport['call_adres'];
   $row['details']=$callReport['call_details'];
   $staff_text=$main->DB->single("SELECT user_name FROM lift_users WHERE user_id=?",array($callReport['call_staff']));
   $staff_text.="<br> уведомлен ".date('d-m-Y H:m',$callReport['call_staff_date'])." ".$main->KeyStaffStatusType((int)$callReport['call_staff_status']);
    $row['staff']=$staff_text;
    $row['solution']=$callReport['call_solution'];
    $notes_arr=json_decode($callReport['call_full_note_history'],true) ;
    if (count($notes_arr)){
        $row['note']=count($notes_arr)."- коментариев <br>";
        foreach ($notes_arr as $note) {
            $user_note_post_name=$main->DB->single("SELECT user_name FROM lift_users WHERE user_id=?",array($note['note_post_user']));
            $row['note'].=' добавил - '. $user_note_post_name." -".date("d-m-Y H:m",$note['note_post_date'])."<br>".$note['note_body']."<hr>";
        }
    }else{
        $row['note']='-';
    }
    $table->setTbody($row);


}
?> <!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=.8">
    <title>Журнал заявок. Отчет </title>
    <meta name="description"
          content="Электронный журнал заявок по ремнту лифтов">
    <meta name="author" content="Zamotaev Anatoliy">
    <meta name="robots" contents="noindex">
    <link rel="icon" type="image/png" href="favicon.ico">

        <style>
            table {
                width: 100%;
                border: none;
                margin-bottom: 20px;
                border-collapse: separate;
            }
            table thead th {
                font-weight: bold;
                text-align: left;
                border: none;
                padding: 10px 15px;
                background: #EDEDED;
                font-size: 14px;
                border-top: 1px solid #ddd;
            }
            table tr th:first-child, .table tr td:first-child {
                border-left: 1px solid #ddd;
            }
            table tr th:last-child, .table tr td:last-child {
                border-right: 1px solid #ddd;
            }
            table thead tr th:first-child {
                border-radius: 20px 0 0 0;
            }
            table thead tr th:last-child {
                border-radius: 0 20px 0 0;
            }
            table tbody td {
                text-align: left;
                border: none;
                padding: 10px 15px;
                font-size: 14px;
                vertical-align: top;
            }
            table tbody tr:nth-child(even) {
                background: #F8F8F8;
            }
            table tbody tr:last-child td{
                border-bottom: 1px solid #ddd;
            }
            table tbody tr:last-child td:first-child {
                border-radius: 0 0 0 20px;
            }
            table tbody tr:last-child td:last-child {
                border-radius: 0 0 20px 0;
            }


            .scroll-right:after {
                content: '';
                display: block;
                width: 15px;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                z-index: 500;
                background: radial-gradient(ellipse at right, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 75%) 100% center;
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-size: 15px 100%;
                background-position: 100% 0%;
            }
            .scroll-left:before {
                content: '';
                display: block;
                width: 15px;
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 500;
                background: radial-gradient(ellipse at left, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 75%) 0 center;
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-size: 15px 100%;
            }
    </style>
    </head><body>
<p>
    <input type="button" value="PDF"
           id="btPrint" onclick="createPDF()" />
</p>
<?php
echo "Отчет ".$user_name."<br>";
echo $table->echoTable();

?>
<script>
    function createPDF() {
        var sTable = document.getElementById('tableReport').innerHTML;

        var style = "<style>";
        style = style + "@page { size: landscape; }table {width: 100%;font: 17px Calibri;}";
        style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
        style = style + "padding: 2px 3px;text-align: center;}";
        style = style + "</style>";

        // CREATE A WINDOW OBJECT.
        var win = window.open('', '', 'height=700,width=700');

        win.document.write('<html><head>');
        win.document.write('<title>Profile</title>');   // <title> FOR PDF HEADER.
        win.document.write(style);          // ADD STYLE INSIDE THE HEAD TAG.
        win.document.write('</head>');
        win.document.write('<body>');
        win.document.write(sTable);         // THE TABLE CONTENTS INSIDE THE BODY TAG.
        win.document.write('</body></html>');

        win.document.close(); 	// CLOSE THE CURRENT WINDOW.

        win.print();    // PRINT THE CONTENTS.
    }

</script>

</body></html>
<?php




class table{
    protected $thead;
    private $nameCol=[];
    protected $tbody=[];
    private $num_row=0;
    public function __construct($nameCol)
    {
      $this->thead='<thead><tr>';
        foreach ($nameCol as $key=>$name) {
            $this->nameCol[]=$key;
            $this->thead.="<th>$name</th>";

  }
        $this->thead.="</tr></thead>";
    }

    /**
     * @param array $tbody
     */
    public function setTbody(array $tbody): void
    {
        $tr="<tr>";
        foreach ($this->nameCol as $col) {
         $tr.="<td>".$tbody[$col]."</td>" ;  
        }
        $tr.="</tr>";
        $this->tbody[$this->num_row] = $tr;
        $this->num_row++;
        
    }
    public function echoTable(){
        $table="<div class='big-table' id='tableReport'><table >".$this->thead."<tbody>";
        
        foreach ($this->tbody as $row) {
            $table.=$row;
        }
        $table.="</tbody></table></div>";
        return $table;
    }

}