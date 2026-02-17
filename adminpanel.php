<?php
session_start();
include('connection.php');
include('navbar.php');

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

$currentUserId = $_SESSION['id'];
$message = '';
$messageType = '';

/* ---------- PROMOTE / DEMOTE ---------- */
if (isset($_POST['action'])) {

    $userId = (int)$_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'promote') {
        mysqli_query($con, "UPDATE users SET role='admin' WHERE id=$userId");
        $message = "User successfully promoted to Shelter!";
        $messageType = "success";
    }

    if ($action === 'demote') {
        mysqli_query($con, "UPDATE users SET role='user' WHERE id=$userId");
        $message = "User successfully demoted to Adopter!";
        $messageType = "success";
    }
}

/* ---------- FILTER ---------- */
$roleFilter = isset($_GET['role']) ? $_GET['role'] : 'all';

/* ---------- FETCH USERS ---------- */
$sql = "SELECT id, full_name, email, role FROM users WHERE id != $currentUserId";

if ($roleFilter !== 'all') {
    $sql .= " AND role='$roleFilter'";
}

$sql .= " ORDER BY id ASC";

$result = mysqli_query($con, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

/* ---------- STATISTICS ---------- */
$statsQuery = "
SELECT 
    COUNT(*) AS total_users,
    SUM(role='user') AS total_adopters,
    SUM(role='admin') AS total_shelters,
    SUM(role='superadmin') AS total_superadmins
FROM users";

$stats = mysqli_fetch_assoc(mysqli_query($con, $statsQuery));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pet Adoption System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Admin Panel Container -->
    <div class="admin-panel-container">
        <!-- Header -->
        <div class="panel-header">
            <h1><i class="fas fa-shield-alt"></i> Super Admin Panel</h1>
            <p>Manage users, assign roles, and oversee the pet adoption system</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Total Users</h3>
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user"></i>
                <h3>Adopters</h3>
                <div class="stat-number"><?php echo $stats['total_adopters']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-home"></i>
                <h3>Shelters</h3>
                <div class="stat-number"><?php echo $stats['total_shelters']; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-crown"></i>
                <h3>Super Admins</h3>
                <div class="stat-number"><?php echo $stats['total_superadmins']; ?></div>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="management-section">
            <div class="section-header">
                <h2><i class="fas fa-users-cog"></i> User Management</h2>
            </div>

            <!-- Filter -->
            <form method="GET" action="adminpanel.php" class="filter-search-container">
                <div class="filter-box">
                    <select name="role" onchange="this.form.submit()">
                        <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>All Roles</option>
                        <option value="user" <?php echo $roleFilter === 'user' ? 'selected' : ''; ?>>Adopters</option>
                        <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Shelters</option>
                    </select>
                </div>
            </form>

            <!-- User Table -->
            <div class="user-table-container">
                <?php if (count($users) > 0): ?>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <?php
                                $roleClass = $user['role'] === 'superadmin' ? 'role-superadmin' : 
                                            ($user['role'] === 'admin' ? 'role-admin' : 'role-user');
                                
                                $roleText = $user['role'] === 'superadmin' ? 'Super Admin' : 
                                           ($user['role'] === 'admin' ? 'Shelter' : 'Adopter');
                                ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="role-badge <?php echo $roleClass; ?>"><?php echo $roleText; ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($user['role'] !== 'admin'): ?>
                                                <button class="btn-action btn-promote" 
                                                        onclick="openModal(<?php echo $user['id']; ?>, 'promote', '<?php echo htmlspecialchars($user['full_name']); ?>')">
                                                    <i class="fas fa-arrow-up"></i> Make Shelter
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-demote" 
                                                        onclick="openModal(<?php echo $user['id']; ?>, 'demote', '<?php echo htmlspecialchars($user['full_name']); ?>')">
                                                    <i class="fas fa-arrow-down"></i> Remove Shelter
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>No users found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for Role Change Confirmation -->
    <div id="roleModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirm Action</h3>
            <p id="modalMessage">Are you sure you want to change this user's role?</p>
            <form method="POST" action="adminpanel.php" id="roleChangeForm">
                <input type="hidden" name="user_id" id="modalUserId">
                <input type="hidden" name="action" id="modalAction">
                <div class="modal-buttons">
                    <button type="submit" class="modal-btn modal-btn-confirm">Confirm</button>
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openModal(userId, action, userName) {
            const modal = document.getElementById('roleModal');
            const message = document.getElementById('modalMessage');
            const userIdInput = document.getElementById('modalUserId');
            const actionInput = document.getElementById('modalAction');
            
            userIdInput.value = userId;
            actionInput.value = action;
            
            if (action === 'promote') {
                message.textContent = `Are you sure you want to promote "${userName}" to Shelter role?`;
            } else {
                message.textContent = `Are you sure you want to demote "${userName}" to Adopter role?`;
            }
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('roleModal').classList.remove('active');
        }

        // Close modal on outside click
        // document.getElementById('roleModal').addEventListener('click', function(e) {
        //     if (e.target === this) {
        //         closeModal();
        //     }
        // });

        // Auto-hide alert messages after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>