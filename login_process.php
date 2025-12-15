<?php
session_start(); // 로그인 상태를 기억하기 위해 세션 시작

// 1. 사용자가 입력한 값 받기
$input_id = $_POST['user_id'];
$input_pw = $_POST['user_pw'];

// 2. 저장된 회원 명부(json) 불러오기
$json_data = file_get_contents('data/users.json');
$users = json_decode($json_data, true);

// 3. 아이디/비번 확인
$is_login_success = false;
$user_name = "";

foreach ($users as $user) {
    if ($user['id'] === $input_id && $user['pw'] === $input_pw) {
        $is_login_success = true;
        $user_name = $user['name'];
        break;
    }
}

// 4. 결과 처리
if ($is_login_success) {
    // 로그인 성공! 세션에 기록
    $_SESSION['user_id'] = $input_id;
    $_SESSION['user_name'] = $user_name;
    
    // 메인 페이지로 이동
    echo "<script>
        alert('반갑습니다, {$user_name}님!');
        location.href = 'index.php';
    </script>";
} else {
    // 로그인 실패
    echo "<script>
        alert('아이디 또는 비밀번호가 틀렸습니다.');
        history.back();
    </script>";
}
?>