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
</style>
@endsection

@section('content')
<div class="form-container">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-row">
            <div class="form-group">
                <label for="product_name">商品名稱 *</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            
            <div class="form-group">
                <label for="category">商品分類 *</label>
                <input type="text" id="category" name="category" required>
            </div>
        </div>
        
        <div class="form-group full-width">
            <label for="description">商品描述 *</label>
            <div id="editor-container"></div>
            <textarea id="description" name="description" style="display:none;" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price">售價 *</label>
                <input type="number" id="price" name="price" required>
            </div>
            
            <div class="form-group">
                <label for="ori_price">原價</label>
                <input type="number" id="ori_price" name="ori_price">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="pic_name">圖片名稱</label>
                <input type="text" id="pic_name" name="pic_name">
            </div>
            
            <div class="form-group">
                <label for="selected">精選商品</label>
                <select id="selected" name="selected">
                    <option value="0" selected>否</option>
                    <option value="1">是</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="pic_dir">商品圖片 *</label>
            <input type="file" id="pic_dir" name="pic_dir" accept="image/*" required>
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
