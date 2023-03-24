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
  createInput(action, name, parentid, placeholder, btnTxt) {
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
      input.setAttribute("data-parentId", parentid);
      console.log(input);
    });
    removeBtn.innerText = btnTxt;
    div.append(input);
    removeBtn.addEventListener("click", () => this.removeDiv(div));
    div.append(removeBtn);

    this.container.append(div);
    this.#inputs.push(div);
  }
  Button(parent, btnTxt, div, hiddenElement) {}
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
  eye =
    '<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" class="eyesvg" enable-background="new 0 0 512 512" version="1.1" xml:space="preserve"> <g class="layer">  <title>"Объект показывается"</title>  <g id="svg_1">   <path d="m19,9.26c-8.28,0 -15.48,4.82 -19,11.87c3.52,7.05 10.72,11.87 19,11.87c8.28,0 15.48,-4.82 19,-11.87c-3.52,-7.05 -10.72,-11.87 -19,-11.87zm9.37,6.3c2.23,1.43 4.13,3.33 5.54,5.58c-1.42,2.24 -3.32,4.15 -5.54,5.58c-2.8,1.8 -6.04,2.74 -9.37,2.74c-3.33,0 -6.56,-0.94 -9.37,-2.74c-2.23,-1.43 -4.13,-3.33 -5.54,-5.58c1.42,-2.24 3.32,-4.15 5.54,-5.58c0.14,-0.09 0.29,-0.18 0.45,-0.28c-0.37,1.01 -0.58,2.11 -0.58,3.25c0,5.24 4.26,9.5 9.5,9.5c5.24,0 9.5,-4.26 9.5,-9.5c0,-1.14 -0.2,-2.24 -0.58,-3.25c0.14,0.09 0.29,0.18 0.45,0.28zm-9.37,1.8c0,1.97 -1.6,3.56 -3.56,3.56s-3.56,-1.6 -3.56,-3.56s1.6,-3.56 3.56,-3.56s3.56,1.6 3.56,3.56z" id="svg_2"/>  </g>  <g id="svg_3"/> </g></svg>';
  eyeBlock =
    ' <svg class="eyesvg" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve"> <g class="layer">    <title>"Объект скрыт"</title>  <g id="svg_1">   <path class="path" d="m32.78,14.13c2.76,1.95 5.01,4.58 6.55,7.64c-3.37,6.74 -10.24,11.35 -18.16,11.35c-2.22,0 -4.36,-0.36 -6.36,-1.03l2.77,-2.77c1.17,0.26 2.37,0.4 3.59,0.4c3.18,0 6.27,-0.9 8.95,-2.62c2.13,-1.36 3.95,-3.18 5.3,-5.33c-1.32,-2.08 -3.05,-3.85 -5.09,-5.19l2.45,-2.45zm-11.62,14.25c-0.81,0 -1.59,-0.1 -2.33,-0.31l11.1,-11.1c0.19,0.74 0.31,1.53 0.31,2.33c0,5.01 -4.06,9.08 -9.08,9.08zm15.89,-24.76l-1.91,0l-7.8,7.8c-1.95,-0.64 -4.04,-0.97 -6.19,-0.97c-7.92,0 -14.78,4.62 -18.17,11.35c1.51,3.03 3.73,5.62 6.42,7.56l-6.42,6.42l0,1.91l1.91,0l32.14,-32.14l0,-1.91l0.01,-0.01l0.01,-0.01zm-19.3,11.14c1.71,0 3.12,1.26 3.37,2.88l-3.89,3.89c-1.63,-0.24 -2.88,-1.67 -2.88,-3.37c0,-1.88 1.53,-3.41 3.41,-3.41l-0.01,0.01zm-10.85,7.03c1.36,-2.14 3.17,-3.97 5.3,-5.33c0.14,-0.09 0.28,-0.18 0.42,-0.26c-0.36,0.97 -0.55,2.01 -0.55,3.1c0,2.08 0.69,3.99 1.87,5.51l-2.08,2.08c-1.99,-1.33 -3.68,-3.08 -4.96,-5.12l0,0.01l0,0.01z" id="svg_2"/>  </g>  <g id="svg_3"/> </g></svg>';
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
    const btnChild = document.createElement("button");
    btnChild.innerHTML = btnTxt;
    input.setAttribute("placeholder", placeholder);
    input.setAttribute("name", name);
    input.setAttribute("data-hidden", hiddenElement);
    input.addEventListener("focus", function () {
      this.parentNode.classList.remove("tooltip");
    });
    input.addEventListener("input", () => {
      input.setAttribute("data-action", action);
      console.log(input);
    });
    input.value = value;
    input.setAttribute("data-name", value);
    input.setAttribute("data-id", id);
    input.setAttribute("data_parentid", parentId);
    btnChild.classList.add("btn_child");
    btnChild.classList.add("shadow");
    btnChild.setAttribute("data-id", id);
    btnChild.setAttribute("data-name", value);
    div.append(input);
    btn.setAttribute("data-hidden", hiddenElement);
    hiddenElement == "1"
      ? btn.classList.add("oldButtonHidden")
      : btn.classList.add("oldButtonNotHidden");
    hiddenElement == "1"
      ? (btn.innerHTML = this.eyeBlock)
      : (btn.innerHTML = this.eye);
    this.Button(div, btnTxt, div);
    this.container.append(div);
    btn.addEventListener("click", () => this.toggle(btn));
    btnChild.addEventListener("click", clickChildBtn);
    div.append(btn);
    name != "object" ? div.append(btnChild) : null;
  }
  //клас создания уже имеющихся объектов
  Button(parent, btnTxt, div, hiddenObj, callback) {
    //кнопка скрыть/показать
  }
  toggle(btn) {
    btn.innerHTML = this.spiner;
  }
}

class bodyObject {
  // список объектов
  #adressData={};
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

    // this.titleDiv.append(divTitleContainer);
    butADD.innerText = BtnTxtAdd[typeId];
    let inputNew = new InputField(divnew, "inputNewContainer");
    butADD.addEventListener("click", () => {
      inputNew.createInput("new", type, parentId, placeholderList[typeId], "X");
    });
    let inputOld = new listObject(divold, "inputOldContainer");
    list.forEach((element) => {
      console.log(element);
      inputOld.createInput(
        "edit",
        type,
        placeholderList[typeId],
        nextsvg,
        element[type + "_name"],
        element.id,
        element[objList[typeId]],
        element["vis_"+type],
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

  set adress(Adresslist) 
  {
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
  constructor(titleDialog, bodyDialog) {
    this.titleDialog = titleDialog;
    this.bodyDialog = bodyDialog;
  }
  start(url) {

    fetchLoad(url,'{"action":"adressEdit"}',(result)=>{
if (result.status==="ok"){
  console.log(result.message);
 
this.echoAdres(result.message)
}else{
  this.bodyDialog.innerHTML=result.message;
  this.titleDialog.innerHTML="Ошибка! При загрузки базы адресов";
}

    })
    
  }
 echoAdres(data){
  let objectList = new bodyObject(this.titleDialog, this.bodyDialog);
objectList.adress=data;
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
    let checkInputs = new check(this.bodyDialog);
    if (checkInputs.check()) {
      return true
    } else {
      return false
    }
  }
}

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
