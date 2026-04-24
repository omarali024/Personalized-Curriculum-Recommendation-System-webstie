<?php
/**
 * Student Dashboard - MVC Entry Point
 * Personalized Curriculum Recommendation System
 */

require_once '../controllers/StudentController.php';

$controller = new StudentController();
$controller->dashboard();
