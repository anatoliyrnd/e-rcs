import {fetchDataServer} from "../main.js";

export class saveButton {
    #button;
        constructor() {
        if (saveButton._instance) {
            return saveButton._instance;
        }
       this.#button = document.createElement('div');
        this.#button.classList.add("button", "pulse")
        document.body.appendChild(this.#button);
        this.#button.innerText = "сохранить";
        saveButton._instance = this;
        this.#button.addEventListener('click', this.save)
    }
hidden (){
    this.#button.classList.add("hidden");
}
    display(){
            this.#button.classList.remove("hidden");
    }
    save() {
        const savedResult = function (message) {
            console.log(message)
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