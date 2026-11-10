<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

$sectionsMap = [
    'math' => 'Математика',
    'physics' => 'Физика',
    'informatics' => 'Информатика'
];

try {
    $stmt = $pdo->query('SELECT * FROM participants ORDER BY id DESC');
    $rows = $stmt->fetchAll();

    $participants = [];
    foreach ($rows as $row) {
        $participants[] = [
            'id' => (int)$row['id'],
            'full_name' => $row['full_name'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'birth_date' => $row['birth_date'],
            'section' => $row['section'],
            'section_name' => $sectionsMap[$row['section']] ?? $row['section'],
            'has_report' => (bool)$row['has_report'],
            'report_topic' => $row['report_topic']
        ];
    }

    echo json_encode(['participants' => $participants], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных'], JSON_UNESCAPED_UNICODE);
}
