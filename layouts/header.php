<!-- layouts/header.php -->
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenBuilder - Construction Management</title>
    <link rel="stylesheet" href="public/css/variables.css?v=<?php echo ASSET_VERSION; ?>">
    <link rel="stylesheet" href="public/css/base.css?v=<?php echo ASSET_VERSION; ?>">
    <script src="public/js/search.js?v=<?php echo ASSET_VERSION; ?>" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
