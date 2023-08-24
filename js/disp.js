import {tableOpen, tableClose} from "./export/tabulatconfig.js";
//import { createList } from "./export/class.js";
import {startAddressSelect, Select, timerCountDown} from "./export/addCall.js";
import {main} from "./export/main.js";
import {loadConfig} from "./export/disp/loadConfig.js";
import {loadData, hardWareInfo} from "./export/disp/dispFunction.js";

let webGL = hardWareInfo();
const headMessage = document.getElementById("head_message");
const headLoader = document.getElementById("loader_head");
const timeLoadDataDefoult = 20000
let timeLoadData = timeLoadDataDefoult;// Интервал обновления данных
let numErrorReload;// счетчик ошибок авторизации
const actionLoadConfig = ['loadStartData', 'loadAddress']
//массив с конфигурационными данными
const configData = {
    nav: [],
    selectData: {
        department: {},
        group: {},
        request: {},
        staff: {},
        repair_time: {}
    },
    addressData: {}
};
let config = new loadConfig(headMessage, headLoader);
setInterval(() => {
    loadConfig.fetchData("calls.php", {"action": "checkNewConfig"}, function (result) {
        if (Number(result.message.force_reload) === 1) document.location.reload(true);
        let action = Number(result.message.data_reload);
        if (action) headMessageEcho('', 20000)
        if (action === 1) loadData(config, actionLoadConfig[0], configData)
        if (action === 2) loadData(config, actionLoadConfig[1], configData)
        if (action === 3) {
            loadData(config, actionLoadConfig[0], configData);
            loadData(config, actionLoadConfig[1], configData)
        }
    })
}, 100000)
//наблюдатель за активацией начала загрузки конфигурационных данных или списка адресов
let observerLoadData = new MutationObserver(header);
observerLoadData.observe(headMessage, {attributes: true, subtree: true})

function header(event) {

    let loadingData = false; //flag наличия загрузки каких то данных конфигурации
    event[0].target.parentNode.childNodes.forEach((element) => {
        if (element.nodeName !== "SPAN") return;
        let loading = element.hasAttribute("loading")
        if (loading) {
            loadingData = true;
        }
    })
    headLoader.hidden = !loadingData;
    if (!loadingData) {

    }
}

{
    loadData(config, actionLoadConfig[0], configData)//загрузим основную конфигурацию
    loadData(config, actionLoadConfig[1], configData)//загрузим базу адресов
    setTimeout(() => {
        document.getElementById("open_calls_table").hidden = false
    }, 1000);
}
const svgstaff =
    '<span class="checkbox__checker"></span><span class="checkbox__txt-left">Да</span><span class="checkbox__txt-right">Нет</span><span class="checkbox__bg"><?xml version="1.0" ?><svg  viewBox="0 0 110 43.76" xmlns="http://www.w3.org/2000/svg"><path d="M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z"/></svg><span>';
//global variabl
const bar = document.getElementById("countdownBar");
let changeCall = new Map(); // массив с внесенными изменениями
const alertCapsUnblockAudio = true; // флаг возможности воспроизвести сообщение о включенном Капслок
const alertENUnblockAudio = true; // флаг возможности воспроизвести сообщение об английской раскладке
const audioCapslock = new Audio("audio/capslock.mp3"); // файл с сообщением о фключенном капслок
const audioENKeyboard = new Audio("audio/enkeybord.mp3"); // файл с сообщением об  английской раскладке
const closeDialog = document.getElementById("close"); //кнопка закрытия модального окна
const saveDialog = document.getElementById("save"); //кнопка сохранить - модального окна
const titleDialog = document.getElementById("title_dialog"); // заголовок модального окна
const dialog = document.querySelector("dialog"); //модальное окно
const bodyDialog = document.getElementById("body_dialog"); // тело модального окна
const menuListModal = document.getElementById("menu_madal"); // список меню для модального окна
let resetModalTimeOut = '';// ссылка на таймер для автоматического закрытия модалки
const toggle_head = document.getElementById("toggle_head"); //кнопка главного меню
const mainBody = document.getElementById("main_body"); // контент главнорго окна
const spinerDialog = '<div class="lds-dual-ring" id="spinerDialog"></div>'; // спинер кнопки сохранить диалогового окна
const menu = document.getElementById("menu").getElementsByTagName("ul")[0];
const help = document.getElementById("help");
let quantityCalls = {open: 0, close: 0}; // массив количества заявок
let address = null; //объект для генерации адреса новой заявки
document.addEventListener("click", clickMouse)
const timerModal = new timerCountDown(document.getElementById("closeTimer"), 600, modalClose, 'time');// timer button close modal
const timerDownCloseCalls = new timerCountDown(bar, 120, viewMainBody, 'bar');// top  timer
help.addEventListener("click", (event) => {
        openRequestedTab('/help_disp.html', 'Краткая видеоинструкция по работе с системой для диспетчера ');
        event.preventDefault();
    },
    false);
let windowObjectReference = null; // global variable
function openRequestedTab(url, windowName) {
    if (windowObjectReference === null || windowObjectReference.closed) {
        windowObjectReference = window.open(url, windowName, 'popup');
    } else {
        windowObjectReference.focus();
    }
}

function clickMouse() {
    timerDownCloseCalls.reset();
}

function modalClose() {
    //подчищаем все перед закрытием модалки
    address = null;
    bodyDialog.innerHTML = '';
    if (resetModalTimeOut) {
//если есть таймер для закрытия модалки то удаляем его
        clearTimeout(resetModalTimeOut);
    }
    timerModal.changeTime(0);
    if (document.getElementById("menu_dialog-modal").checked)
        clickButton("menu_dialog-modal");
    if (closeDialog.hasAttribute("body")) bodyDialog.innerHTML = "";
    saveDialog.disabled = true;
    saveDialog.innerHTML = "Сохранить";
    titleDialog.innerHTML = "";
    changeCall.clear();
    alertCapsUnblockAudio = true;
    alertENUnblockAudio = true;
    saveDialog.removeAttribute("action");
    if (menuListModal.querySelector("ul")) {
        let ul = menuListModal.querySelector("ul");
        ul.remove();
    }
    dialog.close();
}

for (const list of menu.querySelectorAll("li")) {
    list.addEventListener("click", clickMenu);
}
setInterval(() => {
    //обновляем данные с сервера
    tableOpen.replaceData();
    tableClose.replaceData();
}, timeLoadData);
saveDialog.addEventListener("click", savecall, false);
closeDialog.addEventListener("click", modalClose);
dialog.addEventListener("cancel", modalClose);

function clickMenu() {
    //console.log(this.getAttribute("name"));
    toggle_head.classList.remove("open");
    document.getElementById("menu").classList.remove("opened");
    timerDownCloseCalls.stop();
    switch (this.getAttribute("name")) {
        case "clickMenu-newCall":
            callNew();
            break;
        case "clickMenu-openCalls":
            viewMainBody("open");
            break;
        case "clickMenu-closeCalls":
            viewMainBody("close");
            break;

        default:
            break;
    }
}

function viewMainBody(type = "open") {
    const title = {
        open: "открытые заявки",
        close: "закрытые заявки",
    };
    if (type === 'close') timerDownCloseCalls.startCountdown();
    let list = mainBody.querySelectorAll(".mainchild");
    list.forEach((element) => {
        //console.log(element.getAttribute("name"), type);
        if (element.getAttribute("name") == type) {
            element.hidden = false;
        } else {
            element.hidden = true;
        }
        headMessageEcho(
            `Сейчас Вы просматриваете ${title[type]}<br>Открытых заявок - ${quantityCalls.open} . Закрытых за последние 24 час - ${quantityCalls.close}`
        );
    });
}

function callNew(data = {nodata: true}) {

    saveDialog.setAttribute("action", "callnew")
    address = new startAddressSelect(titleDialog, bodyDialog, configData.addressData, next)
    address.city()// выбирем адрес
    function next(addressCall) {
        //получим массив с адресом по заявке
        changeCall.set("city", addressCall[0]);
        changeCall.set("street", addressCall[1]);
        changeCall.set("home", addressCall[2]);
        changeCall.set("object", addressCall[3]);
        changeCall.set("fullAdress", addressCall[4]);
        address = null;
        titleDialog.innerHTML = addressCall[4];
        bodyDialog.innerHTML = "";
        callNewStep2();
    }

    showDialog(false, false, 1200)
}

function callNewStep2() {
    timerModal.changeTime(1200);
    // создание новой заявки шаг 2 выбор разделов и срока ремонта
    let buttonNext = document.createElement("button");
    let divContainer = document.createElement("div");
    divContainer.classList.add("grid-container");
    buttonNext.addEventListener("click", callNewStep3, true);
    buttonNext.classList.add("button_next");
    buttonNext.innerText = "Далее";
    buttonNext.disabled = true;
    // создание новой заявки шаг 2 выбор отдела уровня и
    const selectList = {
        group: "Группа",
        request: "Уровень",
        department: "Отдел",
        repair_time: "Срок предпологаемого ремонта",
    };
    creatSelectCard(selectList, divContainer, buttonNext, 4);
    bodyDialog.append(divContainer);
    bodyDialog.append(buttonNext);
}

function callNewStep3() {
    timerModal.changeTime(1200);
    //создание новой заявки шаг 3 описание заявки
    bodyDialog.innerHTML = "";
    let body = document.createElement("div");
    body.classList.add("grid_close");
    let textarea = document.createElement("textarea");
    textarea.name = "details";
    textarea.addEventListener("input", checkTextarea, false);
    textarea.classList.add("textarea_close");
    body.innerHTML = "<div class='label_close'>Введите описание заявки</div>";
    body.append(textarea);
    bodyDialog.append(body);
    bodyDialog.append(document.createElement("hr"));
    bodyDialog.append(
        (document.createElement("span").innerHTML =
            "Параметры ниже можно заполнить позднее")
    );
    bodyDialog.append(document.createElement("hr"));

    //для поля уведомление ответсвственного
    let divStaff = document.createElement("div");
    divStaff.classList.add("staff_checkbox");
    let labelStaff = document.createElement("label");
    labelStaff.classList.add("checkbox__container");
    let inputStaff = document.createElement("input");
    inputStaff.setAttribute("type", "checkbox");
    inputStaff.checked = false;
    inputStaff.disabled = true;
    inputStaff.classList.add("checkbox__toggle");
    inputStaff.addEventListener("change", function () {
        changeCall.set("staff_status", this.checked);
    });

    labelStaff.innerHTML = svgstaff;
    labelStaff.prepend(inputStaff);
    divStaff.append(labelStaff);
    let divContainer = document.createElement("div");
    divContainer.classList.add("grid-container");
    let cardContent = cardCreat(divContainer, "Уведомлен по телефону");
    cardContent.append(divStaff);
    //выбор ответсвенного по заявке

    const selectlist = {
        staff: "Ответсвенный по заявке",
    };
    creatSelectCard(selectlist, divContainer, inputStaff, 1);
    bodyDialog.append(divContainer);
}

function creatSelectCard(selectList, parent, button, length = 0) {
    //создаем список выбора
    let control = [];
    for (const key in selectList) {
        const element = selectList[key];
        console.log(configData.selectData[key]);
        let cardContent = cardCreat(parent, element);
        let select = new Select(key, "select");
        select.appendTo(cardContent, configData.selectData[key], 0, function () {
            this.classList.add("change_select");
            control[key] = true;
            changeCall.set(key, this.value);
            if (Object.keys(control).length >= length) {
                button.disabled = false;
            }
        });
    }
}

function cardCreat(parent, labelText, content = "") {
    //создание ячейки сетки
    let div = document.createElement("div");
    div.classList.add("grid-item");
    let card = document.createElement("div");
    card.classList.add("card");
    let cardContent = document.createElement("div");
    cardContent.classList.add("card-content");
    let label = document.createElement("label");
    label.classList.add("animate__fadeIn");
    label.classList.add("card_label");
    label.innerText = labelText;
    card.append(cardContent);
    card.append(label);
    div.append(card);
    parent.append(div);
    cardContent.innerHTML = content;
    return cardContent;
}


function callViewer(data) {
    //создание модального окна для просмотра заявки
    let title = data["adress"];
    title += data['type'] === 'close' ? "<span style='color:red'> Заявка закрыта</span>" : " Просмотр";

    let body = generateBodyDialog(data, 0);
    bodyDialog.append(body);
    if (data["type"] === "open") {
        let ul = generatemenu(data, 0);
        menuListModal.append(ul);
    }

    saveDialog.removeAttribute("action");
    showDialog(title, true, 1200);
    saveDialog.disabled = true;
}

function callEdit(data) {
    changeCall.set("call_id", data.id);
    //создание модального окна для редактирования заявки
    let title = data["adress"] + " Редактирование";
    let ul = generatemenu(data, 1);
    let body = generateBodyDialog(data, 1);
    bodyDialog.append(body);
    menuListModal.append(ul);
    saveDialog.setAttribute("action", "calledit");
    showDialog(title, true);
}

function callClose(data) {
    changeCall.set("call_id", data.id);
    //создание модального окна для закрытия заявки
    let title = data["adress"] + " Закрытие";
    let ul = generatemenu(data, 2);
    let body = document.createElement("div");
    body.classList.add("grid_close");
    let textarea = document.createElement("textarea");
    textarea.name = "call_close";
    textarea.addEventListener("input", checkTextarea, false);
    textarea.classList.add("textarea_close");
    body.innerHTML = "<div class='label_close'>Введите решение по заявке</div>";
    body.append(textarea);
    bodyDialog.append(body);
    menuListModal.append(ul);
    saveDialog.setAttribute("action", "callclose");
    showDialog(title);
}

function callNote(data) {
    //создание модального окна для заметок к заявке
    let title = data["adress"] + " Заметки";
    let ul = generatemenu(data, 3);
    menuListModal.append(ul);
    saveDialog.setAttribute("action", "callnote");
    let body = cardTemplateNote(data);
    bodyDialog.append(body);
    showDialog(title);
}

function showDialog(title = '', body = '', timer = 600) {
    //окончательная сборка модалки и ее вывод на экран
    if (title) {
        titleDialog.innerHTML = title
    }
    ;

    if (body) {
        closeDialog.setAttribute("body", body)
    }
    ;
    //запускаем диологовое окно

    timerModal.startCountdown(timer);
    dialog.showModal();
}

function generatemenu(data, type) {
    //Функция генерации меню для модального окна
    let typetext = [
        "Просмотр заявки",
        "Редактировать заявки",
        "Закрыть заявку",
        "Заметки к заявке",
        "Создать новую заявку",
    ];

    let functioncall = [callViewer, callEdit, callClose, callNote, callNew];
    let ul = document.createElement("ul");
    ul.classList.add("menu_dialog");
    for (let key = 0; key < typetext.length; key++) {
        console.log(configData.nav[key]);
        if (key == type) continue; //если уже находимся в нужном месте
        if (!configData.nav[key]) continue; // если пользователю запрещен доступ к этому поункто то идем далее
        new ModalMenuItem(typetext[key]).appendTo(ul, function () {
            ul.remove();
            modalClose();
            functioncall[key](data);
        });
    }
    return ul;
}

class ModalMenuItem {
    // класс пункта меню
    constructor(name) {
        this.li = document.createElement("li");
        this.li.innerText = name;
    }

    appendTo(parent, callback) {
        parent.append(this.li);
        this.li.addEventListener("click", callback);
    }
}

// функция эмуляции клика
function clickButton(id) {
    document.querySelector("#" + id).click();
}

function generateBodyDialog(data, type) {
    //<div class="grid-item">1</div>
    let card = false;
    let divContainer = document.createElement("div");
    divContainer.classList.add("grid-container");
    for (let index in data) {
        let div = document.createElement("div");
        div.classList.add("grid-item");
        console.log(index, data[index], data)
        if (type === 0) card = cardtemplate(index, data[index], data['type']);
        if (type === 1) card = cardTemplateEdit(index, data);
        if (!card) continue;
        div.append(card);
        divContainer.append(div);
    }
    return divContainer;
}

function cardTemplateNote(data) {
    changeCall.set("call_id", data.id);
    let divReturn = document.createElement("div");
    let divContainer = document.createElement("div");
    divContainer.classList.add("grid-container");
    divReturn.append(divContainer);
    if (Number(data.note_num) !== 0) {
        for (const key in data.note) {
            if (Object.hasOwnProperty.call(data.note, key)) {
                const element = data.note[key];
                console.log(element);
                let cardContent = cardCreat(
                    divContainer,
                    element.user + " - " + element.date
                );
                if (element.type == "1") {
                    cardContent.innerHTML = element.body;
                }
                if (element.type == "2") {
                    const link = "note_images.php?id=" + element.id;
                    cardContent.innerHTML = "<a href='" + link + "' target='blank'><img src='" + link + "&type=thumb'></a>";
                }
            }
        }
    }
    //console.log(data);
    bodyDialog.innerHTML = "";
    let body = document.createElement("div");
    body.classList.add("grid_close");
    let textarea = document.createElement("textarea");
    textarea.name = "note";

    textarea.addEventListener("input", checkTextarea, false);
    textarea.classList.add("textarea_close");
    body.innerHTML = "<div class='label_close'>Введите новую заметку</div>";
    body.append(textarea);
    divReturn.append(body);
    return divReturn;
}

function cardtemplate(labelindex, data, type) {
    //Генерация карточек к заявке
    //console.log(data);
    let name = {};
    if (type === "open") {
        name = {
            details: "Описание заявки",
            open_name: "Открыл заявку",
            date: "Дата открытия",
            staff_date: "Дата уведомления ответственного",
            repair_time: "Предполагаемая дата ремонта",
            staff: "Ответсвенный",
            department: "Отдел",
            group: "Группа",
            request: "Уровень",
        };
    } else {
        name = {
            details: "Описание заявки",
            solution: "Решение",
            open_name: "Открыл заявку",
            date: "Дата открытия",
            close_name: "Закрыл заявку",
            close_date: "Дата закрытия",
            repair_time: "Предполагаемая дата ремонта",
            staff: "Ответсвенный",
            department: "Отдел",
            group: "Группа",
            request: "Уровень",
            full_history: "История изменений",
        }
    }
    if (!name.hasOwnProperty(labelindex)) return false;

    let card = document.createElement("div");
    card.classList.add("card");
    let cardContent = document.createElement("div");
    cardContent.classList.add("card-content");
    cardContent.innerHTML = data;
    let label = document.createElement("label");
    label.classList.add("animate__fadeIn");
    label.classList.add("card_label");
    label.innerText = name[labelindex];
    card.append(cardContent);
    card.append(label);
    return card;
}

function cardTemplateEdit(index, fulldata) {
    //генерим тело модалки для редактирования
    //<div class="card"><div class="card-content"></div><label class="animate__fadeIn" >label</label></div>
    let name = {
        staff_status: "Статус уведомления ответственного",
        details: "Описание заявки",
        open_name: "Открыл заявку",
        date: "Дата открытия",
        staff_date: "Дата уведомления ответственного",
        repair_time: "Предполагаемая дата ремонта",
        staff: "Ответсвенный",
        department: "Отдел",
        group: "Группа",
        request: "Уровень",
    };
    let cardContent;
    if (!name.hasOwnProperty(index)) return false; //если данные не из нужного списка  - выходим

    let card = document.createElement("div");
    card.classList.add("card");
    if (index === "details") {
        cardContent = document.createElement("textarea");
        cardContent.classList.add("card-content");
        cardContent.classList.add("textarea");
        cardContent.value = fulldata["details"];
        cardContent.name = "details";
        cardContent.addEventListener("input", checkTextarea, false);
    } else {
        cardContent = document.createElement("div");
        cardContent.classList.add("card-content");
    }
    let label = document.createElement("label");
    label.classList.add("animate__fadeIn");
    label.classList.add("card_label");
    label.innerText = name[index];

    if (
        index === "staff" ||
        index === "department" ||
        index === "group" ||
        index === "request" ||
        index === "repair_time"
    ) {
        //генерируем список select для этих полей
        let select = new Select(index, "select");
        if (index === "repair_time") {
            configData.selectData.repair_time[0] = fulldata["repair_time"];
            fulldata["repair_time_id"] = 0;
        }

        select.appendTo(
            cardContent,
            configData.selectData[index],
            fulldata[index + "_id"],
            function () {
                if (this.value != fulldata[index + "_id"]) {
                    //проверим изменено ли значение
                    this.classList.add("change_select");
                    changeCall.set(index + "_id", this.value);
                    if (saveDialog.innerText == "Сохранить") saveDialog.disabled = false;
                } else {
                    this.classList.remove("change_select");
                    changeCall.delete(index + "_id");
                    if (!changeCall.has("details")) saveDialog.disabled = true;
                }
            }
        );
    }
    if (index === "staff_status") {
        //для поля уведомление ответсвственного
        let divStaff = document.createElement("div");
        divStaff.classList.add("staff_checkbox");
        let labelStaff = document.createElement("label");
        labelStaff.classList.add("checkbox__container");
        let inputStaff = document.createElement("input");
        inputStaff.setAttribute("type", "checkbox");
        if (Number(fulldata["staff_status"]) != 0) inputStaff.checked = true;
        if (Number(fulldata["staff_status"]) > 1) inputStaff.disabled = true;
        inputStaff.classList.add("checkbox__toggle");
        inputStaff.addEventListener("change", function () {
            changeCall.set("staff_status", this.checked);
            if (saveDialog.innerText == "Сохранить") saveDialog.disabled = false;
        });
        labelStaff.innerHTML = svgstaff;
        labelStaff.prepend(inputStaff);
        divStaff.append(labelStaff);
        cardContent.append(divStaff);
    }
    if (index === "open_name" || index === "date" || index === "staff_date") {
        cardContent.innerText = fulldata[index];
    }

    card.append(cardContent);
    card.append(label);
    return card;
}

function checkTextarea(element) {
    //различные проверки поля текстареа
    timerModal.changeTime(900);
    let caps = new RegExp("[А-Я]{4,}"); //
    let en = new RegExp("[A-Za-z]{3,}");

    if (element.target.value.length < 3) {
        //если удалили все из текстареа, то возвращемся к исходному состоянию
        alertCapsUnblockAudio = true;
        alertENUnblockAudio = true;
        changeCall.delete("details");
        changeCall.delete("note");
        changeCall.delete("call_close");
        element.target.classList.remove("change_select");
        saveDialog.disabled = true; // кнопку сохранить
    }
    if (en.test(element.target.value) && alertENUnblockAudio) {
        alertENUnblockAudio = false; // запретим повторное воспроизведение (ведь уже предупредили)
        audioENKeyboard.currentTime = 0;
        audioENKeyboard.play();
    }
    if (caps.test(element.target.value) && alertCapsUnblockAudio) {
        alertCapsUnblockAudio = false;
        audioCapslock.currentTime = 0;
        audioCapslock.play();
    }
    let type = element.target.name;
    //console.log(type);
    if (element.target.value.length > 4) {
        element.target.classList.add("change_select");
        changeCall.set(type, element.target.value); //добавми в массив решение по заявке
        if (saveDialog.innerText == "Сохранить") saveDialog.disabled = false; //активируем кнопку сохранить
    }
}

function savecall(savedata) {
    //сохраняем данные из диалогового окна
    changeCall.set("action", savedata.target.getAttribute("action"));
    saveDialog.disabled = true;
    saveDialog.innerHTML = spinerDialog;

    main.fetchData(
        "/calls.php",
        Object.fromEntries(changeCall),
        saveCallResult
    );
    //console.log(changeCall,changeCall.size,savedata.target.getAttribute("action"));
}

function saveCallResult(getResponse) {
    //кэлбэк функция сохранения данных по заявке
    if (getResponse.status === "ok") {
        tableOpen.replaceData();
        tableClose.replaceData();
        if (dialog.open) {
            //если открыто диалоговое окно то сообщим на кнопки диалог
            saveDialog.innerHTML = "<span style='color:green'>Сохранено</span>";
            if (timerModal) {
                timerModal.changeTime(10);
            } else {
                resetModalTimeOut = setTimeout(() => {
                    resetModalTimeOut = null;
                    modalClose();
                }, 2000);
            }
        }
        headMessageEcho(getResponse.message, 30000);
    } else {
        if (dialog.open) {

            //если открыто диалоговое окно то сообщим на кнопки диалог
            saveDialog.innerHTML = "<span style='color:red'>Ошибка</span>";
            if (timerModal) {
                timerModal.changeTime(10);
            } else {
                resetModalTimeOut = setTimeout(() => {
                    resetModalTimeOut = null;
                    modalClose();
                }, 2000);
            }
            headMessageEcho(getResponse.message, 30000);
        }
    }
}

function headMessageEcho(message, time = 1000) {
    if (!Boolean(time)) {
        let oldText = headMessage.innerHTML;
        setTimeout(() => {
            headMessage.innerHTML = oldText;
        }, time);
    }
    headMessage.innerHTML = message;
}

function openCallsTab() {
}

tableClose.on("dataProcessed", function () {
    quantityCalls.close = tableClose.getDataCount();
});
//данные загружены, обработаны, и сформированы
tableOpen.on("dataProcessed", function () {
    numErrorReload = 0;
    timeLoadData = timeLoadDataDefoult;
    quantityCalls.open = tableOpen.getDataCount();

});

tableClose.on("rowClick", function (e, row) {
    //ловим клик по строке
    let on = row.getData();
    callViewer(on);

    //  alert("Row " + row.getIndex() + " Clicked!!!!"+on.com)
});
tableOpen.on("dataLoadError", function (error) {
    numErrorReload++;
    timeLoadData += numErrorReload * 1000;
    if (timeLoadData >= 1100000) timeLoadData = 1100000;
    let text = '<svg width="22" height="22" enable-background="new 0 0 16 16" version="1.1" xml:space="preserve"> <defs>  <symbol height="24" id="svg_1" width="24" >  </defs>  <g class="layer">   <title>Layer 1</title>   <use fill="#00ff00" id="svg_2" transform="matrix(1.39878 0 0 1.39878 3.48517 43.7184)" x="6" xlink:href="#svg_1" y="7"/>     <circle cx="10.66" cy="10.38" fill="#ff0000" id="svg_5" r="8" stroke="#000000"/>  </g> </svg>' + user_name + "-" + error;
    info.innerHTML = text;
    tockenAutorization();
    //error - the returned error object
});
tableOpen.on("rowClick", function (e, row) {
    //ловим клик по строке
    let on = row.getData();
    callViewer(on);

    //  alert("Row " + row.getIndex() + " Clicked!!!!"+on.com)
});
tableOpen.on("rowTap", function (e, row) {
    // для сенсорного экрана
    //e - the tap event object
    //row - row component
});
tableOpen.on("dataLoaded", function (data) {
    //data has been loaded
    let actualtime = '<svg width="22" height="22" enable-background="new 0 0 16 16" version="1.1" xml:space="preserve"> <defs>  <symbol height="24" id="svg_1" width="24" >  </defs>  <g class="layer">   <title>Layer 1</title>   <use fill="#00ff00" id="svg_2" transform="matrix(1.39878 0 0 1.39878 3.48517 43.7184)" x="4" xlink:href="#svg_1" y="7"/>     <circle cx="10.66" cy="10.38" fill="#00ff00" id="svg_5" r="8" stroke="#000000"/>  </g> </svg>' + user_name + '  ->  Данные актуальны по состоянию на - ' + new Date().toLocaleString();
    info.innerHTML = actualtime;
});
tableOpen.on("rowContext", function (e, row) {
    //e - the click event object
    //row - row component

    e.preventDefault(); // prevent the browsers default context menu form appearing.
});

async function tockenAutorization() {
    let storage = window['localStorage']
    let login = [];

    login['token'] = storage.getItem('token');
    if (login['token']?.length != 32) {
        console.log("tokenerror");
        return false;
    }
    login['id'] = storage.getItem('id');
    login['username'] = storage.getItem('name');

    fetch("login2.php", {

        method: "post",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: login['id'],
            token: login['token'],
            mobile: false,
            webGL: webGL
        }),
    })
}

function catcherrorfetch(error) {
    //console.log(error);
}

