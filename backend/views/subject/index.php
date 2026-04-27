<?php 
ob_start();
?>

<div class="grid">
    <div class="stat-card">
        <h3><?= count($subjects) ?></h3>
        <p>Total Assignatures</p>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Llista d'Assignatures</h2>
        <a href="/subject/create" class="btn btn-success">+ Nova Assignatura</a>
    </div>
    
    <?php if (empty($subjects)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">No hi ha assignatures registrades encara.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Curs</th>
                    <th>Professor</th>
                    <th>Estat</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?= htmlspecialchars($subject->id()->value()) ?></td>
                        <td><?= htmlspecialchars($subject->name()) ?></td>
                        <td><?= htmlspecialchars($subject->course()->name()) ?></td>
                        <td>
                            <?php if ($subject->hasTeacher()): ?>
                                <?php $teachers = $subject->teachers()->toArray(); ?>
                                <?= htmlspecialchars(implode(', ', array_map(static fn ($t) => $t->name(), $teachers))) ?>
                            <?php else: ?>
                                <span style="color: #e74c3c;">Sense assignar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($subject->hasTeacher()): ?>
                                <span style="color: #27ae60; font-weight: bold;">Assignada</span>
                            <?php else: ?>
                                <span style="color: #f39c12; font-weight: bold;">Pendent</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/subject/edit?id=<?= urlencode($subject->id()->value()) ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                            <a href="/subject/delete?id=<?= urlencode($subject->id()->value()) ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Segur que vols eliminar aquesta assignatura?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
