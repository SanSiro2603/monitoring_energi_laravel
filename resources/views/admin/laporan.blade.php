@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📊 Laporan Konsumsi Energi</h4>

    <!-- Filter -->
    <form method="GET" class="row g-2 mt-3 mb-3" id="filterForm">
        <div class="col-md-3">
            <input type="text" name="kantor" class="form-control" placeholder="Kantor" value="{{ $kantor }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="bulan" class="form-control" placeholder="Bulan" value="{{ $bulan }}">
        </div>
        <div class="col-md-2">
            <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="{{ $tahun }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Tampilkan</button>
        </div>
    </form>

    <!-- Tombol Export -->
    <div class="mb-3 d-flex gap-2">
        <a href="{{ url('/export-energi') }}" class="btn btn-success">🗃️Export Excel</a>
        <a href="{{ url('/laporan/admin/export-pdf') }}" class="btn btn-danger">📄 Export ke PDF</a>
        <button onclick="downloadChartImage()" class="btn btn-outline-secondary">🖼 Download Gambar Chart</button>
    </div>

    <div id="laporanPDF">
        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="summaryTable">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Kantor</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Listrik</th>
                        <th>Air</th>
                        <th>BBM</th>
                        <th>Kertas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->kantor }}</td>
                        <td>{{ $row->bulan }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ $row->listrik }}</td>
                        <td>{{ $row->air }}</td>
                        <td>{{ $row->bbm }}</td>
                        <td>{{ $row->kertas }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
    {{ $data->links() }}
</div>

        </div>

        <!-- Kontrol Grafik -->
        <div class="row mt-4 mb-3">
            <div class="col-md-12">
                <h5>📊 Grafik Konsumsi Energi</h5>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Pilih Kantor:</label>
                                <select id="chartKantor" class="form-select">
                                    <option value="">Semua Kantor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Pilih Tahun:</label>
                                <select id="chartTahun" class="form-select">
                                    <option value="">Pilih Tahun</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Tipe Grafik:</label>
                                <select id="chartType" class="form-select">
                                    <option value="monthly">Per Bulan</option>
                                    <option value="yearly">Per Tahun</option>
                                    <option value="comparison">Perbandingan Energi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Jenis Energi:</label>
                                <select id="energyType" class="form-select">
                                    <option value="all">Semua</option>
                                    <option value="listrik">Listrik</option>
                                    <option value="air">Air</option>
                                    <option value="bbm">BBM</option>
                                    <option value="kertas">Kertas</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button onclick="updateChart()" class="btn btn-primary">Update Grafik</button>
                                <button onclick="resetChart()" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 400px; position: relative;">
                            <canvas id="chartEnergi"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden div untuk menyimpan data -->
<div id="energiData" style="display: none;">{!! json_encode($dataAll) !!}</div>


@endsection

@push('scripts')
<!-- SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartEnergi;
let allData = [];

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    // Ambil data dari hidden div
    const dataElement = document.getElementById('energiData');
    try {
        allData = JSON.parse(dataElement.textContent);
    } catch (e) {
        console.error('Error parsing data:', e);
        allData = [];
    }
    
    populateFilters();
    initChart();
});

// Populate dropdown filters
function populateFilters() {
    const kantorSelect = document.getElementById('chartKantor');
    const tahunSelect = document.getElementById('chartTahun');
    
    // Clear existing options
    kantorSelect.innerHTML = '<option value="">Semua Kantor</option>';
    tahunSelect.innerHTML = '<option value="">Pilih Tahun</option>';
    
    if (allData.length === 0) return;
    
    // Get unique values
    const uniqueKantor = [...new Set(allData.map(item => item.kantor))];
    const uniqueTahun = [...new Set(allData.map(item => item.tahun))].sort((a, b) => b - a);
    
    // Populate kantor
    uniqueKantor.forEach(kantor => {
        kantorSelect.innerHTML += `<option value="${kantor}">${kantor}</option>`;
    });
    
    // Populate tahun
    uniqueTahun.forEach(tahun => {
        tahunSelect.innerHTML += `<option value="${tahun}">${tahun}</option>`;
    });
}

// Initialize chart
function initChart() {
    const ctx = document.getElementById('chartEnergi').getContext('2d');
    
    chartEnergi = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Konsumsi Energi',
                    font: { size: 18 }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Penggunaan'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Periode'
                    }
                }
            }
        }
    });
    
    // Load initial data
    updateChart();
}

// Update chart based on filters
function updateChart() {
    const kantor = document.getElementById('chartKantor').value;
    const tahun = document.getElementById('chartTahun').value;
    const chartType = document.getElementById('chartType').value;
    const energyType = document.getElementById('energyType').value;
    
    let filteredData = [...allData];
    
    // Filter by kantor
    if (kantor) {
        filteredData = filteredData.filter(item => item.kantor === kantor);
    }
    
    // Filter by tahun
    if (tahun) {
        filteredData = filteredData.filter(item => item.tahun == tahun);
    }
    
    // Generate chart based on type
    switch (chartType) {
        case 'monthly':
            generateMonthlyChart(filteredData, energyType);
            break;
        case 'yearly':
            generateYearlyChart(filteredData, energyType);
            break;
        case 'comparison':
            generateComparisonChart(filteredData);
            break;
    }
}

// Generate monthly chart
function generateMonthlyChart(data, energyType) {
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Group by month
    const monthlyData = {};
    data.forEach(item => {
        const monthKey = item.bulan;
        if (!monthlyData[monthKey]) {
            monthlyData[monthKey] = {
                listrik: 0,
                air: 0,
                bbm: 0,
                kertas: 0
            };
        }
        monthlyData[monthKey].listrik += parseFloat(item.listrik || 0);
        monthlyData[monthKey].air += parseFloat(item.air || 0);
        monthlyData[monthKey].bbm += parseFloat(item.bbm || 0);
        monthlyData[monthKey].kertas += parseFloat(item.kertas || 0);
    });
    
    const labels = Object.keys(monthlyData).sort();
    let datasets = [];
    
    if (energyType === 'all') {
        datasets = [
            {
                label: 'Listrik (kWh)',
                data: labels.map(month => monthlyData[month].listrik),
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            },
            {
                label: 'Air (m³)',
                data: labels.map(month => monthlyData[month].air),
                backgroundColor: '#2ecc71',
                borderColor: '#27ae60',
                borderWidth: 1
            },
            {
                label: 'BBM (L)',
                data: labels.map(month => monthlyData[month].bbm),
                backgroundColor: '#e67e22',
                borderColor: '#d35400',
                borderWidth: 1
            },
            {
                label: 'Kertas (Rim)',
                data: labels.map(month => monthlyData[month].kertas),
                backgroundColor: '#9b59b6',
                borderColor: '#8e44ad',
                borderWidth: 1
            }
        ];
    } else {
        const energyLabels = {
            'listrik': 'Listrik (kWh)',
            'air': 'Air (m³)',
            'bbm': 'BBM (L)',
            'kertas': 'Kertas (Rim)'
        };
        const colors = {
            'listrik': '#3498db',
            'air': '#2ecc71',
            'bbm': '#e67e22',
            'kertas': '#9b59b6'
        };
        
        datasets = [{
            label: energyLabels[energyType],
            data: labels.map(month => monthlyData[month][energyType]),
            backgroundColor: colors[energyType],
            borderColor: colors[energyType],
            borderWidth: 1
        }];
    }
    
    chartEnergi.data.labels = labels;
    chartEnergi.data.datasets = datasets;
    chartEnergi.options.plugins.title.text = `Konsumsi Energi Per Bulan`;
    chartEnergi.update();
}

// Generate yearly chart
function generateYearlyChart(data, energyType) {
    // Group by year
    const yearlyData = {};
    data.forEach(item => {
        const year = item.tahun;
        if (!yearlyData[year]) {
            yearlyData[year] = {
                listrik: 0,
                air: 0,
                bbm: 0,
                kertas: 0
            };
        }
        yearlyData[year].listrik += parseFloat(item.listrik || 0);
        yearlyData[year].air += parseFloat(item.air || 0);
        yearlyData[year].bbm += parseFloat(item.bbm || 0);
        yearlyData[year].kertas += parseFloat(item.kertas || 0);
    });
    
    const labels = Object.keys(yearlyData).sort();
    let datasets = [];
    
    if (energyType === 'all') {
        datasets = [
            {
                label: 'Listrik (kWh)',
                data: labels.map(year => yearlyData[year].listrik),
                backgroundColor: '#3498db'
            },
            {
                label: 'Air (m³)',
                data: labels.map(year => yearlyData[year].air),
                backgroundColor: '#2ecc71'
            },
            {
                label: 'BBM (L)',
                data: labels.map(year => yearlyData[year].bbm),
                backgroundColor: '#e67e22'
            },
            {
                label: 'Kertas (Rim)',
                data: labels.map(year => yearlyData[year].kertas),
                backgroundColor: '#9b59b6'
            }
        ];
    } else {
        const energyLabels = {
            'listrik': 'Listrik (kWh)',
            'air': 'Air (m³)',
            'bbm': 'BBM (L)',
            'kertas': 'Kertas (Rim)'
        };
        const colors = {
            'listrik': '#3498db',
            'air': '#2ecc71',
            'bbm': '#e67e22',
            'kertas': '#9b59b6'
        };
        
        datasets = [{
            label: energyLabels[energyType],
            data: labels.map(year => yearlyData[year][energyType]),
            backgroundColor: colors[energyType]
        }];
    }
    
    chartEnergi.data.labels = labels;
    chartEnergi.data.datasets = datasets;
    chartEnergi.options.plugins.title.text = `Konsumsi Energi Per Tahun`;
    chartEnergi.update();
}

// Generate comparison chart
function generateComparisonChart(data) {
    const totals = {
        listrik: data.reduce((sum, item) => sum + parseFloat(item.listrik || 0), 0),
        air: data.reduce((sum, item) => sum + parseFloat(item.air || 0), 0),
        bbm: data.reduce((sum, item) => sum + parseFloat(item.bbm || 0), 0),
        kertas: data.reduce((sum, item) => sum + parseFloat(item.kertas || 0), 0)
    };
    
    chartEnergi.data.labels = ['Listrik (kWh)', 'Air (m³)', 'BBM (L)', 'Kertas (Rim)'];
    chartEnergi.data.datasets = [{
        label: 'Total Konsumsi',
        data: [totals.listrik, totals.air, totals.bbm, totals.kertas],
        backgroundColor: ['#3498db', '#2ecc71', '#e67e22', '#9b59b6']
    }];
    chartEnergi.options.plugins.title.text = `Perbandingan Total Konsumsi Energi`;
    chartEnergi.update();
}

// Reset chart
function resetChart() {
    document.getElementById('chartKantor').value = '';
    document.getElementById('chartTahun').value = '';
    document.getElementById('chartType').value = 'monthly';
    document.getElementById('energyType').value = 'all';
    updateChart();
}

// Download functions
function downloadPDF() {
    const element = document.getElementById('laporanPDF');
    const opt = {
        margin: 0.3,
        filename: 'laporan_energi.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().from(element).set(opt).save();
}

function exportTableToExcel(tableID, filename = '') {
    let dataType = 'application/vnd.ms-excel';
    let tableSelect = document.getElementById(tableID);
    let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename + '.xls' : 'data.xls';

    let downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        let blob = new Blob(['\ufeff', tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

function downloadChartImage() {
    const canvas = document.getElementById('chartEnergi');
    const link = document.createElement('a');
    link.download = 'grafik_energi.png';
    link.href = canvas.toDataURL();
    link.click();
}
</script>

@endpush