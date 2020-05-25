PrintGrid Cloud Print
======================

PHP class to print documents using PrintGrid Cloud Print using OAuth2

https://printgrid.io
PrintGrid is a cloud print solution, very similar to Google Cloud Print. This library will help you integrate with PrintGrid.
Language: PHP
Version: 5.3 and above

In order to use this you will need to complete the setup process as mentioned on the portal here:
https://app.printgrid.io/app/setup

Generate the "Refresh token" from the setup instructions. You will need to replace that in the example.php

## Instructions

1. Create an account on PrintGrid.io

2. Follow the setup instructions:
	a. Install drivers on your laptop using Access Key. 
	b. Once you have completed the setup instructions, you will be having a refresh token(Do note that refresh token is different from access token).
	
3. Now, replace your Refresh token from step 2.b in example.php file.

4. On successful call, you should be able to see the document on the PrintGrid dashboard.

## Additional resources

Do checkout our Developer API documentation here
https://developer.printgrid.io