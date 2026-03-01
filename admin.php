<?php
session_start();

// Admin credentials
$admin_username = '$$$$';
$admin_password = '$$$$';

// Login handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Logout handling
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check if logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle model operations
$models_file = 'models.json';
$message = '';
$message_type = '';

if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Load current models
    $models = file_exists($models_file) ? json_decode(file_get_contents($models_file), true) : [];
    
    // Add model
    if (isset($_POST['add_model'])) {
        $new_model = [
            'id' => $_POST['model_id'],
            'name' => $_POST['model_name'],
            'free' => isset($_POST['is_free'])
        ];
        
        // Check if model ID already exists
        $exists = false;
        foreach ($models as $model) {
            if ($model['id'] === $new_model['id']) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $models[] = $new_model;
            file_put_contents($models_file, json_encode($models, JSON_PRETTY_PRINT));
            $message = 'Model added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Model ID already exists!';
            $message_type = 'error';
        }
    }
    
    // Delete model
    if (isset($_POST['delete_model'])) {
        $model_id_to_delete = $_POST['model_id'];
        $models = array_filter($models, function($model) use ($model_id_to_delete) {
            return $model['id'] !== $model_id_to_delete;
        });
        $models = array_values($models); // Reindex array
        file_put_contents($models_file, json_encode($models, JSON_PRETTY_PRINT));
        $message = 'Model deleted successfully!';
        $message_type = 'success';
    }
    
    // Edit model
    if (isset($_POST['edit_model'])) {
        $original_id = $_POST['original_id'];
        $updated_model = [
            'id' => $_POST['model_id'],
            'name' => $_POST['model_name'],
            'free' => isset($_POST['is_free'])
        ];
        
        foreach ($models as &$model) {
            if ($model['id'] === $original_id) {
                $model = $updated_model;
                break;
            }
        }
        file_put_contents($models_file, json_encode($models, JSON_PRETTY_PRINT));
        $message = 'Model updated successfully!';
        $message_type = 'success';
    }
}

// Load models for display
$models = file_exists($models_file) ? json_decode(file_get_contents($models_file), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STWAI Admin Panel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #151515;
            --bg-tertiary: #202020;
            --border-color: #333;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-primary: #10a37f;
            --accent-hover: #0d8c6d;
            --accent-secondary: #6366f1;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-color);
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            background: linear-gradient(135deg, #10a37f, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--accent-primary);
            background: var(--bg-tertiary);
        }

        .logout-btn {
            background: var(--bg-tertiary);
            color: #ef4444;
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(135deg, #10a37f, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(16, 163, 127, 0.15);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 163, 127, 0.3);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 14px;
        }

        .success-message {
            background: rgba(16, 163, 127, 0.1);
            color: var(--accent-primary);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(16, 163, 127, 0.2);
            font-size: 14px;
        }

        .admin-panel {
            padding: 40px 0;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .panel-title {
            font-size: 28px;
            font-weight: 600;
            background: linear-gradient(135deg, #10a37f, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .add-model-btn {
            background: var(--accent-primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .add-model-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 163, 127, 0.3);
        }

        .models-table {
            background: var(--bg-secondary);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr 100px;
            padding: 16px 20px;
            background: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--text-secondary);
        }

        .table-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr 100px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-row:hover {
            background: rgba(16, 163, 127, 0.05);
        }

        .free-badge {
            background: rgba(16, 163, 127, 0.15);
            color: var(--accent-primary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .edit-btn {
            background: var(--accent-primary);
            color: white;
        }

        .edit-btn:hover {
            background: var(--accent-hover);
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            font-size: 14px;
            cursor: pointer;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-primary);
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-btn.save {
            background: var(--accent-primary);
            color: white;
        }

        .modal-btn.save:hover {
            background: var(--accent-hover);
        }

        .modal-btn.cancel {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .modal-btn.cancel:hover {
            background: var(--border-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="url(#gradient)" stroke-width="2"/>
                    <path d="M2 17L12 22L22 17" stroke="url(#gradient)" stroke-width="2"/>
                    <path d="M2 12L12 17L22 12" stroke="url(#gradient)" stroke-width="2"/>
                    <defs>
                        <linearGradient id="gradient" x1="2" y1="2" x2="22" y2="22">
                            <stop offset="0%" stop-color="#10a37f"/>
                            <stop offset="100%" stop-color="#6366f1"/>
                        </linearGradient>
                    </defs>
                </svg>
                STW<span>AI</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Back to Chat</a>
                <?php if ($is_logged_in): ?>
                    <a href="?logout=1" class="logout-btn">Logout</a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-container">
                <h1 class="login-title">Admin Login</h1>
                <?php if (isset($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <button type="submit" name="login" class="login-btn">Login</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Panel -->
            <div class="admin-panel">
                <div class="panel-header">
                    <h1 class="panel-title">Manage AI Models</h1>
                    <button class="add-model-btn" onclick="openAddModal()">+ Add New Model</button>
                </div>

                <?php if ($message): ?>
                    <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="models-table">
                    <div class="table-header">
                        <div>Model ID</div>
                        <div>Name</div>
                        <div>Status</div>
                        <div>Type</div>
                        <div>Actions</div>
                    </div>
                    <?php foreach ($models as $model): ?>
                        <div class="table-row">
                            <div style="color: var(--text-secondary); font-family: monospace; font-size: 13px;"><?php echo htmlspecialchars($model['id']); ?></div>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($model['name']); ?></div>
                            <div>
                                <?php if ($model['free']): ?>
                                    <span class="free-badge">FREE</span>
                                <?php else: ?>
                                    <span class="free-badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">PAID</span>
                                <?php endif; ?>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 13px;">AI Model</div>
                            <div class="action-buttons">
                                <button class="edit-btn" onclick="openEditModal('<?php echo htmlspecialchars($model['id']); ?>', '<?php echo htmlspecialchars($model['name']); ?>', <?php echo $model['free'] ? 'true' : 'false'; ?>)">Edit</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this model?');">
                                    <input type="hidden" name="model_id" value="<?php echo htmlspecialchars($model['id']); ?>">
                                    <button type="submit" name="delete_model" class="delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Add Model Modal -->
            <div class="modal" id="addModal">
                <div class="modal-content">
                    <h2 class="modal-title">Add New Model</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Model ID</label>
                            <input type="text" name="model_id" class="form-input" required placeholder="e.g., openai/gpt-4">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model Name</label>
                            <input type="text" name="model_name" class="form-input" required placeholder="e.g., GPT-4">
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_free" class="checkbox-input">
                                Free Model
                            </label>
                        </div>
                        <div class="modal-buttons">
                            <button type="submit" name="add_model" class="modal-btn save">Add Model</button>
                            <button type="button" class="modal-btn cancel" onclick="closeAddModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Model Modal -->
            <div class="modal" id="editModal">
                <div class="modal-content">
                    <h2 class="modal-title">Edit Model</h2>
                    <form method="POST">
                        <input type="hidden" name="original_id" id="editOriginalId">
                        <div class="form-group">
                            <label class="form-label">Model ID</label>
                            <input type="text" name="model_id" id="editModelId" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model Name</label>
                            <input type="text" name="model_name" id="editModelName" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_free" id="editIsFree" class="checkbox-input">
                                Free Model
                            </label>
                        </div>
                        <div class="modal-buttons">
                            <button type="submit" name="edit_model" class="modal-btn save">Update Model</button>
                            <button type="button" class="modal-btn cancel" onclick="closeEditModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openAddModal() {
                    document.getElementById('addModal').classList.add('active');
                }

                function closeAddModal() {
                    document.getElementById('addModal').classList.remove('active');
                }

                function openEditModal(id, name, isFree) {
                    document.getElementById('editOriginalId').value = id;
                    document.getElementById('editModelId').value = id;
                    document.getElementById('editModelName').value = name;
                    document.getElementById('editIsFree').checked = isFree;
                    document.getElementById('editModal').classList.add('active');
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.remove('active');
                }

                // Close modals when clicking outside
                window.onclick = function(event) {
                    const addModal = document.getElementById('addModal');
                    const editModal = document.getElementById('editModal');
                    if (event.target === addModal) {
                        closeAddModal();
                    }
                    if (event.target === editModal) {
                        closeEditModal();
                    }
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
