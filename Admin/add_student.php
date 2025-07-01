<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';

$message = '';
$error = '';
$form_data = ['full_name' => '', 'email' => '', 'matricule' => '', 'department_id' => ''];

// Register student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['full_name'] = trim($_POST['full_name']);
    $form_data['email'] = trim($_POST['email']);
    $form_data['matricule'] = trim($_POST['matricule']);
    $form_data['department_id'] = $_POST['department_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if matricule or email already exists
    $check = $pdo->prepare("SELECT id FROM students WHERE matricule = ? OR email = ?");
    $check->execute([$form_data['matricule'], $form_data['email']]);

    if ($check->rowCount() > 0) {
        $error = "Matricule or email already exists!";
    } elseif (!$form_data['department_id']) {
        $error = "Please select a department.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO students (full_name, email, matricule, password, department_id)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $form_data['full_name'],
            $form_data['email'],
            $form_data['matricule'],
            $password,
            $form_data['department_id']
        ]);
        $message = "✅ Student registered successfully!";
        $form_data = ['full_name' => '', 'email' => '', 'matricule' => '', 'department_id' => ''];
    }
}

// Get departments
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f6f8; }
        form {
            width: 50%;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #007BFF; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .message, .error {
            text-align: center;
            padding: 10px;
            margin: 10px auto;
            width: 50%;
            border-radius: 5px;
        }
        .message { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h2>Register New Student</h2>

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php elseif ($error): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <label>Full Name:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($form_data['full_name']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($form_data['email']) ?>" required>

    <label>Matricule:</label>
    <input type="text" name="matricule" value="<?= htmlspecialchars($form_data['matricule']) ?>" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Department:</label>
    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>" <?= $d['id'] == $form_data['department_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Register Student</button>
</form>
</body>
</html>
