<?php
// header.php – session logic and navigation

// 1) Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Expose the logged-in user’s info
$meId   = $_SESSION['user_id']   ?? null;
$meName = $_SESSION['username']  ?? '';
$meType = $_SESSION['UserType']  ?? '';  // 'Admin', 'Agent', or 'Client'

// 3) Convenience functions (only declare if not already)
if (! function_exists('isClient')) {
    function isClient()   { global $meType; return $meType === 'Client'; }
}
if (! function_exists('isLoggedIn')) {
    function isLoggedIn() { global $meId;   return (bool)$meId; }
}
if (! function_exists('isAdmin')) {
    function isAdmin()    { global $meType; return $meType === 'Admin'; }
}
if (! function_exists('isAgent')) {
    function isAgent()    { global $meType; return in_array($meType, ['Agent','Admin']); }
}
?>

