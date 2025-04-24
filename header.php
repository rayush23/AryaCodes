<?php
// header.php
session_start();

// Make these variables available to every page
$meId   = $_SESSION['user_id']   ?? null;
$meName = $_SESSION['username']  ?? '';
$meType = $_SESSION['UserType']  ?? '';  // 'Admin', 'Agent', or 'Client'

// Helper functions for access checks
function isLoggedIn() { global $meId; return (bool)$meId; }
function isAdmin()    { global $meType; return $meType === 'Admin'; }
function isAgent()    { global $meType; return in_array($meType, ['Agent','Admin']); }
