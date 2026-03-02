@extends('layouts.admin')

@section('title', '聯絡訊息 - 管理後台')
@section('page-title', '聯絡訊息')

@section('styles')
<style>
    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .search-bar input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .data-table tr:hover {
        background: #f8f9fa;
    }
    
    .data-table tr.unread {
        background: #fff3cd;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 30px;
        border-radius: 8px;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover {
        color: #000;
    }
    
    .message-detail {
        margin: 20px 0;
    }
    
    .message-detail h3 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .message-detail p {
        margin: 10px 0;
        line-height: 1.6;
    }
    
    .message-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #3498db;
        margin: 15px 0;
    }
    
    /* Loading overlay */
    #loadingOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    #loadingOverlay.active {
        display: flex;
    }
    
    .loading-content {
        background-color: white;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        color: #2c3e50;
        font-size: 16px;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">聯絡訊息列表</h2>
    </div>
    
    <!-- 待處理訊息區域 -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db;">
            待處理訊息 <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 12px; font-size: 14px;">{{ count($pendingContacts ?? []) }}</span>
        </h3>
        
        <div class="search-bar">
            <input type="text" id="searchPendingInput" placeholder="搜尋姓名、Email或電話..." onkeyup="searchTable('pendingTable', 'searchPendingInput')">
        </div>
        
        <table class="data-table" id="pendingTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>Email</th>
                    <th>電話</th>
                    <th>訊息摘要</th>
                    <th>提交時間</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingContacts ?? [] as $contact)
                <tr>
                    <td>{{ $contact->id }}</td>
                    <td><strong>{{ $contact->name }}</strong></td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->phone }}</td>
                    <td>{{ Str::limit($contact->information, 80) }}</td>
                    <td>{{ $contact->created_at ? $contact->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        <div class="action-btns">
                            <button onclick="viewContact({{ $contact->id }})" class="btn btn-primary btn-sm">查看</button>
                            <button onclick="deleteContact({{ $contact->id }})" class="btn btn-danger btn-sm">刪除</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #7f8c8d; padding: 40px;">
                        暫無待處理訊息
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- 已回覆訊息區域 -->
    <div>
        <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #27ae60;">
            已回覆訊息 <span style="background: #27ae60; color: white; padding: 2px 8px; border-radius: 12px; font-size: 14px;">{{ count($repliedContacts ?? []) }}</span>
        </h3>
        
        <div class="search-bar">
            <input type="text" id="searchRepliedInput" placeholder="搜尋姓名、Email或電話..." onkeyup="searchTable('repliedTable', 'searchRepliedInput')">
        </div>
        
        <table class="data-table" id="repliedTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>Email</th>
                    <th>電話</th>
                    <th>訊息摘要</th>
                    <th>提交時間</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($repliedContacts ?? [] as $contact)
                <tr>
                    <td>{{ $contact->id }}</td>
                    <td><strong>{{ $contact->name }}</strong></td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->phone }}</td>
                    <td>{{ Str::limit($contact->information, 80) }}</td>
                    <td>{{ $contact->created_at ? $contact->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        <div class="action-btns">
                            <button onclick="viewContact({{ $contact->id }})" class="btn btn-primary btn-sm">查看</button>
                            <button onclick="deleteContact({{ $contact->id }})" class="btn btn-danger btn-sm">刪除</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #7f8c8d; padding: 40px;">
                        暫無已回覆訊息
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- View Contact Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="contactDetails"></div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div class="loading-text">發送中請稍後...</div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function searchTable(tableId, inputId) {
        const input = document.getElementById(inputId);
        const filter = input.value.toUpperCase();
        const table = document.getElementById(tableId);
        const tr = table.getElementsByTagName('tr');
        
        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            tr[i].style.display = found ? '' : 'none';
        }
    }
    
    function viewContact(id) {
        fetch(`/admin/contacts/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contact = data.contact;
                    document.getElementById('contactDetails').innerHTML = `
                        <h2>聯絡訊息詳情</h2>
                        <div class="message-detail">
                            <p><strong>姓名:</strong> ${contact.name}</p>
                            <p><strong>Email:</strong> <a href="mailto:${contact.email}">${contact.email}</a></p>
                            <p><strong>電話:</strong> ${contact.phone}</p>
                            <p><strong>提交時間:</strong> ${contact.created_at}</p>
                            
                            <h3 style="margin-top: 25px;">客戶訊息:</h3>
                            <div class="message-content">
                                ${contact.information.replace(/\n/g, '<br>')}
                            </div>
                            <textarea id="replyMessage" style="width:100%;height:100px;margin-top:15px;resize:vertical;" placeholder="請輸入回覆內容..."></textarea>
                            <div style="margin-top: 20px; display: flex; gap: 10px;">
                                <button onclick="replyContact('${contact.email}', ${contact.id})" class="btn btn-success">回覆此訊息</button>
                                <button onclick="closeModal()" class="btn" style="background: #95a5a6; color: white;">關閉</button>
                            </div>
                        </div>
                    `;
                    document.getElementById('contactModal').style.display = 'block';
                }
            })
            .catch(error => {
                alert('無法載入訊息詳情');
                console.error('Error:', error);
            });
    }
    
    function deleteContact(id) {
        if (confirm('確定要刪除此聯絡訊息嗎？此操作無法復原！')) {
            fetch(`/admin/contacts/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('刪除成功！');
                    location.reload();
                } else {
                    alert('刪除失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                alert('刪除失敗：' + error.message);
            });
        }
    }
    
    function closeModal() {
        document.getElementById('contactModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('contactModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // 回覆聯絡訊息
    function replyContact(email, id) {
        const message = document.getElementById('replyMessage').value.trim();
        if (!message) {
            alert('請輸入回覆內容');
            return;
        }
        
        // 顯示 loading overlay
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.classList.add('active');
        
        fetch('/admin/contacts/reply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                id: id,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            // 隱藏 loading overlay
            loadingOverlay.classList.remove('active');
            
            if (data.success) {
                alert('回覆已發送');
                closeModal();
                // 重新載入頁面以更新訊息狀態
                location.reload();
            } else {
                alert('回覆失敗：' + (data.message || '未知錯誤'));
            }
        })
        .catch(error => {
            // 隱藏 loading overlay
            loadingOverlay.classList.remove('active');
            alert('回覆失敗：' + error.message);
        });
    }
</script>
@endsection
