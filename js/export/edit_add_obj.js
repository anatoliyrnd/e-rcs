const nextsvg =
  '<svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" class="btn_childsvg" version="1.1" xml:space="preserve"> <g class="layer">  <title>Загрузить список дочерних объектов</title>  <g id="svg_1">   <g id="svg_2">    <polygon fill="#030104" id="svg_3" points="20.25,6 20.25,7.770125389099121 30.875,16.625 20.25,25.479875564575195 20.25,27.25 33,16.625 "/>    <path d="m28.75,16.63l-12.75,-10.63l0,6.54c-7.52,0.68 -13.41,3.59 -17,12.58c5.27,-4.38 10.54,-5.89 17,-5.06l0,7.18l12.75,-10.63z" fill="#030104" id="svg_4"/>   </g>  </g> </g></svg>';
class InputField {
  #inputs = [];
  // класс создания поля инпут
  //classDiv CSSклас родительского контейнера для созданного Input и Button
  constructor(container, classDiv) {
    this.container = container;
    this.classDiv = classDiv;
  }
  createInput(action, name, parentId, placeholder, btnTxt) {
    const removeBtn = document.createElement("button");
    const div = document.createElement("div");
    div.classList.add(this.classDiv);
    const input = document.createElement("input");
    input.setAttribute("placeholder", placeholder);
    input.setAttribute("data-type", name);

    input.addEventListener("focus", function () {
      this.parentNode.classList.remove("tooltip");
    });
    input.addEventListener("input", () => {
      input.setAttribute("data-action", action);
      input.setAttribute("data-parentId", parentId);
      console.log(input);
    });
    removeBtn.innerText = btnTxt;
    div.append(input);
    removeBtn.addEventListener("click", () => this.removeDiv(div));
    div.append(removeBtn);

    this.container.append(div);
    this.#inputs.push(div);
  }

  removeDiv(divinput) {
    this.#inputs = this.#inputs.filter((i) => i !== divinput);
    divinput.remove();
  }
  get list() {
    return this.#inputs;
  }
}
class listObject {
  spiner =
    '<svg class="spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg>';

  constructor(container, classDiv) {
    this.container = container;
    this.classDiv = classDiv;
  }
  createInput(
    action,
    name,
    placeholder,
    btnTxt,
    value,
    id,
    parentId,
    hiddenElement = "0",
    clickChildBtn = () => {}
  ) {
    const div = document.createElement("div");
    div.classList.add(this.classDiv);
    const input = document.createElement("input");
    const btn = document.createElement("button");
    const btnChild = document.createElement("button"); //кнопка загрузки дочерних объектов
    btnChild.innerHTML = btnTxt;
    input.setAttribute("placeholder", placeholder);
    input.setAttribute("data-type", name);
    input.setAttribute("data-hidden", hiddenElement);
    input.addEventListener("focus", function () {
      this.parentNode.classList.remove("tooltip"); // для удаления сообщений которые появляются после проверки
    });
    input.addEventListener("input", () => {
      input.setAttribute("data-action", action);
      // если изменили поле то добавим атрибут для пометки этого поля.
    });
    // все данные по объекту в атрибуты поля инпут
    input.value = value;
    input.setAttribute("data-name", value);
    input.setAttribute("data-id", id);
    input.setAttribute("data-parentid", parentId);
    btnChild.classList.add("btn_child");
    btnChild.classList.add("shadow");
    btnChild.setAttribute("data-id", id);
    btnChild.setAttribute("data-name", value);
    div.append(input);
    btn.setAttribute("data-hidden", hiddenElement);
    hiddenElement == "1"
      ? btn.classList.add("oldButtonHidden")
      : btn.classList.add("oldButtonNotHidden");

    this.container.append(div);
    btn.addEventListener("click", () => this.toggle(btn));
    btnChild.addEventListener("click", clickChildBtn);
    div.append(btn);
    name != "object" ? div.append(btnChild) : null;
  }
  //клас создания уже имеющихся объектов

  toggle(btn) {
    btn.innerHTML = this.spiner;
    if (btn.disabled) return;
    btn.disabled = true;
    setInterval(() => {
      btn.disabled = false;
    }, 5000);
    let hidden = btn.dataset.hidden;
    let type = btn.previousSibling.dataset.type;
    let id = btn.previousSibling.dataset.id;
    fetchLoad(
      "../settings/edit_obj_control.php",
      `{"action":"visableObject","type":"${type}","id":"${id}","hidden":"${hidden}"}`,
      function (result) {
        if (result.status === "ok") {
          btn.classList.toggle("oldButtonNotHidden");
          btn.classList.toggle("oldButtonHidden");
          btn.innerHTML = "";
        } else {
          btn.innerHTML = "err";
        }
      }
    );
  }
}

class bodyObject {
  // класс формировния список объектов в основное окно согласно выбранного уровня (город,улица,дом ...)
  #adressData = {};
  constructor(titleDiv, bodyDiv) {
    this.titleDiv = titleDiv;
    this.bodyDiv = bodyDiv;
    this.back = {
      city: 0,
      city_name: " списку городов",
    };
    this.backId = [0, 1, 2, 3, 4];
    this.backName = ["Список городов", 1, 2, 3, 4];
    this.backBut = {};
    this.titleDiv.classList.add("flex-cont-title");
    this.titleDiv.insertAdjacentHTML(
      "beforeend",
      "<div class='flex-box-title '></div>".repeat(5)
    );
  }
  generateList(
    type,
    Go_Back = false,
    parentName = "",
    parentId = 0,
    callback = () => {},
    backClickFunction = () => {}
  ) {
    //генерируем список объектов из общей базы.
    //массивы данных для сообщений а дата атрибутов исходя из типа объекта
    const typeList = ["city", "street", "home", "object"];
    const typeId = typeList.indexOf(type);
    const nameList = [
      "Список городов",
      "Список улиц в ",
      "Список домов по ",
      "Список лифтов в доме №",
    ];
    const objList = ["", "city_id", "street_id", "home_id"];
    const placeholderList = [
      "Введите город",
      "Введите улицу",
      "Введите номер дома",
      "Введите лифт и подъезд",
    ];
    const BtnTxtAdd = [
      "Добавить город",
      "Добавить улицу",
      "Добавить дом",
      "Добавить лифт",
    ];
    const parentType = ["city", "city", "street", "home"];
    const btnTxtBack = ["", "списку городов", "списку улиц", "списку домов"];
    this.backBut[type] = document.createElement("button");
    this.bodyDiv.innerHTML = "";
    this.titleDiv.childNodes[4].innerHTML = nameList[typeId] + parentName;
    this.titleDiv.childNodes[4].classList.remove("hidden");
    for (let i = typeId; i < 4; i++) {
      this.titleDiv.childNodes[i].classList.add("hidden");
    }
    //хлебные крошки в заголовок диалогового окна
    if (type !== "city") {
      Go_Back ? null : (this.backId[typeId] = parentId);
      Go_Back ? null : (this.backName[typeId] = parentName);
      this.titleDiv.childNodes[typeId - 1].innerHTML =
        this.backName[typeId - 1];
      this.titleDiv.childNodes[typeId - 1].classList.remove("hidden");
      this.titleDiv.childNodes[typeId - 1].addEventListener(
        "click",
        backClickFunction
      );
      this.titleDiv.childNodes[typeId - 1].setAttribute(
        "data-id",
        this.backId[typeId - 1]
      );
      this.titleDiv.childNodes[typeId - 1].setAttribute(
        "data-name",
        this.backName[typeId - 1]
      );
      this.titleDiv.childNodes[typeId - 1].setAttribute(
        "data-button",
        "Go_Back"
      );
    }
    const butADD = document.createElement("button");

    const divTitleContainer = document.createElement("div");
    const titleText = document.createElement("sapn");
    let butbacktxt = "Назад к " + this.back[typeList[typeId - 1] + "_name"];
    this.back[typeList[typeId - 1]] = parentId; //данные родителя
    this.back[typeList[typeId - 1] + "_name"] = parentName; //данные родителя
    this.backBut[type].innerHTML = butbacktxt;
    this.backBut[type].setAttribute("data-id", this.back[typeList[typeId - 1]]);
    this.backBut[type].setAttribute(
      "data-name",
      this.back[typeList[typeId - 1] + "_name"]
    );
    this.backBut[type].setAttribute("data-button", "Go_Back");
    this.backBut[type].addEventListener("click", backClickFunction);
    //(type === "city")?butBack.hidden = true:butBack.hidden = false;
    type !== "city"
      ? divTitleContainer.append(this.backBut[type])
      : divTitleContainer.append(titleText);
    const divnew = document.createElement("div");
    const divold = document.createElement("div");
    this.bodyDiv.append(butADD);
    this.bodyDiv.append(divnew);
    this.bodyDiv.append(divold);

    titleText.innerText = nameList[typeId];
    let list = [];
    if (parentId) {
      list = this.#adressData[type].filter(
        (value) => value[objList[typeId]] == parentId
      );
    } else {
      list = this.#adressData[type];
    }
    butADD.innerText = BtnTxtAdd[typeId];
    let inputNew = new InputField(divnew, "inputNewContainer"); //  управление инпутами для добавления объекта
    butADD.addEventListener("click", () => {
      inputNew.createInput("new", type, parentId, placeholderList[typeId], "X");
    });
    let inputOld = new listObject(divold, "inputOldContainer"); // управление инпутами уже существующего объекта
    list.forEach((element) => {
      // создадим список уже существующих объектов
      inputOld.createInput(
        "edit",
        type,
        placeholderList[typeId],
        nextsvg,
        element[type + "_name"],
        element.id,
        element[objList[typeId]],
        element["vis_" + type],
        callback
      );
    });
  }

  removeButtonBack(city = false, street = false, home = false) {
    console.log(
      this.backBut["street"],
      this.backBut["home"],
      this.backBut["object"]
    );
    city ? this.backBut["street"]?.remove() : null;
    street ? this.backBut["home"]?.remove() : null;
    home ? this.backBut["object"]?.remove() : null;
  }

  set adress(Adresslist) {
    this.#adressData = Adresslist;
  }
}
class check {
  constructor(container) {
    this.container = container;
  }
  check() {
    let inputFieldList = new Map();
    // проверим все input на пустоту
    let list = this.container.querySelectorAll("input");
    let chekResult = true;
    list.forEach((input) => {
      let val = input.value.replace(/[\s.,%]/g, ""); //удалим пробелы точки и запятые :)

      if (val.length < 1) {
        //если пусто то повесим сообщение
        input.parentNode.classList.add("tooltip");
        input.parentNode.setAttribute("data-tooltip", "Введите значение");
        chekResult = false;
      } else {
        // добавим элемент в Map
        inputFieldList.set(input, val);
      }
    });
    let checkDublikate = this.checkMapDublikate(inputFieldList);
    if (checkDublikate.length != 0) {
      chekResult = false;
      for (let inputField of checkDublikate) {
        inputField.parentNode.classList.add("tooltip");
        inputField.parentNode.setAttribute("data-tooltip", "Повтор");
      }
    }
    return chekResult;
  }
  checkMapDublikate(arr) {
    // возвращаем массив индексов в которых есть одинаковые значения
    function getByValue(map, searchValue) {
      //функция поиска значений в массиве
      for (let [key, value] of map.entries()) {
        if (value === searchValue) return key;
      }
    }
    let arr2 = [];
    for (let pair of arr) {
      //переберем массив введеных данных
      arr.delete(pair[0]);
      let ret = getByValue(arr, pair[1]);
      if (ret) {
        arr2.push(ret);
        arr2.push(pair[0]);
      }
    }
    //console.log(arr2);
    return arr2;
  }
}

export class startEditObj {
  //основной класс для формирования списка адресов
  constructor(titleDialog, bodyDialog) {
    this.titleDialog = titleDialog;
    this.bodyDialog = bodyDialog;
  }
  start(url) {
    fetchLoad(url, '{"action":"adressEdit"}', (result) => {
      if (result.status === "ok") {
        console.log(result.message);

        this.echoAdres(result.message);
      } else {
        this.bodyDialog.innerHTML = result.message;
        this.titleDialog.innerHTML = "Ошибка! При загрузки базы адресов";
      }
    });
  }
  echoAdres(data) {
    //выводим список обектов по типу и предшественнику
    let objectList = new bodyObject(this.titleDialog, this.bodyDialog);
    objectList.adress = data;
    const lift = function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      let Go_Back = false;
      this.dataset?.button === "Go_Back" ? (Go_Back = true) : null; // если пришли по кнопке возврата на родителский объект
      objectList.generateList("object", Go_Back, name, id, () => {}, home);
    };
    const home = function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      let Go_Back = false;
      this.dataset?.button === "Go_Back" ? (Go_Back = true) : null;
      objectList.generateList("home", Go_Back, name, id, lift, street);
    };
    const street = function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      let Go_Back = false;
      this.dataset?.button === "Go_Back" ? (Go_Back = true) : null;
      objectList.generateList("street", Go_Back, name, id, home, city);
    };
    const city = function () {
      let Go_Back = false;
      this?.dataset?.button === "Go_Back" ? (Go_Back = true) : null;
      objectList.generateList("city", Go_Back, "", 0, street);
    };
    city();
  }
  inputCheck() {
    //confirm inputs на пустоты и повторения
    let checkInputs = new check(this.bodyDialog);
    if (checkInputs.check()) {
      return true;
    } else {
      return false;
    }
  }
  getNewData(calback=()=>{}) {
    //собираем ввденые данные с инпутов (только те поля в которых что-то вводили)
    const list = this.bodyDialog.querySelectorAll(
      "[data-action='edit'],[data-action='new']"
    );
    let newData = { action:"saveObject",parentid: 0, type: "", edit: [], new: [] };
    const newobj = [];
    const editobj = [];
    if (!list.length) {
      return false;
    }
    list.forEach((element) => {
     
      newData.type = element.dataset.type;
      if(newData.type=="city") {newData.parentid = 0}else{newData.parentid = element.dataset.parentid}
      if (element.dataset.action === "edit") {
        editobj.push({ id: element.dataset.id, value: element.value });
      } else if (element.dataset.action === "new") {
        newobj.push(element.value);
      }
    });
    newData.edit=editobj;
    newData.new=newobj;
    fetchLoad("../settings/edit_obj_control.php",JSON.stringify(newData),calback) 
  }
}

export async function fetchLoad(url, data, callback) {
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
  // brlabel: try {
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
    //   break brlabel; // стандартные сетевые ошибки не будем передавать в catch
  }
  const json = await response.json();
  if (json.status === "ok") {
    callback(json);
  } else {
    callback({ status: "error", message: json.message });
  }
  /*  } catch (err) {
    // перехватит любую ошибку в блоке try: и в fetch, и в response.json
    catcherrorfetch(err);
    callback({ status: "error", message: "Глобальная ошибка!" + err });
  } */
}
function catcherrorfetch() {}


