import {dialog,fetchDataServer} from "../main.js";
export function  address(data){

const dialogWindow=new dialog();
const cityId=data.id;
dialogWindow.clearDialog();
dialogWindow.showModal();
dialogWindow.titleTxt=data.city_name+" ID - "+cityId;

function creatInputEdit(){

}


}