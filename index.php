<?php
	session_start();
	require("functions.php");	//file which has required functions
	require("config.php");
?>	 	
	
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <!--<link rel="stylesheet" href="https://yegor256.github.io/tacit/tacit.min.css"/>-->
	<script language="JavaScript">
        function successClicked()
        {
            document.paymentpage.submit();
        }
        function failClicked()
        {
            document.paymentpage.status.value = "N";
            document.paymentpage.submit();
        }
        function pendingClicked()
        {
            document.paymentpage.status.value = "P";
            document.paymentpage.submit();
        }
</script>
</head>
<body>
<center>
<?php
		
		
		//This filter removes data that is potentially harmful for your application. It is used to strip tags and remove or encode unwanted characters.
		$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
		
		//Below are the  parameters which will be passed from foundation as http GET request
		$paymentTypeId = $_GET["paymenttypeid"];  //payment type id
		$transId = $_GET["transid"];			   //This refers to a unique transaction ID which we generate for each transaction
		$userId = $_GET["userid"];               //userid of the user who is trying to make the payment
		$userType = $_GET["usertype"];  		   //This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
		$transactionType = $_GET["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)

		$invoiceIds = $_GET["invoiceids"];		   //comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
		$debitNoteIds = $_GET["debitnoteids"];	   //comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"

		$description = $_GET["description"];
		
		$sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
        $accountingCurrencyAmount = $_GET["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency

		$redirectUrl = $_GET["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him

						
		$checksum = $_GET["checksum"];	 //checksum for validation

         //echo "Secure Connection Verification..............";
echo (int)$accountingCurrencyAmount;
		 
		if(verifyChecksum($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum))
		{
			$_SESSION['userType']=$userType;
			//$_SESSION['price']=$price;
			$_SESSION['userId']=$userId;
			$_SESSION['redirecturl']=$redirectUrl;
			$_SESSION['transid']=$transId;
			$_SESSION['accountingCurencyAmount']=$accountingCurrencyAmount;
			$_SESSION['sellingCurrencyAmount']=$sellingCurrencyAmount;

?>


    <form id="checkout-selection" method="session" >
        <input type="radio" name="checkout" value="automatic">Check to continue<br>
        <!--<input type="radio" name="checkout" value="orders">Manual Checkout Demo<br>-->
        <input type="submit" value="Submit">
    </form>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) 
        {
            var form = $('#checkout-selection');
            var radio = $('input[name="checkout"]');
            var choice = '';

            radio.change(function(e) 
            {
                choice = this.value;
                if (choice === 'orders') 
                {
                    form.attr('action', 'pay.php?checkout=manual');
                } 
                else 
                {
                    form.attr('action', 'pay.php?checkout=automatic');
                }
            });
        });
    </script>
<?php

		}
		else
		{

			echo "Checksum mismatch !";			

		}
	?>
	</center>
</body>
</html>