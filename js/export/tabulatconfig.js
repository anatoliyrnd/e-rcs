import  './tabulator.min.js';
//Build Tabulator
//сохранять состояние таблицы при закрытии браузера - persistence: true,
//auto detect the current language. -locale: true,
// config open calls table
export class  Select {
  //прототип формы select
  constructor(name, styleClass) {
    this.elem = document.createElement("select");
    if (name) this.elem.setAttribute("name", name);
    if (styleClass) {
      this.elem.classList.add(styleClass);
    }
  }
  appendTo(parent, options, selected, callback) {
    //parent родительский элемент куда вставлять select
    //options массив списка 
    // selected ключ который по которому стивим выбранный пункт если не будет совпадения будет добавлен пункт со зночением 0 и текстом Выберите
    //callbak функция для при выборе
    let rand = Math.floor(Math.random() * 10000);
    let selectedopt=false;
    this.elem.setAttribute("id", "rand" + rand);

    for (let key in options) {
      let option = document.createElement("option");
      option.value = key;
      option.innerText = options[key];

      if (key == selected) {
        selectedopt=true;
        option.selected = true;
      }
      this.elem.append(option);
    }
    if (!selectedopt){
      let option = document.createElement("option");
      option.value = "0";
      option.innerText = "Выберите...";
      option.selected = true;
      option.disabled=true;
      this.elem.prepend(option);
    }
    parent.append(this.elem);
    this.elem.addEventListener("change", callback);
  }
}
const windowInnerWidth = window.innerWidth;
let adres_width = 280;
if (windowInnerWidth < 1000) {
  adres_width = 180;
} else if (windowInnerWidth > 1500) {
  adres_width = (windowInnerWidth - 850) / 2;
}
 
let columsdata = [
  {
    title: "Adress",
    field: "adress",
    sorter: "string",
    frozen: true,
    tooltip: true,
    minWidth: adres_width,
    headerFilter: "input",
  },
  {
    title: "Details",
    field: "details",
    headerSort: false,
    minWidth: adres_width,
    tooltip: true,
  },
  {
    title: "Staff",
    field: "staff",
    sorter: "string",
    headerFilter: "input",
    headerTooltip: "Ответсвенный за исполнение заявки",
    tooltip: true,
    minWidth: 130,
  },
  {
    title: "Status",
    field: "staff_status",
    sorter: "boolean",
    formatter: "tickCross",
    minWidth: 40,
    headerTooltip: "Статус уведомления о заявке ответсвенного",
    tooltip: true,
  },
  {
    title: "repair_time",
    field: "repair_time",
    sorter: "DateTime",
    hozAlign: "center",
    headerTooltip: "Дата и время препологаемого окончния ремонта",
    tooltip: true,
    minWidth: 150,
    sorterParams: {
      format: "dd-MM-yyyy HH:mm",
      alignEmptyValues: "top",
    },
  },
  {
    title: "Departament",
    field: "department",
    sorter: "string",
    minWidth: 150,
  },
  {
    title: "Open date",
    field: "date",
    hozAlign: "center",
    sorter: "DateTime",
    tooltip: true,
    minWidth: 130,
    sorterParams: {
      format: "dd-MM-yyyy HH:mm",
      alignEmptyValues: "top",
    },
  },
  {
    title: "Other",
    field: "other",
    formatter: "html",
    headerSort: false,
    hozAlign: "center",
    headerTooltip: "Дополнительная информация",
    tooltip: true,
    minWidth: 50,
  },
];

if (document.documentElement.clientWidth <= 450) {
  // код для мобильных устройств
   columsdataClose = [
    {
      title: "Adress",
      field: "adress",
      sorter: "string",
      frozen: true,
      tooltip: true,
      minWidth: document.documentElement.clientWidth,
      headerFilter: "input",
    },
  ];
}

const tablgeight = document.documentElement.clientHeight - 80;
//функция создания экземпляра таблицы открытых заявок
export let tableOpen = new Tabulator("#open_calls_table", {
  persistence: true,
  ajaxURL: "/disp/calls.php?data=open",
  footerElement: "<span id='info'></span>",

  height: tablgeight,
  layout: "fitColumns",
  placeholder: "Нет данных для отображения. (Нет открытых заявок)",
  locale: true,
  
  //настройка столбцов таблицы
  columns: columsdata,
  langs: {
    ru: {
      columns: {
        name: "Имя", //replace the title of column name with the value "Name"
        progress: "Прогресс",
        adress: "Адрес : ",
        staff_status: "Сататус",
        details: "Описание проблемы",
        staff: "Ответсвенный",
        repair_time: "Дата ремонта",
        date: "Дата открытия",
        department: "Отдел",
        other: "Дополнительно",
      },
      data: {
        loading: "Загрузка", //data loader text
        error: "Ошибка", //data error text
      },
      groups: {
        //copy for the auto generated item count in group header
        item: "Пункт", //the singular  for item
        items: "Пункты", //the plural for items
      },
      pagination: {
        page_size: "Размер страницы", //label for the page size select element
        page_title: "Показать страницу", //tooltip text for the numeric page button, appears in front of the page number (eg. "Show Page" will result in a tool tip of "Show Page 1" on the page 1 button)
        first: "First", //text for the first page button
        first_title: "Первая страница", //tooltip text for the first page button
        last: "Last",
        last_title: "Последняя страница",
        prev: "Prev",
        prev_title: "Предидущая страница",
        next: "Next",
        next_title: "Следующая страница",
        all: "Все",
        counter: {
          showing: "Показать",
          of: "от",
          rows: "порядок",
          pages: "страниц",
        },
      },
      headerFilters: {
        default: "поиск....", //default header filter placeholder text
        columns: {
          adress: "филтр по адресу...", //replace default header filter text for column name
          call_staff: "Фильтр по отв.",
        },
      },
    },
  },
});


//close table configuraion
let columsdataClose = [
  {
    title: "Adress",
    field: "adress",
    sorter: "string",
    frozen: true,
    tooltip: true,
    minWidth: adres_width,
    headerFilter: "input",
  },
  {
    title: "Details",
    field: "details",
    headerSort: false,
    minWidth: adres_width,
    tooltip: true,
  },
  {
    title: "Solution",
    field: "solution",
    headerSort: false,
    minWidth: adres_width,
    tooltip: true,
  },
  
  {
    title: "Staff",
    field: "staff",
    sorter: "string",
    headerFilter: "input",
    headerTooltip: "Ответсвенный за исполнение заявки",
    tooltip: true,
    minWidth: 130,
  },
  {
    title: "repair_time",
    field: "repair_time",
    sorter: "DateTime",
    hozAlign: "center",
    headerTooltip: "Дата и время препологаемого окончния ремонта",
    tooltip: true,
    minWidth: 150,
    sorterParams: {
      format: "dd-MM-yyyy HH:mm",
      alignEmptyValues: "top",
    },
  },
  {
    title: "Open date",
    field: "date",
    hozAlign: "center",
    sorter: "DateTime",
    tooltip: true,
    minWidth: 130,
    sorterParams: {
      format: "dd-MM-yyyy HH:mm",
      alignEmptyValues: "top",
    },
  },
  {
    title: "Close date",
    field: "close_date",
    hozAlign: "center",
    sorter: "DateTime",
    tooltip: true,
    minWidth: 130,
    sorterParams: {
      format: "dd-MM-yyyy HH:mm",
      alignEmptyValues: "top",
    },
  },
  
];

//great close calls table
export let tableClose = new Tabulator("#close_calls_table", {
  persistence: true,
  ajaxURL: "/disp/calls.php?data=close",
  footerElement: "<span id='info-close'></span>",

  height: tablgeight,
  layout: "fitColumns",
  placeholder: "Нет данных для отображения. (Нет открытых заявок)",
  locale: true,

  //настройка столбцов таблицы
  columns: columsdataClose,
  langs: {
    ru: {
      columns: {
        name: "Имя", //replace the title of column name with the value "Name"
        progress: "Прогресс",
        adress: "Адрес : ",
        staff_status: "Сататус",
        details: "Описание проблемы",
        staff: "Ответсвенный",
        repair_time: "Дата ремонта",
        date: "Дата открытия",
        close_date:"Дата закрытия",
        department: "Отдел",
        other: "Дополнительно",
        solution:"Решение"
      },
      data: {
        loading: "Загрузка", //data loader text
        error: "Ошибка", //data error text
      },
      groups: {
        //copy for the auto generated item count in group header
        item: "Пункт", //the singular  for item
        items: "Пункты", //the plural for items
      },
      pagination: {
        page_size: "Размер страницы", //label for the page size select element
        page_title: "Показать страницу", //tooltip text for the numeric page button, appears in front of the page number (eg. "Show Page" will result in a tool tip of "Show Page 1" on the page 1 button)
        first: "First", //text for the first page button
        first_title: "Первая страница", //tooltip text for the first page button
        last: "Last",
        last_title: "Последняя страница",
        prev: "Prev",
        prev_title: "Предидущая страница",
        next: "Next",
        next_title: "Следующая страница",
        all: "Все",
        counter: {
          showing: "Показать",
          of: "от",
          rows: "порядок",
          pages: "страниц",
        },
      },
      headerFilters: {
        default: "поиск....", //default header filter placeholder text
        columns: {
          adress: "филтр по адресу...", //replace default header filter text for column name
          call_staff: "Фильтр по отв.",
        },
      },
    },
  },
});