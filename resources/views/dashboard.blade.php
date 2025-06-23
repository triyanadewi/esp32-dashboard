<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Sensor Gas Dan Suhu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">

<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">🌡️ Dashboard Pemantauan Gas & Suhu</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-600">Gas (MQ2)</h3>
            <p class="text-3xl font-bold text-red-600 gas-value">{{ $latest->gas ?? 'N/A' }} ppm</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-600">Suhu (°C)</h3>
            <p class="text-3xl font-bold text-blue-600 suhu-value">{{ $latest->temperature ?? 'N/A' }} °C</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-600">Kelembapan (%)</h3>
            <p class="text-3xl font-bold text-green-600 hum-value">{{ $latest->humidity ?? 'N/A' }} %</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-600">Status</h3>
            <span class="font-bold text-lg status-value {{ $latest->gas >= 300 ? 'text-red-600' : 'text-green-600' }}">
                {{ $latest->gas >= 300 ? '🚨 BAHAYA' : '✅ AMAN' }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-center">📈 Grafik Historis Sensor</h2>
        <canvas id="sensorChart" height="100"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('sensorChart').getContext('2d');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($history->pluck('created_at')->map(fn($d) => $d->format('H:i'))) !!},
            datasets: [
                {
                    label: 'Gas (MQ2)',
                    data: {!! json_encode($history->pluck('gas')) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.1)',
                    tension: 0.4
                },
                {
                    label: 'Suhu (°C)',
                    data: {!! json_encode($history->pluck('temperature')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    tension: 0.4
                },
                {
                    label: 'Kelembapan (%)',
                    data: {!! json_encode($history->pluck('humidity')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Waktu'
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    setInterval(async () => {
        try {
            const res = await fetch('/api/latest');
            const json = await res.json();

            const { latest, history } = json;

            // Update nilai kartu
            document.querySelector('.gas-value').textContent = latest.gas + ' ppm';
            document.querySelector('.suhu-value').textContent = latest.temperature + ' °C';
            document.querySelector('.hum-value').textContent = latest.humidity + ' %';

            const statusEl = document.querySelector('.status-value');
            if (latest.gas >= 300) {
                statusEl.textContent = '🚨 BAHAYA';
                statusEl.classList.remove('text-green-600');
                statusEl.classList.add('text-red-600');
            } else {
                statusEl.textContent = '✅ AMAN';
                statusEl.classList.remove('text-red-600');
                statusEl.classList.add('text-green-600');
            }

            // Update chart
            chart.data.labels = history.map(d => new Date(d.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
            chart.data.datasets[0].data = history.map(d => d.gas);
            chart.data.datasets[1].data = history.map(d => d.temperature);
            chart.data.datasets[2].data = history.map(d => d.humidity);
            chart.update();

        } catch (err) {
            console.error('Gagal fetch data:', err);
        }
    }, 10000); // update tiap 10 detik
</script>

</body>
</html>
