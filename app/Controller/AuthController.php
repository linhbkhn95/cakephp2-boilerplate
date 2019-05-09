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
class AuthController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
   public $uses = array('User');

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
	var $components = array('Session');

	public function beforeFilter() {
		parent::beforeFilter();

		// $this->Auth->allow('add','create','index','logout','login','me');


		// $this->header('Access-Control-Allow-Credentials', true);
	}
	public function checkPass($user,$password){
		$storedHash = $user['User']['password'];
		$newHash = Security::hash($password, 'blowfish', $storedHash);
		if($storedHash == $newHash){
				return true;
		}else{
				return false;
		}
	}
	public function me(){
		try{
			$this->log("authController ->  me -> start!", 'debug');
			$user = $_SESSION['user'];
			$this->log( $user,'debug');
            if($user)
				return $this->jsonResponseSuccess($user);
			else
			    return $this->jsonResponseError('unAuthorize',401);

		}catch(Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			return $this->jsonResponseError('gg');

		}

	}
	public function login() {
		$this->log("Something did not work!", 'debug');

		if ($this->request->is('post')) {
			$data = $this->request->input('json_decode', true);
			if(empty($data)){
			   $data = $this->request->data;
			}
			$user = $this->User->findByUsername($data['username']);

			// return $this->jsonResponseSuccess($user);
			if(!$user){
				return $this->jsonResponseError('User has not in system');
			}
			if(!$this->checkPass($user,$data['password'])){
				return $this->jsonResponseError('invalid password');
			}
			unset($user['User']['password']);
			if ($this->Auth->login($user)) {
				$this->Session->write('user', $user['User']);
                $_SESSION['user'] = $user['User'];
				return $this->jsonResponseSuccess($user['User']);
			} else {
				return $this->jsonResponseError('invalid username or password');
			}
		}
		return $this->jsonResponseError('HTTP method not allowed');
	}

	public function logout() {
		// return $this->jsonResponseError('invalid password');

		if ($this->request->is('post')) {
			if($this->Auth->logout()){
				$_SESSION['user'] =null;
				return $this->jsonResponseSuccess('Logout was successfull');

			}
		}
		return $this->jsonResponseError('HTTP method not allowed');

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
					//return success
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

}
