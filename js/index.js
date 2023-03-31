const params = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});
// Get the value of "some_key" in eg "https://example.com/?some_key=some_value"
let tokenAuth = params.e; // "some_value"
if (tokenAuth) {
    localData("exit");
}
let detect = new MobileDetect(window.navigator.userAgent);
const button = document.getElementById("login");
let mobile = detect.mobile();

let localSt = localData();
if (localSt) {
    //если есть данные для автоматического входа отправим их на сервер
 
    button.classList.add("button--loading");
    button.disabled = true;
      let txtold = button.innerText;
      button.innerText="Авторизуемся";
    fetch("login2.php", {
        method: "post",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: localSt['id'],
            token: localSt['token'],
            mobile: mobile
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            button.classList.remove("button--loading");
            console.log(data);
            if (data.status === "error") {
                buttonalert(data.message, 'fontred');
                setTimeout(buttonalert, 5000, txtold);
                const storage = window['localStorage']
                storage.removeItem('token');
            }
            if (data.status === "ok") {
                buttonalert(data.message, 'fontgreen');
                setTimeout(buttonalert, 2000, txtold);
                console.log(data.url);
                // перейдем на основную страницу
                window.location.replace(data.url);
            }
        })
        .catch((error) => {
            button.classList.remove("button--loading");
            console.error("Error:", error);
        });
}


console.log(mobile);

button.addEventListener("click", login,true);

function login(Event) {
    Event.preventDefault();
    let username = document.getElementById("username").value;
    let pass = document.getElementById("pass").value;
    let autoLogin=document.getElementById("autoLogin").checked;
    button.classList.add("button--loading");
    button.disabled = true;
    console.log(JSON.stringify({
        name: username,
        pname: pass,
        mobile: mobile,
        autoLogin:autoLogin
    }))
    fetch("login2.php", {


        method: "post",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        //make sure to serialize your JSON body
        body: JSON.stringify({
            name: username,
            pname: pass,
            mobile: mobile,
            autoLogin:autoLogin
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            button.classList.remove("button--loading");
            if (data.status === "error") {
                let txtold = button.innerText;
                buttonalert(data.message, 'fontred');
                setTimeout(buttonalert, 5000, txtold);
            }
            if (data.status === "ok") {
                let txtold = button.innerText;
                buttonalert(data.message, 'fontgreen');
                setTimeout(buttonalert, 2000, txtold);
                console.log('save', data.url);
                if (data.token.length != 32) {
                    console.log('save',);
                    window.location.replace(data.url);
                    // если нет токена то переходим дальше
                } else {
                    localData('save', data.token, data.id, data.userName)
                    window.location.replace(data.url);
                    // сохранимся в локальном хранилище
                }
            }

        })
        .catch((error) => {
            button.classList.remove("button--loading");
            let txtold = button.innerText;
            buttonalert('Ошибка сервера', 'fontred');
            setTimeout(buttonalert, 5000, txtold);
            console.error("Error:", error);
        });
}

function buttonalert(txt, act = false) {
    if (act) {
        button.innerText = txt;
        button.classList.add(act);
    } else {
        button.innerText = txt;
        //button.className = '';
        button.disabled = false;
    }
}

function storageAvailable(type) {
    try {
        let storage = window[type],
            x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    } catch (e) {
        return false;
    }
}

function localData(type = '', token = '', id = '', name = '') {
    if (type === "exit") {
        if (storageAvailable('localStorage')) {
            // localStorage работает
            let storage = window['localStorage']


            login['token'] = storage.getItem('token', token);
            if (login['token']?.length > 0) {
                storage.removeItem("token");
            }
            login['id'] = storage.getItem('id', id);
            login['username'] = storage.getItem('name', name);
            return login;

        } else {
            console.warn("localStorage не работает");
            return false;
        }
    }
    if (type === "save") {
        if (storageAvailable('localStorage')) {
            // localStorage работает
            let storage = window['localStorage']
            storage.setItem('token', token);
            storage.setItem('id', id);
            storage.setItem('name', name);

        } else {
            console.warn("localStorage не работает");
            return;
        }
    } else {
        if (storageAvailable('localStorage')) {
            // localStorage работает
            let storage = window['localStorage']
            let login = [];

            login['token'] = storage.getItem('token', token);
            if (login['token']?.length != 32) {
                return false;
            }
            login['id'] = storage.getItem('id', id);
            login['username'] = storage.getItem('name', name);
            return login;

        } else {
            console.warn("localStorage не работает");
            return false;
        }
    }
}
