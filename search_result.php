<?php 
include 'includes/header.php'; 

// 1. 데이터 불러오기
$json_path = 'data/posts.json';
$posts = [];
if (file_exists($json_path)) {
    $posts = json_decode(file_get_contents($json_path), true);
    if (!$posts) $posts = [];
}

// 2. 입력값 확인 및 텍스트 검색
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
$results = [];
$search_mode = "text"; // 검색 모드를 텍스트로 고정 (AI 로직 제거)

if ($keyword != "") {
    // 키워드가 있으면 제목이나 설명에 포함된 게시물 검색
    foreach ($posts as $post) {
        // stripos: 대소문자 구분 없이 검색
        if (stripos($post['title'], $keyword) !== false || stripos($post['desc'], $keyword) !== false) {
            $results[] = $post;
        }
    }
} else {
    $results = $posts; // 검색어 없으면 전체 게시물 표시
}
?>

<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/search.css">

<div class="search-header">
    <div class="container">
        <h2>🔍 <span class="search-keyword">'<?php echo htmlspecialchars($keyword); ?>'</span> 검색 결과</h2>
        
        <p class="result-msg" style="margin-top:20px;">총 <strong><?php echo count($results); ?></strong>개의 상품을 찾았습니다.</p>
    </div>
</div>

<section class="container">
    <div class="post-grid">
        <?php if (empty($results)): ?>
            <p style="text-align:center; grid-column: 1 / -1; padding: 50px; color:#888;">
                검색 결과가 없습니다.
            </p>
        <?php else: ?>
            <?php foreach ($results as $post): ?>
                <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="post-card">
                    <div class="post-img" style="background-image: url('uploads/products/<?php echo $post['image']; ?>');"></div>
                    <div class="post-info">
                        <p class="post-price"><?php echo number_format($post['price']); ?>원</p>
                        <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

</body>
</html>