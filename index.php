<?php 
include 'includes/header.php'; 

// 저장된 게시물 데이터 불러오기
$json_path = 'data/posts.json';
$posts = [];

if (file_exists($json_path)) {
    $json_data = file_get_contents($json_path);
    $posts = json_decode($json_data, true);
    
    if ($posts === null) {
        $posts = []; // JSON 깨짐 방지
    }
}

// 최신순으로 3개만 자르기 (데이터가 있으면)
$recent_posts = array_slice($posts, 0, 3);
?>

<link rel="stylesheet" href="css/main.css">

<section class="hero-section">
    <h1 class="hero-title">어떤 물건을 찾으시나요?</h1>
    
    <form action="search_result.php" method="POST" class="search-box" autocomplete="off">
        <input type="text" name="keyword" placeholder="상품명이나 태그를 입력하세요...">
        
        <button type="submit" class="btn-search">검색</button>
    </form>
    </section>

<section class="container">
    <h2 class="section-title">✨ 최근 올라온 상품</h2>
    
    <div class="post-grid">
        <?php if (empty($recent_posts)): ?>
            <p style="text-align:center; grid-column: 1 / -1; color:#888;">아직 등록된 상품이 없습니다. 첫 판매자가 되어보세요!</p>
        <?php else: ?>
            <?php foreach ($recent_posts as $post): ?>
                <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="post-card">
                    <div class="post-img" style="background-image: url('uploads/products/<?php echo $post['image']; ?>');"></div>
                    <div class="post-info">
                        <p class="post-price"><?php echo number_format($post['price']); ?>원</p>
                        <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p style="color:#888; font-size:12px;"><?php echo $post['date']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

</body>
</html>