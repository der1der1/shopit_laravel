# Quill 文字編輯器設置說明

## 概述

本專案已整合 **Quill 2.0** 文字編輯器到商品管理系統中，用於編輯商品描述。Quill 是一個功能強大、易於維護且持續更新的富文本編輯器。

## 為何選擇 Quill？

- ✅ **完全免費**：不需要 API key
- ✅ **無警告訊息**：不會出現授權警告
- ✅ **持續維護**：活躍的社群和定期更新
- ✅ **輕量級**：體積小、載入快
- ✅ **功能完整**：支援文字格式化和圖片上傳
- ✅ **易於自訂**：簡潔的 API 和豐富的配置選項

## 功能特點

- ✅ 豐富的文字格式化選項（粗體、斜體、底線、刪除線）
- ✅ 標題層級（H1-H6）
- ✅ 文字和背景顏色
- ✅ 文字對齊（左、中、右、兩端對齊）
- ✅ 有序和無序列表
- ✅ 引用和程式碼區塊
- ✅ 超連結插入
- ✅ 圖片上傳並嵌入文字中
- ✅ 清除格式功能

## 已修改的檔案

### 1. 視圖檔案
- `resources/views/admin/products-edit.blade.php` - 整合 Quill 編輯器
- `resources/views/admin/products-create.blade.php` - 整合 Quill 編輯器

### 2. 控制器
- `app/Http/Controllers/AdminController.php` 
  - 新增 `uploadImage()` 方法處理圖片上傳

### 3. 路由
- `routes/web.php`
  - 新增 `POST /admin/products/upload-image` 路由

### 4. 目錄結構
- `public/img/products/descriptions/` - 存放編輯器上傳的圖片（自動創建）

## 使用方式

### 管理員端

1. 登入管理後台
2. 前往「商品管理」→「新增商品」或「編輯商品」
3. 在「商品描述」欄位，會看到 Quill 編輯器
4. 使用工具列進行文字格式化：
   - **標題**：選擇 H1-H6 或一般段落
   - **格式**：粗體、斜體、底線、刪除線
   - **顏色**：文字顏色和背景顏色
   - **對齊**：左對齊、置中、右對齊
   - **列表**：有序列表、無序列表
   - **插入**：引用、程式碼、連結、圖片
5. 上傳圖片：
   - 點擊工具列的「圖片」按鈕
   - 選擇圖片檔案（支援 jpg, png, gif 等）
   - 圖片會自動上傳並嵌入到內容中
   - 圖片大小限制：2MB

### 前端顯示

商品描述的 HTML 內容會直接顯示在商品頁面上，需要使用 `{!! $product->description !!}` 來輸出 HTML。

## 技術細節

### Quill 配置

```javascript
const quill = new Quill('#editor-container', {
    theme: 'snow',  // 使用 Snow 主題（白色背景）
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            ['clean']
        ]
    },
    placeholder: '請輸入商品描述...'
});
```

### 圖片上傳流程

1. 使用者點擊編輯器工具列的圖片按鈕
2. 選擇本地圖片檔案
3. JavaScript 通過 Fetch API 發送到 `/admin/products/upload-image`
4. 後端驗證圖片（類型、大小）
5. 儲存到 `public/img/products/descriptions/` 目錄
6. 返回圖片 URL 給前端
7. Quill 自動插入圖片到編輯器中

### 表單提交處理

編輯器內容在表單提交時自動複製到隱藏的 textarea：

```javascript
document.querySelector('form').onsubmit = function() {
    document.querySelector('#description').value = quill.root.innerHTML;
};
```

### 安全性

- ✅ CSRF Token 驗證
- ✅ 檔案類型驗證（只允許圖片）
- ✅ 檔案大小限制（2MB）
- ✅ 管理員權限檢查

## 維護資訊

### Quill 版本
- **當前版本**：2.0.2 (CDN)
- **官方網站**：https://quilljs.com/
- **文件**：https://quilljs.com/docs/
- **GitHub**：https://github.com/quilljs/quill
- **授權**：BSD 3-Clause License（完全免費）

### 更新 Quill

Quill 使用 CDN 載入。如需固定版本或更新：

```html
<!-- 固定版本 -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<!-- 或使用最新版本 -->
<link href="https://cdn.jsdelivr.net/npm/quill@latest/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@latest/dist/quill.js"></script>
```

### 自訂配置

#### 修改工具列

```javascript
toolbar: [
    // 新增字型大小選項
    [{ 'size': ['small', false, 'large', 'huge'] }],
    
    // 新增更多顏色
    [{ 'color': ['#000', '#e60000', '#ff9900', '#ffff00'] }],
    
    // 新增縮排
    [{ 'indent': '-1'}, { 'indent': '+1' }],
    
    // 新增影片
    ['video']
]
```

#### 修改編輯器高度

在 CSS 中修改：

```css
#editor-container {
    height: 600px;  /* 預設是 400px */
}
```

#### 切換主題

Quill 提供兩種主題：
- `snow`：標準主題，白色背景
- `bubble`：氣泡主題，工具列僅在選取文字時顯示

```javascript
const quill = new Quill('#editor-container', {
    theme: 'bubble'  // 改為氣泡主題
});
```

## 常見問題

### Q: 圖片上傳失敗？
A: 檢查以下項目：
1. `public/img/products/descriptions/` 目錄權限
2. 圖片大小是否超過 2MB
3. 確認 CSRF token 正確
4. 檢查瀏覽器控制台的錯誤訊息

### Q: 編輯器沒有顯示？
A: 檢查：
1. 瀏覽器控制台是否有 JavaScript 錯誤
2. CDN 連線是否正常
3. `#editor-container` 元素是否存在
4. CSS 是否正確載入

### Q: 如何更改圖片上傳大小限制？
A: 修改 `AdminController.php` 中的驗證規則：

```php
'file' => 'required|image|max:5120' // 改為 5MB
```

同時需要修改 `php.ini` 設定：
```ini
upload_max_filesize = 5M
post_max_size = 5M
```

### Q: 如何禁用某些工具列功能？
A: 在 toolbar 配置中移除不需要的項目：

```javascript
toolbar: [
    ['bold', 'italic'],  // 只保留粗體和斜體
    ['link']  // 只保留連結
]
```

### Q: 儲存的內容沒有套用樣式？
A: 確保前端顯示時使用 `{!! !!}` 而非 `{{ }}`：

```blade
<!-- 正確 - 會渲染 HTML -->
{!! $product->description !!}

<!-- 錯誤 - 會轉義 HTML -->
{{ $product->description }}
```

## 與其他編輯器比較

| 特性 | Quill | TinyMCE | CKEditor |
|------|-------|---------|----------|
| 免費使用 | ✅ 完全免費 | ⚠️ 需要 API key | ✅ 有免費版 |
| API key | ❌ 不需要 | ✅ 需要 | ❌ 不需要 |
| 授權警告 | ❌ 無 | ⚠️ 有 | ❌ 無 |
| 體積 | 🟢 小 (43KB) | 🟡 中等 | 🟡 中等 |
| 易用性 | 🟢 簡單 | 🟡 中等 | 🟡 中等 |
| 自訂性 | 🟢 容易 | 🟢 容易 | 🟢 容易 |
| 維護狀態 | ✅ 活躍 | ✅ 活躍 | ✅ 活躍 |

## 替代方案

如果 Quill 不符合需求，可考慮以下替代方案：

1. **CKEditor 5** - https://ckeditor.com/
   - 功能豐富，免費版本可用
   - 無需 API key

2. **SimpleMDE** - https://simplemde.com/
   - Markdown 編輯器
   - 輕量級

3. **Trix** - https://trix-editor.org/
   - Basecamp 開發
   - 簡潔且穩定

## 支援

- Quill 官方文件：https://quilljs.com/docs/
- GitHub Issues：https://github.com/quilljs/quill/issues
- Stack Overflow：搜尋 `quilljs` 標籤
- 社群論壇：https://github.com/quilljs/quill/discussions

## 更新日誌

### 2026-03-02
- 從 TinyMCE 切換到 Quill
- 原因：TinyMCE CDN 版本需要 API key 並顯示警告訊息
- Quill 完全免費且無任何限制
