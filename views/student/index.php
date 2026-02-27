<?php 
ob_start();
?>

<div class="grid">
    <div class="stat-card">
        <h3><?= count($students) ?></h3>
        <p>Total Alumnes</p>
    </div>
    <div class="stat-card">
        <h3><?= count($courses) ?></h3>
        <p>Cursos Disponibles</p>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Llista d'Alumnes</h2>
        <div>
            <a href="/student/create" class="btn btn-success">+ Nou Alumne</a>
            <a href="/student/enroll" class="btn">Matricular</a>
        </div>
    </div>
    
    <?php if (empty($students)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">No hi ha alumnes registrats encara.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Matrícules</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student->id()->value()) ?></td>
                        <td><?= htmlspecialchars($student->name()) ?></td>
                        <td><?= htmlspecialchars($student->email()) ?></td>
                        <td>
                            <?php 
                            $enrollments = $student->enrollments();
                            echo count(is_array($enrollments) ? $enrollments : iterator_to_array($enrollments));
                            ?>
                        </td>
                        <td>
                            <a href="/student/enroll" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Matricular</a>
                            <a href="/student/edit?id=<?= urlencode($student->id()->value()) ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                            <a href="/student/delete?id=<?= urlencode($student->id()->value()) ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Segur que vols eliminar aquest alumne?')">Eliminar</a>
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
