const titleMainObj = "Управление адресами объектов";
const type = ["object_delete", "home_delete", "street_delete", "city_delete"];
const textType = [
  "Удалить лифты",
  "Удалить дома",
  "Удалить улицы",
  "Удалить города",
];

export function mainBody(parent, clickfunction) {

  let h2 = document.createElement("h2");
  let divgrid = document.createElement("div");
  let hrefEdit = document.createElement("a");
 
  divgrid.classList.add("parent_grid_object");
  hrefEdit.setAttribute("href", "#");
  hrefEdit.classList.add("btn"); 
  h2.innerText = titleMainObj;
  let hrefoptimization= hrefEdit.cloneNode(true)
  hrefEdit.innerText = "Управление базой адресов";
  hrefEdit.setAttribute("data-action", "object_edit");
  hrefEdit.addEventListener("click", clickfunction);
  
 hrefoptimization.innerText="Оптимизировать базу адресов"
hrefoptimization.addEventListener("click",optimization);
  divgrid.append(hrefEdit);
  divgrid.append(hrefoptimization);
  const href = [];
  for (let index = 0; index < type.length; index++) {
    href[index] = document.createElement("a");
    href[index].addEventListener("click", clickfunction);
    href[index].setAttribute("data-action", type[index]);
    href[index].innerText = textType[index];
    href[index].classList.add("btn");
    href[index].setAttribute("href", "#");
    divgrid.append(href[index]);
  }
  parent.append(h2);
  parent.append(divgrid);
}
export function deleteObject(parent, parentTitle, action) {
  parentTitle.innerText =  textType[type.indexOf(action)];
  const data = JSON.stringify({ action: action });
  fetchLoad("../settings/edit_obj_control.php", data, (res) => {
    console.log(res);
    if (res.status === "error") {
      parent.innerText = res.message;
      parentTitle.innerText = "при получении данных произошла ошибка";
      return;
    }
    
    let type = res.type;
    
    let objPos = new generate_delete("possible",type);// таблица с объектами, которые возможно удалить
    objPos.TitleName = ("Можно удалить ");
    objPos.titleAppendTo(parent);
    objPos.generateTableAppendTo(parent, "pos", res.possible);
    let objImpos = new generate_delete("impossible",type);// таблица с объектами которые не возможно удалить (так как есть ссылки на них)
    objImpos.TitleName = ("Не возможно  удалить ");
    objImpos.titleAppendTo(parent);
    objImpos.generateTableAppendTo(parent, "impos", res.impossible);


  });
}

class generate_delete {
  #title = document.createElement("div");
  #tableHeader = document.createElement("div");
  #divTablePos = document.createElement("div");
  #divConteinerPos = document.createElement("div");
  #divTable_content = document.createElement("div");
    

  constructor(possibility,action) {
    this.#title.classList.add("info_" + possibility);
    this.action=action
  }

  set TitleName(text) {
    this.#title.innerText = text;
  }
  titleAppendTo(parent) {
    parent.append(this.#title);
  }
  generateTableAppendTo(parent, type, data) {
    const header = {
      pos: "<div class='header__item'>ID</div><div class='header__item'>Название</div><div class='header__item'>Действие</div>",
      impos:
        "<div class='header__item'>ID</div><div class='header__item'>Название</div><div class='header__item'>Причина</div>",
    };
    const generate = {
      pos: function (parent, data,action,clickFunc) {
        // сосздаем тело таблицы с объектами которые возможно удалить

        Object.keys(data).forEach(function (key) {
          let divRow = document.createElement("div");
          divRow.classList.add("table-row");
          divRow.setAttribute("id",key);
          let divCellId = document.createElement("div");
          divCellId.classList.add("table-data");
          let divCellName = divCellId.cloneNode(true);
          let divCellAction = divCellId.cloneNode(true);
          divCellId.innerText = key;
          divCellName.innerText = this[key];
          divCellAction.classList.add("button_delete");
          divCellAction.innerText="Удалить"
          divCellAction.setAttribute("data-id",key)
          divCellAction.setAttribute("action",action);
          divCellAction.addEventListener("click",clickFunc,false)
          divRow.append(divCellId);
          divRow.append(divCellName);
          divRow.append(divCellAction)
          parent.append(divRow)
        }, data);
      },
      impos: function (parent, data,action,clickFunc) {
        Object.keys(data).forEach(function (key) {
          let divRow = document.createElement("div");
          divRow.classList.add("table-row");
          divRow.setAttribute("id",key);
          let divCellId = document.createElement("div");
          divCellId.classList.add("table-data");
          let divCellName = divCellId.cloneNode(true);
          let divCellAction = divCellId.cloneNode(true);
          divCellId.innerText = key;
          divCellName.innerText = this[key].name;
          divCellAction.innerHTML=this[key].reason;
          divRow.append(divCellId);
          divRow.append(divCellName);
          divRow.append(divCellAction)
          parent.append(divRow)
        }, data);

      },
    };
    this.#divTablePos.classList.add("table");
    this.#divConteinerPos.classList.add("conteiner_table");
    this.#divConteinerPos.append(this.#divTablePos);
    this.#tableHeader.classList.add("table-header");
    this.#tableHeader.innerHTML = header[type];
    this.#divTablePos.append(this.#tableHeader);
    generate[type](this.#divTable_content,data,this.action,this.#delObject);
    this.#divTablePos.append(this.#divTable_content);
    this.#divTable_content.classList.add("table-content");
    parent.append(this.#divConteinerPos);
  }
  get TitleName() {
    console.log(this.#title);
    //return this.#title;
  }
  #delObject(event){
    let id=event.target.getAttribute('data-id');
    let type=event.target.getAttribute("action");
    event.target.classList.remove("button_delete");
    event.target.innerHTML='<div class="lds-dual-ring" style="position: relative;"></div>' ;
   fetchLoad("../settings/edit_obj_control.php",`{"action":"delete","type":"${type}","id":"${id}"}`,function(result) {
if (result.status==="error"){
  alert("Ошибка" + result.message);
  return
}
    event.target.parentElement.remove();
    console.log(result);
   })

   
    
  }
  
}
function optimization(event){
  const optimizationAnimation=`<span style="--i:1">В</span>
  <span style="--i:2">ы</span>
  <span style="--i:3">п</span>
  <span style="--i:4">о</span>
  <span style="--i:5">л</span>
  <span style="--i:6">н</span>
  <span style="--i:7">я</span>
  <span style="--i:8">ю</span>`;

event.target.classList.add("waviy")
event.target.innerHTML=optimizationAnimation;
event.target.disabled=true;
event.target.removeEventListener("click",optimization);
 fetchLoad("../settings/optimization.php",'{"action":"no"',function(result){
  if(result.status==="error"){
    alert(result.message);
    return
  }
  if (result.status==="ok") {
    event.target.classList.remove("waviy")
    event.target.innerHTML="Выполнено";
  }
console.log(result);
 })
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
function catcherrorfetch() {}
