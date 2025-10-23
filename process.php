<?php
include 'config.php';
$type = $_POST['type'] ?? '';

if ($type == 'investor') {
    $name = $_POST['investor_name'];
    $start = $_POST['start_date'];
    $modal = $_POST['principal'];
    $conn->query("INSERT INTO investments (investor_name, start_date, principal)
                  VALUES ('$name', '$start', '$modal')");
    header("Location: investor_add.php?status=success");
    exit;
}

if ($type == 'transaction') {
    $id = $_POST['investment_id'];
    if (!empty($_POST['trans_date'])) {
        foreach ($_POST['trans_date'] as $i => $tdate) {
            $desc = $_POST['description'][$i];
            $type_trans = $_POST['trans_type'][$i];
            $amt  = $_POST['amount'][$i];
            if ($tdate && $amt) {
                $conn->query("INSERT INTO transactions (investment_id, trans_date, description, trans_type, amount)
                              VALUES ('$id', '$tdate', '$desc', '$type_trans', '$amt')");
            }
        }
    }
    header("Location: transaction_add.php?status=success");
    exit;
}

if ($type == 'delete_investor') {
    $id = $_POST['id'];
    $conn->query("DELETE FROM investments WHERE id = '$id'");
    header("Location: investor_add.php?status=success");
    exit;
}
?>