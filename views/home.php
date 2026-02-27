<?php 
ob_start();
?>

<div class="grid">
    <div class="stat-card">
        <h3>Alumnes</h3>
        <p><a href="/student">Gestionar Alumnes</a></p>
    </div>
    <div class="stat-card">
        <h3>Professors</h3>
        <p><a href="/teacher">Gestionar Professors</a></p>
    </div>
    <div class="stat-card">
        <h3>Cursos</h3>
        <p><a href="/course">Gestionar Cursos</a></p>
    </div>
    <div class="stat-card">
        <h3>Assignatures</h3>
        <p><a href="/subject">Gestionar Assignatures</a></p>
    </div>
</div>

<div class="card">
    <h2>School Management System</h2>
    <p>Benvingut al sistema de gestio escolar amb Domain Driven Design (DDD).</p>
    
    <h3>Casos d'us implementats</h3>
    <ul>
        <li><strong>CreateStudent</strong> - Crear nous alumnes</li>
        <li><strong>CreateTeacher</strong> - Crear nous professors</li>
        <li><strong>CreateCourse</strong> - Crear nous cursos</li>
        <li><strong>CreateSubject</strong> - Crear noves assignatures</li>
        <li><strong>EnrollStudent</strong> - Matricular alumnes en cursos</li>
        <li><strong>AssignTeacherToSubject</strong> - Assignar professors a assignatures</li>
    </ul>
    
    <h3>Arquitectura DDD</h3>
    <ul>
        <li><strong>Domain Layer:</strong> Entitats, Value Objects, Repository Contracts</li>
        <li><strong>Application Layer:</strong> Commands, Handlers (Casos d'Ús)</li>
        <li><strong>Infrastructure Layer:</strong> Doctrine Repositories, Web Controllers, Views</li>
    </ul>
    
    <h3>Tests</h3>
    <ul>
        <li>Tests de Domini (purs, sense mocks)</li>
        <li>Tests d'Application (amb mocks)</li>
        <li>Tests per EnrollStudent i AssignTeacherToSubject (obligatoris)</li>
    </ul>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
