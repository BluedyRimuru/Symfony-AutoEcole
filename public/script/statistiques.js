const barCanvas = document.getElementById("barCanvas");
// bar, pie, doughnut, and more... at https://www.chartjs.org/docs/latest/charts/doughnut.html
const barChart = new Chart(barCanvas, {
    type: "doughnut",
    data: {
        labels: ["Beijing", "Tokyo", "Seoul", "Hong Kong"],
        datasets: [{
            data: [240, 120, 140, 130]
        }]
    }
})