<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['pet_id']) || empty($_GET['pet_id'])) {
    header("Location: browsePets.php");
    exit();
}

$pet_id  = sanitize_int($_GET['pet_id']);
$user_id = $_SESSION['id'];

$stmt = $con->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
$pet    = $result->fetch_assoc();
$stmt->close();

if (!$pet) {
    header("Location: browsePets.php");
    exit();
}

if ($pet['status'] != 'available') {
    header("Location: pet-details.php?id=$pet_id");
    exit();
}

$stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

$success_message = "";
$error_message   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $applicant_name      = sanitize_string($_POST['full_name']);
    $applicant_email     = sanitize_email($_POST['email']);
    $applicant_phone     = sanitize_phone($_POST['phone_num']);
    $reason              = sanitize_text($_POST['reason']);
    $has_experience      = sanitize_string($_POST['has_experience'] ?? '');
    $experience_details  = sanitize_text($_POST['experience_details'] ?? '');
    $housing_type        = sanitize_string($_POST['housing_type'] ?? '');
    $has_space           = sanitize_string($_POST['has_space'] ?? '');
    $has_other_pets      = sanitize_string($_POST['has_other_pets'] ?? '');
    $other_pets_details  = sanitize_text($_POST['other_pets_details'] ?? '');
    $commitment_confirmed = sanitize_string($_POST['commitment_confirmed'] ?? '');
    $additional_notes    = sanitize_text($_POST['additional_notes'] ?? '');

    if (empty($applicant_name) || empty($applicant_email) || empty($applicant_phone)) {
        $error_message = "Please fill in all your personal information.";
    }

    if (empty($error_message)) {
        if (!filter_var($applicant_email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Enter a valid email address.";
        }
    }

    if (empty($error_message)) {
        if (!preg_match('/^(98|97)\d{8}$/', $applicant_phone)) {
            $error_message = "Phone number must be a valid 10-digit Nepali number starting with 98 or 97.";
        }
    }

    if (empty($error_message)) {
        if (empty($reason)) {
            $error_message = "Please provide a reason for adoption.";
        }
    }

    if (empty($error_message)) {
        if (empty($has_experience)) {
            $error_message = "Please indicate whether you have pet experience.";
        }
    }

    if (empty($error_message)) {
        if (empty($housing_type)) {
            $error_message = "Please select your housing type.";
        }
    }

    if (empty($error_message)) {
        if (empty($has_space)) {
            $error_message = "Please indicate whether you have adequate space for a pet.";
        }
    }

    if (empty($error_message)) {
        if (empty($has_other_pets)) {
            $error_message = "Please indicate whether you have other pets.";
        }
    }

    if (empty($error_message)) {
        if ($commitment_confirmed != 'yes') {
            $error_message = "You must confirm your commitment to adopt this pet.";
        }
    }

    if (empty($error_message)) {
        $stmt = $con->prepare("INSERT INTO adoption_requests 
            (pet_id, user_id, reason, has_experience, experience_details, housing_type, 
            has_space, has_other_pets, other_pets_details, commitment_confirmed, additional_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iisssssssss",
            $pet_id,
            $user_id,
            $reason,
            $has_experience,
            $experience_details,
            $housing_type,
            $has_space,
            $has_other_pets,
            $other_pets_details,
            $commitment_confirmed,
            $additional_notes
        );
        if ($stmt->execute()) {
            $success_message = "Your adoption request has been submitted successfully!";
            header("refresh:3;url=browsePets.php");
        } else {
            $error_message = "Error submitting request: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adoption Request | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="adoption-request-wrapper">
        <div class="adoption-request-container">
            <div class="adoption-header">
                <h1><i class="fas fa-heart"></i> Adoption Request Form</h1>
                <p>Apply to adopt <?php echo e($pet['name']); ?></p>
            </div>

            <?php
            if (!empty($success_message)) {
                echo '<div class="message success">' . e($success_message) . '</div>';
            }
            if (!empty($error_message)) {
                echo '<div class="message error">' . e($error_message) . '</div>';
            }
            ?>

            <div class="pet-summary-card">
                <img src="assets/images/<?php echo e($pet['image'] ?? 'default_pet.jpg'); ?>" 
                     alt="<?php echo e($pet['name']); ?>">
                <div class="pet-summary-info">
                    <h2><?php echo e($pet['name']); ?></h2>
                    <p><strong>Breed:</strong> <?php echo e($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo e($pet['age']); ?> months</p>
                    <p><strong>Gender:</strong> <?php echo e($pet['gender']); ?></p>
                </div>
            </div>

            <form method="POST" action="" class="adoption-form">

                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Your Information</h3>
                    <p class="info-note">Your information is pre-filled from your profile. You can modify it if needed for this application.</p>
                    <div class="applicant-form-grid">
                        <div class="form-group-half">
                            <label for="full_name">Full Name <span class="required">*</span></label>
                            <input type="text" id="full_name" name="full_name"
                                value="<?php echo isset($_POST['full_name']) ? e($_POST['full_name']) : e($user['full_name']); ?>">
                        </div>
                        <div class="form-group-half">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email"
                                value="<?php echo isset($_POST['email']) ? e($_POST['email']) : e($user['email']); ?>">
                        </div>
                        <div class="form-group-half">
                            <label for="phone_num">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone_num" name="phone_num"
                                value="<?php echo isset($_POST['phone_num']) ? e($_POST['phone_num']) : e($user['phone_num']); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-question-circle"></i> Why Do You Want to Adopt?</h3>
                    <div class="form-group-full">
                        <label for="reason">Reason for Adoption <span class="required">*</span></label>
                        <textarea id="reason" name="reason" rows="4"
                            placeholder="Tell us why you want to adopt <?php echo e($pet['name']); ?>..."><?php echo isset($_POST['reason']) ? e($_POST['reason']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-paw"></i> Pet Experience</h3>
                    <div class="form-group-full">
                        <label>Do you have experience with pets? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="has_experience" value="yes"
                                    <?php echo (isset($_POST['has_experience']) && $_POST['has_experience'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_experience" value="no"
                                    <?php echo (isset($_POST['has_experience']) && $_POST['has_experience'] == 'no') ? 'checked' : ''; ?>>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label for="experience_details">Please describe your experience (optional)</label>
                        <textarea id="experience_details" name="experience_details" rows="3"
                            placeholder="Share your experience with pets..."><?php echo isset($_POST['experience_details']) ? e($_POST['experience_details']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-home"></i> Housing Information</h3>
                    <div class="form-group-full">
                        <label>Housing Type <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="housing_type" value="owned"
                                    <?php echo (isset($_POST['housing_type']) && $_POST['housing_type'] == 'owned') ? 'checked' : ''; ?>>
                                <span>Owned</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="housing_type" value="rented"
                                    <?php echo (isset($_POST['housing_type']) && $_POST['housing_type'] == 'rented') ? 'checked' : ''; ?>>
                                <span>Rented</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label>Do you have adequate space for a pet? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="has_space" value="yes"
                                    <?php echo (isset($_POST['has_space']) && $_POST['has_space'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_space" value="no"
                                    <?php echo (isset($_POST['has_space']) && $_POST['has_space'] == 'no') ? 'checked' : ''; ?>>
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-cat"></i> Other Pets</h3>
                    <div class="form-group-full">
                        <label>Do you have other pets? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="has_other_pets" value="yes"
                                    <?php echo (isset($_POST['has_other_pets']) && $_POST['has_other_pets'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_other_pets" value="no"
                                    <?php echo (isset($_POST['has_other_pets']) && $_POST['has_other_pets'] == 'no') ? 'checked' : ''; ?>>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label for="other_pets_details">If yes, please provide details (optional)</label>
                        <textarea id="other_pets_details" name="other_pets_details" rows="3"
                            placeholder="Describe your other pets..."><?php echo isset($_POST['other_pets_details']) ? e($_POST['other_pets_details']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-check-circle"></i> Commitment</h3>
                    <div class="form-group-full">
                        <label class="checkbox-label">
                            <input type="checkbox" name="commitment_confirmed" value="yes">
                            <span>I confirm that I am committed to providing a loving and caring home for <?php echo e($pet['name']); ?> <span class="required">*</span></span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-comment"></i> Additional Notes</h3>
                    <div class="form-group-full">
                        <label for="additional_notes">Any additional information (optional)</label>
                        <textarea id="additional_notes" name="additional_notes" rows="3"
                            placeholder="Share any additional information..."><?php echo isset($_POST['additional_notes']) ? e($_POST['additional_notes']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit-request">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                    <a href="pet-details.php?id=<?php echo $pet_id; ?>" class="btn-cancel-request">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
<?php include("footer.php"); ?>
</html>