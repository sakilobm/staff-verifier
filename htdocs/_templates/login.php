<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?></title>
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/index.css">
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/verifier.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Welcome Back / உள்நுழைவு</h2>
            <p style="margin-bottom: 24px; color:var(--text-secondary);">Sign in to <?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?></p>

            <form id="login-form" novalidate>
                <div class="form-group">
                    <label for="login-user">Username or Email</label>
                    <input type="text" id="login-user" name="user" autocomplete="username" required placeholder="name@gmail.com">
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" autocomplete="current-password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                    Sign In / உள்நுழைக
                </button>
            </form>

            <p style="margin-top:20px;text-align:center;font-size:14px;color:var(--text-muted);">
                Don't have an account? <a href="<?= get_config('base_path') ?>signup" style="color:var(--teal);font-weight:600;">Register</a>
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
