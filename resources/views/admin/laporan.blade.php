@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h4>📊 Laporan Konsumsi Energi</h4>

    <!-- Filter -->
    <form method="GET" class="row g-2 mt-3 mb-3">
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
        <a href="{{ url('/laporan/admin/export-excel') }}" class="btn btn-primary">📥 Export ke Excel</a>
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
        </div>

<h5 class="mt-4">📊 Grafik Konsumsi Energi</h5>
<div style="height: 400px">
    <canvas id="chartEnergi"></canvas>
</div>

    </div>
</div>
@endsection

@push('scripts')
<!-- SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart
  const ctx = document.getElementById('chartEnergi').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Air', 'Listrik', 'BBM', 'Kertas'],
      datasets: [{
        label: 'Total Penggunaan',
        data: [<?= $total['air'] ?>, <?= $total['listrik'] ?>, <?= $total['bbm'] ?>, <?= $total['kertas'] ?>],
        backgroundColor: ['#3498db', '#2ecc71', '#e67e22', '#9b59b6']
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });

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
