#Coinbase (Bitcoin) Payment Gateway Module for WHMCS 
## A payment gateway module for WHMCS and Coinbase's bitcoin payment API.
#How To Install 



**Make sure https://www.coinbase.com/merchant_profiles is filled out, legitimate or illegitimate information, just make sure it is completed. Your merchant profile wont work with the buttons without this completed. The payment buttons will not show (common issue) because of this**

- Upload the contents to your web server
- Move the contents of this module into the WHMCS root directory
- Then login to the admin side of WHMCS, and go to Setup -> Payments -> Payment Gateways. 
- Select the module ("Coinbase (Bitcoin)") from the drop-down select box, and then click the Activate button to the right of it.
- On this same page, you can now configure the module, mainly you'll want to pay close attention to these items: 
 - Coinbase API Key - This is generated from within Coinbase, keep this private 
 - Coinbase CA Cert Path - This will be the path where the ca-coinbase.crt is. 
 - Coinbase Callback Secret - Generate a long and random string and input it in this field. Provide this to Coinbase on the backend under Callback URL at https://www.coinbase.com/merchant_settings . This is used to authenticate their callback in the field "Callback URL" example being http://example.com/modules/gateway/callback/coinbase.php?secret=1f2d3e4r5t1s19s Make sure to enter that in both WHMCS and Coinbase 
- Save your changes and do a test transaction
- For the coinbase API http://i.imgur.com/XUHg9Zm.png select Merchant, button, orders, and recurring payments on API v1
