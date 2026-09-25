<?php
// edit.php - Update task information
require_once 'db/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = trim($_POST['task_name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("UPDATE tasks SET task_name = ?, description = ?, status = ?, due_date = ? WHERE id = ?");
    $stmt->execute([$task_name, $description, $status, $due_date, $id]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🏍️ Edit Task</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #121212;
            color: #eee;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-box {
            background: linear-gradient(135deg, #1a1a1a, #242424);
            border: 1px solid #2196f3;
            border-radius: 12px;
            padding: 35px;
            width: 100%;
            max-width: 480px;
        }

        .form-box::before {
            content: '';
            display: block;
            height: 5px;
            margin: -35px -35px 25px;
            background: repeating-linear-gradient(45deg, #2196f3 0 15px, #1a1a1a 15px 30px);
            border-radius: 12px 12px 0 0;
        }

        h1 {
            color: #2196f3;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 15px rgba(33,150,243,0.4);
        }

        label {
            display: block;
            margin: 18px 0 6px;
            color: #aaa;
            text-transform: uppercase;
            font-size: 0.8em;
            letter-spacing: 1.5px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            background: #0d0d0d;
            border: 1px solid #444;
            border-radius: 6px;
            color: #eee;
            font-size: 1em;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #2196f3;
            box-shadow: 0 0 10px rgba(33,150,243,0.3);
        }

        button {
            width: 100%;
            margin-top: 25px;
            background: linear-gradient(135deg, #2196f3, #0d47a1);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-size: 1em;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(33,150,243,0.4);
            transition: transform 0.2s;
        }

        button:hover { transform: translateY(-3px); }

        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #2196f3; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="form-box">
        <h1> Edit Task</h1>
        <form method="POST">
            <label>Task Name:</label>
            <input type="text" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>

            <label>📝 Description:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>

            <label>⚙️ Status:</label>
            <select name="status">
                <option value="Pending" <?php if ($task['status'] === 'Pending') echo 'selected'; ?>>⏳ Pending</option>
                <option value="Completed" <?php if ($task['status'] === 'Completed') echo 'selected'; ?>>🏆 Completed</option>
            </select>

            <label>📅 Due Date:</label>
            <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>">

            <button type="submit">🔧 Update Task</button>
        </form>
        <p class="back-link"><a href="index.php">&larr; Balik sa Garage</a></p>
    </div>
</body>
</html>