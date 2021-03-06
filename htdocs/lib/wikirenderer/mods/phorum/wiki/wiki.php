<?php

// change the path if it is needed and the name of the rules
// at line 12
require_once(dirname(__FILE__).'/wikirenderer/WikiRenderer.lib.php');

if(!defined("PHORUM")) return;

function phorum_wikirenderer ($data) {
   static $wikirenderer = null;
   if($wikirenderer == null)
      $wikirenderer = new WikiRenderer('classicwr_to_xhtml');

   foreach($data as $key => $message){
      if(isset($message["subject"])){
         $data[$key]["subject"]=htmlspecialchars($message["subject"]);
      }

      if(isset($message["body"])){
         $data[$key]["body"]=$wikirenderer->render($message["body"]);
      }
      if(isset($message['signature_author']) && trim($message['signature_author']) != ''){
         $data[$key]['signature_author']=$wikirenderer->render($message['signature_author']);
      }
   }

   return $data;
}
