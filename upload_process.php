<?php
session_start();

// 1. 데이터 받기
$title = $_POST['title'];
$price = $_POST['price'];
$desc = $_POST['desc'];
$author = $_SESSION['user_id'];
$date = date("Y-m-d H:i"); // 현재 시간

// 2. 사진 파일 처리
// 사진 이름이 겹치지 않게 '현재시간_파일명'으로 변경 (예: 171542_apple.jpg)
$file_name = time() . "_" . $_FILES['product_image']['name'];
$upload_dir = "uploads/products/"; // 저장될 경로
$upload_file = $upload_dir . $file_name;

// 실제 파일을 서버 폴더로 이동
if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_file)) {
    
    // 3. JSON 파일에 정보 저장
    $json_path = 'data/posts.json';
    
    // 기존 데이터 읽기
    $json_data = file_get_contents($json_path);
    $posts = json_decode($json_data, true);
    if (!$posts) $posts = []; // 파일이 비었으면 빈 배열로 시작

    // 새 게시물 정보 만들기
    $new_post = [
        "id" => uniqid(), // 고유 ID 생성
        "title" => $title,
        "price" => $price,
        "desc" => $desc,
        "image" => $file_name, // 파일명만 저장
        "author" => $author,
        "date" => $date
    ];

    // 배열 앞에 추가 (최신글이 위로 오게)
    array_unshift($posts, $new_post);

    // 다시 JSON으로 저장 (JSON_PRETTY_PRINT는 사람이 보기 좋게 줄바꿈 해줌)
    file_put_contents($json_path, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "<script>
        alert('상품이 등록되었습니다!');
        location.href = 'index.php';
    </script>";

} else {
    echo "<script>
        alert('사진 업로드에 실패했습니다. 폴더 권한을 확인해주세요.');
        history.back();
    </script>";
}
?>