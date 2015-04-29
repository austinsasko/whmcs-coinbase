<?php
# HostDark.com
# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "coinbase"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation

$adminuser = $GATEWAY['whmcs_admin_username'];
$secret_key = $GATEWAY['callback_secret'];
$provided_secret = $_GET['secret'];
if ($provided_secret != $secret_key) {
        header('HTTP/1.0 403 Forbidden');
        die("Invalid");
}


$json = json_decode(file_get_contents('php://input'));
$order = $json->order;
$id = $order->id;
$completed_at = $order->completed_at;
$status = $order->status;
$total_btc_cents = $order->total_btc->cents;
$total_btc_currency = $order->total_btc->currency_iso;
$total_native_cents = $order->total_native->cents;
$total_native_currency = $order->total_native->currency_iso;
$invoice_id = $order->custom;
$trans_id = $order->transaction->hash;
$confirmation = $order->transaction->confirmation;
$fee = '0.00';
$amount = number_format($total_native_cents/100, 2, '.', '');

$invoice_id = checkCbInvoiceID($invoice_id,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

checkCbTransID($trans_id); # Checks transaction number isn't already in the database and ends processing if it does

if ($status=="completed") {

  # Successful + Adds note to the invoice
  $command = "updateinvoice";
  $values["invoiceid"] = $invoice_id; #changeme
  $values["notes"] = "BTC:{$total_btc_cents}; > USD:{$total_native_cents};"; #changeme
  $results = localAPI($command,$values,$adminuser);
//Adds the transaction + Invoice Payment
  $command = "addinvoicepayment";
  $values["invoiceid"] = $invoice_id;
  $values["transid"] = $trans_id;
  $values["amount"] = $amount;
  $values["fees"] = $fee;
  $values["gateway"] = $gatewaymodule;
  $results = localAPI($command,$values,$adminuser);
  logTransaction($GATEWAY["name"],file_get_contents('php://input'),"Successful"); # Save to Gateway Log: name, data array, status
} elseif ($status=="canceled") {

  # Cancelled
  $command = "updateinvoice";
  $values["invoiceid"] = $invoice_id;
  $values["status"] = "Unpaid";
  $results = localAPI($command,$values,$adminuser);
  logTransaction($GATEWAY["name"],file_get_contents('php://input'),"Cancelled"); # Save to Gateway Log: name, data array, status

} else {

  # Unsuccessful
  logTransaction($GATEWAY["name"],file_get_contents('php://input'),"Payment Failed"); # Save to Gateway Log: name, data array, status

}

?>
