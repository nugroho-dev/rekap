	@php
		use Carbon\Carbon;

		$namaBulan = [];
		$jumlahData = [];
		foreach ($items as $data) {
			$namaBulan[] = Carbon::createFromDate(null, $data['bulan'], 1)->translatedFormat('F');
			$jumlahData[] = $data['jumlah_data'];
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
					enabled: false,
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
					name: "Izin Terbit",
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
					show: false,
				},
				point: {
					show: false
				},
			})).render();
		});
	</script>
	
	 