var change = document.getElementById("change_password");

change.addEventListener("change", function (e) {
    e.preventDefault();
    document.getElementById("content-password").classList.toggle("none");
});