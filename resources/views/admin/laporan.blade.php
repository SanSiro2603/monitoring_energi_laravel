@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📊 Laporan Konsumsi Energi</h4>

    {{-- Filter Utama untuk Tabel - Sekarang dengan dropdown dinamis --}}
    <form method="GET" class="row g-2 mt-3 mb-3" id="filterForm">
        <div class="col-md-3">
            <label for="filterKantor" class="form-label visually-hidden">Kantor</label>
            <select name="kantor" id="filterKantor" class="form-select">
                <option value="">-- Semua Kantor --</option>
                @foreach($uniqueKantor as $k)
                    <option value="{{ $k }}" {{ ($kantor == $k) ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterBulan" class="form-label visually-hidden">Bulan</label>
            <select name="bulan" id="filterBulan" class="form-select">
                <option value="">-- Semua Bulan --</option>
                @foreach($uniqueBulan as $b)
                    <option value="{{ $b }}" {{ ($bulan == $b) ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterTahun" class="form-label visually-hidden">Tahun</label>
            <select name="tahun" id="filterTahun" class="form-select">
                <option value="">-- Semua Tahun --</option>
                @foreach($uniqueTahun as $t)
                    <option value="{{ $t }}" {{ ($tahun == $t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Tampilkan</button>
        </div>
    </form>

    @php
    $userRole = Auth::check() ? Auth::user()->role : 'guest';
    $exportRoute = match ($userRole) {
        'super_user' => route('admin.laporan.export-excel'),
        'divisi_user' => route('divisi.laporan.export-excel'),
        'user_umum' => route('umum.laporan.export-excel'),
        default => '#',
    };
    @endphp

    <div class="mb-3 d-flex gap-2">
        {{-- Tombol Export Excel dengan Preview --}}
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#previewExcelModal">
            🗃️ Export Excel
        </button>

        {{-- Tombol Export PDF (untuk tabel) --}}
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exportPdfModal" data-export-type="table">📄 Export Tabel ke PDF</button>

        {{-- Tombol Export Chart to PDF (Menggunakan Browsershot) --}}
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exportChartPdfModal">📊 Export Chart ke PDF</button>

        <button onclick="downloadChartImage()" class="btn btn-secondary custom-grey-hover">
            🖼 Download Gambar Chart
        </button>
    </div>

    {{-- Div yang akan diekspor ke PDF (hanya tabel) --}}
    <div id="laporanTablePDF" class="mb-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="summaryTable">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Kantor</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>PERTALITE (L)</th>
                        <th>PERTAMAX (L)</th>
                        <th>SOLAR (L)</th>
                        <th>DEXLITE (L)</th>
                        <th>PERTAMINA DEX (L)</th>
                        <th>Listrik (kWh)</th>
                        <th>Daya Listrik (VA)</th>
                        <th>Air (m³)</th>
                        <th>Kertas (rim)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $row->kantor }}</td>
                        <td>{{ $row->bulan }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ $row->pertalite ?? '0' }}</td>
                        <td>{{ $row->pertamax ?? '0' }}</td>
                        <td>{{ $row->solar ?? '0' }}</td>
                        <td>{{ $row->dexlite ?? '0' }}</td>
                        <td>{{ $row->pertamina_dex ?? '0' }}</td>
                        <td>{{ $row->listrik }}</td>
                        <td>{{ $row->daya_listrik ?? '-' }}</td>
                        <td>{{ $row->air }}</td>
                        <td>{{ $row->kertas }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $data->links() }}
            </div>
        </div>
    </div> {{-- End laporanTablePDF --}}

    {{-- Bagian Grafik --}}
    <div class="row mt-4 mb-3">
        <div class="col-md-12">
            <h5>📊 Grafik Konsumsi Energi</h5>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="chartKantor">Pilih Kantor:</label>
                            <select id="chartKantor" class="form-select">
                                <option value="">Semua Kantor</option>
                                {{-- Opsi ini akan diisi oleh JavaScript dari dataAll --}}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="chartTahun">Pilih Tahun:</label>
                            <select id="chartTahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                {{-- Opsi ini akan diisi oleh JavaScript dari dataAll --}}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="chartType">Tipe Grafik:</label>
                            <select id="chartType" class="form-select">
                                <option value="monthly">Per Bulan</option>
                                <option value="yearly">Per Tahun</option>
                                <option value="comparison">Perbandingan Energi</option>
                                <option value="bbm_comparison">Perbandingan Jenis BBM</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="energyType">Jenis Energi:</label>
                            <select id="energyType" class="form-select">
                                <option value="all">Semua</option>
                                <option value="listrik">Listrik</option>
                                <option value="air">Air</option>
                                <option value="bbm">Total BBM</option>
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

    {{-- Summary Cards --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Listrik</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalListrik, 2) }} kWh</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Air</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalAir, 2) }} m³</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total BBM</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalBBM, 2) }} L</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Kertas</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($totalKertas, 2) }} rim</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Menyimpan dataAll sebagai JSON tersembunyi untuk ChartJS --}}
    <div id="energiData" style="display: none;">{!! json_encode($dataAll) !!}</div>

</div>

{{-- Modal Preview Excel --}}
<div class="modal fade" id="previewExcelModal" tabindex="-1" aria-labelledby="previewExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewExcelModalLabel">Preview & Filter Export Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Filter Section --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="excelKantor" class="form-label">Kantor</label>
                        <select class="form-select" id="excelKantor">
                            <option value="">-- Semua Kantor --</option>
                            @foreach($uniqueKantor as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="excelBulan" class="form-label">Bulan</label>
                        <select class="form-select" id="excelBulan">
                            <option value="">-- Semua Bulan --</option>
                            @foreach($uniqueBulan as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="excelTahun" class="form-label">Tahun</label>
                        <select class="form-select" id="excelTahun">
                            <option value="">-- Semua Tahun --</option>
                            @foreach($uniqueTahun as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100" onclick="updateExcelPreview()">
                            <i class="bi bi-funnel"></i> Apply Filter
                        </button>
                    </div>
                </div>

                {{-- Preview Table --}}
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" id="excelPreviewTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>No</th>
                                <th>Kantor</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>PERTALITE</th>
                                <th>PERTAMAX</th>
                                <th>SOLAR</th>
                                <th>DEXLITE</th>
                                <th>PERTAMINA DEX</th>
                                <th>Listrik</th>
                                <th>Daya Listrik</th>
                                <th>Air</th>
                                <th>Kertas</th>
                            </tr>
                        </thead>
                        <tbody id="excelPreviewBody">
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Summary Info --}}
                <div class="mt-3">
                    <div class="alert alert-info">
                        <strong>Total Data: </strong><span id="totalDataCount">0</span> baris
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="bi bi-download"></i> Download Excel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export PDF (untuk Tabel) --}}
<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Filter Export Tabel ke PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportPdfForm" method="GET" action="{{ route(Auth::user()->role === 'super_user' ? 'admin.laporan.export-excel' : (Auth::user()->role === 'divisi_user' ? 'divisi.laporan.export-excel' : 'umum.laporan.export-excel')) }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfKantor" class="form-label">Kantor</label>
                        <select class="form-select" id="pdfKantor" name="kantor">
                            <option value="">-- Pilih Kantor --</option>
                            @foreach($uniqueKantor as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pdfBulan" class="form-label">Bulan</label>
                        <select class="form-select" id="pdfBulan" name="bulan">
                            <option value="">-- Pilih Bulan --</option>
                            @foreach($uniqueBulan as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pdfTahun" class="form-label">Tahun</label>
                        <select class="form-select" id="pdfTahun" name="tahun">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($uniqueTahun as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Export PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Export Chart ke PDF (untuk Browsershot) --}}
<div class="modal fade" id="exportChartPdfModal" tabindex="-1" aria-labelledby="exportChartPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportChartPdfModalLabel">Filter Export Chart ke PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportChartPdfForm" method="GET" action="{{ url('/laporan/export-chart-pdf') }}">
                <div class="modal-body">
                    <p class="alert alert-info">Ekspor ini akan menyertakan chart dengan filter yang dipilih saat ini.</p>
                    <div class="mb-3">
                        <label for="chartPdfKantor" class="form-label">Kantor (dari Chart)</label>
                        <select class="form-select" id="chartPdfKantor" name="kantor">
                            <option value="">-- Semua Kantor --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="chartPdfTahun" class="form-label">Tahun (dari Chart)</label>
                        <select class="form-select" id="chartPdfTahun" name="tahun">
                            <option value="">-- Semua Tahun --</option>
                        </select>
                    </div>
                    <input type="hidden" name="bulan" id="chartPdfBulanHidden">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Export Chart PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

/* Modal preview table styles */
#excelPreviewTable {
    font-size: 0.875rem;
}

#excelPreviewTable th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #212529;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .btn {
        font-size: 0.875rem;
    }
}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartEnergi;
let allData = [];
let filteredExcelData = [];

document.addEventListener('DOMContentLoaded', function() {
    const dataElement = document.getElementById('energiData');
    try {
        allData = JSON.parse(dataElement.textContent);
    } catch (e) {
        console.error('Error parsing data from #energiData:', e);
        allData = [];
    }
    
    populateChartFilters();
    initChart();

    // Event Listener untuk Modal Preview Excel
    const previewExcelModal = document.getElementById('previewExcelModal');
    if (previewExcelModal) {
        previewExcelModal.addEventListener('show.bs.modal', function (event) {
            // Set initial filter values from main filter
            document.getElementById('excelKantor').value = document.getElementById('filterKantor').value;
            document.getElementById('excelBulan').value = document.getElementById('filterBulan').value;
            document.getElementById('excelTahun').value = document.getElementById('filterTahun').value;
            
            // Update preview
            updateExcelPreview();
        });
    }

    // Event Listener untuk Modal Export PDF (Tabel)
    const exportPdfModal = document.getElementById('exportPdfModal');
    if (exportPdfModal) {
        exportPdfModal.addEventListener('show.bs.modal', function (event) {
            document.getElementById('pdfKantor').value = document.getElementById('filterKantor').value;
            document.getElementById('pdfBulan').value = document.getElementById('filterBulan').value;
            document.getElementById('pdfTahun').value = document.getElementById('filterTahun').value;
        });
    }

    // Event Listener untuk Modal Export Chart ke PDF
    const exportChartPdfModal = document.getElementById('exportChartPdfModal');
    if (exportChartPdfModal) {
        exportChartPdfModal.addEventListener('show.bs.modal', function (event) {
            const chartKantorVal = document.getElementById('chartKantor').value;
            const chartTahunVal = document.getElementById('chartTahun').value;
            const filterBulanVal = document.getElementById('filterBulan').value;

            const chartPdfKantorSelect = document.getElementById('chartPdfKantor');
            const chartPdfTahunSelect = document.getElementById('chartPdfTahun');
            const chartPdfBulanHiddenInput = document.getElementById('chartPdfBulanHidden');

            chartPdfKantorSelect.innerHTML = '<option value="">-- Semua Kantor --</option>';
            chartPdfTahunSelect.innerHTML = '<option value="">-- Semua Tahun --</option>';

            const uniqueKantorForChartModal = [...new Set(allData.map(item => item.kantor))].filter(Boolean);
            const uniqueTahunForChartModal = [...new Set(allData.map(item => item.tahun))].filter(Boolean).sort((a, b) => b - a);

            uniqueKantorForChartModal.forEach(kantor => {
                const selectedAttr = (kantor == chartKantorVal) ? 'selected' : '';
                chartPdfKantorSelect.innerHTML += `<option value="${kantor}" ${selectedAttr}>${kantor}</option>`;
            });

            uniqueTahunForChartModal.forEach(tahun => {
                const selectedAttr = (tahun == chartTahunVal) ? 'selected' : '';
                chartPdfTahunSelect.innerHTML += `<option value="${tahun}" ${selectedAttr}>${tahun}</option>`;
            });

            chartPdfBulanHiddenInput.value = filterBulanVal;

            const form = document.getElementById('exportChartPdfForm');
            let actionUrl = "{{ url('/laporan/export-chart-pdf') }}";
            const params = new URLSearchParams();

            if (chartPdfKantorSelect.value) params.append('kantor', chartPdfKantorSelect.value);
            if (chartPdfTahunSelect.value) params.append('tahun', chartPdfTahunSelect.value);
            params.append('bulan', filterBulanVal); 

            if (params.toString()) {
                actionUrl += '?' + params.toString();
            }
            form.action = actionUrl;
        });
    }
});

// Function to update Excel preview
function updateExcelPreview() {
    const kantor = document.getElementById('excelKantor').value;
    const bulan = document.getElementById('excelBulan').value;
    const tahun = document.getElementById('excelTahun').value;
    
    // Filter data
    filteredExcelData = allData.filter(item => {
        let match = true;
        if (kantor && item.kantor !== kantor) match = false;
        if (bulan && item.bulan !== bulan) match = false;
        if (tahun && item.tahun != tahun) match = false;
        return match;
    });
    
    // Update preview table
    const tbody = document.getElementById('excelPreviewBody');
    tbody.innerHTML = '';
    
    if (filteredExcelData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center">Tidak ada data dengan filter yang dipilih</td></tr>';
    } else {
        filteredExcelData.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${row.kantor}</td>
                <td>${row.bulan}</td>
                <td>${row.tahun}</td>
                <td>${row.pertalite || '0'}</td>
                <td>${row.pertamax || '0'}</td>
                <td>${row.solar || '0'}</td>
                <td>${row.dexlite || '0'}</td>
                <td>${row.pertamina_dex || '0'}</td>
                <td>${row.listrik}</td>
                <td>${row.daya_listrik || '-'}</td>
                <td>${row.air}</td>
                <td>${row.kertas}</td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    // Update count
    document.getElementById('totalDataCount').textContent = filteredExcelData.length;
}

// Function to download Excel with selected filters
function downloadExcel() {
    const kantor = document.getElementById('excelKantor').value;
    const bulan = document.getElementById('excelBulan').value;
    const tahun = document.getElementById('excelTahun').value;
    
    // Build URL with parameters
   <?php
$baseRoute = match($userRole) {
    'super_user' => 'admin.laporan.export-excel',
    'divisi_user' => 'divisi.laporan.export-excel', 
    'user_umum' => 'umum.laporan.export-excel',
    default => '',
};
?>

    
    let url = "{{ route($baseRoute) }}";
    const params = new URLSearchParams();
    
    if (kantor) params.append('kantor', kantor);
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Close modal and download
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewExcelModal'));
    modal.hide();
    
    // Download file
    window.location.href = url;
}

function populateChartFilters() {
    const kantorSelect = document.getElementById('chartKantor');
    const tahunSelect = document.getElementById('chartTahun');
    
    kantorSelect.innerHTML = '<option value="">Semua Kantor</option>';
    tahunSelect.innerHTML = '<option value="">Semua Tahun</option>';
    
    if (allData.length === 0) return;
    
    const uniqueKantor = [...new Set(allData.map(item => item.kantor))].filter(Boolean);
    const uniqueTahun = [...new Set(allData.map(item => item.tahun))].filter(Boolean).sort((a, b) => b - a);
    
    uniqueKantor.forEach(kantor => {
        kantorSelect.innerHTML += `<option value="${kantor}">${kantor}</option>`;
    });
    
    uniqueTahun.forEach(tahun => {
        tahunSelect.innerHTML += `<option value="${tahun}">${tahun}</option>`;
    });
}

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
    
    updateChart();
}

function updateChart() {
    const kantor = document.getElementById('chartKantor').value;
    const tahun = document.getElementById('chartTahun').value;
    const chartType = document.getElementById('chartType').value;
    const energyType = document.getElementById('energyType').value;
    
    let filteredData = [...allData];
    
    if (kantor) {
        filteredData = filteredData.filter(item => item.kantor === kantor);
    }
    
    if (tahun) {
        filteredData = filteredData.filter(item => item.tahun == tahun);
    }
    
    const filterBulanUtama = document.getElementById('filterBulan').value;
    if (filterBulanUtama) {
        filteredData = filteredData.filter(item => item.bulan === filterBulanUtama);
    }

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
        case 'bbm_comparison':
            generateBBMComparisonChart(filteredData);
            break;
    }
}

function generateMonthlyChart(data, energyType) {
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const monthlyData = {};
    data.forEach(item => {
        const monthIndex = monthNames.indexOf(item.bulan);
        if (monthIndex === -1) return;
        
        const monthKey = monthIndex.toString().padStart(2, '0') + '-' + item.bulan;
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
        monthlyData[monthKey].bbm += parseFloat(item.pertalite || 0) + parseFloat(item.pertamax || 0) + 
                                    parseFloat(item.solar || 0) + parseFloat(item.dexlite || 0) + 
                                    parseFloat(item.pertamina_dex || 0);
        monthlyData[monthKey].kertas += parseFloat(item.kertas || 0);
    });
    
    const labels = Object.keys(monthlyData).sort().map(key => key.substring(3));
    let datasets = [];
    
    const energyLabels = {
        'listrik': 'Listrik (kWh)',
        'air': 'Air (m³)',
        'bbm': 'Total BBM (L)',
        'kertas': 'Kertas (Rim)'
    };
    const colors = {
        'listrik': '#3498db',
        'air': '#2ecc71',
        'bbm': '#e67e22',
        'kertas': '#9b59b6'
    };

    if (energyType === 'all') {
        for (const type in energyLabels) {
            datasets.push({
                label: energyLabels[type],
                data: labels.map(month => {
                    const foundKey = Object.keys(monthlyData).find(key => key.includes(month));
                    return foundKey ? monthlyData[foundKey][type] : 0;
                }),
                backgroundColor: colors[type],
                borderColor: colors[type],
                borderWidth: 1
            });
        }
    } else {
        datasets.push({
            label: energyLabels[energyType],
            data: labels.map(month => {
                const foundKey = Object.keys(monthlyData).find(key => key.includes(month));
                return foundKey ? monthlyData[foundKey][energyType] : 0;
            }),
            backgroundColor: colors[energyType],
            borderColor: colors[energyType],
            borderWidth: 1
        });
    }
    
    chartEnergi.data.labels = labels;
    chartEnergi.data.datasets = datasets;
    chartEnergi.options.plugins.title.text = `Konsumsi Energi Per Bulan`;
    chartEnergi.options.scales.x.title.text = 'Bulan';
    chartEnergi.options.scales.y.title.text = 'Jumlah Penggunaan';
    chartEnergi.update();
}

function generateYearlyChart(data, energyType) {
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
        yearlyData[year].bbm += parseFloat(item.pertalite || 0) + parseFloat(item.pertamax || 0) + 
                                parseFloat(item.solar || 0) + parseFloat(item.dexlite || 0) + 
                                parseFloat(item.pertamina_dex || 0);
        yearlyData[year].kertas += parseFloat(item.kertas || 0);
    });
    
    const labels = Object.keys(yearlyData).sort();
    let datasets = [];
    
    const energyLabels = {
        'listrik': 'Listrik (kWh)',
        'air': 'Air (m³)',
        'bbm': 'Total BBM (L)',
        'kertas': 'Kertas (Rim)'
    };
    const colors = {
        'listrik': '#3498db',
        'air': '#2ecc71',
        'bbm': '#e67e22',
        'kertas': '#9b59b6'
    };

    if (energyType === 'all') {
        for (const type in energyLabels) {
            datasets.push({
                label: energyLabels[type],
                data: labels.map(year => yearlyData[year][type]),
                backgroundColor: colors[type],
                borderColor: colors[type],
                borderWidth: 1
            });
        }
    } else {
        datasets.push({
            label: energyLabels[energyType],
            data: labels.map(year => yearlyData[year][energyType]),
            backgroundColor: colors[energyType],
            borderColor: colors[energyType],
            borderWidth: 1
        });
    }
    
    chartEnergi.data.labels = labels;
    chartEnergi.data.datasets = datasets;
    chartEnergi.options.plugins.title.text = `Konsumsi Energi Per Tahun`;
    chartEnergi.options.scales.x.title.text = 'Tahun';
    chartEnergi.options.scales.y.title.text = 'Jumlah Penggunaan';
    chartEnergi.update();
}

function generateComparisonChart(data) {
    const totals = {
        listrik: data.reduce((sum, item) => sum + parseFloat(item.listrik || 0), 0),
        air: data.reduce((sum, item) => sum + parseFloat(item.air || 0), 0),
        bbm: data.reduce((sum, item) => sum + parseFloat(item.pertalite || 0) + parseFloat(item.pertamax || 0) + 
                                       parseFloat(item.solar || 0) + parseFloat(item.dexlite || 0) + 
                                       parseFloat(item.pertamina_dex || 0), 0),
        kertas: data.reduce((sum, item) => sum + parseFloat(item.kertas || 0), 0)
    };
    
    chartEnergi.data.labels = ['Listrik (kWh)', 'Air (m³)', 'Total BBM (L)', 'Kertas (Rim)'];
    chartEnergi.data.datasets = [{
        label: 'Total Konsumsi',
        data: [totals.listrik, totals.air, totals.bbm, totals.kertas],
        backgroundColor: ['#3498db', '#2ecc71', '#e67e22', '#9b59b6'],
        borderColor: ['#2980b9', '#27ae60', '#d35400', '#8e44ad'],
        borderWidth: 1
    }];
    
    chartEnergi.options.plugins.title.text = `Perbandingan Total Konsumsi Energi`;
    chartEnergi.options.scales.x.title.text = 'Jenis Energi';
    chartEnergi.options.scales.y.title.text = 'Jumlah Penggunaan';
    chartEnergi.update();
}

function generateBBMComparisonChart(data) {
    const bbmTotals = {
        pertalite: data.reduce((sum, item) => sum + parseFloat(item.pertalite || 0), 0),
        pertamax: data.reduce((sum, item) => sum + parseFloat(item.pertamax || 0), 0),
        solar: data.reduce((sum, item) => sum + parseFloat(item.solar || 0), 0),
        dexlite: data.reduce((sum, item) => sum + parseFloat(item.dexlite || 0), 0),
        pertamina_dex: data.reduce((sum, item) => sum + parseFloat(item.pertamina_dex || 0), 0)
    };
    
    chartEnergi.data.labels = ['PERTALITE', 'PERTAMAX', 'SOLAR', 'DEXLITE', 'PERTAMINA DEX'];
    chartEnergi.data.datasets = [{
        label: 'Konsumsi BBM (Liter)',
        data: [bbmTotals.pertalite, bbmTotals.pertamax, bbmTotals.solar, bbmTotals.dexlite, bbmTotals.pertamina_dex],
        backgroundColor: ['#e74c3c', '#e67e22', '#f39c12', '#f1c40f', '#2ecc71'],
        borderColor: ['#c0392b', '#d35400', '#e67e22', '#f39c12', '#27ae60'],
        borderWidth: 1
    }];
    
    chartEnergi.options.plugins.title.text = `Perbandingan Konsumsi Per Jenis BBM`;
    chartEnergi.options.scales.x.title.text = 'Jenis BBM';
    chartEnergi.options.scales.y.title.text = 'Jumlah (Liter)';
    chartEnergi.update();
}

function resetChart() {
    document.getElementById('chartKantor').value = '';
    document.getElementById('chartTahun').value = '';
    document.getElementById('chartType').value = 'monthly';
    document.getElementById('energyType').value = 'all';
    updateChart();
}

function downloadChartImage() {
    const canvas = document.getElementById('chartEnergi');
    const link = document.createElement('a');
    link.download = 'grafik_konsumsi_energi.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}
</script>

@endpush