<?php
declare(strict_types=1);

$host     = getenv('DB_HOST') ?: 'localhost';
$port     = getenv('DB_PORT') ?: '3306';
$dbname   = getenv('DB_NAME') ?: 'resource_allocation';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Enable SSL for cloud MySQL providers (TiDB Cloud, Aiven, Railway)
if ($host !== 'localhost' && $host !== '127.0.0.1') {
    if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        $pdoOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }
    $caPaths = [
        '/etc/pki/tls/certs/ca-bundle.crt',
        '/etc/ssl/certs/ca-certificates.crt',
        'C:/xampp/apache/bin/curl-ca-bundle.crt',
        'C:/xampp/php/extras/ssl/cacert.pem',
    ];
    foreach ($caPaths as $ca) {
        if (file_exists($ca)) {
            $pdoOptions[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            break;
        }
    }
    if (!isset($pdoOptions[PDO::MYSQL_ATTR_SSL_CA]) && defined('PDO::MYSQL_ATTR_SSL_CA')) {
        $pdoOptions[PDO::MYSQL_ATTR_SSL_CA] = true;
    }
}

try {
    try {
        // First attempt direct connection to the database
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password, $pdoOptions);
    } catch (PDOException $dbErr) {
        // Fallback: connect without dbname and create it (e.g. for initial local XAMPP setup)
        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, $pdoOptions);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbname}`");
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL UNIQUE,
            phone_number VARCHAR(15) NOT NULL DEFAULT '+910000000000',
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('Admin', 'Manager', 'Coordinator') NOT NULL DEFAULT 'Admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS resources (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category ENUM('Human / Staff', 'Room / Facility', 'IT / Cloud Server', 'Equipment / Fleet', 'Service / Catering') NOT NULL,
            capacity DECIMAL(8,2) NOT NULL DEFAULT 8.00,
            active_load DECIMAL(8,2) NOT NULL DEFAULT 0.00,
            status ENUM('available','busy','inactive') NOT NULL DEFAULT 'available',
            skills VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            required_skill VARCHAR(100) NOT NULL,
            priority TINYINT NOT NULL DEFAULT 3,
            estimated_effort DECIMAL(8,2) NOT NULL,
            status ENUM('pending','allocated','completed') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS allocations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_id INT NOT NULL UNIQUE,
            resource_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            score DECIMAL(8,2) NOT NULL,
            status ENUM('active','released') NOT NULL DEFAULT 'active',
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS system_settings (
            setting_key VARCHAR(60) PRIMARY KEY,
            setting_value VARCHAR(255) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // Seed default settings if empty
    $settingsCount = (int)$pdo->query("SELECT COUNT(*) FROM system_settings")->fetchColumn();
    if ($settingsCount === 0) {$pdo->exec("
            INSERT INTO system_settings (setting_key, setting_value) VALUES
            ('org_name', 'RTRAE Campus & Institutional Operations'),
            ('weight_skill', '0.50'),
            ('weight_availability', '0.30'),
            ('weight_fairness', '0.20'),
            ('workday_start', '08:00'),
            ('workday_end', '17:00'),
            ('max_daily_hours', '8.0'),
            ('support_phone', '+91 7048700224'),
            ('support_email', '25amtics@gmail.com')
        ");
    }

    // Seed default admin user if empty
    $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {$demoHash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt =$pdo->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['System Admin', 'admin@rtrae.edu.in', '+919876543210', $demoHash, 'Admin']);
    }

    // Seed comprehensive real-world campus resources (B01-B03, B11-B13, Wings B-I, Teachers, Peons, Vehicles, Food)
    $resCount = (int)$pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    if ($resCount === 0) {$pdo->exec("
            INSERT INTO resources (name, category, capacity, active_load, status, skills) VALUES
            ('Classroom B01 (Ground Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Smartboard,Lecture,Capacity 60'),
            ('Classroom B02 (Ground Floor)', 'Room / Facility', 8.00, 2.00, 'available', 'Projector,Lecture,Capacity 60'),
            ('Classroom B03 (Ground Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Smartboard,Seminar,Capacity 90'),
            ('Classroom B11 (First Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Computer Lab,Practical,Capacity 90'),
            ('Classroom B12 (First Floor)', 'Room / Facility', 8.00, 4.00, 'available', 'Smartboard,Lecture,Capacity 60'),
            ('Classroom B13 (First Floor)', 'Room / Facility', 8.00, 0.00, 'available', 'Projector,Workshop,Capacity 75'),
            ('Wing B Seminar Hall', 'Room / Facility', 8.00, 2.00, 'available', 'Conference,Smartboard,Meeting'),
            ('Wing C Department Lab', 'Room / Facility', 8.00, 0.00, 'available', 'Coding,Practical,High-Spec PCs'),
            ('Wing D Faculty Lounge', 'Room / Facility', 8.00, 1.00, 'available', 'Meeting,Briefing,Discussion'),
            ('Wing E Auditorium', 'Room / Facility', 8.00, 0.00, 'available', 'Auditorium,Events,Capacity 300'),
            ('Wing F Research Center', 'Room / Facility', 8.00, 0.00, 'available', 'AI Training,Research,High Compute'),
            ('Wing G Examination Hall', 'Room / Facility', 8.00, 0.00, 'available', 'Exam Duty,Invigilation,Capacity 200'),
            ('Wing H Multi-Purpose Hall', 'Room / Facility', 8.00, 0.00, 'available', 'Workshop,Exhibition,Gathering'),
            ('Wing I Executive Boardroom', 'Room / Facility', 8.00, 0.00, 'available', 'Meeting,Conference,Presentation'),
            ('Prof. Kartikey Singh', 'Human / Staff', 8.00, 2.00, 'available', 'DBMS,SQL,Backend,Teacher'),
            ('Prof. Adhiraj Jha', 'Human / Staff', 8.00, 3.00, 'available', 'Java,Algorithms,Lab Supervision,Teacher'),
            ('Prof. Nitin Tiwari', 'Human / Staff', 8.00, 1.00, 'available', 'Frontend,UI/UX,Web Tech,Teacher'),
            ('Ramesh Kumar (Senior Peon)', 'Human / Staff', 8.00, 2.00, 'available', 'Peon,Delivery,Document Transport,Errand'),
            ('Suresh Patel (Campus Peon)', 'Human / Staff', 8.00, 0.00, 'available', 'Peon,Exam Support,Bell Duty,Cleaning'),
            ('Campus Transit Van #1', 'Equipment / Fleet', 10.00, 2.00, 'available', 'Vehicle,Transport,Field Trip,Luggage'),
            ('Faculty Utility Car #2', 'Equipment / Fleet', 8.00, 0.00, 'available', 'Vehicle,Official Travel,Pickup'),
            ('Campus Central Canteen Catering', 'Service / Catering', 16.00, 4.00, 'available', 'Food,Catering,Event Snacks,Lunch'),
            ('VIP Guest High-Tea Services', 'Service / Catering', 8.00, 0.00, 'available', 'Food,High-Tea,Conference Catering')
        ");
    }

    $taskCount = (int)$pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
    if ($taskCount === 0) {$pdo->exec("
            INSERT INTO tasks (title, required_skill, priority, estimated_effort) VALUES
            ('Computer Lab Session for 90 Students (Classroom B03)', 'Computer Lab', 5, 2.00),
            ('Database Practical Exam Supervision in B11', 'Practical', 5, 3.00),
            ('Document Delivery from Wing B to Wing I', 'Peon', 3, 1.00),
            ('Campus Van for Industrial Field Visit', 'Vehicle', 4, 4.00),
            ('Catering for Department Annual Meeting in Wing I', 'Food', 4, 2.00),
            ('Morning Lecture in Classroom B01', 'Smartboard', 4, 2.00)
        ");
    }
} catch (PDOException $e) {
    if (getenv('VERCEL') || !empty($_ENV['VERCEL']) || getenv('AWS_LAMBDA_FUNCTION_NAME')) {
        http_response_code(503);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><title>Database Configuration Required</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#0d1117;color:#e6edf3;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;box-sizing:border-box;}.card{background:#161b22;border:1px solid #30363d;border-radius:12px;padding:32px;max-width:600px;width:100%;box-shadow:0 15px 35px rgba(0,0,0,0.4);}h2{color:#f85149;margin-top:0;}code{background:#21262d;padding:3px 8px;border-radius:6px;font-family:monospace;color:#7ee787;}.step{background:#21262d;border-left:4px solid #238636;padding:12px 16px;margin:16px 0;border-radius:0 8px 8px 0;}</style></head><body>';
        echo '<div class="card">';
        echo '<h2>⚡ Vercel Deployment Active</h2>';
        echo '<p>Your RTRAE application is live on Vercel! To connect it to your database, add these <strong>Environment Variables</strong> in your Vercel Project Settings:</p>';
        echo '<div class="step"><code>DB_HOST</code> &bull; <code>DB_PORT</code> &bull; <code>DB_NAME</code> &bull; <code>DB_USER</code> &bull; <code>DB_PASS</code></div>';
        echo '<p style="color:#8b949e;font-size:13px;word-break:break-all;"><strong>Connection notice:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div></body></html>';
        exit;
    }
    http_response_code(500);
    die("Database setup failed: " . htmlspecialchars($e->getMessage()));
}