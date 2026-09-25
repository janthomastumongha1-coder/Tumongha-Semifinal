<?php
// add.php - Create a new task
require_once 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = trim($_POST['task_name']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    if (!empty($task_name)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (task_name, description, status, due_date) VALUES (?, ?, 'Pending', ?)");
        $stmt->execute([$task_name, $description, $due_date]);
        header("Location: index.php");
        exit();
    } else {
        $error = "Ang task name kay required bai!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🏍️ Add Task</title>
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
            border: 1px solid #ff6b00;
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
            background: repeating-linear-gradient(45deg, #ff6b00 0 15px, #1a1a1a 15px 30px);
            border-radius: 12px 12px 0 0;
        }

        h1 {
            color: #ff6b00;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 15px rgba(255,107,0,0.4);
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

        input, textarea {
            width: 100%;
            padding: 12px;
            background: #0d0d0d;
            border: 1px solid #444;
            border-radius: 6px;
            color: #eee;
            font-size: 1em;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #ff6b00;
            box-shadow: 0 0 10px rgba(255,107,0,0.3);
        }

        button {
            width: 100%;
            margin-top: 25px;
            background: linear-gradient(135deg, #ff6b00, #ff3300);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-size: 1em;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255,107,0,0.4);
            transition: transform 0.2s;
        }

        button:hover { transform: translateY(-3px); }

        .error {
            background: rgba(244,67,54,0.15);
            border: 1px solid #f44336;
            color: #f44336;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 10px;
        }

        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #ff6b00; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="form-box">
        <h1>🏁 Add Task</h1>
        <?php if (isset($error)) echo "<p class='error'>⚠️ $error</p>"; ?>
        <form method="POST">
            <label>🏍️ Task Name:</label>
            <input type="text" name="task_name" required>

            <label>📝 Description:</label>
            <textarea name="description" rows="4"></textarea>

            <label>📅 Due Date:</label>
            <input type="date" name="due_date">

            <button type="submit">🏁 Save Task</button>
        </form>
        <p class="back-link"><a href="index.php">&larr; Balik sa Garage</a></p>
    </div>
</body>
</html>