<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé - ABI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .error-card {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: #ef4444;
            margin: 0;
            line-height: 1;
        }
        
        .error-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 20px 0 10px;
        }
        
        .error-message {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">🚫</div>
            <h1 class="error-code">403</h1>
            <h2 class="error-title">Accès Refusé</h2>
            <p class="error-message">
                Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page.
                Veuillez contacter votre administrateur si vous pensez qu'il s'agit d'une erreur.
            </p>
            <div class="error-actions">
                <a href="dashboard.php" class="btn btn-primary">
                    ← Retour au Tableau de Bord
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    Déconnexion
                </a>
            </div>
        </div>
    </div>
</body>
</html>
