<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';
require __DIR__ . '/../config.php';

$notification = '';

/**
 * Save or Update Country + Carrier Rates
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_rate') {
    $country_id = !empty($_POST['country_id']) ? intval($_POST['country_id']) : null;
    
    // Split Name.Code if coming from select
    $parts = explode('.', $_POST['country_name']);
    $country_name = trim($parts[0]);
    $country_code = trim($parts[1] ?? '');
    
    $expiry_info = $_POST['expiry_info'] ?? 'No expiry date';
    $connection_fee_note = $_POST['connection_fee_note'] ?? null;

    try {
        // Check duplicate
        if ($country_id) {
            $checkStmt = $pdo->prepare("SELECT id FROM rates WHERE country_name=? AND id != ?");
            $checkStmt->execute([$country_name, $country_id]);
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM rates WHERE country_name=?");
            $checkStmt->execute([$country_name]);
        }

        if ($checkStmt->fetch()) {
            $notification = "<div class='alert alert-danger'>Error: Country name already exists!</div>";
        } else {
            if ($country_id) {
                // Update
                $stmt = $pdo->prepare("UPDATE rates SET country_name=?, country_code=?, last_update=NOW(), expiry_info=?, connection_fee_note=? WHERE id=?");
                $stmt->execute([$country_name, $country_code, $expiry_info, $connection_fee_note, $country_id]);
                $pdo->prepare("DELETE FROM rate_details WHERE rate_id=?")->execute([$country_id]);
                $rate_id = $country_id;
                $msg = "Country rates updated successfully!";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO rates (country_name, country_code, last_update, expiry_info, connection_fee_note) VALUES (?, ?, NOW(), ?, ?)");
                $stmt->execute([$country_name, $country_code, $expiry_info, $connection_fee_note]);
                $rate_id = $pdo->lastInsertId();
                $msg = "New country rates added successfully!";
            }

            // Save carriers
            if (!empty($_POST['carrier_name'])) {
                $carrier_names = $_POST['carrier_name'];
                $sms_rates = $_POST['sms_rate'];
                $call_rates = $_POST['call_rate'];
                $bundle_infos = $_POST['bundle_info'];

                $insert = $pdo->prepare("INSERT INTO rate_details (rate_id, carrier_name, sms_rate, call_rate, bundle_info) VALUES (?,?,?,?,?)");

                foreach ($carrier_names as $i => $carrier) {
                    if (!empty(trim($carrier))) {
                        $insert->execute([
                            $rate_id,
                            trim($carrier),
                            $sms_rates[$i] ?? null,
                            $call_rates[$i] ?? null,
                            $bundle_infos[$i] ?? null
                        ]);
                    }
                }
            }

            $notification = "<div class='alert alert-success'>$msg</div>";
        }
    } catch (PDOException $e) {
        $notification = "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/**
 * Delete Country + Carrier Rates
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_rate') {
    $country_id = $_POST['country_id'] ?? null;

    if ($country_id) {
        try {
            $pdo->prepare("DELETE FROM rate_details WHERE rate_id=?")->execute([$country_id]);
            $pdo->prepare("DELETE FROM rates WHERE id=?")->execute([$country_id]);
            $notification = "<div class='alert alert-success'>Country rates deleted successfully!</div>";
        } catch (PDOException $e) {
            $notification = "<div class='alert alert-danger'>Error deleting rates: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

/**
 * Fetch all countries with their carriers
 */
$countries = $pdo->query("SELECT * FROM rates ORDER BY country_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$ratesData = [];

foreach ($countries as $country) {
    $carriers = $pdo->prepare("SELECT * FROM rate_details WHERE rate_id=?");
    $carriers->execute([$country['id']]);
    $ratesData[] = [
        'country' => $country,
        'carriers' => $carriers->fetchAll(PDO::FETCH_ASSOC)
    ];
}
?>
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-category">Rates Management</h5>
          <h3 class="card-title">Country Calling & SMS Rates</h3>
          <div class="text-right">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#rateModal" onclick="resetRateModal()">+ Add Country</button>
          </div>
        </div>

        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
              <div class="card card-tasks">

                <div class="card-body">
                  <?= $notification ?>

          <div class="table-full-width table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Country</th>
                  <th>Last Update</th>
                  <th>Expiry Info</th>
                  <th>Connection Fee</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ratesData as $item): ?>
                  <tr>
                    <td>
                      <?php if (!empty($item['country']['country_code'])): ?>
                        <img src="https://flagcdn.com/w20/<?= strtolower($item['country']['country_code']) ?>.png" 
                             alt="<?= htmlspecialchars($item['country']['country_name']) ?>" 
                             class="country-flag">
                      <?php endif; ?>
                      <?= htmlspecialchars($item['country']['country_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($item['country']['last_update'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['country']['expiry_info']) ?></td>
                    <td><?= htmlspecialchars($item['country']['connection_fee_note'] ?? '-') ?></td>
                    <td class="text-right">
                      <button class="btn btn-link" data-toggle="modal"
                        data-target="#rateModal"
                        onclick='editRate(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                        <i class="tim-icons icon-pencil"></i>
                      </button>
                      <button class="btn btn-link text-danger" 
                        onclick='deleteRate(<?= $item["country"]["id"] ?>, "<?= htmlspecialchars($item["country"]["country_name"], ENT_QUOTES) ?>")'>
                        <i class="tim-icons icon-trash-simple"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


<!-- Rate Modal -->
<div class="modal fade" id="rateModal" tabindex="-1" role="dialog" aria-labelledby="rateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rateModalLabel">Manage Country Rates</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
<!-- Replace your entire <style> section with this -->
<style>
/* ============================================
   COMPLETE SELECT2 DARK THEME FIX
   ============================================ */

/* 1. Main Selection Box */
.select2-container--default .select2-selection--single {
    background-color: #2b3553 !important;
    border: 1px solid #2b3553 !important;
    border-radius: 0.4285rem !important;
    height: 40px !important;
    padding: 5px 12px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: rgba(255, 255, 255, 0.8) !important;
    line-height: 28px !important;
    padding: 0 !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    top: 1px !important;
    right: 3px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: rgba(255, 255, 255, 0.8) transparent transparent transparent !important;
    border-width: 5px 4px 0 4px !important;
    margin-left: -4px !important;
    margin-top: -2px !important;
}

.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent rgba(255, 255, 255, 0.8) transparent !important;
    border-width: 0 4px 5px 4px !important;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #1d8cf8 !important;
}

/* 2. Dropdown Container - CRITICAL */
span.select2-dropdown {
    background-color: #27293d !important;
    border: 1px solid #2b3553 !important;
    border-radius: 0.4285rem !important;
}

.select2-container--default.select2-container--open .select2-dropdown {
    background-color: #27293d !important;
}

/* 3. Search Box in Dropdown */
.select2-search--dropdown {
    padding: 10px !important;
    background-color: #27293d !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    background-color: #1e1e2f !important;
    border: 1px solid #2b3553 !important;
    color: rgba(255, 255, 255, 0.8) !important;
    border-radius: 0.4285rem !important;
    padding: 8px 12px !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #1d8cf8 !important;
    outline: none !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
    color: rgba(255, 255, 255, 0.4) !important;
}

/* 4. Results Container */
.select2-results {
    background-color: #27293d !important;
}

.select2-results__options {
    background-color: #27293d !important;
}

/* 5. Individual Result Options - MOST IMPORTANT */
.select2-container--default .select2-results__option {
    padding: 8px 12px !important;
    color: rgba(255, 255, 255, 0.8) !important;
    background-color: #27293d !important;
}

/* 6. Hover State */
.select2-container--default .select2-results__option--highlighted[aria-selected],
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: #1d8cf8 !important;
    color: #ffffff !important;
}

/* 7. Selected Option */
.select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #344675 !important;
    color: #ffffff !important;
}

.select2-container--default .select2-results__option--selected {
    background-color: #344675 !important;
    color: #ffffff !important;
}

/* 8. Disabled Option */
.select2-container--default .select2-results__option--disabled {
    color: rgba(255, 255, 255, 0.3) !important;
    background-color: #27293d !important;
}

/* 9. Messages (No results, Loading, etc) */
.select2-container--default .select2-results__message {
    color: rgba(255, 255, 255, 0.6) !important;
    background-color: #27293d !important;
}

/* 10. Clear Button */
.select2-container--default .select2-selection--single .select2-selection__clear {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 18px !important;
    margin-right: 8px !important;
}

/* 11. Z-index for Modal */
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

/* 12. Scrollbar Styling */
.select2-results__options::-webkit-scrollbar {
    width: 6px;
}

.select2-results__options::-webkit-scrollbar-track {
    background: #1e1e2f;
}

.select2-results__options::-webkit-scrollbar-thumb {
    background: #2b3553;
    border-radius: 3px;
}

.select2-results__options::-webkit-scrollbar-thumb:hover {
    background: #344675;
}

/* 13. Country Flag Styling */
img.country-flag {
    width: 20px;
    height: auto;
    margin-right: 8px;
    vertical-align: middle;
}

.select2-results__option img.country-flag,
.select2-selection__rendered img.country-flag {
    display: inline-block;
    margin-right: 8px;
    vertical-align: middle;
}
</style>
      <form action="" method="POST">
        <input type="hidden" name="action" value="save_rate">
        <input type="hidden" name="country_id" id="country_id">
        <input type="hidden" name="country_code" id="country_code">

        <div class="modal-body">
          <div class="form-group">
            <label>Country Name <span class="text-danger">*</span></label>
            <select name="country_name" id="country_select" class="form-control" required >
                <option value="">Select Country</option>
            </select>
          </div>

          <div class="form-group">
            <label>Expiry Info</label>
            <input type="text" class="form-control" name="expiry_info" id="expiry_info" placeholder="Calls (No expiry date)">
          </div>

          <div class="form-group">
            <label>Connection Fee Note</label>
            <input type="text" class="form-control" name="connection_fee_note" id="connection_fee_note">
          </div>

          <hr>
          <h5>Carrier Rates</h5>

          <div id="carrierContainer"></div>
          <button type="button" class="btn btn-info btn-sm mt-2" onclick="addCarrierRow()">+ Add Carrier</button>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="tim-icons icon-check-2 mr-1"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete_rate">
  <input type="hidden" name="country_id" id="delete_country_id">
</form>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>

let countriesData = [];

// Load countries from JSON
async function loadCountries() {
    try {
        const response = await fetch('countries.json');
        if (!response.ok) throw new Error('Failed to load countries.json');
        countriesData = await response.json();
        countriesData.sort((a, b) => {
            let nameA = a.name.split('.')[0];
            let nameB = b.name.split('.')[0];
            return nameA.localeCompare(nameB);
        });
    } catch (err) {
        console.error(err);
        alert('Failed to load countries list.');
        return;
    }

    const select = document.getElementById('country_select');
    select.innerHTML = '<option value="">Select Country</option>';

    countriesData.forEach(c => {
        const parts = c.name.split('.');
        const countryName = parts[0];
        const countryCode = parts[1] ?? '';
        const option = document.createElement('option');
        option.value = c.name; // Full "Name.Code" value
        option.textContent = countryName;
        option.setAttribute('data-code', countryCode);
        select.appendChild(option);
    });

    // Destroy old Select2
    if ($('#country_select').hasClass("select2-hidden-accessible")) {
        $('#country_select').select2('destroy');
    }

    $('#country_select').select2({
        placeholder: 'Search and select country',
        allowClear: true,
        dropdownParent: $('#rateModal'),
        width: '100%',
        theme: 'default',
        templateResult: formatCountryOption,
        templateSelection: formatCountryOption
    });

    // Update country_code input on change
    $('#country_select').off('change').on('change', function() {
        const selected = $(this).val();
        if (selected) {
            const parts = selected.split('.');
            document.getElementById('country_code').value = parts[1] ?? '';
            console.log('Selected:', parts[0], 'Code:', parts[1]);
        } else {
            document.getElementById('country_code').value = '';
        }
    });
}

// Format option with flag
function formatCountryOption(country) {
    if (!country.id) return country.text;
    const code = $(country.element).data('code');
    if (!code) return country.text;
    return $(`<span><img src="https://flagcdn.com/w20/${code.toLowerCase()}.png" class="country-flag" /> ${country.text}</span>`);
}

document.addEventListener('DOMContentLoaded', loadCountries);

function resetRateModal() {
    document.getElementById('country_id').value = '';
    document.getElementById('country_code').value = '';
    $('#country_select').val(null).trigger('change');
    document.getElementById('expiry_info').value = '';
    document.getElementById('connection_fee_note').value = '';
    document.getElementById('carrierContainer').innerHTML = '';
    document.getElementById('rateModalLabel').textContent = 'Add New Country Rates';
    addCarrierRow();
}

function addCarrierRow(data = {}) {
    const container = document.getElementById('carrierContainer');
    const row = document.createElement('div');
    row.classList.add('form-row', 'mb-2', 'carrier-row');
    row.innerHTML = `
        <div class="col">
            <input type="text" class="form-control" name="carrier_name[]" placeholder="Carrier Name" value="${escapeHtml(data.carrier_name||'')}">
        </div>
        <div class="col">
            <input type="text" class="form-control" name="sms_rate[]" placeholder="SMS Rate" value="${escapeHtml(data.sms_rate||'')}">
        </div>
        <div class="col">
            <input type="text" class="form-control" name="call_rate[]" placeholder="Call Rate" value="${escapeHtml(data.call_rate||'')}">
        </div>
        <div class="col">
            <input type="text" class="form-control" name="bundle_info[]" placeholder="Bundle Info" value="${escapeHtml(data.bundle_info||'')}">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.carrier-row').remove()">&times;</button>
        </div>
    `;
    container.appendChild(row);
}

function editRate(item) {
    const country = item.country;
    document.getElementById('country_id').value = country.id;
    document.getElementById('country_code').value = country.country_code || '';

    // Set Select2 value using Name.Code
    $('#country_select').val(country.country_name + '.' + country.country_code).trigger('change');

    document.getElementById('expiry_info').value = country.expiry_info || '';
    document.getElementById('connection_fee_note').value = country.connection_fee_note || '';
    document.getElementById('rateModalLabel').textContent = 'Edit Country Rates';

    const container = document.getElementById('carrierContainer');
    container.innerHTML = '';
    if (item.carriers && item.carriers.length > 0) {
        item.carriers.forEach(c => addCarrierRow(c));
    } else {
        addCarrierRow();
    }
}

function deleteRate(id, name) {
    if (confirm(`Are you sure you want to delete rates for "${name}"?\nThis will also delete all carrier rates.`)) {
        document.getElementById('delete_country_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function escapeHtml(text) {
    const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
    return String(text).replace(/[&<>"']/g, m=>map[m]);
}
</script>



<?php include 'includes/footer.php'; ?>