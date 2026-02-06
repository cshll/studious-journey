<?php
require 'init.php';

if (!$is_logged_in) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['userid'];
$error = '';

$price_id = $_GET['price_id'] ?? null;
if (!$price_id) {
  header('Location: tickets.php');
  exit;
}

$sql = "SELECT ticket_prices.price_id, ticket_prices.price, ticket_scopes.name as scope_name, passenger_types.name as passenger_name, ticket_scopes.description 
  FROM ticket_prices 
  JOIN ticket_scopes ON ticket_prices.scope_id = ticket_scopes.scope_id 
  JOIN passenger_types ON ticket_prices.passenger_type_id = passenger_types.passenger_type_id 
  WHERE ticket_prices.price_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$price_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
  header('Location: tickets.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['payment_method']) {
  $allowed_methods = ['stripe', 'apple_pay', 'google_pay'];
  $payment_method = $_POST['payment_method'];

  if (!in_array($payment_method, $allowed_methods)) {
    $error = "Invalid payment method!";
  } else {
    try {
      $pdo->beginTransaction();

      $ticket_hash = hash('sha256', uniqid($user_id . $price_id, true));
      $sql_ticket = "INSERT INTO user_tickets (user_id, price_id, ticket_hash, status) VALUES (?, ?, ?, 'unused')";
      $stmt_ticket = $pdo->prepare($sql_ticket);
      $stmt_ticket->execute([$user_id, $price_id, $ticket_hash]);

      $new_ticket_id = $pdo->lastInsertId();

      $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
      $random_str = substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3);
      $support_ref = 'TRF-' . $random_str;

      $sql_support = "INSERT INTO support_requests (support_ref, user_id, related_ticket_id) VALUES (?, ?, ?)";
      $stmt_support = $pdo->prepare($sql_support);
      $stmt_support->execute([$support_ref, $user_id, $new_ticket_id]);

      $pdo->commit();

      header("Location: tickets.php");
      exit;
    } catch (Exception $error_msg) {
      $pdo->rollBack();
      $error = "Error with payment system! " . $error_msg->getMessage();
    }
  }
}

require 'header.php';
?>

<main class="site-content" style="padding: 20px; margin: 60px auto; max-width: 900px; overflow: hidden;">
  <div class="container auth-card checkout-page">
    <h1><!--Un-->Secure Checkout</h1>
    <div class="ticket-details">
      <h3><?= htmlspecialchars($ticket['scope_name']) ?></h3>
      <p><?= htmlspecialchars($ticket['passenger_name']) ?> &bull; <?= htmlspecialchars($ticket['description']) ?></p>
      <span>
        £<?= number_format($ticket['price'], 2) ?>
      </span>
    </div>

    <hr class="divider">

    <?php if ($error): ?>
      <div class="alert alert-error" style="color: red;"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
      <p>Choose a payment method:</p>

      <button type="submit" name="payment_method" value="apple_pay" class="pay-btn btn-apple">
        <span class="pay-icon"></span> Apple Pay
      </button>

      <button type="submit" name="payment_method" value="google_pay" class="pay-btn btn-google">
        <span class="pay-icon">G</span> Google Pay
      </button>

      <button type="submit" name="payment_method" value="stripe" class="pay-btn btn-stripe">
        <span class="pay-icon">💳</span> Pay With Stripe
      </button>
    </form>
  </div>
</main>

<?php require 'footer.php'; ?>
