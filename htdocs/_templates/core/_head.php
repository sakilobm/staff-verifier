<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title><?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?></title>
<meta name="description" content="<?= htmlspecialchars(get_config('meta_description', 'Staff Verification Register — பணியாளர் சரிபார்ப்பு பதிவேடு')) ?>">

<!-- Theme Anti-Flash initialization -->
<script>
  (function(){
    var saved = localStorage.getItem('appTheme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    document.documentElement.setAttribute('data-theme', saved);
  })();
</script>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- Framework & Verifier CSS -->
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/verifier.css">
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/index.css">

<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars(get_config('project_title', 'Staff Verifier')) ?>">
<meta property="og:type" content="website">
