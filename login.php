<?php include 'includes/header.php'; ?>

<div class="container" style="max-width: 400px; margin-top: 50px; text-align: center;">
    <h2 style="margin-bottom: 20px;">로그인</h2>
    
    <form action="login_process.php" method="POST" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 15px;">
            <input type="text" name="user_id" placeholder="아이디" autocomplete="off" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        <div style="margin-bottom: 20px;">
            <input type="password" name="user_pw" placeholder="비밀번호" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">로그인 하기</button>
    </form>
    
    <p style="margin-top: 20px; font-size: 14px; color: #666;">
        (테스트용 계정: <strong>user</strong> / <strong>a1234</strong>)
    </p>
</div>

</body>
</html>