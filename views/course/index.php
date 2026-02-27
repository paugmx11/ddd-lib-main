<?php 
ob_start();
?>

<div class="grid">
    <div class="stat-card">
        <h3><?= count($courses) ?></h3>
        <p>Total Cursos</p>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Llista de Cursos</h2>
        <a href="/course/create" class="btn btn-success">+ Nou Curs</a>
    </div>
    
    <?php if (empty($courses)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">No hi ha cursos registrats encara.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Descripció</th>
                    <th>Data Inici</th>
                    <th>Data Fi</th>
                    <th>Estat</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course->id()->value()) ?></td>
                        <td><?= htmlspecialchars($course->name()) ?></td>
                        <td><?= htmlspecialchars($course->description() ?? '-') ?></td>
                        <td><?= $course->startDate()->format('d/m/Y') ?></td>
                        <td><?= $course->endDate()->format('d/m/Y') ?></td>
                        <td>
                            <?php if ($course->isActive()): ?>
                                <span style="color: #27ae60; font-weight: bold;">Actiu</span>
                            <?php elseif ($course->hasEnded()): ?>
                                <span style="color: #e74c3c; font-weight: bold;">Finalitzat</span>
                            <?php else: ?>
                                <span style="color: #f39c12; font-weight: bold;">Pendent</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/course/edit?id=<?= urlencode($course->id()->value()) ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                            <a href="/course/delete?id=<?= urlencode($course->id()->value()) ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Segur que vols eliminar aquest curs?')">Eliminar</a>
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
