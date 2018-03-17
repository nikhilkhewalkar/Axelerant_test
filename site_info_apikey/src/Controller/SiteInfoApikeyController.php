<?php

namespace Drupal\site_info_apikey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

Class SiteInfoApikeyController extends ControllerBase{

    public function accessChecker(){
        $siteapikey = \Drupal::config('siteapi.settings')->get('siteapikey') ;
        
        $nodeid = explode("/",\Drupal::request()->getRequestUri())[3];      //get node id from url
        $node = \Drupal\node\Entity\Node::load($nodeid);


        if ($node != null) {
            $node = \Drupal\node\Entity\Node::load($nodeid)->get('type')->getString();
            if ($siteapikey == explode("/", \Drupal::request()->getRequestUri())[2]) {
                if (\Drupal\node\Entity\Node::load($nodeid)->get('type')->getString() == "page") {
                    $node = \Drupal\node\Entity\Node::load($nodeid);
                    $node_author = $node->getOwner()->id();
                    $node_body = $node->get('body')->getValue();
                    $node_type = $node->getType();
                    $node_title = $node->getTitle();
                    $node_created = $node->getCreatedTime();
                    $node_status = $node->isPublished();
                    // Create JSON response
                    $json_response = [
                        'nodeID' => $nodeid,
                        'title' => $node_title,
                        'body' => $node_body,
                        'nodeType' => $node_type,
                        'created' => $node_created,
                        'uid' => $node_author,
                        'published' => $node_status,
                    ];

                    $data = ['http_code' => '200', 'values' => $json_response];
                    return new JsonResponse($data);
                } else {
                    $data = ['http_code' => '403', 'values' => ['Err_msg' => 'Access denied',
                        'Solution' => t('Accept content of only page content type.')]];
                }
            }
        }
        $data = ['http_code' => '403', 'values' => ['Err_msg' => 'Access denied',
            'Solution' => t('Please Enter Valid Node Id')]];

        $response = new Response(
            json_encode($data),
            Response::HTTP_FORBIDDEN,
            array('content-type' => 'application/json')
        );
        //throw new AccessDeniedHttpException();
        return $response;
    }

}