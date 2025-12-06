<?php
session_start();
require '../includes/db_connect.php';
if(!isset($_SESSION['user_id'])){
    die("Access denied.");
}

$stmt = $pdo->prepare("SELECT * FROM members WHERE user_id=:uid");
$stmt->execute([':uid'=>$_SESSION['user_id']]);
$member = $stmt->fetch();
if(!$member){
    die("You must be a library member to access this page.");
}
$member_id = $member['member_id'];

// --- fine update ---
$fine_per_day = 2; // $2 per overdue day
$today = new DateTime();

// Fetch unreturned books and update fines
$stmtOverdue = $pdo->prepare("
    SELECT i.issue_id, i.due_date, f.fine_id, f.paid_status
    FROM issued_books i
    LEFT JOIN fines f ON i.issue_id = f.issue_id
    WHERE i.member_id = :mid AND i.return_date IS NULL
");
$stmtOverdue->execute([':mid' => $member_id]);

while($row = $stmtOverdue->fetch(PDO::FETCH_ASSOC)) {
    $due = new DateTime($row['due_date']);
    if($today > $due) {
        $days_overdue = $today->diff($due)->days;
        $amount = $days_overdue * $fine_per_day;

        if($row['fine_id']) {
            // Update existing unpaid fine
            if($row['paid_status'] === 'Unpaid') {
                $update = $pdo->prepare("UPDATE fines SET fine_amount = :amt, fine_date = NOW() WHERE fine_id = :fid");
                $update->execute([':amt' => $amount, ':fid' => $row['fine_id']]);
            }
        } else {
            // Insert new fine
            $insert = $pdo->prepare("
                INSERT INTO fines (issue_id, member_id, fine_amount, paid_status, paid_method, fine_date, remarks)
                VALUES (:iid, :mid, :amt, 'Unpaid', NULL, NOW(), 'Overdue')
            ");
            $insert->execute([
                ':iid' => $row['issue_id'],
                ':mid' => $member_id,
                ':amt' => $amount
            ]);
        }
    }
}

$stmt = $pdo->prepare("
    SELECT f.fine_id, f.fine_amount, f.paid_status, f.paid_method, f.fine_date, f.remarks, b.title
    FROM fines f
    JOIN issued_books i ON f.issue_id=i.issue_id
    JOIN books b ON i.book_id=b.book_id
    WHERE f.member_id=:mid
    ORDER BY f.fine_date DESC
");
$stmt->execute([':mid'=>$member_id]);
$fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Fines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Fines</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="cart.php" class="cart-icon" title="View Cart">
                <i class="fas fa-shopping-cart"></i>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="badge bg-danger rounded-circle" style="position: absolute; top: -10px; right: -10px; font-size: 0.7rem;">
                        <?= count($_SESSION['cart']); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <?php if(empty($fines)): ?>
        <div class="alert alert-info">You have not issued any books yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped shadow-sm">
            <tr>
                <th>Book</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Fine Date</th>
                <th>Action</th>
                <th>Remarks</th>
            </tr>
            <?php foreach($fines as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['title']); ?></td>
                <td>$<?= $f['fine_amount']; ?></td>
                <td id="status-<?= $f['fine_id']; ?>">
                    <?php 
                        if($f['paid_status'] === 'Paid'){
                            echo $f['paid_method'] === 'offline' ? 
                                '<span class="badge bg-success">Paid offline</span>' : 
                                '<span class="badge bg-success">Paid online</span>';
                        } else {
                            echo '<span class="badge bg-danger">Unpaid</span>';
                        }
                    ?>
                </td>
                <td><?= $f['fine_date']; ?></td>
                <td>
                    <?php if ($f['paid_status'] === 'Unpaid' && $f['fine_amount'] > 0): ?>
                        <button class="btn btn-sm btn-success pay-btn" 
                                data-fine="<?= $f['fine_id']; ?>" 
                                data-amount="<?= $f['fine_amount']; ?>" 
                                data-title="<?= htmlspecialchars($f['title']); ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#payModal">
                            Pay Fine
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                    <?php endif; ?>
                </td>
                <td>
                <?= htmlspecialchars(($f['remarks'] && $f['remarks'] !== '') ? $f['remarks'] : 'No remarks'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<!-- Pay Fine Modal -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="payFineForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payModalLabel">Pay Fine</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="fine_id" id="modal_fine_id">
          <div class="mb-3">
              <label for="card_number" class="form-label">Card Number</label>
              <input type="text" class="form-control" id="card_number" name="card_number" maxlength="16" inputmode="numeric" pattern="\d{16}" required>
          </div>
          <div class="mb-3">
              <label for="expiry" class="form-label">Expiry Date (MM/YY)</label>
              <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
          </div>
          <div class="mb-3">
              <label for="cvv" class="form-label">CVV</label>
              <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" inputmode="numeric" pattern="\d{3}" required>
          </div>
          <p>Amount: $<span id="modal_amount"></span></p>
          <p>Book: <span id="modal_title"></span></p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Pay Now</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    // Card number: digits only, max 16
    $('#card_number').on('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,16);
    });

    // CVV: digits only, max 3
    $('#cvv').on('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,3);
    });

    $('.pay-btn').click(function(){
        $('#modal_fine_id').val($(this).data('fine'));
        $('#modal_amount').text($(this).data('amount'));
        $('#modal_title').text($(this).data('title'));
    });

    $('#payFineForm').submit(function(e){
        e.preventDefault();
        let fine_id = $('#modal_fine_id').val();
        let card_number = $('#card_number').val();
        let cvv = $('#cvv').val();
        let expiry = $('#expiry').val();

        let expRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if(!expRegex.test(expiry)){
            alert("Invalid expiry format. Use MM/YY.");
            return;
        }

        let parts = expiry.split("/");
        let expMonth = parseInt(parts[0],10);
        let expYear = 2000 + parseInt(parts[1],10); 

        let today = new Date();
        let currentMonth = today.getMonth() + 1;
        let currentYear = today.getFullYear();

        if(expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)){
            alert("Expiry date must be in the future.");
            return;
        }

        $.post('pay_fine.php', {
            fine_id: fine_id,
            card_number: card_number,
            cvv: cvv,
            expiry: expiry
        }, function(response){
            if(response.status === 'success'){
                let modal = bootstrap.Modal.getInstance(document.getElementById('payModal'));
                modal.hide();

                // Update badge
                $('#status-' + fine_id).html('<span class="badge bg-success">Paid online</span>');

                $('.pay-btn[data-fine="'+fine_id+'"]')
                    .prop('disabled', true)
                    .removeClass('btn-success')
                    .addClass('btn-secondary')
                    .text('Paid');

                alert(response.msg);
            } else {
                alert(response.msg);
            }
        }, 'json').fail(function(){
            alert("Error processing payment.");
        });
    });
});
</script>
</body>
</html>
