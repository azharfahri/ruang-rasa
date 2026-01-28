document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Grafik Tren Penjualan
    const salesChart = new ApexCharts(document.querySelector("#salesChart"), {
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#5d87ff'],
        series: [{
            name: 'Pendapatan',
            data: window.dashboardData.salesValues
        }],
        xaxis: {
            categories: window.dashboardData.salesLabels,
        },
        yaxis: {
            labels: {
                formatter: (val) => "Rp " + val.toLocaleString('id-ID')
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.5,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        grid: { borderColor: '#f1f1f1' }
    });
    salesChart.render();

    // 2. Inisialisasi Grafik Status Pesanan
    const statusChart = new ApexCharts(document.querySelector("#statusChart"), {
        chart: {
            type: 'donut',
            height: 320
        },
        series: window.dashboardData.statusStats,
        labels: ['Selesai', 'Proses', 'Pending', 'Siap Diambil'],
        colors: ['#13de1d', '#ffae1f', '#fa896b', '#13deb9'],
        legend: { position: 'bottom' },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Order',
                            formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        }
    });
    statusChart.render();

    // 3. Logika Filter AJAX (Tanpa Refresh)
    document.querySelectorAll('.chart-filter').forEach(select => {
        select.addEventListener('change', function () {
            const type = this.dataset.type; // 'sales' atau 'status'
            const filterValue = this.value;
            const cardBody = this.closest('.card').querySelector('.card-body');

            // Efek Loading Sederhana
            cardBody.style.opacity = '0.5';
            this.disabled = true;

            // Memanggil endpoint API yang kita buat di controller
            fetch(`/cashier/dashboard/data?type=${type}&filter=${filterValue}`)
                .then(response => response.json())
                .then(data => {
                    if (type === 'sales') {
                        // Update Data dan Label Sumbu X untuk Area Chart
                        salesChart.updateOptions({
                            xaxis: { categories: data.labels },
                            series: [{ data: data.values }]
                        });
                    } else if (type === 'status') {
                        // Update Angka saja untuk Donut Chart
                        statusChart.updateSeries(data.stats);
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    cardBody.style.opacity = '1';
                    this.disabled = false;
                });
        });
    });
});
