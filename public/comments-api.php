<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$commentsFile = __DIR__ . '/rick-comments.json';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// GET - Load comments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($commentsFile)) {
        $comments = json_decode(file_get_contents($commentsFile), true);
        echo json_encode(['success' => true, 'comments' => $comments]);
    } else {
        echo json_encode(['success' => true, 'comments' => []]);
    }
    exit;
}

// POST - Save new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name']) || !isset($input['comment'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and comment required']);
        exit;
    }

    // Load existing comments
    $comments = [];
    if (file_exists($commentsFile)) {
        $comments = json_decode(file_get_contents($commentsFile), true);
    }

    // Add new comment
    $comments[] = [
        'name' => htmlspecialchars($input['name']),
        'comment' => htmlspecialchars($input['comment']),
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    // Save to file
    if (file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to save comment']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
