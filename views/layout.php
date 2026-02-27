<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management - DDD</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #fafafa; color: #222; }
        .navbar { padding: 12px 16px; border-bottom: 1px solid #ddd; background: #fff; }
        .navbar h1 { margin: 0 0 8px 0; font-size: 20px; }
        .navbar nav a { margin-right: 12px; color: #222; text-decoration: none; }
        .navbar nav a:hover { text-decoration: underline; }
        .container { max-width: 980px; margin: 16px auto; padding: 0 12px; }
        .card { background: #fff; border: 1px solid #ddd; padding: 16px; margin-bottom: 16px; }
        .card h2 { margin: 0 0 12px; font-size: 22px; }
        .btn { display: inline-block; padding: 8px 12px; background: #eee; color: #111; text-decoration: none; border: 1px solid #bbb; cursor: pointer; }
        .btn-success { background: #e8f5e9; border-color: #99c49a; }
        .btn-danger { background: #fdecea; border-color: #e0a7a1; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #f2f2f2; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 4px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #bbb; }
        .alert { padding: 10px; border: 1px solid #ddd; margin-bottom: 12px; background: #fff; }
        .alert-success { border-color: #99c49a; background: #f3fbf4; }
        .alert-error { border-color: #e0a7a1; background: #fff5f4; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 16px; }
        .stat-card { background: #fff; border: 1px solid #ddd; padding: 12px; }
        .stat-card h3 { margin: 0 0 6px 0; font-size: 24px; }
        .stat-card p { margin: 0; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>School Management</h1>
        <nav>
            <a href="/">Inici</a>
            <a href="/student">Alumnes</a>
            <a href="/teacher">Professors</a>
            <a href="/course">Cursos</a>
            <a href="/subject">Assignatures</a>
        </nav>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </div>
</body>
</html>
