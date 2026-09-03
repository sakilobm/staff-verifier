<?php
use Aether\Session;

// Determine status code & custom contextual messages
$code = http_response_code() ?: 404;

$errorConfig = [
    403 => [
        'badge'       => '403 // ACCESS FORBIDDEN',
        'title'       => 'Access Restricted',
        'subtitle'    => 'You don’t have permission to view this directory or access this endpoint.',
        'accent'      => '#ef4444',
        'badge_class' => 'badge-danger',
        'icon'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>'
    ],
    404 => [
        'badge'       => '404 // PAGE NOT FOUND',
        'title'       => 'Lost in Digital Space',
        'subtitle'    => 'The route, file, or resource you are looking for has been relocated or vanished.',
        'accent'      => '#6366f1',
        'badge_class' => 'badge-primary',
        'icon'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>'
    ],
    500 => [
        'badge'       => '500 // SYSTEM EXCEPTION',
        'title'       => 'Internal System Anomaly',
        'subtitle'    => 'Our backend encountered an unexpected state. Our engineering team has been notified.',
        'accent'      => '#f59e0b',
        'badge_class' => 'badge-warning',
        'icon'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
    ]
];

$active = $errorConfig[$code] ?? $errorConfig[404];
$base = rtrim(get_config('base_path', '/'), '/');
?>

<section class="hw-error-section">
    <!-- Ambient Dynamic Glows -->
    <div class="hw-error-glow hw-glow-1"></div>
    <div class="hw-error-glow hw-glow-2"></div>
    <div class="hw-error-grid"></div>

    <div class="hw-error-container">
        <!-- Eyebrow Badge (Centered Cleanly on Top) -->
        <div class="hw-error-badge <?= $active['badge_class'] ?>">
            <span class="hw-badge-icon"><?= $active['icon'] ?></span>
            <span class="hw-badge-text"><?= htmlspecialchars($active['badge']) ?></span>
        </div>

        <!-- Giant Centered Stat Number -->
        <div class="hw-error-big-number"><?= $code ?></div>

        <!-- Headings -->
        <h1 class="hw-error-title"><?= htmlspecialchars($active['title']) ?></h1>
        <p class="hw-error-desc"><?= htmlspecialchars($active['subtitle']) ?></p>

        <!-- Quick Action Hub -->
        <div class="hw-error-actions">
            <a href="<?= $base ?>/" class="hw-btn hw-btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Return to Home</span>
            </a>
            <button type="button" onclick="window.history.back()" class="hw-btn hw-btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                <span>Go Back</span>
            </button>
            <a href="<?= $base ?>/#contact" class="hw-btn hw-btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Contact Support</span>
            </a>
        </div>

        <!-- Terminal Diagnostic Box -->
        <div class="hw-error-terminal">
            <div class="hw-terminal-header">
                <div class="hw-terminal-dots">
                    <span class="dot-red"></span>
                    <span class="dot-yellow"></span>
                    <span class="dot-green"></span>
                </div>
                <div class="hw-terminal-title">system_diagnostics.sh</div>
            </div>
            <div class="hw-terminal-body">
                <div class="hw-terminal-line"><span class="hw-term-prompt">HIGHWIN@CORE:~$</span> request_uri: <span class="hw-term-val"><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?></span></div>
                <div class="hw-terminal-line"><span class="hw-term-prompt">HIGHWIN@CORE:~$</span> status_code: <span class="hw-term-code"><?= $code ?> <?= $code === 403 ? 'FORBIDDEN' : ($code === 500 ? 'INTERNAL_SERVER_ERROR' : 'NOT_FOUND') ?></span></div>
                <div class="hw-terminal-line"><span class="hw-term-prompt">HIGHWIN@CORE:~$</span> server_time: <span class="hw-term-time"><?= date('Y-m-d H:i:s T') ?></span></div>
            </div>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   HIGHWIN ERROR PAGE — DUAL THEME (DARK & LIGHT)
   ========================================================================== */

.hw-error-section {
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 120px 24px 80px;
    overflow: hidden;
    background: #090a10;
    color: #e2e8f0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    transition: background 0.3s ease, color 0.3s ease;
}

.hw-error-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
}

.hw-error-glow {
    position: absolute;
    border-radius: 50%;
    filter: blur(120px);
    pointer-events: none;
    opacity: 0.25;
    animation: hwPulse 8s ease-in-out infinite alternate;
}

.hw-glow-1 {
    top: 15%;
    left: 25%;
    width: 380px;
    height: 380px;
    background: #6366f1;
}

.hw-glow-2 {
    bottom: 10%;
    right: 25%;
    width: 420px;
    height: 420px;
    background: #06b6d4;
    animation-delay: -4s;
}

@keyframes hwPulse {
    0% { transform: scale(1) translate(0, 0); opacity: 0.2; }
    100% { transform: scale(1.2) translate(30px, -20px); opacity: 0.35; }
}

.hw-error-container {
    position: relative;
    z-index: 2;
    max-width: 680px;
    width: 100%;
    margin: 0 auto;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Eyebrow Badge */
.hw-error-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    letter-spacing: 0.08em;
    backdrop-filter: blur(10px);
    margin-bottom: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.hw-error-badge.badge-danger {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #f87171;
}

.hw-error-badge.badge-primary {
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.35);
    color: #a5b4fc;
}

.hw-error-badge.badge-warning {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.35);
    color: #fbbf24;
}

.hw-badge-icon {
    display: flex;
    align-items: center;
}

/* Big Number */
.hw-error-big-number {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(6.5rem, 16vw, 10.5rem);
    font-weight: 800;
    line-height: 0.95;
    letter-spacing: -0.05em;
    background: linear-gradient(180deg, #ffffff 25%, rgba(255, 255, 255, 0.18) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 70px rgba(99, 102, 241, 0.25);
    user-select: none;
    margin: 4px 0 16px;
}

.hw-error-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    font-weight: 700;
    color: #f8fafc;
    margin: 0 0 12px;
    letter-spacing: -0.02em;
}

.hw-error-desc {
    font-size: 1.05rem;
    color: #94a3b8;
    line-height: 1.6;
    max-width: 520px;
    margin: 0 auto 32px;
}

/* Buttons */
.hw-error-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 36px;
}

.hw-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 0.92rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    border: 1px solid transparent;
}

.hw-btn-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #ffffff;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35);
}

.hw-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
    color: #ffffff;
}

.hw-btn-secondary {
    background: rgba(255, 255, 255, 0.05);
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
}

.hw-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.hw-btn-ghost {
    background: transparent;
    color: #94a3b8;
    border-color: rgba(255, 255, 255, 0.08);
}

.hw-btn-ghost:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.2);
}

/* Terminal Box */
.hw-error-terminal {
    width: 100%;
    max-width: 540px;
    background: rgba(10, 11, 18, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    text-align: left;
    backdrop-filter: blur(16px);
    transition: all 0.3s ease;
}

.hw-terminal-header {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.hw-terminal-dots {
    display: flex;
    gap: 6px;
}

.hw-terminal-dots span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.dot-red { background: #ef4444; }
.dot-yellow { background: #f59e0b; }
.dot-green { background: #10b981; }

.hw-terminal-title {
    margin-left: 12px;
    font-size: 0.75rem;
    font-family: 'JetBrains Mono', monospace;
    color: #64748b;
}

.hw-terminal-body {
    padding: 14px 16px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    line-height: 1.7;
}

.hw-term-prompt {
    color: #06b6d4;
    margin-right: 6px;
}

.hw-term-val {
    color: #f1f5f9;
}

.hw-term-code {
    color: #f43f5e;
    font-weight: 600;
}

.hw-term-time {
    color: #a5b4fc;
}

/* ==========================================================================
   LIGHT THEME ADAPTATION
   ========================================================================== */

[data-theme="light"] .hw-error-section {
    background: #f8fafc;
    color: #1e293b;
}

[data-theme="light"] .hw-error-grid {
    background-image: 
        linear-gradient(to right, rgba(0, 0, 0, 0.04) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.04) 1px, transparent 1px);
    mask-image: radial-gradient(circle at center, black 50%, transparent 85%);
}

[data-theme="light"] .hw-glow-1 {
    background: #818cf8;
    opacity: 0.15;
}

[data-theme="light"] .hw-glow-2 {
    background: #38bdf8;
    opacity: 0.15;
}

[data-theme="light"] .hw-error-big-number {
    background: linear-gradient(180deg, #0f172a 20%, #475569 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
}

[data-theme="light"] .hw-error-title {
    color: #0f172a;
}

[data-theme="light"] .hw-error-desc {
    color: #475569;
}

[data-theme="light"] .hw-error-badge.badge-danger {
    background: rgba(239, 68, 68, 0.08);
    border-color: rgba(239, 68, 68, 0.25);
    color: #dc2626;
}

[data-theme="light"] .hw-error-badge.badge-primary {
    background: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.25);
    color: #4f46e5;
}

[data-theme="light"] .hw-error-badge.badge-warning {
    background: rgba(245, 158, 11, 0.08);
    border-color: rgba(245, 158, 11, 0.25);
    color: #d97706;
}

[data-theme="light"] .hw-btn-secondary {
    background: #ffffff;
    color: #334155;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

[data-theme="light"] .hw-btn-secondary:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

[data-theme="light"] .hw-btn-ghost {
    background: transparent;
    color: #475569;
    border-color: #cbd5e1;
}

[data-theme="light"] .hw-btn-ghost:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}

[data-theme="light"] .hw-error-terminal {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
}

[data-theme="light"] .hw-terminal-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

[data-theme="light"] .hw-terminal-title {
    color: #64748b;
}

[data-theme="light"] .hw-term-prompt {
    color: #0284c7;
}

[data-theme="light"] .hw-term-val {
    color: #0f172a;
}

[data-theme="light"] .hw-term-code {
    color: #e11d48;
}

[data-theme="light"] .hw-term-time {
    color: #4f46e5;
}

@media (max-width: 640px) {
    .hw-error-actions {
        flex-direction: column;
        width: 100%;
    }
    .hw-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
