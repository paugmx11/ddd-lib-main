<?php 
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Assignar Professor a Assignatura</h2>
    
    <?php if (empty($teachers)): ?>
        <div class="alert alert-error">
            No hi ha professors registrats. <a href="/teacher/create">Crear un professor primer</a>
        </div>
    <?php elseif (empty($subjects)): ?>
        <div class="alert alert-error">
            No hi ha assignatures disponibles. <a href="/subject/create">Crear una assignatura primer</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/teacher/assign">
            <div class="form-group">
                <label for="teacherId">Professor *</label>
                <select id="teacherId" name="teacherId" required>
                    <option value="">-- Selecciona un professor --</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= htmlspecialchars($teacher->id()->value()) ?>">
                            <?= htmlspecialchars($teacher->name()) ?> (<?= htmlspecialchars($teacher->email()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subjectId">Assignatura *</label>
                <select id="subjectId" name="subjectId" required>
                    <option value="">-- Selecciona una assignatura --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= htmlspecialchars($subject->id()->value()) ?>">
                            <?= htmlspecialchars($subject->name()) ?> 
                            (Curs: <?= htmlspecialchars($subject->course()->name()) ?>)
                            <?= $subject->hasTeacher() ? 'Ja té professors (es pot afegir un altre)' : 'Disponible' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">Assignar</button>
                <a href="/teacher" class="btn" style="background: #95a5a6;">Cancel·lar</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
