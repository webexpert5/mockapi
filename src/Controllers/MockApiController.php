<?php

namespace Package;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

interface MockApiInterface{
	
    public function get_method($request, $response, $args);
    public function getorder($request, $response, $args);
    public function cancelorder($request, $response, $args);
}

class MockApiController implements MockApiInterface{
	
    public $request;
    public $response;
    private $dbService;
    private $errorhandler;
   
    public function __construct($dbService, $errorhandler) {
       $this->dbService = $dbService;
       $this->errorhandler = $errorhandler;
    }
   
   
    public function get_method($request, $response, $args) {
        $query =  $this->dbService->prepare("SELECT * FROM users");
        $query->execute();
        $arr = $query->fetchAll();
        return $response->withJson($arr);
    }
   
    public function getorder($request, $response, $args) {
		if(is_numeric($args['orderid'])){
			 $orderid = $args['orderid'];
			 $query =  $this->dbService->prepare("SELECT * FROM orders where order_id=".$orderid."");
			 $query->execute();
			 $arr = $query->fetch();
			 if(!empty($arr)){
				 $user_query =  $this->dbService->prepare("SELECT * FROM users where userid=".$arr[user_id]."");
				 $user_query->execute();
				 $userarr = $user_query->fetch();
				 $res = array_merge($arr, $userarr);
				 return $response->withJson($res);
			 }else{
				 $this->errorhandler = "no data found for orderid ".$orderid."" ;
				 return $response->withJson($this->errorhandler);
			 }
		 }else{
			 return $response->withJson('Please enter numeric id');
		 }
    }
      
    public function cancelorder($request, $response, $args) {
		 $bodyArr = $request->getParsedBody();
		 $order_id  = $bodyArr['orderid'];
		 try {
		  if(!empty($order_id) && is_numeric($order_id)){
			 $query =  $this->dbService->prepare("UPDATE orders SET order_status= 2 WHERE order_id=".$order_id."");
			 $query->execute();
			 $res = array('msg'=>'order has been updated');
			 return $response->withJson($res);
		  }else{
			   return $response->withJson('Please enter numeric id');
		  }
		}catch(\PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
   }
   
    public function get_cancelorder($request, $response, $args) {
		 try {
			if(is_numeric($args['orderid'])){
				 $order_id = $args['orderid'];
				 $query =  $this->dbService->prepare("UPDATE orders SET order_status= 1 WHERE order_id=".$order_id."");
				 $query->execute();
				 $query_1 = $this->dbService->prepare("SELECT * FROM orders where order_id=".$order_id."");
				 $query_1->execute();
			     $arr = $query_1->fetch();
			     if($arr['order_status'] == '1'){
					 $res = array('msg'=>'order has been updated');
					 return $response->withJson($res);
				 }else{
					 $res = array('msg'=>'no order found to update');
					 return $response->withJson($res);
				 }
			}else{
				 return $response->withJson('Please enter numeric id');
			}
		}catch(\PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
}

