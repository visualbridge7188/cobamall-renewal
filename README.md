# 코바몰(Cobamall) 고도몰5 스킨 리뉴얼 프로젝트

고도몰5(Godomall 5) 기반 코바몰의 PC 및 모바일 쇼핑몰 스킨 리뉴얼 및 디자인 커스터마이징 저장소입니다.

---

## 📁 디렉터리 구조

```
├── AGENTS.md                  # 고도몰5 스킨 작업 가이드 및 에이전트 지침
├── CONTEXT.md                 # 프로젝트 상세 컨텍스트 및 고도몰5 공식 가이드 규칙
├── korvamall_logo.png         # 로고 이미지
├── skin_backup/               # 원본 스킨 백업 (ZIP 아카이브)
│   ├── designbook_plen_backup.zip
│   ├── designbookM_plen_backup (1).zip
│   └── 고도몰_원본소스.zip
└── 작업장/                    # 실제 작업 대상 스킨 디렉터리
    ├── designbook_plen_backup/    # PC 스킨 소스 (HTML/CSS/JS/IMG)
    ├── designbookM_plen_backup/   # 모바일 스킨 소스 (HTML/CSS/JS/IMG)
    └── 고도몰_원본소스/           # 기본 원본 소스 참조
```

---

## 📌 핵심 작업 규칙 (고도몰5 템플릿 문법 보존)

1. **PC / 모바일 스킨 분리**: PC(`designbook_plen_backup`)와 모바일(`designbookM_plen_backup`)은 별도 스킨으로 관리됩니다.
2. **치환 코드 보존**: `{=...}`, `{...}`, `{ # ... }`, `<!--{ ? ... }-->`, `<!--{ @ ... }-->` 등 고도몰 전용 템플릿 치환식 및 조건/반복문 문법을 절대 훼손하지 않습니다.
3. **주요 커스텀 진입점**:
   - `main/index.html` : 메인 페이지 레이아웃
   - `outline/` : 헤더, 푸터, 레이아웃 프레임
   - `_dbook/` : 디자인북 커스텀 모듈
   - `css/custom.css` : 커스텀 스타일시트

---

## 🤖 ChatGPT / AI 활용 안내

이 저장소를 ChatGPT Web(GitHub 커넥터 또는 레포지토리 연동)에서 읽어 분석할 때:
- **전체 가이드**: `AGENTS.md`, `CONTEXT.md`를 먼저 참조하여 고도몰5 템플릿 규칙 및 작업 가이드를 확인하세요.
- **스킨 소스**: `작업장/designbook_plen_backup`(PC) 및 `작업장/designbookM_plen_backup`(모바일) 내 템플릿 파일을 참조하세요.
