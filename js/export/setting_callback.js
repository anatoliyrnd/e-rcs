import {setting_system} from "./setting/seting_system.js";
import {saveButton} from "./setting/save_button.js";

export const settingCallback ={
   setting: function(data){
       const button=new saveButton();
       const settingElement=document.getElementById("setting");
       console.log(button)
button.hidden();
       settingElement.innerHTML='';
       let setting=new setting_system(settingElement);
           for (const index in data.message) {
           setting.addRow(data.message[index])
       }
       if (data.status==='error'){
           alert (data.message);
           return false;
       }


    },
users: function(data){
    const usersElement=document.getElementById("users");
    usersElement.innerHTML='';
    let users=new setting_system(usersElement);
    for (const index in data.message){
        users.echoUser(data.message[index])
    }
    if (data.status==='error'){
        alert (data.message);
        return false;
    }
    if (data.status==='error'){
        alert (data.message);
        return false;
    }
    },

    address: function(data){
const addressElement=document.getElementById("address");
addressElement.innerHTML='';
if (data.status==='error'){
            alert (data.message);
            return false;
        }
let address=new setting_system(addressElement);
        address.echoAddress(data.message)
for (const index in data.message){
    //address.echoAddress(data.message[index])
}


    },
    reportCall: function(data){

    },
    logSystem: function(data){

    }
}
