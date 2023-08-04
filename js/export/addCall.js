export class Select {
    // формы select
    /**
     * select form
     * @constructor
     * @param name {string} attribute name
     * @param styleClass {string} name style class
     */
    constructor(name, styleClass) {
        this.elem = document.createElement("select");
        if (name) this.elem.setAttribute("name", name);
        if (styleClass) {
            this.elem.classList.add(styleClass);
        }
    }

    appendTo(parent, options, selected, callback) {
        /**
         * append method.
         * @param parent {HTMLElement} родительский элемент куда вставлять select
         * @param options {array} массив списка
         * @param callback {function} callback function
         * @param selected {string}ключ  по которому стивим выбранный пункт если не будет совпадения будет добавлен пункт со зночением 0 и текстом Выберите
         */
        let rand = Math.floor(Math.random() * 10000);
        let selectedOption = false;
        this.elem.setAttribute("id", "rand" + rand);

        for (let key in options) {
            let option = document.createElement("option");
            option.value = key;
            option.innerText = options[key];
            if (key == selected) {
                selectedOption = true;
                option.selected = true;
            }
            this.elem.append(option);
        }
        if (!selectedOption) {
            let option = document.createElement("option");
            option.value = "0";
            option.innerText = "Выберите...";
            option.selected = true;
            option.disabled = true;
            this.elem.prepend(option);
        }
        parent.append(this.elem);
        this.elem.addEventListener("change", callback);
    }
}
export class main{
    typeList = ["city", "street", "home", "object"];// название типа в общем массиве
    parentType = ["", "city_id", "street_id", "home_id"];//название ключа для ID родительского элемента
    typeId = 0;
    nextStep = null; // Функция для следующего шага (генерация адреса пошагова - вначале Город, затем улица, затем дом и лифт)
    currentList = null; //актуальный список объектов для текущего типа и родительского объекта
    addressList=[];
constructor(address){
    this.addressList = address; //объект с полным перечнем всех адресов
}
    fullListFilter(parentId, typeId) {
        let list = this.addressList.city;

        if (parentId) {
            //получим список всех дочерних элемнтов ссылающихся на родительский элемент c ParentId
            let childrenType = this.typeList[typeId];
            list = this.addressList[childrenType].filter(
                (value) => value[this.parentType[typeId]] === parentId
            );
        }

        return list;
        //генерируем полный список с филтром по родительскому элементу
        //console.log(list);
    }
    get typeList(){
    return this.typeList
    }
    get parentType(){
    return this.parentType
    }
}
class mainAddress extends main{

    /**
     *  address select generation.
     * @constructor
     * @param titleModal {HTMLElement} link HTML element title for modal window
     * @param bodyModal {HTMLElement} link HTML element body for modal window
     * @param address {object} object with all address
     */
    constructor(titleModal, bodyModal,address) {
        super (address);

        this.divTitleContainer = document.createElement("div");// контейнер для заголовка модалки
        this.divTitleContainer.classList.add("flex-cont-title");
        titleModal.append(this.divTitleContainer);
        this.bread_crumbs = new Array(5);// массив эллементов адреса, для возврата на необходимый уровень (последний элемент поле ввода для поиска по текущем объекта)
        for (let index = 0; index < this.bread_crumbs.length; index++) {
            this.bread_crumbs[index] = document.createElement("div");
            this.bread_crumbs[index].classList.add("flex-box-title");
            this.bread_crumbs[index].classList.add("scale");
            this.divTitleContainer.append(this.bread_crumbs[index]);
        }
        this.divObjectList = document.createElement("div");
        bodyModal.append(this.divObjectList);
        this.input = document.createElement("input");
        this.bread_crumbs[4].append(this.input);
        this.generateList = this.generateList.bind(this); //привяжем метод к контексту объекта
    }

    getTypeId(name) {
        /**
         * получить номер по названию типа объекта
         * @param name {string} name type object
         */
        let result = this.typeList.indexOf(name);
        if (result === -1) result = false;
        return result;
    }

    creatSearchInput(typeId, list, nextStepFunction) {
        /**
         * конфигурация поля ввода для поиска и вывода списка объектов
         * @param typeId {number} id type object address
         * @param list {array} array or object list address
         * @param nextStepFunction {function} the following function when forming an application
         */

        this.typeId = typeId;
        this.currentList = list;
        for (let i = 0; i < this.typeList.length; i++) {
            typeId <= i
                ? (this.bread_crumbs[i].hidden = true)
                : (this.bread_crumbs[i].hidden = false);
        }
        this.nextStep = nextStepFunction;
        let type = this.typeList[typeId];
        this.input.setAttribute("data-type", type);
        this.input.setAttribute("placeholder", "Введите для поиска");
        this.generateList();
        this.input.addEventListener("input", this.generateList);
    }

    generateList() {
        /**
         * создаем обекты исходя из введеное поискового запроса
         */
        let time = 1000;
        this.divObjectList.innerHTML = ""; //отчистим список
        this.divObjectList.classList.add("hidden");
        let serch = this.input.value;
        if (serch.length >= 1) time = 200;
        let type = this.input.dataset.type;
        let list = this.currentList;
        //let top = document.createElement("div");
        let typeId = this.getTypeId(type);
        if (!Array.isArray(list)) {
            console.warn("not array ");
            list = Object.entries(list);
        }
        list.forEach((element) => {
            //console.log(  element[this.type + "_name"]);
            if (
                element[type + "_name"].toLowerCase().indexOf(serch.toLowerCase()) !==
                -1
            ) {
                //console.log(element[this.type + "_name"]);
                let divObject = document.createElement("div");
                divObject.classList.add("adress_card");
                divObject.innerText = element[type + "_name"];
                divObject.setAttribute("data-id", element.id);
                divObject.addEventListener("click", (event) => {
                    // установим атрибуты  "хлебных крошек" для возврата назад и навесим обработчик
                    this.bread_crumbs[typeId].setAttribute(
                        "data-parentid",
                        event.target.dataset.id
                    );
                    this.bread_crumbs[typeId].innerText = event.target.innerText;
                    this.bread_crumbs[typeId].addEventListener("click", this.nextStep);
                    this.nextStep(); // переходим к следующему шагу
                });
                this.divObjectList.classList.add("addnewcall_conteiner");
                this.divObjectList.append(divObject);
            }
        });
        setTimeout(() => {
            this.divObjectList.classList.remove("hidden");

        }, time);
    }
}

export class startAddressSelect extends mainAddress {
    #addressCallArr = new Array(5);

    constructor(titleModal, bodyModal, address, callback) {
        super(titleModal, bodyModal, address);
        this.callbackFunction = callback;
        //привяжем контекст к текущему объекту
        this.city = this.city.bind(this);
        this.street = this.street.bind(this);
        this.home = this.home.bind(this);
        this.lift = this.lift.bind(this);
        this.checkAddress = this.checkAddress.bind(this);
        this.exit = this.exit.bind(this);
    }

    city() {
        let filterList = this.fullListFilter(0, 0);
        this.creatSearchInput(0, filterList, this.street);
    }

    street() {
        //calback для генерации списка улиц по индексу выбраного города
        let parent = this.bread_crumbs[0].dataset.parentid;
        let filterList = this.fullListFilter(parent, 1);
        console.log(filterList)
        this.creatSearchInput(1, filterList, this.home);
    }

    home() {
        // callback для генерации списка домов по индексу выбранной улицы
        let parent = this.bread_crumbs[1].dataset.parentid;
        let filterList = this.fullListFilter(parent, 2);
        this.creatSearchInput(2, filterList, this.lift);
    }

    lift() {
        //callback для генерации списко лифтоа по индексу выбраного дома
        let parent = this.bread_crumbs[2].dataset.parentid;
        let filterList = this.fullListFilter(parent, 3);
        this.creatSearchInput(3, filterList, this.checkAddress);
    }

    checkAddress() {
        this.bread_crumbs[3].hidden = false;
        this.bread_crumbs[4].hidden = true; // спрячем поле input

        let addressText = "";
        //callback для упаковки и возврата полного адреса  к заявке, и переходу к следующему шагу
        this.#addressCallArr[0] = this.bread_crumbs[0].dataset.parentid;
        addressText += this.bread_crumbs[0].innerText;
        this.#addressCallArr[1] = this.bread_crumbs[1].dataset.parentid;
        addressText += " - " + this.bread_crumbs[1].innerText;
        this.#addressCallArr[2] = this.bread_crumbs[2].dataset.parentid;
        addressText += " дом № " + this.bread_crumbs[2].innerText;
        this.#addressCallArr[3] = this.bread_crumbs[3].dataset.parentid;
        addressText += " - " + this.bread_crumbs[3].innerText;
        this.#addressCallArr[4] = addressText;
        this.divObjectList.innerHTML = "";
        const divAdr = document.createElement("div");
        divAdr.innerHTML =
            "Проверьте правильность указанного адреса: <br><b>" +
            addressText +
            "</b><br>";
        divAdr.style = "  width: 350px;   position: relative;   left: 50%;   transform: translateX(-50%);";
        this.divObjectList.append(divAdr);
        this.divObjectList.removeAttribute('class');
        this.divObjectList.style = "  position: relative; margin: 20px; padding: 20px;"
        const div = document.createElement("div");
        this.divObjectList.append(div);
        const button = document.createElement("button");
        div.append(button);
        //div.classList.add("")
        button.innerText = "Адрес указан верно? Продолжить->";
        button.classList.add('button_next');
        button.addEventListener('click', this.exit)
    }

    exit() {
        this.callbackFunction(this.#addressCallArr);
    }

    fullListFilter(parentid, typeId) {
        this.bread_crumbs[4].hidden = false;// вклим поле инпут
        this.input.value = '';
        let list = this.addressList.city;
        if (parentid) {
            //получим список всех дочерних элемнтов ссылающихся на родительский элемент c ParentId
            let childrenType = this.typeList[typeId];
            list = this.addressList[childrenType].filter(
                (value) => value[this.parentType[typeId]] == parentid
            );
        }
        return list;
        //генерируем полный список с филтром по родительскому элементу
        //console.log(list);
    }
}


export class timerCountDown {
    /**
     * таймер  обратного отсчета
     * @constructor
     * @param timerId {HTMLElement} link HTML element
     * @param time {number} time in seconds
     * @callback callback
     * @param callback {function} callback function
     * @param type {'time' | 'number' | 'bar'}
     */

    constructor(timerId, time, callback, type) {
        this.remainingTime = this.time = time;
        this.timerId = timerId;
        this.callback = callback;
        this.interval = null;
        this.count = 0;
        this.type = type;
    }

    startCountdown(time = 0) {
        if (time) {
            this.count = time;
        } else {
            this.count = this.time;
        }
        if (this.interval) {
            clearInterval(this.interval);
        }

        this.interval = setInterval(() => {
            let timer;
            this.count--;
            let barWidth = '100%';
            if (this.count > 60) {
                let timerMinute = Math.floor(this.count / 60);
                let timerSec = this.count - timerMinute * 60;
                timer = timerMinute + " мин." + timerSec + " сек";
            } else {
                timer = this.count + " сек."
            }
            barWidth = (Math.floor(this.count / this.remainingTime * 100)) + '%';
            this.type === 'time' ? this.timerId.innerText = timer : null;
            this.type === 'number' ? this.timerId.innerText = this.count : null;
            this.type === 'bar' ? this.timerId.style.width = barWidth : null;
            this.type === 'bar' ? this.timerId.classList.remove("hidden") : null;
            if (this.count <= 0) {
                this.count = 0;
                this.callback();
                clearInterval(this.interval);

            }
        }, 1000);
    }

    reset() {
        this.count = this.remainingTime;
    }

    changeTime(time) {
        this.remainingTime = this.count = time;
    }

    stop() {
        this.type === 'bar' ? this.timerId.classList.add("hidden") : null;
        clearInterval(this.interval);
    }

}
