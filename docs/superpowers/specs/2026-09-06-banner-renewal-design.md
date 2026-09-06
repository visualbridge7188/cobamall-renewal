# 코바몰 고도몰5 메인/와이드 배너 리뉴얼 및 에셋 패키징 스펙

## 1. 개요
코바몰 고도몰5 PC 및 모바일 스킨의 메인 비주얼 배너(4종)와 중간 와이드 배너(함초, 코스메틱)의 텍스트, 이미지, 스타일(곡률 및 반응형 여백)을 개선하고, 쇼핑몰 원격 서버에서 바로 적용할 수 있도록 스킨 내부 에셋 탑재 및 ZIP 배포 패키징을 수행한다.

## 2. 작업 대상 파일
- PC 스킨: `작업장/designbook_plen_backup/`
  - `main/index.html`
  - `_dbook/css/main.css`
  - `_dbook/img/Favicon.svg`, `_dbook/img/코바코스메틱 Favicon.webp`
  - `_dbook/img/banner/` (`Artboard 1~4`, `함초배너.png`, `코스메틱 배너.png`)
- 모바일 스킨: `작업장/designbookM_plen_backup/`
  - `main/index.html`
  - `_dbook/css/main.css`
  - `_dbook/img/Favicon.svg`, `_dbook/img/코바코스메틱 Favicon.webp`
  - `_dbook/img/banner/` (동일 에셋)
- 배포 패키지:
  - `dist/designbook_plen_renewal.zip` (PC 스킨 배포용)
  - `dist/designbookM_plen_renewal.zip` (모바일 스킨 배포용)

## 3. 세부 요구사항 및 확정 스펙

### A. 메인 비주얼 배너 (PC / 모바일)
- 고도몰 위젯(`_slider_banner.html`)의 배너 코드(`511517418`, `3606671697`)는 유지.
- 텍스트 레이어(`.slider-nav`)를 기존 3개에서 4개 슬라이드로 확장.
- 슬라이드 1 (떡국):
  - 제목: `즉석 쌀떡국 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="파비콘" />`
  - 설명: `진한 사골국물과 갓 찧은 햅쌀 사용으로 더욱 쫄깃한 떡국입니다.`
  - 링크: `https://www.korvamall.com/goods/goods_list.php?cateCd=003001`
- 슬라이드 2 (멸치 쌀국수):
  - 제목: `즉석 멸치 쌀국수 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="파비콘" />`
  - 설명: `기름에 튀기지 않은 쫄깃한 면발과 시원한 멸치국물로 우려낸 코바 대표 상품입니다.`
  - 링크: `https://www.korvamall.com/goods/goods_list.php?cateCd=003002`
- 슬라이드 3 (냉면):
  - 제목: `코바 냉면 출시 <img class="ico-fav" src="../_dbook/img/Favicon.svg" alt="파비콘" />`
  - 설명: `함초자염이 들어간 깔끔한 맛의 육수와 비빔장! 쌀이 함유된 부드러운 면발!`
  - 링크: `https://www.korvamall.com/goods/goods_list.php?cateCd=003004`
- 슬라이드 4 (김):
  - 제목: `서천 바다 100% 국내산 원초의 담백한 맛 그대로`
  - 설명: `기름을 바르지 않고 자연 그대로 살짝 갓 구워 원초의 향과 풍미 그대로를 담았습니다.`
  - 링크: `https://www.korvamall.com/goods/goods_list.php?cateCd=003003`

### B. 함초 와이드 배너 (main-box2)
- 제목: `자연에서 얻은 천연 미네랄 바다의 산삼, 함초를 아시나요?`
- 텍스트 (A안 확정):
  `함초는 갯벌(순천만)에서 자라며, 자연의 짠맛을 지닌 염생식물로<br class="pc-br" />일반 소금보다 각종 영양성분 및 미네랄이 풍부합니다.`
- 스타일: 컨테이너 및 이미지에 곡률 20px (모바일 12px) 적용, 텍스트 가독성 여백 확보.

### C. 코스메틱 와이드 배너 (main-box7)
- 제목:
  `<h3><img class="ico-cosmetic-fav" src="../_dbook/img/코바코스메틱 Favicon.webp" alt="코스메틱" /> (주)코바식품에서 런칭한 자회사 코스메틱 브랜드</h3>`
- 설명: `식품의 신뢰를 피부에 닿는 과학으로 확장합니다.`
- 스타일: 컨테이너 및 이미지에 곡률 20px (모바일 12px) 적용.

### D. 에셋 탑재 및 패키징
- 모든 신규 에셋을 스킨 내부 `_dbook/img/`에 직접 배치하여 외부 의존성 제거.
- 고도몰 관리자 배너 관리 등록을 돕기 위해 에셋 파일명 표준화 및 매칭 가이드 제공.
- 수정 완료된 스킨을 ZIP으로 압축 패키징하여 즉시 업로드 가능하도록 산출물 생성.

