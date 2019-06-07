function circle_init()
{
    let divs = document.getElementsByClassName("circle-range");

    for (let element of divs) {
        let content = document.createElement("div");
        let range = document.createElement("div");
        let porcentaje = document.createElement("span");

        content.append(range);
        content.append(porcentaje);
        element.append(content);
    }
}