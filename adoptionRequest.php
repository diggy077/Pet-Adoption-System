<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['pet_id']) || empty($_GET['pet_id'])) {
    header("Location: browsePets.php");
    exit();
}

$pet_id = mysqli_real_escape_string($con, $_GET['pet_id']);
$user_id = $_SESSION['id'];

$pet_query = "SELECT * FROM pets WHERE id = '$pet_id'";
$pet_result = mysqli_query($con, $pet_query);

if (!$pet_result || mysqli_num_rows($pet_result) == 0) {
    header("Location: browsePets.php");
    exit();
}

$pet = mysqli_fetch_assoc($pet_result);

if ($pet['status'] != 'available') {
    header("Location: petDetails.php?id=$pet_id");
    exit();
}

$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($con, $user_query);
$user = mysqli_fetch_assoc($user_result);

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $applicant_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $applicant_email = mysqli_real_escape_string($con, $_POST['email']);
    $applicant_phone = mysqli_real_escape_string($con, $_POST['phone_num']);
    
    $reason = mysqli_real_escape_string($con, $_POST['reason']);
    $has_experience = mysqli_real_escape_string($con, $_POST['has_experience']);
    $experience_details = mysqli_real_escape_string($con, $_POST['experience_details']);
    $housing_type = mysqli_real_escape_string($con, $_POST['housing_type']);
    $has_space = mysqli_real_escape_string($con, $_POST['has_space']);
    $has_other_pets = mysqli_real_escape_string($con, $_POST['has_other_pets']);
    $other_pets_details = mysqli_real_escape_string($con, $_POST['other_pets_details']);
    $commitment_confirmed = mysqli_real_escape_string($con, $_POST['commitment_confirmed']);
    $additional_notes = mysqli_real_escape_string($con, $_POST['additional_notes']);

    // Validation
    if (empty($applicant_name) || empty($applicant_email) || empty($applicant_phone)) {
        $error_message = "Please fill in all your personal information.";
    } elseif (empty($reason)) {
        $error_message = "Please provide a reason for adoption.";
    } elseif ($commitment_confirmed != 'yes') {
        $error_message = "You must confirm your commitment to adopt this pet.";
    } else {
        // Insert adoption request
        $insert_query = "INSERT INTO adoption_requests 
            (pet_id, user_id, reason, has_experience, experience_details, housing_type, 
            has_space, has_other_pets, other_pets_details, commitment_confirmed, additional_notes) 
            VALUES 
            ('$pet_id', '$user_id', '$reason', '$has_experience', '$experience_details', 
            '$housing_type', '$has_space', '$has_other_pets', '$other_pets_details', 
            '$commitment_confirmed', '$additional_notes')";

        if (mysqli_query($con, $insert_query)) {
            $success_message = "Your adoption request has been submitted successfully!";
            // Optionally redirect after a few seconds
            header("refresh:3;url=browsePets.php");
        } else {
            $error_message = "Error submitting request: " . mysqli_error($con);
        }
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
                <p>Apply to adopt <?php echo htmlspecialchars($pet['name']); ?></p>
            </div>

            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="pet-summary-card">
                <img src="assets/images/<?php echo $pet['image'] ?? 'default_pet.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($pet['name']); ?>">
                <div class="pet-summary-info">
                    <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> months</p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
                </div>
            </div>

            <form method="POST" action="" class="adoption-form">

                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Your Information</h3>
                    <p class="info-note">Your information is pre-filled from your profile. You can modify it if needed for this application.</p>
                    <div class="applicant-form-grid">
                        <div class="form-group-half">
                            <label for="full_name">Full Name <span class="required">*</span></label>
                            <input type="text" id="full_name" name="full_name" required
                                value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($user['full_name']); ?>">
                        </div>
                        <div class="form-group-half">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="form-group-half">
                            <label for="phone_num">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone_num" name="phone_num" required
                                value="<?php echo isset($_POST['phone_num']) ? htmlspecialchars($_POST['phone_num']) : htmlspecialchars($user['phone_num']); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-question-circle"></i> Why Do You Want to Adopt?</h3>
                    <div class="form-group-full">
                        <label for="reason">Reason for Adoption <span class="required">*</span></label>
                        <textarea id="reason" name="reason" rows="4" required 
                            placeholder="Tell us why you want to adopt <?php echo htmlspecialchars($pet['name']); ?>..."><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-paw"></i> Pet Experience</h3>
                    <div class="form-group-full">
                        <label>Do you have experience with pets? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="has_experience" value="yes" required
                                    <?php echo (isset($_POST['has_experience']) && $_POST['has_experience'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_experience" value="no" required
                                    <?php echo (isset($_POST['has_experience']) && $_POST['has_experience'] == 'no') ? 'checked' : ''; ?>>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label for="experience_details">Please describe your experience (optional)</label>
                        <textarea id="experience_details" name="experience_details" rows="3" 
                            placeholder="Share your experience with pets..."><?php echo isset($_POST['experience_details']) ? htmlspecialchars($_POST['experience_details']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-home"></i> Housing Information</h3>
                    <div class="form-group-full">
                        <label>Housing Type <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="housing_type" value="owned" required
                                    <?php echo (isset($_POST['housing_type']) && $_POST['housing_type'] == 'owned') ? 'checked' : ''; ?>>
                                <span>Owned</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="housing_type" value="rented" required
                                    <?php echo (isset($_POST['housing_type']) && $_POST['housing_type'] == 'rented') ? 'checked' : ''; ?>>
                                <span>Rented</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label>Do you have adequate space for a pet? <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="has_space" value="yes" required
                                    <?php echo (isset($_POST['has_space']) && $_POST['has_space'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_space" value="no" required
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
                                <input type="radio" name="has_other_pets" value="yes" required
                                    <?php echo (isset($_POST['has_other_pets']) && $_POST['has_other_pets'] == 'yes') ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="has_other_pets" value="no" required
                                    <?php echo (isset($_POST['has_other_pets']) && $_POST['has_other_pets'] == 'no') ? 'checked' : ''; ?>>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-full">
                        <label for="other_pets_details">If yes, please provide details (optional)</label>
                        <textarea id="other_pets_details" name="other_pets_details" rows="3" 
                            placeholder="Describe your other pets..."><?php echo isset($_POST['other_pets_details']) ? htmlspecialchars($_POST['other_pets_details']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-check-circle"></i> Commitment</h3>
                    <div class="form-group-full">
                        <label class="checkbox-label">
                            <input type="checkbox" name="commitment_confirmed" value="yes" required>
                            <span>I confirm that I am committed to providing a loving and caring home for <?php echo htmlspecialchars($pet['name']); ?> <span class="required">*</span></span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-comment"></i> Additional Notes</h3>
                    <div class="form-group-full">
                        <label for="additional_notes">Any additional information (optional)</label>
                        <textarea id="additional_notes" name="additional_notes" rows="3" 
                            placeholder="Share any additional information..."><?php echo isset($_POST['additional_notes']) ? htmlspecialchars($_POST['additional_notes']) : ''; ?></textarea>
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