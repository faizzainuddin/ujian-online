<?php
declare(strict_types=1);

$databasePath = realpath(__DIR__ . '/../database/database.sqlite');
if ($databasePath === false) {
    throw new RuntimeException('File database/database.sqlite tidak ditemukan.');
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

function fetchInt(PDO $pdo, string $query, array $params = []): ?int
{
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value === false ? null : (int) $value;
}

function hasColumn(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->query('PRAGMA table_info(' . $pdo->quote($table) . ')');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (strcasecmp($row['name'], $column) === 0) {
            return true;
        }
    }

    return false;
}

$now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
$examStart = '2025-11-26 08:00:00';
$examEnd = '2025-11-26 09:30:00';
$adminUsername = 'codex-demo-admin';
$teacherUsername = 'codex-demo-guru';
$studentUsername = 'codex-demo-siswa';
$questionSetDescription = 'Set soal Matematika demo untuk Semester 2';
$examName = 'Ujian Demo Matematika';
$subject = 'matematika';
$semesterValue = 'Semester 2';
$studentClass = 'XI IPS 1';

try {
    $pdo->beginTransaction();

    $pdo->prepare('DELETE FROM hasil_ujian WHERE siswa_id = (SELECT siswa_id FROM siswa WHERE username = ?)')->execute([$studentUsername]);
    $pdo->prepare('DELETE FROM hasil_ujian WHERE ujian_id IN (SELECT ujian_id FROM ujian WHERE nama_ujian = ?)')->execute([$examName]);
    $pdo->prepare('DELETE FROM ujian WHERE nama_ujian = ? AND guru_id = (SELECT guru_id FROM guru WHERE username = ?)')->execute([$examName, $teacherUsername]);
    $pdo->prepare('DELETE FROM question_sets WHERE description = ?')->execute([$questionSetDescription]);
    $pdo->prepare('DELETE FROM siswa WHERE username = ?')->execute([$studentUsername]);
    $pdo->prepare('DELETE FROM guru WHERE username = ?')->execute([$teacherUsername]);
    $pdo->prepare('DELETE FROM admin WHERE username = ?')->execute([$adminUsername]);

    $stmt = $pdo->prepare('INSERT INTO admin (username,password,nama_admin,created_at,updated_at) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $adminUsername,
        password_hash('admin123', PASSWORD_DEFAULT),
        'Administrator Demo',
        $now,
        $now,
    ]);

    $adminId = fetchInt($pdo, 'SELECT admin_id FROM admin WHERE username = ?', [$adminUsername]);
    if ($adminId === null) {
        throw new RuntimeException('Gagal mengambil admin_id.');
    }

    $stmt = $pdo->prepare('INSERT INTO guru (username,password,nama_guru,matapelajaran,admin_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([
        $teacherUsername,
        password_hash('guru123', PASSWORD_DEFAULT),
        'Guru Matematika Demo',
        $subject,
        $adminId,
        $now,
        $now,
    ]);

    $guruId = fetchInt($pdo, 'SELECT guru_id FROM guru WHERE username = ?', [$teacherUsername]);
    if ($guruId === null) {
        throw new RuntimeException('Gagal mengambil guru_id.');
    }

    $stmt = $pdo->prepare('INSERT INTO siswa (username,password,nama_siswa,kelas,admin_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([
        $studentUsername,
        password_hash('siswa123', PASSWORD_DEFAULT),
        'Siswa Demo',
        $studentClass,
        $adminId,
        $now,
        $now,
    ]);

    $siswaId = fetchInt($pdo, 'SELECT siswa_id FROM siswa WHERE username = ?', [$studentUsername]);
    if ($siswaId === null) {
        throw new RuntimeException('Gagal mengambil siswa_id.');
    }

    $stmt = $pdo->prepare('INSERT INTO question_sets (teacher_id,subject,exam_type,semester,class_level,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $guruId,
        'Matematika',
        'Ulangan Harian',
        $semesterValue,
        $studentClass,
        $questionSetDescription,
        $now,
        $now,
    ]);

    $questionSetId = fetchInt($pdo, 'SELECT id FROM question_sets WHERE description = ?', [$questionSetDescription]);
    if ($questionSetId === null) {
        throw new RuntimeException('Gagal mengambil question_set_id.');
    }

    $hasQuestionSetId = hasColumn($pdo, 'ujian', 'question_set_id');
    $params = [
        $examName,
        $examStart,
        $examEnd,
        90,
        $guruId,
        $adminId,
    ];

    if ($hasQuestionSetId) {
        $params[] = $questionSetId;
    }

    $params[] = $now;
    $params[] = $now;

    $ujianSql = $hasQuestionSetId
        ? 'INSERT INTO ujian (nama_ujian,tanggal_mulai,tanggal_selesai,durasi,guru_id,admin_id,question_set_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)'
        : 'INSERT INTO ujian (nama_ujian,tanggal_mulai,tanggal_selesai,durasi,guru_id,admin_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)';

    $stmt = $pdo->prepare($ujianSql);
    $stmt->execute($params);

    $ujianId = fetchInt($pdo, 'SELECT ujian_id FROM ujian WHERE nama_ujian = ? ORDER BY ujian_id DESC LIMIT 1', [$examName]);
    if ($ujianId === null) {
        throw new RuntimeException('Gagal mengambil ujian_id.');
    }

    $stmt = $pdo->prepare('INSERT INTO hasil_ujian (ujian_id,siswa_id,nilai,status,waktu_mulai,waktu_selesai,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $ujianId,
        $siswaId,
        89.5,
        'Selesai',
        $examStart,
        $examEnd,
        $now,
        $now,
    ]);

    $pdo->commit();

    $resultSelect = <<<SQL
SELECT
    h.hasil_id,
    h.nilai,
    h.status,
    s.nama_siswa,
    s.kelas,
    g.nama_guru,
    g.matapelajaran,
    q.semester
FROM hasil_ujian h
JOIN siswa s ON h.siswa_id = s.siswa_id
LEFT JOIN ujian u ON h.ujian_id = u.ujian_id
LEFT JOIN guru g ON u.guru_id = g.guru_id
LEFT JOIN question_sets q ON u.question_set_id = q.id
WHERE g.matapelajaran = :mata AND s.kelas = :kelas AND q.semester = :semester
ORDER BY h.hasil_id DESC
SQL;

    $stmt = $pdo->prepare($resultSelect);
    $stmt->execute([
        ':mata' => $subject,
        ':kelas' => $studentClass,
        ':semester' => $semesterValue,
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Data dummy selesai dimasukkan.\n";
    if (count($results) === 0) {
        echo "Query hasil guru tidak mengembalikan baris pada filter matapelajaran, kelas, dan semester yang sama.\n";
    } else {
        foreach ($results as $row) {
            $semesterRow = $row['semester'] ?? 'N/A';
            echo sprintf(
                "Hasil #%d: %s (%s) nilai %.2f status %s, semester %s, guru %s (%s)\n",
                $row['hasil_id'],
                $row['nama_siswa'],
                $row['kelas'],
                $row['nilai'],
                $row['status'],
                $semesterRow,
                $row['nama_guru'],
                $row['matapelajaran'],
            );
        }
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Gagal menghasilkan data: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
