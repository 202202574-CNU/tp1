<?php
// 세션 시작 (로그인 상태 유지를 위해 필수)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>중고 마켓</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><i class="fas fa-leaf"></i> EcoMarket</a>
    </div>

    <nav class="nav-menu">
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="upload.php" class="btn btn-primary"><i class="fas fa-camera"></i> 판매하기</a></li>
                <li><a href="mypage.php">마이페이지</a></li>
                <li><a href="logout.php">로그아웃</a></li>
                <li><span>👋 환영합니다, <strong><?php echo $_SESSION['user_id']; ?></strong>님!</span></li>
            <?php else: ?>
                <li><a href="login.php">로그인</a></li>
                <li><a href="register.php">회원가입</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>