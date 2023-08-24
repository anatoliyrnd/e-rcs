 export const dispCallback={
 loadStartSata:function(result) {
     idConfig.innerText = "Конфигурация загруженна!";
     idConfig.setAttribute("data-loading","false");
     //  headLoader.hidden = true;
     if (result.status === "ok") {
         nav = result.message.nav;
         selectData = result.message;
     } else {
         headMessage.innerHTML =
             result.message +
             "<br> Конфигурационные данные не загружены с сервера, будет предпринята попытка получить последние локальные данные ";
     }

 }
}