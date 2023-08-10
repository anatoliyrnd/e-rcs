import {saveButton} from "./save_button.js";
import {main as mainSetting, dialog} from "../main.js";
import {main} from "../addCall.js";
import {fetchDataServer} from "../main.js";

export class setting_system {

    #divSettingAll;
    descriptionUserRow
    #settingForm

    constructor(parentElement) {
        this.#divSettingAll = document.createElement("div");
        parentElement.append(this.#divSettingAll);
        this.dialog = new dialog();
        this.#settingForm = new mainSetting() // форма для окна с основными настройками

    }

    clearSetting() {
        this.#divSettingAll.innerHTML = "";
    }

    addForm() {
        this.#settingForm.creatForm(this.#divSettingAll) // куда вставлять форму
    }

    addRow(data) {
        let text = this.#settingForm.createElementForm(data.type, data.name, data.description, data.value, data.option_name)
        this.#settingForm.addElementFormEnd(text)
        //добавляет строку с названием и значением параметра настроек системы
    }

    addSave(data) {
        const li = document.createElement("li")
        const button = document.createElement('div');
        button.classList.add("button_save", "pulse")
        button.innerText = "Сохранить"
        li.append(button);
        this.addAttribute(data, button)
        this.#settingForm.addElementFormEnd(li)
        button.addEventListener("click",(e)=>{
            let data=this.#settingForm.readForm();
            data.userId = userId;
            data.nacl = nacl;
            data.action="setSettings";
            
            console.log(data);
            fetchDataServer("set_setting.php",data,(event)=>{
                console.log(event);
                event.status==="ok"?window.location.reload(true):null
            })
        })
    }

    creatElementList(name, data, type, fullName) {
        const UL = document.createElement("ul");
        if (type === "city") {
            UL.classList.add("tree");
        }
        const LI = document.createElement("li");
        UL.append(LI)
        let details = document.createElement("details")
        LI.append(details)
        let summary = document.createElement("summary")
        this.addAttribute(data, summary, name)
        summary.setAttribute("data-type", type)
        summary.setAttribute("data-full_name", fullName)
        if (data['vis']) summary.setAttribute("data-block", "true");
        details.append(summary)
        return UL
    }

    echoAddress(data) {
        //console.log(data)
        function buttonAdd(parent, text, dataAttribute, addCity = false) {
            let mainUlADD = document.createElement("ul");
            if (addCity) {
                parent.append(mainUlADD)
            } else {
                parent.firstElementChild.firstElementChild.append(mainUlADD);
            }

            for (const key of Object.keys(dataAttribute)) {

                let attribute = "data-" + key;
                mainUlADD.setAttribute(attribute, dataAttribute[key]);
            }
            mainUlADD.classList.add("add");
            mainUlADD.innerText = text;
        }

        const addressList = new main(data)
        const cityList = addressList.fullListFilter(0, 0)
        let fullAddressForStreet;
        let fullAddressForHome;
        let fullAddressForLift
        for (const keyCity in cityList) {

            let cityListElement = this.creatElementList(cityList[keyCity].city_name, cityList[keyCity], addressList.typeList[0], 'Изменить город')
            this.#divSettingAll.append(cityListElement);
            const streetList = addressList.fullListFilter(cityList[keyCity].id, 1)
            for (const keyStreet in streetList) {
                fullAddressForStreet = cityList[keyCity].city_name;
                const streetElementList = this.creatElementList(streetList[keyStreet].street_name, streetList[keyStreet], addressList.typeList[1], fullAddressForStreet)
                cityListElement.firstElementChild.firstElementChild.append(streetElementList);
                const homeList = addressList.fullListFilter(streetList[keyStreet].id, 2)
                for (const keyHome in homeList) {
                    fullAddressForHome = cityList[keyCity].city_name + " " + streetList[keyStreet].street_name
                    const homeListElement = this.creatElementList(homeList[keyHome].home_name, homeList[keyHome], addressList.typeList[2], fullAddressForHome)
                    streetElementList.firstElementChild.firstElementChild.append(homeListElement);
                    const liftList = addressList.fullListFilter(homeList[keyHome].id, 3)
                    let liftUl = document.createElement("ul")
                    homeListElement.firstElementChild.firstElementChild.append(liftUl)
                    for (const keyLift in liftList) {
                        fullAddressForLift = cityList[keyCity].city_name + " " + streetList[keyStreet].street_name + " № " + homeList[keyHome].home_name;
                        let mainLi = document.createElement("li");
                        if (liftList[keyLift]['vis']) mainLi.setAttribute("data-block", "true")
                        this.addAttribute(liftList[keyLift], mainLi, liftList[keyLift].object_name)
                        mainLi.setAttribute("data-type", addressList.typeList[3])
                        mainLi.setAttribute("data-full_name", fullAddressForLift)
                        liftUl.append(mainLi);

                    }
                    buttonAdd(homeListElement, "Добавить лифт в Дом № " + homeList[keyHome].home_name, {
                        parentType: addressList.typeList[3],
                        action: "add",
                        type: "object",
                        parent: homeList[keyHome].id,
                        parent_name: homeList[keyHome].home_name,
                        full_name: fullAddressForLift
                    })
                }
                buttonAdd(streetElementList, "Добавить дом в  " + streetList[keyStreet].street_name, {
                    parentType: addressList.typeList[2],
                    action: "add",
                    type: "home",
                    parent: streetList[keyStreet].id,
                    parent_name: streetList[keyStreet].street_name,
                    full_name: fullAddressForHome
                })
            }
            buttonAdd(cityListElement, "Добавить улицу в  " + cityList[keyCity].city_name, {
                parentType: addressList.typeList[1],
                action: "add",
                type: "street",
                parent: cityList[keyCity].id,
                parent_name: cityList[keyCity].city_name,
                full_name: fullAddressForStreet
            })
        }
        buttonAdd(this.#divSettingAll, "Добавить город", {
            parentType: addressList.typeList[0],
            action: "add",
            type: "city",
            parent: 0,
            full_name: ''
        }, true)
        this.#divSettingAll.addEventListener("contextmenu", this.editAddress)
        this.#divSettingAll.dialog = this.dialog//добавим ссылку на диалоговое окно
    }

    editAddress(e) {
        //console.log(e.target.dataset)
        const name = {city: "Название города", street: "Название улицы", home: "Номер дома", object: "Лифт"}
        let data = e.target.dataset
        const dialog = e.currentTarget.dialog
        let inputName;
        e.preventDefault()
        dialog.showModal();
        dialog.clearDialog();
        dialog.creatForm(dialog.body)

        if (data.action === "add") {
            dialog.titleTxt = "Добавить  в " + data["full_name"];
            dialog.setDataAttribute({action: "add", parent_id: data.parent, type: data.name})
            const inputNew = dialog.createElementForm("text", name[data.type], 'Введите ' + name[data.type], null)
            dialog.addElementFormEnd(inputNew)
        } else {
            let nameKey = data.type + "_name"
            dialog.titleTxt = data["full_name"];
            dialog.setDataAttribute({
                action: "edit",
                type: data.type,
                id: data.id,
                oldValue: data[nameKey],
                oldVis: data.vis
            })
            const inputName = dialog.createElementForm('text', name[data.type], "Измените поле выше ", data[nameKey], "name")
            dialog.addElementFormEnd(inputName)
            const hidden = dialog.createElementForm("checkbox", "Скрытие у диспетчера", "Поставьте галочку если объект НЕ должен показываться у диспетчера при выборе адреса", data.vis === "1", "vis")
            dialog.addElementFormEnd(hidden)
            //const number=dialog.createElementForm("select","test ", "testovoe",null,"test")
            //dialog.addElementFormEnd(number)
        }
//dialog.bodyHTML = dialog.getForm(form);
    }

    permissionUserEcho(permission, parent, description) {
        const permissionKey = ['user_localadmin', 'user_block', 'user_disppermission', 'user_edit_obj', 'user_edit_user', 'user_telegram', 'user_read_all_calls', 'user_hiden']

        for (const key of permissionKey) {
            permission[key] ? parent.append(addIco(key, description[key].description)) : null;
        }

        function addIco(name, title) {

            const img = document.createElement("img")

            img.classList.add('userDescriptionIcon')
            img.setAttribute("data-tooltip", title)
            console.log(name)
            img.setAttribute("src", '/ico/' + name + '.png')
            return img
        }

        return "description"
    }

    creatUserRow(users, descriptions) {
        const addButton = document.createElement("div")
        addButton.classList.add("uiverse");
        const spanTooltip = document.createElement("span")
        spanTooltip.innerText = "Добавить пользователя"
        spanTooltip.classList.add("tooltip")
        addButton.append(spanTooltip)
        const spanAdd = document.createElement("span")
        spanAdd.innerText = "+";
        addButton.append(spanAdd);

        const mainDiv = document.createElement("div");
        mainDiv.append(addButton)
        const ulUsers = new Array();
        for (const index in users) {
            ulUsers[index] = document.createElement("ul");
            mainDiv.append(ulUsers[index]);
            ulUsers[index].classList.add("users");
            const liName = document.createElement("li");
            ulUsers[index].append(liName);
            ulUsers[index].info = users[index]
            liName.innerText = users[index].user_name;
            const liDescription = document.createElement("li");
            ulUsers[index].append(liDescription);
            this.permissionUserEcho(users[index], liDescription, descriptions)

        }

        this.descriptionUserRow = descriptions
        this.#divSettingAll.append(mainDiv);

        // обработка клика по кнопке добавить пользователя
        addButton.addEventListener("click", (e) => {
            e.stopPropagation()
            this.dialog.clearDialog();
            this.dialog.creatForm(this.dialog.body)
            this.dialog.titleTxt = "Добавить нового пользователя"
            this.dialog.setDataAttribute({action: "addUser"})
            let input = new Array();
            for (const key in this.descriptionUserRow) {
                const description = this.descriptionUserRow[key]
                let index = description.display_order //порядок отображения
                if (!description.editable) continue;
                input[index] = this.dialog.createElementForm(description.type, description.text, description.description, '', key, {0: "не указан"})
            }
            for (const key in input) {
                this.dialog.addElementFormEnd(input[key])
            }
            this.dialog.showModal()
            this.dialog.saveClick((e) => {
                let data = this.dialog.readForm();
                data.action = e.target.dataset.action
                data.userId = userId;
                data.nacl = nacl;
                data.id=e.target.dataset.id
                console.log(data, JSON.stringify(data))
                fetchDataServer('set_setting.php', data, (e) => {
                    console.log(this,e)
                    this.dialog.toastShow(e)
                })
            })
        })
        // делегирование клика по полю с именами пользователей
        mainDiv.addEventListener("click", (e) => {
            //редактирование пользователя
            this.dialog.clearDialog();
            this.dialog.creatForm(this.dialog.body)
            if (e.target.tagName !== "LI") return;
            this.dialog.titleTxt = "редактировать пользователя " + e.target.parentNode.info.user_name;
            this.dialog.setDataAttribute({action: "editUser", id: e.target.parentNode.info.user_id})
             let input = new Array()
            for (const [key, value] of Object.entries(e.target.parentNode.info)) {
                if(key==='user_id')continue;
                const description = this.descriptionUserRow[key]
                let index = description.display_order //порядок отображения
                input[index] = this.dialog.createElementForm(description.type, description.text, description.description, value, key, {0: "не указан"}, description.editable)
            }
            for (const key in input) {
                this.dialog.addElementFormEnd(input[key])
            }
            this.dialog.showModal()
            this.dialog.saveClick((e) => {
                let data = this.dialog.readForm();
                data.action = e.target.dataset.action
                data.userId = userId;
                data.nacl = nacl;
                data.id=e.target.dataset.id
                console.log(data, JSON.stringify(data))
                fetchDataServer('set_setting.php', data, (e) => {
                    console.log(this,e)
                    this.dialog.toastShow(e)
                })
            })
        })

    }

    addAttribute(data, element, text = '') {
        for (const key in data) {
            let attributeName = "data-" + key;
            element.setAttribute(attributeName, data[key])
            if (text.length >= 1) {
                element.innerText = text
            }
        }

    }

    activationSave() {
        this.setAttribute("modified", true);
        this.setAttribute("data-value", this.value)
        let button = new saveButton();
        button.display()
        button.text = "Сохранить"

    }

    generateBodyDialogAddressEdit(data, dialog) {

    }

}