<?php
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Editar Assignatura</h2>

    <?php if (empty($courses)): ?>
        <div class="alert alert-error">
            No hi ha cursos disponibles. <a href="/course/create">Crear un curs primer</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/subject/edit?id=<?= urlencode($subject->id()->value()) ?>">
            <div class="form-group">
                <label for="name">Nom de l'assignatura *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    value="<?= htmlspecialchars($_POST['name'] ?? $subject->name()) ?>"
                >
            </div>

            <div class="form-group">
                <label for="courseId">Curs *</label>
                <?php $selectedCourseId = (string) ($_POST['courseId'] ?? $subject->course()->id()->value()); ?>
                <select id="courseId" name="courseId" required>
                    <option value="">-- Selecciona un curs --</option>
                    <?php foreach ($courses as $course): ?>
                        <option
                            value="<?= htmlspecialchars($course->id()->value()) ?>"
                            <?= $selectedCourseId === $course->id()->value() ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($course->name()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="/subject" class="btn" style="background: #95a5a6;">Cancel·lar</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
