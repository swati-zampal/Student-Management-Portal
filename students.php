<?php
include 'config/db.php';

// Add Student
if (isset($_POST['add_student'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    $stmt = $conn->prepare(
        "INSERT INTO students (name, email, course)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $name, $email, $course);

    if ($stmt->execute()) {
        echo "<script>
            alert('Student Added Successfully!');
            window.location='students.php';
        </script>";
        exit;
    }

    $stmt->close();
}

// Delete Student
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM students WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: students.php");
    exit;
}

// Fetch Students
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");

include 'includes/header.php';
?>

<h1>Student Management</h1>

<h2>Register New Student</h2>

<form method="POST">

    <input type="text"
           name="name"
           placeholder="Enter Student Name"
           required>

    <input type="email"
           name="email"
           placeholder="Enter Email Address"
           required>

    <select name="course" required>
        <option value="">Select Course</option>
        <option value="AWS">AWS</option>
        <option value="DevOps">DevOps</option>
        <option value="Full Stack">Full Stack</option>
        <option value="Data Science">Data Science</option>
    </select>

    <button type="submit" name="add_student">
        Add Student
    </button>

</form>

<h2>Registered Students</h2>

<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>

    <tr>
        <td><?php echo $row['id']; ?></td>

        <td>
            <?php echo htmlspecialchars($row['name']); ?>
        </td>

        <td>
            <?php echo htmlspecialchars($row['email']); ?>
        </td>

        <td>
            <?php echo htmlspecialchars($row['course']); ?>
        </td>

        <td>
            <a class="delete"
               href="students.php?delete=<?php echo $row['id']; ?>"
               onclick="return confirm('Delete this student?')">
               Delete
            </a>
        </td>
    </tr>

    <?php } ?>

</table>

<?php include 'includes/footer.php'; ?>
