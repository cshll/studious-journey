<?php
require 'init.php';

$sql_scopes = "SELECT * FROM ticket_scopes ORDER BY validity_seconds ASC";
$stmt_scopes = $pdo->query($sql_scopes);
$scopes = $stmt_scopes->fetchAll(PDO::FETCH_ASSOC);

$sql_prices = "SELECT ticket_prices.price, ticket_prices.price_id, ticket_prices.scope_id, passenger_types.name as passenger_name, passenger_types.passenger_type_id 
  FROM ticket_prices 
  JOIN passenger_types ON ticket_prices.passenger_type_id = passenger_types.passenger_type_id 
  ORDER BY passenger_types.passenger_type_id ASC";
$stmt_prices = $pdo->query($sql_prices);
$all_prices = $stmt_prices->fetchAll(PDO::FETCH_ASSOC);

$prices_by_scope = [];
foreach ($all_prices as $p) {
  $prices_by_scope[$p['scope_id']][] = $p;
}

//
if ($is_logged_in) {
  $user_id = $_SESSION['userid'];
  $msg = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_ticket_id'])) {
    $ticket_id = $_POST['activate_ticket_id'];

    $sql = "SELECT ticket_scopes.validity_seconds 
      FROM user_tickets 
      JOIN ticket_prices ON user_tickets.price_id = ticket_prices.price_id 
      JOIN ticket_scopes ON ticket_prices.scope_id = ticket_scopes.scope_id 
      WHERE user_tickets.ticket_id = ? AND user_tickets.user_id = ? AND user_tickets.status = 'unused'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_id, $user_id]);
    $duration = $stmt->fetchColumn();

    if ($duration !== false) {
      if ($duration == 0) {
        $duration = 5400;
      }

      $now = new DateTime();
      $expires = clone $now;
      $expires->modify("+$duration seconds");

      $sql_update = "UPDATE user_tickets SET status = 'active', activated_at = NOW(), expires_at = ? WHERE ticket_id = ?";
      $stmt_update = $pdo->prepare($sql_update);
      $stmt_update->execute([$expires->format('Y-m-d H:i:s'), $ticket_id]);

      header("Location: tickets.php#my-tickets");
      exit;
    }
  }

  $sql = "SELECT user_tickets.*, ticket_scopes.name as scope_name, ticket_scopes.description, passenger_types.name as passenger_name, support_requests.support_ref, ticket_prices.price 
    FROM user_tickets 
    JOIN ticket_prices ON user_tickets.price_id = ticket_prices.price_id 
    JOIN ticket_scopes ON ticket_prices.scope_id = ticket_scopes.scope_id 
    JOIN passenger_types ON ticket_prices.passenger_type_id = passenger_types.passenger_type_id 
    LEFT JOIN support_requests ON support_requests.related_ticket_id = user_tickets.ticket_id 
    WHERE user_tickets.user_id = ? 
    ORDER BY user_tickets.purchase_date DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$user_id]);
  $all_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $active = [];
  $unused = [];
  $expired = [];

  foreach ($all_tickets as $ticket) {
    if ($ticket['status'] == 'active' && new DateTime($ticket['expires_at']) < new DateTime()) {
      $ticket['status'] = 'expired';
      $expired[] = $ticket;
    } elseif ($ticket['status'] == 'active') {
      $active[] = $ticket;
    } elseif ($ticket['status'] == 'unused') {
      $unused[] = $ticket;
    } else {
      $expired[] = $ticket;
    }
  }
}

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <?php if ($is_logged_in && count($all_tickets) > 0): ?>
      <div id="my-tickets" class="wrapper my-tickets">
        <h1>Your Tickets</h1>

        <?php if (!empty($active)): ?>
          <h3 class="section-title">Live Tickets <span class="pulse-dot"></span></h3>
          <div class="ticket-group">
            <?php foreach ($active as $ticket):
              $expiry_iso = (new DateTime($ticket['expires_at']))->format('c');
            ?>
              <div class="ticket-card active-ticket">
                <div class="ticket-header">
                  <span class="ticket-type"><?= htmlspecialchars($ticket['scope_name']) ?></span>
                  <span class="support-ref">Reference: <?= $ticket['support_ref'] ?? 'N/A' ?></span>
                </div>
                
                <div class="ticket-body">
                  <div class="qr-section">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $ticket['ticket_hash'] ?>" alt="Scan to Board">
                    <p class="scan-inst">Scan on Bus</p>
                  </div>
                  <div class="info-section">
                    <div class="passenger"><?= htmlspecialchars($ticket['passenger_name']) ?></div>
                    <div class="timer-box">
                      <small>Expires in</small>
                      <div class="countdown" data-expires="<?= $expiry_iso ?>">Calculating...</div>
                    </div>
                    <div class="date-info">Activated: <?= date('H:i d M', strtotime($ticket['activated_at'])) ?></div>
                  </div>
                </div>

                <div class="ticket-footer active-footer">
                  Valid for travel on all Trafford Bus routes.
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if(!empty($unused)): ?>
          <h3 class="section-title">Ready to Use</h3>
          <div class="ticket-group">
            <?php foreach ($unused as $ticket): ?>
              <div class="ticket-card unused-ticket">
                <div class="ticket-header-simple">
                  <div>
                    <strong><?= htmlspecialchars($ticket['scope_name']) ?></strong>
                    <span class="pill"><?= htmlspecialchars($ticket['passenger_name']) ?></span>
                  </div>
                  <div class="support-ref-dark">Reference: <?= $ticket['support_ref'] ?? 'N/A' ?></div>
                </div>

                <div class="ticket-action-area">
                  <p>Purchase on <?= date('d M Y', strtotime($ticket['purchase_date'])) ?></p>
                  <form method="POST" onsubmit="return confirm('Activate this ticket now? The timer will start immediately.');">
                    <input type="hidden" name="activate_ticket_id" value="<?= $ticket['ticket_id'] ?>">
                    <button type="submit" class="btn btn-primary-small">Activate Ticket</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($expired)): ?>
          <div style="margin-top: 40px; text-align: center;">
            <button id="toggle-history" class="btn btn-outline-small" onclick="document.getElementById('expired-list').style.display = 'block'; this.style.display = 'none';">
              Show Expired Tickets (<?= count($expired) ?>)
            </button>
          </div>

          <div id="expired-list" style="display: none; margin-top: 20px;">
            <h4 style="color: #888;">Expired History</h4>
            <?php foreach ($expired as $ticket): ?>
              <div class="ticket-card expired-ticket">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                  <div>
                    <strong><?= htmlspecialchars($ticket['scope_name']) ?></strong>
                    <span style="color: #999;">(<?= htmlspecialchars($ticket['passenger_name']) ?>)</span>
                  </div>
                  <div class="support-ref-dark" style="font-size: 0.8rem;">Reference: <?= $ticket['support_ref'] ?? 'N/A' ?></div>
                </div>
                <div style="margin-top: 5px; font-size: 0.85rem; color: #999">
                  Expired: <?= $ticket['expires_at'] ? date('d M Y H:i', strtotime($ticket['expires_at'])) : 'N/A' ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <script src="ticket-timer.js"></script>

      <hr class="divider">
    <?php endif; ?>

    <div id="purchase" class="wrapper tickets">
      <h1>Purchase a Ticket</h1>
      <div class="ticket-grid">
        <?php foreach ($scopes as $scope): ?>
          <div class="ticket-card" style="border-top-color: <?= htmlspecialchars($scope['ui_color_hex']) ?>;">
            <div class="ticket-info">
              <h2 style="color: <?= htmlspecialchars($scope['ui_color_hex']) ?>;">
                <?= htmlspecialchars($scope['name']) ?>
              </h2>
              <p class="description"><?= htmlspecialchars($scope['description']) ?></p>
            </div>

            <div class="ticket-pricing">
              <table>
                <?php if (isset($prices_by_scope[$scope['scope_id']])): ?>
                  <?php foreach ($prices_by_scope[$scope['scope_id']] as $price_opt): ?>
                    <tr>
                      <td class="passenger-type"><?= htmlspecialchars($price_opt['passenger_name']) ?></td>
                      <td class="price-val">£<?= number_format($price_opt['price'], 2) ?></td>
                      <td class="action-col">
                        <?php if ($is_logged_in): ?>
                          <button type="submit" class="btn btn-add" aria-label="Purchase" onclick="window.location.href = 'checkout.php?price_id=<?= $price_opt['price_id'] ?>'">+</button>
                        <?php else: ?>
                          <button type="button" class="btn btn-add" onclick="window.location.href='login.php'" aria-label="Purchase">+</button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="3">Unavailable</td></tr>
                <?php endif; ?>
              </table>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>

<?php require 'footer.php'; ?>
