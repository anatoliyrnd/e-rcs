const objBody = document.getElementById("object");
import { mainBody, deleteObject } from "./export/setting_object.js";
import { startEditObj, fetchLoad } from "./export/edit_add_obj.js";
mainBody(objBody, objectClick);
const closeDialog = document.getElementById("close"); //кнопка закрытия модального окна
const saveDialog = document.getElementById("save"); //кнопка сохранить - модального окна
const titleDialog = document.getElementById("title_dialog"); // заголовок модального окна
const dialog = document.querySelector("dialog"); //модальное окно
const bodyDialog = document.getElementById("body_dialog"); // тело модального окна
closeDialog.addEventListener("click", modalClose);
dialog.addEventListener("cancel", modalClose);
function objectClick() {
  const action = this.dataset.action;
  if (action === "object_edit") {
    showDialog("editAddObject");
    bodyDialog.innerHTML = "<div class='loader'>Загружаем списко адресов</div>";
    const editObject = new startEditObj(titleDialog, bodyDialog);
    editObject.start("../settings/edit_obj_control.php");
    saveDialog.disabled = false;
    saveDialog.innerText = "Проверить";
    saveDialog.addEventListener("click", () => {
      if (editObject.inputCheck()) {
        if (confirm("Проверка прошла успешно. Сохранить данные?")) {
          editObject.getNewData((result) => {
            console.log(result);
            if (result.status==="ok"){modalClose();}
          });
        } else {
          modalClose();
        }
      }
    },{once:true});
  } else {
    deleteObject(bodyDialog, titleDialog, action);
    showDialog(action);
  }
}
function showDialog(saveAction) {
  //окончательная сборка модалки и ее вывод на экран

  saveDialog.setAttribute("action", saveAction);
  dialog.showModal();
}
function modalClose() {
  //подчищаем все перед закрытием модалки
  titleDialog.innerText = "";
  bodyDialog.innerHTML = "";
  saveDialog.setAttribute("action", "no");
  saveDialog.disabled = true;
  saveDialog.innerHTML = "";
  dialog.close();
}
