<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mini ERP' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .btn-primary {
            background-color: #77773c;
            border-color: #77773c;
        }
        .btn-primary:hover {
            background-color: #666632;
            border-color: #666632;
        }
        .btn-primary:focus, .btn-primary.focus {
            box-shadow: 0 0 0 0.2rem rgba(119, 119, 60, 0.5);
        }
        .btn-success {
            background-color: #77773c;
            border-color: #77773c;
        }
        .btn-success:hover {
            background-color: #666632;
            border-color: #666632;
        }
        .card {
            border: 2px solid #77773c;
        }
        .btn-outline-primary {
            color: #77773c;
            border-color: #77773c;
        }
        .btn-outline-primary:hover {
            background-color: #77773c;
            border-color: #77773c;
            color: white;
        }
        .btn-outline-secondary {
            color: #77773c;
            border-color: #77773c;
        }
        .btn-outline-secondary:hover {
            background-color: #77773c;
            border-color: #77773c;
            color: white;
        }
        .badge.bg-success {
            background-color: #77773c !important;
        }
        .badge.bg-danger {
            background-color: #cc4444 !important;
        }
        h1 {
            color: #55552b;
        }
        .navbar-brand {
            color: #f6f6ee !important;
        }
        .navbar-nav .nav-link {
            color: #f6f6ee !important;
        }
        .navbar-nav .nav-link:hover {
            color: #ffffff !important;
        }
    </style>
</head>
<body style="background-color: #eeeedd;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #77773c;">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shop"></i> Mini ERP
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Produtos</a>
                <a class="nav-link" href="/produto/new">Novo Produto</a>
                <?php if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0): ?>
                    <a class="nav-link" href="/carrinho">
                        <i class="bi bi-cart"></i> Carrinho (<?= count($_SESSION['carrinho']) ?>)
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?= $content ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
    </script>
</body>
</html>
