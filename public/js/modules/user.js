var http = new XMLHttpRequest();

var users = document.getElementsByClassName("user-delete");

for (let user of users) {
    user.addEventListener("click", function (event) {
        event.preventDefault();
        http.open("GET", this.getAttribute("href"));
        http.addEventListener("error", onError);
        http.addEventListener("loadend", onSuccess);
        http.send()

    })
}
function onSuccess() {
    if ( this.statusCode != 500 )  {
        console.log(JSON.parse(this.response));
    } else {
        console.log(this.startMessages());
    }
}

function onError(e) {
    console.log(e);
}