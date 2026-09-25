# 🏍️ Ride Task Manager

A dark-themed **Task Manager web application** for riders, built with **PHP (PDO) + MySQL**. Features a bold dark UI with neon orange/blue accents, prepared statements for security, and full CRUD operations. Includes Laravel scaffolding files for the upcoming MVC version.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![PDO](https://img.shields.io/badge/Database-PDO-orange)

---

## ✨ Features

- 📝 **Full CRUD** — Create, Read, Update, and Delete tasks
- 🔒 **Secure PDO Prepared Statements** — protection against SQL injection
- 🏍️ **Rider-Themed Dark UI** — neon orange (add) / neon blue (edit) accent design
- ✅ **One-Click Status Toggle** — switch tasks between Pending and Completed
- 📅 **Due Date Sorting** — tasks ordered by due date automatically
- 🗄️ **SQL Dump Included** — import `Database/janthomas.sql` to get started instantly

---

## 🗂️ Project Structure

```
jan thomas/
├── index.php              # Main dashboard (task table, toggle, delete)
├── add.php                # Add new task form
├── edit.php               # Edit task form
├── db/
│   └── db.php             # PDO database connection
├── Database/
│   └── janthomas.sql      # Database dump (import via phpMyAdmin)
├── http/
│   └── controller.php     # Laravel base controller
├── routes/
│   ├── web.php            # Laravel web routes
│   └── console.php        # Laravel console routes
└── views/
    └── welcome.blade.php  # Laravel welcome view
```

---

## 🛠️ Requirements

### Classic PHP Version
- **PHP** >= 8.0 (tested on PHP 8.1)
- **MySQL** / MariaDB
- **XAMPP** / WAMP / Laragon

### Laravel Version
- **PHP** >= 8.1
- **Composer**
- **Node.js** & **NPM** (for Vite)
- **MySQL** / MariaDB

---

## 🚀 Setup Option 1 — Classic PHP + MySQL (XAMPP)

### 1. Extract the project

Place the folder inside your web server directory:

```
C:\xampp\htdocs\jan-thomas
```

### 2. Import the database

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create a new database named `janthomas`
3. Select the database, go to the **Import** tab
4. Upload `Database/janthomas.sql` and click **Go**

Or run manually:

```sql
CREATE DATABASE IF NOT EXISTS janthomas;

CREATE TABLE janthomas.tasks (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  task_name varchar(255) NOT NULL,
  description text DEFAULT NULL,
  status enum('Pending','Completed') DEFAULT 'Pending',
  due_date date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Configure the database connection

Edit `db/db.php` if your credentials differ:

```php
<?php
$host = 'localhost';
$dbname = 'janthomas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

> Default XAMPP credentials: user `root`, empty password.

### 4. Run the app

1. Start **Apache** and **MySQL** in the XAMPP Control Panel
2. Open your browser and visit:

```
http://localhost/jan-thomas/index.php
```

---

## 🚀 Setup Option 2 — Laravel Version

### 1. Create the Laravel project

```bash
composer create-project laravel/laravel ride-task-manager
cd ride-task-manager
```

### 2. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=janthomas
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Create the tasks table

```bash
php artisan make:model Task -m
```

Edit the migration:

```php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('task_name');
        $table->text('description')->nullable();
        $table->enum('status', ['Pending', 'Completed'])->default('Pending');
        $table->date('due_date')->nullable();
        $table->timestamps();
    });
}
```

```bash
php artisan migrate
```

### 4. Create the Task model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['task_name', 'description', 'status', 'due_date'];
}
```

### 5. Create the controller

```bash
php artisan make:controller TaskController --resource
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('due_date', 'asc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        Task::create($request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]));

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,Completed',
            'due_date' => 'nullable|date',
        ]));

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function toggle(Task $task)
    {
        $task->update([
            'status' => $task->status === 'Pending' ? 'Completed' : 'Pending',
        ]);

        return redirect()->route('tasks.index');
    }
}
```

### 6. Add the routes

Edit `routes/web.php`:

```php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('tasks', TaskController::class);
Route::get('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
```

### 7. Create the Blade views

- `resources/views/tasks/index.blade.php` — task table with toggle & delete actions
- `resources/views/tasks/edit.blade.php` — edit form
- Move the dark rider-themed CSS into `public/css/style.css` (or Vite)

### 8. Run the dev server

```bash
php artisan serve
```

Visit: `http://localhost:8000/tasks`

---

## 📖 Usage

| Action | How |
|--------|-----|
| ➕ Add task | Open `add.php`, fill in the form, click **Add Task** |
| ✏️ Edit task | Click **Edit** on any row |
| ✅ Complete / Pending | Click the toggle button on any row |
| 🗑️ Delete task | Click **Delete** on any row |

---

## 🧩 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend (Classic) | PHP 8.x (PDO, prepared statements) |
| Backend (Laravel) | Laravel 10.x (MVC, Eloquent ORM) |
| Database | MySQL / MariaDB |
| Frontend | HTML5, CSS3 (dark theme, neon accents) |
| Templating (Laravel) | Blade |

---

## 🔒 Security Notes

- All database queries use **PDO prepared statements** — no SQL injection
- Input is sanitized with `trim()` before saving
- Use `htmlspecialchars()` when echoing user data in views for XSS protection

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

<p align="center">🏍️ Ride safe. Stay organized.</p>
