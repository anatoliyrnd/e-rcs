import { tableOpen, tableClose, Select } from "./export/tabulatconfig.js";
import { createlist } from "./export/class.js";
const headMesage = document.getElementById("head_mesage");
const headLoader = document.getElementById("loader_head");
let selectData = {
  department: {},
  group: {},
  request: {},
  staff: {},
  repair_time: {},
};
let adressData = {};

{
  headMesage.innerHTML = "Загружаем основные конфигурационные данные";

  let url = "/disp/loadconfig.php";
  fetchLoad(url, '{"action":"loadstartdate"}', start);
}
const svgstaff =
  '<span class="checkbox__checker"></span><span class="checkbox__txt-left">Да</span><span class="checkbox__txt-right">Нет</span><span class="checkbox__bg"><?xml version="1.0" ?><svg  viewBox="0 0 110 43.76" xmlns="http://www.w3.org/2000/svg"><path d="M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z"/></svg><span>';
//global variabl
let changeCall = new Map(); // массив с внесенными изменениями
let alertCapsUnblockAudio = true; // флаг возможности воспроизвести сообщение о включенном Капслок
let alertENUnblockAudio = true; // флаг возможности воспроизвести сообщение об английской раскладке
const audioCapslock = new Audio("audio/capslock.mp3"); // файл с сообщением о фключенном капслок
const audioENKeyboard = new Audio("audio/enkeybord.mp3"); // файл с сообщением об  английской раскладке
const closeDialog = document.getElementById("close"); //кнопка закрытия модального окна
const saveDialog = document.getElementById("save"); //кнопка сохранить - модального окна
const titleDialog = document.getElementById("title_dialog"); // заголовок модального окна
const dialog = document.querySelector("dialog"); //модальное окно
const bodyDialog = document.getElementById("body_dialog"); // тело модального окна
const menuListModal = document.getElementById("menu_madal"); // список меню для модального окна

const toggle_head = document.getElementById("toggle_head"); //кнопка главного меню
const mainBody = document.getElementById("main_body"); // контент главнорго окна
const spinerDialog = '<div class="lds-dual-ring" id="spinerDialog"></div>'; // спинер кнопки сохранить диалогового окна
const menu = document.getElementById("menu").getElementsByTagName("ul")[0];
let quantityCalls = { open: 0, close: 0 }; // массив количества заявок
for (const list of menu.querySelectorAll("li")) {
  //console.log(list);
  list.addEventListener("click", clickMenu);
}
setInterval(() => {
  //обновляем данные с сервера
  tableOpen.replaceData();
    tableClose.replaceData();
}, 60000);
saveDialog.addEventListener("click", savecall, false);
closeDialog.addEventListener("click", modalClose);
dialog.addEventListener("cancel", modalClose);
function startstep2(rezult) {
  headMesage.innerHTML = "";
  headLoader.hidden = true;
  if (rezult.status === "ok") {
    adressData = rezult.message;
//console.log(adressData);
    document.getElementById("open_calls_table").hidden = false;
    headMessageEcho(
      `Сейчас Вы просматриваете открытые заявки<br>Открытых заявок - ${quantityCalls.open} . Закрытых за последние 24 час - ${quantityCalls.close}`
    );
  } else {
    headMesage.innerHTML =
      rezult.message +
      "<br> База адресов не загружена. Будет предпринята попытка получить локальную базу адресов ";
  }
}
function clickMenu() {
  //console.log(this.getAttribute("name"));
  toggle_head.classList.remove("open");
  document.getElementById("menu").classList.remove("opened");
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
function viewMainBody(type) {
  const title = {
    open: "открытые заявки",
    close: "закрытые заявки",
  };
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
function start(rezult) {
  headMesage.innerHTML = "Конфигурация загруженна!";
  headLoader.hidden = true;
  if (rezult.status === "ok") {
    selectData = rezult.message;
    setTimeout(() => {
      headMesage.innerHTML = "Загружаем базу адресов";
      headLoader.hidden = false;
      fetchLoad("disp/loadconfig.php", '{"action":"loadadress"}', startstep2);
    }, 500);
  } else {
    headMesage.innerHTML =
      rezult.message +
      "<br> Конфигурационные данные не загружены с сервера, будет предпринята попытка получить последние локальные данные ";
  }
}

function callNew(data = { nodata: true }) {
  let addNewCallData = []; //масив для адреса новой заявки
  //создание модального окна для редактирования заявки
  let title = "Создание новой заявки->Выбирите город";
  let adressDiv = document.createElement("div");
  let adressList = document.createElement("div");
  //console.log(data);
  if (data.nodata) {
  } else {
    let ul = generatemenu(data, 4);
    menuListModal.append(ul);
  }
  saveDialog.setAttribute("action", "callnew");
  showDialog(title, true);
  bodyDialog.append(adressDiv);
  bodyDialog.append(adressList);

  let city = new createlist(
    adressData.city,
    "city",
    "none",
    adressList,
    adressDiv
  );
  let street = new createlist(
    adressData.street,
    "street",
    "city_id",
    adressList,
    adressDiv
  );
  let home = new createlist(
    adressData.home,
    "home",
    "street_id",
    adressList,
    adressDiv
  );
  let lift = new createlist(
    adressData.lift,
    "object",
    "home_id",
    adressList,
    adressDiv
  );

  city.fullList(0);
  city.generateTitle(cityclick);

  function cityclick() {
    //выбрали город
    addNewCallData["city"] = { id: city.id, name: city.value };
    title = addNewCallData.city.name + " ->Выбирите улицу";
    titleDialog.innerHTML = title;
    city.divname = "";
    street.fullList(city.id);
    street.generateTitle(streetclick);
  }
  function streetclick() {
    //выбрали улицу
    addNewCallData["street"] = { id: street.id, name: street.value };
    title =
      addNewCallData.city.name +
      " - " +
      addNewCallData.street.name +
      " ->Выбирите дом";
    titleDialog.innerHTML = title;
    street.divname = "";
    home.fullList(street.id);
    home.generateTitle(homeclick);
  }
  function homeclick() {
    //выбрали дом
    addNewCallData["home"] = { id: home.id, name: home.value };
    title =
      addNewCallData.city.name +
      " - " +
      addNewCallData.street.name +
      " Дом№ " +
      addNewCallData.home.name +
      " -> Выбирите лифт";
    home.divname = "";
    titleDialog.innerHTML = title;
    //console.log(addNewCallData);
    //console.log(home.id);
    lift.fullList(home.id);
    lift.generateTitle(liftclick);
  }
  function liftclick() {
    //создали полный адресс заявки
    addNewCallData["object"] = { id: lift.id, name: lift.value };
    title =
      addNewCallData.city.name +
      " - " +
      addNewCallData.street.name +
      " Дом№ " +
      addNewCallData.home.name +
      " - " +
      addNewCallData.object.name;
    titleDialog.innerHTML = title;
    lift.divname = "";
    adressList.innerHTML = "";
    //console.log(addNewCallData);
    changeCall.set("city", addNewCallData.city.id);
    changeCall.set("street", addNewCallData.street.id);
    changeCall.set("home", addNewCallData.home.id);
    changeCall.set("object", addNewCallData.object.id);
    changeCall.set("fullAdress", title);
    callNewStep2();
  }
}
function callNewStep2() {
  // создание новой заявки шаг 2 выбор разделов и срока ремонта
  let buttonNext = document.createElement("button");
  let divContainer = document.createElement("div");
  divContainer.classList.add("grid-container");
  buttonNext.addEventListener("click", callNewStep3, true);
  buttonNext.classList.add("button_next");
  buttonNext.innerText = "Далее";
  buttonNext.disabled = true;
  // создание новой заявки шаг 2 выбор отдела уровня и
  const selectlist = {
    group: "Группа",
    request: "Уровень",
    department: "Отдел",
    repair_time: "Срок предпологаемого ремонта",
  };
  creatSelectCard(selectlist, divContainer, buttonNext, 4);
  bodyDialog.append(divContainer);
  bodyDialog.append(buttonNext);
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
function creatSelectCard(selectlist, parent, button, length = 0) {
  //создаем список выбора
  let control = [];
  for (const key in selectlist) {
    const element = selectlist[key];
    let cardContent = cardCreat(parent, element);
    let select = new Select(key, "select");
    select.appendTo(cardContent, selectData[key], 0, function () {
      this.classList.add("change_select");
      control[key] = true;
      changeCall.set(key, this.value);
      if (Object.keys(control).length >= length) {
        button.disabled = false;
      }
      //addNewCallData[index]{nam this.value};
    });
  }
}
function callNewStep3() {
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

function callViewer(data) {
  //создание модального окна для просмотра заявки
  let title = data["adress"] + " Просмотр";

  let body = geenrateBodyDialog(data, 0);
  bodyDialog.append(body);
  if (data["type"] === "open") {
    let ul = generatemenu(data, 0);
    menuListModal.append(ul);
  }

  saveDialog.removeAttribute("action");
  showDialog(title, true);
  saveDialog.disabled = true;
}

function callEdit(data) {
  changeCall.set("call_id", data.id);
  //создание модального окна для редактирования заявки
  let title = data["adress"] + " Редактирование";
  let ul = generatemenu(data, 1);
  let body = geenrateBodyDialog(data, 1);
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

function showDialog(title, body) {
  //окончательная сборка модалки и ее вывод на экран
  titleDialog.innerText = title;

  closeDialog.setAttribute("body", body);
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
    if (key == type) continue; //если уже находимся в нужном месте
    if (!nav[key]) continue; // если пользователю запрещен доступ к этому поункто то идем далее
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
function modalClose() {
  //подчищаем все перед закрытием модалки
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
// функция эмуляции клика
function clickButton(id) {
  document.querySelector("#" + id).click();
}
function geenrateBodyDialog(data, type) {
  //<div class="grid-item">1</div>
  let card = false;
  let divContainer = document.createElement("div");
  divContainer.classList.add("grid-container");
  for (let index in data) {
    let div = document.createElement("div");
    div.classList.add("grid-item");
    if (type === 0) card = cardtemplate(index, data[index],data['type']);
    if (type === 1) card = cardTemplateEdit(index, data);
    if (!card) continue;
    div.append(card);
    divContainer.append(div);
  }
  return divContainer;
}
function cardTemplateNote(data) {
  changeCall.set("call_id", data.id);
  let divreturn = document.createElement("div");
  let divContainer = document.createElement("div");
  divContainer.classList.add("grid-container");
  divreturn.append(divContainer);
  if (Number(data.note_num) !== 0) {
    for (const key in data.note) {
      if (Object.hasOwnProperty.call(data.note, key)) {
        const element = data.note[key];
        let cardContent = cardCreat(
          divContainer,
          element.user + " - " + element.date
        );
        cardContent.innerHTML = element.body;
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
  divreturn.append(body);
  return divreturn;
}
function cardtemplate(labelindex, data,type) {
  //console.log(data);
  let name={};
  //<div class="card"><div class="card-content"></div><label class="card_label animate__fadeIn" >label</label></div>
  if (type==="open"){
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
  };}else{
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
      full_history:"История изменений",
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
      selectData.repair_time[0] = fulldata["repair_time"];
      fulldata["repair_time_id"] = 0;
    }

    select.appendTo(
      cardContent,
      selectData[index],
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

  fetchLoad(
    "/disp/save.php",
    JSON.stringify(Object.fromEntries(changeCall)),
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

      setTimeout(() => {
        modalClose();
      }, 10000);
    }
    headMessageEcho(getResponse.message, 30000);
  } else {
    if (dialog.open) {
      //если открыто диалоговое окно то сообщим на кнопки диалог
      saveDialog.innerHTML = "<span style='color:red'>Ошибка</span>";
      setTimeout(() => {
        modalClose();
      }, 15000);
      headMessageEcho(getResponse.message, 30000);
    }
  }
}
function headMessageEcho(message, time = 0) {
  if (!Boolean(time)) {
    let oldText = headMesage.innerHTML;
    setTimeout(() => {
      headMesage.innerHTML = oldText;
    }, time);
  }
  headMesage.innerHTML = message;
}
function openCallsTab() {}
tableClose.on("dataProcessed", function () {
  quantityCalls.close = tableClose.getDataCount();
});
//данные загружены, обработаны, и сформированы
tableOpen.on("dataProcessed", function () {
  quantityCalls.open = tableOpen.getDataCount();
});

tableClose.on("rowClick", function (e, row) {
  //ловим клик по строке
  let on = row.getData();
  callViewer(on);

  //  alert("Row " + row.getIndex() + " Clicked!!!!"+on.com)
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
  let actualtime =
  user_name+"  ->  Данные актуальны по состоянию на - " + new Date().toLocaleString() ;
  info.innerHTML = actualtime;
});
tableOpen.on("rowContext", function (e, row) {
  //e - the click event object
  //row - row component

  e.preventDefault(); // prevent the browsers default context menu form appearing.
});

async function fetchLoad(url, data, callback) {
  //Функция сохранения изменений
  function translate(code) {
    let rez = "Не известная ошибка ";
    switch (code) {
      case 404:
        rez = " - Страница не найдена! ";
        break;
      case 403:
          rez = " - Доступ запрещен! ";  
        break;
      case 500:
          rez = " - Ошибка сервера! ";  
        break;
      case 502:
          rez = " - Ошибка шлюза! ";  
        break;
      case 429:
          rez = " - Слишком много запросов! ";  
        break;
    }
    return rez;
  }
  brlabel: try {
    const response = await fetch(url, {
      //передаем через пост в теле json данных
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: data,
    });
    if (!response.ok) {
      let text = translate(response.status);
      callback({ status: "error", message: response.status + text });
      break brlabel; // стандартные сетевые ошибки не будем передавать в catch
    }
    const json = await response.json();
    if (json.status === "ok") {
      callback(json);
    } else {
      callback({ status: "error", message: json.message });
    }
  } catch (err) {
    // перехватит любую ошибку в блоке try: и в fetch, и в response.json
    catcherrorfetch(err);
    callback({ status: "error", message: "Глобальная ошибка!" + err });
  }
}

function errorfetch(element, message, timer = 4000) {
  element.innerHTML = message;
  setTimeout(() => {
    element.innerHTML = "";
  }, timer);
}
function catcherrorfetch(error) {
  //console.log(error);
}