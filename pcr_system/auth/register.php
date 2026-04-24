<?php
/**
 * Student Registration Page - MVC Entry Point
 * Personalized Curriculum Recommendation System
 */

require_once '../controllers/AuthController.php';

$controller = new AuthController();
$controller->register();
