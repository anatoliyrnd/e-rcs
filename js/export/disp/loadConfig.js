import {main} from "../main.js";


export class loadConfig extends main {
    #action ;
    #url = false;
    headMessage
    headLoader
    #loadConfigSpan
    #loadAddressSpan
#span=[]; //объект с набором html SPAN с именем свойства идентичному запросу action
    constructor(headMessage, headLoader) {
        super();
        this.headMessage = headMessage;
        this.headLoader = headLoader;
    }

    /**
     * @param action тип запроса
     */
    set action(action) {
        this.#action = {"action": action};
        if ( this.#span.hasOwnProperty(action)){
            //если информационное поле под действие (т.е. html елемент создан) создавалось то выходим
            console.log("object "+action+" sozdan ranee")

        }else {
            this.#span[action] = document.createElement("span");
        }
        this.headMessage.append(this.#span[action]);
    }
    set actionText(text){
        this.#span[this.#action.action].innerText=text
     }

    set URL(url) {
        this.#url = url;
    }
get TextElement(){
        return this.#span[this.#action.action];
}

    loadData(callBack) {

        if (!this.#action.hasOwnProperty('action')) {
            console.warn('не установлен тип запроса')
            return false;
        }
        if (!this.#url) {
            console.warn('не установлена страница обработки запроса')
            return false;
        }
       this.#span[this.#action.action].setAttribute("loading","true")
       // this.headLoader.hidden = true;
       main.fetchData(this.#url, this.#action, callBack)

    }
}