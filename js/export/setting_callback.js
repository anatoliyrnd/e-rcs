import {setting_system} from "./setting/seting_system.js";

// callback функции по клику на пункты основного меню
export const settingCallback ={
   setting: function(data){
       if (data.status==='error'){
       alert (data.message);
       return false;
   }
        const settingElement=document.getElementById("setting");
         settingElement.innerHTML='';
       let setting=new setting_system(settingElement);
       setting.addForm();
           for (const index in data.message) {
           setting.addRow(data.message[index])
       }
           setting.addSave({nacl:nacl,userId:userId,action:"saveSettings"},"Сохранить","set_setting.php")

    },
users: function(usersData)
{if (usersData.status==='error'){
    alert (usersData.message);
    return false;
}
    console.log(usersData)
    const usersElement=document.getElementById("users");
    usersElement.innerHTML='';
    let users=new setting_system(usersElement);
    users.creatUserRow(usersData.message.data,usersData.message.descriptions)
    //users.descriptionUserRow=usersData.message.descriptions




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
    },
    reportCall: function(data){
const reportElement=document.getElementById('reportCall')
        reportElement.innerHTML=''
        if (data.status==='error'){
            alert (data.message);
            return false;
        }
        let report=new setting_system(reportElement);
        report.report(data.message)
        report.addSave({nacl:nacl,userId:userId,action:"getReport"},"Сформировать","report.php",true,true)
    },
    logSystem: function(data){

    }
}
