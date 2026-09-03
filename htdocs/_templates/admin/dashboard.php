<?php
use App\College;
$stats = College::getOverallStats();
?>
<section class="dashboard-stats" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="stat-card" style="background:var(--bg-card); padding:20px; border-radius:12px; border:1px solid var(--glass-border);">
        <h3 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Colleges / கல்லூரிகள்</h3>
        <p class="stat-value" style="font-size:26px; font-weight:700; color:var(--teal); margin-top:8px;"><?= number_format($stats['colleges']) ?></p>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:20px; border-radius:12px; border:1px solid var(--glass-border);">
        <h3 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Total Staff / மொத்த பணியாளர்</h3>
        <p class="stat-value" style="font-size:26px; font-weight:700; color:var(--text-primary); margin-top:8px;"><?= number_format($stats['staff']) ?></p>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:20px; border-radius:12px; border:1px solid var(--glass-border);">
        <h3 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Verified Yes / ஆம்</h3>
        <p class="stat-value" style="font-size:26px; font-weight:700; color:var(--green); margin-top:8px;"><?= number_format($stats['yes']) ?></p>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:20px; border-radius:12px; border:1px solid var(--glass-border);">
        <h3 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Verified No / இல்லை</h3>
        <p class="stat-value" style="font-size:26px; font-weight:700; color:var(--red); margin-top:8px;"><?= number_format($stats['no']) ?></p>
    </div>
    <div class="stat-card" style="background:var(--bg-card); padding:20px; border-radius:12px; border:1px solid var(--glass-border);">
        <h3 style="font-size:13px; color:var(--text-muted); text-transform:uppercase;">Pending / நிலுவை</h3>
        <p class="stat-value" style="font-size:26px; font-weight:700; color:var(--amber); margin-top:8px;"><?= number_format($stats['pending']) ?></p>
    </div>
</section>

<section class="dashboard-welcome" style="background:var(--bg-card); padding:24px; border-radius:12px; border:1px solid var(--glass-border);">
    <h2>Welcome, <?= htmlspecialchars(Session::getUser() ? Session::getUser()->getUsername() : 'Admin') ?></h2>
    <p style="margin-top:6px; color:var(--text-secondary);">You have administrative control over the Staff Verification System.</p>
    <div style="margin-top:16px; display:flex; gap:12px;">
        <a href="<?= get_config('base_path') ?>" class="btn-primary" style="padding:10px 18px; border-radius:8px; text-decoration:none; background:var(--gradient-teal); color:#fff; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
            Open Staff Verifier Register →
        </a>
        <a href="<?= get_config('base_path') ?>api/verify/export" class="btn-secondary" style="padding:10px 18px; border-radius:8px; text-decoration:none; background:var(--bg-secondary); color:var(--text-primary); border:1px solid var(--line); font-weight:500; display:inline-flex; align-items:center; gap:6px;">
            Download Full CSV Export 📋
        </a>
    </div>
</section>
