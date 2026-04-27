<?php 
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Crear Nou Curs</h2>
    
    <form method="POST" action="/course/create">
        <div class="form-group">
            <label for="name">Nom del curs *</label>
            <input type="text" id="name" name="name" required 
                   placeholder="Ex: PHP Domain Driven Design" 
                   value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Descripció</label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Descripció del curs..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="startDate">Data d'inici *</label>
            <input type="date" id="startDate" name="startDate" required
                   value="<?= isset($_POST['startDate']) ? htmlspecialchars($_POST['startDate']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="endDate">Data de finalització *</label>
            <input type="date" id="endDate" name="endDate" required
                   value="<?= isset($_POST['endDate']) ? htmlspecialchars($_POST['endDate']) : '' ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Crear Curs</button>
            <a href="/course" class="btn" style="background: #95a5a6;">Cancel·lar</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
