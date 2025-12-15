<?php
session_start();

// 1. 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
    exit;
}

// 2. 입력값 받기
$post_id = $_POST['post_id'] ?? '';
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if (empty($post_id) || empty($content)) {
    echo "<script>alert('게시물 ID 또는 댓글 내용이 누락되었습니다.'); history.back();</script>";
    exit;
}

// 3. JSON 데이터 처리
$json_path = 'data/comments.json';
$comments = [];
if (file_exists($json_path)) {
    $comments = json_decode(file_get_contents($json_path), true) ?? [];
}

// 새 댓글 데이터 생성
$new_comment = [
    'id' => uniqid('cmt_'),
    'post_id' => $post_id,
    'user_id' => $user_id,
    'content' => htmlspecialchars($content), // XSS 방지를 위해 HTML 특수문자 처리
    'date' => date("Y-m-d H:i")
];

// 배열 맨 앞에 추가 (최신 댓글이 위로 오게)
array_unshift($comments, $new_comment);

// JSON 파일 저장
file_put_contents($json_path, json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 4. 상세 페이지로 리다이렉트
echo "<script>
    alert('댓글이 작성되었습니다.');
    location.href = 'post_detail.php?id={$post_id}';
</script>";
?>