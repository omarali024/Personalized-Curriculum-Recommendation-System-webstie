<?php
/**
 * Student Profile Page - MVC Entry Point
 * Personalized Curriculum Recommendation System
 */

require_once '../controllers/StudentController.php';

$controller = new StudentController();
$controller->profile();
