<?php
require_once '../includes/admin_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../libs/TCPDF-main/tcpdf.php';

// Check if we want to generate actual PDF or HTML preview
$generate_pdf = isset($_GET['pdf']) && $_GET['pdf'] === '1';

// Class untuk generate PDF menggunakan TCPDF
class PDFReportGenerator extends TCPDF {
    private $data;

    public function __construct($data) {
        parent::__construct();
        $this->data = $data;
        // Set document information
        $this->SetCreator('Insidentia System');
        $this->SetAuthor('Administrator');
        $this->SetTitle('Laporan Ringkasan Insiden');
        $this->SetSubject('Report');
        $this->SetKeywords('TCPDF, PDF, report, insiden');
    }

    public function Header() {
        // Set margin from top
        $this->SetY(15);
        
        // Set font for title
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(255, 20, 147); // Deep pink color
        
        // Title
        $this->Cell(0, 12, '[ I ] INSIDENTIA', 0, 1, 'C');
        $this->Ln(3);
        
        // Subtitle
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(0, 0, 0); // Black color
        $this->Cell(0, 10, 'Laporan Ringkasan Insiden Berdasarkan Status', 0, 1, 'C');
        $this->Ln(3);
        
        // Date and time
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(100, 100, 100); // Gray color
        $this->Cell(0, 8, 'Tanggal: ' . date('d F Y, H:i') . ' WIB', 0, 1, 'C');
        
        // Add a line separator
        $this->Ln(5);
        $this->SetDrawColor(255, 182, 193); // Light pink
        $this->Line(20, $this->GetY(), $this->getPageWidth()-20, $this->GetY());
        $this->Ln(8);
        
        // Reset text color to black
        $this->SetTextColor(0, 0, 0);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . ' dari ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function generatePDF() {
        $this->AddPage();
        $this->SetAutoPageBreak(TRUE, 25);
        
        // Add spacing after header
        $this->Ln(5);
        
        // Summary information box
        $this->SetFillColor(255, 240, 245); // Light pink background
        $this->SetDrawColor(255, 105, 180); // Pink border
        $this->SetLineWidth(0.5);
        
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(255, 20, 147); // Deep pink
        $this->Cell(0, 8, 'RINGKASAN LAPORAN', 1, 1, 'C', true);
        
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(95, 6, 'Total Insiden: ' . $this->data['total'] . ' insiden', 1, 0, 'L', true);
        $this->Cell(95, 6, 'Jumlah Status: ' . count($this->data['reports']) . ' status', 1, 1, 'L', true);
        $this->Cell(190, 6, 'Tanggal Generate: ' . date('d F Y, H:i:s') . ' WIB', 1, 1, 'L', true);
        
        $this->Ln(10);
        
        // Table header
        $this->SetFont('helvetica', 'B', 10);
        $this->SetFillColor(255, 182, 193); // Light pink
        $this->SetTextColor(255, 255, 255); // White text
        $this->SetDrawColor(128, 128, 128); // Gray border
        
        // Table headers with proper widths
        $this->Cell(60, 10, 'STATUS', 1, 0, 'C', true);
        $this->Cell(30, 10, 'JUMLAH', 1, 0, 'C', true);
        $this->Cell(30, 10, 'PERSENTASE', 1, 0, 'C', true);
        $this->Cell(20, 10, 'RANK', 1, 0, 'C', true);
        $this->Cell(50, 10, 'KETERANGAN', 1, 1, 'C', true);
        
        // Table body
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(0, 0, 0);
        
        $rank = 1;
        foreach($this->data['reports'] as $row) {
            $persentase = $this->data['total'] > 0 ? round(($row['total'] / $this->data['total']) * 100, 2) : 0;
            
            // Determine status color and description
            $statusColor = $this->getStatusColor($row['status']);
            $keterangan = $this->getStatusDescription($row['status']);
            
            // Alternate row colors
            if ($rank % 2 == 0) {
                $this->SetFillColor(248, 248, 248); // Light gray for even rows
            } else {
                $this->SetFillColor(255, 255, 255); // White for odd rows
            }
            
            $this->Cell(60, 8, $row['status'], 1, 0, 'L', true);
            $this->Cell(30, 8, $row['total'], 1, 0, 'C', true);
            $this->Cell(30, 8, $persentase . '%', 1, 0, 'C', true);
            $this->Cell(20, 8, $rank, 1, 0, 'C', true);
            $this->Cell(50, 8, $keterangan, 1, 1, 'L', true);
            
            $rank++;
        }
        
        // Total row
        $this->SetFont('helvetica', 'B', 9);
        $this->SetFillColor(255, 182, 193); // Pink background for total
        $this->SetTextColor(0, 0, 0);
        $this->Cell(60, 8, 'TOTAL KESELURUHAN', 1, 0, 'C', true);
        $this->Cell(30, 8, $this->data['total'], 1, 0, 'C', true);
        $this->Cell(30, 8, '100%', 1, 0, 'C', true);
        $this->Cell(20, 8, '-', 1, 0, 'C', true);
        $this->Cell(50, 8, 'Semua Status', 1, 1, 'C', true);
        
        $this->Ln(10);
        
        // Notes section with better styling
        $this->SetFillColor(255, 250, 250); // Very light pink
        $this->SetDrawColor(255, 182, 193);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(255, 20, 147);
        $this->Cell(0, 8, 'CATATAN PENTING', 1, 1, 'L', true);
        
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(0, 0, 0);
        
        $notes = [
            '1. Laporan ini menampilkan ringkasan insiden berdasarkan status dalam sistem.',
            '2. Data diurutkan berdasarkan jumlah insiden dari tertinggi ke terendah.',
            '3. Persentase dihitung dari total keseluruhan insiden yang terdaftar.',
            '4. Ranking menunjukkan urutan status berdasarkan frekuensi kejadian.',
            '5. Laporan ini bersifat rahasia dan hanya untuk keperluan internal.'
        ];
        
        foreach ($notes as $note) {
            $this->Cell(0, 6, $note, 1, 1, 'L', true);
        }
        
        // Add signature area
        $this->Ln(15);
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(100, 100, 100);
        
        // Two columns for signatures
        $this->Cell(95, 6, 'Dibuat oleh:', 0, 0, 'L');
        $this->Cell(95, 6, 'Disetujui oleh:', 0, 1, 'L');
        $this->Ln(15);
        $this->Cell(95, 6, '_____________________', 0, 0, 'L');
        $this->Cell(95, 6, '_____________________', 0, 1, 'L');
        $this->Cell(95, 6, 'Administrator Sistem', 0, 0, 'L');
        $this->Cell(95, 6, 'Manager IT', 0, 1, 'L');
        
        $this->lastPage();
        
        // Output PDF
        $filename = 'Laporan_Insiden_' . date('Y-m-d_H-i-s') . '.pdf';
        $this->Output($filename, 'I');
    }
    
    private function getStatusColor($status) {
        $colors = [
            'Dilaporkan' => '#ff6b6b',
            'Dalam Peninjauan' => '#4ecdc4',
            'Dalam Proses' => '#45b7d1',
            'Menunggu Informasi' => '#f7ca18',
            'Selesai' => '#2ecc71',
            'Ditolak' => '#e74c3c'
        ];
        
        return $colors[$status] ?? '#95a5a6';
    }
    
    private function getStatusDescription($status) {
        $descriptions = [
            'Dilaporkan' => 'Baru diterima',
            'Dalam Peninjauan' => 'Sedang dianalisis',
            'Dalam Proses' => 'Sedang dikerjakan',
            'Menunggu Informasi' => 'Butuh data tambahan',
            'Selesai' => 'Sudah terselesaikan',
            'Ditolak' => 'Tidak dapat diproses'
        ];
        
        return $descriptions[$status] ?? 'Status lainnya';
    }
}

// Class untuk generate HTML preview
class HTMLReportGenerator {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function generateHTML() {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Insiden - Insidentia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #fff0f5;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #ffb6c1, #ff69b4);
            color: white;
            border-radius: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
            opacity: 0.9;
        }
        
        .info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-left: 4px solid #ff69b4;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        th {
            background: linear-gradient(135deg, #ffb6c1, #ff69b4);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        
        tr:nth-child(even) {
            background-color: #fff5f9;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .total-row {
            background-color: #ffb6c1 !important;
            font-weight: bold;
            color: #333;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            color: #666;
            font-size: 12px;
        }
        
        .summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        
        .summary-item {
            text-align: center;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            border: 2px solid #ff69b4;
            min-width: 120px;
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            color: #ff69b4;
        }
        
        @media print {
            body {
                margin: 0;
                background-color: white;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 INSIDENTIA</h1>
        <h2>Laporan Ringkasan Insiden Berdasarkan Status</h2>
    </div>
    
    <div class="info">
        <strong>📊 Informasi Laporan:</strong><br>
        Tanggal Laporan: ' . date('d F Y, H:i') . ' WIB<br>
        Dibuat oleh: ' . htmlspecialchars($_SESSION['name']) . '<br>
        Total Insiden: ' . $this->data['total'] . ' insiden
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <div class="summary-number">' . $this->data['total'] . '</div>
            <div>Total Insiden</div>
        </div>
        <div class="summary-item">
            <div class="summary-number">' . count($this->data['reports']) . '</div>
            <div>Status Berbeda</div>
        </div>
        <div class="summary-item">
            <div class="summary-number">' . date('Y') . '</div>
            <div>Tahun Laporan</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Status</th>
                <th style="width: 25%;">Jumlah Insiden</th>
                <th style="width: 25%;">Persentase</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach($this->data['reports'] as $row) {
            $persentase = $this->data['total'] > 0 ? round(($row['total'] / $this->data['total']) * 100, 2) : 0;
            $keterangan = $this->getStatusDescription($row['status']);
            
            $html .= '<tr>
                <td><span class="status-badge" style="background-color: #ffb6c1; color: #333;">' . htmlspecialchars($row['status']) . '</span></td>
                <td>' . htmlspecialchars($row['total']) . '</td>
                <td>' . $persentase . '%</td>
                <td>' . $keterangan . '</td>
            </tr>';
        }
        
        $html .= '<tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td><strong>' . $this->data['total'] . '</strong></td>
                <td><strong>100%</strong></td>
                <td><strong>Semua Status</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <strong>🔒 Dokumen ini bersifat rahasia dan hanya untuk penggunaan internal</strong><br>
        Dicetak dari Sistem Insidentia - Sistem Manajemen Insiden Terpadu<br>
        © ' . date('Y') . ' Insidentia. Semua hak dilindungi undang-undang.
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="
            background: linear-gradient(135deg, #ff69b4, #ff1493);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        ">🖨️ Cetak Laporan</button>
        <button onclick="window.close()" style="
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        ">❌ Tutup</button>
    </div>
    
</body>
</html>';
        
        return $html;
    }
    
    private function getStatusDescription($status) {
        $descriptions = [
            'Dilaporkan' => 'Baru diterima',
            'Dalam Peninjauan' => 'Sedang dianalisis',
            'Dalam Proses' => 'Sedang dikerjakan',
            'Menunggu Informasi' => 'Butuh data tambahan',
            'Selesai' => 'Sudah terselesaikan',
            'Ditolak' => 'Tidak dapat diproses'
        ];
        
        return $descriptions[$status] ?? 'Status lainnya';
    }
}

// Ambil data laporan dari database
$query = "SELECT s.name AS status, COUNT(i.id) AS total 
          FROM incident_statuses s 
          LEFT JOIN incidents i ON s.id = i.status_id 
          GROUP BY s.id, s.name 
          ORDER BY total DESC";
$result = $conn->query($query);

$report_data = [];
$total_insiden = 0;

while ($row = $result->fetch_assoc()) {
    $report_data[] = $row;
    $total_insiden += $row['total'];
}

$data = [
    'reports' => $report_data,
    'total' => $total_insiden
];

// Check if user wants PDF or HTML preview
if ($generate_pdf) {
    // Generate PDF
    $pdf = new PDFReportGenerator($data);
    $pdf->generatePDF();
} else {
    // Generate HTML preview  
    $html = new HTMLReportGenerator($data);
    echo $html->generateHTML();
}
?>
