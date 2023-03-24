export class createlist {
    // класс для создания списка объектов
   #list = [];
   #inputSerch;
   #buttonNext;
   #divinput;
    constructor(arrayList, type, parentType, parentList, parentTitle) {
      this.array = arrayList;
      this.type = type;
      this.parentType = parentType;
      this.parentList=parentList;
      this.parentTitle=parentTitle;
    }
    get value(){
      return this.#inputSerch.value;
    }
    get id(){
      return this.#inputSerch.id;
    }
    /**
     * @param {any} HTML
     */
    set divname(HTML){
  this.#divinput.innerHTML=HTML;
    }
    generateTitle(callback){
      this.#divinput=document.createElement('div');
      this.#divinput.classList.add("div_add_new_input");
      this.#inputSerch=document.createElement('input');
      this.#inputSerch.classList.add("input_title");
      this.#buttonNext=document.createElement('button');
      this.#buttonNext.classList.add("button_next");
      this.#buttonNext.innerText="Далее";
      this.#buttonNext.disabled=true;
      //console.log(this.#inputSerch,this.#buttonNext);
      this.parentTitle.append(this.#divinput);
      this.#divinput.append(this.#inputSerch);
      this.#divinput.append(this.#buttonNext);
      this.parentList.className="addnewcall_conteiner addnewcall_conteiner_"+this.type;
      this.generateList();
      this.#buttonNext.addEventListener('click',callback);
      this.#inputSerch.addEventListener("input",()=>{this.generateList(this.#inputSerch,this.#buttonNext)},false)
    
    }
  
    fullList(parentid) {
      if (!parentid){
         this.#list = this.array;
         return;
      } 
         //генерируем полный список с филтром по родительскому элементу
    
      this.#list = this.array.filter(
        (value) => value[this.parentType] == parentid
      );
      //console.log(this.#list);
    }
    generateList(input=this.#inputSerch,button=this.#buttonNext) {
      //console.log(this.#list);
      //создаем обекты исходя из введеное поискового запроса
      this.parentList.innerHTML = "";
      let serch= input.value;
      let top=document.createElement('div');
button.after(top);
      if(input.value.length===0){ button.disabled=true;}
      if(!Array.isArray(this.#list)){
        console.warn("not array ");
        this.#list=Object.entries(this.#list);
      }
      this.#list.forEach((element) => { 
        
       //console.log(  element[this.type + "_name"]);
        if (
          element[this.type + "_name"]
            .toLowerCase()
            .indexOf(serch.toLowerCase()) !== -1
        ) {
         //console.log(element[this.type + "_name"]);
          let div = document.createElement("div");
          div.classList.add("adress_card");
          div.innerText = element[this.type + "_name"];
          div.id = element.id;
          this.parentList.append(div);
        }
      });
      this.parentList.addEventListener("click", function (event) {
        //console.log("click->adresslist");
      top.scrollIntoView({block: "center", behavior: "smooth"});
       input.value='';
       input.value=event.target.innerText;
       input.id=event.target.id;
       button.id=event.target.id;
       button.disabled=false;
      }, true);
    }
  }
  