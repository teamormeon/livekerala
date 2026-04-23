<div class="col-lg-6">
    <div class="card-body pb-0" id="totalSalesChart">
        <h4>@lang('Total sales')</h4>
        <div class="row align-items-sm-center mt-4 mt-sm-0">
            <div class="col-sm mb-3 mb-sm-0">
                <span class="display-5 text-dark me-2">{{ currencyPosition($totalSalesAmount) }}</span>
            </div>
            <div class="col-sm-auto">
                  <span class="h3 text-{{ $todayToYesterdaySalesAmountPercentage >= 0 ? 'success' : 'danger' }}">
                    <i class="bi-graph-{{ $todayToYesterdaySalesAmountPercentage >= 0 ? 'up' : 'down' }}"></i> {{ $todayToYesterdaySalesAmountPercentage }}%
                  </span>
                <span class="d-block">&mdash; {{ $totalOrderCount }} @lang('orders') <span class="badge bg-soft-dark text-dark rounded-pill ms-1">{{ currencyPosition($todaySalesAmount) }} today</span></span>
            </div>
        </div>

        <div class="chartjs-custom">
            <canvas id="ecommerce-total-sales"></canvas>
        </div>

    </div>
</div>


@push('script')
    <script>
        $(document).ready(function() {
            Notiflix.Block.standard('#totalSalesChart');
            $.ajax({
                url: '{{ route("admin.get.totalSalesHistory") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    updateChart(response.labels, response.dataSet1, response.dataSet2);
                    Notiflix.Block.remove('#totalSalesChart');
                },
            });

            function updateChart(labels, dataSet1, dataSet2) {
                var ctx = document.getElementById('ecommerce-total-sales').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Today',
                            data: dataSet1,
                            backgroundColor: "transparent",
                            borderColor: "#377dff",
                            borderWidth: 2,
                            pointRadius: 0,
                            hoverBorderColor: "#377dff",
                            pointBackgroundColor: "#377dff",
                            pointBorderColor: "#fff",
                            pointHoverRadius: 0,
                            tension: 0.4
                        },
                            {
                                label: 'Yesterday',
                                data: dataSet2,
                                backgroundColor: "transparent",
                                borderColor: 'rgba(153,153,153,0.50)',
                                hoverBorderColor: 'rgba(153,153,153,0.50)',
                                pointBackgroundColor: 'rgba(153,153,153,0.50)',
                                pointBorderColor: "#fff",
                                borderWidth: 2,
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                grid: {
                                    color: "#e7eaf3",
                                    drawBorder: false,
                                    zeroLineColor: "#e7eaf3"
                                },
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 100,
                                    color: "#97a4af",
                                    font: {
                                        size: 12,
                                        family: "Open Sans, sans-serif"
                                    },
                                    padding: 5,
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: "#97a4af",
                                    font: {
                                        size: 12,
                                        family: "Open Sans, sans-serif"
                                    },
                                    padding: 5
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label +=  currencyPosition(context.parsed.y);
                                        }
                                        return label;
                                    }
                                },
                                mode: "index",
                                intersect: false,
                                lineMode: true,
                                lineWithLineColor: "rgba(19, 33, 68, 0.075)"
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    color: '#333',
                                    padding: 20,
                                    boxWidth: 20,
                                    boxHeight: 10,
                                    pointStyle: 'circle',
                                }
                            }
                        },
                        hover: {
                            mode: "nearest",
                            intersect: true
                        }
                    }
                });

            }
        });


    </script>
@endpush
