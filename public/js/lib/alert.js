let alerts = document.getElementsByClassName("alert");

for (let alert of alerts) {
    alert.addEventListener("click", function (e) {
        e.preventDefault();
        this.style.display = "none";
    });
}