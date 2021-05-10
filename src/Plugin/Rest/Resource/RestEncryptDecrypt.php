<?php

namespace Drupal\rest_encryptdecrypt\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
 use Drupal\encrypt\Entity\EncryptionProfile;

class RestEncryptDecrypt extends ResourceBase {

  /**
   * Responds to POST requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  
  public function post($data) {

   //$data = json_encode($data);
    $response_data = ['message' => 'success','encrypted_string'=>'','decrypted_string'=>''];
	$instance_id = $data['encrypt_profile'];
   if($data["action"] == "encrypt"){  //===if action parameter has encrypt values execute this section
	   
	//$instance_id = 'encrypt_profile';
	try{
		
		$string = $data['input_text']; //====input string parameter to be encrypted
		$encryption_profile = EncryptionProfile::load($instance_id);
		$encrypted = \Drupal::service('encryption')->encrypt($string, $encryption_profile);
		$response_data['encrypted_string'] = $encrypted;  //===output assings to response array
	}catch(Exception $e){
		\Drupal::logger('type')->error($e->getMessage());
		$response_data['message'] = 'Something went wrong';
	}
	   
   }else if($data["action"] == "decrypt"){  //===if action parameter has decrypt values execute this section
	  try{ 
	    $string = $data['input_text']; //input string parameter to be decrypted
		  $encryption_profile = EncryptionProfile::load($instance_id);
		  $decrypted = \Drupal::service('encryption')->decrypt($string, $encryption_profile);
		  $response_data['decrypted_string'] =  $decrypted;
	   }catch(Exception $e){
		\Drupal::logger('type')->error($e->getMessage());
		$response_data['message'] = 'Something went wrong';
	  }
   }
	    
    $response = new ResourceResponse($response_data);
    // In order to generate fresh result every time (without clearing 
    // the cache), you need to invalidate the cache.
    $response->addCacheableDependency($response_data);
    return $response;
  }

}