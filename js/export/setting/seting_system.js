import {saveButton} from "./save_button.js";
import {address} from "./address.js";
import {fetchDataServer} from "../main.js";
import {main} from "../addCall.js";

export class setting_system {

    #divSettingAll;

  constructor(parentElement) {
      this.#divSettingAll=document.createElement("div");
      parentElement.append( this.#divSettingAll);
        }

    clearSetting () {
      this.#divSettingAll.innerHTML="";
    }
    addRow(data){
      //добавляет строку с названием и значением параметра настроек системы
        this.#divSettingAll.classList.add("grid-container");
    const divItem=document.createElement("div");
    divItem.classList.add("grid-item");
    const divCard=document.createElement("div");
    divCard.classList.add("card");
      const name=document.createElement('label');
      name.classList.add("card-label");
      const input=document.createElement("input");
      input.classList.add("card-content");
      input.value=data.value;
      name.innerText=data.name;
     let size=16-Math.round(data.name.length/10);
     if (size<10){size=10}
     if(size>15){size=15}
      let fontSize=String(size)
        name.setAttribute("style","font-size:"+fontSize+"px;");
        console.log(fontSize)
     this.#divSettingAll.append(divItem);
     divItem.append(divCard)
     divCard.append(name);
     divCard.append(input);
        for (const key in data) {
            console.log(data[key])
            if (data[key]==="number"){
                input.setAttribute("type","number");
                input.setAttribute("min","1");

            }else {
                let attributeName = "data-" + key;
                if(key==="error"){input.disabled=true}
                input.setAttribute(attributeName, data[key])
            }
           }
        input.addEventListener('input',this.activationSave);
    }
echoAddress (data){
 function creatElementList(name,city=false){
     const UL=document.createElement("ul");
     if (city) {
         UL.classList.add("tree");
     }
     const LI=document.createElement("li");
     UL.append(LI)
     let details=document.createElement("details")
     LI.append(details)
     let summary=document.createElement("summary")
     summary.innerText=name;
     details.append(summary)
     return UL
 }
 function buttonAdd(parent,text,dataAttribute,addCity=false){
     let mainUlADD=document.createElement("ul");
     if(addCity){
         parent.append(mainUlADD)
     }else {
         parent.firstElementChild.firstElementChild.append(mainUlADD);
     }

     for (const key of Object.keys(dataAttribute)) {

         let attribute="data-"+key;
         mainUlADD.setAttribute(attribute,dataAttribute[key]);
     }
     mainUlADD.classList.add("add");
     mainUlADD.innerText=text;
 }
const mainUl=[];
    const addressList=new main(data)
 const cityList=addressList.fullListFilter(0,0)
        for (const key in cityList){
          let cityListElement=creatElementList(cityList[key].city_name,true)
            this.#divSettingAll.append(cityListElement);
            console.log(cityListElement.children)
            const streetList=addressList.fullListFilter(cityList[key].id,1)
            for (const key in streetList) {
                const  streetElementList=creatElementList(streetList[key].street_name)
                cityListElement.firstElementChild.firstElementChild.append(streetElementList);
                const homeList=addressList.fullListFilter(streetList[key].id,2)
                for (const key in homeList) {
                    const homeListElement=creatElementList(homeList[key].home_name)
                    streetElementList.firstElementChild.firstElementChild.append(homeListElement);
                    const liftList=addressList.fullListFilter(homeList[key].id,3)
                    let liftUl=document.createElement("ul")
                    homeListElement.firstElementChild.firstElementChild.append(liftUl)
                    for (const key in liftList) {
                        let mainLi=document.createElement("li");
                        liftUl.append(mainLi);
                        mainLi.innerText=liftList[key].object_name;
                    }
                    buttonAdd(homeListElement,"Добавить лифт в Дом № "+homeList[key].home_name,{type:"add_lift",parent:homeList[key].id,parent_name:homeList[key].home_name})
                }
                buttonAdd(streetElementList,"Добавить дом в  "+streetList[key].street_name,{type:"add_home",parent:streetList[key].id,parent_name:streetList[key].street_name})
            }
            buttonAdd(  cityListElement,"Добавить улицу в  "+cityList[key].city_name,{type:"add_street",parent:cityList[key].id,parent_name:cityList[key].city_name} )
                       }
        buttonAdd(this.#divSettingAll,"Добавить город", {type:"add_city",parent:0},true)



    this.#divSettingAll.addEventListener("click",(e)=>{

        console.log(e.target.dataset)

    })

}
echoUser(data){
    const mainDiv=document.createElement("div");
    const nameDiv=document.createElement("div");
    mainDiv.append(nameDiv);
    this.#divSettingAll.append(mainDiv);
    for (const key in data){
        let attributeName="data-"+key;
        nameDiv.setAttribute(attributeName,data[key])
        nameDiv.innerText=data.user_name;
    }
    nameDiv.addEventListener("click",(e)=>{
        console.log(e.target.dataset)

    })
}
    activationSave(){
        this.setAttribute("modified",true);
        let button=new saveButton();
        button.display();

            }

}