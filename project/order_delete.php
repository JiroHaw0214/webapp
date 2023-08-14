<?php
// include database connection
include 'config/database.php';
try {
    // get record ID
    // isset() is a PHP function used to verify if a value is there or not
    $id = isset($_GET['id']) ? $_GET['id'] :  die('ERROR: Record ID not found.');

    // delete query
    $summary_query = "DELETE FROM order_summary WHERE id = ?";
    $stmt = $con->prepare($summary_query);
    $stmt->bindParam(1, $id);

    $detail_query = "DELETE FROM order_details WHERE order_id = ?";
    $detail_stmt = $con->prepare($detail_query);
    $detail_stmt->bindParam(1, $id);

    if ($stmt->execute() && $detail_stmt->execute()) {
        // redirect to read records page and
        // tell the user record was deleted
        header('Location: order_read.php?action=deleted');
    } else {
        die('Unable to delete record.');
    }
}
// show error
catch (PDOException $exception) {
    die('ERROR: ' . $exception->getMessage());
}