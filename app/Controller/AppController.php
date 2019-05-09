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
			'loginAction' => array(
				'controller' => 'auth',
				'action' => 'login'

			),
			// 'userModel' => 'User',//sử dụng model User
			// 'fields' => array('username' => 'username', 'password' => 'password'),
            // 'loginRedirect' => array('controller' => 'post', 'action' => 'create'),
            // 'logoutRedirect' => array('controller' => 'post', 'action' => 'unAuthorize')
		),
		// 'authenticate' => array(
        //     'Form' => array(
        //         'passwordHasher' => 'Blowfish'
        //     )
		// ),
		// 'authError' => 'Không thể truy cập',//báo lỗi
		// 'authenticate' => array(
        //     'Form' => array(
        //         'fields' => array(
        //             'username' => 'mon_champ_username_personnalise', // 'username' par défaut
        //             'password' => 'mon_champ_password_personnalise'  // 'password' par défaut
        //         )
        //     )
		// ),
        // 'authorize' => array('Controller') // Added this line
	);

	// public function redirect($url, $status = null, $exit = true) {
	// 	if ($exit && $this->Components->enabled('Session') && $this->Session->started()) {
	// 		session_write_close();
	// 	}
	// 	// return $this->jsonResponseError('unAuthorize',401);

	// 	// return parent::redirect($url, $status, $exit);
	// }
    public function beforeFilter() {
		if(!isset($_SESSION))
		{
			session_start();
		}
		// if ($this->request->is('options')) {
		// 	$this->setCorsHeaders();
		// 	return $this->response;
		// }
		// $request = $this->request;
		// $response = $this->response;
		// if ($request->method() == 'OPTIONS')
		// {
		// 	$method = $request->header('Access-Control-Request-Method');
		// 	$headers = $request->header('Access-Control-Request-Headers');
		// 	$response->header('Access-Control-Allow-Headers', $headers);
		// 	$response->header('Access-Control-Allow-Methods', empty($method) ? 'GET, POST, PUT, DELETE' : $method);
		// 	$response->header('Access-Control-Allow-Credentials', 'true');
		// 	$response->header('Access-Control-Max-Age', '86400');
		// 	$response->send();
		// 	die;
		// }
		if (empty($_SESSION['user'])&& ($this->request->params['controller'] != 'Auth'|| $this->request->params['action'] != 'login'));
		// return $this->jsonResponseError('unAuthorize',401);


        // $this->Auth->allow('index', 'view');
	}
	// public function beforeRender(event $event) {
	// 	// $this->setCorsHeaders();
	// }

	// private function setCorsHeaders() {
	// 	$this->response->cors($this->request)
	// 		->allowOrigin(['*'])
	// 		->allowMethods(['*'])
	// 		->allowHeaders(['x-xsrf-token', 'Origin', 'Content-Type', 'X-Auth-Token'])
	// 		->allowCredentials(['true'])
	// 		->exposeHeaders(['Link'])
	// 		->maxAge(300)
	// 		->build();
	// }
	public function isAuthorized($user)
	{
		// Admin can access every action
		if (isset($user['role_id']) && $user['role_id'] === '1') {
			return true;
		}

		// Default deny
		// return $this->jsonResponseError('unAuthorize',401);
	}
	public function jsonAPIOutput($code, $message, $errors, $data, $status) {

	    $data = ['code' => $code, 'data' => $data,'errors' => $errors,'message' => $message];
		$this->response->type('json');
		// $this->response->header('Access-Control-Allow-Credentials', 'true');

		// $this->response->header('Access-Control-Allow-Origin', '*');
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
