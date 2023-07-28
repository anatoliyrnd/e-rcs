<?php
require_once("./include/autoload.php");
$main=new \mainSRC\main();
$main->checkSession();
if (!$main->checkUser()){
echo "Ошибка авторизации";
  $main->logSave("setting authorisation error id-".$main->getUserId()." name -".$main->getUserName(),"settings","setting");
 exit();
}
$user_id=$main->getUserId();
$nacl=$main->nacl($user_id);

/*
 * Настройки, Адреса, Пользователи,  Отчеты, Логи,
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Журнал по учету заявок на ремонт лифтов.Настройки</title>
<meta name="description" content="Электронный журнал заявок по ремнту лифтов -> Настройки" />
<meta name="author" content="Zamotaev Anatoliy" />
<style>
    *["data-user_admin"=true]{
      color:red;  
    }
</style>
    <script>
        const nacl="<?php echo $nacl; ?>";
        const userId="<?php echo $user_id;?>";
        const URI="<?php echo $main->getHostURL(); ?>";

    </script>
<script type="module" src="./js/setting.js"></script>
<link rel="stylesheet" href="./css/setting.css" />
</head>
<body>
<h1>Страница настроек системы</h1>
<div class="tabs" id="tabs">
    <input type="radio" id="tab1"  data-type="setting" data-action="get" name="tab-control" checked>
    <input type="radio" id="tab2"  data-type="address" data-action="get" name="tab-control">
    <input type="radio" id="tab3"  data-type="users" data-action="get" name="tab-control">
    <input type="radio" id="tab4"  data-type="reportCall" data-action="get" name="tab-control">
    <input type="radio" id="tab5"  data-type="logSystem" data-action="get" name="tab-control">
    <ul menu="true">
        <li  menu="true" title="Настройки"><label for="tab1" role="button"><svg viewBox="0 0 24 24"><path d="m12.44228,21.07898c-0.2811,0.0237 -0.56849,0.04008 -0.86408,0.04008l0,0c-0.26439,0 -0.52122,-0.01297 -0.77176,-0.03219l0,0l-2.16931,2.4976l-0.5335,-0.13548c-0.62616,-0.15751 -1.23436,-0.35511 -1.81922,-0.59222l0,0l-0.50703,-0.2055l0.13046,-3.13723c-0.46103,-0.28907 -0.89117,-0.61086 -1.28951,-0.96369l0,0l-3.42981,0.65997l-0.31323,-0.41216c-0.36744,-0.48381 -0.68067,-0.9908 -0.95828,-1.50338l0,0l-0.24108,-0.44771l2.32878,-2.30902c-0.1456,-0.48892 -0.24202,-0.99364 -0.29181,-1.50792l0,0l-3.08602,-1.46162l0.05609,-0.49514c0.06619,-0.59223 0.19412,-1.15903 0.34695,-1.70382l0,0l0.13646,-0.48328l3.4421,-0.4268c0.24547,-0.46407 0.53446,-0.90385 0.85997,-1.31712l0,0l-1.31344,-2.89673l0.39643,-0.34777c0.46606,-0.40648 0.96491,-0.77513 1.48548,-1.11218l0,0l0.45095,-0.29129l2.97131,1.67163c0.50075,-0.20662 1.02447,-0.37655 1.56712,-0.50697l0,0l1.0771,-2.99383l0.55525,-0.03781c0.29906,-0.01975 0.62049,-0.04291 0.96995,-0.04291l0,0c0.34853,0 0.669,0.02316 0.96807,0.04291l0,0l0.55524,0.03781l1.08875,3.02491c0.52185,0.13039 1.02763,0.29694 1.51229,0.49907l0,0l3.01291,-1.69539l0.45252,0.29132c0.52059,0.33649 1.02036,0.70626 1.4855,1.11105l0,0l0.39799,0.34777l-1.35377,2.98256c0.29496,0.38389 0.56092,0.79038 0.78813,1.21775l0,0l3.54957,0.44035l0.13802,0.48325c0.15504,0.54425 0.28236,1.11105 0.34694,1.70441l0,0l0.05611,0.49511l-3.1938,1.51243c-0.04789,0.46917 -0.13298,0.93098 -0.25998,1.38092l0,0l2.40629,2.38132l-0.24201,0.44936c-0.27668,0.51432 -0.58835,1.02298 -0.95736,1.50511l0,0l-0.31323,0.41098l-3.51489,-0.67461c-0.37248,0.33364 -0.77583,0.64076 -1.20694,0.91796l0,0l0.13392,3.19877l-0.50829,0.20607c-0.58551,0.23598 -1.19275,0.43414 -1.81889,0.59166l0,0l-0.53541,0.13549l-2.17403,-2.50381l0,0l0,0zm2.78948,0.80112c0.18751,-0.05591 0.37185,-0.11633 0.5543,-0.18069l0,0l-0.12541,-3.02092l0.39549,-0.22753c0.59243,-0.34436 1.13192,-0.75763 1.61091,-1.22676l0,0l0.3189,-0.31275l3.32584,0.63792c0.0999,-0.14962 0.19663,-0.3026 0.2899,-0.45897l0,0l-2.27488,-2.25315l0.14118,-0.40254c0.20072,-0.5798 0.31481,-1.20139 0.34852,-1.85002l0,0l0.0208,-0.42232l3.02172,-1.43056c-0.03151,-0.17333 -0.06773,-0.34551 -0.10902,-0.51829l0,0l-3.36207,-0.41661l-0.18086,-0.38956c-0.27039,-0.58264 -0.62806,-1.12799 -1.05505,-1.631l0,0l-0.28236,-0.33535l1.27878,-2.81769c-0.14811,-0.11633 -0.3,-0.22922 -0.45599,-0.33874l0,0l-2.85439,1.60504l-0.42038,-0.19875c-0.62267,-0.29357 -1.29611,-0.51657 -2.0064,-0.66108l0,0l-0.45945,-0.09427l-1.03077,-2.86343c-0.10022,-0.0045 -0.19759,-0.00678 -0.29244,-0.00678l0,0c-0.09675,0 -0.19412,0.00228 -0.29497,0.00678l0,0l-1.02351,2.84481l-0.46608,0.0892c-0.72259,0.13886 -1.41176,0.36526 -2.05366,0.66278l0,0l-0.41944,0.19533l-2.80807,-1.57961c-0.15504,0.10952 -0.30725,0.22241 -0.45409,0.33874l0,0l1.24473,2.74374l-0.29242,0.33589c-0.45599,0.52447 -0.83509,1.09921 -1.11934,1.71626l0,0l-0.17993,0.39124l-3.25398,0.40308c-0.04128,0.17277 -0.07782,0.34496 -0.10839,0.51829l0,0l2.91711,1.38146l0.01795,0.42565c0.03056,0.6871 0.15882,1.3465 0.3794,1.96299l0,0l0.14434,0.40533l-2.20305,2.18315c0.09266,0.15693 0.18908,0.30992 0.28993,0.45953l0,0l3.23506,-0.62383l0.32207,0.31276c0.49632,0.48667 1.06323,0.90951 1.68781,1.26068l0,0l0.40305,0.22523l-0.12322,2.97465c0.18277,0.06436 0.36775,0.12478 0.55525,0.18069l0,0l2.04769,-2.35872l0.46764,0.05135c0.35294,0.04009 0.69642,0.06606 1.03834,0.06606l0,0c0.36775,0 0.73864,-0.02878 1.11774,-0.07677l0,0l0.47175,-0.05926l2.06343,2.37734l0,0l0,0zm0.9334,-6.12544l0.45125,0.62098l-2.8607,1.66716l-1.01532,-1.39956l0,0c-0.36934,0.08072 -0.75757,0.12818 -1.16155,0.12818l0,0c-2.79548,0 -5.06059,-2.02959 -5.06059,-4.53114l0,0c0,-2.50379 2.26511,-4.53282 5.06059,-4.53282l0,0c2.79232,0 5.05805,2.02903 5.05805,4.53282l0,0c0,1.02804 -0.3857,1.98045 -1.03233,2.74092l0,0l0.5606,0.77345m-1.92448,0.23878l0.08918,-0.05079l-0.81554,-1.12402l0.47268,-0.42343c0.61797,-0.55495 0.99706,-1.31202 0.99706,-2.15492l0,0c-0.00347,-1.68518 -1.52678,-3.04974 -3.40586,-3.05311l0,0c-1.88351,0.00337 -3.40524,1.36623 -3.40839,3.05311l0,0c0.00315,1.68351 1.52646,3.04803 3.40839,3.05143l0,0c0.42036,0 0.825,-0.07735 1.21669,-0.21171l0,0l0.62457,-0.21453l0.82121,1.12797l0,0z"/>
                </svg><br><span>Настройки</span></label></li>
        <li  menu="true" title="Адреса"><label for="tab2" role="button"><svg viewBox="0 0 24 24"><path d="m22.52344,12.43988l-2.98112,-3.10246l0,-7.40963l-2.55566,0l0,4.74995l-5.96322,-6.20594l-11.5,11.96808l2.73772,-0.06789l0,12.0998l17.40285,0l0,-12.03192l2.85942,0zm-11.5,10.30135l-5.67893,0l0,-7.0925l5.67893,0l0,7.0925zm5.51731,-3.03964l-3.73176,0l0,-4.09541l3.73176,0l0,4.09541z" />
                </svg><br><span>Адреса</span></label></li>
        <li  menu="true" title="Пользователи"><label for="tab3" role="button"><svg viewBox="0 0 24 24">
                    <path d="m15.62394,17.28524c-1.0187,-0.44464 -1.42546,-1.66666 -1.42546,-1.66666s-0.45895,0.27679 -0.45895,-0.50059s0.45895,0.50059 0.91791,-2.50096c0,0 1.27247,-0.38967 1.0187,-3.61207l-0.30597,0c0,0 0.76402,-3.44521 0,-4.61226c-0.76492,-1.16705 -1.06999,-1.94443 -2.75192,-2.50096s-1.06909,-0.44562 -2.29207,-0.38869c-1.22298,0.05595 -2.24258,0.77738 -2.24258,1.16607c0,0 -0.76402,0.05595 -1.06909,0.38967c-0.30597,0.33372 -0.81532,1.88848 -0.81532,2.27815s0.25467,3.00155 0.50935,3.5571l-0.30327,0.11091c-0.25467,3.2224 1.0187,3.61207 1.0187,3.61207c0.45805,3.00155 0.91701,1.72358 0.91701,2.50096s-0.45895,0.50059 -0.45895,0.50059s-0.40676,1.22202 -1.42546,1.66666c-1.0187,0.44366 -6.67373,2.83273 -7.13359,3.33331c-0.45985,0.50157 -0.40766,2.83469 -0.40766,2.83469l24.24987,0c0,0 0.05309,-2.33312 -0.40676,-2.83469c-0.46075,-0.50059 -6.11579,-2.88965 -7.13449,-3.33331zm-11.22188,-0.16882c-0.08909,-0.17668 -0.13319,-0.30428 -0.13319,-0.30428s-0.38876,0.23459 -0.38876,-0.42403s0.38876,0.42403 0.77752,-2.11915c0,0 1.07899,-0.3298 0.86301,-3.06142l-0.25917,0c0,0 0.12869,-0.58009 0.21328,-1.30937c-0.0036,-0.30231 0.0054,-0.62426 0.0333,-0.97761l0.0342,-0.41814c-0.0189,-0.48292 -0.09629,-0.92167 -0.28077,-1.20337c-0.64794,-0.98841 -0.90711,-1.64801 -2.33167,-2.11915c-1.42456,-0.47114 -0.90711,-0.37789 -1.94291,-0.3298c-1.0367,0.04711 -1.90061,0.65861 -1.90061,0.98939c0,0 -0.64794,0.04711 -0.90711,0.3298c-0.24388,0.266 -0.63444,1.43501 -0.68123,1.8502l0,0.27581c0.0423,0.64095 0.23218,2.40379 0.42206,2.81898l-0.25737,0.09423c-0.21508,2.73163 0.86301,3.06142 0.86301,3.06142c0.38876,2.54317 0.77752,1.46053 0.77752,2.11915s-0.38876,0.42403 -0.38876,0.42403s-0.34467,1.03749 -1.20858,1.41244c-0.05489,0.02356 -0.12509,0.05497 -0.20878,0.0903l0,5.13738l0.51745,0c-0.0261,-1.25441 0.06929,-2.87297 0.67133,-3.52766c0.32037,-0.34845 1.37056,-0.92167 5.71623,-2.80917zm20.09499,-8.88883c-0.036,-0.37102 -0.11429,-0.7018 -0.26277,-0.92854c-0.64704,-0.98939 -0.90711,-1.64801 -2.33077,-2.11915c-1.42546,-0.47114 -0.90711,-0.37789 -1.94381,-0.3298c-1.0358,0.04711 -1.89971,0.65861 -1.89971,0.98939c0,0 -0.64704,0.04711 -0.90711,0.3298c-0.24388,0.26698 -0.63804,1.44483 -0.68213,1.85609l0.0297,0l0.07199,0.89615c0.018,0.22674 0.0198,0.42795 0.0243,0.63309c0.08099,0.65371 0.18898,1.32508 0.29697,1.55967l-0.25737,0.09423c-0.21508,2.73163 0.86391,3.06142 0.86391,3.06142c0.38876,2.54317 0.77662,1.46053 0.77662,2.11915s-0.38876,0.42403 -0.38876,0.42403s-0.0477,0.13938 -0.14669,0.33176c4.29257,1.86493 5.33377,2.43422 5.65053,2.78169c0.60294,0.65469 0.69743,2.27227 0.67133,3.52766l0.43196,0l0,-5.20805c-0.0144,-0.00589 -0.0342,-0.01472 -0.0468,-0.02061c-0.86301,-0.37593 -1.20858,-1.41244 -1.20858,-1.41244s-0.38966,0.23459 -0.38966,-0.42403s0.38966,0.42403 0.77752,-2.11915c0,0 0.72353,-0.22477 0.86661,-1.80702l0,-1.20435c-0.0009,-0.01767 -0.0009,-0.03239 -0.0027,-0.05006l-0.26007,0c0,0 0.19348,-0.87357 0.26277,-1.82665l0,-1.15429l0.0027,0z" />
                </svg><br><span>Пользователи</span></label></li>
        <li  menu="true" title="Отчеты"><label for="tab4" role="button"><svg viewBox="0 0 24 24"><path d="M14,2A8,8 0 0,0 6,10A8,8 0 0,0 14,18A8,8 0 0,0 22,10H20C20,13.32 17.32,16 14,16A6,6 0 0,1 8,10A6,6 0 0,1 14,4C14.43,4 14.86,4.05 15.27,4.14L16.88,2.54C15.96,2.18 15,2 14,2M20.59,3.58L14,10.17L11.62,7.79L10.21,9.21L14,13L22,5M4.93,5.82C3.08,7.34 2,9.61 2,12A8,8 0 0,0 10,20C10.64,20 11.27,19.92 11.88,19.77C10.12,19.38 8.5,18.5 7.17,17.29C5.22,16.25 4,14.21 4,12C4,11.7 4.03,11.41 4.07,11.11C4.03,10.74 4,10.37 4,10C4,8.56 4.32,7.13 4.93,5.82Z"/>
                </svg><br><span>Отчеты</span></label></li>
        <li  menu="true" title="Логи"><label for="tab5" role="button"><svg viewBox="0 0 24 24">
                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" />
                </svg><br><span>Логи</span></label></li>
    </ul>

    <div class="slider"><div class="indicator"></div></div>
    <div class="content">
        <section>
            <h2>Настройки системы</h2><span id="setting">Выберите раздел <span id="testmodal"> тестовая модалка</span></span></section>
        <section>
            <h2>Адреса объектов</h2>
            <span id="address"><span id="testmodal2"> тестовая модалка</span></span>
           </section>
        <section>
            <h2>Пользователи</h2>
            <span id="users"></span></section>
        <section>
            <h2>Отчеты</h2>
            <span id="reportCall"></span></section>
        <section>
            <h2>Логи</h2>
            <span id="logSystem"></span></section>
    </div>
</div>
<div id="modal"></div>

<script>
    </script>
</body>
</html>

