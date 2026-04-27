<?php 
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Crear Nou Alumne</h2>
    
    <form method="POST" action="/student/create">
        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input type="text" id="name" name="name" required 
                   placeholder="Ex: Joan Garcia" 
                   value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Correu electrònic *</label>
            <input type="email" id="email" name="email" required 
                   placeholder="Ex: joan@example.com"
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Crear Alumne</button>
            <a href="/student" class="btn" style="background: #95a5a6;">Cancel·lar</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
