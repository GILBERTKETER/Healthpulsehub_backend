<?php
session_start();

// Check if LOGGED_IN_EMAIL key exists in the session
if (isset($_SESSION['LOGGED_IN_EMAIL'])) {
    echo "Session in auth: {$_SESSION['LOGGED_IN_EMAIL']}";
} else {
    echo "LOGGED_IN_EMAIL not found in session";
}
