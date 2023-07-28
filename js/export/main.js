
export class dialog{
    title;
    #body;
    #save
    #close
    #dialog
    #clickCallback
    constructor(parent) {
        this.#dialog=document.createElement("dialog");
        this.#dialog.classList.add("confirm");
        const div_content=document.createElement("div");
        div_content.classList.add("content_dialog");
        this.title=document.createElement("div");
        this.title.classList.add("title_dialog");
        this.#body=document.createElement("div");
        this.#body.classList.add("body_dialog");
        div_content.append(this.title);
        div_content.append(this.#body);
        this.#dialog.append(div_content);
        const div_but=document.createElement("div");
        div_but.classList.add("modal_but");
        this.#save=document.createElement("button");
        this.#save.innerText="Сохранить";
        this.#save.disable=true;
        this.#save.setAttribute("id","save")
        this.#close=document.createElement("button");
        this.#close.innerText="Закрыть";
        this.#close.setAttribute("id","close")
        this.#close.addEventListener("click",()=>{this.#dialog.close();})
        div_but.append(this.#save);
        div_but.append(this.#close);
        this.#dialog.append(div_but);
        let parentElement = parent || document.getElementById("modal");
        parentElement.append(this.#dialog);
    }
    showModal(){
        this.#dialog.showModal();
    }
    set titleTxt(title){
       this.title.innerText=title;
    }
    set bodyHTML(body){
        this.#body.insertAdjacentHTML("afterbegin",body);
    }
    get modalId(){
        return this.#dialog;
    }
    clearDialog(){
        this.title.innerText="";
        this.#body.innerHTML="";
        this.#save.removeEventListener("click",this.#clickCallback)
    }
    saveClick(func){
        this.#clickCallback=func;
        this.#save.addEventListener("click",this.#clickCallback)
    }

}
export async function fetchDataServer(url, data, callback){
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
           callback({ status: "error", message: "Глобальная ошибка!" + err });
        }
}
