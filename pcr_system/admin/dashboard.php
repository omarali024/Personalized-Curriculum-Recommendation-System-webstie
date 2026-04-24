<?php
/**
 * Admin Dashboard - MVC Entry Point
 * Personalized Curriculum Recommendation System
 */

require_once '../controllers/AdminController.php';

$controller = new AdminController();
$controller->dashboard();
