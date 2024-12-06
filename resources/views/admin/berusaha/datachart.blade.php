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
						enabled: true
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
					name: "NIB Terbit",
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
	   <script>
		// @formatter:off
		document.addEventListener("DOMContentLoaded", function () {
			window.ApexCharts && (new ApexCharts(document.getElementById('chart-mentions'), {
				chart: {
					type: "bar",
					fontFamily: 'inherit',
					height: 240,
					parentHeightOffset: 0,
					toolbar: {
						show: false,
					},
					animations: {
						enabled: true
					},
					stacked: true,
				},
				plotOptions: {
					bar: {
						columnWidth: '50%',
					}
				},
				dataLabels: {
					enabled: true,
				},
				fill: {
					opacity: 1,
				},
				series: [{
					name: "Web",
					data: [@foreach ($totalPerBulan as $data) 
						{{ $data->jumlah_nib }},
						@endforeach]
				},//{
					//name: "Social",
					//data: [2, 5, 4, 3, 3, 1, 4, 7, 5, 1, 2, 5, 3, 2, 6, 7, 7, 1, 5, 5, 2, 12, 4, 6, 18, 3, 5, 2, 13, 15, 20, 47, 18, 15, 11, 10, 0]
				//},{
					//name: "Other",
					//data: [2, 9, 1, 7, 8, 3, 6, 5, 5, 4, 6, 4, 1, 9, 3, 6, 7, 5, 2, 8, 4, 9, 1, 2, 6, 7, 5, 1, 8, 3, 2, 3, 4, 9, 7, 1, 6]
				//}
			],
				tooltip: {
					theme: 'dark'
				},
				grid: {
					padding: {
						top: -20,
						right: 0,
						left: -4,
						bottom: -4
					},
					strokeDashArray: 4,
					xaxis: {
						lines: {
							show: true
						}
					},
				},
				xaxis: {
					labels: {
						padding: 0,
					},
					tooltip: {
						enabled: false
					},
					axisBorder: {
						show: true,
					},
					//type: 'datetime',
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
				//colors: [tabler.getColor("primary"), tabler.getColor("primary", 0.8), tabler.getColor("green", 0.8)],
				legend: {
					show: true,
				},
			})).render();
		});
		// @formatter:on
	  </script>
	  <script>
      // @formatter:off
      document.addEventListener("DOMContentLoaded", function () {
      	window.ApexCharts && (new ApexCharts(document.getElementById('chart-campaigns'), {
      		chart: {
      			type: "radialBar",
      			fontFamily: 'inherit',
      			height: 240,
      			sparkline: {
      				enabled: true
      			},
      			animations: {
      				enabled: true
      			},
      		},
      		plotOptions: {
      			radialBar: {
      				dataLabels: {
      					total: {
      						show: true,
      						label: 'Totals',
      						formatter: function (val) {
      							return "44%";
      						},
      					},
      				},
      			},
      		},
      		fill: {
      			opacity: 1,
      		},
      		series: [44, 36, 18],
      		labels: ["Total Sent", "Reached", "Opened"],
      		tooltip: {
      			theme: 'dark'
      		},
      		grid: {
      			strokeDashArray: 4,
      		},
      		colors: [tabler.getColor("primary"), tabler.getColor("primary", 0.8), tabler.getColor("primary", 0.6)],
      		legend: {
      			show: false,
      		},
      	})).render();
      });
      // @formatter:on
    </script>
	<script>
		// @formatter:off
		document.addEventListener("DOMContentLoaded", function () {
			window.ApexCharts && (new ApexCharts(document.getElementById('chart-demo-pie'), {
				chart: {
					type: "donut",
					fontFamily: 'inherit',
					height: 240,
					sparkline: {
						enabled: true
					},
					animations: {
						enabled: true
					},
				},
				fill: {
					opacity: 1,
				},
				dataLabels: {
					enabled: true,
				},
				series: [ @foreach ($itemsrisiko as $data) {{ $data->total }}, @endforeach],
				labels: [@foreach ($itemsrisiko as $data) "{{ $data->kd_resiko == 'R' ? 'Rendah' : ($data->kd_resiko == 'MR'? 'Menegah Rendah' : ($data->kd_resiko == 'MT'? 'Menegah Tinggi' : ($data->kd_resiko == 'T'? 'Tinggi' :'Unclassified')))}}", @endforeach],
				tooltip: {
					theme: 'dark'
				},
				grid: {
					strokeDashArray: 4,
				},
				colors: [tabler.getColor("primary"), tabler.getColor("primary", 0.8), tabler.getColor("green"),tabler.getColor("green", 0.8), tabler.getColor("red")],
				legend: {
					show: true,
					position: 'bottom',
					offsetY: 12,
					markers: {
						width: 10,
						height: 10,
						radius: 100,
					},
					itemMargin: {
						horizontal: 8,
						vertical: 8
					},
				},
				tooltip: {
					fillSeriesColor: false
				},
			})).render();
		});
		// @formatter:on
	  </script>
	  <script>
		// @formatter:off
		document.addEventListener("DOMContentLoaded", function () {
			window.ApexCharts && (new ApexCharts(document.getElementById('chart-mentions-II'), {
				chart: {
					type: "bar",
					fontFamily: 'inherit',
					height: 240,
					parentHeightOffset: 0,
					toolbar: {
						show: true,
					},
					animations: {
						enabled: true
					},
					stacked: true,
				},
				plotOptions: {
					bar: {
						columnWidth: '50%',
					}
				},
				dataLabels: {
					enabled: true,
				},
				fill: {
					opacity: 1,
				},
				series: [{
					name: "Rendah",
					data: [@foreach ($resikoPerBulan as $data){{ $data->R }},@endforeach]
				},{
					name: "Menengah Rendah",
					data: [@foreach ($resikoPerBulan as $data){{ $data->MR }},@endforeach]
				},{
					name: "Mengeh Tinggi",
					data: [@foreach ($resikoPerBulan as $data){{ $data->MT }},@endforeach]
				},{
					name: "Tinggi",
					data: [@foreach ($resikoPerBulan as $data){{ $data->T }},@endforeach]
				},{
					name: "Unclassified",
					data: [@foreach ($resikoPerBulan as $data){{ $data->UNCLAS }},@endforeach]
				}],
				tooltip: {
					theme: 'dark'
				},
				grid: {
					padding: {
						top: -20,
						right: 0,
						left: -4,
						bottom: -4
					},
					strokeDashArray: 4,
					xaxis: {
						lines: {
							show: true
						}
					},
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
					//type: 'datetime',
				},
				yaxis: {
					labels: {
						padding: 4
					},
				},
				labels: [
					@foreach ($resikoPerBulan as $data)
					'{{ Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}',
					@endforeach
				],
				colors: [tabler.getColor("primary"), tabler.getColor("primary", 0.8), tabler.getColor("green"),tabler.getColor("green", 0.8),tabler.getColor("red")],
				legend: {
					show: true,
				},
			})).render();
		});
		// @formatter:on
	  </script>