<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *

 */
class UserController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
   public $uses = array();

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	// public $components = array(
	// 	'Session',
	// 	'Auth' => array(
	// 		'authenticate' => array('Basic')
	// 	)
	// );
	var $components = array('Auth');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('add','create','index','update','rudUser');
	}
	public function login() {
		if ($this->Auth->login()) {
			return $this->redirect($this->Auth->redirect());
		} else {
			$this->Session->setFlash('Not able to login');
		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}


	public function create() {

		if ($this->request->is('post')) {
			 //get data from request object
			 $data = $this->request->input('json_decode', true);
			 if(empty($data)){
				$data = $this->request->data;
			}

			if(!empty($data)){
				//call the model's save function
				$this->User->create();

				$record =$this->User->save($data);
				if($record){
					unset($record['User']['password']);
					$this->Auth->login($record);
					return $this->jsonResponseSuccess($record['User']);
				} else{
					 return $this->jsonResponseError('Something was wrong');
				}
			}

		}
		return $this->jsonResponseError('HTTP method not allowed');
	}

	public function index() {
		try{
		//grab all Users and pass it to the view:

		$list = $this->User->find('all');
		$this->jsonResponseSuccess($list['User']);

		//catch exception
		}catch(Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$this->jsonResponseError('gg');

		}
	}
	public function rudUser(){
		$id = $this->request->params['id'];
		if ($this->request->is('get')) {

			return $this->detail($id);
		}
		if ($this->request->is('put')) {

			return $this->update($id);
		}
	   if ($this->request->is('delete')) {
			return $this->delete($id);
		}

	   return $this->jsonResponseError('HTTP method not allowed');

	}
	public function detail($id) {

		if ($this->request->is('get')) {
			//get data from request object

			$this->User->id = $id;
			if (!$this->User->exists()) {
				return $this->jsonResponseError('Invalid User');
			}
		  if(!empty($id)){
			   $record =$this->User->findById($id);
			   if($record){
				   //return success
				   return $this->jsonResponseSuccess($record['User']);
			   } else{
					return $this->jsonResponseError('Something was wrong');
			   }
		   }

	  }
	//    if($this->request->is('put'))
	   return $this->jsonResponseError('HTTP method not allowed');
    }
	public function update($id) {

		if ($this->request->is('put')) {
			//get data from request object
			$data = $this->request->input('json_decode', true);

			$this->User->id = $id;
			if (!$this->User->exists()) {
				return $this->jsonResponseError('Invalid User');
			}
			$data = $this->request->input('json_decode', true);
			if(empty($data)){
			   $data = $this->request->data;
		   }

		   if(!empty($id)){
			   //call the model's save function
			   $data['id'] = $id;
			   $record =$this->User->save($data);
			   if($record){
				   //return success
				   return $this->jsonResponseSuccess($record['User']);
			   } else{
					return $this->jsonResponseError('Something was wrong');
			   }
		   }

	  }
	//    if($this->request->is('put'))
	   return $this->jsonResponseError('HTTP method not allowed');
    }

    public function delete($id = null) {
		if ($this->request->is('delete')) {
			//get data from request object
			$data = $this->request->input('json_decode', true);
			$this->User->id = $id;
			if (!$this->User->exists()) {
				return $this->jsonResponseError('Invalid User');
			}
			if(empty($data)){
			   $data = $this->request->data;
		   }
		   if(!empty($id)){
			   //call the model's save function
			   $record =$this->User->delete($id,true);
			   if($record){
				   //return success
				   return $this->jsonResponseSuccess($record);
			   } else{
					return $this->jsonResponseError('Something was wrong');
			   }
		   }

	   }
	   return $this->jsonResponseError('HTTP method not allowed');

    }
}
