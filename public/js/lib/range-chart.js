function range_init(config = {
    item : "range",
    label : "label",
    maximo: 100,
    valor : 50,
    background : "red"
}) {
    var left = 20;
    elements = document.getElementsByClassName(config.item);
    for (let element of elements) {
        if (element.classList.contains("big")) {
            left = 25;
        }
        let area_total = document.createElement("div");
        let area_paint = document.createElement("div");
        let circle = document.createElement("span");
        let label = document.createElement("span");
        if (element.getAttribute("data-max")) {
            config.maximo = element.getAttribute("data-max");
        }
        if (element.getAttribute("data-valor")) {
            config.valor = calculate_valor(config.maximo, element.getAttribute("data-valor"))
            if (config.valor > 100) {
                config.valor = 100;
            }
        } else {
            config.valor = calculate_valor(config.maximo, config.valor);
        }
        if (config.valor < 10) {
            left = 0;
        }
        if (element.getAttribute("data-background")) {
            config.background = element.getAttribute("data-background");
        }
        if (element.getAttribute("data-label")) {
            config.label = element.getAttribute("data-label") + " de: " + config.maximo;
        } else {
            config.label = config.label + " de: " + config.maximo;
        }

        area_paint.style.width = config.valor + "%";
        area_paint.style.backgroundColor = config.background;
        circle.style.backgroundColor = config.background;
        circle.style.left = "calc(" +config.valor +"% - "+left+"px)";
        label.innerText = config.label;
        if (config.valor > 60) {
            config.background = "black";
        }
        label.style.color = config.background;
        area_total.classList.add("area-total");
        area_paint.classList.add("area-paint");
        circle.classList.add("make")
        label.classList.add("label");
        area_total.append(circle);
        area_total.append(label);
        area_total.append(area_paint);
        init(element, area_total);
    }
        function init(element, area_total) {
        element.append(area_total)
    }


    function calculate_valor(total, valor) {
        var porcentaje = Math.trunc((valor * 100)/total);
        return porcentaje;
    }
}

range_init();