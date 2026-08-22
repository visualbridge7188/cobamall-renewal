# 고도몰5 스킨 리뉴얼 작업 컨텍스트

## 프로젝트 목적

고도몰5 쇼핑몰의 프론트엔드 스킨을 리뉴얼한다. 대상은 PC 스킨과 모바일 스킨이며, 기능·관리자 연동·치환코드를 보존한 상태에서 HTML/CSS/JS와 이미지 자산을 수정하고 배포 전 검증하는 것이 목적이다. NHN커머스는 고도몰5를 커스터마이징 가능한 쇼핑몰 솔루션으로 설명한다 ([NHN 서비스 안내](https://nhn.com/services?tab=commerce), [고도몰5 서비스 소개](https://www.nhn-commerce.com/company/service/solution-info.gd)).

## 현재 구조

- PC: `작업장/designbook_plen_backup`
- 모바일: `작업장/designbookM_plen_backup`
- 원본 백업: `skin_backup/`의 ZIP 2개. 작업 중 수정하지 않는다.
- 전체 2,973개 파일: PC 1,563개, 모바일 1,409개, 루트 `.DS_Store` 1개.
- 두 스킨 모두 `main`, `goods`, `member`, `mypage`, `order`, `board`, `event`, `service`, `outline`, `share`, `css`, `js`, `img`, `__conf__` 등의 영역을 가진다. 실제 파일은 대체로 `.html` 템플릿이다.
- 주요 커스텀 진입점은 `main/index.html`, `outline/`, `_dbook/`, PC의 `css/custom.css`다. 페이지별 기본 기능 파일은 필요한 경우에만 수정한다.
- 고도몰은 PC와 모바일 스킨을 각각 관리한다고 안내한다. 현재 작업장도 별도 디렉터리와 템플릿을 유지하므로 공통 변경은 양쪽 파일을 모두 대조한다 ([디자인 설정 FAQ](https://godomall-help.nhn-commerce.com/faq/admin/design/option), [PC 스킨 관리](https://godomall-help.nhn-commerce.com/beginner/design/skin-setting/pc), [모바일 스킨 관리](https://godomall-help.nhn-commerce.com/biginner/design/skin-setting/mobile)). 모바일샵을 끄면 모바일 기기에도 PC 화면이 표시될 수 있으므로 실제 운영 설정은 관리자에서 확인한다.

## 공식 문서 기반 핵심 규칙

### 편집 위치와 작업 스킨

고도몰 공식 가이드는 사용 스킨과 작업 스킨을 구분한다. 사용 스킨은 고객에게 노출되며, 다른 스킨을 작업 스킨으로 지정하면 노출하지 않고 HTML을 수정할 수 있다. 삭제한 페이지는 복구할 수 없으므로 사용 스킨이 아닌 작업 스킨에서 작업하라고 안내한다 ([PC 스킨 관리](https://godomall-help.nhn-commerce.com/beginner/design/skin-setting/pc), [디자인 설정 FAQ](https://godomall-help.nhn-commerce.com/faq/admin/design/option)). 로컬 파일을 적용하기 전 실제 대상 스킨과 PC/모바일을 먼저 식별한다.

### 백업·업로드·검수

공식 가이드는 스킨 패치 전에 쇼핑몰 소스코드를 반드시 백업하고, 상품/기능 설치 없이 스킨 패치만 적용하면 오류가 날 수 있다고 경고한다 ([NHN커머스 스킨 패치 가이드](https://store-help.nhn-commerce.com/app/promotion/1517)). 적용 순서는 `백업 → 작업 스킨 확인 → 파일별 변경 → 관리자 디자인 소스에 업로드/적용 → 실제 화면 검수`로 고정한다.

관리자에서 보유 스킨을 ZIP으로 내려받고 다시 업로드할 수 있다. 공식 FAQ 기준 ZIP 업로드 한도는 5MB이며, 초과 시 큰 자산을 임시 제외해 스킨을 등록한 뒤 FTP로 보완한다 ([디자인 설정 FAQ](https://godomall-help.nhn-commerce.com/faq/admin/design/option)). 이 제한은 향후 관리자 정책 변경 가능성이 있으므로 실제 업로드 화면에서도 재확인한다.

### 치환코드·주석·템플릿 문법

현재 스킨에는 `{=변수}`, `{=__('문구')}`, `<!--{ ? 조건 }--> … <!--{ / }-->`, `<!--{ @ 반복변수 }-->` 형태가 실제 사용된다. 이는 이 저장소의 템플릿 현황이며, 문법을 임의로 일반 PHP/JavaScript 문법으로 바꾸지 않는다. 공식 패치 가이드도 HTML 안에 고도몰 템플릿 변수와 조건문이 포함된 파일을 직접 수정하는 예를 제시한다 ([공식 스킨 패치 예시](https://store-help.nhn-commerce.com/app/promotion/1517)). 다만 NHN커머스가 공개한 현재 버전의 치환코드 전체 목록, 주석 처리 규칙, 변수별 타입/출력 이스케이프 규칙 원문은 이번 조사에서 확인하지 못했다. 새 치환코드를 만들거나 기존 코드를 삭제하지 말고, 기능별 공식 패치 예시 또는 운영 중인 동일 파일을 기준으로 변경한다.

### PC·모바일·언어 스킨

NHN커머스 고객센터는 글로벌 기능을 쓰려면 디자인 관리의 스킨 리스트에서 해당 언어 지원 스킨을 사용 스킨으로 적용해야 하며, 언어 표시가 없는 스킨은 한국어 스킨이라 글로벌 기능이 정상 작동하지 않을 수 있다고 안내한다 ([NHN커머스 디자인센터 FAQ](https://godomall-help.nhn-commerce.com/faq/manage/design-center)). 따라서 모바일/PC뿐 아니라 국문·글로벌 스킨 여부도 적용 전에 확인한다. 현재 두 백업 디렉터리가 어느 관리자 스킨 ID/언어와 연결되는지는 로컬 파일만으로 확정할 수 없다.

### 디자인 보안·주의사항

이번 조사에서 확인 가능한 공식 자료는 소스 백업, 기능 설치 선행, 현재 작업스킨 선택, 언어 지원 확인까지다 ([스킨 패치 주의사항](https://store-help.nhn-commerce.com/app/promotion/1517), [디자인센터 FAQ](https://godomall-help.nhn-commerce.com/faq/manage/design-center)). 관리자 비밀번호·FTP 자격증명·결제/개인정보 값은 스킨 파일이나 저장소에 넣지 않는다. 다만 고도몰5 스킨의 허용 파일 확장자, 서버 측 실행 차단, CSP/XSS/외부 스크립트 정책을 설명한 현재 공식 문서는 확인하지 못했으므로 이 문서에서는 플랫폼 보장 규칙으로 단정하지 않는다.

## 미확정 사항

- 현재 쇼핑몰의 고도몰5 basic/pro 및 패치 버전, 실제 활성 PC/모바일 스킨 ID
- 관리자에서 두 백업 디렉터리를 적용할 정확한 경로와 업로드 방식(디자인 관리자 업로드인지 FTP인지)
- 치환코드 전체 사전, 주석 문법의 공식 최신 명세, 출력 이스케이프/보안 규칙
- PC와 모바일의 자동 전환 조건 및 반응형/전용 모바일 운영 여부
- 공식 문서에서 제공하는 별도 스테이징·캐시 초기화·브라우저별 검증 절차

위 항목은 관리자 화면, 쇼핑몰 계약/계정, NHN커머스 기술지원 답변 없이는 사실로 확정하지 않는다. 현재 공식 운영 가이드의 목차에는 `디자인 관리`, `배너 및 팝업창 관리`, `디자인 향상시키기`가 포함되어 있다 ([고도몰 운영 가이드](https://godomall-help.nhn-commerce.com/)).

## 출처

- [NHN 서비스 안내 — godomall](https://nhn.com/services?tab=commerce)
- [NHN커머스 — 고도몰5 서비스 소개](https://www.nhn-commerce.com/company/service/solution-info.gd)
- [고도몰 운영 가이드](https://godomall-help.nhn-commerce.com/)
- [고도몰 디자인센터 FAQ](https://godomall-help.nhn-commerce.com/faq/manage/design-center)
- [고도몰 디자인 설정 FAQ](https://godomall-help.nhn-commerce.com/faq/admin/design/option)
- [고도몰 PC 스킨 관리](https://godomall-help.nhn-commerce.com/beginner/design/skin-setting/pc)
- [고도몰 모바일 스킨 관리](https://godomall-help.nhn-commerce.com/biginner/design/skin-setting/mobile)
- [고도몰 PC 배너 관리](https://godomall-help.nhn-commerce.com/beginner/design/banner-setting/pc)
- [NHN커머스 스킨 패치 가이드](https://store-help.nhn-commerce.com/app/promotion/1517)
- [고도몰5 시작 가이드 PDF](https://static.godo.co.kr/download/startguide.pdf)

조사 기준일: 2026-08-09. 공식/1차 출처만 사용했으며, 검색·접근이 되지 않은 문서는 미확정 사항으로 분리했다.
