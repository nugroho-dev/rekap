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
			window.ApexCharts && (new ApexCharts(document.getElementById('chart-berusaha'), {
				chart: {
					type: "bar",
					fontFamily: 'inherit',
					height: 40.0,
					sparkline: {
						enabled: true
					},
					animations: {
						enabled: false
					},
				},
				plotOptions: {
					bar: {
						columnWidth: '50%',
					}
				},
				dataLabels: {
					enabled: false,
					offsetY: 21,
        			style: {
          				fontSize: "9px",
          				colors: ["#000"]
        				}
				},
				fill: {
					opacity: 1,
				},
				series: [{
					name: "Profits",
					data: [
						
						@foreach ($totalPerBulan as $data) 
						{{ $data->jumlah_nib }},
						@endforeach
						]
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
				@foreach ($totalPerBulan as $data) 
                '{{ Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}',
                @endforeach
				],
				colors: [tabler.getColor("primary")],
				legend: {
					show: false,
				},
			})).render();
		});
		// @formatter:on
	  </script>