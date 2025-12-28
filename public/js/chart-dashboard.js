document.addEventListener('DOMContentLoaded', function () {
    function renderApexBarChart(divId, data, label, color) {
        var el = document.getElementById(divId);
        if (!el) return;
        var options = {
            chart: {
                type: 'bar',
                height: 300
            },
            series: [{
                name: label,
                data: [
                    data[1] || 0,
                    data[2] || 0,
                    data[3] || 0,
                    data[4] || 0,
                    data[5] || 0,
                    data[6] || 0,
                    data[7] || 0,
                    data[8] || 0,
                    data[9] || 0,
                    data[10] || 0,
                    data[11] || 0,
                    data[12] || 0
                ]
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            },
            colors: [color],
            dataLabels: {
                enabled: true
            }
        };
        new ApexCharts(el, options).render();
    }

    renderApexBarChart('apexchart-berusaha', window.monthlyBerusaha, 'Berusaha Bulanan', '#36a2eb');
    renderApexBarChart('apexchart-insentif', window.monthlyInsentif, 'Insentif Bulanan', '#ff6384');
    renderApexBarChart('apexchart-pengawasan', window.monthlyPengawasan, 'Pengawasan Bulanan', '#ffce56');
    renderApexBarChart('apexchart-expo', window.monthlyExpo, 'Expo Bulanan', '#4bc0c0');
    renderApexBarChart('apexchart-businessmeeting', window.monthlyBusinessMeeting, 'Business Meeting Bulanan', '#9966ff');
    renderApexBarChart('apexchart-loi', window.monthlyLoi, 'LOI Bulanan', '#ff9f40');
    renderApexBarChart('apexchart-fasilitasi', window.monthlyFasilitasi, 'Fasilitasi Bulanan', '#43a047');
    renderApexBarChart('apexchart-bimtek', window.monthlyBimtek, 'Bimtek Bulanan', '#3949ab');
    renderApexBarChart('apexchart-izin', window.monthlyIzin, 'Izin Bulanan', '#e91e63');
    renderApexBarChart('apexchart-proyek', window.monthlyProyek, 'Proyek Bulanan', '#00bcd4');
    renderApexBarChart('apexchart-nib', window.monthlyNib, 'NIB Bulanan', '#607d8b');
    renderApexBarChart('apexchart-izinterbitsicantik', window.monthlyIzinTerbitSicantik, 'Izin Terbit SiCantik Bulanan', '#388e3c');
    renderApexBarChart('apexchart-pbg', window.monthlyPbg, 'PBG Bulanan', '#1976d2');
    renderApexBarChart('apexchart-simpel', window.monthlySimpel, 'SIMPEL Bulanan', '#00acc1');
    renderApexBarChart('apexchart-mppd', window.monthlyMppd, 'MPPD Bulanan', '#ffa000');
    renderApexBarChart('apexchart-komitmen', window.monthlyKomitmen, 'Komitmen Bulanan', '#009688');
    renderApexBarChart('apexchart-konsultasi', window.monthlyKonsultasi, 'Konsultasi Bulanan', '#8bc34a');
    renderApexBarChart('apexchart-informasi', window.monthlyInformasi, 'Informasi Bulanan', '#cddc39');
    renderApexBarChart('apexchart-pengaduan', window.monthlyPengaduan, 'Pengaduan Bulanan', '#f44336');
    renderApexBarChart('apexchart-produkhukum', window.monthlyProdukHukum, 'Produk Hukum Bulanan', '#ec407a');
    renderApexBarChart('apexchart-petapotensi', window.monthlyPetaPotensi, 'Peta Potensi Bulanan', '#ff7043');
    
});
