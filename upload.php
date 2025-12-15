<?php 
include 'includes/header.php'; 

// 로그인 안 한 사람은 못 들어오게 튕겨내기
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다!'); location.href='login.php';</script>";
    exit;
}
?>

<div class="container" style="max-width: 600px; margin-top: 40px;">
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #2E7D32; padding-bottom: 10px;">상품 등록</h2>
    
    <form action="upload_process.php" method="POST" enctype="multipart/form-data">
        
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">상품 제목</label>
            <input type="text" name="title" autocomplete="off" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">판매 가격 (원)</label>
            <input type="number" name="price"  autocomplete="off" required placeholder="예: 10000" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">상품 사진</label>
            <input type="file" name="product_image" accept="image/*" required style="width:100%; padding:10px; background:white; border:1px solid #ddd;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">상세 설명</label>
            <textarea name="desc" rows="5" autocomplete="off" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; resize:none;"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 16px;">상품 등록 완료</button>
    </form>
</div>

</body>
</html>