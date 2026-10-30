<?php
include 'config/db.php';

$result = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_students = $result->fetch_assoc()['total'];

$total_courses = 4;

include 'includes/header.php';
?>

<h1>Welcome to Student Portal</h1>

<p>Manage your students and training courses.</p>

<div class="cards">

    <div class="card">
        <h3>Total Students</h3>
        <h1><?php echo $total_students; ?></h1>
    </div>

    <div class="card">
        <h3>Available Courses</h3>
        <h1><?php echo $total_courses; ?></h1>
    </div>

</div>

<h2>Our Training Programs</h2>

<ul>
    <li>AWS Cloud Computing</li>
    <li>DevOps Engineering</li>
    <li>Full Stack Development</li>
    <li>Data Science</li>
</ul>

<?php include 'includes/footer.php'; ?>
