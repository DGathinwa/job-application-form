<?php
// PHP Configuration for better error handling during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the upload directory for resumes
// !! IMPORTANT: In a production environment, store uploaded files outside the web-accessible root if possible,
//    and serve them via a separate script to add another layer of security.
$uploadDir = 'uploads/'; // Make sure this directory exists and is writable by your web server!

// Initialize an array to store validation errors
$errors = [];

// --- 1. Receive and Sanitize Data ---
// Use null coalescing operator (?? '') for robustness
$fullName = htmlspecialchars(trim($_POST['full-name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$position = htmlspecialchars(trim($_POST['position'] ?? '')); // Sanitize even dropdowns for display
$gender = htmlspecialchars(trim($_POST['work_mode'] ?? 'N/A')); // Corrected name for preferred work mode
$skills = $_POST['skills'] ?? []; // This should be an array, no htmlspecialchars here if you're processing it as such
$bio = htmlspecialchars(trim($_POST['bio'] ?? ''));
$terms = isset($_POST['terms']); // Check if the checkbox was checked

// Resume file details (initialize for cases where no file is uploaded)
$resumeOriginalName = $_FILES['resume']['name'] ?? 'No file uploaded';
$uploadedResumePath = ''; // This will store the path to the saved file if upload is successful

// --- 2. Server-Side Validation ---

// Validate Full Name
if (strlen($fullName) < 3) {
    $errors[] = "Full name must be at least 3 characters long.";
}

// Validate Email Format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// Validate Position Selection
if (empty($position)) { // Check for empty string if the default value is ""
    $errors[] = "Please select a position to apply for.";
}

// Validate Terms and Conditions
if (!$terms) {
    $errors[] = "You must agree to the Terms and Conditions.";
}

// Validate Gender (assuming it's a mandatory choice)
// Check if 'work_mode' was submitted and is one of the expected values
$allowedWorkModes = ['remote', 'hybrid', 'onsite'];
if (!in_array($gender, $allowedWorkModes)) {
    $errors[] = "Please select a valid work mode.";
}


// --- 3. Handle Resume File Upload (Most Critical Section) ---
if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['resume']['tmp_name'];
    $fileName = $_FILES['resume']['name'];
    $fileSize = $_FILES['resume']['size'];
    $fileType = $_FILES['resume']['type']; // MIME type from browser, can be spoofed

    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Define allowed file extensions and MIME types
    $allowedFileExtensions = ['pdf']; // Only PDF for resume as per your client-side validation
    $allowedMimeTypes = ['application/pdf']; // For server-side check

    // Validate file extension
    if (!in_array($fileExtension, $allowedFileExtensions)) {
        $errors[] = "Invalid resume file extension. Only PDF files are allowed.";
    }

    // Validate MIME type more robustly (requires php-fileinfo extension)
    // Check if the fileinfo extension is loaded before using it
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMimeType = finfo_file($finfo, $fileTmpPath);
        finfo_close($finfo);

        if (!in_array($detectedMimeType, $allowedMimeTypes)) {
            $errors[] = "Invalid resume file type detected: " . $detectedMimeType . ". Only PDF is allowed.";
        }
    } else {
        // Fallback or warning if fileinfo is not available
        // For production, consider this a hard fail if you require strong MIME validation
        error_log("PHP 'fileinfo' extension is not enabled. Cannot perform robust MIME type validation for resume upload.");
        // Optionally, you could add this to errors if you absolutely require it:
        // $errors[] = "Server configuration error: Required file type validation not available.";
    }


    // Limit file size (e.g., 5MB)
    $maxFileSize = 5 * 1024 * 1024; // 5 MB in bytes
    if ($fileSize > $maxFileSize) {
        $errors[] = "Resume file size exceeds the maximum limit (5MB).";
    }

    // Generate a unique filename to prevent overwriting and enhance security
    $newFileName = uniqid('resume_', true) . '.' . $fileExtension; // More unique than md5(time())
    $destPath = $uploadDir . $newFileName;

    // Check if upload directory exists and is writable, create if not (with appropriate permissions)
    if (!is_dir($uploadDir)) {
        // 0755 permissions: owner rwx, group rx, others rx
        // true for recursive creation of directories
        if (!mkdir($uploadDir, 0755, true)) {
            $errors[] = "Server error: Failed to create upload directory.";
        }
    }
    
    // Attempt to move the uploaded file only if no prior errors for the file itself
    if (empty($errors)) { // Check errors array again before attempting move
        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            $errors[] = "Server error: Failed to move uploaded resume file.";
        } else {
            $uploadedResumePath = $destPath; // Store the actual path for confirmation
            // For display, use the new unique filename
            $resumeOriginalName = $newFileName; // Update to the new secure name
        }
    }

} else if (isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Catch other PHP upload errors (e.g., file too large for server config, partial upload)
    $phpFileUploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
    ];
    $errors[] = "Resume upload error: " . ($phpFileUploadErrors[$_FILES['resume']['error']] ?? 'Unknown upload error.');
}
// If UPLOAD_ERR_NO_FILE, it simply means no file was selected, which might be acceptable if resume is optional.
// No error is added for UPLOAD_ERR_NO_FILE here.

// --- 4. Handle Validation Failure ---
if (!empty($errors)) {
    echo "<h2 style='color:red;'>Submission Error(s):</h2>";
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>"; // Sanitize errors too
    }
    echo "</ul>";
    echo "<p><a href='index.html'>Go Back to Application</a></p>"; // Link back to original form
    exit; // Stop script execution
}

// --- 5. Process and Display Data (if validation passed) ---
// In a real application, you would now store this data in a database,
// send emails, or perform other business logic.

echo "<h2>✅ Application Received</h2>";
echo "<p><strong>Name:</strong> " . $fullName . "</p>";
echo "<p><strong>Email:</strong> " . $email . "</p>";
echo "<p><strong>Phone:</strong> " . ($phone ?: 'N/A') . "</p>"; // Display 'N/A' if empty
echo "<p><strong>Position:</strong> " . $position . "</p>";
echo "<p><strong>Preferred Work Mode:</strong> " . $gender . "</p>"; // Corrected for work_mode

// Display Bio (handle empty bio)
echo "<p><strong>Bio:</strong> " . ($bio ?: 'No bio provided.') . "</p>";

// Display Skills
if (!empty($skills)) {
    echo "<p><strong>Skills:</strong> " . htmlspecialchars(implode(', ', $skills)) . "</p>";
} else {
    echo "<p><strong>Skills:</strong> None selected.</p>";
}

// Display Resume status
if (!empty($uploadedResumePath)) {
    echo "<p><strong>Resume Uploaded:</strong> <a href='" . htmlspecialchars($uploadedResumePath) . "' target='_blank'>" . htmlspecialchars($resumeOriginalName) . "</a></p>";
    // IMPORTANT: In a production setting, you would typically store $newFileName in your database
    // and serve the file via a secure script, not direct link to uploads/
} else {
    echo "<p><strong>Resume:</strong> No resume file uploaded or issue during upload.</p>";
}

// Optionally, log the successful submission for auditing
error_log("Application submitted successfully for: " . $fullName . " (" . $email . ")");

// You might redirect the user to a thank you page here instead of displaying directly
// header("Location: thank_you.html");
// exit;

?>