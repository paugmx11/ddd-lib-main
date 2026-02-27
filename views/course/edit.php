<?php
ob_start();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Editar Curs</h2>

    <form method="POST" action="/course/edit?id=<?= urlencode($course->id()->value()) ?>">
        <div class="form-group">
            <label for="name">Nom del curs *</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                value="<?= htmlspecialchars($_POST['name'] ?? $course->name()) ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Descripció</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? ($course->description() ?? '')) ?></textarea>
        </div>

        <div class="form-group">
            <label for="startDate">Data d'inici *</label>
            <input
                type="date"
                id="startDate"
                name="startDate"
                required
                value="<?= htmlspecialchars($_POST['startDate'] ?? $course->startDate()->format('Y-m-d')) ?>"
            >
        </div>

        <div class="form-group">
            <label for="endDate">Data de finalització *</label>
            <input
                type="date"
                id="endDate"
                name="endDate"
                required
                value="<?= htmlspecialchars($_POST['endDate'] ?? $course->endDate()->format('Y-m-d')) ?>"
            >
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="/course" class="btn" style="background: #95a5a6;">Cancel·lar</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
