<?php 
ob_start();
?>

<div class="grid">
    <div class="stat-card">
        <h3><?= count($teachers) ?></h3>
        <p>Total Professors</p>
    </div>
    <div class="stat-card">
        <h3><?= count($subjects) ?></h3>
        <p>Assignatures</p>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Llista de Professors</h2>
        <div>
            <a href="/teacher/create" class="btn btn-success">+ Nou Professor</a>
            <a href="/teacher/assign" class="btn">Assignar Assignatura</a>
        </div>
    </div>
    
    <?php if (empty($teachers)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">No hi ha professors registrats encara.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Assignatures</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <?php $teacherSubjects = is_array($teacher->subjects()) ? $teacher->subjects() : iterator_to_array($teacher->subjects()); ?>
                    <tr>
                        <td><?= htmlspecialchars($teacher->id()->value()) ?></td>
                        <td><?= htmlspecialchars($teacher->name()) ?></td>
                        <td><?= htmlspecialchars($teacher->email()) ?></td>
                        <td>
                            <?php if (empty($teacherSubjects)): ?>
                                <span style="color: #666;">Cap</span>
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <?php foreach ($teacherSubjects as $subject): ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                            <span><?= htmlspecialchars($subject->name()) ?></span>
                                            <form method="POST" action="/teacher/unassign" style="margin: 0;">
                                                <input type="hidden" name="teacherId" value="<?= htmlspecialchars($teacher->id()->value()) ?>">
                                                <input type="hidden" name="subjectId" value="<?= htmlspecialchars($subject->id()->value()) ?>">
                                                <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;" onclick="return confirm('Segur que vols desassignar aquesta assignatura?')">Desassignar</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/teacher/assign" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Assignar</a>
                            <a href="/teacher/edit?id=<?= urlencode($teacher->id()->value()) ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                            <a href="/teacher/delete?id=<?= urlencode($teacher->id()->value()) ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Segur que vols eliminar aquest professor?')">Eliminar</a>
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
