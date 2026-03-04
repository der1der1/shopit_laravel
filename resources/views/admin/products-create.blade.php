@extends('layouts.admin')

@section('title', '新增商品 - 管理後台')
@section('page-title', '新增商品')

@section('styles')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    
    /* 圖片上傳區域樣式 */
    .image-upload-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 10px;
    }
    
    .image-upload-box {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 15px;
        background: #f9f9f9;
        transition: all 0.3s;
    }
    
    .image-upload-box:hover {
        border-color: #3498db;
        background: #f0f8ff;
    }
    
    .image-name-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
        font-size: 14px;
    }
    
    .image-preview-area {
        position: relative;
        aspect-ratio: 1;
        border: 2px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-preview-area:hover .image-add-btn {
        background: #3498db;
        color: white;
    }
    
    .image-add-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        pointer-events: none;
    }
    
    .plus-icon {
        font-size: 48px;
        color: #3498db;
        font-weight: 300;
    }
    
    .add-text {
        font-size: 14px;
        color: #666;
    }
    
    .image-preview-area:hover .plus-icon {
        color: white;
    }
    
    .image-preview-area:hover .add-text {
        color: white;
    }
    
    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }
    
    .remove-image-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: rgba(231, 76, 60, 0.9);
        color: white;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 10;
    }
    
    .image-preview-area:hover .remove-image-btn {
        opacity: 1;
    }
    
    .remove-image-btn:hover {
        background: #c0392b;
        transform: scale(1.1);
    }

    /* Toggle Switch 樣式 */
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 30px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: #3498db;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(30px);
    }

    .toggle-label {
        font-weight: 600;
        color: #666;
        transition: color 0.3s;
    }

    .toggle-switch input:checked ~ .toggle-label {
        color: #3498db;
    }
    /* Toggle Switch 結束 */

    /* Category Select Styles */
    .category-select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }

    .category-new-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #3498db;
        border-radius: 5px;
        font-size: 14px;
    }
    /* Category Select 結束 */
</style>
@endsection

@section('content')
<div class="form-container">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- 商品狀態 Toggle -->
        <div class="form-group full-width">
            <label for="is_active">商品狀態 *</label>
            <div class="toggle-container">
                <label class="toggle-switch">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label" id="status-label-text">上架</span>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_name">商品名稱 *</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            
            <div class="form-group">
                <label for="category">商品分類 *</label>
                <select id="category_select" class="category-select" onchange="handleCategoryChange(this)">
                    <option value="">請選擇分類</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                    <option value="__new__">➕ 新增分類...</option>
                </select>
                <input type="text" id="category" name="category" style="display:none;" required>
                <input type="text" id="category_new" class="category-new-input" placeholder="輸入新分類名稱" style="display:none; margin-top:10px;">
            </div>
        </div>
        
        <div class="form-group full-width">
            <label for="description">商品描述 *</label>
            <div id="editor-container"></div>
            <textarea id="description" name="description" style="display:none;" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ori_price">原價</label>
                <input type="number" id="ori_price" name="ori_price">
            </div>

            <div class="form-group">
                <label for="price">售價 *</label>
                <input type="number" id="price" name="price" required style="background: #64e0ff;">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="quantity">商品庫存數 *</label>
                <input type="number" id="quantity" name="quantity" min="0" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="min_quantity">最低庫存數 *</label>
                <input type="number" id="min_quantity" name="min_quantity" min="0" value="0" required>
                <a href="{{ route('admin.maillist') }}" style="display: inline-block; margin-top: 8px; color: #3498db; text-decoration: none; font-size: 13px;">
                    📧 前往數量不足通報設定
                </a>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="pic_name">圖片名稱</label>
                <input type="text" id="pic_name" name="pic_name">
            </div>
            
            <div class="form-group">
                <label for="selected">精選商品</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" id="selected" name="selected" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label" id="toggle-label-text">否</span>
                </div>
            </div>
        </div>
        
        <!-- 商品圖片上傳區 (四張圖片) -->
        <div class="form-group full-width">
            <label>商品圖片（最少 1 張，最多 4 張）*</label>
            <div class="image-upload-grid">
                <!-- 圖片 1 (必填-首圖) -->
                <div class="image-upload-box" data-index="0">
                    <div style="margin-bottom: 5px;">
                        <span style="display: inline-block; background: #e74c3c; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">首圖</span>
                    </div>
                    <input type="text" class="image-name-input" name="image_names[]" placeholder="圖片名稱 (必填)" required>
                    <div class="image-preview-area">
                        <input type="file" class="image-file-input" name="images[]" accept="image/*" data-index="0" required style="display:none;">
                        <div class="image-add-btn">
                            <span class="plus-icon">+</span>
                            <span class="add-text">點擊上傳圖片</span>
                        </div>
                        <img class="preview-image" src="" style="display:none;">
                        <button type="button" class="remove-image-btn" style="display:none;">×</button>
                    </div>
                </div>

                <!-- 圖片 2-4 (選填) -->
                <div class="image-upload-box" data-index="1">
                    <div class="image-preview-area">
                        <input type="file" class="image-file-input" name="images[]" accept="image/*" data-index="1" style="display:none;">
                        <div class="image-add-btn">
                            <span class="plus-icon">+</span>
                            <span class="add-text">點擊上傳圖片</span>
                        </div>
                        <img class="preview-image" src="" style="display:none;">
                        <button type="button" class="remove-image-btn" style="display:none;">×</button>
                    </div>
                </div>

                <div class="image-upload-box" data-index="2">
                    <div class="image-preview-area">
                        <input type="file" class="image-file-input" name="images[]" accept="image/*" data-index="2" style="display:none;">
                        <div class="image-add-btn">
                            <span class="plus-icon">+</span>
                            <span class="add-text">點擊上傳圖片</span>
                        </div>
                        <img class="preview-image" src="" style="display:none;">
                        <button type="button" class="remove-image-btn" style="display:none;">×</button>
                    </div>
                </div>

                <div class="image-upload-box" data-index="3">
                    <div class="image-preview-area">
                        <input type="file" class="image-file-input" name="images[]" accept="image/*" data-index="3" style="display:none;">
                        <div class="image-add-btn">
                            <span class="plus-icon">+</span>
                            <span class="add-text">點擊上傳圖片</span>
                        </div>
                        <img class="preview-image" src="" style="display:none;">
                        <button type="button" class="remove-image-btn" style="display:none;">×</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">新增商品</button>
            <a href="{{ route('admin.products') }}" class="btn" style="background: #95a5a6; color: white;">返回列表</a>
        </div>
    </form>
</div>

<!-- Image Resize Modal -->
<div id="imageResizeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:10px; max-width:800px; width:90%;">
        <h3 style="margin-top:0; color:#2c3e50;">調整圖片大小 (建議您保持圖片大小一致)</h3>
        <div style="text-align:center; margin:20px 0; position:relative; display:inline-block;">
            <img id="previewImage" src="" style="max-width:100%; border:2px solid #ddd; border-radius:5px; cursor:move; position:relative;">
            <div id="resizeHandle" style="position:absolute; right:-5px; bottom:-5px; width:15px; height:15px; background:#3498db; cursor:nwse-resize; border:2px solid white; border-radius:50%;"></div>
        </div>
        <div style="margin:20px 0;">
            <label style="display:block; margin-bottom:10px; font-weight:600;">寬度 (px):</label>
            <input type="number" id="imageWidth" min="50" style="width:120px; padding:8px; border:1px solid #ddd; border-radius:5px;">
            <label style="display:inline-block; margin-left:20px; margin-right:10px; font-weight:600;">高度 (px):</label>
            <input type="number" id="imageHeight" min="50" style="width:120px; padding:8px; border:1px solid #ddd; border-radius:5px;">
            <label style="display:inline-block; margin-left:20px;">
                <input type="checkbox" id="keepRatio" checked> 維持比例
            </label>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
            <button onclick="cancelImageResize()" class="btn" style="background:#95a5a6; color:white;">取消</button>
            <button onclick="confirmImageResize()" class="btn btn-primary">確認插入</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Quill Editor -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    let pendingImageFile = null;
    let pendingImageUrl = null;
    let originalImageWidth = 0;
    let originalImageHeight = 0;
    let currentInsertPosition = null;

    // Initialize Quill editor
    const quill = new Quill('#editor-container', {
        theme: 'snow',
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

    // Handle image upload with modal
    quill.getModule('toolbar').addHandler('image', function() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                pendingImageFile = file;
                currentInsertPosition = quill.getSelection();
                
                // Preview image in modal
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('previewImage');
                    img.src = e.target.result;
                    img.onload = function() {
                        originalImageWidth = img.naturalWidth;
                        originalImageHeight = img.naturalHeight;
                        document.getElementById('imageWidth').value = originalImageWidth;
                        document.getElementById('imageHeight').value = originalImageHeight;
                        document.getElementById('imageResizeModal').style.display = 'flex';
                    };
                };
                reader.readAsDataURL(file);
            }
        };
    });

    // Keep aspect ratio and update preview
    document.getElementById('imageWidth').addEventListener('input', function() {
        if (document.getElementById('keepRatio').checked && this.value) {
            const ratio = originalImageHeight / originalImageWidth;
            document.getElementById('imageHeight').value = Math.round(this.value * ratio);
        }
        updatePreviewSize();
    });

    document.getElementById('imageHeight').addEventListener('input', function() {
        if (document.getElementById('keepRatio').checked && this.value) {
            const ratio = originalImageWidth / originalImageHeight;
            document.getElementById('imageWidth').value = Math.round(this.value * ratio);
        }
        updatePreviewSize();
    });

    function updatePreviewSize() {
        const width = document.getElementById('imageWidth').value;
        const height = document.getElementById('imageHeight').value;
        const img = document.getElementById('previewImage');
        if (width && height) {
            img.style.width = width + 'px';
            img.style.height = height + 'px';
        }
    }

    // Drag to resize functionality
    document.addEventListener('DOMContentLoaded', function() {
        let isResizing = false;
        let startX, startY, startWidth, startHeight;

        document.getElementById('resizeHandle').addEventListener('mousedown', function(e) {
            e.preventDefault();
            isResizing = true;
            const img = document.getElementById('previewImage');
            startX = e.clientX;
            startY = e.clientY;
            startWidth = parseInt(img.style.width) || img.offsetWidth;
            startHeight = parseInt(img.style.height) || img.offsetHeight;
            
            document.body.style.cursor = 'nwse-resize';
        });

        document.addEventListener('mousemove', function(e) {
            if (!isResizing) return;
            
            const img = document.getElementById('previewImage');
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
            let newWidth = startWidth + dx;
            let newHeight = startHeight + dy;
            
            if (newWidth < 50) newWidth = 50;
            if (newHeight < 50) newHeight = 50;
            
            // 保持比例
            if (document.getElementById('keepRatio').checked) {
                const ratio = originalImageWidth / originalImageHeight;
                newHeight = Math.round(newWidth / ratio);
            }
            
            img.style.width = newWidth + 'px';
            img.style.height = newHeight + 'px';
            
            // 更新輸入框的數值
            document.getElementById('imageWidth').value = Math.round(newWidth);
            document.getElementById('imageHeight').value = Math.round(newHeight);
        });

        document.addEventListener('mouseup', function() {
            if (isResizing) {
                isResizing = false;
                document.body.style.cursor = 'default';
            }
        });
    });

    // Cancel button
    function cancelImageResize() {
        document.getElementById('imageResizeModal').style.display = 'none';
        pendingImageFile = null;
        pendingImageUrl = null;
        document.getElementById('previewImage').src = '';
        document.getElementById('imageWidth').value = '';
        document.getElementById('imageHeight').value = '';
    }

    // Confirm and upload
    async function confirmImageResize() {
        if (!pendingImageFile) return;

        const formData = new FormData();
        formData.append('file', pendingImageFile);

        try {
            const response = await fetch('{{ route("admin.products.upload-image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            if (data.location) {
                const width = document.getElementById('imageWidth').value;
                const height = document.getElementById('imageHeight').value;
                
                // Insert image with custom size
                quill.insertEmbed(currentInsertPosition.index, 'image', data.location);
                
                // Apply custom size if specified
                setTimeout(() => {
                    const images = document.querySelectorAll('#editor-container img');
                    const lastImage = images[images.length - 1];
                    if (lastImage && width && height) {
                        lastImage.style.width = width + 'px';
                        lastImage.style.height = height + 'px';
                    }
                }, 100);
                
                // Close modal
                cancelImageResize();
            } else {
                alert('圖片上傳失敗');
            }
        } catch (error) {
            alert('圖片上傳錯誤: ' + error.message);
        }
    }

    // Update hidden textarea before form submission
    document.querySelector('form').onsubmit = function() {
        document.querySelector('#description').value = quill.root.innerHTML;
    };
    
    // 商品圖片上傳功能
    document.addEventListener('DOMContentLoaded', function() {
        const imageBoxes = document.querySelectorAll('.image-upload-box');
        
        imageBoxes.forEach((box, index) => {
            const previewArea = box.querySelector('.image-preview-area');
            const fileInput = box.querySelector('.image-file-input');
            const previewImage = box.querySelector('.preview-image');
            const addBtn = box.querySelector('.image-add-btn');
            const removeBtn = box.querySelector('.remove-image-btn');
            
            // 點擊預覽區域觸發文件選擇
            previewArea.addEventListener('click', function(e) {
                if (e.target === removeBtn) return;
                fileInput.click();
            });
            
            // 文件選擇後預覽
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        addBtn.style.display = 'none';
                        removeBtn.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // 刪除圖片
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                previewImage.src = '';
                previewImage.style.display = 'none';
                addBtn.style.display = 'flex';
                removeBtn.style.display = 'none';
                
                // 清空對應的圖片名稱
                const nameInput = box.querySelector('.image-name-input');
                if (index > 0) { // 第一張圖片的名稱保留required
                    nameInput.value = '';
                }
            });
        });
        
        // Toggle switch 標籤更新 - 精選商品
        const selectedToggle = document.getElementById('selected');
        const toggleLabelText = document.getElementById('toggle-label-text');
        
        if (selectedToggle && toggleLabelText) {
            selectedToggle.addEventListener('change', function() {
                toggleLabelText.textContent = this.checked ? '是' : '否';
            });
        }
        
        // Toggle switch 標籤更新 - 商品狀態
        const statusToggle = document.getElementById('is_active');
        const statusLabelText = document.getElementById('status-label-text');
        
        if (statusToggle && statusLabelText) {
            statusToggle.addEventListener('change', function() {
                statusLabelText.textContent = this.checked ? '上架' : '下架';
                statusLabelText.style.color = this.checked ? '#3498db' : '#e74c3c';
            });
        }
    });
    
    // Category dropdown handler
    function handleCategoryChange(select) {
        const categoryInput = document.getElementById('category');
        const categoryNewInput = document.getElementById('category_new');
        
        if (select.value === '__new__') {
            // Show new category input
            categoryNewInput.style.display = 'block';
            categoryNewInput.required = true;
            categoryNewInput.focus();
            categoryInput.value = '';
            
            // Update hidden input when typing in new category
            categoryNewInput.addEventListener('input', function() {
                categoryInput.value = this.value;
            });
        } else {
            // Use selected category
            categoryNewInput.style.display = 'none';
            categoryNewInput.required = false;
            categoryNewInput.value = '';
            categoryInput.value = select.value;
        }
    }
</script>

<style>
    #editor-container {
        height: 400px;
        background: white;
    }
    .ql-editor {
        min-height: 400px;
        font-size: 14px;
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
    }
    .ql-toolbar {
        background: #f8f9fa;
        border-radius: 5px 5px 0 0;
    }
</style>
@endsection
