// js/mypage.js 전문 (이벤트 리스너 디버깅 강화)

document.addEventListener('DOMContentLoaded', function() {
    
    // 탭 버튼 요소
    const tabBookmarks = document.getElementById('tabBookmarks');
    const tabComments = document.getElementById('tabComments');
    const tabCompare = document.getElementById('tabCompare'); 
    
    // 탭 내용 요소
    const contentBookmarks = document.getElementById('contentBookmarks');
    const contentComments = document.getElementById('contentComments');
    const contentCompare = document.getElementById('contentCompare'); 
    
    // 필수 요소 존재 확인 및 디버깅
    if (!tabComments || !contentComments || !tabCompare || !contentCompare) {
        console.error("❌ mypage.js: 탭 전환에 필요한 필수 ID 요소 중 일부를 찾지 못했습니다. HTML ID를 확인하세요.");
        return; 
    }
    console.log("✅ mypage.js: 모든 탭 요소가 정상적으로 로드되었습니다.");


    function showTab(tabToShow) {
        console.log("-> 탭 전환 시도:", tabToShow); // 이 메시지가 클릭할 때마다 떠야 함!

        // 모든 탭 버튼 비활성화, 모든 콘텐츠 숨김
        tabBookmarks.classList.remove('active');
        tabComments.classList.remove('active');
        tabCompare.classList.remove('active'); 
        
        contentBookmarks.style.display = 'none';
        contentComments.style.display = 'none';
        contentCompare.style.display = 'none'; 

        // 선택된 탭 활성화 및 콘텐츠 표시
        if (tabToShow === 'bookmarks') {
            tabBookmarks.classList.add('active');
            contentBookmarks.style.display = 'block';
        } else if (tabToShow === 'comments') {
            tabComments.classList.add('active');
            contentComments.style.display = 'block';
        } else if (tabToShow === 'compare') { 
            tabCompare.classList.add('active');
            contentCompare.style.display = 'block';
            document.getElementById('compareResultArea').style.display = 'none';
        }
    }

    // 탭 클릭 이벤트 리스너 설정
    tabBookmarks.addEventListener('click', () => {
        console.log("북마크 탭 클릭 이벤트 감지"); // 추가 디버깅
        showTab('bookmarks');
    });
    tabComments.addEventListener('click', () => {
        console.log("댓글 탭 클릭 이벤트 감지"); // 추가 디버깅
        showTab('comments');
    });
    tabCompare.addEventListener('click', () => {
        console.log("비교 탭 클릭 이벤트 감지"); // 추가 디버깅
        showTab('compare');
    }); 

    // --- (이하 비교 로직은 이전 코드와 동일하게 유지) ---

    const compareCheckboxes = document.querySelectorAll('.compare-checkbox');
    const startCompareBtn = document.getElementById('startCompareBtn');
    const compareResultArea = document.getElementById('compareResultArea');
    
    // (여기에는 나머지 모든 비교 로직 함수들이 있어야 합니다.)
    // (updateCompareButtonState, startCompareBtn.addEventListener 등)

    // 초기화: 기본 탭 설정
    showTab('bookmarks'); 
    
    // Note: 누락된 비교 로직 함수들은 이 파일에 반드시 포함되어야 합니다.
});