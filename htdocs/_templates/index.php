<?php
use Aether\Session;
use Aether\Database;

$isAuth = Session::isAuthenticated();
$currentUser = $isAuth ? Session::getUser() : null;
$username = $currentUser ? $currentUser->getUsername() : '';
$userEmail = $currentUser ? $currentUser->getEmail() : '';
$userPhone = '';

if ($currentUser) {
    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT phone FROM auth WHERE id = ? LIMIT 1");
        $stmt->execute([$currentUser->getID()]);
        $userPhone = $stmt->fetchColumn() ?: '';
    } catch (\Throwable $e) {}
}
?>

<!-- Access Gate (Identity Verification) -->
<div class="gate-overlay" id="gateOverlay">
  <div class="gate-card">
    <div class="gate-icon-badge">
      <svg class="icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <h2>சரிபார்ப்பாளர் விவரம் / Verifier Details</h2>
    <p class="gate-sub">தொடர்வதற்கு முன் உங்கள் தொலைபேசி எண் மற்றும் ஜிமெயில் ஐடியை உள்ளிடவும் / Enter your phone number and Gmail ID before you can access this list</p>
    <label>தொலைபேசி எண் / Phone number</label>
    <input id="gatePhone" type="tel" inputmode="numeric" placeholder="9876543210" maxlength="15" value="<?= htmlspecialchars($userPhone, ENT_QUOTES) ?>">
    <label>ஜிமெயில் / Gmail</label>
    <input id="gateEmail" type="email" placeholder="name@gmail.com" value="<?= htmlspecialchars($userEmail, ENT_QUOTES) ?>">
    <div class="gate-error" id="gateError"></div>
    <button class="gate-submit" onclick="submitGate()">தொடரவும் / Continue</button>

    <?php if (!$isAuth): ?>
    <div class="gate-auth-link">
      ஏற்கனவே கணக்கு உள்ளதா? <a href="<?= get_config('base_path') ?>login">உள்நுழைக / Sign In</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Non-Sticky Overview Hero Header -->
<div class="top-hero" id="topHero">
  <div class="hero-inner">
    <div class="title-row">
      <div class="title-block">
        <h1>பணியாளர் சரிபார்ப்பு பதிவேடு</h1>
        <span class="bi-sub">Staff Verification Register</span>
      </div>
      <div class="hero-actions">
        <?php if ($isAuth): ?>
        <span class="user-badge-chip" title="Logged in as <?= htmlspecialchars($username) ?>">
          <svg class="icon-svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span><?= htmlspecialchars($username) ?></span>
        </span>
        <?php else: ?>
        <a href="<?= get_config('base_path') ?>login" class="login-header-btn" title="Sign In">
          <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
          <span>உள்நுழை</span>
        </a>
        <?php endif; ?>

        <button class="theme-btn" onclick="toggleTheme()" aria-label="Toggle Dark / Light Theme" title="Toggle Dark / Light Theme">
          <svg class="icon-svg theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
          <svg class="icon-svg theme-icon-moon" style="display:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
        </button>

        <button class="menu-btn" onclick="openSheet()">
          <svg class="icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
          <span>மெனு / Menu</span>
        </button>
      </div>
    </div>

    <!-- Live Database Stats Card (Collapsible) -->
    <div class="stats-card" id="statsCard">
      <div class="stats-strip">
        <div class="stat"><b id="statColleges">0</b><span>கல்லூரிகள்<br>Colleges</span></div>
        <div class="stat"><b id="statTotal">0</b><span>பணியாளர்<br>Staff</span></div>
        <div class="stat yes"><b id="statYes">0</b><span>ஆம்<br>Yes</span></div>
        <div class="stat no"><b id="statNo">0</b><span>இல்லை<br>No</span></div>
        <div class="stat pending"><b id="statPending">0</b><span>நிலுவை<br>Pending</span></div>
      </div>
      <div class="progress-bar"><span class="seg-yes" id="segYes"></span><span class="seg-no" id="segNo"></span></div>
    </div>
  </div>
</div>

<!-- Ultra-Compact Sticky Navigation Toolbar -->
<header class="sticky-nav" id="stickyNav">
  <div class="nav-inner">
    <div class="search-row">
      <svg class="search-icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input id="search" type="text" placeholder="தேடவும் / Search staff, college, code, designation…" autocomplete="off">
    </div>
    <div class="chips-wrap">
      <div class="chips">
        <button class="chip active" data-f="all" onclick="setFilter('all')">அனைத்தும் / All</button>
        <button class="chip" data-f="pending" onclick="setFilter('pending')">நிலுவை / Pending</button>
        <button class="chip" data-f="yes" onclick="setFilter('yes')">ஆம் / Yes</button>
        <button class="chip" data-f="no" onclick="setFilter('no')">இல்லை / No</button>
      </div>
      <button class="stats-toggle-btn" onclick="toggleStatsCard()" title="புள்ளிவிவரம் காட்டு/மறை / Toggle Stats" id="statsToggleBtn">
        <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
        <span class="btn-text">விவரம்</span>
      </button>
    </div>
  </div>
</header>

<!-- Main College List -->
<main id="list">
  <div class="empty-msg" id="loadingMsg">
    <svg class="icon-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
    <div style="margin-top:8px;">தரவு ஏற்றப்படுகிறது / Loading live database records...</div>
  </div>
</main>

<!-- Bottom Fixed Action Bar -->
<footer class="bottom-actions">
  <button onclick="exportCSV()">
    <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
    <span>CSV ஏற்றுமதி<br>Export CSV</span>
  </button>
  <button class="primary" onclick="scrollToFirstPending()">
    <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 4 15 12 5 20 5 4"/><line x1="19" x2="19" y1="5" y2="19"/></svg>
    <span>அடுத்த நிலுவை<br>Next Pending</span>
  </button>
</footer>

<!-- Bottom Sheet Menu -->
<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet()"></div>
<div class="sheet" id="sheet">
  <div class="sheet-header">
    <h3>மெனு / Menu</h3>
    <button class="sheet-close-btn" onclick="closeSheet()" aria-label="Close menu">
      <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  </div>

  <?php if ($isAuth): ?>
  <div style="padding: 10px 8px 12px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid var(--line-soft);">
    👤 உள்நுழைந்துள்ளீர்கள்: <b style="color:var(--text-primary);"><?= htmlspecialchars($username) ?></b> (<?= htmlspecialchars($userEmail) ?>)
  </div>
  <a class="sheet-item" href="<?= get_config('base_path') ?>admin">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
    <span>நிர்வாக பலகை / Admin Dashboard</span>
  </a>
  <a class="sheet-item" href="<?= get_config('base_path') ?>?logout=1">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
    <span>வெளியேறு / Logout</span>
  </a>
  <?php else: ?>
  <a class="sheet-item" href="<?= get_config('base_path') ?>login">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
    <span>உள்நுழைய / Sign In</span>
  </a>
  <a class="sheet-item" href="<?= get_config('base_path') ?>signup">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" x2="20" y1="8" y2="14"/><line x1="23" x2="17" y1="11" y2="11"/></svg>
    <span>புதிய கணக்கு / Register</span>
  </a>
  <?php endif; ?>

  <button class="sheet-item" onclick="toggleStatsCard(); closeSheet();">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
    <span>புள்ளிவிவரத்தை காட்டு / மறை / Toggle Stats</span>
  </button>
  <button class="sheet-item" onclick="toggleTheme();">
    <svg class="icon-svg theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
    <svg class="icon-svg theme-icon-moon" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    <span id="themeSheetText">லைட் பயன்முறை / Switch to Light Mode</span>
  </button>
  <button class="sheet-item" onclick="exportCSV()">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
    <span>முழு CSV ஏற்றுமதி / Export full CSV</span>
  </button>
  <button class="sheet-item" onclick="exportPendingCSV()">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><circle cx="9" cy="11" r="1" fill="currentColor"/><circle cx="9" cy="16" r="1" fill="currentColor"/></svg>
    <span>நிலுவை மட்டும் CSV / Export pending-only CSV</span>
  </button>
  <button class="sheet-item" onclick="changeVerifierInfo()">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    <span>சரிபார்ப்பாளர் தகவலை மாற்று / Change verifier info</span>
  </button>
  <button class="sheet-item danger" onclick="resetAll()">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
    <span>அனைத்தையும் அழி / Reset all verification data</span>
  </button>
  <button class="sheet-item" onclick="closeSheet()">
    <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    <span>மூடு / Close</span>
  </button>
</div>

<!-- Verification View Modal -->
<div class="verify-view" id="verifyView">
  <div class="verify-head">
    <div class="verify-head-row">
      <button class="verify-back" onclick="closeVerify()">
        <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        <span>பட்டியல் / List</span>
      </button>
      <div class="verify-head-text">
        <span class="vcode" id="verifyCode"></span>
        <span class="vname" id="verifyName"></span>
      </div>
    </div>
    <div class="verify-progress-wrap">
      <div class="verify-page-label" id="verifyPage"></div>
      <div class="progress-bar"><span class="seg-yes" id="verifyProgressBar" style="background:var(--gradient-teal)"></span></div>
    </div>
  </div>
  <div class="verify-body">
    <div class="verify-question">
      <span class="ta">இந்த பணியாளர் தற்போது இந்த கல்லூரியில் பணிபுரிகிறாரா? ஒவ்வொருவருக்கும் 'ஆம்' அல்லது 'இல்லை' என்பதைத் தேர்வு செய்யவும்.</span>
      <span class="en">Is this staff member currently working here? Select YES or NO for each staff member.</span>
    </div>
    <div class="verify-cols">
      <span class="vinfo-sp"></span>
      <span class="vcol">ஆம்<br>YES</span>
      <span class="vcol">இல்லை<br>NO</span>
    </div>
    <div id="verifyBody"></div>
    <div class="vbody-spacer" style="height:44px; width:100%;"></div>
  </div>
  <div class="verify-foot">
    <div class="verify-foot-row">
      <button id="btnPrevCollege" onclick="navigateVerify(-1)">
        <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        <span>முந்தைய<br>Previous</span>
      </button>
      <button class="primary" onclick="submitVerify()">
        <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <span>சமர்ப்பி<br>Submit</span>
      </button>
      <button id="btnNextCollege" onclick="navigateVerify(1)">
        <span>அடுத்தது<br>Next</span>
        <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </button>
    </div>
    <button class="verify-clear-link" onclick="clearThisCollege()">
      <svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
      <span>படிவத்தை அழி / Clear this college's answers</span>
    </button>
  </div>
</div>

<!-- Client Controller Script -->
<script>
let COLLEGES = [];
let currentFilter = 'all';
let currentSearch = '';
let currentVerifyIndex = -1;
let currentStaffList = [];
let verifyAnswers = {}; // { sno: 'yes'|'no' }
let verifierInfo = null;

// --- Theme Management ---
function initTheme() {
  const current = document.documentElement.getAttribute('data-theme') || 'dark';
  updateThemeUI(current);
}

function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  try { localStorage.setItem('appTheme', next); } catch(e){}
  updateThemeUI(next);
}

function updateThemeUI(theme) {
  const sunIcons = document.querySelectorAll('.theme-icon-sun');
  const moonIcons = document.querySelectorAll('.theme-icon-moon');
  const sheetText = document.getElementById('themeSheetText');
  
  if (theme === 'light') {
    sunIcons.forEach(el => el.style.display = 'none');
    moonIcons.forEach(el => el.style.display = 'inline-block');
    if (sheetText) sheetText.textContent = 'டார்க் பயன்முறை / Switch to Dark Mode';
  } else {
    sunIcons.forEach(el => el.style.display = 'inline-block');
    moonIcons.forEach(el => el.style.display = 'none');
    if (sheetText) sheetText.textContent = 'லைட் பயன்முறை / Switch to Light Mode';
  }
}

// --- Stats Card Visibility ---
function toggleStatsCard() {
  const card = document.getElementById('statsCard');
  const btn = document.getElementById('statsToggleBtn');
  if (!card) return;
  card.classList.toggle('collapsed');
  const isCollapsed = card.classList.contains('collapsed');
  if (btn) btn.classList.toggle('active', !isCollapsed);
  try { localStorage.setItem('staffStatsCollapsed', isCollapsed ? '1' : '0'); } catch(e){}
}

function initStatsVisibility() {
  try {
    const isCollapsed = localStorage.getItem('staffStatsCollapsed') === '1';
    const card = document.getElementById('statsCard');
    const btn = document.getElementById('statsToggleBtn');
    if (card && isCollapsed) card.classList.add('collapsed');
    if (btn) btn.classList.toggle('active', !isCollapsed);
  } catch(e){}
}

// --- Access Gate ---
function checkGate() {
  // If user is authenticated via framework login session, bypass gate
  if (window.IS_AUTHENTICATED && window.CURRENT_USER) {
    verifierInfo = {
      phone: document.getElementById('gatePhone').value || '',
      email: window.CURRENT_USER.email || '',
      auth_user: window.CURRENT_USER.username
    };
    document.getElementById('gateOverlay').style.display = 'none';
    return;
  }

  try {
    const saved = localStorage.getItem('staffVerifierInfo');
    if (saved) {
      verifierInfo = JSON.parse(saved);
      if (verifierInfo.phone && verifierInfo.email) {
        document.getElementById('gateOverlay').style.display = 'none';
        return;
      }
    }
  } catch(e){}
  document.getElementById('gateOverlay').style.display = 'flex';
}

function submitGate() {
  const phone = document.getElementById('gatePhone').value.trim();
  const email = document.getElementById('gateEmail').value.trim();
  const err = document.getElementById('gateError');
  err.textContent = '';

  const cleanPhone = phone.replace(/[^0-9]/g, '');
  if (cleanPhone.length < 10) {
    err.textContent = 'சரியான தொலைபேசி எண்ணை உள்ளிடவும் (குறைந்தது 10 இலக்கங்கள்) / Enter valid phone number';
    return;
  }
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRe.test(email) || !email.toLowerCase().includes('gmail.com')) {
    err.textContent = 'சரியான ஜிமெயில் ஐடியை உள்ளிடவும் / Enter a valid Gmail ID';
    return;
  }

  verifierInfo = { phone: cleanPhone, email: email };
  try { localStorage.setItem('staffVerifierInfo', JSON.stringify(verifierInfo)); } catch(e){}
  document.getElementById('gateOverlay').style.display = 'none';
}

function changeVerifierInfo() {
  closeSheet();
  try { localStorage.removeItem('staffVerifierInfo'); } catch(e){}
  document.getElementById('gateOverlay').style.display = 'flex';
}

// --- Data Loading from MySQL API ---
async function loadColleges() {
  const list = document.getElementById('list');
  try {
    const res = await ApiClient.get('verify', 'colleges', { search: currentSearch, filter: currentFilter });
    if (res && res.status === 'success') {
      COLLEGES = res.colleges || [];
      renderCollegeList();
    }
  } catch (err) {
    console.error('Error loading colleges:', err);
    list.innerHTML = `<div class="empty-msg" style="color:var(--red);">தரவு ஏற்றுவதில் பிழை: ${err.message}<br><button onclick="loadColleges()" style="margin-top:10px;padding:6px 14px;border-radius:6px;background:var(--teal);color:#fff;border:none;cursor:pointer;">மீண்டும் முயல்க / Retry</button></div>`;
  }
}

async function loadStats() {
  try {
    const stats = await ApiClient.get('verify', 'stats');
    if (stats) {
      document.getElementById('statColleges').textContent = Number(stats.colleges).toLocaleString();
      document.getElementById('statTotal').textContent = Number(stats.staff).toLocaleString();
      document.getElementById('statYes').textContent = Number(stats.yes).toLocaleString();
      document.getElementById('statNo').textContent = Number(stats.no).toLocaleString();
      document.getElementById('statPending').textContent = Number(stats.pending).toLocaleString();

      const total = stats.staff || 1;
      const yPct = ((stats.yes / total) * 100).toFixed(1);
      const nPct = ((stats.no / total) * 100).toFixed(1);
      document.getElementById('segYes').style.width = yPct + '%';
      document.getElementById('segNo').style.width = nPct + '%';
    }
  } catch(e) {
    console.error('Error loading stats:', e);
  }
}

function renderCollegeList() {
  const list = document.getElementById('list');
  if (!COLLEGES.length) {
    list.innerHTML = `<div class="empty-msg">கல்லூரிகள் எதுவும் கிடைக்கவில்லை / No colleges match your query</div>`;
    return;
  }

  let html = '';
  COLLEGES.forEach((c, idx) => {
    let badgeClass = '';
    let badgeText = '';

    if (c.pending_count === 0 && c.total_staff > 0) {
      badgeClass = 'done';
      badgeText = `முழுமை / Complete (${c.total_staff}/${c.total_staff})`;
    } else if (c.yes_count > 0 || c.no_count > 0) {
      badgeClass = 'some';
      badgeText = `${c.yes_count + c.no_count}/${c.total_staff} முடிந்தது`;
    } else {
      badgeText = `நிலுவை / ${c.total_staff}`;
    }

    html += `
      <div class="college-row" onclick="openVerify(${idx})" data-idx="${idx}">
        <div class="sum-text">
          <span class="cname">${escapeHtml(c.name)}</span>
          <span class="ccode">#${escapeHtml(c.code)}</span>
        </div>
        <div class="badge ${badgeClass}">${badgeText}</div>
        <div class="chev">
          <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </div>
      </div>
    `;
  });

  list.innerHTML = html;
}

// --- Verification Modal Page ---
async function openVerify(idx) {
  if (idx < 0 || idx >= COLLEGES.length) return;
  currentVerifyIndex = idx;
  const c = COLLEGES[idx];

  document.getElementById('verifyCode').textContent = '#' + c.code;
  document.getElementById('verifyName').textContent = c.name;
  document.getElementById('verifyPage').textContent = `கல்லூரி ${idx + 1} / ${COLLEGES.length}`;
  document.getElementById('verifyProgressBar').style.width = `${((idx + 1) / COLLEGES.length) * 100}%`;
  
  document.getElementById('btnPrevCollege').disabled = (idx === 0);
  document.getElementById('btnNextCollege').disabled = (idx === COLLEGES.length - 1);

  const bodyEl = document.getElementById('verifyBody');
  bodyEl.innerHTML = `<div class="empty-msg"><svg class="icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg><div style="margin-top:6px;">பணியாளர் பட்டியல் பெறப்படுகிறது...</div></div>`;

  document.getElementById('verifyView').classList.add('open');

  try {
    const res = await ApiClient.get('verify', 'college_staff', { code: c.code });
    if (res && res.status === 'success') {
      currentStaffList = res.staff || [];
      verifyAnswers = {};
      currentStaffList.forEach(s => {
        if (s.status) verifyAnswers[s.sno] = s.status;
      });
      renderVerifyStaffRows();
    }
  } catch (err) {
    bodyEl.innerHTML = `<div class="empty-msg" style="color:var(--red);">பணியாளர் தகவலைப் பெற முடியவில்லை: ${err.message}</div>`;
  }
}

function renderVerifyStaffRows() {
  const bodyEl = document.getElementById('verifyBody');
  if (!currentStaffList.length) {
    bodyEl.innerHTML = `<div class="empty-msg">இந்தப் பட்டியலில் பணியாளர்கள் இல்லை / No staff members found</div>`;
    return;
  }

  let html = '';
  currentStaffList.forEach((s, idx) => {
    const curAns = verifyAnswers[s.sno] || '';
    const isYes = curAns === 'yes';
    const isNo = curAns === 'no';
    const rowStatusAttr = curAns ? `data-status="${curAns}"` : '';

    html += `
      <div class="vrow" id="vrow_${s.sno}" ${rowStatusAttr}>
        <div class="vinfo">
          <span class="vnum">#${idx + 1}</span>
          <span class="vname">${escapeHtml(s.name)}</span>
          ${s.designation ? `<span class="vdesig">${escapeHtml(s.designation)}</span>` : ''}
        </div>
        <div class="vradios">
          <label class="vradio" title="ஆம் / YES">
            <input type="radio" name="staff_opt_${s.sno}" value="yes" ${isYes ? 'checked' : ''} onchange="setVerifyStatus(${s.sno}, 'yes')">
          </label>
          <label class="vradio" title="இல்லை / NO">
            <input type="radio" name="staff_opt_${s.sno}" value="no" ${isNo ? 'checked' : ''} onchange="setVerifyStatus(${s.sno}, 'no')">
          </label>
        </div>
      </div>
    `;
  });

  bodyEl.innerHTML = html;
}

function setVerifyStatus(sno, status) {
  verifyAnswers[sno] = status;
  const row = document.getElementById(`vrow_${sno}`);
  if (row) {
    row.setAttribute('data-status', status);
  }
}

async function submitVerify() {
  const c = COLLEGES[currentVerifyIndex];
  if (!c) return;

  const phone = verifierInfo ? verifierInfo.phone : '';
  const email = verifierInfo ? verifierInfo.email : '';

  try {
    const res = await ApiClient.post('verify', 'submit_college', {
      code: c.code,
      answers: verifyAnswers,
      phone: phone,
      email: email
    });

    if (typeof toast !== 'undefined' && toast.success) {
      toast.success('சமர்ப்பிக்கப்பட்டது / Submitted', `${c.code} சரிபார்ப்பு சேமிக்கப்பட்டது.`);
    }

    // Refresh current college and stats
    await loadStats();
    await loadColleges();

    // Navigate to next or close
    if (currentVerifyIndex < COLLEGES.length - 1) {
      openVerify(currentVerifyIndex + 1);
    } else {
      closeVerify();
    }
  } catch (err) {
    if (typeof toast !== 'undefined' && toast.error) {
      toast.error('பிழை / Error', err.message || 'சேமிக்க முடியவில்லை');
    } else {
      alert('Error: ' + err.message);
    }
  }
}

async function clearThisCollege() {
  const c = COLLEGES[currentVerifyIndex];
  if (!c) return;
  if (!confirm(`கல்லூரி #${c.code}-ன் அனைத்து பதில்களையும் அழிக்க விரும்புகிறீர்களா? / Clear all answers for this college?`)) {
    return;
  }

  try {
    await ApiClient.post('verify', 'clear_college', { code: c.code });
    verifyAnswers = {};
    renderVerifyStaffRows();
    await loadStats();
    await loadColleges();
    if (typeof toast !== 'undefined' && toast.success) {
      toast.success('அழிக்கப்பட்டது', 'இந்த கல்லூரிக்கான பதில்கள் அழிக்கப்பட்டன.');
    }
  } catch(err) {
    alert('Failed to clear: ' + err.message);
  }
}

function closeVerify() {
  document.getElementById('verifyView').classList.remove('open');
  currentVerifyIndex = -1;
  currentStaffList = [];
  verifyAnswers = {};
}

function navigateVerify(dir) {
  const target = currentVerifyIndex + dir;
  if (target >= 0 && target < COLLEGES.length) {
    openVerify(target);
  }
}

// --- Search & Filters ---
let searchDebounceTimer = null;
document.getElementById('search').addEventListener('input', (e) => {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    currentSearch = e.target.value.trim();
    loadColleges();
  }, 250);
});

function setFilter(f) {
  currentFilter = f;
  document.querySelectorAll('.chips .chip').forEach(b => {
    b.classList.toggle('active', b.getAttribute('data-f') === f);
  });
  loadColleges();
}

function scrollToFirstPending() {
  const pendingIdx = COLLEGES.findIndex(c => c.pending_count > 0);
  if (pendingIdx !== -1) {
    openVerify(pendingIdx);
  } else {
    if (typeof toast !== 'undefined' && toast.info) {
      toast.info('அனைத்தும் சரிபார்க்கப்பட்டது!', 'நிலுவையில் உள்ள கல்லூரிகள் எதுவும் இல்லை.');
    } else {
      alert('All colleges are verified! No pending items.');
    }
  }
}

// --- CSV Exports ---
function exportCSV() {
  const base = window.APP_BASE_PATH || '/staff-verifier/htdocs/';
  window.location.href = base.replace(/\/?$/, '/') + 'api/verify/export';
}

function exportPendingCSV() {
  const base = window.APP_BASE_PATH || '/staff-verifier/htdocs/';
  window.location.href = base.replace(/\/?$/, '/') + 'api/verify/export?pending=1';
}

async function resetAll() {
  closeSheet();
  if (!confirm('எச்சரிக்கை: அனைத்து கல்லூரிகளின் சரிபார்ப்புத் தரவுகளும் நிரந்தரமாக அழிக்கப்படும். தொடரவா?\nWARNING: Reset all verification data?')) {
    return;
  }

  try {
    await ApiClient.post('verify', 'reset_all');
    if (typeof toast !== 'undefined' && toast.success) {
      toast.success('அனைத்தும் அழிக்கப்பட்டது', 'புதிய சரிபார்ப்பைத் தொடங்கலாம்.');
    }
    await loadStats();
    await loadColleges();
  } catch (err) {
    alert('Reset failed: ' + err.message);
  }
}

// --- Menu Sheet ---
function openSheet() {
  document.getElementById('sheetOverlay').classList.add('open');
  document.getElementById('sheet').classList.add('open');
}

function closeSheet() {
  document.getElementById('sheetOverlay').classList.remove('open');
  document.getElementById('sheet').classList.remove('open');
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  })[m]);
}

// --- Bootstrap ---
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initStatsVisibility();
  checkGate();
  loadStats();
  loadColleges();
});
</script>
