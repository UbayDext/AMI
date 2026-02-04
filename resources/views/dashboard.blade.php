<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Dashboard</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ✅ 2 chart dalam 1 baris (di layar besar) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-4">Assessment Submitted per Tahun Akreditasi</h3>

                {{-- ✅ setting ukuran chart di SINI (tinggi + max lebar) --}}
                <div class="h-80 w-full max-w-xl mx-auto">
                    <canvas id="chartAssessments" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-4">Temuan/Kritik per Tahun Akreditasi</h3>

                {{-- ✅ setting ukuran chart di SINI juga --}}
                <div class="h-80 w-full max-w-xl mx-auto">
                    <canvas id="chartFindings" class="w-full h-full"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartAssessmentsInstance = null;
        let chartFindingsInstance = null;

        async function loadCharts() {
            const res = await fetch("{{ route('dashboard.data') }}");
            const payload = await res.json();

            const labels = payload.labels;
            const assessments = payload.assessments;
            const findings = payload.findings;

            const radarOptions = {
                responsive: true,

                // ✅ setting agar chart ngikutin tinggi wrapper div (h-80)
                maintainAspectRatio: false,

                plugins: {
                    legend: { position: 'top' },
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            };

            // ✅ biar gak dobel saat reload
            if (chartAssessmentsInstance) chartAssessmentsInstance.destroy();
            if (chartFindingsInstance) chartFindingsInstance.destroy();

            chartAssessmentsInstance = new Chart(document.getElementById('chartAssessments'), {
                type: 'radar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Submitted',
                        data: assessments,
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: radarOptions
            });

            chartFindingsInstance = new Chart(document.getElementById('chartFindings'), {
                type: 'radar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Temuan',
                        data: findings,
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: radarOptions
            });
        }

        loadCharts();
    </script>
</x-app-layout>
