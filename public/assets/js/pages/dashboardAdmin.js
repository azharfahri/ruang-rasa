const chartData = window.salesChartData;

var options = {
    series: [{
        name: 'Total Penjualan',
        data: chartData.data
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    xaxis: {
        categories: chartData.labels
    },
    dataLabels: {
        enabled: true
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "Rp " + val.toLocaleString('id-ID');
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#chart-sales-branch"), options);
chart.render();
