	@php
		use Carbon\Carbon;
		// Safeguard: ensure $items iterable to avoid undefined variable errors when view required.
		$items = (isset($items) && is_iterable($items)) ? $items : [];
		$namaBulan = [];
		$jumlahData = [];
		foreach ($items as $data) {
			$bulanRaw = is_array($data) ? ($data['bulan'] ?? null) : ($data->bulan ?? null);
			$jumlahRaw = is_array($data) ? ($data['jumlah_data'] ?? null) : ($data->jumlah_data ?? null);
			if ($bulanRaw !== null) {
				try { $namaBulan[] = Carbon::createFromDate(null, (int)$bulanRaw, 1)->translatedFormat('F'); } catch (\Throwable $e) { /* skip invalid month */ }
			}
			$jumlahData[] = (int)($jumlahRaw ?? 0);
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
	
	 