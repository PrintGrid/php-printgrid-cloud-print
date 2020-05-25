<?php
/**
 * PHP implementation of PrintGrid Cloud Print
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'PrintGridCloudPrint.php';

const Refresh_Token = 'REPLACE-THIS-WITH-YOUR-REFRESH-ACCESS-TOKEN';

session_start();

// Create object
$pcp = new PrintGridCloudPrint();
$token = $pcp->getAccessTokenByRefreshToken(Refresh_Token);
$pcp->setAuthToken($token);
$printers = $pcp->getPrinters();

$printerid = "";
if(count($printers)==0) {
	
	echo "Could not get printers";
	exit;
}
else {
	
	$printerid = $printers[0]['id']; // Pass id of any printer to be used for print

	// Send document to the printer
	$resarray = $pcp->sendPrintToPrinter($printerid, 'Category', 'Job Title', "./sample.pdf", "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}
}
