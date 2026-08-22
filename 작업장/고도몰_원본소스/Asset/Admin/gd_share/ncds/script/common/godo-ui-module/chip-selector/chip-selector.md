# ChipSelector Component Guide

## 📋 목차
1. [개요](#개요)
2. [주요 기능](#주요-기능)
3. [설치 및 로드](#설치-및-로드)
4. [기본 사용법](#기본-사용법)
5. [생성 옵션](#생성-옵션)
6. [Public API](#public-api)
7. [실전 예제](#실전-예제)
8. [MessageInput 연동](#messageinput-연동)
9. [카테고리 데이터 구조](#카테고리-데이터-구조)
10. [주의사항](#주의사항)

---

## 개요

**ChipSelector**는 카테고리별로 변수를 선택할 수 있는 UI 모듈입니다. CRM 메시지 발송 화면에서 메시지 내용에 치환코드(변수)를 쉽게 삽입할 수 있도록 도와줍니다.

### 특징
- ✅ 카테고리별 변수 분류
- ✅ 드롭다운으로 카테고리 선택
- ✅ 칩 버튼 형태의 직관적인 UI
- ✅ 콜백을 통한 유연한 이벤트 처리
- ✅ XSS 방지 (HTML escape)
- ✅ 메서드 체이닝 지원

### UI 구조
```
┌─────────────────────────────────────┐
│  치환코드 (제목)                     │
├─────────────────────────────────────┤
│  [회원정보 ▼] ← 카테고리 선택       │
├─────────────────────────────────────┤
│  [#{회원명} 회원이름]  [#{이메일}]   │ ← 칩 버튼들
│  [#{휴대폰번호}]  [#{등급명}]         │
└─────────────────────────────────────┘
```

---

## 주요 기능

### 1. **카테고리 선택**
드롭다운 메뉴에서 카테고리를 선택하면 해당 카테고리의 변수들이 표시됩니다.

```
[회원정보 ▼]  →  [주문정보 ▼]
  ↓                ↓
회원명, 이메일    주문번호, 주문금액
```

### 2. **변수 선택 (칩 버튼)**
칩 버튼을 클릭하면 `onSelect` 콜백이 실행되어 해당 변수를 처리할 수 있습니다.

```javascript
onSelect: (variable, button) => {
    // variable: "#{회원명}"
    // button: 클릭된 DOM 요소
    console.log(variable);
}
```

### 3. **동적 업데이트**
카테고리 목록을 동적으로 변경하거나 새로고침할 수 있습니다.

```javascript
chipSelector.setCategories(newCategories);
chipSelector.refresh();
```

---

## 설치 및 로드

### HTML에서 로드

```html
<!-- ChipSelector 클래스 직접 사용 -->
<script src="path/to/chip-selector.js"></script>

<!-- 또는 GodoUIModule 사용 (권장) -->
<script src="path/to/chip-selector.js"></script>
<script src="path/to/godo-ui-module.js"></script>
```

### CommonJS/Node.js

```javascript
const ChipSelector = require('./chip-selector.js');
```

---

## 기본 사용법

### 방법 1: GodoUIModule 사용 (권장)

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-container',
    title: '치환코드',
    categories: [
        {
            value: 'member',
            label: '회원정보',
            variables: [
                { key: '#{회원명}', label: '회원이름' },
                { key: '#{이메일}', label: '이메일' }
            ]
        },
        {
            value: 'order',
            label: '주문정보',
            variables: [
                { key: '#{주문번호}', label: '주문번호' },
                { key: '#{주문금액}', label: '주문금액' }
            ]
        }
    ],
    onSelect: (variable, button) => {
        console.log('선택된 변수:', variable);
    }
});
```

### 방법 2: 클래스 직접 사용

```javascript
const chipSelector = new ChipSelector({
    container: '#chip-container',
    title: '치환코드',
    categories: [...],
    onSelect: (variable, button) => {
        console.log('선택된 변수:', variable);
    }
});
```

---

## 생성 옵션

### Options Object

| 옵션 | 타입 | 기본값 | 설명 |
|------|------|--------|------|
| `container` | `string \| HTMLElement` | **(필수)** | 렌더링될 컨테이너 |
| `title` | `string` | `'치환코드'` | 섹션 제목 |
| `tooltipSeq` | `string` | `null` | 툴팁 시퀀스 (data-tooltip-seq 속성) |
| `cautionHTML` | `string` | `null` | 안내 텍스트 HTML |
| `categories` | `Array<Category>` | `[]` | 카테고리 배열 |
| `onSelect` | `Function` | `null` | 칩 선택 시 콜백 함수 |
| `onCategoryChange` | `Function` | `null` | 카테고리 변경 시 콜백 함수 |

### Category 객체 구조

```javascript
{
    value: 'member',        // 카테고리 식별값
    label: '회원정보',      // 화면에 표시되는 이름
    variables: [            // 변수 배열
        {
            key: '#{회원명}',    // 실제 변수 코드
            label: '회원이름'    // 변수 설명
        }
    ]
}
```

---

## Public API

### 카테고리 관리

#### `setCategories(categories)`
카테고리 목록을 설정하고 다시 렌더링합니다.

```javascript
const newCategories = [
    {
        value: 'coupon',
        label: '쿠폰정보',
        variables: [
            { key: '#{쿠폰명}', label: '쿠폰명' },
            { key: '#{할인금액}', label: '할인금액' }
        ]
    }
];

chipSelector.setCategories(newCategories);
```

**매개변수**:
- `categories` (Array<Category>): 새로운 카테고리 배열

**반환값**: `this` (체이닝 가능)

**동작**:
1. 카테고리 목록 교체
2. 첫 번째 카테고리로 자동 선택
3. 전체 UI 재렌더링

---

#### `getSelectedCategory()`
현재 선택된 카테고리의 value를 반환합니다.

```javascript
const current = chipSelector.getSelectedCategory();
console.log(current); // "member"
```

**반환값**: `string` - 현재 선택된 카테고리 값

---

### 콜백 관리

#### `onSelect(callback)`
칩 선택 시 실행될 콜백 함수를 설정합니다.

```javascript
chipSelector.onSelect((variable, button) => {
    console.log('새로운 콜백:', variable);
    insertToTextarea(variable);
});
```

**매개변수**:
- `callback` (Function): 콜백 함수
  - 첫 번째 인자: `variable` (string) - 선택된 변수 키
  - 두 번째 인자: `button` (HTMLElement) - 클릭된 버튼 요소

**반환값**: `this` (체이닝 가능)

---

#### `onCategoryChange(callback)`
카테고리 변경 시 실행될 콜백 함수를 설정합니다.

```javascript
chipSelector.onCategoryChange((categoryValue) => {
    // 특정 카테고리일 때 추가 액션
    if (categoryValue === 'order') {
        // 추가 작업 수행
    }
});
```

**매개변수**:
- `callback` (Function): 콜백 함수
  - 인자: `categoryValue` (string) - 변경된 카테고리 값

**반환값**: `this` (체이닝 가능)

**사용 시기**:
- 특정 카테고리 선택 시 추가 액션이 필요한 경우
- 카테고리 변경 이벤트를 로깅하거나 추적할 때
- 카테고리별로 다른 UI나 데이터를 표시해야 할 때

---

### 안내 텍스트 관리

#### `setCautionHTML(html)`
안내 텍스트 HTML을 설정합니다.

```javascript
const cautionHTML = `
    <ul class="replace-code-caution">
        <li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li>
        <li class="ncua-caution-text">템플릿 사용시에 미지원 치환코드가 포함된 경우, 해당 값은 공란으로 발송되므로 미리보기를 확인해주시기 바랍니다.</li>
    </ul>
`;

chipSelector.setCautionHTML(cautionHTML);
```

**매개변수**:
- `html` (string | null): 안내 텍스트 HTML. `null`을 전달하면 안내 텍스트가 제거됩니다.

**반환값**: `this` (체이닝 가능)

**동작**:
1. 안내 텍스트 HTML 설정
2. 전체 UI 재렌더링

**사용 예시**:
```javascript
// 안내 텍스트 추가
chipSelector.setCautionHTML(`
    <ul class="replace-code-caution">
        <li class="ncua-caution-text">⚠️ 중요한 안내 사항입니다.</li>
    </ul>
`);

// 안내 텍스트 제거
chipSelector.setCautionHTML(null);
```

---

### 렌더링 & 업데이트

#### `render()`
전체 UI를 다시 렌더링합니다.

```javascript
chipSelector.render();
```

**반환값**: `this` (체이닝 가능)

**동작**:
1. HTML 생성
2. 컨테이너에 삽입
3. 이벤트 재바인딩

---

#### `refresh()`
칩 버튼만 업데이트합니다 (카테고리 선택은 유지).

```javascript
chipSelector.refresh();
```

**반환값**: `this` (체이닝 가능)

**사용 시기**:
- 카테고리 목록은 유지하되 칩 버튼만 새로고침할 때
- 이벤트 리스너를 다시 바인딩할 때

---

#### `destroy()`
UI 모듈을 파괴하고 DOM을 정리합니다.

```javascript
chipSelector.destroy();
```

---

## 실전 예제

### 예제 1: 기본 치환코드 선택기

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#variable-selector',
    title: '사용 가능한 변수',
    categories: [
        {
            value: 'member',
            label: '회원정보',
            variables: [
                { key: '#{회원명}', label: '회원이름' },
                { key: '#{회원아이디}', label: '아이디' },
                { key: '#{이메일}', label: '이메일' },
                { key: '#{휴대폰번호}', label: '휴대폰' }
            ]
        },
        {
            value: 'order',
            label: '주문정보',
            variables: [
                { key: '#{주문번호}', label: '주문번호' },
                { key: '#{주문일시}', label: '주문일시' },
                { key: '#{주문금액}', label: '주문금액' }
            ]
        }
    ],
    onSelect: (variable, button) => {
        alert(`선택한 변수: ${variable}`);
    }
});
```

### 예제 1-1: 안내 텍스트 포함

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#variable-selector',
    title: '사용 가능한 변수',
    cautionHTML: `
        <ul class="replace-code-caution">
            <li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li>
            <li class="ncua-caution-text">템플릿 사용시에 미지원 치환코드가 포함된 경우, 해당 값은 공란으로 발송되므로 미리보기를 확인해주시기 바랍니다.</li>
        </ul>
    `,
    categories: [...],
    onSelect: (variable) => {
        console.log('선택된 변수:', variable);
    }
});
```

### 예제 2: Textarea에 변수 삽입

```javascript
const textarea = document.getElementById('message-content');

const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [...],
    onSelect: (variable, button) => {
        // 커서 위치에 변수 삽입
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        
        textarea.value = 
            text.slice(0, start) + 
            variable + 
            text.slice(end);
        
        // 커서를 삽입된 변수 뒤로 이동
        const newPos = start + variable.length;
        textarea.setSelectionRange(newPos, newPos);
        textarea.focus();
    }
});
```

### 예제 3: 동적 카테고리 변경

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: initialCategories,
    onSelect: handleSelect
});

// 사용자 등급에 따라 카테고리 변경
function updateCategoriesByUserGrade(grade) {
    let categories = [...basicCategories];
    
    if (grade === 'VIP') {
        categories.push({
            value: 'vip',
            label: 'VIP 전용',
            variables: [
                { key: '#{VIP등급}', label: 'VIP 등급' },
                { key: '#{적립금}', label: '적립금' }
            ]
        });
    }
    
    chipSelector.setCategories(categories);
}
```

### 예제 4: 카테고리 변경 이벤트 처리

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [
        {
            value: 'member',
            label: '회원정보',
            variables: [...]
        },
        {
            value: 'order',
            label: '주문정보',
            variables: [...]
        }
    ],
    onSelect: (variable) => {
        insertToTextarea(variable);
    },
    onCategoryChange: (categoryValue) => {
        console.log('카테고리 변경:', categoryValue);
        
        // 특정 카테고리일 때 추가 액션
        if (categoryValue === 'order') {
            // 주문 정보 관련 추가 UI 표시
            showOrderInfo();
        } else if (categoryValue === 'member') {
            // 회원 정보 관련 추가 UI 표시
            showMemberInfo();
        }
    }
});
```

### 예제 5: 메서드 체이닝

```javascript
chipSelector
    .setCategories(newCategories)
    .onSelect((variable) => console.log(variable))
    .refresh();
```

---

## MessageInput 연동

ChipSelector와 MessageInput을 함께 사용하여 변수 삽입 기능을 구현할 수 있습니다.

### 기본 연동

```javascript
// 1. MessageInput 생성
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    maxLength: 2000,
    useBytes: true
});

// 2. ChipSelector 생성 및 연동
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    title: '치환코드',
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
    onSelect: (variable, button) => {
        // MessageInput에 변수 삽입
        messageInput.insertText(variable);
    }
});
```

### 완전한 통합 (MessageInput + ChipSelector + CrmPreview)

```javascript
// 1. CrmPreview 생성
const crmPreview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '#preview',
    messageType: 'SMS'
});

// 2. MessageInput 생성 (Preview 연동)
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    maxLength: 2000,
    useBytes: true,
    onInput: (value) => {
        // 입력 시 미리보기 업데이트
        crmPreview.setContent(value);
    }
});

// 3. ChipSelector 생성 (MessageInput 연동)
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [...],
    onSelect: (variable) => {
        // 변수 삽입 → onInput 트리거 → 미리보기 자동 업데이트
        messageInput.insertText(variable);
    }
});

// 데이터 흐름:
// ChipSelector 클릭 
//   → MessageInput.insertText() 
//   → onInput 콜백 
//   → CrmPreview.setContent() 
//   → 실시간 미리보기 ✨
```

---

## 카테고리 데이터 구조

### 기본 구조

```javascript
const categories = [
    {
        value: 'member',           // 내부 식별값 (필수)
        label: '회원정보',         // 화면 표시 이름 (필수)
        variables: [               // 변수 배열 (필수)
            {
                key: '#{회원명}',      // 변수 키 (필수)
                label: '회원이름'      // 변수 설명 (필수)
            },
            {
                key: '#{이메일}',
                label: '이메일 주소'
            }
        ]
    }
];
```

### 실제 데이터 예시

```javascript
const categoriesData = [
    {
        value: 'member',
        label: '회원정보',
        variables: [
            { key: '#{회원명}', label: '회원이름' },
            { key: '#{회원아이디}', label: '아이디' },
            { key: '#{이메일}', label: '이메일' },
            { key: '#{휴대폰번호}', label: '휴대폰' },
            { key: '#{등급명}', label: '회원등급' },
            { key: '#{가입일}', label: '가입일' }
        ]
    },
    {
        value: 'order',
        label: '주문정보',
        variables: [
            { key: '#{주문번호}', label: '주문번호' },
            { key: '#{주문일시}', label: '주문일시' },
            { key: '#{주문상품}', label: '주문상품명' },
            { key: '#{주문금액}', label: '주문금액' },
            { key: '#{배송상태}', label: '배송상태' },
            { key: '#{송장번호}', label: '송장번호' }
        ]
    },
    {
        value: 'product',
        label: '상품정보',
        variables: [
            { key: '#{상품명}', label: '상품명' },
            { key: '#{상품가격}', label: '상품가격' },
            { key: '#{할인가}', label: '할인가격' },
            { key: '#{재고수량}', label: '재고' },
            { key: '#{브랜드명}', label: '브랜드' }
        ]
    },
    {
        value: 'shop',
        label: '쇼핑몰정보',
        variables: [
            { key: '#{쇼핑몰명}', label: '쇼핑몰명' },
            { key: '#{대표전화}', label: '대표전화' },
            { key: '#{고객센터}', label: '고객센터' },
            { key: '#{URL}', label: '홈페이지' }
        ]
    }
];
```

### 빈 카테고리 처리

```javascript
// 변수가 없는 카테고리
{
    value: 'empty',
    label: '빈 카테고리',
    variables: []  // 빈 배열이면 칩 버튼이 표시되지 않음
}
```

---

## 고급 사용법

### 1. 조건부 변수 표시

```javascript
// 사용자 타입에 따라 다른 변수 제공
function getCategoriesByUserType(userType) {
    const baseCategories = [
        {
            value: 'member',
            label: '회원정보',
            variables: [
                { key: '#{회원명}', label: '회원이름' },
                { key: '#{이메일}', label: '이메일' }
            ]
        }
    ];
    
    if (userType === 'admin') {
        baseCategories.push({
            value: 'admin',
            label: '관리자 전용',
            variables: [
                { key: '#{관리자명}', label: '관리자명' },
                { key: '#{권한레벨}', label: '권한' }
            ]
        });
    }
    
    return baseCategories;
}

chipSelector.setCategories(getCategoriesByUserType('admin'));
```

### 2. 변수 동적 추가

```javascript
// 현재 카테고리에 변수 추가
function addVariableToCurrentCategory(key, label) {
    const currentCategory = chipSelector.getSelectedCategory();
    
    // categories 배열 찾아서 수정
    const category = categoriesData.find(c => c.value === currentCategory);
    if (category) {
        category.variables.push({ key, label });
        chipSelector.refresh();  // 칩 버튼만 업데이트
    }
}

addVariableToCurrentCategory('#{신규변수}', '새로운 변수');
```

### 3. 선택된 변수 하이라이트

```javascript
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [...],
    onSelect: (variable, button) => {
        // 이전 선택 해제
        document.querySelectorAll('.chip-button').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        // 현재 선택 하이라이트
        button.classList.add('selected');
        
        insertVariable(variable);
    }
});
```

CSS 추가:
```css
.chip-button.selected {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}
```

### 4. 카테고리 변경 감지

```javascript
// 카테고리 변경 시 로깅
const originalRender = chipSelector.render;
chipSelector.render = function() {
    const oldCategory = this.currentCategoryValue;
    const result = originalRender.call(this);
    const newCategory = this.currentCategoryValue;
    
    if (oldCategory !== newCategory) {
        console.log('카테고리 변경:', oldCategory, '→', newCategory);
    }
    
    return result;
};
```

---

## 주의사항

### 1. Container 필수

```javascript
// ❌ 잘못된 사용
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    categories: [...]
    // target이 없음!
});

// ✅ 올바른 사용
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container',
    categories: [...]
});
```

### 2. 카테고리 배열이 비어있으면?

```javascript
// 빈 배열이면 첫 번째 카테고리를 선택할 수 없음
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container',
    categories: []  // ⚠️ 빈 배열
});

// currentCategoryValue = '' (빈 문자열)
// 칩 버튼이 표시되지 않음
```

**권장**: 최소 1개 이상의 카테고리 제공

### 3. HTML Escape

모든 텍스트는 자동으로 escape됩니다.

```javascript
const categories = [
    {
        value: 'test',
        label: '<script>alert(1)</script>',  // 위험한 입력
        variables: [
            { key: '#{test}', label: '<b>볼드</b>' }
        ]
    }
];

// 렌더링 결과:
// &lt;script&gt;alert(1)&lt;/script&gt; ✅ 안전
```

### 4. onSelect 콜백이 없으면?

```javascript
// onSelect가 없어도 동작은 하지만 아무 일도 일어나지 않음
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container',
    categories: [...]
    // onSelect 없음 - 칩 클릭해도 아무 일 없음
});

// 나중에 추가 가능
chipSelector.onSelect((variable) => {
    console.log('나중에 추가한 콜백:', variable);
});
```

### 5. 카테고리 변경 시 주의

```javascript
// setCategories()는 전체 렌더링 (비용 높음)
chipSelector.setCategories(newCategories);

// 변수만 추가하고 싶으면 refresh() 사용 (비용 낮음)
categoriesData[0].variables.push({ key: '#{new}', label: '신규' });
chipSelector.refresh();
```

---

## 스타일 커스터마이징

ChipSelector는 NCUA 디자인 시스템 클래스를 사용합니다. 커스터마이징이 필요하면 다음 클래스를 오버라이드하세요:

```css
/* 섹션 제목 */
.ncua-card__body-title--xs {
    font-size: 16px;
    color: #333;
}

/* 카테고리 선택 드롭다운 */
.ncua-select__tag {
    border: 2px solid #007bff;
}

/* 칩 버튼 */
.chip-button {
    border: 2px solid #007bff;
    border-radius: 25px;
}

.chip-button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

/* 칩 버튼 래퍼 */
.chip-button-wrap {
    gap: 12px;  /* 버튼 간격 조정 */
}
```

---

## 성능 최적화

### 1. 많은 변수 처리

변수가 100개 이상일 경우:

```javascript
// ❌ 비효율적: 전체 렌더링
chipSelector.setCategories(largeCategories);

// ✅ 효율적: 필요한 카테고리만 표시
const filteredCategories = largeCategories.map(cat => ({
    ...cat,
    variables: cat.variables.slice(0, 20)  // 처음 20개만
}));
chipSelector.setCategories(filteredCategories);
```

### 2. 빈번한 카테고리 변경

```javascript
// ❌ 비효율적
categories.forEach(cat => {
    chipSelector.setCategories([cat]);  // 매번 전체 렌더링
});

// ✅ 효율적
chipSelector.setCategories(categories);  // 한 번만 렌더링
```

---

## 이벤트 처리

### onSelect 콜백 상세

```javascript
onSelect: (variable, button) => {
    // variable: 선택된 변수의 key (예: "#{회원명}")
    // button: 클릭된 HTML 버튼 요소
    
    console.log('변수:', variable);
    console.log('버튼 텍스트:', button.textContent);
    console.log('버튼 위치:', button.getBoundingClientRect());
    
    // 버튼 스타일 변경
    button.style.backgroundColor = '#28a745';
    
    // 변수 삽입
    insertToTextarea(variable);
}
```

### onCategoryChange 콜백 상세

```javascript
onCategoryChange: (categoryValue) => {
    // categoryValue: 변경된 카테고리의 value (예: "order")
    
    console.log('변경된 카테고리:', categoryValue);
    
    // 카테고리별 추가 액션
    switch (categoryValue) {
        case 'member':
            loadMemberInfo();
            break;
        case 'order':
            loadOrderInfo();
            break;
        case 'product':
            loadProductInfo();
            break;
    }
}
```

### 생성 시 vs 메서드로 등록

```javascript
// 방법 1: 생성 시 등록 (권장)
const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [...],
    onSelect: (variable) => console.log(variable),
    onCategoryChange: (categoryValue) => console.log(categoryValue)
});

// 방법 2: 나중에 메서드로 등록
chipSelector
    .onSelect((variable) => console.log(variable))
    .onCategoryChange((categoryValue) => console.log(categoryValue));
```

---

## 브라우저 호환성

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ⚠️ IE11 (지원 안 함 - Arrow function 사용)

---

## 문제 해결

### Q1: 칩 버튼이 표시되지 않습니다.

**확인 사항**:
1. 카테고리 배열이 비어있지 않은지
2. 선택된 카테고리에 `variables` 배열이 있는지
3. `variables` 배열이 비어있지 않은지

```javascript
console.log(chipSelector.getSelectedCategory());
console.log(categoriesData);
```

### Q2: onSelect 콜백이 실행되지 않습니다.

**확인 사항**:
1. `onSelect` 콜백이 함수인지
2. 콜백 내부에서 오류가 발생하지 않는지
3. 이벤트가 제대로 바인딩되었는지

```javascript
chipSelector.onSelect((variable, button) => {
    console.log('콜백 실행됨:', variable);
    // 여기서 오류 확인
});
```

### Q3: 카테고리를 변경해도 칩이 업데이트되지 않습니다.

```javascript
// ❌ 참조만 변경 (렌더링 안 됨)
categoriesData = newCategories;

// ✅ setCategories 사용
chipSelector.setCategories(newCategories);
```

### Q4: 여러 ChipSelector를 사용할 수 있나요?

**A**: 네, 각각 다른 container에 렌더링하면 독립적으로 사용 가능합니다.

```javascript
const chipSelector1 = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container1',
    categories: categories1
});

const chipSelector2 = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#container2',
    categories: categories2
});
```

---

## 라이센스

이 UI 모듈은 고도몰 프로젝트의 일부입니다.

---

## 버전 히스토리

### v1.0.0 (2024-XX-XX)
- 초기 릴리스
- 카테고리 선택
- 칩 버튼 렌더링
- 변수 선택 콜백
- HTML escape
- 메서드 체이닝

---

## 관련 UI 모듈

- [MessageInput](../message-input/message-input.md) - 메시지 입력 UI 모듈
- [CrmPreview](../crm-preview/crm-preview.md) - 메시지 미리보기 UI 모듈
- [GodoUIModule](../godo-ui-module.md) - 통합 UI 모듈

---

## FAQ

### Q1: 카테고리 순서를 변경하려면?
**A**: 배열 순서를 변경하고 `setCategories()`를 호출하세요.

```javascript
const reordered = [categories[1], categories[0], categories[2]];
chipSelector.setCategories(reordered);
```

### Q2: 특정 카테고리를 기본 선택하려면?
**A**: 해당 카테고리를 배열의 첫 번째에 배치하세요.

```javascript
// 'order'를 기본 선택하고 싶으면
const categories = [
    orderCategory,   // 첫 번째
    memberCategory,
    productCategory
];
```

### Q3: 칩 버튼에 아이콘을 추가하려면?
**A**: `buildChipButtonsHTML()` 메서드를 커스터마이징하거나, CSS로 처리하세요.

```javascript
// variables 객체에 icon 추가
variables: [
    { key: '#{회원명}', label: '회원이름', icon: '👤' }
]

// buildChipButtonsHTML 수정 (ChipSelector 확장)
buildChipButtonsHTML(variables) {
    return variables.map(v =>
        `<button type="button" class="chip-button" data-variable="${this.escapeHTML(v.key)}">
            ${v.icon ? v.icon + ' ' : ''}
            <span class="chip-button-text">${this.escapeHTML(v.key)} ${this.escapeHTML(v.label)}</span>
        </button>`
    ).join('');
}
```

### Q4: 검색 기능을 추가할 수 있나요?
**A**: ChipSelector는 단순 선택기입니다. 검색 기능이 필요하면 별도 UI 모듈을 만들거나 확장하세요.

```javascript
// 간단한 필터링 예시
const searchInput = document.getElementById('search');
searchInput.addEventListener('input', (e) => {
    const keyword = e.target.value.toLowerCase();
    
    const filtered = categoriesData.map(cat => ({
        ...cat,
        variables: cat.variables.filter(v => 
            v.key.toLowerCase().includes(keyword) ||
            v.label.toLowerCase().includes(keyword)
        )
    }));
    
    chipSelector.setCategories(filtered);
});
```

---

## 지원

문제가 발생하거나 제안 사항이 있으면 이슈를 등록해주세요.
