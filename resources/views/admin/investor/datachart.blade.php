	@php
	use Carbon\Carbon;

	$namaBulan = [];
	$jumlahData = [];
	foreach ($proyek as $data) {
		$namaBulan[] = Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F');
		$jumlahData[] = $data->jumlah_nib;
	}
	@endphp

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			window.ApexCharts && (new ApexCharts(document.getElementById('chart-development-activity-sicantik'), {
				chart: {
					type: "area",
					fontFamily: 'inherit',
					height: 192,
					sparkline: {
						enabled: true
					},
					animations: {
						enabled: false
					},
				},
				dataLabels: {
					enabled: true,
				},
				fill: {
					opacity: .16,
					type: 'solid'
				},
				stroke: {
					width: 2,
					lineCap: "round",
					curve: "smooth",
				},
				series: [{
					name: "Proyek",
					data: @json($jumlahData)
				}],
				tooltip: {
					theme: 'dark'
				},
				grid: {
					strokeDashArray: 4,
				},
				xaxis: {
					labels: {
						padding: 0,
					},
					tooltip: {
						enabled: false
					},
					axisBorder: {
						show: false,
					},
				},
				yaxis: {
					labels: {
						padding: 4
					},
				},
				labels: @json($namaBulan),
				colors: [tabler.getColor("primary")],
				legend: {
					show: true,
				},
				point: {
					show: true
				},
			})).render();
		});
	</script>
	
	 