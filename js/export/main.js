export class main{
    #form ;
    #idFormElement=1;
    #id=1;

    constructor(){

    }

    creatForm(parent) {
        this.#form = document.createElement("form");
        this.#form.classList.add("form-style")
        this.#form.setAttribute("id", this.#id.toString())
        const ul = document.createElement("ul")
        this.#form.append(ul)
        parent.append(this.#form)
        this.#id++
    }
    get Form() {
        return this.#form;
    }
    addElementFormEnd(element) {
          this.#form.insertAdjacentElement("beforeend", element);

    }
addElementAfterend(element){
    this.#form.insertAdjacentElement("afterend", element);
}
    addElementFormBegin(element) {

        this.#form.insertAdjacentElement("afterbegin", element);
    }
createElementInput(typeElement,value,optionValue,editable){
        let input
    if(typeElement==='html'){
        input=document.createElement('div')
        input.innerHTML=value;

    }
    else if (typeElement === "textarea") {
        input = document.createElement("textarea")
        input.addEventListener("keyup",(e)=>{
            //увеличим текстовое поле  ввысоту на всю ширину контента
            e.target.style.height = "20px";
            e.target.style.height = (e.target.scrollHeight)+"px";
        })
    } else if (typeElement === "select") {
        input = document.createElement("select")

        for (const key in optionValue){
            const option=document.createElement("option");
            option.value=key;
            option.innerText=optionValue[key]
            input.append(option)
        }
    } else {
        input = document.createElement("input")
        input.setAttribute("type", typeElement)
        typeElement==="checkbox"?input.checked=Number(value):null;

    }
    if(!editable)input.disabled=true;
    return input
}
    createElementForm(typeElement, name, description, value,dataNameAttribute=null,optionValue= {0:"не указан"},editable=true) {
        const li = document.createElement("li")
        const label = document.createElement("label")
        label.setAttribute("for", "formElement" + this.#idFormElement)
        label.innerText = name;
        li.append(label)
        let input;
        input=this.createElementInput(typeElement,value,optionValue,editable)
        li.append(input)

        if(dataNameAttribute)input.setAttribute("data-name",dataNameAttribute)
        input.setAttribute("name", "formElement" + this.#idFormElement)
        if (value) input.value = value;
        const span = document.createElement("span")
        span.innerText = description
        li.append(span)
        this.#idFormElement++;
        return li
    }
    readForm(){
        let inputs=this.#form.querySelectorAll("input,textarea,select")
        let data=new Object()

        for (const inputsKey of inputs.keys()) {
            let input=inputs[inputsKey];

           let value=null;
          input.getAttribute("type")==='checkbox'?value=input.checked:value=input.value
            data[input.dataset.name]=value
        }
return data;
    }
    static test(txt){
        return txt
    }
    static async  fetchData(url, data, callback){
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
            console.log(data)
            console.log(JSON.stringify(data))
            const response = await fetch(url, {

                //передаем данные запроса в пост в теле json данных
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            });
            if (!response.ok) {
                let text = translate(response.status);
                callback({status: "error", message: response.status + text});
                break brlabel; // стандартные сетевые ошибки не будем передавать в catch
            }

            const json = await response.json();
            if (json.status === "ok") {
                callback(json);
            } else {
                callback({status: "error", message: json.message});
            }
        } catch (err) {

            // перехватит любую ошибку в блоке try: и в fetch, и в response.json
            callback({status: "error", message: "Глобальная ошибка!" + err});
        }
    }

}
export class dialog extends main {
    title;
    #body;
    #save
    #close
    #dialog
    #clickCallback
    #checkBoxId = 1
toast
    constructor(parent) {
        super();
        if (dialog._instance) {
            return dialog._instance;
        }
        this.#dialog = document.createElement("dialog");

        this.#dialog.classList.add("confirm");
        const div_content = document.createElement("div");
        div_content.classList.add("content_dialog");
        this.title = document.createElement("div");
        this.title.classList.add("title_dialog");
        this.#body = document.createElement("div");
        this.#body.classList.add("body_dialog");
        div_content.append(this.title);
        div_content.append(this.#body);
        this.#dialog.append(div_content);
        const div_but = document.createElement("div");
        div_but.classList.add("modal_but");
        this.#save = document.createElement("button");
        this.#save.innerText = "Сохранить";
        this.#save.disable = true;
        this.#save.setAttribute("id", "save")
        this.#close = document.createElement("button");
        this.#close.innerText = "Закрыть";
        this.#close.setAttribute("id", "close")
        this.#close.addEventListener("click", () => {
            this.#dialog.close();
        })
        div_but.append(this.#save);
        div_but.append(this.#close);
        this.#dialog.append(div_but);
        let parentElement = parent || document.getElementById("modal");
        parentElement.append(this.#dialog);
        dialog._instance = this;
    }

    showModal() {
        this.#dialog.showModal();
    }

    set titleTxt(title) {
        this.title.innerText = title;
    }

    get body() {
        return this.#body;
    }

    get modalId() {
        return this.#dialog;
    }

    clearDialog() {
        this.title.innerText = "";
        this.#body.innerHTML = "";
        this.#save.removeEventListener("click", this.#clickCallback)
    }

    setDataAttribute(data) {
        this.removeAllDataAttributes(this.#save)
        for (const key in data) {
            let attributeName = "data-" + key;
            this.#save.setAttribute(attributeName, data[key])
        }
    }

    removeAllDataAttributes(element) {
        for (const name of element.getAttributeNames()) {
            if (name.startsWith('data-')) element.removeAttribute(name)
        }

    }

    saveClick(func) {
        this.#clickCallback = func;
        this.#save.addEventListener("click", this.#clickCallback)
    }
    toastShow(data) {
let time=10000
if (!this.toast){
        this.toast=document.createElement("div")
        this.toast.setAttribute("id","toast")
        this.#body.insertAdjacentElement('afterend',this.toast)
        }
        this.toast.className = "show";
        data.status==='error'?this.toast.style.cssText="background:rgba(255, 0, 0, 0.24)":time=3000;
        data.status==='warning'?this.toast.style.cssText="background:rgba(247, 255, 0, 0.24)":null;
        data.status==='ok'?this.toast.style.cssText="background:rgba(0,255, 55, 0.24)":null;
        data.status==='ok'?this.toast.status='ok':null;
           this.toast.insertAdjacentHTML('beforeend',data.message+"<hr>" );
        console.log(data.message)
        setTimeout(()=>{
            this.toast.classList.remove("show")
            this.toast.status==='ok'?this.#dialog.close():null;
this.toast.innerHTML='';
            }, time);
    }







    createCheckBox(type, name, check, text = "не указан", textChecked = "не указан") {

        const checkBox = document.createElement("input")
        const div = document.createElement("div")
        div.classList.add("input-wrapper")
        checkBox.setAttribute("type", "checkbox")
        checkBox.setAttribute("id", "checkBox" + this.#checkBoxId)
        checkBox.classList.add("checkbox");
        checkBox.setAttribute("data-type", type)
        checkBox.setAttribute("data-name", name)
        checkBox.setAttribute("text", text)
        checkBox.setAttribute("text-checked", textChecked)
        check ? checkBox.checked = true : checkBox.checked = false;
        div.append(checkBox)
        checkBox.addEventListener("click", (e) => {
            console.log(e.target.checked)
        })
        this.#checkBoxId++;
        return div;
    }
    addAttribute(data,element,text=''){

        for (const key in data) {
            let attributeName="data-"+key;
            element.setAttribute(attributeName,data[key])
        }
        if(text.length>=1){element.innerText=text}
    }
}

export async function fetchDataServer(url, data, callback) {
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
        console.log(data)
        console.log(JSON.stringify(data))
        const response = await fetch(url, {

            //передаем данные запроса в пост в теле json данных
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            let text = translate(response.status);
            callback({status: "error", message: response.status + text});
            break brlabel; // стандартные сетевые ошибки не будем передавать в catch
        }

        const json = await response.json();
        if (json.status === "ok") {
            callback(json);
        } else {
            callback({status: "error", message: json.message});
        }
    } catch (err) {

        // перехватит любую ошибку в блоке try: и в fetch, и в response.json
        callback({status: "error", message: "Глобальная ошибка!" + err});
    }

}