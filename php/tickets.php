<?php
require 'init.php';

$sql_scopes = "SELECT * FROM ticket_scopes ORDER BY validity_seconds ASC";
$stmt_scopes = $pdo->query($sql_scopes);
$scopes = $stmt_scopes->fetchAll(PDO::FETCH_ASSOC);

$sql_prices = "SELECT ticket_prices.price, ticket_prices.scope_id, passenger_types.name as passenger_name, passenger_types.passenger_type_id 
  FROM ticket_prices 
  JOIN passenger_types ON ticket_prices.passenger_type_id = passenger_types.passenger_type_id 
  ORDER BY passenger_types.passenger_type_id ASC";
$stmt_prices = $pdo->query($sql_prices);
$all_prices = $stmt_prices->fetchAll(PDO::FETCH_ASSOC);

$prices_by_scope = [];
foreach ($all_prices as $p) {
  $prices_by_scope[$p['scope_id']][] = $p;
}

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <?php if ($is_logged_in): ?>
      <hr class="divider">
      <!--TODO: USER TICKETS SECTION -->
    <?php endif; ?>

    <div class="ticket-grid">
      <?php 
        $total = count($scopes);
        $i = 0;

        foreach ($scopes as $scope):
          $i++; 
      ?>
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
                        <input type="hidden" name="price_id" value="<?= 'N/A' ?>">
                        <button type="submit" class="btn btn-add" aria-label="Purchase">+</button>
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

        <?php if ($i != $total): ?>
          <hr class="divider">
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?php require 'footer.php'; ?>
