<div id="storageErrorModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>系統通知</h3>
        </div>
        <div class="modal-body">
            <p>抱歉，由於 Symbolic Link（storage:link）權限失效，</br>
                以致圖片未能顯示。目前正在盡速修復中！</p>
            <div class="countdown-container">
                <span class="countdown-text">此訊息將在 </span>
                <span class="countdown-number" id="countdown">7</span>
                <span class="countdown-text"> 秒後自動關閉</span>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.fade-out {
        opacity: 0;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        padding: 0;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        transform: scale(1);
        transition: transform 0.3s ease;
    }

    .modal-overlay.fade-out .modal-content {
        transform: scale(0.95);
    }

    .modal-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }

    .modal-header h3 {
        margin: 0;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-body p {
        margin: 0 0 15px 0;
        color: #6c757d;
        line-height: 1.5;
        font-size: 16px;
    }

    .countdown-container {
        text-align: center;
        font-size: 14px;
        color: #868e96;
    }

    .countdown-number {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: bold;
        min-width: 20px;
        text-align: center;
    }

    .countdown-text {
        color: #6c757d;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('storageErrorModal');
        const countdownElement = document.getElementById('countdown');
        let timeLeft = 7;

        // 倒數計時器
        const countdownTimer = setInterval(function() {
            timeLeft--;
            countdownElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownTimer);

                // 開始淡出動畫
                modal.classList.add('fade-out');

                // 淡出動畫完成後移除modal
                setTimeout(function() {
                    modal.style.display = 'none';
                }, 300); // 等待淡出動畫完成
            }
        }, 1000);
    });
</script>