<?php
// lib/Invoice.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

class Invoice {
  public static function pathFor($orderId) {
    $dir = __DIR__ . '/../invoices';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir . '/INV-' . $orderId . '.pdf';
  }

  public static function generate($orderId) {
    global $pdo;
    $o = $pdo->prepare("SELECT * FROM orders WHERE id=?");
    $o->execute([$orderId]);
    $order = $o->fetch();
    if (!$order) throw new Exception('Order not found');

    $items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
    $items->execute([$orderId]);
    $lines = $items->fetchAll();

    require_once __DIR__ . '/tcpdf/tcpdf.php';
    $pdf = new TCPDF('P','mm','A4', true, 'UTF-8', false);
    $pdf->SetCreator(APP_NAME);
    $pdf->SetAuthor(APP_NAME);
    $pdf->SetTitle('Invoice #'.$orderId);
    $pdf->SetMargins(12, 12, 12);
    $pdf->AddPage();

    $html = '<h2 style="margin:0;color:#111">'.APP_NAME.' — Invoice</h2>';
    $html .= '<p style="margin:2px 0 10px;color:#555">Invoice #: <b>INV-'.$orderId.'</b><br>Date: '.htmlspecialchars($order['created_at']).'</p>';

    $html .= '<table cellpadding="6" cellspacing="0" border="0" width="100%" style="font-size:11px;">
      <tr>
        <td width="55%" style="background-color:#f4f4f4"><b>Bill To</b><br>'
        . htmlspecialchars($order['name']) . '<br>'
        . nl2br(htmlspecialchars($order['address'])) . '<br>'
        . 'Phone: ' . htmlspecialchars($order['phone']) . '<br>'
        . 'Email: ' . htmlspecialchars($order['email']) .
      '</td>
        <td width="45%" style="background-color:#f4f4f4">
          <b>Order Info</b><br>
          Order ID: #'.$orderId.'<br>
          Payment: '.strtoupper($order['payment_method']).'<br>
          Status: '.htmlspecialchars($order['status']).'<br>
          Location: '.htmlspecialchars($order['location']).'
        </td>
      </tr>
    </table>';

    $html .= '<br><table cellpadding="6" cellspacing="0" border="1" width="100%" style="border-color:#ddd;font-size:11px;">
      <tr style="background-color:#efefef">
        <th align="left">Product</th>
        <th align="right" width="12%">Qty</th>
        <th align="right" width="18%">Price</th>
        <th align="right" width="18%">Total</th>
      </tr>';
    foreach ($lines as $ln) {
      $html .= '<tr>
        <td>'.htmlspecialchars($ln['name']).'</td>
        <td align="right">'.$ln['qty'].'</td>
        <td align="right">৳ '.number_format($ln['price'],2).'</td>
        <td align="right">৳ '.number_format($ln['line_total'],2).'</td>
      </tr>';
    }
    $html .= '</table>';

    $html .= '<br><table cellpadding="6" cellspacing="0" border="0" width="100%" style="font-size:11px;">
      <tr>
        <td width="60%"></td>
        <td width="40%">
          <table cellpadding="6" cellspacing="0" border="0" width="100%" style="font-size:11px;">
            <tr><td>Subtotal</td><td align="right">৳ '.number_format($order['subtotal'],2).'</td></tr>
            <tr><td>Delivery</td><td align="right">৳ '.number_format($order['delivery_fee'],2).'</td></tr>
            <tr><td>Discount'.($order['coupon_code']?' ('.htmlspecialchars($order['coupon_code']).')':'').'</td><td align="right">৳ -'.number_format($order['discount_amount'],2).'</td></tr>
            <tr><td><b>Grand Total</b></td><td align="right"><b>৳ '.number_format($order['grand_total'],2).'</b></td></tr>
          </table>
        </td>
      </tr>
    </table>';

    $html .= '<p style="font-size:10px;color:#666;margin-top:20px">Thank you for shopping at '.APP_NAME.'. This invoice is computer generated and valid without signature.</p>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $out = self::pathFor($orderId);
    $pdf->Output($out, 'F');
    return $out;
  }
}
