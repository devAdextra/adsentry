@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <div class="rounded-circle overflow-hidden">
                        <img src="{{ asset('assets/images/avatars/01.png') }}" width="48" height="48" class="rounded-circle" alt="">
                    </div>
                    <div>
                        <h6 class="mb-0">Welcome to Scoring</h6>
                        <p class="mb-0">Select your options</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card border shadow-none">
            <div class="card-header bg-light">
                <h6 class="mb-0">Vertical</h6>
            </div>
            <div class="card-body">
                <div class="mb-0">
                    <select id="vertical-select" class="form-select form-select-sm select2">
                        <option value="Tutti">Tutti</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border shadow-none">
            <div class="card-header bg-light">
                <h6 class="mb-0">Categoria</h6>
            </div>
            <div class="card-body">
                <div class="mb-0">
                    <select id="categoria-select" class="form-select form-select-sm select2">
                        <option value="Tutti">Tutti</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border shadow-none">
            <div class="card-header bg-light">
                <h6 class="mb-0">Prodotto</h6>
            </div>
            <div class="card-body">
                <div class="mb-0">
                    <select id="prodotto-select" class="form-select form-select-sm select2">
                        <option value="Tutti">Tutti</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border shadow-none">
            <div class="card-header bg-light">
                <h6 class="mb-0">Extra</h6>
            </div>
            <div class="card-body">
                <div class="mb-0">
                    <select id="extra-select" class="form-select form-select-sm select2">
                        <option value="Tutti">Tutti</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Monthly Movements -->
    <div class="col-xxl-6 d-flex flex-column gap-4">
        <div class="card w-100 rounded-4" style="height: 330px;">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="mb-0">Monthly Movements</h6>
                </div>
                <div class="mt-4" id="monthlyChart" style="min-height: 295px;"></div>
            </div>
        </div>
    <!-- Score Distribution -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Score Distribution</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start flex-row">
                    <!-- Grafico -->
                    <div class="position-relative" style="width: 350px;">
                        <div class="piechart-legend text-center">
                            <h2 class="mb-1 total-distribution">0%</h2>
                            <h6 class="mb-0">Total Distribution</h6>
                            <small class="text-muted total-leads">0 / 0 leads</small>
                        </div>
                        <div id="scoreChart"></div>
                    </div>
                    <!-- Etichette Score -->
                    <div id="scoreLabels" class="ms-4" style="min-width: 220px;"></div>
                    <!-- Categoria selezionata -->
                    <div class="selected-score-details ms-4" style="min-width: 200px;">
                        <div>
                            <p class="mb-1 text-muted">Categoria Selezionata</p>
                            <h4 class="mb-2" id="selectedScoreLabel">Score 9</h4>
                            <p class="mb-2">Distribution Details</p>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0" id="selectedScorePercent">35%</h3>
                                <span class="badge bg-success" id="selectedScoreDelta">+12.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Volume Totale -->
    <div class="col-xxl-4 offset-xxl-2 ms-auto">
        <div class="card w-100 rounded-4 ">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="mb-0">Volume Totale</h6>
                </div>
                <div class="bg-grd-purple rounded-3 mt-3 p-3">
                    <div class="text-center">
                        <h2 class="mb-0 text-white" id="totalFilteredUsers" style="font-size: 2.5rem;">0</h2>
                    </div>
                </div>
                <!-- Lista social -->
                <div class="mt-3">
                    <div class="social-leads">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="social-box bg-light-primary text-primary">
                                <img src="{{ asset('assets/images/icons/icon-dem.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">DEM</p>
                                <h4 class="mb-0">24.5K</h4>
                            </div>
                            <div class="text-success d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_up</span> 4.1%
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="social-box bg-light-danger text-danger">
                                <img src="{{ asset('assets/images/icons/icon-youtube.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Youtube</p>
                                <h4 class="mb-0">78.4K</h4>
                            </div>
                            <div class="text-success d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_up</span> 3.2%
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="social-box bg-light-primary text-primary">
                                <img src="{{ asset('assets/images/icons/icon-meta.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Meta</p>
                                <h4 class="mb-0">24.5K</h4>
                            </div>
                            <div class="text-success d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_up</span> 4.1%
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="social-box bg-light-danger text-danger">
                                <img src="{{ asset('assets/images/icons/icon-linkedin.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Linkedin</p>
                                <h4 class="mb-0">78.4K</h4>
                            </div>
                            <div class="text-success d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_up</span> 3.2%
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="social-box bg-light-primary text-primary">
                                <img src="{{ asset('assets/images/icons/icon-tiktok.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Tiktok</p>
                                <h4 class="mb-0">24.5K</h4>
                            </div>
                            <div class="text-success d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_up</span> 4.1%
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="social-box bg-light-danger text-danger">
                                <img src="{{ asset('assets/images/icons/icon-display.svg') }}" alt="dem" width="24" height="24">
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Display</p>
                                <h4 class="mb-0">32.7K</h4>
                            </div>
                            <div class="text-danger d-flex align-items-center">
                                <span class="material-icons-outlined fs-6">trending_down</span> 2.1%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pulsante Download Lead Filtrate -->
<div class="row mt-3">
    <div class="col-xxl-4 offset-xxl-8 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-lg" id="downloadFilteredLeadsBtn" data-bs-toggle="modal" data-bs-target="#downloadLeadsModal">
            <span class="material-icons-outlined align-middle me-2">download</span>
            Scarica Lead Filtrate
        </button>
    </div>
</div>

<!-- Overlay Spinner -->
<div id="global-spinner-overlay" style="display:none;">
    <div class="global-spinner-backdrop"></div>
    <div class="global-spinner-center">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Modal Download Lead Filtrate -->
<div class="modal fade" id="downloadLeadsModal" tabindex="-1" aria-labelledby="downloadLeadsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="downloadLeadsModalLabel">
          <span class="material-icons-outlined align-middle me-2">download</span>
          Scarica Lead Filtrate
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
            <span class="spinner-border text-primary" id="db-list-spinner" style="display:none;" role="status"></span>
        </div>
        <form id="dbSelectionForm">
            <!-- Qui verranno inserite le checkbox dei db -->
        </form>
      </div>
      <div id="downloadLeadsModalAlert" class="alert alert-danger d-none" role="alert"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" id="confirmDownloadLeadsBtn">Conferma Download</button>
      </div>
    </div>
  </div>
</div>

<style>
.position-relative {
    position: relative;
}

.piechart-legend {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
    pointer-events: none;
}

#scoreChart {
    position: relative;
    z-index: 2;
}

.total-distribution {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
    line-height: 1;
}

.total-leads {
    font-size: 0.875rem;
    color: #6c757d;
}

#scoreLabels {
    min-width: 220px;
    margin-top: 30px;
}
#scoreLabels .d-flex {
    align-items: center;
    margin-bottom: 8px;
}
.selected-score-details {
    margin-top: 30px;
}
@media (max-width: 991px) {
    .card-body .d-flex.flex-row {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    #scoreLabels, .selected-score-details {
        margin-left: 0 !important;
        margin-top: 20px !important;
    }
}

#global-spinner-overlay {
    position: fixed;
    z-index: 9999;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: all;
}
.global-spinner-backdrop {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255,255,255,0.7); /* bianco trasparente, regola l'alpha a piacere */
    z-index: 1;
}
.global-spinner-center {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.min.js"></script>
<script>
$(document).ready(function() {
    window.lastDbList = [];
    console.log('✅ JS ESEGUITO');

    // Inizializza il grafico
    initScoreChart();
    
    // Carica i dati iniziali senza filtri
    updateScoreDistribution();

    // Definisci il mapping tra ID dei select e nomi dei campi
    const fieldMap = {
        'vertical-select': 'macro',
        'categoria-select': 'micro',
        'prodotto-select': 'nano',
        'extra-select': 'extra'
    };

    let chart = null;

    // Rimuovi l'evento change precedente e usa select2:select
    $('.select2').on('select2:select', async function(e) {
        try {
            console.log('Select2 selection changed:', e);
            const changedSelectId = $(this).attr('id');
            const selectedValue = $(this).val();
            console.log('Select cambiato:', changedSelectId, 'Nuovo valore:', selectedValue);

            // Raccogli tutti i filtri attivi
            const activeFilters = {};
            Object.entries(fieldMap).forEach(([selectId, field]) => {
                const value = $(`#${selectId}`).val();
                console.log(`Valore corrente di ${selectId}:`, value);
                if (value && value !== 'Tutti') {
                    activeFilters[field] = value.split(' (')[0].trim();
                }
            });

            console.log('Filtri attivi dopo il cambio:', activeFilters);

            // Aggiorna gli altri selettori
            const updatePromises = Object.entries(fieldMap)
                .filter(([selectId]) => selectId !== changedSelectId)
                .map(([selectId]) => updateSelect(selectId, activeFilters));

            await Promise.all(updatePromises);

            // Aggiorna il grafico e il volume totale
            const [chartData] = await Promise.all([
                updateChartData(activeFilters),
                aggiornaVolumeTotale(activeFilters)
            ]);

            if (chartData && chartData.months && chartData.counts) {
                initChart(chartData);
            }

            // Aggiorna anche la distribuzione dei punteggi con i nuovi filtri
            await updateScoreDistribution(activeFilters);

        } catch (error) {
            console.error('Errore durante l\'aggiornamento:', error);
        }
    });

    const updateSelect = async (selectId, filters = {}) => {
        const field = fieldMap[selectId];
        console.log(`Iniziando aggiornamento di ${selectId} con filtri:`, filters);

        try {
            // Aggiungi il parametro column ai filtri invece di metterlo direttamente nell'URL
            const queryParams = {
                ...filters,
                column: field
            };
            const queryString = costruisciQueryString(queryParams);
            const url = `/adsentry/public/filter-options${queryString}`;
            console.log('URL richiesta:', url);

            const response = await fetch(url);
            const responseText = await response.text();
            console.log('Risposta grezza:', responseText);
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                throw new Error('Risposta non JSON: ' + responseText);
            }
            
            console.log(`Risposta per ${selectId}:`, data);

            if (!data.success || !data.results) {
                console.error('Risposta non valida:', data);
                return;
            }

            const select = $(`#${selectId}`);
            const currentValue = select.val()?.split(' (')[0]; // Estrai il valore pulito
            
            // Calcola il totale per l'opzione "Tutti"
            const totalCount = data.results.reduce((sum, item) => sum + parseInt(item.count), 0);
            
            // Salva le opzioni correnti prima di svuotare
            select.empty();
            
            // Aggiungi "Tutti" con il conteggio totale
            select.append(new Option(`Tutti (${totalCount})`, 'Tutti'));
            
            // Aggiungi le altre opzioni con i loro conteggi
            data.results.forEach(item => {
                const optionText = `${item[field]} (${item.count})`;
                const optionValue = item[field];
                select.append(new Option(optionText, optionValue));
            });

            // Mantieni il valore selezionato se ancora disponibile
            if (currentValue) {
                const stillAvailable = data.results.some(item => item[field] === currentValue);
                select.val(stillAvailable ? currentValue : 'Tutti');
            }

            // Aggiorna select2
            select.trigger('change.select2');
            console.log(`Aggiornamento di ${selectId} completato`);

        } catch (error) {
            console.error(`Errore aggiornamento ${selectId}:`, error);
        }
    };

    // Funzione per costruire la query string
    const costruisciQueryString = (filters) => {
        const params = new URLSearchParams();
        // Aggiungi prima il parametro column se presente
        if (filters.column) {
            params.append('column', filters.column);
        }
        // Aggiungi gli altri filtri
        Object.entries(filters).forEach(([key, value]) => {
            if (key !== 'column' && value && value !== 'Tutti') {
                params.append(key, value);
            }
        });
        return params.toString() ? `?${params.toString()}` : '';
    };

    // Inizializza Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Carica i dati iniziali
    (async function() {
        // Carica i dati iniziali per tutti i select
        const initPromises = Object.keys(fieldMap).map(selectId => updateSelect(selectId, {}));
        await Promise.all(initPromises);

        // Inizializza il grafico
        const chartData = await updateChartData({});
        if (chartData) {
            initChart(chartData);
        }
    })();

    // Funzione per ottenere i dati del grafico
    async function updateChartData(filters = {}) {
        try {
            const url = `${window.location.origin}/adsentry/public/movements/monthly-stats?${new URLSearchParams(filters)}`;
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching chart data:', error);
            return null;
        }
    }

    // Inizializzazione del grafico
    function initChart(data) {
        const options = {
            series: [{
                name: "Movimenti",
                data: data.counts
            }],
            chart: {
                foreColor: "#9ba7b2",
                height: 280,
                type: 'bar',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    borderRadiusApplication: 'around',
                    borderRadiusWhenStacked: 'last',
                    columnWidth: '45%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 1,
                colors: ["transparent"]
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    gradientToColors: ['#009efd'],
                    shadeIntensity: 1,
                    type: 'vertical',
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100, 100, 100]
                },
            },
            colors: ["#2af598"],
            grid: {
                show: true,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                strokeDashArray: 4,
            },
            xaxis: {
                categories: data.months,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                theme: "dark",
                y: {
                    formatter: function(val) {
                        if (typeof val !== 'number' || isNaN(val)) return '-';
                        return val + " movimenti"
                    }
                }
            }
        };

        if (chart) {
            chart.updateOptions(options);
        } else {
            chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
            chart.render();
        }

        // Calcola e aggiorna le percentuali
        updatePercentages(data.counts);
    }

    function updatePercentages(counts) {
        // Calcola la variazione percentuale totale
        const firstValue = counts[0];
        const lastValue = counts[counts.length - 1];
        const totalChange = ((lastValue - firstValue) / firstValue) * 100;

        // Calcola la variazione mensile (ultimi due mesi)
        const monthlyChange = ((counts[counts.length - 1] - counts[counts.length - 2]) / counts[counts.length - 2]) * 100;

        // Aggiorna i valori nell'interfaccia


        // Aggiorna il totale degli utenti filtrati
        const totalUsers = counts.reduce((a, b) => a + b, 0);
        document.getElementById('totalFilteredUsers').textContent = totalUsers.toLocaleString();
    }

    // Inizializza il contatore
    let countUpInstance = null;
    
    // Modifica la funzione aggiornaVolumeTotale
    const aggiornaVolumeTotale = async (filters = {}) => {
        try {
            console.log('Filtri attivi per volume totale:', filters);
            const queryString = costruisciQueryString(filters);
            const url = `/adsentry/public/movements/unique-leads${queryString}`;
            console.log('URL richiesta volume totale:', url);

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            console.log('Risposta volume totale:', data);
            
            if (data.success) {
                // Usa CountUp per l'animazione
                const element = document.getElementById('totalFilteredUsers');
                const newValue = data.uniqueLeads;
                
                if (countUpInstance) {
                    countUpInstance.update(newValue);
                } else {
                    countUpInstance = new CountUp('totalFilteredUsers', newValue, {
                        duration: 3, // durata dell'animazione in secondi
                        separator: ',', // separatore per le migliaia
                        decimal: '.', // separatore decimale
                        useGrouping: true, // usa i separatori delle migliaia
                        useEasing: true, // usa l'effetto easing
                        smartEaseAmount: true // regola automaticamente la velocità dell'easing
                    });
                    if (!countUpInstance.error) {
                        countUpInstance.start();
                    } else {
                        console.error('CountUp error:', countUpInstance.error);
                        // Fallback al metodo standard in caso di errore
                        element.textContent = newValue.toLocaleString();
                    }
                }
            } else {
                console.error('Risposta non valida:', data);
            }
        } catch (error) {
            console.error('Errore durante aggiornamento volume totale:', error);
        }
    };

    // Definizione della funzione initScoreChart
    function initScoreChart() {
        const options = {
            series: [], // Serie vuota inizialmente
            chart: {
                type: 'donut',
                height: 380,
                foreColor: "#9ba7b2"
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '85%'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(value) {
                        return value + '%';
                    }
                }
            },
            stroke: {
                width: 0
            },
            colors: [
                "#ff6a00", // Score 9
                "#98ec2d", // Score 8
                "#3494e6", // Score 7
                "#fc185a", // Score 6
                "#495057"  // Altri
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 280
                    }
                }
            }]
        };

        window.scoreChart = new ApexCharts(document.querySelector("#scoreChart"), options);
        return window.scoreChart.render();
    }

    // Definizione della funzione updateScoreDistribution
    async function updateScoreDistribution(filters = {}) {
        // Mostra spinner
        document.getElementById('global-spinner-overlay').style.display = 'flex';
        try {
            console.log('Fetching score distribution with filters:', filters);
            const response = await fetch(`/adsentry/public/score/distribution?${new URLSearchParams(filters)}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            console.log('Score distribution data:', data);
            
            if (data.success && data.scores) {
                // Prepara i dati per il grafico in modo corretto
                const scores = Array.from({length: 9}, (_, i) => {
                    const score = data.scores[i] || { count: 0, percentage: 0 };
                    return {
                        count: parseInt(score.count) || 0,
                        percentage: parseFloat(score.percentage) || 0
                    };
                });

                console.log('Processed scores:', scores);
            console.log('✅ Ricevuto:', data);
            
                // Filtra gli score >= 5% per grafico e etichette
                const filteredScores = scores
                    .map((score, idx) => ({ ...score, idx })) // aggiungi indice per colore/label
                    .filter(score => score.percentage >= 5);

                // Aggiorna il grafico solo con i punteggi >= 5%
                const options = {
                    series: filteredScores.map(score => score.count),
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    labels: filteredScores.map(score => `Score ${score.idx + 1}`),
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '12px',
                                        fontFamily: 'inherit',
                                        offsetY: 20
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '12px',
                                        fontFamily: 'inherit',
                                        formatter: function (val) {
                                            if (typeof val !== 'number' || isNaN(val)) return '-';
                                            return val.toFixed(1) + '%';
                                        }
                                    },
                                    total: {
                                        show: false
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value) {
                                const scoreIndex = this.dataPointIndex;
                                const score = scores[scoreIndex];
                                const scoreObj = scores ? scores[scoreIndex] : undefined;
                                if (!scoreObj) return '-';
                                return `${score.count} lead (${value.toFixed(1)}%)`;
                            }
                        }
                    },
                    colors: ['#495057', '#ff6a00', '#fc185a', '#3494e6', '#98ec2d', 
                             '#ff6a00', '#3494e6', '#98ec2d', '#fc185a']
                };

                // Aggiorna o crea il grafico
                if (window.scoreChart) {
                    console.log('Updating existing chart');
                    window.scoreChart.updateOptions(options);
                } else {
                    console.log('Creating new chart');
                    const chartElement = document.querySelector("#scoreChart");
                    if (chartElement) {
                        window.scoreChart = new ApexCharts(chartElement, options);
                        window.scoreChart.render();
                    } else {
                        console.error('Chart element not found');
                    }
                }

                // Aggiorna i totali
                const totalDistribution = document.querySelector('.total-distribution');
                const totalLeads = document.querySelector('.total-leads');
                if (totalDistribution && totalLeads && data.filtered_leads !== undefined && data.total_leads !== undefined) {
                    const percentage = data.total_leads > 0 
                        ? ((data.filtered_leads / data.total_leads) * 100).toFixed(1) 
                        : 0;
                    totalDistribution.textContent = `${percentage}%`;
                    totalLeads.textContent = `${data.filtered_leads} / ${data.total_leads} leads`;
                }

                const colors = ['#495057', '#ff6a00', '#fc185a', '#3494e6', '#98ec2d', 
                                '#ff6a00', '#3494e6', '#98ec2d', '#fc185a'];

                    const labelsContainer = document.getElementById('scoreLabels');
                if (labelsContainer) {
                    labelsContainer.innerHTML = filteredScores.map((score) => `
                        <div class="d-flex align-items-center mb-2 score-label-item" data-score="${score.idx + 1}">
                            <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:${colors[score.idx]};margin-right:8px;"></span>
                            <span class="fw-bold me-2">Score ${score.idx + 1}:</span>
                            <span class="me-2">${score.count} lead</span>
                            <span class="text-muted">(${score.percentage}%)</span>
                        </div>
                    `).join('');

                    // Handler per selezione score
                    Array.from(labelsContainer.querySelectorAll('.score-label-item')).forEach(el => {
                        el.addEventListener('click', function() {
                            const scoreIdx = parseInt(this.getAttribute('data-score'), 10) - 1;
                            document.getElementById('selectedScoreLabel').textContent = `Score ${scoreIdx + 1}`;
                            document.getElementById('selectedScorePercent').textContent = `${scores[scoreIdx].percentage}%`;
                            // Aggiorna delta se hai il dato, altrimenti nascondi
                            // document.getElementById('selectedScoreDelta').textContent = ...;
                        });
                    });
                }

                window.lastDbList = data.db_list || [];
            }
        } catch (error) {
            console.error('Errore nell\'aggiornamento della distribuzione:', error);
        } finally {
            // Nascondi spinner SEMPRE, anche in caso di errore
            document.getElementById('global-spinner-overlay').style.display = 'none';
        }
    }
        // Quando si apre il modal, carica i db disponibili
        console.log('🤖 Init evento modal:', !!document.getElementById('downloadLeadsModal'));

        const downloadModal = document.getElementById('downloadLeadsModal');
        if (downloadModal) {
            downloadModal.addEventListener('shown.bs.modal', function () {
                document.getElementById('db-list-spinner').style.display = 'none'; // niente spinner, è istantaneo!
                document.getElementById('downloadLeadsModalAlert').classList.add('d-none');
                const dbList = window.lastDbList || [];
                if (dbList.length === 0) {
                    document.getElementById('dbSelectionForm').innerHTML = '<div class="text-muted">Nessun db disponibile per i filtri selezionati.</div>';
                } else {
                    document.getElementById('dbSelectionForm').innerHTML = dbList.map(db => `
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="${db}" id="db_${db}" name="selectedDb">
                            <label class="form-check-label" for="db_${db}">${db}</label>
                        </div>
                    `).join('');
                }
            });
        }

    // 1. Disabilita il bottone se nessun db è selezionato
    $('#dbSelectionForm').on('change', 'input[type=radio]', function() {
        $('#confirmDownloadLeadsBtn').prop('disabled', false);
    });

    // 2. Reset selezione db e bottone ogni volta che si apre il modal
    $('#downloadLeadsModal').on('show.bs.modal', function() {
        $('#dbSelectionForm input[type=radio]').prop('checked', false);
        $('#confirmDownloadLeadsBtn').prop('disabled', true);
        $('#downloadLeadsModalAlert').addClass('d-none').text('');
    });

    // 3. Gestione click su Conferma Download
    $('#confirmDownloadLeadsBtn').on('click', async function() {
        const selectedDb = $('#dbSelectionForm input[type=radio]:checked').val();
        if (!selectedDb) {
            $('#downloadLeadsModalAlert')
                .removeClass('d-none alert-success')
                .addClass('alert-danger')
                .text('Seleziona un database prima di confermare.');
            return;
        }

        // Raccogli i filtri attivi
        const filters = {
            macro: $('#vertical-select').val(),
            micro: $('#categoria-select').val(),
            nano: $('#prodotto-select').val(),
            extra: $('#extra-select').val(),
            score: $('#selectedScoreLabel').text().replace('Score ', '')
        };

        try {
            // Mostra spinner sul bottone
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            // Invia la richiesta AJAX (modifica l'URL se necessario)
            const response = await fetch('/adsentry/public/downloads', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    selectedDb: selectedDb,
                    filters: filters
                })
            });

            const responseText = await response.text();
            console.log('Risposta grezza:', responseText);
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                throw new Error('Risposta non JSON: ' + responseText);
            }

            if (data.success) {
                // Mostra messaggio di conferma
                $('#downloadLeadsModalAlert')
                    .removeClass('d-none alert-danger')
                    .addClass('alert-success')
                    .text('Il file sarà disponibile nella sezione Download.');

                setTimeout(() => {
                    $('#downloadLeadsModal').modal('hide');
                    $('#downloadLeadsModalAlert').addClass('d-none').removeClass('alert-success').text('');
                }, 2000);
            } else {
                throw new Error(data.message || 'Errore durante la richiesta.');
            }
        } catch (err) {
            $('#downloadLeadsModalAlert')
                .removeClass('d-none alert-success')
                .addClass('alert-danger')
                .text('Errore durante la richiesta: ' + err.message);
            $(this).prop('disabled', false).text('Conferma Download');
        } finally {
            $(this).prop('disabled', false).text('Conferma Download');
        }
    });

    console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content'));
});
</script>
@endpush
@endsection