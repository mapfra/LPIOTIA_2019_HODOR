<?php

/* echo '<form action="/mqtt-test.php" method="post">';
echo '<input type="submit" name="btn1" value="PUBLISH">';
echo '<input type="submit" name="btn2" value="SUBSCRIBE">';
echo '</form>'; */

$statusmsg = "";
$rcv_message = "";

CONST TOPIC = 'guizard/hodor/status_door';
CONST BROKER = '90.116.66.46';
CONST PORT = 8086;
CONST KEEPALIVE = 20;
CONST TIMEOUT = 10;


/* if ($_POST["btn1"] == "PUBLISH") {
    publish_message('test mqtt', TOPIC, BROKER, PORT, KEEPALIVE);				    
}

if ($_POST["btn2"] == "SUBSCRIBE")	{
	$statusmsg = "";	
	$rcv_message = "";
	
	read_topic(TOPIC, BROKER, PORT, KEEPALIVE, TIMEOUT);	
	
	if(!empty($rcv_message) ) {
		echo $statusmsg."RCVD|" . $rcv_message ;	
	} else {
		echo $statusmsg."TIMEDOUT"; 	
	}		
} */



function publish_message($msg, $topic, $server, $port, $keepalive) {
	
	$client = new Mosquitto\Client();
	$client->onConnect('connect');
	$client->onDisconnect('disconnect');
	$client->onPublish('publish');
	$client->connect($server, $port, $keepalive);
	
	try {
		$client->loop();
		$mid = $client->publish($topic, $msg);
		$client->loop();

	} catch (Mosquitto\Exception $e){
		echo 'Exception';          
		return false;
	}
    $client->disconnect();
	unset($client);
	return true;
}

function read_topic($topic, $server, $port, $keepalive, $timeout) {
	$client = new Mosquitto\Client();
	$client->onConnect('connect');
	$client->onDisconnect('disconnect');
	$client->onSubscribe('subscribe');
	$client->onMessage('message');
	$client->connect($server, $port, $keepalive);
	$client->subscribe($topic, 1);
	
	$date1 = time();
	$GLOBALS['rcv_message'] = '';
	while (true) {
			$client->loop();
			sleep(1);
			$date2 = time();
			if (($date2 - $date1) > $timeout) break;
			if(!empty($GLOBALS['rcv_message'])) break;
	}
	 
	$client->disconnect();
	unset($client);						
} 

/*****************************************************************
 * Call back functions for MQTT library
 * ***************************************************************/					
function connect($r) {
		if($r == 0) echo "{$r}-CONX-OK|";
		if($r == 1) echo "{$r}-Connection refused (unacceptable protocol version)|";
		if($r == 2) echo "{$r}-Connection refused (identifier rejected)|";
		if($r == 3) echo "{$r}-Connection refused (broker unavailable )|";        
}
 
function publish() {
        global $client;
        echo "Mesage published:";
}
 
function disconnect() {
        echo "Disconnected|";
}


function subscribe() {
	    //**Store the status to a global variable - debug purposes 
		$GLOBALS['statusmsg'] = $GLOBALS['statusmsg'] . "SUB-OK|";
}

function message($message) {
	    //**Store the status to a global variable - debug purposes
		$GLOBALS['statusmsg']  = "RX-OK|";
		
		//**Store the received message to a global variable
		$GLOBALS['rcv_message'] =  $message->payload;
}

?>
