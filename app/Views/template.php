<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - Learning Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-hover: #0b5ed7;
        }
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background-color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e9ecef;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .back-btn:hover {
            color: var(--primary-hover);
            transform: translateX(-2px);
        }
        .main-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .page-title {
            color: #2c3e50;
            margin: 1rem 0 2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="container">
            <a href="javascript:history.back()" class="back-btn">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h1 class="h3 mb-0"><?= $title ?? 'LMS Dashboard' ?></h1>
        </div>
    </header>
    
    <main class="container">
        <div class="main-container">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Set base URL for JS -->
    <script>
        window.baseUrl = '<?= base_url() ?>';
    </script>
    <!-- Notifications JS -->
    <script src="<?= base_url('js/notifications.js') ?>"></script>
</body>
</html>
