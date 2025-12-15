<?php
session_start();
header('Content-Type: application/json');

// 1. 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? '';

if (empty($post_id)) {
    echo json_encode(['success' => false, 'message' => '게시물 ID가 누락되었습니다.']);
    exit;
}

$json_path = 'data/bookmarks.json';
$bookmarks = [];
if (file_exists($json_path)) {
    $bookmarks = json_decode(file_get_contents($json_path), true) ?? [];
}

// 2. 현재 북마크 상태 확인 및 토글
$is_bookmarked = false;
$key_to_remove = -1;

foreach ($bookmarks as $key => $bookmark) {
    if ($bookmark['user_id'] === $user_id && $bookmark['post_id'] === $post_id) {
        $is_bookmarked = true;
        $key_to_remove = $key;
        break;
    }
}

if ($is_bookmarked) {
    // 북마크 해제
    array_splice($bookmarks, $key_to_remove, 1);
    $message = '북마크가 해제되었습니다.';
    $action = 'removed';
} else {
    // 북마크 추가
    $new_bookmark = [
        'user_id' => $user_id,
        'post_id' => $post_id,
        'date' => date("Y-m-d H:i")
    ];
    $bookmarks[] = $new_bookmark;
    $message = '북마크에 추가되었습니다.';
    $action = 'added';
}

// 3. JSON 파일 저장
file_put_contents($json_path, json_encode($bookmarks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'message' => $message, 'action' => $action]);

?>