import {fetchDataServer} from "../main.js";

export class saveButton {
    #button;
        constructor() {
        if (saveButton._instance) {
            return saveButton._instance;
        }
       this.#button = document.createElement('div');
        this.#button.classList.add("button_save", "pulse")
        document.body.appendChild(this.#button);

        saveButton._instance = this;
        this.#button.addEventListener('click', this.save)
    }
hidden (){
    this.#button.classList.add("hidden");
}
    display(){
            this.#button.classList.remove("hidden");
            this.#button.classList.add("button_save", "pulse")
            this.#button.addEventListener('click', this.save)

    }
   set text(text){
            this.#button.innerText=text;
   }
   disabled(){
      this.#button.classList.remove("pulse") ;
      this.#button.removeEventListener('click', this.save);
   }
    save() {
        const savedResult = function (message) {

           const  button=new saveButton();
            console.log(button)
           if (message.status==="ok") {
               button.text="Сохранено"}
           else{
               button.text="Ошибка";
               console.error(message.message)
           }
           button.disabled();

           setTimeout( ()=>{
               const button=new saveButton();
               button.hidden()
           },4000)
        }
        let modified=document.querySelectorAll('[modified=true]');
        console.log(modified);
        let saveData=[];
       for (let i=0; i<modified.length; i++){
           console.log(modified[i].dataset);
         saveData.push(modified[i].dataset);
       }
        fetchDataServer(URI + "/get_setting.php", {
            action: "get",
            type: "setting",
            userId: userId,
            nacl: nacl,
            data: saveData
        }, savedResult)
    }


}