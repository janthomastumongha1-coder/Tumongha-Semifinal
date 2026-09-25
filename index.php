<?php
// index.php - View all tasks
require_once 'db/db.php';

// Handle status update via GET (toggle)
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $stmt = $pdo->prepare("SELECT status FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    if ($task) {
        $newStatus = ($task['status'] === 'Pending') ? 'Completed' : 'Pending';
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
    }
    header("Location: index.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY due_date ASC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🏍️ Ride Task Manager</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #121212;
            background-image:
                linear-gradient(45deg, transparent 48%, #1e1e1e 48%, #1e1e1e 52%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, #1e1e1e 48%, #1e1e1e 52%, transparent 52%);
            background-size: 60px 60px;
            color: #eee;
            min-height: 100vh;
            padding: 40px 15px;
        }

        .container { max-width: 1000px; margin: 0 auto; }

        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid #ff6b00;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 5px;
            background: repeating-linear-gradient(45deg, #ff6b00 0 15px, #1a1a1a 15px 30px);
        }

        header h1 {
            font-size: 2.2em;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #ff6b00;
            text-shadow: 0 0 20px rgba(255,107,0,0.5);
        }

        header p { color: #aaa; margin-top: 5px; letter-spacing: 1px; }

        .toolbar { margin: 25px 0; text-align: right; }

        .btn-add {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b00, #ff3300);
            color: #fff;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(255,107,0,0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255,107,0,0.7);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }

        th, td { padding: 14px 12px; text-align: left; }

        th {
            background: linear-gradient(135deg, #ff6b00, #cc4400);
            color: #fff;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1.5px;
        }

        td {
            border-bottom: 1px solid #333;
            background: #1c1c1c;
        }

        tr:hover td { background: #262626; }

        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pending {
            background: rgba(255,193,7,0.15);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .completed {
            background: rgba(76,175,80,0.15);
            color: #4caf50;
            border: 1px solid #4caf50;
        }

        .actions a {
            text-decoration: none;
            margin-right: 10px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .btn-toggle { color: #ff6b00; }
        .btn-edit { color: #2196f3; }
        .btn-delete { color: #f44336; }

        .actions a:hover { text-decoration: underline; }

        .empty-msg {
            text-align: center;
            padding: 40px !important;
            color: #777;
            font-size: 1.1em;
            background: #1c1c1c;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            color: #555;
            font-size: 0.85em;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1> Ride Task Manager</h1>
            <p>Full throttle sa imong mga tasks bai!</p>
        </header>

        <div class="toolbar">
            <a class="btn-add" href="add.php">🏁 + Add Task</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Task Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($task['task_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($task['status']); ?>">
                            <?php echo $task['status'] === 'Completed' ? '🏆 ' : '⏳ '; echo $task['status']; ?>
                        </span>
                    </td>
                    <td><?php echo $task['due_date']; ?></td>
                    <td class="actions">
                        <a class="btn-toggle" href="index.php?toggle_status=<?php echo $task['id']; ?>">
                            [<?php echo $task['status'] === 'Pending' ? 'Mark Completed' : 'Mark Pending'; ?>]
                        </a>
                        <a class="btn-edit" href="edit.php?id=<?php echo $task['id']; ?>">[Edit]</a>
                        <a class="btn-delete" href="index.php?delete=<?php echo $task['id']; ?>"
                           onclick="return confirm('Sure ka bai? Delete ni?');">[Delete]</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty-msg">🏍️ Wala pay tasks bai. Gasolina pa og add na! 🔧</td></tr>
            <?php endif; ?>
        </table>

        <footer>RIDE HARD • WORK HARD • FINISH STRONG</footer>
    </div>
</body>
</html>