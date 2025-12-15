<?php 
// 탭 전환 JS 파일의 경로 문제를 해결하기 위해 PHP 변수를 사용합니다.
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/TP/'; // 예: http://localhost/TP/

session_start();
include 'includes/header.php'; 

// -----------------------------------------------------
// 1. 로그인 확인 및 사용자 정보 설정
// -----------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? $user_id; // 이름이 없으면 ID 사용

// -----------------------------------------------------
// 2. 데이터 로딩 및 준비
// -----------------------------------------------------

// 전체 게시물 로드 (북마크/댓글 상세 정보를 찾기 위함)
$posts = json_decode(file_get_contents('data/posts.json') ?? '[]', true) ?? [];
$post_map = []; // [게시물 ID => 게시물 객체] 형태로 빠르게 찾기 위함
foreach ($posts as $p) {
    $post_map[$p['id']] = $p;
}

// 2-1. 내 북마크 목록 필터링
$all_bookmarks = json_decode(file_get_contents('data/bookmarks.json') ?? '[]', true) ?? [];
$my_bookmarks = array_filter($all_bookmarks, function($bookmark) use ($user_id) {
    return $bookmark['user_id'] === $user_id;
});

// 북마크된 게시물의 상세 정보를 가져와서 병합
$bookmarked_posts = [];
foreach ($my_bookmarks as $bookmark) {
    $post_id = $bookmark['post_id'];
    if (isset($post_map[$post_id])) {
        $bookmarked_posts[] = $post_map[$post_id];
    }
}

// 2-2. 내 댓글 목록 필터링
$all_comments = json_decode(file_get_contents('data/comments.json') ?? '[]', true) ?? [];
$my_comments = array_filter($all_comments, function($comment) use ($user_id) {
    return $comment['user_id'] === $user_id;
});
?>

<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/mypage.css"> 

<div class="container mypage-container">
    <div class="profile-header">
        <p style="font-size: 16px; color:#555;">마이 페이지</p>
        <h2>환영합니다, <?php echo htmlspecialchars($user_name); ?>님</h2>
    </div>

    <div class="tab-nav">
        <button id="tabBookmarks" class="active">북마크 목록 (<?php echo count($bookmarked_posts); ?>)</button>
        <button id="tabComments">작성한 댓글 (<?php echo count($my_comments); ?>)</button>
        <button id="tabCompare">비교하기</button>
    </div>

    <div id="contentBookmarks">
        <?php if (empty($bookmarked_posts)): ?>
            <p style="text-align: center; padding: 30px; color:#888;">아직 북마크한 상품이 없습니다.</p>
        <?php else: ?>
            <?php foreach ($bookmarked_posts as $post): ?>
                <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="post-card-mypage">
                    <div class="post-card-img" style="background-image: url('uploads/products/<?php echo $post['image']; ?>');"></div>
                    <div class="post-card-info">
                        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                        <p><?php echo number_format($post['price']); ?>원</p>
                        <p style="font-size:12px; color:#aaa; margin-top:5px;">등록일: <?php echo $post['date']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="contentComments">
        <?php if (empty($my_comments)): ?>
            <p style="text-align: center; padding: 30px; color:#888;">아직 작성한 댓글이 없습니다.</p>
        <?php else: ?>
            <?php foreach ($my_comments as $comment): ?>
                <div class="comment-list-item">
                    <p><strong>내용:</strong> <?php echo htmlspecialchars($comment['content']); ?></p>
                    <p class="comment-date">작성일: <?php echo $comment['date']; ?></p>
                    <a href="post_detail.php?id=<?php echo $comment['post_id']; ?>" class="post-link">
                        [게시물 보기: <?php echo htmlspecialchars($post_map[$comment['post_id']]['title'] ?? '삭제된 게시물'); ?>]
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div id="contentCompare">
        <h3>비교할 상품 선택</h3>
        <p class="selection-message" style="margin-bottom: 20px;">비교할 상품을 2개 이상 선택해주세요.</p>
        <button id="startCompareBtn" class="btn btn-secondary" disabled>선택된 상품 비교하기 (0개)</button>

        <div class="compare-selection-list" style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
            <?php if (empty($bookmarked_posts)): ?>
                <p style="text-align: center; padding: 30px; color:#888;">북마크한 상품이 없습니다. 비교할 수 없습니다.</p>
            <?php else: ?>
                <ul id="compareListItems">
                <?php foreach ($bookmarked_posts as $post): ?>
                    <?php 
                        // JavaScript에서 사용하기 위해 게시물 정보를 data 속성에 저장
                        // mypage.php: line 113
                        $truncated_desc = mb_substr(strip_tags($post['desc']), 0, 50, 'UTF-8') . '...';
                    ?>
                    <li 
                        data-post-id="<?php echo $post['id']; ?>"
                        data-post-title="<?php echo htmlspecialchars($post['title']); ?>"
                        data-post-price="<?php echo htmlspecialchars($post['price']); ?>"
                        data-post-desc="<?php echo htmlspecialchars($truncated_desc); ?>"
                    >
                        <input type="checkbox" class="compare-checkbox" data-post-id="<?php echo $post['id']; ?>">
                        <a href="post_detail.php?id=<?php echo $post['id']; ?>" target="_blank" style="display: flex; text-decoration: none; color: inherit; flex-grow: 1;">
                            <div class="post-card-img" style="background-image: url('uploads/products/<?php echo $post['image']; ?>');"></div>
                            <div>
                                <h4 style="margin: 0; font-size: 16px;"><?php echo htmlspecialchars($post['title']); ?></h4>
                                <p style="margin: 0; font-size: 14px; color: #2E7D32; font-weight: bold;"><?php echo number_format($post['price']); ?>원</p>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <div id="compareResultArea" style="margin-top: 40px; display: none;">
        </div>
    </div>
    
</div>

<script src="<?php echo $base_url; ?>js/mypage.js"></script>

</body>
</html>