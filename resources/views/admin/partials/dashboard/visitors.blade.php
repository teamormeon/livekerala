<div class="col-lg-6">
    <div class="card-body pb-2" id="visitorsChart">
        <h4>@lang('Visitors')</h4>
        <div class="row align-items-sm-center mt-4 mt-sm-0 mb-5">
            <div class="col-sm mb-3 mb-sm-0">
                <span class="display-5 text-dark me-2">{{ $totalVisitorCount }}</span>
            </div>
            <div class="col-sm-auto">
                <span class="h3 {{ $yesterdayVisitorCount != 0 && (($todayVisitorCount - $yesterdayVisitorCount) / $yesterdayVisitorCount) * 100 >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="{{ $yesterdayVisitorCount != 0 && (($todayVisitorCount - $yesterdayVisitorCount) / $yesterdayVisitorCount) * 100 >= 0 ? 'bi-graph-up' : 'bi-graph-down' }}"></i>
                    {{ $yesterdayVisitorCount != 0 ? number_format(abs((($todayVisitorCount - $yesterdayVisitorCount) / $yesterdayVisitorCount) * 100), 2) : 0 }}%
                </span>
                <span class="d-block">&mdash; {{ $uniqueVisitorCount }} unique <span class="badge bg-soft-dark text-dark rounded-pill ms-1">{{ $todayVisitorCount }} today</span></span>
            </div>

        </div>
        <div class="chartjs-custom">
            <canvas id="visitors">
            </canvas>
        </div>
    </div>
</div>


@push('script')
    <script>
        $(document).ready(function() {
            Notiflix.Block.standard('#visitorsChart');
            $.ajax({
                url: '{{ route("admin.get.visitorsHistory") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    updateChart(response.labels, response.dataSet1, response.dataSet2);
                    Notiflix.Block.remove('#visitorsChart');
                },
            });

            function updateChart(labels, dataSet1, dataSet2) {
                var ctx = document.getElementById('visitors').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Today',
                            data: dataSet1,
                            backgroundColor: 'transparent',
                            borderColor: '#377dff',
                            borderWidth: 2,
                            pointRadius: 0,
                            hoverBorderColor: '#377dff',
                            pointBackgroundColor: '#377dff',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 0,
                            tension: 0.4
                        },
                            {
                                label: 'Yesterday',
                                data: dataSet2,
                                backgroundColor: 'transparent',
                                borderColor: 'rgba(153,153,153,0.50)',
                                borderWidth: 2,
                                pointRadius: 0,
                                hoverBorderColor: 'rgba(153,153,153,0.50)',
                                pointBackgroundColor: 'rgba(153,153,153,0.50)',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 0,
                                tension: 0.4
                            }]
                    },
                    options: {
                        scales: {
                            y: {
                                grid: {
                                    color: '#e7eaf3',
                                    drawBorder: false,
                                    zeroLineColor: '#e7eaf3'
                                },
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 100,
                                    color: '#97a4af',
                                    font: {
                                        size: 12,
                                        family: 'Open Sans, sans-serif'
                                    },
                                    padding: 10,
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#97a4af',
                                    font: {
                                        size: 12,
                                        family: 'Open Sans, sans-serif'
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
                                            // Simply using the value without formatting
                                            label += context.parsed.y;
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
                            mode: 'nearest',
                            intersect: true
                        }
                    }
                });
            }
        });
    </script>
@endpush
