<?php
include 'header.php';
$page = pageData('sms_route');
?>

<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        text-align: center;
        padding-top: 40px;
    }

    .header {
        margin-bottom: 50px;
        position: relative;
    }

    /* yellow bar now inside header, above h1 */
    .header .yellow-bar {
        height: 8px;
        background-color: #ffc107;
        width: 100%;
        margin-bottom: 25px;
    }

    .header h1 {
        font-size: 48px;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 15px;
    }

    .header .underline {
        width: 100px;
        height: 4px;
        background-color: #ffc107;
        margin: 0 auto 20px;
    }

    .header p {
        font-size: 20px;
        color: #333;
        font-weight: 500;
    }

    .apps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* exactly 4 per row */
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .app-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 40px 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .app-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .app-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .app-icon img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .app-name {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }

    .op-code {
        font-size: 16px;
        font-weight: 600;
        color: #666;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .download-btn img {
        width: 160px;
        height: auto;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .download-btn img:hover {
        transform: scale(1.05);
    }

    /* Icon styles */
    .itel-icon {
        background: linear-gradient(135deg, #006666 0%, #004d4d 100%);
        border-radius: 25px;
        width: 110px;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
    }

    .itel-icon::before {
        content: '📱';
        font-size: 50px;
    }

    .silver-icon {
        background: linear-gradient(135deg, #3d4054 0%, #2a2c3d 100%);
        border-radius: 15px;
        width: 110px;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #999;
        font-size: 70px;
        font-weight: 700;
    }

    .tp-icon {
        color: #0288d1;
        font-size: 90px;
        font-weight: 700;
        font-style: italic;
        position: relative;
    }

    .tp-icon .small-text {
        font-size: 28px;
        color: #e53935;
        font-weight: 600;
        position: absolute;
        bottom: 5px;
        right: -10px;
    }

    .zeo-icon {
        width: 110px;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .zeo-flower {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 3px;
        width: 70px;
        height: 70px;
    }

    .petal {
        background-color: #2196f3;
        border-radius: 50% 50% 0 0;
    }

    .petal:nth-child(1) {
        transform: rotate(45deg);
        transform-origin: bottom right;
    }

    .petal:nth-child(2) {
        transform: rotate(-45deg);
        transform-origin: bottom left;
    }

    .petal:nth-child(3) {
        transform: rotate(135deg);
        transform-origin: top right;
    }

    .petal:nth-child(4) {
        transform: rotate(-135deg);
        transform-origin: top left;
    }

    @media (max-width: 992px) {
        .apps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .apps-grid {
            grid-template-columns: 1fr;
        }

        .header h1 {
            font-size: 36px;
        }

        .header p {
            font-size: 18px;
        }
    }
</style>

<div class="container">
    <div class="header">
        <div class="yellow-bar"></div>
        <h1>অ্যাপ ডাউনলোড</h1>
        <div class="underline"></div>
        <p>আইফোন এবং আইফোন সাপোর্টেড</p>
    </div>

    <div class="apps-grid">
        <!-- iTel Express -->
        <div class="app-card">
            <div class="app-icon">
                <div class="itel-icon"></div>
            </div>
            <div class="app-name">iTel Express</div>
            <div class="op-code">OP CODE: ?</div>
            <div class="download-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
            </div>
        </div>

        <!-- Silver Dialer -->
        <div class="app-card">
            <div class="app-icon">
                <div class="silver-icon">S</div>
            </div>
            <div class="app-name">Silver Dialer</div>
            <div class="op-code">OP CODE: ?</div>
            <div class="download-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
            </div>
        </div>

        <!-- TP Smart -->
        <div class="app-card">
            <div class="app-icon">
                <div class="tp-icon">
                    Tp
                    <span class="small-text">smart</span>
                </div>
            </div>
            <div class="app-name">TP Smart</div>
            <div class="op-code">OP CODE: ?</div>
            <div class="download-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
            </div>
        </div>

        <!-- Zeo Dialer -->
        <div class="app-card">
            <div class="app-icon">
                <div class="zeo-icon">
                    <div class="zeo-flower">
                        <div class="petal"></div>
                        <div class="petal"></div>
                        <div class="petal"></div>
                        <div class="petal"></div>
                    </div>
                </div>
            </div>
            <div class="app-name">Zeo Dialer</div>
            <div class="op-code">OP CODE: ?</div>
            <div class="download-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
