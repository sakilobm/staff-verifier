<?php use Aether\Session; ?>
<!doctype html>
<html lang="en">
<head>
    <?php Session::loadTemplate('core/_head'); ?>
</head>

<body>
    <!-- Main Content Injected by Session::renderView() -->
    <div id="app-root">
        <?php
        if (isset($content)) {
            echo $content;
        } else {
            Session::loadTemplate(Session::currentScript());
        }
        ?>
    </div>

    <?php Session::loadTemplate('core/_toastv3'); ?>

    <!-- Global App Configuration -->
    <script>
        window.APP_BASE_PATH = "<?= get_config('base_path') ?>";
        window.IS_AUTHENTICATED = <?= Session::isAuthenticated() ? 'true' : 'false' ?>;
        <?php if (Session::isAuthenticated()): ?>
        window.CURRENT_USER = {
            id: <?= (int)Session::getUser()->getID() ?>,
            username: "<?= htmlspecialchars(Session::getUser()->getUsername(), ENT_QUOTES) ?>",
            email: "<?= htmlspecialchars(Session::getUser()->getEmail(), ENT_QUOTES) ?>"
        };
        <?php else: ?>
        window.CURRENT_USER = null;
        <?php endif; ?>
    </script>

    <!-- Core Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>
</body>
</html>
