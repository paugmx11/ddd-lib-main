<?php 
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>📋 Matricular Alumne en Curs</h2>
    
    <?php if (empty($students)): ?>
        <div class="alert alert-error">
            No hi ha alumnes registrats. <a href="/student/create">Crear un alumne primer</a>
        </div>
    <?php elseif (empty($courses)): ?>
        <div class="alert alert-error">
            No hi ha cursos disponibles. <a href="/course/create">Crear un curs primer</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/student/enroll">
            <div class="form-group">
                <label for="studentId">Alumne *</label>
                <select id="studentId" name="studentId" required>
                    <option value="">-- Selecciona un alumne --</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= htmlspecialchars($student->id()->value()) ?>">
                            <?= htmlspecialchars($student->name()) ?> (<?= htmlspecialchars($student->email()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="courseId">Curs *</label>
                <select id="courseId" name="courseId" required>
                    <option value="">-- Selecciona un curs --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course->id()->value()) ?>">
                            <?= htmlspecialchars($course->name()) ?> 
                            (<?= $course->startDate()->format('d/m/Y') ?> - <?= $course->endDate()->format('d/m/Y') ?>)
                            <?= $course->isActive() ? 'Actiu' : 'No actiu' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">📋 Matricular</button>
                <a href="/student" class="btn" style="background: #95a5a6;">Cancel·lar</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
