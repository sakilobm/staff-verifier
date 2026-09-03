<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — <?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?></title>
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/index.css">
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/verifier.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Create Account / புதிய கணக்கு</h2>
            <p style="margin-bottom: 24px; color:var(--text-secondary);">Join <?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?> today</p>

            <form id="signup-form" novalidate>
                <div class="form-group">
                    <label for="signup-username">Username</label>
                    <input type="text" id="signup-username" name="username" autocomplete="username" required placeholder="sakil">
                </div>
                <div class="form-group">
                    <label for="signup-email">Email</label>
                    <input type="email" id="signup-email" name="email_address" autocomplete="email" required placeholder="name@gmail.com">
                </div>
                <div class="form-group">
                    <label for="signup-phone">Phone</label>
                    <input type="tel" id="signup-phone" name="phone" autocomplete="tel" placeholder="9876543210">
                </div>
                <div class="form-group">
                    <label for="signup-password">Password</label>
                    <input type="password" id="signup-password" name="password" autocomplete="new-password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                    Create Account / பதிவு செய்க
                </button>
            </form>

            <p style="margin-top:20px;text-align:center;font-size:14px;color:var(--text-muted);">
                Already have an account? <a href="<?= get_config('base_path') ?>login" style="color:var(--teal);font-weight:600;">Sign in</a>
            </p>
            <p style="margin-top:12px;text-align:center;font-size:13px;">
                <a href="<?= get_config('base_path') ?>" style="color:var(--text-secondary);text-decoration:none;">← பதிவேட்டிற்கு திரும்ப / Back to Register</a>
            </p>
        </div>
    </div>

    <div class="toast-panel" id="toast-container"></div>

    <script>
        window.APP_BASE_PATH = "<?= get_config('base_path') ?>";
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>
</body>
</html>
