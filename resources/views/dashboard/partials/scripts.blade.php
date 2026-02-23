<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Set Chart.js global font family
    Chart.defaults.font.family = "'Figtree', sans-serif";

    // Define global variables so we can destroy/re-render charts dynamically
    let chartAssessmentsInstance = null;
    let chartKategoriInstance = null;
    let yearDropdownReady = false;

    // Helper to get dynamic colors based on dark mode class
    const getChartColors = () => {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            text: isDark ? '#9ca3af' : '#6b7280',
            textBold: isDark ? '#d4d1db8a' : '#374151',
            grid: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)',
            angleLines: isDark ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.08)'
        };
    };

    const getRadarOptions = () => {
        const colors = getChartColors();
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 10,
                        font: {
                            size: 11
                        },
                        color: colors.text
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        font: {
                            size: 9
                        },
                        backdropColor: 'transparent',
                        color: colors.text
                    },
                    pointLabels: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: colors.textBold
                    },
                    grid: {
                        color: colors.grid
                    },
                    angleLines: {
                        color: colors.angleLines
                    }
                }
            }
        };
    };

    // Cache loaded chart data so we don't have to refetch when toggling themes
    let globalAssessmentsData = null;
    let globalKategoriData = {}; // keyed by yearId
    let currentKategoriYearId = '';

    document.addEventListener('DOMContentLoaded', function() {
        loadCharts();
        initKategoriChart();
    });

    // Listen for Dark Mode toggles globally
    window.addEventListener('theme-changed', () => {
        // Re-render chart assessing mode
        if (globalAssessmentsData) {
            renderAssessmentsChart();
        }
        if (globalKategoriData[currentKategoriYearId]) {
            const payload = globalKategoriData[currentKategoriYearId];
            renderKategoriChart(payload, currentKategoriYearId);
        }
    });

    // ── Assessments & Findings chart ──────────────────────────────────
    async function loadCharts() {
        try {
            const res = await fetch("{{ route('dashboard.data') }}");
            globalAssessmentsData = await res.json();
            renderAssessmentsChart();
        } catch (err) {
            console.error('Chart load error:', err);
        }
    }

    function renderAssessmentsChart() {
        if (chartAssessmentsInstance) chartAssessmentsInstance.destroy();

        const data = globalAssessmentsData;
        chartAssessmentsInstance = new Chart(document.getElementById('chartAssessments'), {
            type: 'radar',
            data: {
                labels: data.labels,
                datasets: [{
                        label: 'Submitted',
                        data: data.assessments,
                        backgroundColor: 'rgba(99,102,241,0.15)',
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                    },
                    {
                        label: 'Temuan',
                        data: data.findings,
                        backgroundColor: 'rgba(239,68,68,0.1)',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                    }
                ]
            },
            options: getRadarOptions()
        });
    }

    // ── Kategori PTK chart ────────────────────────────────────────────
    async function initKategoriChart(yearId = '') {
        currentKategoriYearId = yearId;
        try {
            if (!globalKategoriData[yearId]) {
                const url = "{{ route('dashboard.kategori') }}" + (yearId ? '?year_id=' + yearId : '');
                const res = await fetch(url);
                globalKategoriData[yearId] = await res.json();
            }

            // Populate year dropdown once
            const payload = globalKategoriData[yearId];
            const select = document.getElementById('yearFilter');
            if (!yearDropdownReady && payload.years?.length) {
                payload.years.forEach(y => {
                    const opt = document.createElement('option');
                    opt.value = y.id;
                    opt.textContent = y.year;
                    select.appendChild(opt);
                });

                select.value = yearId;
                select.addEventListener('change', function() {
                    initKategoriChart(this.value);
                });
                yearDropdownReady = true;
            } else {
                select.value = yearId;
            }

            renderKategoriChart(payload, yearId);
        } catch (err) {
            console.error('Kategori chart error:', err);
        }
    }

    function renderKategoriChart(payload, yearId) {
        const {
            labels,
            datasets,
            mode
        } = payload;
        const select = document.getElementById('yearFilter');
        const total = datasets.reduce((s, ds) => s + ds.data.reduce((a, b) => a + b, 0), 0);

        // Subtitle
        const subtitle = document.getElementById('chartKategoriSubtitle');
        if (subtitle) {
            subtitle.textContent = mode === 'single' ?
                'Tahun ' + (select.options[select.selectedIndex]?.text || '') :
                'Semua tahun';
        }

        const emptyEl = document.getElementById('chartKategoriEmpty');
        const canvas = document.getElementById('chartKategoriPtk');
        emptyEl.classList.toggle('hidden', total > 0);
        canvas.classList.toggle('hidden', total === 0);

        if (chartKategoriInstance) chartKategoriInstance.destroy();

        datasets.forEach(ds => {
            ds.fill = true;
            ds.pointHoverRadius = 6;
        });

        const colors = getChartColors();
        const specificOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.r}`
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        font: {
                            size: 9
                        },
                        backdropColor: 'transparent',
                        color: colors.text
                    },
                    pointLabels: {
                        font: {
                            size: 10,
                            weight: mode === 'single' ? '600' : '400'
                        },
                        color: mode === 'single' ? ['#16a34a', '#ca8a04', '#ea580c', '#dc2626', '#2563eb'] : colors.textBold
                    },
                    grid: {
                        color: colors.grid
                    },
                    angleLines: {
                        color: colors.angleLines
                    }
                }
            }
        };

        chartKategoriInstance = new Chart(canvas, {
            type: 'radar',
            data: {
                labels,
                datasets
            },
            options: specificOptions
        });
    }
</script>