# GodoUIModule Guide

## 📋 목차
1. [개요](#개요)
2. [주요 기능](#주요-기능)
3. [설치 및 로드](#설치-및-로드)
4. [기본 사용법](#기본-사용법)
5. [지원 UI 모듈](#지원-UI 모듈)
6. [ChipSelector](#chipselector)
7. [MessageInput](#messageinput)
8. [MessagePreview (CrmPreview)](#messagepreview-crmpreview)
9. [통합 예제](#통합-예제)
10. [아키텍처](#아키텍처)
11. [문제 해결](#문제-해결)

---

## 개요

**GodoUIModule**은 CRM 메시지 발송 화면에서 사용되는 UI 모듈들을 통합 관리하는 모듈입니다. 어댑터/파사드 패턴을 사용하여 여러 UI 모듈을 일관된 인터페이스로 생성하고 관리할 수 있습니다.

### 특징
- ✅ 일관된 API로 여러 UI 모듈 사용
- ✅ UI 모듈 간 의존성 관리
- ✅ 모듈화된 구조로 유지보수 용이
- ✅ 각 UI 모듈의 인스턴스를 직접 반환하여 완전한 API 접근 가능

### 아키텍처 패턴
- **Adapter Pattern**: 각 UI 모듈의 클래스를 감싸서 일관된 인터페이스 제공
- **Factory Pattern**: `type` 옵션으로 적절한 UI 모듈 생성
- **Module Pattern**: IIFE로 private/public API 구분

---

## 주요 기능

### 1. 통합 렌더링 인터페이스
모든 UI 모듈을 동일한 방식으로 생성:

```javascript
GodoUIModule.render({
    type: 'ComponentType',
    // ... UI 모듈별 옵션
});
```

### 2. 지원 UI 모듈
- **ChipSelector**: 카테고리별 변수 선택 UI
- **MessageInput**: 메시지 입력 및 문자/바이트 카운팅
- **MessagePreview**: SMS/카카오톡 메시지 미리보기

### 3. UI 모듈 간 연동
각 UI 모듈은 독립적으로 동작하지만, 이벤트 콜백을 통해 유기적으로 연결 가능:

```
ChipSelector → MessageInput → MessagePreview
   (변수 선택)    (메시지 작성)    (실시간 미리보기)
```

---

## 설치 및 로드

### 파일 구조

```
godo-ui-module/
├── godo-ui-module.js              # 통합 모듈 (어댑터)
├── godo-ui-module.md              # 이 가이드
├── chip-selector/
│   ├── chip-selector.js           # ChipSelector UI 모듈
│   ├── chip-selector.md           # ChipSelector 가이드
│   └── test.html
├── message-input/
│   ├── message-input.js           # MessageInput UI 모듈
│   ├── message-input.md           # MessageInput 가이드
│   ├── test.html
│   └── integration-test.html
└── crm-preview/
    ├── crm-preview.js             # CrmPreview UI 모듈
    ├── crm-preview.md             # CrmPreview 가이드
    ├── crm-preview-loader.js
    └── test.html
```

### HTML에서 로드

```html
<!-- 각 UI 모듈 클래스 로드 -->
<script src="path/to/chip-selector/chip-selector.js"></script>
<script src="path/to/message-input/message-input.js"></script>
<script src="path/to/crm-preview/crm-preview-loader.js"></script>

<!-- GodoUIModule 로드 (마지막에) -->
<script src="path/to/godo-ui-module.js"></script>
```

### 주의사항
- GodoUIModule은 각 UI 모듈 클래스(`ChipSelector`, `MessageInput`, `CrmPreview`)가 이미 로드되어 있어야 합니다.
- UI 모듈들을 먼저 로드하고, 그 다음에 GodoUIModule을 로드해야 합니다.

---

## 기본 사용법

### 단일 UI 모듈 사용

```javascript
// ChipSelector 생성
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-container',
    title: '치환코드',
    categories: [...]
});

// MessageInput 생성
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input-container',
    maxLength: 2000,
    useBytes: true
});

// MessagePreview 생성
const preview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '#preview-container',
    sendType: 'SMS'
});
```

### UI 모듈 API 사용

`GodoUIModule.render()`는 각 UI 모듈의 인스턴스를 그대로 반환하므로, 모든 Public API를 사용할 수 있습니다:

```javascript
// ChipSelector API
chipSelector.setCategories(newCategories);
chipSelector.getSelectedCategory();

// MessageInput API
messageInput.setValue('안녕하세요');
messageInput.getValue();
messageInput.insertText('#{회원명}');

// MessagePreview API
preview.setContent('메시지 내용');
preview.setSendType('FRIENDTALK');
```

---

## 지원 UI 모듈

### UI 모듈 타입

| 타입 | UI 모듈 | 설명 |
|------|---------|------|
| `ChipSelector` | ChipSelector | 카테고리별 변수 칩 버튼 선택기 |
| `MessageInput` | MessageInput | 메시지 입력 textarea (문자/바이트 카운팅) |
| `MessagePreview` | CrmPreview | SMS/카카오톡 메시지 미리보기 |

---

## ChipSelector

### 옵션

```javascript
GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container',           // 필수: 렌더링 대상
    title: '치환코드',              // 제목 (기본: '치환코드')
    tooltipSeq: '003',              // 툴팁 시퀀스 (optional)
    cautionHTML: '<ul>...</ul>',    // 안내 텍스트 HTML (optional)
    categories: [],                 // 카테고리 배열
    onSelect: (variable, button) => {} // 칩 선택 시 콜백
});
```

### 주요 API

| 메서드 | 설명 | 반환 |
|--------|------|------|
| `setCategories(categories)` | 카테고리 목록 설정 | `this` |
| `getSelectedCategory()` | 현재 선택된 카테고리 반환 | `string` |
| `onSelect(callback)` | 선택 콜백 설정 | `this` |
| `setCautionHTML(html)` | 안내 텍스트 설정 | `this` |
| `refresh()` | 칩 버튼 새로고침 | `this` |
| `render()` | 전체 재렌더링 | `this` |
| `destroy()` | UI 모듈 파괴 | - |

### 사용 예시

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    title: '사용 가능한 변수',
    cautionHTML: `
        <ul class="replace-code-caution">
            <li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않습니다.</li>
        </ul>
    `,
    categories: [
        {
            value: 'member',
            label: '회원정보',
            variables: [
                { key: '#{회원명}', label: '회원이름' },
                { key: '#{이메일}', label: '이메일' }
            ]
        }
    ],
    onSelect: (variable) => {
        console.log('선택:', variable);
    }
});

// API 사용
chipSelector.getSelectedCategory(); // "member"
chipSelector.setCautionHTML('<p>새로운 안내</p>');
```

📖 **자세한 정보**: [ChipSelector 가이드](./chip-selector/chip-selector.md)

---

## MessageInput

### 옵션

```javascript
GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',              // 필수: 렌더링 대상
    name: 'messageContent',            // textarea name (기본: 'messageContent')
    placeholder: '입력하세요',         // placeholder
    hintText: '힌트 텍스트',           // 힌트 (optional)
    maxLength: 2000,                   // 최대 길이 (기본: 2000)
    useBytes: true,                    // bytes 모드 (기본: false)
    bytesThreshold: 90,                // bytes 임계값 (기본: 90)
    allowEmoji: true,                  // 이모티콘 허용 여부 (기본: true) (optional)
    initialValue: '',                  // 초기 값 (optional)
    onInput: (value, event) => {},     // input 이벤트 콜백
    onChange: (value, event) => {}     // change 이벤트 콜백
});
```

### 주요 API

| 메서드 | 설명 | 반환 |
|--------|------|------|
| `getValue()` | 현재 값 반환 | `string` |
| `setValue(value)` | 값 설정 | `this` |
| `insertText(text)` | 커서 위치에 텍스트 삽입 | `this` |
| `clear()` | 내용 초기화 | `this` |
| `setHint(text)` | 힌트 텍스트 설정 | `this` |
| `setPlaceholder(text)` | Placeholder 설정 | `this` |
| `setMaxLength(length)` | 최대 길이 설정 | `this` |
| `enableBytesMode(options)` | Bytes 모드 활성화 | `this` |
| `enableCharMode(options)` | Char 모드 활성화 | `this` |
| `focus()` | 포커스 | `this` |
| `getCursorPosition()` | 커서 위치 반환 | `{start, end}` |
| `destroy()` | UI 모듈 파괴 | - |

### 사용 예시

```javascript
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    placeholder: '메시지를 입력하세요',
    hintText: 'SMS는 90 bytes 이하 권장',
    maxLength: 2000,
    useBytes: true,
    bytesThreshold: 90,
    onInput: (value) => {
        console.log('입력:', value);
    }
});

// API 사용
messageInput.insertText('#{회원명}');
messageInput.getValue(); // 현재 값 조회
messageInput.enableBytesMode({ maxLength: 2000, bytesThreshold: 90 });
```

📖 **자세한 정보**: [MessageInput 가이드](./message-input/message-input.md)

---

## MessagePreview (CrmPreview)

### 옵션

```javascript
GodoUIModule.render({
    type: 'MessagePreview',
    target: '#container',              // 필수: 렌더링 대상
    sendType: 'SMS',                   // 발송 타입 (SMS, FRIENDTALK, ALIMTALK, MYAPP)
    messageType: 'TEXT',               // 친구톡 메시지 타입 (optional, FRIENDTALK일 때만 사용)
    noticeText: '안내 텍스트',         // Notice 텍스트 (optional)
    checkboxText: '변수로 변환',       // Checkbox 라벨 (optional)
    data: {                            // 초기 데이터 (optional)
        title: '제목',
        content: '내용',
        image: 'url',
        buttons: [],
        coupon: {},
        itemList: []
    },
    onVariableToggle: (checked) => {}  // 변수 토글 콜백
});
```

### 주요 API

| 메서드 | 설명 | 반환 |
|--------|------|------|
| `setSendType(type)` | 발송 유형 설정 | `this` |
| `setTitle(title)` | 제목 설정 | `this` |
| `setContent(content)` | 내용 설정 | `this` |
| `setImage(imageUrl)` | 이미지 URL 설정 | `this` |
| `setButtons(buttons)` | 버튼 목록 설정 | `this` |
| `setCoupon(coupon)` | 쿠폰 정보 설정 | `this` |
| `setItemList(items)` | 아이템 리스트 설정 | `this` |
| `render()` | 재렌더링 | `this` |
| `destroy()` | UI 모듈 파괴 | - |

### 사용 예시

```javascript
const preview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '#preview',
    sendType: 'SMS',
    noticeText: '미리보기와 실제가 다를 수 있습니다.',
    checkboxText: '변수로 변환해서 보기'
});

// API 사용
preview.setContent('안녕하세요 #{회원명}님!');
preview.setSendType('FRIENDTALK');
preview.setTitle('프로모션 안내');
```

📖 **자세한 정보**: [CrmPreview 가이드](./crm-preview/crm-preview.md)

---

### 메서드 체이닝

모든 UI 모듈은 메서드 체이닝을 지원합니다:

```javascript
// ChipSelector 체이닝
chipSelector
    .setCategories(newCategories)
    .onSelect((variable) => console.log(variable))
    .refresh();

// MessageInput 체이닝
messageInput
    .setValue('안녕하세요')
    .insertText(' #{회원명}님!')
    .focus();

// CrmPreview 체이닝
crmPreview
    .setSendType('FRIENDTALK')
    .setTitle('프로모션 안내')
    .setContent('특별 할인 행사!')
    .render();
```

---

## 아키텍처

### 모듈 구조

```
GodoUIModule (IIFE)
├── renderChipSelector()      - ChipSelector 어댑터
├── renderMessageInput()       - MessageInput 어댑터
├── renderMessagePreview()     - CrmPreview 어댑터
└── render()                   - 메인 팩토리 함수
```

### 어댑터 패턴

각 UI 모듈은 독립적인 클래스로 구현되어 있으며, GodoUIModule은 이들을 감싸는 어댑터 역할:

```javascript
// GodoUIModule이 하는 일:
function renderChipSelector(config) {
    // 1. config 검증
    if (!config.target) return null;
    
    // 2. ChipSelector 인스턴스 생성
    const chipSelector = new ChipSelector({
        container: config.target,
        title: config.title,
        // ... 옵션 매핑
    });
    
    // 3. 인스턴스 그대로 반환 (래핑하지 않음)
    return chipSelector;
}
```

### 팩토리 패턴

`render()` 함수는 `type`에 따라 적절한 UI 모듈을 생성:

```javascript
function render(config) {
    switch (config.type) {
        case 'ChipSelector':
            return renderChipSelector(config);
        case 'MessageInput':
            return renderMessageInput(config);
        case 'MessagePreview':
            return renderMessagePreview(config);
        default:
            console.error('Unknown type');
            return null;
    }
}
```

### 의존성 관리

```
GodoUIModule
    ↓ depends on
ChipSelector, MessageInput, CrmPreview (각각 독립적인 클래스)
```

**중요**: GodoUIModule을 로드하기 전에 각 UI 모듈 클래스가 먼저 로드되어야 합니다.

---

## 고급 사용법

### 1. 동적 UI 모듈 생성

```javascript
function createComponent(type, targetId, options = {}) {
    return GodoUIModule.render({
        type: type,
        target: `#${targetId}`,
        ...options
    });
}

// 사용
const chipSelector = createComponent('ChipSelector', 'chip-container', {
    title: '변수',
    categories: [...]
});
```

### 2. UI 모듈 재사용

```javascript
const components = {};

// UI 모듈 생성 및 저장
components.chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip',
    categories: [...]
});

components.messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input',
    maxLength: 2000
});

// 나중에 참조
components.chipSelector.setCategories(newCategories);
components.messageInput.setValue('새 메시지');
```

### 3. 조건부 UI 모듈 로딩

```javascript
function initComponents(userType) {
    const components = {};
    
    // 기본 UI 모듈
    components.messageInput = GodoUIModule.render({
        type: 'MessageInput',
        target: '#input',
        maxLength: 2000
    });
    
    // VIP 사용자만 치환코드 사용 가능
    if (userType === 'VIP') {
        components.chipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#chip',
            categories: vipCategories
        });
    }
    
    // 미리보기는 모두 제공
    components.preview = GodoUIModule.render({
        type: 'MessagePreview',
        target: '#preview',
        sendType: 'SMS'
    });
    
    return components;
}
```

### 4. 이벤트 연결 헬퍼

```javascript
function connectComponents(chipSelector, messageInput, preview) {
    // ChipSelector → MessageInput
    chipSelector.onSelect((variable) => {
        messageInput.insertText(variable);
    });
    
    // MessageInput → Preview
    const originalOnInput = messageInput.onInput;
    messageInput.onInput = (value, event) => {
        preview.setContent(value);
        if (originalOnInput) originalOnInput(value, event);
    };
}

// 사용
const chip = GodoUIModule.render({ type: 'ChipSelector', ... });
const input = GodoUIModule.render({ type: 'MessageInput', ... });
const preview = GodoUIModule.render({ type: 'MessagePreview', ... });

connectComponents(chip, input, preview);
```

---

## 문제 해결

### Q1: "ChipSelector is not defined" 오류

**원인**: GodoUIModule보다 UI 모듈 클래스가 먼저 로드되지 않았습니다.

**해결**:
```html
<!-- 올바른 순서 -->
<script src="chip-selector/chip-selector.js"></script>
<script src="message-input/message-input.js"></script>
<script src="crm-preview/crm-preview-loader.js"></script>
<script src="godo-ui-module.js"></script> <!-- 마지막 -->
```

### Q2: render()가 null을 반환합니다

**원인 1**: `type` 또는 `target` 옵션이 누락되었습니다.

```javascript
// ❌ 잘못됨
GodoUIModule.render({
    // type 없음!
    target: '#container'
});

// ✅ 올바름
GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container',
    categories: [...]
});
```

**원인 2**: 올바르지 않은 `type`을 사용했습니다.

```javascript
// ❌ 잘못됨
GodoUIModule.render({
    type: 'ChipSelect',  // 오타!
    target: '#container'
});

// ✅ 올바름 (대소문자 구분)
GodoUIModule.render({
    type: 'ChipSelector',  // 정확한 이름
    target: '#container'
});
```

### Q3: UI 모듈 메서드를 호출할 수 없습니다

**원인**: render()의 반환값을 저장하지 않았습니다.

```javascript
// ❌ 잘못됨
GodoUIModule.render({
    type: 'MessageInput',
    target: '#input'
});
// 인스턴스를 저장하지 않아서 나중에 사용 불가!

// ✅ 올바름
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input'
});

// 이제 API 사용 가능
messageInput.setValue('Hello');
```

### Q4: CrmPreview가 작동하지 않습니다

**원인**: `crm-preview-loader.js`의 비동기 로딩 때문에 CrmPreview가 아직 준비되지 않았습니다.

**해결**:
```javascript
// crmPreviewLoaded 이벤트 대기
window.addEventListener('crmPreviewLoaded', function() {
    const preview = GodoUIModule.render({
        type: 'MessagePreview',
        target: '#preview',
        sendType: 'SMS'
    });
});
```

### Q5: 여러 UI 모듈을 동시에 사용할 수 있나요?

**A**: 네! 각 UI 모듈은 독립적으로 동작하며, 원하는 만큼 생성 가능합니다.

```javascript
// 같은 페이지에 여러 MessageInput
const input1 = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input1',
    maxLength: 1000
});

const input2 = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input2',
    maxLength: 2000
});

// 완전히 독립적으로 동작
input1.setValue('메시지 1');
input2.setValue('메시지 2');
```

---

## 모범 사례

### 1. UI 모듈 인스턴스 관리

```javascript
// ✅ Good: 객체로 관리
const ui = {
    chipSelector: null,
    messageInput: null,
    preview: null
};

function initUI() {
    ui.chipSelector = GodoUIModule.render({...});
    ui.messageInput = GodoUIModule.render({...});
    ui.preview = GodoUIModule.render({...});
}

function updateMessage(text) {
    ui.messageInput.setValue(text);
    ui.preview.setContent(text);
}
```

### 2. 에러 처리

```javascript
function safeRender(config) {
    try {
        const component = GodoUIModule.render(config);
        
        if (!component) {
            console.error('UI 모듈 생성 실패:', config);
            return null;
        }
        
        return component;
    } catch (error) {
        console.error('UI 모듈 생성 중 오류:', error);
        return null;
    }
}
```

### 3. 초기화 함수 패턴

```javascript
function initMessageUI(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Container not found:', containerId);
        return null;
    }
    
    // 모든 UI 모듈 생성
    const components = {
        preview: GodoUIModule.render({
            type: 'MessagePreview',
            target: `#${containerId}-preview`,
            sendType: 'SMS'
        }),
        
        input: GodoUIModule.render({
            type: 'MessageInput',
            target: `#${containerId}-input`,
            maxLength: 2000,
            onInput: (value) => {
                components.preview.setContent(value);
            }
        }),
        
        chipSelector: GodoUIModule.render({
            type: 'ChipSelector',
            target: `#${containerId}-chip`,
            categories: getCategories(),
            onSelect: (variable) => {
                components.input.insertText(variable);
            }
        })
    };
    
    return components;
}

// 사용
const messageUI = initMessageUI('message-container');
```

---

## 성능 최적화

### 1. 필요한 UI 모듈만 로드

```javascript
// 미리보기가 필요 없으면 생성하지 않음
function createBasicMessageInput(target) {
    return GodoUIModule.render({
        type: 'MessageInput',
        target: target,
        maxLength: 2000
    });
}
```

### 2. 이벤트 디바운싱

```javascript
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input',
    onInput: debounce((value) => {
        // 300ms 후에 실행
        preview.setContent(value);
    }, 300)
});
```

### 3. UI 모듈 재사용

```javascript
// ❌ Bad: 매번 새로 생성
function updateCategories(categories) {
    const chipSelector = GodoUIModule.render({...});
    chipSelector.setCategories(categories);
}

// ✅ Good: 한 번 생성하고 재사용
const chipSelector = GodoUIModule.render({...});

function updateCategories(categories) {
    chipSelector.setCategories(categories);
}
```

---

## 관련 문서

- [ChipSelector 가이드](./chip-selector/chip-selector.md)
- [MessageInput 가이드](./message-input/message-input.md)
- [CrmPreview 가이드](./crm-preview/crm-preview.md)

---

## FAQ

### Q1: GodoUIModule 없이 UI 모듈을 직접 사용할 수 있나요?

**A**: 네! 각 UI 모듈은 독립적인 클래스이므로 직접 사용 가능합니다.

```javascript
// GodoUIModule 사용
const input1 = GodoUIModule.render({
    type: 'MessageInput',
    target: '#input1'
});

// 직접 사용
const input2 = new MessageInput({
    container: '#input2'
});

// 둘 다 동일한 인스턴스 타입
```

### Q2: 왜 GodoUIModule을 사용해야 하나요?

**A**: 
- 일관된 API로 여러 UI 모듈 관리
- 팩토리 패턴으로 동적 UI 모듈 생성 용이
- 옵션 이름 통일 (target vs container)
- 향후 기능 확장 시 호환성 유지

### Q3: 새로운 UI 모듈을 추가하려면?

**A**: 
1. 새 UI 모듈 클래스 작성
2. GodoUIModule에 어댑터 함수 추가
3. `render()` switch문에 case 추가

```javascript
// 새 UI 모듈 어댑터
function renderNewComponent(config) {
    return new NewComponent({
        container: config.target,
        // ... 옵션 매핑
    });
}

// render()에 추가
function render(config) {
    switch (config.type) {
        // ... 기존 케이스들
        case 'NewComponent':
            return renderNewComponent(config);
    }
}
```
