<?php
// Function to sanitize user inputs
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($value);
        }
        return $input;
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Function to set flash message
function set_flash_message($message, $type = 'success') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type // success, danger, warning, info
    ];
}

// Function to display flash message
function display_flash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' alert-dismissible fade show mx-3 mt-3" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['flash']);
    }
}

// Function to format currency
function format_currency($amount) {
    return '$' . number_format((float)$amount, 2);
}
?>
