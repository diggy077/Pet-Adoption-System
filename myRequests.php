
<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status']) && $user_role == 'admin') {
    $request_id = mysqli_real_escape_string($con, $_POST['request_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);
    $remarks = mysqli_real_escape_string($con, $_POST['remarks']);
    
    $update_query = "UPDATE adoption_requests 
                     SET status = '$new_status', remarks = '$remarks' 
                     WHERE request_id = '$request_id'";
    
    if (mysqli_query($con, $update_query)) {
        $success_message = "Request status updated successfully!";
    } else {
        $error_message = "Error updating status: " . mysqli_error($con);
    }
}

if ($user_role == 'admin') {
    $requests_query = "SELECT ar.*, p.name as pet_name, p.breed, p.image, p.category, 
                              u.full_name as user_name, u.email as user_email, u.phone_num as user_phone
                       FROM adoption_requests ar
                       JOIN pets p ON ar.pet_id = p.id
                       JOIN users u ON ar.user_id = u.id
                       ORDER BY ar.created_at DESC";
} else {
    $requests_query = "SELECT ar.*, p.name as pet_name, p.breed, p.image, p.category
                       FROM adoption_requests ar
                       JOIN pets p ON ar.pet_id = p.id
                       WHERE ar.user_id = '$user_id'
                       ORDER BY ar.created_at DESC";
}

$requests_result = mysqli_query($con, $requests_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Adoption Requests | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="requests-wrapper">
        <div class="requests-container">
            <div class="requests-header">
                <h1><i class="fas fa-clipboard-list"></i> 
                    <?php echo $user_role == 'admin' ? 'All Adoption Requests' : 'My Adoption Requests'; ?>
                </h1>
                <p><?php echo $user_role == 'admin' ? 'Manage all adoption requests from users' : 'Track your pet adoption applications'; ?></p>
            </div>

            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($requests_result) == 0): ?>
                <div class="empty-requests">
                    <i class="fas fa-inbox"></i>
                    <h3>No Adoption Requests Found</h3>
                    <p><?php echo $user_role == 'admin' ? 'No users have submitted adoption requests yet.' : 'You haven\'t submitted any adoption requests yet.'; ?></p>
                    <?php if ($user_role != 'admin'): ?>
                        <a href="browsePets.php" class="btn-browse-pets">
                            <i class="fas fa-paw"></i> Browse Pets
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="requests-list">
                    <?php while ($request = mysqli_fetch_assoc($requests_result)): ?>
                        <div class="request-card">
                            <div class="request-card-header">
                                <div class="request-pet-info">
                                    <img src="assets/images/<?php echo $request['image'] ?? 'default_pet.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($request['pet_name']); ?>">
                                    <div class="request-pet-details">
                                        <h3><?php echo htmlspecialchars($request['pet_name']); ?></h3>
                                        <p class="pet-meta">
                                            <i class="fas fa-paw"></i> <?php echo htmlspecialchars($request['breed']); ?> • 
                                            <?php echo htmlspecialchars($request['category']); ?>
                                        </p>
                                        <?php if ($user_role == 'admin'): ?>
                                            <p class="applicant-meta">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($request['user_name']); ?><br>
                                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($request['user_email']); ?><br>
                                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($request['user_phone']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="request-status-badge">
                                    <span class="status-badge status-<?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                    <p class="request-date">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="request-card-body">
                                <div class="request-info-grid">
                                    <div class="info-box">
                                        <h4><i class="fas fa-comment-dots"></i> Reason for Adoption</h4>
                                        <p><?php echo nl2br(htmlspecialchars($request['reason'])); ?></p>
                                    </div>

                                    <div class="info-box">
                                        <h4><i class="fas fa-paw"></i> Pet Experience</h4>
                                        <p><strong>Has Experience:</strong> <?php echo ucfirst($request['has_experience']); ?></p>
                                        <?php if (!empty($request['experience_details'])): ?>
                                            <p class="details-text"><?php echo nl2br(htmlspecialchars($request['experience_details'])); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="info-box">
                                        <h4><i class="fas fa-home"></i> Housing Information</h4>
                                        <p><strong>Type:</strong> <?php echo ucfirst($request['housing_type']); ?></p>
                                        <p><strong>Adequate Space:</strong> <?php echo ucfirst($request['has_space']); ?></p>
                                    </div>

                                    <div class="info-box">
                                        <h4><i class="fas fa-cat"></i> Other Pets</h4>
                                        <p><strong>Has Other Pets:</strong> <?php echo ucfirst($request['has_other_pets']); ?></p>
                                        <?php if (!empty($request['other_pets_details'])): ?>
                                            <p class="details-text"><?php echo nl2br(htmlspecialchars($request['other_pets_details'])); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($request['additional_notes'])): ?>
                                        <div class="info-box full-width">
                                            <h4><i class="fas fa-sticky-note"></i> Additional Notes</h4>
                                            <p><?php echo nl2br(htmlspecialchars($request['additional_notes'])); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($request['remarks'])): ?>
                                        <div class="info-box full-width admin-remarks">
                                            <h4><i class="fas fa-user-shield"></i> Admin Remarks</h4>
                                            <p><?php echo nl2br(htmlspecialchars($request['remarks'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($user_role == 'admin'): ?>
                                    <div class="admin-actions">
                                        <button class="btn-manage-request" onclick="openStatusModal(<?php echo $request['request_id']; ?>, '<?php echo $request['status']; ?>', '<?php echo addslashes($request['remarks'] ?? ''); ?>')">
                                            <i class="fas fa-edit"></i> Manage Request
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($user_role == 'admin'): ?>
    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Update Request Status</h3>
            <form method="POST" action="">
                <input type="hidden" name="request_id" id="modal_request_id">
                
                <div class="modal-form-group">
                    <label for="modal_status">Status <span class="required">*</span></label>
                    <select name="status" id="modal_status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="modal-form-group">
                    <label for="modal_remarks">Admin Remarks</label>
                    <textarea name="remarks" id="modal_remarks" rows="4" 
                        placeholder="Add any notes or feedback for the applicant..."></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="submit" name="update_status" class="modal-btn-confirm">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                    <button type="button" class="modal-btn-cancel" onclick="closeStatusModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(requestId, currentStatus, currentRemarks) {
            document.getElementById('modal_request_id').value = requestId;
            document.getElementById('modal_status').value = currentStatus;
            document.getElementById('modal_remarks').value = currentRemarks;
            document.getElementById('statusModal').style.display = 'flex';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>
    <?php endif; ?>
</body>
<?php include("footer.php"); ?>
</html>