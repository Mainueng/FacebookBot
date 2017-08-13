<?php
$access_token = "EAABjN3Es9UkBAFpIT1Yt0h7sdfw5tNK1ZALBMPqIw9cm3bNiEnOL514T3NXcgrMm3T00HXZB7Ebgrenwn9ZAcjuwtPGMwIjGiUnYaWW7UPTZCZCAkD31IAU6ECWwRS25xqhdQtXV32AkZA6EOAHW8jYRdDBXYPg1dZAiHONjzprBGz1uXj8eGHM";

//$proxy = 'http://fixie:vkd7AP4Z3dnMLIA@velodrome.usefixie.com:80';
//$proxnueng = '5303phanat@gmail.com:tffunelee01';

$verify_token = "mianueng";

$hub_verify_token = null;
 
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
 
if ($hub_verify_token === $verify_token) {
    echo $challenge;
}
$input = json_decode(file_get_contents('php://input'), true);
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];

$api_key="KQwfH7eNH_WLCmVVENPPyl2kWYflYa5u";
$urlMlab = 'https://api.mlab.com/api/1/databases/mianueng/collections/LineBot?apiKey='.$api_key.'';
$json = file_get_contents('https://api.mlab.com/api/1/databases/mianueng/collections/LineBot?apiKey='.$api_key.'&q={"question":"'.$message.'"}');
$dataMlab = json_decode($json);
$isData=sizeof($dataMlab);

if (strpos($message, ':') !== false) {	
    $words = explode(":", $message);
    $newData = json_encode(
      array(
        'question' => $words[0],
        'answer'=> $words[1]
      )
    );
    $opts = array(
      'http' => array(
          'method' => "POST",
          'header' => "Content-type: application/json",
          'content' => $newData
       )
    );
    $context = stream_context_create($opts);
    $returnValue = file_get_contents($urlMlab,false,$context);
    $message_to_reply = 'ขอบคุณที่สอนไม้หนึ่ง';
 }

elseif (strpos($message, '=') !== false) {
        $symbol = explode(" ", $message);
          if($symbol[1] == "+"){
            $cal = intval($symbol[0]) + intval($symbol[2]);
          }
          else if($symbol[1] == "-"){
            $cal = intval($symbol[0]) - intval($symbol[2]); 
          }
          else if($symbol[1] == "*"){
            $cal = intval($symbol[0]) * intval($symbol[2]); 
          }
          else if($symbol[1] == "/"){
            $cal = intval($symbol[0]) / intval($symbol[2]); 
          }

          $cal = (string)$cal;
        
            if ($cal <= 999999999999999999 ) {
              $message_to_reply = $message." ".$cal;
            }

            else {
              $message_to_reply = 'ERROR !!!';
            }
}

elseif (strpos($message, 'วัน') !== false){
  $message_to_reply = date("l")." ".date("d/m/Y");
}

elseif (strpos($message, 'โมง') !== false){
  date_default_timezone_set("asia/bangkok");
  $message_to_reply = date("H:i:s");
}
elseif (strpos($message, 'เวลา') !== false){
  date_default_timezone_set("asia/bangkok");
  $message_to_reply = date("H:i:s");
}


else{
  if($isData >0){
   foreach($dataMlab as $rec){
     $message_to_reply = $rec->answer;
   }
  }
  else{
    $message_to_reply = 'ไม้หนึ่งไม่เข้าใจ คุณสามารถสอนไม้หนึ่งได้เพียงพิมพ์ คำถาม:คำตอบ';
  	//$messages_to_reply = $isData;
  }	
}			

$urlFacebook = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;

$jsonData = '{
    "recipient":{
        "id":"'.$sender.'"
    },
    "message":{
        "text":"'.$message_to_reply.'"
    }
}';

$jsonDataEncoded = $jsonData;
$ch = curl_init($urlFacebook);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
if(!empty($input['entry'][0]['messaging'][0]['message'])){
    $result = curl_exec($ch);
}

?>
