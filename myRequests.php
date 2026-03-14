<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id   = sanitize_int($_SESSION['id']);
$user_role = sanitize_string($_SESSION['role']);

$success_message = "";
$error_message   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status']) && $user_role == 'admin') {

    $request_id = sanitize_int($_POST['request_id']);
    $new_status = sanitize_string($_POST['status']);
    $remarks    = sanitize_text($_POST['remarks']);

    if (empty($request_id) || empty($new_status)) {
        $error_message = "Request ID and status are required.";
    }

    if (empty($error_message)) {
        $allowed_statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        if (!in_array($new_status, $allowed_statuses)) {
            $error_message = "Invalid status value.";
        }
    }

    if (empty($error_message)) {
        $con->begin_transaction();

        try {

            /* UPDATE CURRENT REQUEST */
            $stmt = $con->prepare("UPDATE adoption_requests 
                           SET status = ?, remarks = ? 
                           WHERE request_id = ?");
            $stmt->bind_param("ssi", $new_status, $remarks, $request_id);
            $stmt->execute();
            $stmt->close();


            /* IF APPROVED -> UPDATE PET + REJECT OTHERS */
            if ($new_status == 'approved') {

                /* GET PET ID OF REQUEST */
                $stmt = $con->prepare("SELECT pet_id FROM adoption_requests WHERE request_id = ?");
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $pet_id = $row['pet_id'];
                $stmt->close();


                /* UPDATE PET STATUS */
                $stmt = $con->prepare("UPDATE pets SET status = 'adopted' WHERE id = ?");
                $stmt->bind_param("i", $pet_id);
                $stmt->execute();
                $stmt->close();


                /* REJECT OTHER REQUESTS */
                $stmt = $con->prepare("UPDATE adoption_requests 
                               SET status = 'rejected' 
                               WHERE pet_id = ? 
                               AND request_id != ?");
                $stmt->bind_param("ii", $pet_id, $request_id);
                $stmt->execute();
                $stmt->close();
            }

            $con->commit();
            $success_message = "Request status updated successfully!";
        } catch (Exception $e) {

            $con->rollback();
            $error_message = "Error updating request: " . $e->getMessage();
        }
    }
}

if ($user_role == 'admin') {
    // Admin only sees requests for pets they added
    $stmt = $con->prepare("SELECT ar.*, 
                                  p.name as pet_name, p.breed, p.image, p.category,
                                  u.full_name as user_name, 
                                  u.email as user_email, 
                                  u.phone_num as user_phone
                           FROM adoption_requests ar
                           JOIN pets p ON ar.pet_id = p.id
                           JOIN users u ON ar.user_id = u.id
                           WHERE p.admin_id = ?
                           ORDER BY ar.created_at DESC");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $con->prepare("SELECT ar.*, 
                                  p.name as pet_name, p.breed, p.image, p.category
                           FROM adoption_requests ar
                           JOIN pets p ON ar.pet_id = p.id
                           WHERE ar.user_id = ?
                           ORDER BY ar.created_at DESC");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$requests_result = $stmt->get_result();
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
                    <?php echo $user_role == 'admin' ? 'Adoption Requests for My Pets' : 'My Adoption Requests'; ?>
                </h1>
                <p><?php echo $user_role == 'admin' ? 'Manage adoption requests for pets you added' : 'Track your pet adoption applications'; ?></p>
            </div>

            <?php
            if (!empty($success_message)) {
                echo '<div class="message success">' . e($success_message) . '</div>';
            }
            if (!empty($error_message)) {
                echo '<div class="message error">' . e($error_message) . '</div>';
            }
            ?>

            <?php
            if (mysqli_num_rows($requests_result) == 0) {
                echo '<div class="empty-requests">
                        <i class="fas fa-inbox"></i>
                        <h3>No Adoption Requests Found</h3>
                        <p>' . ($user_role == 'admin' ? 'No users have submitted requests for your pets yet.' : 'You haven\'t submitted any adoption requests yet.') . '</p>';
                if ($user_role != 'admin') {
                    echo '<a href="browsePets.php" class="btn-browse-pets"><i class="fas fa-paw"></i> Browse Pets</a>';
                }
                echo '</div>';
            } else {
                echo '<div class="requests-list">';
                while ($request = mysqli_fetch_assoc($requests_result)) {
                    echo '
                    <div class="request-card">
                        <div class="request-card-header">
                            <div class="request-pet-info">
                                <img src="assets/images/' . e($request['image'] ?? 'default_pet.jpg') . '" alt="' . e($request['pet_name']) . '">
                                <div class="request-pet-details">
                                    <h3>' . e($request['pet_name']) . '</h3>
                                    <p class="pet-meta">
                                        <i class="fas fa-paw"></i> ' . e($request['breed']) . ' • ' . e($request['category']) . '
                                    </p>';

                    if ($user_role == 'admin') {
                        echo '
                                    <p class="applicant-meta">
                                        <i class="fas fa-user"></i> ' . e($request['user_name']) . '<br>
                                        <i class="fas fa-envelope"></i> ' . e($request['user_email']) . '<br>
                                        <i class="fas fa-phone"></i> ' . e($request['user_phone']) . '
                                    </p>';
                    }

                    echo '
                                </div>
                            </div>
                            <div class="request-status-badge">
                                <span class="status-badge status-' . e($request['status']) . '">
                                    ' . ucfirst(e($request['status'])) . '
                                </span>
                                <p class="request-date">
                                    <i class="fas fa-calendar"></i>
                                    ' . date('M d, Y', strtotime($request['created_at'])) . '
                                </p>
                            </div>
                        </div>

                        <div class="request-card-body">
                            <div class="request-info-grid">
                                <div class="info-box">
                                    <h4><i class="fas fa-comment-dots"></i> Reason for Adoption</h4>
                                    <p>' . nl2br(e($request['reason'])) . '</p>
                                </div>

                                <div class="info-box">
                                    <h4><i class="fas fa-paw"></i> Pet Experience</h4>
                                    <p><strong>Has Experience:</strong> ' . ucfirst(e($request['has_experience'])) . '</p>';

                    if (!empty($request['experience_details'])) {
                        echo '<p class="details-text">' . nl2br(e($request['experience_details'])) . '</p>';
                    }

                    echo '
                                </div>

                                <div class="info-box">
                                    <h4><i class="fas fa-home"></i> Housing Information</h4>
                                    <p><strong>Type:</strong> ' . ucfirst(e($request['housing_type'])) . '</p>
                                    <p><strong>Adequate Space:</strong> ' . ucfirst(e($request['has_space'])) . '</p>
                                </div>

                                <div class="info-box">
                                    <h4><i class="fas fa-cat"></i> Other Pets</h4>
                                    <p><strong>Has Other Pets:</strong> ' . ucfirst(e($request['has_other_pets'])) . '</p>';

                    if (!empty($request['other_pets_details'])) {
                        echo '<p class="details-text">' . nl2br(e($request['other_pets_details'])) . '</p>';
                    }

                    echo '</div>';

                    if (!empty($request['additional_notes'])) {
                        echo '
                                <div class="info-box full-width">
                                    <h4><i class="fas fa-sticky-note"></i> Additional Notes</h4>
                                    <p>' . nl2br(e($request['additional_notes'])) . '</p>
                                </div>';
                    }

                    if (!empty($request['remarks'])) {
                        echo '
                                <div class="info-box full-width admin-remarks">
                                    <h4><i class="fas fa-user-shield"></i> Admin Remarks</h4>
                                    <p>' . nl2br(e($request['remarks'])) . '</p>
                                </div>';
                    }

                    echo '</div>';

                    if ($user_role == 'admin') {
                        echo '
                            <div class="admin-actions">
                                <button class="btn-manage-request" onclick="openStatusModal(' . sanitize_int($request['request_id']) . ', \'' . e($request['status']) . '\', \'' . addslashes(e($request['remarks'] ?? '')) . '\')">
                                    <i class="fas fa-edit"></i> Manage Request
                                </button>
                            </div>';
                    }

                    echo '
                        </div>
                    </div>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <?php
    if ($user_role == 'admin') {
        echo '
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Update Request Status</h3>
            <form method="POST" action="">
                <input type="hidden" name="request_id" id="modal_request_id">

                <div class="modal-form-group">
                    <label for="modal_status">Status <span class="required">*</span></label>
                    <select name="status" id="modal_status">
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
            document.getElementById("modal_request_id").value = requestId;
            document.getElementById("modal_status").value = currentStatus;
            document.getElementById("modal_remarks").value = currentRemarks;
            document.getElementById("statusModal").style.display = "flex";
        }

        function closeStatusModal() {
            document.getElementById("statusModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("statusModal");
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>';
    }
    ?>
</body>
<?php include("footer.php"); ?>

</html>