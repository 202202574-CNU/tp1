<?php 
session_start();
include 'includes/header.php'; 

// 1. 게시물 ID 확인
$post_id = $_GET['id'] ?? '';
if (empty($post_id)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='index.php';</script>";
    exit;
}

// 2. 전체 게시물 데이터 불러오기
$posts = json_decode(file_get_contents('data/posts.json') ?? '[]', true) ?? [];
$post = null;
foreach ($posts as $p) {
    if ($p['id'] === $post_id) {
        $post = $p;
        break;
    }
}

// 게시물이 없으면 에러
if (!$post) {
    echo "<script>alert('존재하지 않는 게시물입니다.'); location.href='index.php';</script>";
    exit;
}

// 3. 북마크 데이터 불러오기 및 현재 상태 확인
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

$bookmarks = json_decode(file_get_contents('data/bookmarks.json') ?? '[]', true) ?? [];
$is_bookmarked = false;

if ($is_logged_in) {
    foreach ($bookmarks as $bookmark) {
        if ($bookmark['user_id'] === $user_id && $bookmark['post_id'] === $post_id) {
            $is_bookmarked = true;
            break;
        }
    }
}

// 4. 댓글 데이터 불러오기
$all_comments = json_decode(file_get_contents('data/comments.json') ?? '[]', true) ?? [];
$comments = array_filter($all_comments, function($comment) use ($post_id) {
    return $comment['post_id'] === $post_id;
});

?>

<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/post_detail.css"> 

<div class="container post-detail-container">
    
    <div class="post-image" style="background-image: url('uploads/products/<?php echo $post['image']; ?>');"></div>
    
    <div class="post-header">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <button id="bookmarkToggleBtn" 
                class="bookmark-btn <?php echo $is_bookmarked ? 'active' : ''; ?>" 
                data-post-id="<?php echo $post['id']; ?>"
                <?php echo !$is_logged_in ? 'disabled title="로그인 후 이용 가능합니다."' : ''; ?>
        >
            <i class="fas fa-bookmark"></i> 
            <span id="bookmarkText"><?php echo $is_bookmarked ? '북마크됨' : '북마크 추가'; ?></span>
        </button>
    </div>
    
    <div class="post-meta">
        <span>작성자: <?php echo htmlspecialchars($post['author']); ?></span> |
        <span>등록일: <?php echo $post['date']; ?></span>
    </div>
    
    <p class="post-price"><?php echo number_format($post['price']); ?>원</p>
    
    <div class="post-content">
        <?php echo nl2br(htmlspecialchars($post['desc'])); ?>
    </div>

    <div class="comments-section">
        <h3>댓글 (<?php echo count($comments); ?>)</h3>

        <?php if ($is_logged_in): ?>
        <form action="comment_process.php" method="POST" style="margin-bottom: 30px; padding: 15px; border: 1px solid #eee; border-radius: 8px;">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            <textarea name="content" rows="3" placeholder="댓글을 입력하세요..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: none;"></textarea>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px; float: right;">댓글 등록</button>
            <div style="clear: both;"></div>
        </form>
        <?php else: ?>
            <p style="text-align: center; padding: 15px; background: #f9f9f9; border-radius: 5px; color: #888;">댓글을 작성하려면 <a href="login.php" style="color: #2E7D32; font-weight: bold;">로그인</a>이 필요합니다.</p>
        <?php endif; ?>

        <div class="comment-list">
            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: #aaa;">아직 등록된 댓글이 없습니다.</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <p><?php echo $comment['content']; ?></p>
                        <p class="comment-meta">
                            <strong><?php echo htmlspecialchars($comment['user_id']); ?></strong>
                            <span><?php echo $comment['date']; ?></span>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookmarkBtn = document.getElementById('bookmarkToggleBtn');
        const bookmarkText = document.getElementById('bookmarkText');
        
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function() {
                // 로그인 상태인지 체크 (PHP에서 disabled로 막았지만, JS로도 확인)
                if (!<?php echo json_encode($is_logged_in); ?>) {
                    alert('로그인이 필요합니다.');
                    location.href = 'login.php';
                    return;
                }

                const postId = this.getAttribute('data-post-id');
                
                // 1. AJAX 요청 (비동기 통신)
                fetch('bookmark_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `post_id=${postId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        
                        // 2. UI 업데이트
                        if (data.action === 'added') {
                            bookmarkBtn.classList.add('active');
                            bookmarkText.textContent = '북마크됨';
                        } else {
                            bookmarkBtn.classList.remove('active');
                            bookmarkText.textContent = '북마크 추가';
                        }
                    } else {
                        alert('처리 실패: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('서버 통신 중 오류가 발생했습니다.');
                });
            });
        }
    });
</script>

</body>
</html>