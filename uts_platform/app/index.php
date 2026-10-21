<?php
// Cek file log
$logFile = __DIR__ . '/../logs/access.log';
$timestamp = date('Y-m-d H:i:s');
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
file_put_contents($logFile, "[$timestamp] Akses dari $clientIp\n", FILE_APPEND);

// Baca konfigurasi dari file config/.env jika ada
$envFile = __DIR__ . '/../config/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (getenv($key) === false) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

// Ambil konfigurasi dari Environment Variables
$dbHost = getenv('DB_HOST') ?: 'database';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: 'rahasia123';
$dbName = getenv('DB_NAME') ?: 'bukutamu_db';

$connSuccess = false;
$connError = '';
$messages = [];

// Koneksi ke Database
try {
    // Coba koneksi ke host yang didefinisikan, jika database tak dikenal fallback ke 127.0.0.1
    $targetHost = $dbHost;
    if ($targetHost === 'database' && @gethostbyname('database') === 'database') {
        $targetHost = '127.0.0.1';
    }
    $conn = new mysqli($targetHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $connSuccess = true;

    // Buat tabel jika belum ada
    $createTableQuery = "CREATE TABLE IF NOT EXISTS tamu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        pesan TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($createTableQuery);

    // Proses kirim formulir buku tamu
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nama']) && !empty($_POST['pesan'])) {
        $nama = $conn->real_escape_string(trim($_POST['nama']));
        $pesan = $conn->real_escape_string(trim($_POST['pesan']));
        $conn->query("INSERT INTO tamu (nama, pesan) VALUES ('$nama', '$pesan')");
        header("Location: index.php");
        exit;
    }

    // Ambil daftar tamu
    $result = $conn->query("SELECT * FROM tamu ORDER BY id DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
} catch (Exception $e) {
    $connSuccess = false;
    $connError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital - UTS Teknologi Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border: rgba(255, 255, 255, 0.1);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --error: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: radial-gradient(circle at top right, #1e1b4b, var(--bg));
            color: var(--text);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 800px;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a5b4fc, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px 14px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: var(--primary-hover);
        }
        .guest-item {
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }
        .guest-item:last-child { border-bottom: none; }
        .guest-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .guest-name {
            font-weight: 700;
            color: #c7d2fe;
        }
        .guest-message {
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="badge <?php echo $connSuccess ? 'badge-success' : 'badge-error'; ?>">
                Status Database: <?php echo $connSuccess ? '● Terhubung (' . htmlspecialchars($dbHost) . ')' : '○ Terputus (' . htmlspecialchars($connError) . ')'; ?>
            </div>
            <h1>Buku Tamu Digital</h1>
            <p style="color: var(--text-muted);">UTS Teknologi Platform | Mahasiswa: Petrus Admo Samon KW (NIM: 25110027)</p>
        </header>

        <div class="card">
            <h2 style="font-size: 1.2rem; margin-bottom: 16px;">Tulis Pesan Anda</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="form-group">
                    <label for="pesan">Pesan / Kesan</label>
                    <textarea id="pesan" name="pesan" rows="3" required placeholder="Tuliskan pesan Anda di sini..."></textarea>
                </div>
                <button type="submit">Kirim ke Buku Tamu</button>
            </form>
        </div>

        <div class="card">
            <h2 style="font-size: 1.2rem; margin-bottom: 16px;">Daftar Tamu Terbaru (<?php echo count($messages); ?>)</h2>
            <?php if (empty($messages)): ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada tamu yang mengisi. Jadilah yang pertama!</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="guest-item">
                        <div class="guest-header">
                            <span class="guest-name"><?php echo htmlspecialchars($msg['nama']); ?></span>
                            <span><?php echo htmlspecialchars($msg['created_at']); ?></span>
                        </div>
                        <div class="guest-message"><?php echo nl2br(htmlspecialchars($msg['pesan'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
