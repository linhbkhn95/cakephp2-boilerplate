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
		$this->Auth->allow('add','create','index','update','login');
	}
	public function login() {
		if ($this->request->is('post')) {
			$data = $this->request->input('json_decode', true);
			if(empty($data)){
			   $data = $this->request->data;
		    }
			if ($this->Auth->login( $data)) {
				return $this->jsonResponseSuccess('ok');
			} else {
				return $this->jsonResponseError('invalid username or password');
			}
		}
		return $this->jsonResponseError('HTTP method not allowed');
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
