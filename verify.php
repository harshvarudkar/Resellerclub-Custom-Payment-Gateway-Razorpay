<html>
<body>
<center>
<?php

require('config.php');

session_start();
session_save_path("./"); //path on your server where you are storing session


	//file which has required functions
	require("functions.php");

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);
	$payment = $api->payment->fetch($_POST['razorpay_payment_id']);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
		
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
		$status="N";
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
	
{
	$status="Y";
	$_SESSION['accountingCurencyAmount']=($payment->amount)/100;
	$_SESSION['sellingCurrencyAmount']=($payment->amount)/100;
	echo "<h4>Payment Status: " . $payment->status . "<br/>";
	echo "<h4>Transaction ID: " . $_SESSION['transid'] . "<br/>";
	echo "<h4>Amount: " . $_SESSION['accountingCurencyAmount'] . "<br/>";
	echo "<h4>Amount: " . $_SESSION['sellingCurrencyAmount'] . "<br/>";
	echo "<h4>User ID: " . $_SESSION['userId'] . "<br/>";
	echo "<h4>User Type: " . $_SESSION['userType'] . "<br/>";
	
	$redirectUrl = $_SESSION['redirecturl'];  // redirectUrl received from foundation
		$transId = $_SESSION['transid'];		 //Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
		$sellingCurrencyAmount = $_SESSION['sellingCurrencyAmount'];
		$accountingCurrencyAmount = $_SESSION['accountingCurencyAmount'];
	//echo "<h4>Payment Status: " . $status . "<br/>";
	//echo $_SESSION['userType'];
	//echo		$_SESSION['price'];
	//echo		$_SESSION['userId'];
	//echo		$_SESSION['redirecturl'];
	//echo		$_SESSION['transid'];
	
	//echo $payment->amount;
	//echo		$_SESSION['accountingCurencyAmount'];
	//echo		$_SESSION['sellingCurrencyAmount'];
	//$_POST['razorpay_payment_id']
    /*$html = "<p>Your payment was successful</p>
             <p>Payment ID: {$payment->status}</p><br/>
			 <p>Payment ID: {$payment->method}</p><br/>
			 <p>Payment ID: {$payment->amount}</p><br/>
			 <p>Payment ID: {$payment->status}</p><br/>
			 <p>Payment ID: {$payment->status}</p><br/>";*/
}
else
{
	$status="N";
   $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}

srand((double)microtime()*1000000);
		$rkey = rand();


		$checksum =generateChecksum($transId,$sellingCurrencyAmount,$accountingCurrencyAmount,$status, $rkey,$key);

			echo "You'll now redirect to merchant page! <br>";
//echo $html;
?>
<form name="f1" action="<?php echo $redirectUrl;?>">
		<input type="submit" value="Click here to Continue"><BR>
			<input type="hidden" name="transid" value="<?php echo $transId;?>">
		    <input type="hidden" name="status" value="<?php echo $status;?>">
			<input type="hidden" name="rkey" value="<?php echo $rkey;?>">
		    <input type="hidden" name="checksum" value="<?php echo $checksum;?>">
		    <input type="hidden" name="sellingamount" value="<?php echo $sellingCurrencyAmount;?>">
			<input type="hidden" name="accountingamount" value="<?php echo $accountingCurrencyAmount;?>">

			
		</form>

</center>
</body>
</html>