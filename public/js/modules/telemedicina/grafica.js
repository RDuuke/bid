var ctx = document.getElementById("telemedicinaGrafica").getContext("2d");
var speedData = {
    labels: ["Inicio", "Enero/18", "Febrero/18", "Marzo/18", "Abril/18", "Mayo/18", "Junio/18", "Julio/18"],
    datasets: [{
        label: "Número de prestaciones por mes.",
        data: [0, 10, 59, 75, 20, 20, 55, 40],
    }]
};

var chartOptions = {
    legend: {
        display: true,
        position: 'top',
        labels: {
            boxWidth: 80,
            fontColor: 'black'
        }
    }
};
var LineChart = new Chart(ctx, {
    type: 'line',
    data: speedData,
    options: chartOptions
});