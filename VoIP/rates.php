<?php
include 'header.php';
$stmt = $pdo->query("SELECT * FROM rates ORDER BY country_name ASC");
$countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ratesData = [];
foreach ($countries as $country) {
    $carrierStmt = $pdo->prepare("SELECT * FROM rate_details WHERE rate_id=?");
    $carrierStmt->execute([$country['id']]);
    $carriers = $carrierStmt->fetchAll(PDO::FETCH_ASSOC);

    $ratesData[] = [
        'country' => $country,
        'carriers' => $carriers
    ];
}

if (empty($ratesData)) {
    $ratesData = [
        [
            'country' => [
                'country_name' => 'Somalia',
                'country_code' => 'SO',
                'last_update' => date('Y-m-d'),
                'expiry_info' => 'No expiry date',
                'connection_fee_note' => 'A connection fee applies'
            ],
            'carriers' => [
                ['carrier_name' => 'Somalia', 'sms_rate' => '0.074', 'sms_unit' => '/sms', 'call_rate' => '0.3', 'call_unit' => '/min', 'bundle_info' => '3 min/ $1'],
                ['carrier_name' => 'Somalia Mobile', 'sms_rate' => '0.074', 'sms_unit' => '/sms', 'call_rate' => '0.3', 'call_unit' => '/min', 'bundle_info' => '3 min/ $1']
            ]
        ],
        [
            'country' => [
                'country_name' => 'Bangladesh',
                'country_code' => 'BD',
                'last_update' => date('Y-m-d'),
                'expiry_info' => '30 Days Limit',
                'connection_fee_note' => 'A connection fee applies'
            ],
            'carriers' => [
                ['carrier_name' => 'Bangladesh', 'sms_rate' => '100', 'sms_unit' => 'SMS', 'call_rate' => '1.99', 'call_unit' => '$', 'bundle_info' => '0.55/min'],
                ['carrier_name' => 'Bangladesh', 'sms_rate' => '400', 'sms_unit' => 'Call', 'call_rate' => '7.49', 'call_unit' => '$', 'bundle_info' => '0.50/min']
            ]
        ],
        [
            'country' => [
                'country_name' => 'Cuba',
                'country_code' => 'CU',
                'last_update' => date('Y-m-d'),
                'expiry_info' => '30 Days Limit',
                'connection_fee_note' => 'A connection fee applies'
            ],
            'carriers' => [
                ['carrier_name' => 'Cuba', 'sms_rate' => '0.026', 'sms_unit' => '/sms', 'call_rate' => '0.65', 'call_unit' => '$', 'bundle_info' => '0.65/min'],
                ['carrier_name' => 'Cuba', 'sms_rate' => '400', 'sms_unit' => 'Call', 'call_rate' => '7.49', 'call_unit' => '$', 'bundle_info' => '0.50/min']
            ]
        ]
    ];
}


?>
 <style>
        
/* Base Styles */
.header {
    background: linear-gradient(135deg, #3266ec 0%, #082878ff 100%);
    padding: 40px 20px 100px;
    text-align: center;
    position: relative;
}

.header h2 {
    color: white;
    font-size: 24px;
    font-weight: 400;
    margin-bottom: 10px;
}

.header h1 {
    color: white;
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 40px;
}

.search-box {
    background: linear-gradient(135deg, #3266ec 0%, #360da0ff 100%);
    max-width: 1200px;
    margin: -60px auto 0;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    position: relative;
    z-index: 10;
}

.search-box::before {
    content: '';
    position: absolute;
    bottom: -20px;
    left: 50px;
    width: 0;
    height: 0;
    border-left: 30px solid transparent;
    border-top: 20px solid #2910b9ff;
}

.search-inputs {
    display: flex;
    gap: 20px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.search-inputs input {
    flex: 1;
    min-width: 300px;
    padding: 15px 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    text-align: center;
    color: #999;
}

.search-inputs select {
    padding: 15px 40px 15px 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    background: white;
    cursor: pointer;
}

.content {
    max-width: 1400px;
    margin: 80px auto;
    padding: 0 20px;
}

.section-title {
    text-align: center;
    margin-bottom: 50px;
}

.section-title h2 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
}

.divider {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.divider::before,
.divider::after {
    content: '';
    width: 100px;
    height: 1px;
    background: #ddd;
}

.divider .diamond {
    width: 12px;
    height: 12px;
    background: #337ccfff;
    transform: rotate(45deg);
}

.alphabet-filter {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 40px;
}

.filter-title {
    text-align: center;
    color: #333;
    font-size: 16px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alphabet-nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.letter-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: #333;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.letter-btn:hover {
    background: #f5f5f5;
    color: #1a63b8ff;
}

.letter-btn.active {
    background: #e74c3c;
    color: white;
}

.card.hidden {
    display: none;
}

.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #3266ec 0%, #1b0981ff 100%);
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.country-info h3 {
    color: white;
    font-size: 28px;
    margin-bottom: 5px;
}

.country-info p {
    color: rgba(255,255,255,0.8);
    font-size: 12px;
}

.card-header select {
    padding: 8px 30px 8px 15px;
    border: 2px solid white;
    border-radius: 4px;
    background: transparent;
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.card-body {
    padding: 30px;
}

.calls-info {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.calls-info p {
    font-size: 14px;
    color: #666;
}

.calls-info .calls {
    color: #e74c3c;
    font-weight: bold;
}

.pricing-table {
    width: 100%;
}

.pricing-table thead th {
    padding: 15px 10px;
    text-align: left;
    font-size: 16px;
    color: #333;
    font-weight: bold;
    border-bottom: 2px solid #eee;
}

.pricing-table tbody td {
    padding: 20px 10px;
    border-bottom: 1px solid #f5f5f5;
}

.pricing-table tbody tr:last-child td {
    border-bottom: none;
}

.carrier {
    color: #333;
    font-size: 14px;
}

.duration {
    color: #666;
    font-size: 12px;
}

.sms-amount {
    color: #e74c3c;
    font-weight: bold;
    font-size: 16px;
}

.call-amount {
    color: #e74c3c;
    font-weight: bold;
    font-size: 16px;
}

.price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 18px;
}

.price-per-min {
    color: #666;
    font-size: 12px;
}

.footer-note {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #999;
    font-size: 12px;
}

/* ============================================
   MOBILE RESPONSIVE STYLES
   ============================================ */

/* Tablets and below (768px) */
@media (max-width: 768px) {
    .header {
        padding: 30px 15px 80px;
    }

    .header h2 {
        font-size: 18px;
    }

    .header h1 {
        font-size: 28px;
        margin-bottom: 30px;
    }

    .search-box {
        margin: -40px 15px 0;
        padding: 25px 20px;
    }

    .search-box::before {
        left: 30px;
        border-left: 20px solid transparent;
        border-top: 15px solid #2910b9ff;
    }

    .search-inputs {
        flex-direction: column;
        gap: 15px;
    }

    .search-inputs input,
    .search-inputs select {
        width: 100%;
        min-width: auto;
        padding: 12px 15px;
        font-size: 14px;
    }

    .content {
        margin: 60px auto;
        padding: 0 15px;
    }

    .section-title h2 {
        font-size: 24px;
    }

    .divider::before,
    .divider::after {
        width: 50px;
    }

    .alphabet-filter {
        padding: 20px 15px;
    }

    .filter-title {
        font-size: 14px;
    }

    .alphabet-nav {
        gap: 6px;
    }

    .letter-btn {
        width: 32px;
        height: 32px;
        font-size: 13px;
    }

    .cards-container {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-top: 30px;
    }

    .card-header {
        padding: 20px;
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .card-header-left {
        flex-direction: column;
        gap: 15px;
        width: 100%;
    }

    .country-info h3 {
        font-size: 22px;
    }

    .card-body {
        padding: 20px;
    }

    .pricing-table thead th {
        padding: 10px 5px;
        font-size: 14px;
    }

    .pricing-table tbody td {
        padding: 15px 5px;
    }

    .carrier {
        font-size: 13px;
    }

    .sms-amount,
    .call-amount {
        font-size: 14px;
    }

    .price-per-min {
        font-size: 11px;
    }

    .footer-note {
        font-size: 11px;
    }
}

/* Small mobile devices (480px) */
@media (max-width: 480px) {
    .header {
        padding: 20px 10px 70px;
    }

    .header h2 {
        font-size: 16px;
        margin-bottom: 8px;
    }

    .header h1 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    .search-box {
        margin: -35px 10px 0;
        padding: 20px 15px;
    }

    .search-inputs input,
    .search-inputs select {
        padding: 10px 12px;
        font-size: 13px;
    }

    .content {
        margin: 50px auto;
        padding: 0 10px;
    }

    .section-title {
        margin-bottom: 30px;
    }

    .section-title h2 {
        font-size: 20px;
    }

    .divider::before,
    .divider::after {
        width: 30px;
    }

    .divider .diamond {
        width: 10px;
        height: 10px;
    }

    .alphabet-filter {
        padding: 15px 10px;
    }

    .filter-title {
        font-size: 13px;
        margin-bottom: 15px;
    }

    .alphabet-nav {
        gap: 4px;
    }

    .letter-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }

    .cards-container {
        gap: 15px;
        margin-top: 25px;
    }

    .card-header {
        padding: 15px;
    }

    .country-info h3 {
        font-size: 20px;
    }

    .country-info p {
        font-size: 11px;
    }

    .card-body {
        padding: 15px;
    }

    .calls-info {
        margin-bottom: 15px;
        padding-bottom: 15px;
    }

    .calls-info p {
        font-size: 13px;
    }

    .pricing-table {
        font-size: 12px;
    }

    .pricing-table thead th {
        padding: 8px 3px;
        font-size: 13px;
    }

    .pricing-table tbody td {
        padding: 12px 3px;
    }

    .carrier {
        font-size: 12px;
    }

    .sms-amount,
    .call-amount {
        font-size: 13px;
    }

    .price {
        font-size: 16px;
    }

    .price-per-min {
        font-size: 10px;
    }

    .footer-note {
        margin-top: 15px;
        padding-top: 15px;
        font-size: 10px;
    }

    /* Make flag smaller on mobile */
    .fi {
        height: 50px !important;
        width: 70px !important;
    }

    /* Make table more compact on very small screens */
    .pricing-table thead th:first-child,
    .pricing-table tbody td:first-child {
        padding-left: 0;
    }

    .pricing-table thead th:last-child,
    .pricing-table tbody td:last-child {
        padding-right: 0;
    }
}

/* Very small devices (360px) */
@media (max-width: 360px) {
    .header h1 {
        font-size: 18px;
    }

    .section-title h2 {
        font-size: 18px;
    }

    .country-info h3 {
        font-size: 18px;
    }

    .letter-btn {
        width: 26px;
        height: 26px;
        font-size: 11px;
    }

    .fi {
        height: 40px !important;
        width: 60px !important;
    }
}
    </style>

    <div class="header">
        <h2>Call Your Love</h2>
        <h1>Ones Family And Friends</h1>
    </div>

    <div class="search-box">
        <div class="search-inputs">
            <input type="text" placeholder="Where Do You Want To Call?">
           <select id="countrySelect">
                <option value="">Select a country</option>
                <?php foreach ($ratesData as $item): 
                    $country = $item['country'];
                ?>
                    <option value="<?= htmlspecialchars($country['country_name']) ?>">
                        <?= htmlspecialchars($country['country_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>
    </div>

    <div class="content">
        <div class="section-title">
            <h2>Pay As You Go Credit</h2>
            <div class="divider">
                <div class="diamond"></div>
            </div>
        </div>

        <div class="alphabet-filter">
            <p class="filter-title">Browse All Prices By Country:</p>
            <div class="alphabet-nav">
                <button class="letter-btn active" data-letter="A">A</button>
                <button class="letter-btn" data-letter="B">B</button>
                <button class="letter-btn" data-letter="C">C</button>
                <button class="letter-btn" data-letter="D">D</button>
                <button class="letter-btn" data-letter="E">E</button>
                <button class="letter-btn" data-letter="F">F</button>
                <button class="letter-btn" data-letter="G">G</button>
                <button class="letter-btn" data-letter="H">H</button>
                <button class="letter-btn" data-letter="I">I</button>
                <button class="letter-btn" data-letter="J">J</button>
                <button class="letter-btn" data-letter="K">K</button>
                <button class="letter-btn" data-letter="L">L</button>
                <button class="letter-btn" data-letter="M">M</button>
                <button class="letter-btn" data-letter="N">N</button>
                <button class="letter-btn" data-letter="O">O</button>
                <button class="letter-btn" data-letter="P">P</button>
                <button class="letter-btn" data-letter="Q">Q</button>
                <button class="letter-btn" data-letter="R">R</button>
                <button class="letter-btn" data-letter="S">S</button>
                <button class="letter-btn" data-letter="T">T</button>
                <button class="letter-btn" data-letter="U">U</button>
                <button class="letter-btn" data-letter="V">V</button>
                <button class="letter-btn" data-letter="W">W</button>
                <button class="letter-btn" data-letter="X">X</button>
                <button class="letter-btn" data-letter="Y">Y</button>
                <button class="letter-btn" data-letter="Z">Z</button>
            </div>
        </div>

        <div class="cards-container">
            <?php foreach ($ratesData as $item): 
                $country = $item['country'];
                $carriers = $item['carriers'];
                $flagClass = strtolower($country['country_code']);
                $lastUpdate = !empty($country['last_update']) ? date('M d', strtotime($country['last_update'])) : 'N/A';
            ?>
            <div class="card" data-country="<?= htmlspecialchars($country['country_name']) ?>">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="fi fi-<?= $flagClass ?>" style="height:70px; width:100px; border-radius:4px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.2);"></span>
                        <div class="country-info">
                            <h3><?= htmlspecialchars($country['country_name']) ?></h3>
                            <p>*Last update <?= $lastUpdate ?></p>
                        </div>
                    </div>
                    
                </div>
                <div class="card-body">
                    <div class="calls-info">
                        <p><span class="calls">Calls</span> (<?= htmlspecialchars($country['expiry_info']) ?>)</p>
                    </div>
                    <table class="pricing-table">
                        <thead>
                            <tr>
                                <th>Carriers</th>
                                <th>SMS</th>
                                <th>Call</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carriers as $carrier): ?>
                            <tr>
                                <td>
                                    <div class="carrier"><?= htmlspecialchars($carrier['carrier_name']) ?></div>
                                </td>
                                <td>
                                    <div class="sms-amount"><?= htmlspecialchars($carrier['sms_rate']) ?></div>
                                </td>
                                <td>
                                    <div class="call-amount"><?= htmlspecialchars($carrier['call_rate']) ?></div>
                                    <div class="price-per-min"><?= htmlspecialchars($carrier['bundle_info']) ?></div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="footer-note">
                        <?= !empty($country['connection_fee_note']) ? htmlspecialchars($country['connection_fee_note']) : 'spacer* A connection fee applies' ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Get all letter buttons and cards
        const letterBtns = document.querySelectorAll('.letter-btn');
        const cards = document.querySelectorAll('.card');

        // Add click event to each letter button
        letterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const selectedLetter = this.getAttribute('data-letter');
                
                // Remove active class from all buttons
                letterBtns.forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Filter cards
                cards.forEach(card => {
                    const countryName = card.getAttribute('data-country');
                    
                    if (countryName && countryName.charAt(0).toUpperCase() === selectedLetter) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    <script>
    const countrySelect = document.getElementById('countrySelect');
 const sectionTitle = document.querySelector('.section-title');
    const alphabetFilter = document.querySelector('.alphabet-filter');
    countrySelect.addEventListener('change', function() {
        const selectedCountry = this.value;
        cards.forEach(card => {
            const countryName = card.getAttribute('data-country');
            if (!selectedCountry || countryName === selectedCountry) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
              if (selectedCountry) {
            sectionTitle.style.display = 'none';
            alphabetFilter.style.display = 'none';
        } else {
            sectionTitle.style.display = 'block';
            alphabetFilter.style.display = 'block';
        }
        });
    });
</script>


        <?php
        include 'footer.php';
        ?>