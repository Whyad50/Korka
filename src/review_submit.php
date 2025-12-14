<?php
require __DIR__ . '/bootstrap.php';
requireAuth();
$user = currentUser();

$course = trim($_POST['course'] ?? '');
$rating = (int) ($_POST['rating'] ?? 0);
$text = trim($_POST['text'] ?? '');

if ($course === '' || $rating < 1 || $rating > 5 || $text === '') {
    header('Location: /prosmotr_zapis.php');
    exit;
}

try {
    $stmt = db()->prepare('INSERT INTO reviews (user_id, user_login, course, rating, text) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $user['id'],
        $user['login'],
        $course,
        $rating,
        $text,
    ]);
} catch (Throwable $e) {
    error_log('Review save error: ' . $e->getMessage());
}

header('Location: /prosmotr_zapis.php');
exit;
