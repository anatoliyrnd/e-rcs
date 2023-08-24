export function loadData(object,action,data){
    const text={"loadStartData":"Загружаем конфигурацию","loadAddress":"Загружаем базу адресов"}
    const textResult={"loadStartData":"Конфигурация загружена","loadAddress":"База адресов загружена"}
    const textError={"loadStartData":"Ошибка загрузки конфигурации","loadAddress":"Ошибка загрузки базы адресов"}
    object.action = action;
    object.URL = "./calls.php";
    object.actionText=text[action];
    let idElement = object.TextElement;
    object.loadData((result) => {
        idElement.innerText = textResult[action];
        idElement.removeAttribute("loading");
        //  headLoader.hidden = true;
        if (result.status === "ok") {
           if(result.action==="loadConfig") {
               data.nav = result.message.nav;
               data.selectData = result.message;

           }
           if(result.action==="loadAddress"){
               data.addressData=result.message;
           }
        } else {
              idElement.innerText =  result.message +textError[action];
             }

    })
}
