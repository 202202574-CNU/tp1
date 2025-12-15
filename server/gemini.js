const express = require('express');
const bodyParser = require('body-parser');
const { GoogleGenerativeAI } = require("@google/generative-ai");
const fs = require("fs");
const path = require("path");
require('dotenv').config();

// --- 1. 서버 및 API 설정 ---
const app = express();
const PORT = 3000;
const CORS_URL = 'http://localhost'; // 아파치(PHP) 서버가 실행되는 주소

if (!process.env.API_KEY) {
    console.error("❌ 오류: .env 파일에 API_KEY가 없습니다.");
    process.exit(1);
}

const genAI = new GoogleGenerativeAI(process.env.API_KEY);
// 비교 분석에는 이미지 정보가 필요 없으므로, 더 빠르고 저렴한 모델을 사용할 수도 있습니다.
const model = genAI.getGenerativeModel({ model: "gemini-2.5-flash" }); 

// 📂 [중요] 경로 설정 (이 파일에서는 더 이상 사용되지 않지만 구조 유지를 위해 남겨둠)
const postsDir = String.raw`C:\Apache24\htdocs\TP\uploads\products`;       

// --- 2. 미들웨어 설정 ---
app.use(bodyParser.json());
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', CORS_URL); 
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});

// --- 3. 헬퍼 함수 (이미지 검색 관련 헬퍼 함수는 모두 제거됨) ---
// 이 파일은 이제 텍스트 기반 분석만 수행합니다.

// --- 4. 북마크 상품 비교 분석 API 엔드포인트 ---
// mypage.js의 요청을 처리하는 엔드포인트: /api/compareAnalysis
app.post('/api/compareAnalysis', async (req, res) => {
    try {
        const posts = req.body.posts; // JavaScript에서 전달받은 상품 배열 (title, desc, price 포함)

        if (!posts || posts.length < 2) {
            return res.status(400).json({ error: "비교할 상품이 2개 미만입니다." });
        }

        // 프롬프트 구성
        let promptParts = [];
        
        promptParts.push("역할: 상품 분석 및 비교 전문가.");
        promptParts.push("임무: 사용자에게 제공된 상품 목록을 보고 다음 규칙에 따라 분석하고 JSON 형식으로 응답하세요.");
        
        // 규칙 1: 카테고리 일치 판단
        promptParts.push("규칙 1: 상품들이 동일한 대분류 카테고리(예: '태블릿 PC', '스마트폰', '의류', '가전')에 속하는지 판단하세요. 만약 2개 이상의 상품이 서로 완전히 다른 카테고리(예: '노트북'과 '텀블러')라면, 'is_comparable'을 false로 설정하세요.");
        
        // 규칙 2: 핵심 키워드 추출
        promptParts.push("규칙 2: 'is_comparable'이 true라면, 각 상품의 제목과 설명에서 성능 비교에 필요한 핵심 키워드(예: 모델명, 용량, 제조사, 화면 크기 등) 3개를 추출하여 'keywords' 배열에 담으세요.");
        
        // 규칙 3: 출력 형식
        promptParts.push("규칙 3: 출력은 오직 JSON 객체 형태여야 합니다.");
        
        promptParts.push("\n--- 상품 목록 ---");
        posts.forEach((post, index) => {
            promptParts.push(`\n상품 ${index + 1}:`);
            promptParts.push(`ID: ${post.id}`);
            promptParts.push(`제목: ${post.title}`);
            promptParts.push(`설명: ${post.desc}`);
            promptParts.push(`가격: ${post.price}원`);
        });

        promptParts.push("\n--- 응답 형식 ---");
        // AI가 따를 JSON 스키마를 명시적으로 제공
        promptParts.push(`{
            "is_comparable": true/false,
            "category": "추출된 공통 카테고리 (예: 태블릿 PC)",
            "analysis": [
                {
                    "id": "상품1 ID",
                    "keywords": ["키워드1", "키워드2", "키워드3"]
                },
                // ... 상품 수만큼 반복
            ]
        }`);

        // AI 요청 전송
        const result = await model.generateContent(promptParts);
        const responseText = result.response.text.trim();
        
        // AI가 반환한 JSON 텍스트를 파싱
        try {
            const analysisResult = JSON.parse(responseText);
            res.json({ success: true, data: analysisResult });
        } catch (e) {
            console.error("AI 응답 파싱 실패:", responseText);
            // 파싱 실패 시 원본 응답과 함께 500 에러 반환
            res.status(500).json({ success: false, error: "AI 분석 결과 형식이 올바르지 않습니다.", raw: responseText });
        }

    } catch (error) {
        console.error(`❌ 비교 분석 중 에러 발생:`, error.message);
        res.status(500).json({ error: "AI 서버 내부 오류", details: error.message });
    }
});


// --- 5. 서버 시작 ---
app.listen(PORT, () => {
  console.log(`\n🎉 Node.js AI 서버가 http://localhost:${PORT} 에서 실행 중입니다! (이미지 검색 기능 제거됨)`);
  console.log("웹페이지의 북마크 비교 요청을 기다리는 중...");
});