    @php
	  use Carbon\Carbon;

	  $namaBulan = [];
	  for ($i = 1; $i <= 12; $i++) {
		$namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
	  }
	  @endphp
	    <script>
			// @formatter:off
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
						name: "Purchases",
						data: [@foreach ($terbit as $data) 
						{{ $data->jumlah_data }},
						@endforeach]
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
					labels: [
						@foreach ($terbit as $data) 
                	'{{ Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}',
                	@endforeach
					],
					colors: [tabler.getColor("primary")],
					legend: {
						show: false,
					},
					point: {
						show: false
					},
				})).render();
			});
			// @formatter:on
		  </script>
	
	 