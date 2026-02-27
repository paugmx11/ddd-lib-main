<?php 
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Crear Nova Assignatura</h2>
    
    <?php if (empty($courses)): ?>
        <div class="alert alert-error">
            No hi ha cursos disponibles. <a href="/course/create">Crear un curs primer</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/subject/create">
            <div class="form-group">
                <label for="name">Nom de l'assignatura *</label>
                <input type="text" id="name" name="name" required 
                       placeholder="Ex: Domain Driven Design" 
                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="courseId">Curs *</label>
                <select id="courseId" name="courseId" required>
                    <option value="">-- Selecciona un curs --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course->id()->value()) ?>">
                            <?= htmlspecialchars($course->name()) ?> 
                            (<?= $course->startDate()->format('d/m/Y') ?> - <?= $course->endDate()->format('d/m/Y') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">Crear Assignatura</button>
                <a href="/subject" class="btn" style="background: #95a5a6;">Cancel·lar</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
