import {main} from "../main.js";


export class loadConfig extends main {
    #action ;
    #url = false;
    headMessage
    headLoader

    constructor(headMessage, headLoader) {
        super();
        this.headMessage = headMessage;
        this.headLoader = headLoader;
    }

    set action(action) {
        this.#action = {"action": action};
    }

    set URL(url) {
        this.#url = url;
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
        this.headMessage.innerHTML = "Загружаем основные конфигурационные данные";
        this.headLoader.hidden = true;
       main.fetchData(this.#url, this.#action, callBack)
    }
}