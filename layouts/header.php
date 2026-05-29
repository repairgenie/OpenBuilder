<!-- layouts/header.php -->
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenBuilder - Construction Management</title>
    <?php
    // Security headers
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    ?>
    <link rel="stylesheet" href="public/css/variables.css?v=<?php echo ASSET_VERSION; ?>">
    <link rel="stylesheet" href="public/css/base.css?v=<?php echo ASSET_VERSION; ?>">
    <script src="public/js/search.js?v=<?php echo ASSET_VERSION; ?>" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3C50E0',
                        success: '#219653',
                        warning: '#F2994A',
                        danger: '#D34053',
                        stroke: '#E2E8F0',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php if (!empty($_SESSION['flash_success'])): ?>
<div id="flash-toast" class="fixed top-4 right-4 z-50 bg-success text-white px-6 py-3 rounded shadow-lg flex items-center gap-3" style="display:none;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    <span><?php echo htmlspecialchars($_SESSION['flash_success']); ?></span>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
<div id="flash-toast" class="fixed top-4 right-4 z-50 bg-danger text-white px-6 py-3 rounded shadow-lg flex items-center gap-3" style="display:none;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
    <span><?php echo htmlspecialchars($_SESSION['flash_error']); ?></span>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>
<script>
(function(){ var el=document.getElementById('flash-toast'); if(el){ el.style.display='flex'; setTimeout(function(){el.style.display='none';},4000); } })();
</script>
