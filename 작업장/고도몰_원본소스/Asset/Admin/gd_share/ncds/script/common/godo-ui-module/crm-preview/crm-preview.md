# CRM Preview

CRM 메시지 발송 미리보기를 동적으로 생성하고 관리합니다.

## 📋 목차

1. [개요](#개요)
2. [주요 특징](#주요-특징)
3. [설치 및 로드](#설치-및-로드)
4. [기본 사용법](#기본-사용법)
5. [발송 타입별 가이드](#발송-타입별-가이드)
6. [API 레퍼런스](#api-레퍼런스)
7. [GodoUIModule 통합](#godoUImodule-통합)
8. [아키텍처](#아키텍처)
9. [중요 사항](#중요-사항)

---

## 개요

CRM Preview는 4가지 메시지 발송 타입(SMS, 친구톡, 알림톡, 마이앱)의 미리보기를 동적으로 생성하고 관리합니다.

### 지원하는 발송 타입
- **SMS**: 문자 메시지
- **FRIENDTALK**: 카카오 친구톡 (5가지 메시지 타입 지원)
- **ALIMTALK**: 카카오 알림톡
- **MYAPP**: 마이앱 푸시

---

## 주요 특징

✅ **동적 HTML 생성**: 미리보기 HTML을 자동으로 생성합니다
✅ **순수 Vanilla JavaScript**: jQuery 없이 작동 (단, 캐러셀 기능은 slick.js 필요)
✅ **State 패턴**: 각 발송 타입별로 독립적인 State 클래스로 관리
✅ **Facade 패턴**: 단순하고 일관된 API 제공
✅ **실시간 업데이트**: 메서드 체이닝으로 즉시 미리보기 반영

---

## 설치 및 로드

### 방법 1: GodoUIModule 사용 (권장)

```html
<!-- jQuery (캐러셀 사용 시 필요) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Slick (캐러셀 사용 시 필요) -->
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">

<!-- GodoUIModule -->
<script src="godo-ui-module.js"></script>

<script>
    // GodoUIModule을 통한 초기화
    const crmPreview = GodoUIModule.render({
        type: 'MessagePreview',
        target: '.preview-container',
        messageType: 'SMS',
        noticeText: '미리보기와 실제가 다를 수 있습니다.',
        checkboxText: '변수로 변환해서 보기',
        onVariableToggle: function(isChecked) {
            console.log('변수 모드:', isChecked);
        }
    });
</script>
```

### 방법 2: Loader 사용

#### 모든 State 로드 (기본)

```html
<!-- CRM Preview Library Loader -->
<script src="crm-preview-loader.js"></script>

<script>
    window.addEventListener('crmPreviewLoaded', function() {
        const crmPreview = new CrmPreview({
            container: '.preview-content-wrap'
        });
    });
</script>
```

#### 특정 State만 선택적으로 로드

`data-exclude` 속성을 사용하여 불필요한 State를 제외할 수 있습니다. 이렇게 하면 파일 크기를 줄이고 로딩 속도를 개선할 수 있습니다.

```html
<!-- 친구톡만 제외 -->
<script src="crm-preview-loader.js" data-exclude="friendtalk"></script>

<!-- 친구톡과 마이앱 제외 (SMS, 알림톡만 사용) -->
<script src="crm-preview-loader.js" data-exclude="friendtalk,myapp"></script>

<!-- 알림톡만 제외 -->
<script src="crm-preview-loader.js" data-exclude="alimtalk"></script>

<script>
    window.addEventListener('crmPreviewLoaded', function() {
        const crmPreview = new CrmPreview({
            container: '.preview-content-wrap'
        });
        
        // 제외한 타입은 사용할 수 없음
        crmPreview.setSendType('SMS').render();  // ✅ 가능
        crmPreview.setSendType('ALIMTALK').render();  // ✅ 가능
        // crmPreview.setSendType('FRIENDTALK').render();  // ❌ 에러 (제외됨)
    });
</script>
```

**사용 가능한 exclude 값:**
- `sms`: SMS State 제외
- `friendtalk`: 친구톡 State 제외
- `alimtalk`: 알림톡 State 제외
- `myapp`: 마이앱 State 제외

**참고:**
- `constants.js`와 `crm-preview.js`(core)는 항상 로드됩니다 (제외 불가)
- 여러 개를 제외할 때는 쉼표(`,`)로 구분합니다
- 제외된 State는 콘솔에 로그로 표시됩니다

### 방법 3: 수동 로드 (최대 제어)

필요한 파일만 직접 선택하여 로드합니다.

```html
<!-- 필수 파일 -->
<script src="constants.js"></script>

<!-- 사용할 State만 선택적으로 로드 -->
<script src="states/sms-state.js"></script>
<!-- <script src="states/friendtalk-state.js"></script> --> <!-- 불필요하면 제외 -->
<script src="states/alimtalk-state.js"></script>
<script src="states/myapp-state.js"></script>

<!-- 필수 파일 -->
<script src="crm-preview.js"></script>

<script>
    // 바로 사용 가능 (이벤트 대기 불필요)
    const crmPreview = new CrmPreview({
        container: '.preview-content-wrap'
    });
</script>
```

---

## 기본 사용법

### 1. 초기화

```javascript
// 직접 인스턴스 생성
const crmPreview = new CrmPreview({
    container: '.preview-content-wrap'
});

// 또는 GodoUIModule 사용
const crmPreview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '.preview-container',
    messageType: 'SMS'
});

// 발송 타입 설정 및 렌더링
crmPreview.setSendType('SMS').render();
```

### 2. 발송 타입 변경

```javascript
// SMS
crmPreview.setSendType('SMS').render();

// 친구톡
crmPreview.setSendType('FRIENDTALK').render();

// 알림톡
crmPreview.setSendType('ALIMTALK').render();

// 마이앱
crmPreview.setSendType('MYAPP').render();
```

### 3. 내용 업데이트

```javascript
// 제목 설정
crmPreview.setTitle('안녕하세요');

// 내용 설정
crmPreview.setContent('메시지 내용입니다.');

// 이미지 설정
crmPreview.setImage('https://example.com/image.jpg');
```

### 4. 메서드 체이닝

```javascript
crmPreview
    .setSendType('FRIENDTALK')
    .setMessageType('IMAGE')
    .render()
    .setTitle('신상품 안내')
    .setContent('새로운 상품이 출시되었습니다.')
    .setImage('https://example.com/product.jpg');
```

### 5. 초기화

```javascript
// 모든 필드를 초기화
crmPreview.reset();
```

---

## 발송 타입별 가이드

### 📱 SMS

가장 단순한 형태. 텍스트만 지원합니다.

```javascript
crmPreview
    .setSendType('SMS')
    .render()
    .setContent('안녕하세요.\n고객님께 특별한 혜택을 드립니다.');
```

**지원 메서드:**
- `setContent(value)`: 메시지 내용

---

### 💬 FRIENDTALK (친구톡)

5가지 메시지 타입을 지원하는 가장 복잡한 발송 타입입니다.

#### 메시지 타입

1. **TEXT**: 텍스트만
2. **IMAGE**: 제목 + 이미지 + 텍스트
3. **WIDE_IMAGE**: IMAGE와 동일 (와이드 레이아웃)
4. **WIDE_ITEM_LIST**: 제목 + 이미지 + 아이템 리스트 + 텍스트
5. **CAROUSEL_FEED**: 여러 슬라이드를 스와이프로 탐색

#### 기본 사용

```javascript
// TEXT 타입
crmPreview
    .setSendType('FRIENDTALK')
    .setMessageType('TEXT')
    .render()
    .setContent('친구톡 메시지입니다.');

// IMAGE 타입
crmPreview
    .setSendType('FRIENDTALK')
    .setMessageType('IMAGE')
    .render()
    .setTitle('신상품 출시')
    .setImage('https://example.com/product.jpg')
    .setContent('지금 바로 확인하세요!');
```

#### 버튼 추가

```javascript
crmPreview.setButtons([
    { text: '자세히 보기', url: 'https://example.com' },
    { text: '구매하기', url: 'https://example.com/buy' }
]);
```

**참고:** 버튼 클릭 시 자동으로 새 창(`_blank`)에서 URL이 열립니다.

#### 쿠폰 추가

```javascript
crmPreview.setCoupon({
    text: '10% 할인 쿠폰',
    date: '2024.12.31'
});

// 쿠폰 제거
crmPreview.setCoupon(null);
```

#### WIDE_ITEM_LIST 아이템 추가

```javascript
crmPreview.setItemList([
    {
        image: 'https://example.com/item1.jpg',
        title: '상품 A',
        desc: '19,900원'
    },
    {
        image: 'https://example.com/item2.jpg',
        title: '상품 B',
        desc: '29,900원'
    }
]);
```

#### CAROUSEL_FEED (캐러셀)

```javascript
// 캐러셀 타입 설정
crmPreview
    .setSendType('FRIENDTALK')
    .setMessageType('CAROUSEL_FEED')
    .render();

// 슬라이드 추가
crmPreview.addSlide();  // 슬라이드 1 추가
crmPreview.addSlide();  // 슬라이드 2 추가
crmPreview.addSlide();  // 슬라이드 3 추가

// 특정 슬라이드로 이동
crmPreview.setSlideIndex(0);  // 첫 번째 슬라이드

// 현재 슬라이드 내용 업데이트
crmPreview
    .setTitle('슬라이드 1 제목')
    .setContentTitle('슬라이드 1 소제목')
    .setContent('슬라이드 1 내용')
    .setImage('https://example.com/slide1.jpg');

// 다음 슬라이드로 이동 후 내용 업데이트
crmPreview.setSlideIndex(1);
crmPreview
    .setTitle('슬라이드 2 제목')
    .setContent('슬라이드 2 내용')
    .setImage('https://example.com/slide2.jpg');

// 슬라이드 삭제
crmPreview.removeSlide(1);  // 인덱스 1번 슬라이드 삭제

// 슬라이드 개수 확인
const count = crmPreview.getSlideCount();

// 현재 슬라이드 인덱스 확인
const current = crmPreview.getCurrentSlideIndex();
```

**주의사항:**
- 캐러셀 사용 시 jQuery와 slick.js가 필요합니다
- 최소 1개 이상의 슬라이드가 있어야 합니다
- 각 슬라이드는 독립적인 데이터를 가집니다
- `WIDE_IMAGE`, `WIDE_ITEM_LIST`, `CAROUSEL_FEED` 타입에서는 자동으로 와이드 레이아웃(`ncua-prev--lg`)과 수평 버튼 레이아웃(`ncua-prev__btns--horizontal`)이 적용됩니다

**지원 메서드:**
- `setTitle(value)`: 제목
- `setContentTitle(value)`: 내용 제목 (새로 추가됨)
- `setContent(value)`: 내용
- `setImage(src)`: 이미지
- `setButtons(buttons)`: 버튼 목록
- `setCoupon(coupon)`: 쿠폰
- `setItemList(items)`: 아이템 리스트 (WIDE_ITEM_LIST 전용)
- `addSlide()`: 슬라이드 추가 (CAROUSEL_FEED 전용)
- `removeSlide(index)`: 슬라이드 삭제 (CAROUSEL_FEED 전용)
- `setSlideIndex(index)`: 슬라이드 이동 (CAROUSEL_FEED 전용)

---

### 🔔 ALIMTALK (알림톡)

카카오 알림톡 미리보기를 관리합니다.

#### 기본 사용

```javascript
crmPreview
    .setSendType('ALIMTALK')
    .render()
    .setContent('주문이 완료되었습니다.\n배송은 2-3일 소요됩니다.');
```

#### 강조 제목/부제목

```javascript
crmPreview
    .setEmTitle('주문 완료')
    .setEmSubTitle('감사합니다');
```

#### 이미지 추가

```javascript
crmPreview.setImage('https://example.com/order-complete.jpg');
```

**참고:** 이미지가 없을 때는 자동으로 `display: none` 처리되어 UI가 깨지지 않습니다.

#### 버튼 추가

```javascript
crmPreview.setButtons([
    { text: '채널 추가', type: 'add-channel' },  // 채널 추가 버튼
    { text: '배송 조회', url: 'https://example.com/tracking' },
    { text: '상품 상세', url: 'https://example.com/product' }
]);
```

**참고:** 
- `type: 'add-channel'` 속성을 가진 버튼은 특별한 스타일(`ncua-prev-alim__btn--add-channel`)이 적용됩니다
- URL이 있는 버튼은 클릭 시 자동으로 새 창에서 열립니다

#### 리스트 헤더

```javascript
crmPreview.setListHeader('주문 정보');
```

#### 하이라이트 섹션

```javascript
crmPreview
    .setHighlightTitle('무료 배송')
    .setHighlightDesc('50,000원 이상 구매 시')
    .setHighlightImage('https://example.com/highlight.jpg');
```

**참고:** 하이라이트는 제목, 설명, 이미지 중 하나라도 있으면 자동으로 표시됩니다.

#### 아이템 리스트 (테이블 형식)

```javascript
crmPreview.setItemList([
    { title: '주문번호', desc: '202401-12345' },
    { title: '상품명', desc: '신상품 A' },
    { title: '수량', desc: '2개' },
    { title: '결제금액', desc: '50,000원' },
    { title: '합계', desc: '50,000원', isSummary: true }  // 요약 행
]);
```

**참고:** `isSummary: true`인 항목은 테이블 하단(tfoot)에 표시됩니다.

#### 추가 정보 및 채널 메시지

```javascript
crmPreview
    .setExtraInfo('배송비는 별도입니다.')
    .setChannelMessage('문의사항은 고객센터로 연락주세요.');
```

#### 전체 예시

```javascript
crmPreview
    .setSendType('ALIMTALK')
    .render()
    .setEmTitle('주문 완료')
    .setEmSubTitle('감사합니다')
    .setImage('https://example.com/product.jpg')
    .setListHeader('주문 정보')
    .setHighlightTitle('무료 배송')
    .setHighlightDesc('50,000원 이상 구매 시')
    .setHighlightImage('https://example.com/highlight.jpg')
    .setContent('주문이 완료되었습니다.\n배송까지 2-3일 소요됩니다.')
    .setItemList([
        { title: '주문번호', desc: '202401-12345' },
        { title: '상품명', desc: '신상품 A' },
        { title: '합계', desc: '50,000원', isSummary: true }
    ])
    .setExtraInfo('배송비는 별도입니다.')
    .setChannelMessage('문의사항은 고객센터로 연락주세요.')
    .setButtons([
        { text: '채널 추가', type: 'add-channel' },
        { text: '배송 조회', url: 'https://example.com/tracking' }
    ]);
```

**참고:** 
- 리스트 섹션은 리스트 헤더, 하이라이트, 아이템 리스트 중 하나라도 있으면 자동으로 표시됩니다
- 각 요소는 독립적으로 표시/숨김이 제어됩니다

**지원 메서드:**
- `setTitle(value)`: 알림톡 제목
- `setContent(value)`: 내용
- `setImage(src)`: 이미지
- `setEmTitle(value)`: 강조 제목
- `setEmSubTitle(value)`: 강조 부제목
- `setListHeader(value)`: 리스트 헤더
- `setHighlightTitle(value)`: 하이라이트 제목
- `setHighlightDesc(value)`: 하이라이트 설명
- `setHighlightImage(src)`: 하이라이트 이미지
- `setExtraInfo(value)`: 추가 정보
- `setChannelMessage(value)`: 채널 메시지
- `setItemList(items)`: 아이템 리스트
- `setButtons(buttons)`: 버튼 목록

---

### 📲 MYAPP (마이앱)

마이앱 푸시 메시지 미리보기를 관리합니다.

```javascript
crmPreview
    .setSendType('MYAPP')
    .render()
    .setTitle('맛집 BEST 3')
    .setContent('요즘 핫한 파인다이닝')
    .setImage('https://example.com/restaurant.jpg')
    .setWithdrawalMethod('무료거부 0000-0000');
```

**지원 메서드:**
- `setTitle(value)`: 푸시 제목
- `setContent(value)`: 푸시 내용
- `setImage(src)`: 푸시 이미지
- `setWithdrawalMethod(value)`: 수신거부 방법

---

## API 레퍼런스

### CrmPreview (Facade 클래스)

메인 클래스로, 모든 기능에 접근할 수 있는 통합 인터페이스를 제공합니다.

#### Constructor

```javascript
new CrmPreview(options)
```

**Parameters:**
- `options.container` (string | HTMLElement): 미리보기를 렌더링할 컨테이너 **(필수)**
- `options.noticeText` (string): notice 영역 초기 텍스트 (기본: `'<li class="ncua-notice-info">미리보기와 실제가 다를 수 있습니다.</li>'`)
- `options.checkboxText` (string): 체크박스 라벨 텍스트 (기본: `'변수로 변환해서 보기'`)
- `options.showAdLabel` (boolean): 마이앱 제목에 (광고) 표시 여부 (기본: `false`)

**Example:**
```javascript
const preview1 = new CrmPreview({ container: '.preview-wrap' });
const preview2 = new CrmPreview({ container: document.getElementById('preview') });
```

#### 공통 메서드

##### `setSendType(type)`
발송 타입을 설정합니다.

**Parameters:**
- `type` (string): 'SMS' | 'FRIENDTALK' | 'ALIMTALK' | 'MYAPP'

**Returns:** `CrmPreview` (체이닝 가능)

##### `render()`
현재 설정에 따라 미리보기 HTML을 동적으로 생성합니다.

**Returns:** `CrmPreview` (체이닝 가능)

##### `setTitle(value)`
제목을 설정합니다. (SMS는 지원하지 않음)

**Parameters:**
- `value` (string): 제목 텍스트

**Returns:** `CrmPreview` (체이닝 가능)

##### `setContent(value)`
내용을 설정합니다.

**Parameters:**
- `value` (string): 내용 텍스트 (HTML 가능)

**Returns:** `CrmPreview` (체이닝 가능)

##### `setImage(src)`
이미지를 설정합니다. (SMS는 지원하지 않음)

**Parameters:**
- `src` (string): 이미지 URL

**Returns:** `CrmPreview` (체이닝 가능)

##### `setButtons(buttons)`
버튼 목록을 설정합니다. (친구톡, 알림톡만 지원)

**Parameters:**
- `buttons` (Array): 버튼 객체 배열
  - `text` (string): 버튼 텍스트
  - `url` (string): 버튼 링크 URL (선택)
  - `type` (string): 버튼 타입 (선택, 알림톡의 경우 'add-channel'로 채널 추가 버튼 지정)

**Returns:** `CrmPreview` (체이닝 가능)

##### `reset()`
모든 필드를 초기화합니다. 각 State의 `reset()` 메서드를 호출하여 UI를 깨끗하게 정리합니다.

**Returns:** `CrmPreview` (체이닝 가능)

##### `destroy()`
리소스를 정리합니다. 현재 State를 파괴하고, 이벤트 리스너를 제거하며, DOM을 비웁니다.

**Returns:** `void`

```javascript
crmPreview.destroy();
```

##### `getSendType()`
현재 발송 타입을 반환합니다.

**Returns:** `string | null` - 현재 발송 타입 ('SMS' | 'FRIENDTALK' | 'ALIMTALK' | 'MYAPP'), 미설정 시 `null`

##### `hasImage()`
이미지가 설정되어 있는지 확인합니다.

**Returns:** `boolean`

##### `getData()`
현재 데이터 객체의 복사본을 반환합니다.

**Returns:** `Object` - `{ title, content, image, buttons, coupon, itemList, slideIndex, variableMode }`

```javascript
const data = crmPreview.getData();
console.log(data.content); // 현재 내용
```

#### 공지 및 체크박스 메서드

##### `setNoticeText(content)`
공지 텍스트를 설정합니다.

**Parameters:**
- `content` (string | HTMLElement): 공지 텍스트 또는 HTML 요소

**Returns:** `CrmPreview` (체이닝 가능)

```javascript
crmPreview.setNoticeText('미리보기와 실제가 다를 수 있습니다.');
crmPreview.setNoticeText('<strong>중요:</strong> 미리보기입니다.');
```

##### `setCheckboxText(text)`
변수 변환 체크박스의 라벨 텍스트를 설정합니다.

**Parameters:**
- `text` (string): 체크박스 라벨

**Returns:** `CrmPreview` (체이닝 가능)

##### `onVariableCheckboxChange(callback)`
변수 변환 체크박스 변경 시 실행할 콜백을 등록합니다.

**Parameters:**
- `callback` (Function): 콜백 함수 (isChecked 인자를 받음)

**Returns:** `CrmPreview` (체이닝 가능)

```javascript
crmPreview.onVariableCheckboxChange(function(isChecked) {
    console.log('변수 모드:', isChecked);
});
```

##### `setCheckboxChecked(checked)`
체크박스 체크 상태를 설정합니다.

**Parameters:**
- `checked` (boolean): 체크 여부

**Returns:** `CrmPreview` (체이닝 가능)

##### `isCheckboxChecked()`
체크박스 체크 상태를 반환합니다.

**Returns:** `boolean`

#### 친구톡 전용 메서드

##### `setMessageType(type)`
친구톡의 메시지 타입을 설정합니다.

**Parameters:**
- `type` (string): 'TEXT' | 'IMAGE' | 'WIDE_IMAGE' | 'WIDE_ITEM_LIST' | 'CAROUSEL_FEED'

**Returns:** `CrmPreview` (체이닝 가능)

##### `getMessageType()`
현재 메시지 타입을 반환합니다.

**Returns:** `string | null` - 현재 메시지 타입, 미설정 시 `null`

##### `setContentTitle(value)`
내용 제목을 설정합니다.

**Parameters:**
- `value` (string): 내용 제목

**Returns:** `CrmPreview` (체이닝 가능)

##### `setCoupon(coupon)`
쿠폰을 설정합니다.

**Parameters:**
- `coupon` (Object | null): 쿠폰 객체 또는 null (제거)
  - `text` (string): 쿠폰명
  - `date` (string): 사용기한

**Returns:** `CrmPreview` (체이닝 가능)

##### `setItemList(items)`
아이템 리스트를 설정합니다. (WIDE_ITEM_LIST 타입용)

**Parameters:**
- `items` (Array): 아이템 객체 배열
  - `image` (string): 아이템 이미지 URL
  - `title` (string): 아이템 제목
  - `desc` (string): 아이템 설명

**Returns:** `CrmPreview` (체이닝 가능)

##### `addSlide()`
캐러셀에 슬라이드를 추가합니다.

**Returns:** `CrmPreview` (체이닝 가능)

##### `removeSlide(index)`
캐러셀의 특정 슬라이드를 삭제합니다.

**Parameters:**
- `index` (number): 삭제할 슬라이드 인덱스

**Returns:** `CrmPreview` (체이닝 가능)

##### `setSlideIndex(index)`
캐러셀의 현재 슬라이드를 변경합니다.

**Parameters:**
- `index` (number): 이동할 슬라이드 인덱스

**Returns:** `CrmPreview` (체이닝 가능)

##### `getCurrentSlideIndex()`
현재 슬라이드 인덱스를 반환합니다.

**Returns:** `number`

##### `getSlideCount()`
전체 슬라이드 개수를 반환합니다.

**Returns:** `number`

#### 알림톡 전용 메서드

##### `setEmTitle(value)`
강조 제목을 설정합니다.

**Parameters:**
- `value` (string): 강조 제목

**Returns:** `CrmPreview` (체이닝 가능)

##### `setEmSubTitle(value)`
강조 부제목을 설정합니다.

**Parameters:**
- `value` (string): 강조 부제목

**Returns:** `CrmPreview` (체이닝 가능)

##### `setListHeader(value)`
리스트 헤더를 설정합니다.

**Parameters:**
- `value` (string): 리스트 헤더

**Returns:** `CrmPreview` (체이닝 가능)

##### `setHighlightTitle(value)`
하이라이트 제목을 설정합니다.

**Parameters:**
- `value` (string): 하이라이트 제목

**Returns:** `CrmPreview` (체이닝 가능)

##### `setHighlightDesc(value)`
하이라이트 설명을 설정합니다.

**Parameters:**
- `value` (string): 하이라이트 설명

**Returns:** `CrmPreview` (체이닝 가능)

##### `setHighlightImage(src)`
하이라이트 이미지를 설정합니다.

**Parameters:**
- `src` (string): 이미지 URL

**Returns:** `CrmPreview` (체이닝 가능)

##### `setExtraInfo(value)`
추가 정보를 설정합니다.

**Parameters:**
- `value` (string): 추가 정보

**Returns:** `CrmPreview` (체이닝 가능)

##### `setChannelMessage(value)`
채널 메시지를 설정합니다.

**Parameters:**
- `value` (string): 채널 메시지

**Returns:** `CrmPreview` (체이닝 가능)

##### `setItemList(items)`
아이템 리스트를 설정합니다. (테이블 형식)

**Parameters:**
- `items` (Array): 아이템 객체 배열
  - `title` (string): 항목명
  - `desc` (string): 항목 값
  - `isSummary` (boolean): 요약 행 여부

**Returns:** `CrmPreview` (체이닝 가능)

#### 마이앱 전용 메서드

##### `setWithdrawalMethod(value)`
수신거부 방법을 설정합니다.

**Parameters:**
- `value` (string): 수신거부 방법 텍스트

**Returns:** `CrmPreview` (체이닝 가능)

##### `setShowAdLabel(show)`
마이앱 제목에 (광고) 라벨 표시 여부를 설정합니다.

**Parameters:**
- `show` (boolean): 표시 여부

**Returns:** `CrmPreview` (체이닝 가능)

---

## GodoUIModule 통합

CRM Preview는 GodoUIModule과 완벽히 통합되어 있습니다.

### 사용 방법

```javascript
// GodoUIModule을 통한 초기화
const crmPreview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '.preview-container',
    messageType: 'SMS',  // 대문자로 지정
    noticeText: '미리보기와 실제가 다를 수 있습니다.',
    checkboxText: '변수로 변환해서 보기',
    onVariableToggle: function(isChecked) {
        console.log('변수 모드:', isChecked);
    }
});

// 이후 CrmPreview 메서드 직접 사용
crmPreview
    .setSendType('FRIENDTALK')
    .setMessageType('IMAGE')
    .render()
    .setTitle('신상품 안내')
    .setContent('확인하세요!');
```

**참고:**
- `GodoUIModule.render()`는 초기 선언만 담당하고, 실제 로직은 `CrmPreview`에서 처리합니다
- `messageType`은 대문자로 지정해야 합니다 (예: 'SMS', 'FRIENDTALK')
- 반환된 객체는 `CrmPreview` 인스턴스이므로 모든 메서드를 직접 사용할 수 있습니다

---

## 아키텍처

### 디자인 패턴

#### 1. Facade Pattern (CrmPreview)
- 복잡한 State 로직을 감추고 단순한 인터페이스 제공
- 모든 메서드를 통합 관리
- 메서드 체이닝 지원

#### 2. State Pattern (각 State 클래스)
- 발송 타입별로 독립적인 State 클래스
- 각 State가 자신의 렌더링 로직과 데이터 관리 담당
- 타입 변경 시 State 객체만 교체

#### 3. Adapter Pattern (GodoUIModule)
- 기존 UI 모듈 패턴을 유지하면서 CrmPreview 기능 통합
- 초기 선언만 담당하고 실제 로직은 CrmPreview에 위임

### 파일 구조

```
godo-ui-module/
├── godo-ui-module.js         # GodoUIModule (MessagePreview 어댑터 포함)
└── crm-preview/
    ├── constants.js           # 상수 정의 (발송 타입, 메시지 타입)
    ├── crm-preview.js         # Facade 클래스
    ├── crm-preview-loader.js  # 자동 로더
    ├── crm-preview.md         # 문서 (현재 파일)
    ├── test.html              # 테스트 페이지
    └── states/
        ├── sms-state.js       # SMS State
        ├── friendtalk-state.js # 친구톡 State
        ├── alimtalk-state.js  # 알림톡 State
        └── myapp-state.js     # 마이앱 State
```

### 데이터 흐름

```
사용자 입력
    ↓
GodoUIModule (선택적)
    ↓
CrmPreview (Facade)
    ↓
현재 State 객체
    ↓
DOM 생성/업데이트
    ↓
미리보기 화면
```

### 각 State의 reset() 메서드

모든 State 클래스는 `reset()` 메서드를 구현하여 자신의 UI를 초기화합니다:

```javascript
// SmsState
reset() {
    this.setTitle('');
    this.setContent('');
}

// FriendtalkState
reset() {
    this.setTitle('');
    this.setContentTitle('');
    this.setContent('');
    this.setImage('');
    this.setButtons([]);
    this.setCoupon(null);
    this.setItemList([]);
}

// AlimtalkState
reset() {
    this.setTitle('');
    this.setContent('');
    this.setImage('');
    this.setButtons([]);
    this.setEmTitle('');
    this.setEmSubTitle('');
    this.setListHeader('');
    this.setHighlightTitle('');
    this.setHighlightDesc('');
    this.setHighlightImage('');
    this.setExtraInfo('');
    this.setChannelMessage('');
    this.setItemList([]);
}

// MyappState
reset() {
    this.setTitle('');
    this.setContent('');
    this.setImage('');
    this.setWithdrawalMethod('');
}
```

---

## 중요 사항

### ⚠️ 주의사항

1. **Container 필수**: 인스턴스 생성 시 반드시 `container` 옵션을 제공해야 합니다.

2. **render() 호출**: 
   - `setSendType()` 또는 `setMessageType()` 후에는 반드시 `render()`를 호출해야 합니다.
   - 다른 메서드(`setTitle`, `setContent` 등)는 `render()` 없이도 즉시 반영됩니다.

3. **캐러셀 사용 시 jQuery 필수**:
   - CAROUSEL_FEED 타입은 slick.js를 사용하므로 jQuery가 필요합니다.
   - jQuery와 slick.js를 먼저 로드해야 합니다.

4. **슬라이드 최소 개수**:
   - 캐러셀은 최소 1개 이상의 슬라이드가 있어야 합니다.

5. **메서드 체이닝**:
   - 대부분의 메서드가 `this`를 반환하므로 체이닝이 가능합니다.
   - 단, getter 메서드(`getSlideCount`, `getCurrentSlideIndex`, `isCheckboxChecked`)는 값을 반환합니다.

6. **이미지 hidden 처리**:
   - 알림톡과 마이앱의 이미지는 값이 없을 때 `display: none`으로 처리되어 UI가 깨지지 않습니다.

7. **버튼 type 속성**:
   - 알림톡에서 `type: 'add-channel'` 속성을 가진 버튼은 특별한 스타일이 적용됩니다.
   - 인덱스 기반이 아닌 명시적 속성으로 버튼 타입을 구분합니다.

8. **GodoUIModule 사용 시**:
   - `messageType`은 대문자로 지정해야 합니다 (예: 'SMS', 'FRIENDTALK')
   - 초기 선언 후 반환된 객체로 CrmPreview 메서드를 직접 사용할 수 있습니다

### 💡 Best Practices

#### 1. 초기화 시 타입 설정과 렌더링을 함께 수행

```javascript
// Good
crmPreview.setSendType('FRIENDTALK').render();

// Bad
crmPreview.setSendType('FRIENDTALK');
// render() 호출 안 함 - 미리보기가 생성되지 않음
```

#### 2. 캐러셀 슬라이드 관리

```javascript
// Good - 슬라이드 이동 후 내용 업데이트
crmPreview.setSlideIndex(0);
crmPreview.setTitle('슬라이드 1');

// Bad - 잘못된 슬라이드에 내용 업데이트
crmPreview.setTitle('슬라이드 1');
crmPreview.setSlideIndex(0);  // 내용이 이미 다른 슬라이드에 적용됨
```

#### 3. reset() 메서드 활용

```javascript
// Good - 타입 변경 시 reset() 사용
crmPreview
    .reset()  // 이전 데이터 모두 정리
    .setSendType('SMS')
    .render();

// Bad - 이전 데이터가 남아있을 수 있음
crmPreview
    .setSendType('SMS')
    .render();
```

#### 4. GodoUIModule과 함께 사용

```javascript
// Good - GodoUIModule로 초기화 후 직접 메서드 사용
const crmPreview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '.preview-container',
    messageType: 'SMS'
});
crmPreview.setContent('메시지 내용');

// Bad - window 객체를 통한 접근 (전역 오염)
window.crmPreview = GodoUIModule.render({...});
```

### 🐛 트러블슈팅

#### 미리보기가 표시되지 않음
- `render()` 메서드를 호출했는지 확인
- Container가 DOM에 존재하는지 확인
- 브라우저 콘솔에서 에러 확인

#### 캐러셀이 작동하지 않음
- jQuery가 로드되었는지 확인
- slick.js가 로드되었는지 확인
- 최소 1개 이상의 슬라이드가 있는지 확인

#### 버튼/쿠폰이 표시되지 않음
- 해당 기능을 지원하는 타입인지 확인 (SMS는 미지원)
- 데이터가 올바르게 전달되었는지 확인

#### 알림톡 이미지가 깨져서 보임
- 이미지는 자동으로 `display: none` 처리되므로 정상 동작입니다
- 이미지 URL이 올바른지 확인하세요

#### GodoUIModule 통합 시 에러
- `messageType`을 대문자로 지정했는지 확인
- CrmPreview가 전역으로 노출되어 있는지 확인 (`window.CrmPreview`)

---

## 예제 코드

### 완전한 통합 예제 (GodoUIModule 사용)

```javascript
// 1. GodoUIModule로 인스턴스 생성
const crmPreview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '.preview-container',
    messageType: 'SMS',
    noticeText: '미리보기와 실제가 다를 수 있습니다.',
    checkboxText: '변수로 변환해서 보기',
    onVariableToggle: function(isChecked) {
        console.log('변수 모드:', isChecked);
    }
});

// 2. 라디오 버튼으로 발송 타입 변경
document.querySelectorAll('input[name="sendType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        crmPreview.reset().setSendType(this.value).render();
    });
});

// 3. 친구톡 메시지 타입 변경
document.querySelectorAll('input[name="messageType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        crmPreview.setMessageType(this.value).render();
    });
});

// 4. 입력 필드 연동
document.getElementById('titleInput').addEventListener('input', function() {
    crmPreview.setTitle(this.value);
});

document.getElementById('contentTitleInput').addEventListener('input', function() {
    crmPreview.setContentTitle(this.value);
});

document.getElementById('contentInput').addEventListener('input', function() {
    crmPreview.setContent(this.value);
});

document.getElementById('imageInput').addEventListener('input', function() {
    crmPreview.setImage(this.value);
});

// 5. 알림톡 전용 필드
document.getElementById('listHeaderInput').addEventListener('input', function() {
    crmPreview.setListHeader(this.value);
});

document.getElementById('highlightTitleInput').addEventListener('input', function() {
    crmPreview.setHighlightTitle(this.value);
});

// 6. 마이앱 전용 필드
document.getElementById('withdrawalMethodInput').addEventListener('input', function() {
    crmPreview.setWithdrawalMethod(this.value);
});

// 7. 버튼 관리
const buttons = [
    { text: '채널 추가', type: 'add-channel' },
    { text: '상품 보기', url: 'https://example.com/product' }
];
crmPreview.setButtons(buttons);

// 8. 초기화
document.getElementById('resetBtn').addEventListener('click', function() {
    crmPreview.reset();
});
```

