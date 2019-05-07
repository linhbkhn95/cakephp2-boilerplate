<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'post', 'action' => 'create'),
            'logoutRedirect' => array('controller' => 'post', 'action' => 'unAuthorize')
		),
		// 'authenticate' => array(
        //     'Form' => array(
        //         'passwordHasher' => 'Blowfish'
        //     )
        // ),
        // 'authorize' => array('Controller') // Added this line
    );
	public function redirect($url, $status = null, $exit = true) {
		if ($exit && $this->Components->enabled('Session') && $this->Session->started()) {
			session_write_close();
		}
		return parent::redirect($url, $status, $exit);
	}
    // public function beforeFilter() {
    //     $this->Auth->allow('index', 'view');
    // }
	public function isAuthorized($user)
	{
		// Admin can access every action
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}

		// Default deny
		return false;
	}
	public function jsonAPIOutput($code, $message, $errors, $data, $status) {

		Configure::write('debug', 0);
		 $data = ['code' => $code, 'data' => $data,'errors' => $errors,'message' => $message];

		$this->response->type('json');
		$this->response->header('Access-Control-Allow-Origin', '*');
		$this->response->statusCode($status);
		$this->response->body(json_encode($data));
		$this->response->send();
		$this->render(false,false);

	}
	  /*
  code : mã code trả về  client.
  message : message trả về  client thường dùng cho trường hợp có lỗi.
  data : dữ liệu trả về  cho client thường dùng cho trường hợp trả về  khi có kết quả không gặp lỗi.
  */

	public function jsonResponseSuccess($data,$status=200) {

		 $this->jsonAPIOutput(0,"Success",null,$data,$status);

	}
	public function jsonResponseError($errors,$status=500) {

		 $this->jsonAPIOutput(-1,"Errors",$errors,null,$status);
	}
}
