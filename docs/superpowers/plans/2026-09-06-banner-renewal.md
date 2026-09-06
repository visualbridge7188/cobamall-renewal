# Banner Renewal and Packaging Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 코바몰 고도몰5 PC 및 모바일 스킨의 메인 비주얼 배너(4종)와 중간 와이드 배너(함초/코스메틱)의 텍스트, 이미지, 스타일(곡률 및 반응형)을 교체하고, 외부 의존성 없이 스킨 자체 내부에 에셋을 탑재한 후 원격 업로드용 ZIP 배포 패키지를 생성한다.

**Architecture:** 
고도몰5 템플릿의 기존 치환코드 및 배너 위젯(`includeWidget`, `dataBanner`) 구조를 100% 보존하면서, 스킨 내부 폴더(`_dbook/img/`)에 에셋을 탑재한다. `main/index.html`의 `.slider-nav`를 4슬라이드로 확장하고, `_dbook/css/main.css`에서 파비콘 인라인 정렬, 텍스트 줄바꿈/여백, 배너 곡률(PC 20px / Mobile 12px)을 구현한다. 최종 산출물로 고도몰 디자인 관리자 업로드 규격에 맞춘 PC/모바일 ZIP 패키지를 생성한다.

**Tech Stack:** HTML5, CSS3, Godomall 5 Template Engine, Git, Zip CLI

**Spec:** `docs/superpowers/specs/2026-09-06-banner-renewal-design.md`

## Global Constraints

- 고도몰5 치환문법(`{=...}`, `<!--{ ? ... }-->`, `<!--{ @ ... }-->` 등)을 절대 훼손하지 않는다.
- 기존 위젯의 `bannerCode`(PC `511517418`, `3414171578`, `2570777611` / 모바일 `3606671697`, `4252770060`, `2805788834`)와 상품진열 `sno`는 그대로 보존한다.
- 외부 URL 링크나 외부 CDN을 사용하지 않고, 스킨 내부 상대경로(`../_dbook/img/...`)로 참조한다.
- PC와 모바일은 별도 스킨이므로 한쪽 파일의 변경사항을 다른 쪽에 무단 덮어쓰지 않고 각 환경에 맞게 독립 구현한다.

---

### Task 1: 에셋 복사 및 스킨 디렉터리 구조화

**Files:**
- Create: `작업장/designbook_plen_backup/_dbook/img/Favicon.svg`
- Create: `작업장/designbook_plen_backup/_dbook/img/코바코스메틱 Favicon.webp`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/Artboard 1.webp`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/Artboard 2.png`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/Artboard 3.webp`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/Artboard 4.webp`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/함초배너.png`
- Create: `작업장/designbook_plen_backup/_dbook/img/banner/코스메틱 배너.png`
- Create: `작업장/designbookM_plen_backup/_dbook/img/...` (모바일 대응 동일 복사)

**Interfaces:**
- Consumes: 프로젝트 루트의 신규 에셋 원본 파일
- Produces: PC/모바일 스킨 내 상대경로로 참조 가능한 이미지 자산

- [ ] **Step 1: PC 및 모바일 스킨에 배너 디렉터리 생성**

Run:
```bash
mkdir -p "작업장/designbook_plen_backup/_dbook/img/banner"
mkdir -p "작업장/designbookM_plen_backup/_dbook/img/banner"
```

- [ ] **Step 2: 신규 에셋을 PC 및 모바일 스킨 이미지 폴더로 복사**

Run:
```bash
cp Favicon.svg "작업장/designbook_plen_backup/_dbook/img/"
cp "코바코스메틱 Favicon.webp" "작업장/designbook_plen_backup/_dbook/img/"
cp Artboard* "작업장/designbook_plen_backup/_dbook/img/banner/"
cp "함초배너.png" "코스메틱 배너.png" "작업장/designbook_plen_backup/_dbook/img/banner/"

cp Favicon.svg "작업장/designbookM_plen_backup/_dbook/img/"
cp "코바코스메틱 Favicon.webp" "작업장/designbookM_plen_backup/_dbook/img/"
cp Artboard* "작업장/designbookM_plen_backup/_dbook/img/banner/"
cp "함초배너.png" "코스메틱 배너.png" "작업장/designbookM_plen_backup/_dbook/img/banner/"
```

- [ ] **Step 3: 파일 복사 정상 여부 검증**

Run:
```bash
ls -la "작업장/designbook_plen_backup/_dbook/img/banner"
ls -la "작업장/designbookM_plen_backup/_dbook/img/banner"
```
Expected: 각 폴더에 6개 배너 이미지 및 Favicon 2종 정상 존재

- [ ] **Step 4: Git 커밋**

```bash
git add .
git commit -m "chore(assets): place banner and favicon assets into PC and mobile skins"
```

---

### Task 2: PC 메인 비주얼 배너 4종 텍스트 및 파비콘 아이콘 연동

**Files:**
- Modify: `작업장/designbook_plen_backup/main/index.html:12-45`
- Modify: `작업장/designbook_plen_backup/_dbook/css/main.css:1-50`

**Interfaces:**
- Consumes: `../_dbook/img/Favicon.svg`
- Produces: 4개 슬라이드 텍스트 레이어 (`visual-title`)

- [ ] **Step 1: PC `main/index.html`의 `.slider-nav`를 4개 슬라이드로 변경**

수정 내용:
```html
		<ul class="slider-nav" style="display:none;">
			<!-- 슬라이드 1: 떡국 -->
			<li>
				<div class="visual-title">
					<strong><span>BEST</span></strong>		
					<h3><span>즉석 쌀떡국 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>진한 사골국물과 갓 찧은 햅쌀 사용으로 더욱 쫄깃한 떡국입니다.</span></p>
					<i><a href="https://www.korvamall.com/goods/goods_list.php?cateCd=003001"><span>자세히 보기</span></a></i>
				</div>
			</li>
			<!-- 슬라이드 2: 멸치 쌀국수 -->
			<li>
				<div class="visual-title">
					<strong><span>BEST</span></strong>		
					<h3><span>즉석 멸치 쌀국수 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>기름에 튀기지 않은 쫄깃한 면발과 시원한 멸치국물로 우려낸 코바 대표 상품입니다.</span></p>
					<i><a href="https://www.korvamall.com/goods/goods_list.php?cateCd=003002"><span>자세히 보기</span></a></i>
				</div>
			</li>
			<!-- 슬라이드 3: 냉면 -->
			<li>
				<div class="visual-title">
					<strong><span>NEW</span></strong>		
					<h3><span>코바 냉면 출시 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>함초자염이 들어간 깔끔한 맛의 육수와 비빔장! 쌀이 함유된 부드러운 면발!</span></p>
					<i><a href="https://www.korvamall.com/goods/goods_list.php?cateCd=003004"><span>자세히 보기</span></a></i>
				</div>
			</li>
			<!-- 슬라이드 4: 김 -->
			<li>
				<div class="visual-title">
					<strong><span>PROMOTION</span></strong>		
					<h3><span>서천 바다 100% 국내산 원초의 담백한 맛 그대로</span></h3>
					<p><span>기름을 바르지 않고 자연 그대로 살짝 갓 구워 원초의 향과 풍미 그대로를 담았습니다.</span></p>
					<i><a href="https://www.korvamall.com/goods/goods_list.php?cateCd=003003"><span>자세히 보기</span></a></i>
				</div>
			</li>
		</ul>
```

- [ ] **Step 2: PC `_dbook/css/main.css`에 파비콘 아이콘 스타일 추가**

```css
.visual-title h3 .ico-fav {
	display: inline-block;
	vertical-align: middle;
	width: 26px;
	height: 26px;
	margin-left: 6px;
	margin-top: -4px;
}
```

- [ ] **Step 3: 치환식 및 구문 검증**

Run:
```bash
git diff 작업장/designbook_plen_backup/main/index.html
```
Expected: 4개 슬라이드 목록이 정확히 배치되고 플리엔 문구 완전 제거됨

- [ ] **Step 4: Git 커밋**

```bash
git add 작업장/designbook_plen_backup/main/index.html 작업장/designbook_plen_backup/_dbook/css/main.css
git commit -m "feat(pc): update main visual banner texts to 4 slides with favicon icon"
```

---

### Task 3: 모바일 메인 비주얼 배너 4종 텍스트 및 파비콘 아이콘 연동

**Files:**
- Modify: `작업장/designbookM_plen_backup/main/index.html:15-48`
- Modify: `작업장/designbookM_plen_backup/_dbook/css/main.css:1-50`

**Interfaces:**
- Consumes: `../_dbook/img/Favicon.svg`
- Produces: 모바일 슬라이드 텍스트 레이어

- [ ] **Step 1: 모바일 `main/index.html`의 `.slider-nav`를 4개 슬라이드로 변경**

수정 내용:
```html
		<ul class="slider-nav" style="display:none;">
			<li>
				<div class="visual-title">
					<strong><span>BEST</span></strong>		
					<h3><span>즉석 쌀떡국 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>진한 사골국물과 갓 찧은 햅쌀 사용으로 더욱 쫄깃한 떡국</span></p>
				</div>
			</li>
			<li>
				<div class="visual-title">
					<strong><span>BEST</span></strong>		
					<h3><span>즉석 멸치 쌀국수 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>기름에 튀기지 않은 쫄깃한 면발과 시원한 멸치국물</span></p>
				</div>
			</li>
			<li>
				<div class="visual-title">
					<strong><span>NEW</span></strong>		
					<h3><span>코바 냉면 출시 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="코바" /></span></h3>
					<p><span>함초자염 육수와 비빔장, 쌀이 함유된 부드러운 면발</span></p>
				</div>
			</li>
			<li>
				<div class="visual-title">
					<strong><span>PROMOTION</span></strong>		
					<h3><span>서천 바다 100% 원초의 담백한 맛</span></h3>
					<p><span>기름 없이 살짝 갓 구워 원초의 향과 풍미를 담았습니다</span></p>
				</div>
			</li>
		</ul>
```

- [ ] **Step 2: 모바일 `_dbook/css/main.css`에 모바일 파비콘 스타일 추가**

```css
.visual-title h3 .ico-fav {
	display: inline-block;
	vertical-align: middle;
	width: 18px;
	height: 18px;
	margin-left: 4px;
	margin-top: -2px;
}
```

- [ ] **Step 3: 구문 검증 및 확인**

Run:
```bash
git diff 작업장/designbookM_plen_backup/main/index.html
```

- [ ] **Step 4: Git 커밋**

```bash
git add 작업장/designbookM_plen_backup/main/index.html 작업장/designbookM_plen_backup/_dbook/css/main.css
git commit -m "feat(mobile): update main visual banner texts to 4 slides with favicon icon"
```

---

### Task 4: PC 와이드 배너(함초, 코스메틱) 텍스트, 곡률(20px), 스타일링

**Files:**
- Modify: `작업장/designbook_plen_backup/main/index.html:60-80, 195-215`
- Modify: `작업장/designbook_plen_backup/_dbook/css/main.css:80-95`

**Interfaces:**
- Consumes: `../_dbook/img/코바코스메틱 Favicon.webp`
- Produces: 곡률 20px 및 여백이 최적화된 함초/코스메틱 와이드 배너

- [ ] **Step 1: PC `main/index.html`의 `main-box2`(함초) 텍스트 변경**

```html
		<div class="main-box2">
			<div class="main-inner">
				<div class="wide-bn wide-bn-hamcho _transY">
					<!--{ @dataBanner('3414171578',true) }-->
						<a href="{.bannerLink}" target="{.bannerTarget}">
							<span class="w100"><img src="{.bannerImageUrl}"></span>
							<div class="bn-txt _transYS ani4">	
								<h3>자연에서 얻은 천연 미네랄 바다의 산삼, 함초를 아시나요?</h3>
								<p>함초는 갯벌(순천만)에서 자라며, 자연의 짠맛을 지닌 염생식물로<br />일반 소금보다 각종 영양성분 및 미네랄이 풍부합니다.</p>
							</div>
						</a>
					<!--{ / }-->
				</div>
			</div>
		</div>
```

- [ ] **Step 2: PC `main/index.html`의 `main-box7`(코스메틱) 텍스트 변경**

```html
		<div class="main-box7">
			<div class="main-inner">
				<div class="wide-bn wide-bn-cosmetic _transY">
					<!--{ @dataBanner('2570777611',true) }-->
						<a href="{.bannerLink}" target="{.bannerTarget}">
							<span class="w100"><img src="{.bannerImageUrl}"></span>
							<div class="bn-txt bn-txt2 _transYS ani4">	
								<h3><img class="ico-cosmetic-fav" src="../_dbook/img/코바코스메틱 Favicon.webp" alt="" /> (주)코바식품에서 런칭한 자회사 코스메틱 브랜드</h3>
								<p>식품의 신뢰를 피부에 닿는 과학으로 확장합니다.</p>
							</div>
						</a>
					<!--{ / }-->
				</div>
			</div>
		</div>
```

- [ ] **Step 3: PC `_dbook/css/main.css`에 곡률(20px) 및 스타일 적용**

```css
/* 와이드배너 */
.wide-bn { position:relative; border-radius:20px; overflow:hidden; }
.wide-bn img { width:100%; display:block; border-radius:20px; }
.wide-bn .bn-txt { position:absolute; left:80px; top:50%; transform:translateY(-50%); color:#222; line-height:1.4; }
.wide-bn .bn-txt h3 { margin:0 0 12px; font-size:24px; font-weight:700; color:#111; word-break:keep-all; }
.wide-bn .bn-txt p { font-size:15px; color:#444; line-height:1.5; word-break:keep-all; }
.wide-bn .bn-txt2 { left:80px; }
.wide-bn .ico-cosmetic-fav { display:inline-block; vertical-align:middle; width:26px; height:26px; margin-right:6px; margin-top:-3px; }
```

- [ ] **Step 4: 변경 사항 검증**

Run:
```bash
git diff 작업장/designbook_plen_backup/main/index.html 작업장/designbook_plen_backup/_dbook/css/main.css
```

- [ ] **Step 5: Git 커밋**

```bash
git add 작업장/designbook_plen_backup/main/index.html 작업장/designbook_plen_backup/_dbook/css/main.css
git commit -m "feat(pc): apply hamcho and cosmetic wide banner content and 20px radius"
```

---

### Task 5: 모바일 와이드 배너(함초, 코스메틱) 텍스트, 곡률(12px), 반응형 스타일링

**Files:**
- Modify: `작업장/designbookM_plen_backup/main/index.html:60-80, 190-210`
- Modify: `작업장/designbookM_plen_backup/_dbook/css/main.css:80-95`

**Interfaces:**
- Consumes: 모바일 스킨 템플릿 및 스타일시트
- Produces: 모바일 최적화된 함초/코스메틱 배너

- [ ] **Step 1: 모바일 `main/index.html`의 `main-box2`(함초) 텍스트 변경**

```html
		<div class="main-box2">
			<div class="main-inner">
				<div class="wide-bn wide-bn-hamcho _transY">
					<!--{ @dataBanner('4252770060',true) }-->
						<a href="{.bannerLink}" target="{.bannerTarget}">
							<span class="w100"><img src="{.bannerImageUrl}"></span>
							<div class="bn-txt _transYSBn ani4">	
								<h3>바다의 산삼, 함초를 아시나요?</h3>
								<p>자연의 짠맛을 지닌 영양 가득 염생식물</p>
							</div>
						</a>
					<!--{ / }-->
				</div>
			</div>
		</div>
```

- [ ] **Step 2: 모바일 `main/index.html`의 `main-box7`(코스메틱) 텍스트 변경**

```html
		<div class="main-box7">
			<div class="main-inner">
				<div class="wide-bn wide-bn-cosmetic _transY">
					<!--{ @dataBanner('2805788834',true) }-->
						<a href="{.bannerLink}" target="{.bannerTarget}">
							<span class="w100"><img src="{.bannerImageUrl}"></span>
							<div class="bn-txt bn-txt2 _transYSBn ani4">	
								<h3><img class="ico-cosmetic-fav" src="../_dbook/img/코바코스메틱 Favicon.webp" alt="" /> 코바 자회사 코스메틱 브랜드</h3>
								<p>식품의 신뢰를 피부에 닿는 과학으로</p>
							</div>
						</a>
					<!--{ / }-->
				</div>
			</div>
		</div>
```

- [ ] **Step 3: 모바일 `_dbook/css/main.css`에 곡률(12px) 및 반응형 스타일 적용**

```css
/* 와이드배너 */
.wide-bn { position:relative; margin:0 18px; border-radius:12px; overflow:hidden; }
.wide-bn img { width:100%; display:block; border-radius:12px; }
.wide-bn .bn-txt { position:absolute; left:0; top:50%; transform:translate(0,-50%); padding:0 18px; box-sizing:border-box; color:#222; }
.wide-bn .bn-txt h3 { margin:0 0 6px; font-size:15px; font-weight:600; line-height:1.3; color:#111; word-break:keep-all; }
.wide-bn .bn-txt p { font-size:12px; color:#444; line-height:1.35; word-break:keep-all; }
.wide-bn .bn-txt2 { color:#222; }
.wide-bn .ico-cosmetic-fav { display:inline-block; vertical-align:middle; width:18px; height:18px; margin-right:4px; margin-top:-2px; }
```

- [ ] **Step 4: 변경 사항 검증**

Run:
```bash
git diff 작업장/designbookM_plen_backup/main/index.html 작업장/designbookM_plen_backup/_dbook/css/main.css
```

- [ ] **Step 5: Git 커밋**

```bash
git add 작업장/designbookM_plen_backup/main/index.html 작업장/designbookM_plen_backup/_dbook/css/main.css
git commit -m "feat(mobile): apply hamcho and cosmetic wide banner content and 12px radius"
```

---

### Task 6: 템플릿 검증 및 스킨 ZIP 배포 패키징 생성

**Files:**
- Create: `dist/designbook_plen_renewal.zip`
- Create: `dist/designbookM_plen_renewal.zip`

**Interfaces:**
- Consumes: `작업장/designbook_plen_backup/`, `작업장/designbookM_plen_backup/`
- Produces: 고도몰 관리자에서 즉시 업로드 가능한 완제품 ZIP 파일

- [ ] **Step 1: 고도몰 템플릿 치환문법 및 include 검증 스크립트 실행**

Run:
```bash
# 치환 태그 매칭 및 문법 오류 여부 정밀 검사
grep -rn '<!--{ ?' 작업장/designbook_plen_backup/main/index.html
grep -rn '<!--{ ?' 작업장/designbookM_plen_backup/main/index.html
```
Expected: 여닫기 태그 불일치나 문법 오류 없음

- [ ] **Step 2: 배포 디렉터리(`dist/`) 생성 및 ZIP 패키징**

Run:
```bash
mkdir -p dist
cd "작업장/designbook_plen_backup" && zip -rq "../../dist/designbook_plen_renewal.zip" . -x "*.DS_Store"
cd "../../작업장/designbookM_plen_backup" && zip -rq "../../dist/designbookM_plen_renewal.zip" . -x "*.DS_Store"
cd ../..
ls -lh dist/
```
Expected: 두 ZIP 파일이 성공적으로 생성됨

- [ ] **Step 3: 생성된 ZIP 패키지 무결성 검증**

Run:
```bash
unzip -l dist/designbook_plen_renewal.zip | grep -E "(Favicon|_slider_banner|Artboard)" | head -n 10
unzip -l dist/designbookM_plen_renewal.zip | grep -E "(Favicon|_slider_banner|Artboard)" | head -n 10
```
Expected: 스킨 내부에 신규 에셋 및 수정된 템플릿이 완벽하게 포함되어 있음

---

### Task 7: Git 커밋, 태그 및 GitHub 원격 푸시

**Files:**
- Modify: `.gitignore` (필요시 dist 추가/관리)
- Modify: `CONTEXT.md`

**Interfaces:**
- Consumes: 완성된 소스 및 문서
- Produces: 원격 GitHub 리포지토리 최신 커밋

- [ ] **Step 1: CONTEXT.md에 신규 배너 코드 및 매핑 정보 갱신**

```markdown
### 신규 배너 매핑 (2차 리뉴얼 기준)
- 메인 슬라이더 (4종): 떡국, 멸치 쌀국수, 냉면, 서천 김
- 와이드 배너 1: 함초 배너 (main-box2)
- 와이드 배너 2: 코스메틱 배너 (main-box7)
```

- [ ] **Step 2: 최종 Git 커밋 및 GitHub 원격 푸시**

Run:
```bash
git add .
git commit -m "feat: complete banner renewal, local asset integration, and skin packaging"
git push origin main
```

- [ ] **Step 3: 원격 상태 확인**

Run:
```bash
git status
git log -n 1
```
Expected: working tree clean, origin/main과 동기화 완료

