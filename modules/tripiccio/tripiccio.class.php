<?php
/**
 * @copyright Copyright (C) 2013 land in sicht AG All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */
 
include_once ("lib/ezutils/classes/ezmail.php");

class tripiccio {
    
    var $postUrl;
    
    function tripiccio() {
/*        $data_INI = eZINI::instance( "module.ini.append.php", "extension/tripiccio/settings" ); */
       $data_INI = eZINI::instance( "module.ini.append.php" );
       $this->postURL = $data_INI->variable( "newsletter" , 'HTTP_POST_URL' );
       $this->postPath = $data_INI->variable("newsletter" , 'HTTP_POST_PATH');
       $this->variables = $data_INI->group("variables");
       $this->postParameters=array();
       $this->postParameters["ent"] = $data_INI->variable( "newsletter" , 'ent' );
       $this->postParameters["usr"] = $data_INI->variable( "newsletter" , 'usr' );
       $this->postParameters["pwd"] = $data_INI->variable( "newsletter" , 'pwd' );
       $this->postParameters["ret"] = $data_INI->variable( "newsletter" , 'ret' );
       $this->postParameters["upd_ok"] = $data_INI->variable( "newsletter" , 'upd_ok' );
       
       $addtitionalParameters = $data_INI->variable( "newsletter" , 'AdditionalParameter' );
       foreach ($addtitionalParameters as $key=>$value) {
	       $this->postParameters[$key] = $value;
       }
    }
    
    
    function register($collection) {
       #$this->postParameters["p_status"] = "A";
       // collection attribute durchsuchen
       if (is_array($collection)) {
          foreach ($collection as $col) {         
            $variableFlip =array_flip($this->variables);
            $attr = $col->contentClassAttribute();
            $identifier = $attr->attribute("identifier");
            if (in_array($identifier,$this->variables)) {
               //TODO: auf alle datentpe erweitern
              // möglichkeit zum werttauschen
               switch ($attr->attribute("data_type_string")) {
                   case 'ezfloat' :
                   case 'ezprice' :
                     $this->postParameters[$variableFlip[$identifier]] = $col->attribute("data_float");
                     break;
                   case 'ezboolean' :
                   case 'ezdate' :
                   case 'ezdatetime' :
                   case 'ezinteger' :
                   case 'eztime' :
                     $this->postParameters[$variableFlip[$identifier]] = $col->attribute("data_int");
                     break; 
                   //case 'ezoption' : $result = "";
                   case 'ezoption' : if( $col->attribute("data_int") == 0) { $result = "Herr"; } else { $result = "Frau"; }; 
			       	   $content = $col->content();
					   
					   $contentObjectAttribute = $col->contentObjectAttribute();
			
					   $xml = simplexml_load_string($contentObjectAttribute->DataText);
					    
					   foreach ( $xml->options as $item )
					   {
					   		 
					   		 foreach ( $item->option as $opcja )
					   		 { 
					   		 		$ok = false;
					   		 		
						   		 	foreach ( (array)$opcja as $key => $value )
						   		 	{
						   		 			if( $ok == true )
						   		 			{
						   		 				$result = $value;
						   		 				$ok = false;
						   		 			}
						   		 		
						   		 			if( is_array($value) )
						   		 			{
						   		 				if( $value['id'] == $col->DataInt )
						   		 					$ok = true;
						   		 			}
						   		 	}		   		 	
					   		 }
					   }                   
                   	 $this->postParameters[$variableFlip[$identifier]] = $result;
                   	 break;                     
                   case 'ezemail' :
                   case 'ezisbn' :
                   case 'ezstring' :
                   case 'eztext' :
                   case 'ezurl':
                     $this->postParameters[$variableFlip[$identifier]] = $col->attribute("data_text");   
                     break;
                   default:
                     $this->postParameters[$variableFlip[$identifier]] = $col->attribute("data_text");
                     break;
               }
            }
          }
       }
       
       $status = 1; // fehler
       if ($this->postParameters['newsletter'] == 1) {
          if (ezMail::validate($this->postParameters["addr"])) {
             //post data to service
             ezDebug::writeDebug( "Validate email OK. posting data!" );
             $status = $this->postData();          
             ezDebug::writeDebug( $status );
          }          
       } else {
           ezDebug::writeDebug( "newsletter nicht angehackt!" );
       }
              
       //return status

       return array("result" => $status);
    }
    
    function unsubscribe() {
      $status = 1; // fehler
      
      
      return $status;
    }
    
    function postData() {
     
      $parameters=array();
      $urlString="";
      $method = 'POST';
      $host = $this->postURL;
      $buf = "";
      
      foreach ($this->postParameters as $key=>$value) {
         $parameters[] = urlencode($key)."=".urlencode($value);
      }
      
/*       var_dump($parameters); */
      
      $urlString = implode("&",$parameters);
      ezDebug::writeDebug( "Connection to ".$this->postURL);
      ezDebug::writeDebug( "URLString ".$urlString);
      $fp = fsockopen($this->postURL, 80);
      if ($fp) {
         ezDebug::writeDebug( "Connection established" );
      } else {
         #ezDebug::wirteDebug( $errno );
         #ezDebug::wirteDebug( $errdesc );      
         ezDebug::writeDebug( "connection failed" );
      }
   
      fputs($fp, "$method $this->postPath HTTP/1.1\r\n");
      fputs($fp, "Host: $host\r\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
      fputs($fp, "Content-length: " . strlen($urlString) . "\r\n");
      fputs($fp, "User-Agent: LIS\r\n");
      fputs($fp, "Connection: close\r\n\r\n");
      fputs($fp, $urlString);
 
      while (!feof($fp)) {
           $buf .= fgets($fp,128);
      }
      
      fclose($fp);
      return $buf;
    }
}
?>