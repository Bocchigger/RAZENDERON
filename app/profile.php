<?php
$pageTitle = 'User Profile - Razenderon';
# include 'config/database.php';
include '_header.php';

// Access control:
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['id'];
$update_message = '';

// Handle profile update POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);

    // Basic validation
    if (!empty($fullName) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $sql = "UPDATE account SET fullName = :fullName, email = :email WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'fullName' => $fullName, 
                'email' => $email, 
                'id' => $user_id
                ]);
            $update_message = '<p style="color: #8aff8a;">Profile updated successfully!</p>';
        } catch (PDOException $e) {
            // Handle potential duplicate email error
            if ($e->errorInfo[1] == 1062) {
                 $update_message = '<p class="error-message">Error: This email address is already in use.</p>';
            } else {
                 $update_message = '<p class="error-message">An error occurred while updating your profile.</p>';
            }
        }
    } else {
        $update_message = '<p class="error-message">Please provide a valid full name and email address.</p>';
    }
}

// Fetch current user data to display
try {
    $stmt = $db->prepare("SELECT username, fullName, email, createdAt FROM account WHERE id = ?");
    $stmt->execute([$user_id]);
    $userProfile = $stmt->fetch();
} catch (Exception $e) {
    // Kill script if user data can't be fetched, as the page is useless without it.
    die("Could not retrieve user profile data.");
}


// Load the view template
ob_start();
include 'view/profile.html';
$content = ob_get_clean();

// Replace placeholders in the view
$content = str_replace('{{update_message}}', $update_message, $content);
$content = str_replace('{{username}}', htmlspecialchars($userProfile['username']), $content);
$content = str_replace('{{fullName}}', htmlspecialchars($userProfile['fullName']), $content);
$content = str_replace('{{email}}', htmlspecialchars($userProfile['email']), $content);
$content = str_replace('{{createdAt}}', htmlspecialchars(date('F j, Y', strtotime($userProfile['createdAt']))), $content);

echo $content;

include '_footer.php';
