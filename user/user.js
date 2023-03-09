

document.addEventListener("DOMContentLoaded", () => {
    
    const nav = document.getElementById("footer");
    const title=document.getElementById("header");
    const body=document.getElementById("body");
    let closeHtml;
    let noteHtml;
    getDataUser(body,"/user/send_data.php",title,"Загружаем данные", "Загрузка завершена");
   
    
    body.addEventListener("click",(event)=>{
        //console.log(event.target.id);
        if (event.target.id==="close"){
            //переход на форму закрытия заявки
            //console.log(event.target.dataset.id);
             closeHtml=getCloseHtml(event.target.dataset.id,event.target.dataset.adress);
            title.innerHTML="Закрыть заявку";
            toggleTwoClasses(body,closeHtml,1000);
        }
        if (event.target.id==="notes"){
            //переход на форму заметок к заявке
            //console.log("notes-",event.target.dataset.id);
             noteHtml=getNotesHtml(event.target.dataset.id,data.notes,event.target.dataset.adress );
            title.innerHTML="Заметки";
            toggleTwoClasses(body,noteHtml,1000);
        }
        if (event.target.id==="closeCall"){
            //закрываем заявку

            //console.log(event.target.dataset.id, closeInput);
             getDataUser(body,`/user/save_data.php?action=callClose&solution=${closeInput}&call_id=${event.target.dataset.id}&nacl=${nacl}`,title,"Закрываем заявку", "Заявка зарыта","addNote");
            toggleTwoClasses(body,'',1000);
            
          
        }
        if (event.target.id==="addNote"){
            //добавляем заметку
          
           
            
             getDataUser(body,`/user/save_data.php?action=addNote&note=${noteInput}&call_id=${event.target.dataset.id}&nacl=${nacl}`,title,"Сохраняем заметку", "Заметка сохранена","addNote");
         
          
           
        
    }
        if (event.target.id==="read"){
            //нажали кнопочку новая (для того что бы передать что приняли)
            let rezult =getDataUser(body,`/user/save_data.php?action=read&call_id=${event.target.dataset.id}&nacl=${nacl}`,title,"Отправляем данные о прочтении", "Загрузка завершена","read");
            if (rezult){event.target.remove();
           getDataUser(body,"/user/send_data.php",title,"Загружаем данные", "Загрузка завершена");//обновим данные
            }
        }

    })
    nav.addEventListener("click", (event) => {

        if (event.target.id == "s1") {
//архив
            body.innerHTML = closeCallHtml(data);
        }
        if (event.target.id == "s2") {
            //открытые
           getDataUser(body,"/user/send_data.php",title,"Звгружаем открытые заявки", "Загрузка завершена");
        
            body.innerHTML = openCallHtml(data);
        }
        if (event.target.id == "s3") {
            //настройки
            body.innerHTML = "Скоро будет";
        }

    })
    function getCloseHtml(id,adress,){
        //формируем форму заявки
        console.log(id);
        let card = "<div id='card'>"
    


        card+=`<div class='card-container'><span class='new' onclick='document.getElementById("s2").click();'><-</span>
        <h3 class='round'>${adress}</h3>
        <p ><textarea maxlength="300" minlength="5" onblur = 'closeInput=this.value' placeholder="Введите сюда решение по заявке" rows="5" class="textCallClose"></textarea></p>
        <div class='buttons'><button class='primary' id='closeCall' data-adress='${adress}' data-id='${id}'>Закрыть заявку</button>
             </div>
      </div>`
      

        card += "</div>";
        return card;
    }
    function getNotesHtml(id,data,adress){
        //формируем форму notes
        noteInput="";// clear global variablea 
      
        let card = "<div id='card'>"
      
       let noteCall=data.find(item => item["call_id"] == id)
       
        card += ` <div class='card-container'><span class='new' onclick='document.getElementById("s2").click();'><-</span>
            <h3 class='round'>${adress}</h3>
            <p>${noteCall.notes} </p>
            <p><textarea maxlength="200" minlength="5" onblur = 'noteInput=this.value' placeholder="Введите сюда новую заметку к заявке" rows="5" class="textCallClose"></textarea></p>
            <div class='buttons'><button class='primary' id='addNote'  data-adress='${adress}' data-id='${id}' >add note</button>
            </div>
            </div>`


        card += "</div>";
        return card;
    }

});



function toggleTwoClasses(element, html,  timeOfAnimation) {

    // element.classList.add(first);
    //element.classList.remove(second);

    element.classList.add("fadeOut");
    element.classList.remove("fadeIn");
    window.setTimeout(function () {
        element.innerHTML = html;
        element.classList.add("fadeIn");
        element.classList.remove("fadeOut");
    }, timeOfAnimation);

}

function openCallHtml(data) {

    let card = "<div id='card'>"
    //console.log(data.openCalls.length);
    if(!data.openCalls.length){
        card="У Вас нет открытых заявок!</div>";
        return card;
    }
    data.openCalls.forEach((value, index) => {
        dateopen=dateEdit(value.call_date);
        //console.log(dateopen);
        card += " <div class='card-container'>";
        if (value.call_staff_status==="0") {
            card += `<span class='new' id='read' data-id='${value.call_id}'>Новая</span>`
        }
        card += ` <span class='date'>${dateopen}</span>
        <h3 class='round'>${value.call_adres}</h3>
        <p>${value.call_details}</p>
        <div class='buttons'><button class='primary' id='close' data-id='${value.call_id}' data-adress='${value.call_adres}'>Закрыть </button>
          <button class='primary ghost' id='notes' data-id='${value.call_id}' data-adress='${value.call_adres}'>Заметки</button>
        </div>
      </div>`;
        //console.log(index);
    })
    card += "</div>";
    return card;
}
function closeCallHtml(data) {
    let card = "<div id='card'>"
    //console.log(data.closeCalls);
   
    data.closeCalls.forEach((value, index) => {
         dateopen=dateEdit(value.call_date);
        dateclose=dateEdit(value.call_date2);
        card += " <div class='card-container'>";
        card += `<span class='new'>${dateclose}</span>`;
        card += ` <span class='date'>${dateopen}</span>
        <h3 class='round'>${value.call_adres}</h3>
        <p>${value.call_details}</p>
        <div class='buttons'>${value.call_solution}
        </div>
        </div>`;
        
    })
    card += "</div>";
    return card;
}
async function getDataUser(body,url,title,titleTxtStart='',titleTxtEnd='',action="get") {

    const new_url = new URL(window.location.origin + url)
    //const new_url = new URL(`${url}${str}`);
    try {
        //
        if(titleTxtStart.length>2){title.innerHTML=titleTxtStart;}
        let response = await fetch(new_url);
        if (response.ok) {
            // получаем ответ в формате JSON и сохраняем его в data
            let getData = await response.json();
            // выведем данные в #result
            
            if (getData.status === "ok") {
               if (titleTxtEnd.length>2){title.innerHTML=titleTxtEnd;}
            if (action==="get"){
                // если получить данные
              data.openCalls=getData.openCalls;
              data.closeCalls=getData.closeCalls;
              data.notes=getData.notes;
               body.innerHTML = openCallHtml(data);
            }
             if (action==="addNote"){
                body.innerHTML = openCallHtml(data);
               
             } 
              

            } else {
                
               title.innerHTML=getData.status;
            }


        } else {

            title.innerHTML=response.status;

        }
    } catch (error) {

        alert("Ошибка получения данных " + error);

    }

}
function dateEdit(timestamp){
    let stamp=timestamp*1000;
        let d=new Date(stamp),
      yyyy = d.getFullYear(),
      mm = ('0' + (d.getMonth() + 1)).slice(-2),  // Months are zero based. Add leading 0.
         dd = ('0' + d.getDate()).slice(-2),         // Add leading 0.
         hh = d.getHours(),
        min = ('0' + d.getMinutes()).slice(-2);    // Add leading 0.
       let dateResult= dd + '-' + mm + '-' + yyyy+ ', ' + hh + ':' + min;
       return dateResult;
    }