# MessageInput

## 📋 목차
1. [개요](#개요)
2. [주요 기능](#주요-기능)
3. [설치 및 로드](#설치-및-로드)
4. [기본 사용법](#기본-사용법)
5. [생성 옵션](#생성-옵션)
6. [Public API](#public-api)
7. [실전 예제](#실전-예제)
8. [ChipSelector 연동](#chipselector-연동)
9. [모드 전환](#모드-전환)
10. [주의사항](#주의사항)

---

## 개요

**MessageInput**은 메시지 입력을 위한 textarea UI 모듈입니다. CRM 메시지 발송 화면에서 사용되며, 글자수/바이트 카운트, 힌트 표시, 텍스트 삽입 등의 기능을 제공합니다.

### 특징
- ✅ 실시간 글자수/바이트 카운트
- ✅ SMS bytes 계산 (한글 2bytes, 영문 1byte)
- ✅ 커서 위치에 텍스트 삽입
- ✅ 힌트 텍스트 동적 변경
- ✅ Bytes/Char 모드 전환
- ✅ Threshold 기능 (단계별 최대값 표시)
- ✅ 이모티콘 필터링 (옵션)
- ✅ XSS 방지 (HTML escape)
- ✅ 메서드 체이닝 지원

---

## 주요 기능

### 1. **글자수/바이트 카운트**
```
현재 글자수: 45자
현재 바이트: 128 / 2000 bytes
```

### 2. **힌트 텍스트**
```
[힌트 영역] 최대 2000 bytes까지 입력 가능합니다.
```

### 3. **텍스트 삽입**
커서 위치에 변수나 텍스트를 삽입할 수 있습니다.
```javascript
messageInput.insertText('#{회원명}');
```

### 4. **Bytes Threshold**
- 90 bytes 이하: "90" 표시
- 90 bytes 초과: "2000" 표시 (실제 최대값)

---

## 설치 및 로드

### HTML에서 로드

```html
<!-- MessageInput 클래스 직접 사용 -->
<script src="path/to/message-input.js"></script>

<!-- 또는 GodoUIModule 사용 (권장) -->
<script src="path/to/message-input.js"></script>
<script src="path/to/godo-ui-module.js"></script>
```

### CommonJS/Node.js

```javascript
const MessageInput = require('./message-input.js');
```

---

## 기본 사용법

### 방법 1: GodoUIModule 사용 (권장)

```javascript
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-container',
    placeholder: '메시지를 입력하세요',
    hintText: '최대 2000 bytes',
    maxLength: 2000,
    useBytes: true,
    onInput: (value) => {
        console.log('입력:', value);
    }
});
```

### 방법 2: 클래스 직접 사용

```javascript
const messageInput = new MessageInput({
    container: '#message-container',
    placeholder: '메시지를 입력하세요',
    hintText: '최대 2000 bytes',
    maxLength: 2000,
    useBytes: true
});
```

---

## 생성 옵션

### Options Object

| 옵션 | 타입 | 기본값 | 설명 |
|------|------|--------|------|
| `container` | `string \| HTMLElement` | **(필수)** | 렌더링될 컨테이너 |
| `name` | `string` | `'messageContent'` | textarea의 name 속성 |
| `placeholder` | `string` | `'메시지 내용을 입력하세요.'` | placeholder 텍스트 |
| `hintText` | `string` | `''` | 힌트 텍스트 (빨간색) |
| `maxLength` | `number` | `2000` | 최대 길이 (글자 또는 bytes) |
| `useBytes` | `boolean` | `false` | bytes 모드 사용 여부 |
| `bytesThreshold` | `number` | `90` | bytes 임계값 (0이면 비활성화) |
| `allowEmoji` | `boolean` | `true` | 이모티콘 허용 여부 |
| `initialValue` | `string` | `''` | 초기 값 |
| `onInput` | `Function` | `null` | input 이벤트 콜백 |
| `onChange` | `Function` | `null` | change 이벤트 콜백 |

### 옵션 예시

```javascript
// Char 모드 (일반 글자수)
const charInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#char-input',
    maxLength: 1000,
    useBytes: false,
    hintText: '최대 1000자'
});

// Bytes 모드 (SMS)
const smsInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#sms-input',
    maxLength: 2000,
    useBytes: true,
    bytesThreshold: 90,
    hintText: 'SMS는 최대 2000 bytes'
});

// LMS (긴 문자)
const lmsInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#lms-input',
    maxLength: 2000,
    useBytes: true,
    bytesThreshold: 0,  // threshold 비활성화
    hintText: 'LMS는 최대 2000 bytes'
});
```

---

## Public API

### 텍스트 관리

#### `getValue()`
현재 입력된 값을 반환합니다.

```javascript
const value = messageInput.getValue();
console.log(value); // "안녕하세요 #{회원명}님"
```

**반환값**: `string` - 현재 textarea의 값

---

#### `setValue(value)`
textarea의 값을 설정합니다. 최대 길이를 초과하면 자동으로 잘립니다.

```javascript
messageInput.setValue('새로운 메시지 내용');
```

**매개변수**:
- `value` (string): 설정할 값

**반환값**: `this` (체이닝 가능)

---

#### `insertText(text)`
현재 커서 위치에 텍스트를 삽입합니다.

```javascript
// 커서가 5번째 위치에 있을 때
messageInput.insertText('#{회원명}');
// 커서는 삽입된 텍스트 뒤로 이동
```

**매개변수**:
- `text` (string): 삽입할 텍스트

**반환값**: `this` (체이닝 가능)

**동작**:
1. 현재 커서 위치 또는 선택 영역에 텍스트 삽입
2. 최대 길이 초과 시 자동으로 잘림
3. 커서를 삽입된 텍스트 뒤로 이동
4. textarea에 자동 포커스
5. 카운트 업데이트
6. `onInput` 콜백 실행

---

#### `clear()`
textarea의 값을 비웁니다.

```javascript
messageInput.clear();
```

**반환값**: `this` (체이닝 가능)

---

### 힌트 & 표시

#### `setHint(text)`
힌트 텍스트를 설정합니다.

```javascript
messageInput.setHint('최대 2000 bytes까지 입력 가능');
```

**매개변수**:
- `text` (string): 새로운 힌트 텍스트

**반환값**: `this` (체이닝 가능)

---

#### `getHint()`
현재 힌트 텍스트를 반환합니다.

```javascript
const hint = messageInput.getHint();
```

**반환값**: `string` - 현재 힌트 텍스트

---

#### `setPlaceholder(text)`
placeholder를 설정합니다.

```javascript
messageInput.setPlaceholder('새로운 placeholder');
```

**매개변수**:
- `text` (string): 새로운 placeholder

**반환값**: `this` (체이닝 가능)

---

### 길이 & 모드

#### `setMaxLength(length)`
최대 길이를 설정합니다.

```javascript
messageInput.setMaxLength(3000);
```

**매개변수**:
- `length` (number): 새로운 최대 길이

**반환값**: `this` (체이닝 가능)

---

#### `enableBytesMode(options)`
Bytes 모드를 활성화합니다.

```javascript
messageInput.enableBytesMode({
    maxLength: 2000,
    bytesThreshold: 90
});
```

**매개변수**:
- `options` (Object, optional)
  - `maxLength` (number): 최대 bytes (기본값: 현재 maxLength)
  - `bytesThreshold` (number): bytes 임계값 (기본값: 현재 bytesThreshold)

**반환값**: `this` (체이닝 가능)

---

#### `enableCharMode(options)`
일반 글자수 모드를 활성화합니다.

```javascript
messageInput.enableCharMode({
    maxLength: 1000
});
```

**매개변수**:
- `options` (Object, optional)
  - `maxLength` (number): 최대 글자수 (기본값: 현재 maxLength)

**반환값**: `this` (체이닝 가능)

---

### 커서 & 포커스

#### `getCursorPosition()`
현재 커서 위치를 반환합니다.

```javascript
const position = messageInput.getCursorPosition();
console.log(position); // { start: 15, end: 15 }
```

**반환값**: `Object` - 커서 위치 정보
- `start` (number): 선택 영역의 시작 위치
- `end` (number): 선택 영역의 끝 위치

---

#### `setCursorPosition(position)`
커서 위치를 설정합니다.

```javascript
messageInput.setCursorPosition(10);
```

**매개변수**:
- `position` (number): 설정할 커서 위치

**반환값**: `this` (체이닝 가능)

---

#### `focus()`
textarea에 포커스를 줍니다.

```javascript
messageInput.focus();
```

**반환값**: `this` (체이닝 가능)

---

### 이모티콘 필터링

#### `setAllowEmoji(allow)`
이모티콘 허용 여부를 설정합니다. `false`로 설정 시 현재 입력된 내용에서도 이모티콘이 즉시 제거됩니다.

```javascript
// 이모티콘 차단
messageInput.setAllowEmoji(false);

// 이모티콘 허용
messageInput.setAllowEmoji(true);
```

**매개변수**:
- `allow` (boolean): 이모티콘 허용 여부

**반환값**: `this` (체이닝 가능)

**동작**:
1. `allowEmoji` 설정 변경
2. `false`로 설정 시, 현재 textarea의 값에서 이모티콘 제거
3. 커서 위치 자동 보정 (제거된 이모티콘 수만큼 앞으로 이동)
4. 카운트 업데이트

**제거되는 이모티콘 패턴**:
- `U+1F000-U+1F9FF`: 대부분의 컬러 이모티콘 (😀🎉📱 등)
- `U+2600-U+27BF`: 기호/화살표 (☀️✨⚡ 등)
- `U+2B50-U+2BFF`: 별/도형 (⭐✴️ 등)

```javascript
// 예시
messageInput.setValue('안녕하세요⭐✨🎉');
messageInput.setAllowEmoji(false);
console.log(messageInput.getValue()); // "안녕하세요"
```

---

#### `getAllowEmoji()`
현재 이모티콘 허용 여부를 반환합니다.

```javascript
const isAllowed = messageInput.getAllowEmoji();
console.log(isAllowed); // true 또는 false
```

**반환값**: `boolean` - 이모티콘 허용 여부

---

### 렌더링 & 파괴

#### `getTextareaContainer()`
textarea를 감싸는 컨테이너 DOM 요소를 반환합니다.

```javascript
const container = messageInput.getTextareaContainer();
```

**반환값**: `HTMLElement` - textarea 컨테이너 요소

---

#### `render()`
UI 모듈을 다시 렌더링합니다.

```javascript
messageInput.render();
```

**반환값**: `this` (체이닝 가능)

---

#### `destroy()`
UI 모듈을 파괴하고 DOM을 정리합니다.

```javascript
messageInput.destroy();
```

---

## 실전 예제

### 예제 1: 기본 SMS 입력

```javascript
const smsInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#sms-container',
    name: 'smsContent',
    placeholder: 'SMS 내용을 입력하세요',
    hintText: 'SMS는 최대 90 bytes (초과 시 2000 bytes까지)',
    maxLength: 2000,
    useBytes: true,
    bytesThreshold: 90,
    onInput: (value) => {
        // 실시간 검증 또는 미리보기 업데이트
        updatePreview(value);
    },
    onChange: (value) => {
        // 저장 또는 전송
        saveMessage(value);
    }
});
```

### 예제 2: 일반 텍스트 입력

```javascript
const textInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#text-container',
    maxLength: 1000,
    useBytes: false,
    hintText: '최대 1000자까지 입력 가능',
    initialValue: '기본 메시지 내용'
});
```

### 예제 3: 동적 모드 전환

```javascript
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    maxLength: 2000,
    useBytes: true
});

// SMS 선택 시
function onSelectSMS() {
    input.enableBytesMode({
        maxLength: 2000,
        bytesThreshold: 90
    });
    input.setHint('SMS는 90 bytes 이하 권장');
}

// LMS 선택 시
function onSelectLMS() {
    input.enableBytesMode({
        maxLength: 2000,
        bytesThreshold: 0  // threshold 비활성화
    });
    input.setHint('LMS는 최대 2000 bytes');
}

// 일반 메시지 선택 시
function onSelectNormal() {
    input.enableCharMode({
        maxLength: 1000
    });
    input.setHint('최대 1000자');
}
```

### 예제 4: 이모티콘 필터링

```javascript
// SMS 발송 시 이모티콘 차단
const smsInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#sms-input',
    maxLength: 2000,
    useBytes: true,
    allowEmoji: false  // 이모티콘 차단
});

// 일반 메시지는 이모티콘 허용
const normalInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#normal-input',
    maxLength: 1000,
    useBytes: false,
    allowEmoji: true  // 이모티콘 허용
});

// 동적 전환
document.getElementById('messageType').addEventListener('change', (e) => {
    if (e.target.value === 'SMS') {
        messageInput.setAllowEmoji(false);  // SMS는 이모티콘 차단
    } else {
        messageInput.setAllowEmoji(true);   // 기타는 허용
    }
});
```

### 예제 5: 메서드 체이닝

```javascript
messageInput
    .setValue('안녕하세요')
    .insertText(' #{회원명}')
    .insertText('님')
    .setHint('변수가 포함된 메시지')
    .focus();
```

---

## 이모티콘 필터링

MessageInput은 이모티콘 입력을 제어할 수 있는 기능을 제공합니다. SMS 같이 이모티콘을 지원하지 않는 발송 채널에서 유용합니다.

### 기본 사용

```javascript
// 이모티콘 차단
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    maxLength: 2000,
    useBytes: true,
    allowEmoji: false  // 이모티콘 입력 차단
});
```

### 동적 제어

```javascript
// 초기에는 허용
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    allowEmoji: true
});

// 나중에 차단
messageInput.setAllowEmoji(false);

// 다시 허용
messageInput.setAllowEmoji(true);

// 현재 상태 확인
console.log(messageInput.getAllowEmoji()); // true 또는 false
```

### 제거되는 이모티콘

다음 유니코드 범위의 이모티콘이 자동으로 제거됩니다:

| 범위 | 설명 | 예시 |
|------|------|------|
| `U+1F000-U+1F9FF` | 대부분의 컬러 이모티콘 | 😀😂🎉📱💡🔥 |
| `U+2600-U+27BF` | 기호/화살표 | ☀️✨⚡❤️⭐ |
| `U+2B50-U+2BFF` | 별/도형 | ⭐✴️ |

### 커서 위치 보정

이모티콘이 제거될 때 커서 위치가 자동으로 보정됩니다:

```javascript
messageInput.setValue('안녕⭐하세요');
// 커서가 4번째 위치 (⭐ 다음)에 있다면

messageInput.setAllowEmoji(false);
// "안녕하세요"로 변경되고
// 커서는 2번째 위치로 이동 (⭐가 제거된 만큼 앞으로)
```

### 실전 예제: 발송 유형별 제어

```javascript
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    maxLength: 2000,
    useBytes: true
});

// 발송 유형 변경 시
document.getElementById('sendType').addEventListener('change', (e) => {
    const type = e.target.value;
    
    switch (type) {
        case 'SMS':
        case 'LMS':
            // SMS/LMS는 이모티콘 차단
            messageInput.setAllowEmoji(false);
            messageInput.setHint('이모티콘은 사용할 수 없습니다');
            break;
            
        case 'KAKAO':
        case 'EMAIL':
            // 카카오/이메일은 이모티콘 허용
            messageInput.setAllowEmoji(true);
            messageInput.setHint('이모티콘을 사용할 수 있습니다');
            break;
    }
});
```

### 주의사항

1. **실시간 제거**: `allowEmoji`가 `false`일 때는 입력 즉시 이모티콘이 제거됩니다.
2. **기존 내용 처리**: `setAllowEmoji(false)` 호출 시 기존에 입력된 이모티콘도 모두 제거됩니다.
3. **setValue 동작**: `allowEmoji: false` 상태에서 `setValue()`를 호출하면 이모티콘이 자동으로 제거됩니다.

```javascript
messageInput.setAllowEmoji(false);
messageInput.setValue('안녕⭐하세요🎉');
console.log(messageInput.getValue()); // "안녕하세요"
```

---

## ChipSelector 연동

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
        // 칩 클릭 시 MessageInput에 변수 삽입
        messageInput.insertText(variable);
    }
});
```

### 고급 연동 (미리보기 포함)

```javascript
// MessageInput, ChipSelector, CrmPreview 통합
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#message-input',
    maxLength: 2000,
    useBytes: true,
    onInput: (value) => {
        // 입력 시 미리보기 업데이트
        preview.setContent(value);
    }
});

const chipSelector = GodoUIModule.render({
    type: 'ChipSelector',
    target: '#chip-selector',
    categories: [...],
    onSelect: (variable) => {
        messageInput.insertText(variable);
    }
});

const preview = GodoUIModule.render({
    type: 'MessagePreview',
    target: '#preview',
    messageType: 'SMS'
});

// 초기 값 설정
messageInput.setValue('안녕하세요 #{회원명}님');
preview.setContent('안녕하세요 홍길동님');
```

---

## 모드 전환

### Bytes 모드 vs Char 모드

#### Bytes 모드 (SMS/LMS)
- 한글: 2 bytes
- 영문/숫자/특수문자: 1 byte
- CR(13): 무시

```javascript
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    useBytes: true,
    maxLength: 2000,
    bytesThreshold: 90
});

// "안녕하세요" = 10 bytes (한글 5자 × 2)
// "Hello" = 5 bytes (영문 5자 × 1)
```

#### Char 모드 (일반 텍스트)
- 모든 문자: 1자

```javascript
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    useBytes: false,
    maxLength: 1000
});

// "안녕하세요" = 5자
// "Hello" = 5자
```

### Bytes Threshold 동작

```javascript
// bytesThreshold = 90
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    useBytes: true,
    maxLength: 2000,
    bytesThreshold: 90
});

// 입력: "안녕" (4 bytes)
// 표시: "4 / 90 bytes"

// 입력: "안녕하세요안녕하세요안녕하세요안녕하세요안녕하세요" (100 bytes)
// 표시: "100 / 2000 bytes"  ← 최대값으로 변경
```

### 동적 모드 전환

```javascript
// 발송 유형에 따라 모드 전환
document.getElementById('sendType').addEventListener('change', (e) => {
    const type = e.target.value;
    
    switch (type) {
        case 'SMS':
            messageInput.enableBytesMode({
                maxLength: 2000,
                bytesThreshold: 90
            });
            messageInput.setHint('SMS는 90 bytes 이하 권장');
            break;
            
        case 'LMS':
            messageInput.enableBytesMode({
                maxLength: 2000,
                bytesThreshold: 0
            });
            messageInput.setHint('LMS는 최대 2000 bytes');
            break;
            
        case 'KAKAO':
            messageInput.enableCharMode({
                maxLength: 1000
            });
            messageInput.setHint('카카오는 최대 1000자');
            break;
    }
});
```

---

## 주의사항

### 1. Container 필수

```javascript
// ❌ 잘못된 사용
const input = GodoUIModule.render({
    type: 'MessageInput'
    // target이 없음!
});

// ✅ 올바른 사용
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container'
});
```

### 2. HTML Escape

모든 입력/출력 텍스트는 자동으로 escape됩니다.

```javascript
messageInput.setValue('<script>alert(1)</script>');
// 실제 저장: &lt;script&gt;alert(1)&lt;/script&gt;
```

### 3. Bytes 계산 방식

SMS bytes 계산 기준:
- 한글, 한자, 일본어 등: 2 bytes
- 영문, 숫자, 특수문자: 1 byte
- CR(ASCII 13): 무시
- LF(ASCII 10): 1 byte

```javascript
const text = "안녕\nHello";
// 계산: (2×2) + 1 + (5×1) = 4 + 1 + 5 = 10 bytes
```

### 4. MaxLength 제한

- `maxLength`를 초과하는 입력은 자동으로 잘립니다
- `setValue()` 시에도 검증됩니다

```javascript
const input = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    maxLength: 10,
    useBytes: false
});

input.setValue('12345678901234567890');
console.log(input.getValue()); // "1234567890" (10자만)
```

### 5. 이벤트 콜백 실행 순서

```javascript
1. 사용자가 입력
2. 값 검증 및 자르기
3. 카운트 업데이트
4. onInput 콜백 실행
5. (blur 시) onChange 콜백 실행
```

### 6. 메서드 체이닝 주의

체이닝된 메서드는 순차적으로 실행됩니다.

```javascript
// ✅ 올바른 순서
messageInput
    .clear()           // 먼저 비우기
    .setValue('test')  // 값 설정
    .insertText('!')   // 텍스트 추가
    .focus();          // 포커스

// ❌ 잘못된 순서
messageInput
    .setValue('test')
    .clear()           // 설정한 값이 지워짐!
    .focus();
```

---

## 성능 최적화

### 1. 이벤트 콜백 debounce

실시간 검증이나 API 호출 시 debounce 사용 권장:

```javascript
// debounce 헬퍼 함수
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// 적용
const messageInput = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container',
    onInput: debounce((value) => {
        // 300ms 후 실행
        updatePreview(value);
        validateMessage(value);
    }, 300)
});
```

### 2. 불필요한 업데이트 방지

```javascript
// ❌ 비효율적
setInterval(() => {
    messageInput.updateCount();  // 불필요한 호출
}, 100);

// ✅ 효율적
// 입력 시 자동으로 카운트 업데이트되므로 별도 호출 불필요
```

## 관련 UI 모듈

- [ChipSelector](../chip-selector/chip-selector.md) - 변수 선택 UI 모듈
- [CrmPreview](../crm-preview/crm-preview.md) - 메시지 미리보기 UI 모듈
- [GodoUIModule](../godo-ui-module.md) - 통합 UI 모듈

---

## FAQ

### Q1: 한글 입력 시 bytes가 2배로 계산되나요?
**A**: 네, `useBytes: true`일 때 한글은 2 bytes로 계산됩니다. 이는 SMS 표준을 따릅니다.

### Q2: threshold를 비활성화하려면?
**A**: `bytesThreshold: 0`으로 설정하세요.

```javascript
messageInput.enableBytesMode({
    maxLength: 2000,
    bytesThreshold: 0  // threshold 비활성화
});
```

### Q3: 커서 위치를 유지하면서 값을 변경하려면?
**A**: `insertText()`를 사용하세요.

```javascript
// ❌ 커서 위치 손실
messageInput.setValue('새 값');

// ✅ 커서 위치 유지
messageInput.insertText('추가 텍스트');
```

### Q4: 여러 개의 MessageInput을 사용할 수 있나요?
**A**: 네, 각각 다른 container에 렌더링하면 독립적으로 사용 가능합니다.

```javascript
const input1 = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container1'
});

const input2 = GodoUIModule.render({
    type: 'MessageInput',
    target: '#container2'
});
```

---

## 지원

문제가 발생하거나 제안 사항이 있으면 이슈를 등록해주세요.
