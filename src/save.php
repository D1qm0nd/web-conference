<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

$errors = [];

$full_name = trim($_POST['full_name'] ?? '');

$words = preg_split('/\s+/u', $full_name, -1, PREG_SPLIT_NO_EMPTY);
$nameOk = count($words) === 3;
if ($nameOk) {
    foreach ($words as $w) {
        if (!preg_match('/^[А-ЯЁа-яё-]+$/u', $w)) {
            $nameOk = false;
            break;
        }
    }
}
if (!$nameOk) {
    $errors[] = 'Укажите фамилию, имя и отчество русскими буквами';
}

$phoneRaw = trim($_POST['phone'] ?? '');
$digits = preg_replace('/[^0-9]/', '', $phoneRaw);
if (substr($digits, 0, 1) === '8') {
    $digits = '7' . substr($digits, 1);
}
$phone = '';
if (strlen($digits) === 11 && $digits[0] === '7') {
    $phone = '+' . $digits;
} else {
    $errors[] = 'Телефон: +7 и ровно 11 цифр';
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный e-mail';
}

$section = $_POST['section'] ?? '';
$sectionsMap = [
    'math' => 'Математика',
    'physics' => 'Физика',
    'informatics' => 'Информатика'
];
if (!isset($sectionsMap[$section])) {
    $errors[] = 'Выберите секцию';
}

$birthDate = trim($_POST['birth_date'] ?? '');
if ($birthDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDate)) {
    $errors[] = 'Некорректная дата рождения';
}

$hasReport = ($_POST['has_report'] ?? '0') === '1' ? 1 : 0;
$reportTopic = $hasReport ? trim($_POST['report_topic'] ?? '') : '';
if ($hasReport && mb_strlen($reportTopic) < 3) {
    $errors[] = 'Укажите тему доклада';
}

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'errors' => $errors], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO participants (full_name, phone, email, section, birth_date, has_report, report_topic)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $full_name,
        $phone,
        $email,
        $section,
        $birthDate !== '' ? $birthDate : null,
        $hasReport,
        $reportTopic !== '' ? $reportTopic : null
    ]);

    $id = $pdo->lastInsertId();

    echo json_encode([
        'ok' => true,
        'participant' => [
            'id' => (int)$id,
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email,
            'birth_date' => $birthDate !== '' ? $birthDate : null,
            'section' => $section,
            'section_name' => $sectionsMap[$section],
            'has_report' => (bool)$hasReport,
            'report_topic' => $reportTopic
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'errors' => ['Ошибка сохранения в базу данных']], JSON_UNESCAPED_UNICODE);
}
