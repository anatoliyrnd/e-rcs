document.addEventListener("DOMContentLoaded", readyDOM);
import {dialog, fetchDataServer} from "./export/main.js";
import {settingCallback} from "./export/setting_callback.js";
let bodyElement;

function readyDOM() {
    const urlSetting=URI+"/get_setting.php";
    const tabsElement=document.getElementById("tabs");
    const divModal = document.getElementById("modal");
   // const modal = new dialog();
    const tab2 = document.getElementById("users");
    tabsElement.addEventListener("click",clickSettings);
function clickSettings(event){
    let dataEl=event.target
   if (typeof (dataEl.dataset.action)=="undefined" || !dataEl.dataset.action){return false}
   if (dataEl.dataset.action==='get'){
       bodyElement=document.getElementById(dataEl.dataset.type);
       bodyElement.innerHTML='Получаем данные..';
       console.log(dataEl.dataset.type);
       fetchDataServer(urlSetting,{action:"get",type:dataEl.dataset.type,userId:userId, nacl:nacl},settingCallback[dataEl.dataset.type])
   }
    if (dataEl.dataset.action==='editMainSetting'){
        //клик по полю с атрибуто edit
        console.log(dataEl);
    }

}
    const testmodal = document.getElementById("testmodal");
    const testmodal2 = document.getElementById("testmodal2");
let div=document.createElement("div");
div.innerText="divtest";
tab2.append(div);
testmodal2.addEventListener("click",()=>{
    div.remove();
})




    const test = function (event) {
        console.log(event.target);
        let url=URI+"/get_setting.php";
        fetchDataServer(url,{action:"get",type:"getConfigSetting",userId:userId, nacl:nacl},settingCallback.loadConfigSetting)

    }


    function modalOpen() {

       modal.clearDialog();
        modal.titleTxt = "title";
        modal.bodyHTML = "<h1>body</h1>";
        //modal.saveClick(settingCallback.loadConfigSetting);
       modal.showModal();
    }

    testmodal.addEventListener("click", modalOpen);
}