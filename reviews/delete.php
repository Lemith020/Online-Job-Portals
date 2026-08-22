<?php
// -----------------------------------------------------------------
// Reviews - Delete
// Purpose : Delete a review by id
// This file does NOT output HTML. It only processes an action
// (delete / update / logout) and then redirects back.
// -----------------------------------------------------------------
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// TODO: Add real DB logic for: Delete a review by id

// $id = $_GET['id'] ?? null;
// $stmt = $conn->prepare("DELETE FROM table_name WHERE id = ?");
// $stmt->execute([$id]);

header("Location: index.php");
exit();
