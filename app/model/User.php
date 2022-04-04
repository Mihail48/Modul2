<?php

namespace App\model;

if (!session_id()) {
    @session_start();
}

use League\Plates\Engine;

use Delight\Auth\Auth;

use PDO;

use App\model\QueryBuilder;

use \Tamtamchik\SimpleFlash\Flash;

// создаем модель для работы с пользовательскими функциями
class user
{
		private $auth;
		private $templates;
		private $qb;
		private $flash;

		public function __construct(QueryBuilder $qb, Flash $flash, Engine $engine, Auth $auth)
		{
			$this->auth = $auth;
			$this->templates = $engine;
			$this->qb = $qb;
			$this->flash = $flash;
		}

		public function delete()
		{
			try {
				    $user = $this->qb->getOne($_GET['id'],'users_information');
					$email = $user['email'];

				    $this->auth->admin()->deleteUserByEmail($email);
				    $this->qb->delete($_GET['id'],'users_information');
				}
				catch (\Delight\Auth\InvalidEmailException $e)
				{
				    die('Unknown email address');
				}
		}

		public function edit($id)
		{
			$this->qb->update(['username' => $_POST['username'],
								'work' => $_POST['work'],
								'telephone' => $_POST['telephone'],
								'location' => $_POST['location']], $id, 'users_information');

			// $this->qb->update()

		}

		public function security($id)
		{
			$this->qb->update(['email' => $_POST['email']], $id, 'users_information');
			$this->auth->changeEmail($_POST['email'], function ($selector, $token)
			{
            	$_GET['selector'] = $selector;
        	    $_GET['token'] = $token;
    	    });
			$this->auth->confirmEmail($_GET['selector'], $_GET['token']);
			$this->auth->changePassword($_POST['password'], $_POST['password']);


		}

		public function status($id)
		{

			$this->qb->update(['status' => $_POST['set_status']], $id, 'users_information');

		}

		public function images($id)
		{
			$avatar = $_FILES['avatar'];

			if(is_uploaded_file($avatar['tmp_name']))
			{

				$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';

				$filename=$avatar['name'];


				$extension = pathinfo($filename, PATHINFO_EXTENSION); //получил расширение png

				$filename=uniqid().'.'.$extension;

				move_uploaded_file($avatar['tmp_name'], $uploadDir.$filename);


				$this->qb->update(['img' => $filename], $id, 'users_information');

				return TRUE;

			}

		}

		public function delete_images($id)
		{
			$avatar = $_FILES['avatar'];
			if(is_uploaded_file($avatar['tmp_name']))
			{
				$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
				$user = $this->qb->getOne($id,'users_information');
				$filename = $user['img'];
				$deleteFile = $uploadDir.$filename;

				unlink($deleteFile);
			}

		}

		public function create_user()
		{

				try {
					    $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username']);
					    $this->qb->insert(['id' => $userId,
								   'email' => $_POST['email'],
								'username' =>$_POST['username'],
									'work' =>$_POST['work'],
							   'telephone' =>$_POST['telephone'],
								  'status' =>$_POST['set_status'],
							    'location' =>$_POST['location'],
									  'vk' =>$_POST['vk'],
							    'telegram' =>$_POST['telegram'],
							   'instagram' =>$_POST['instagram']],'users_information');
					    return $userId;


					}
					catch (\Delight\Auth\InvalidEmailException $e) {
					    die('Invalid email address');
					}
					catch (\Delight\Auth\InvalidPasswordException $e) {
					    die('Invalid password');
					}
					catch (\Delight\Auth\UserAlreadyExistsException $e) {
					    die('User already exists');
					}
					catch (\Delight\Auth\TooManyRequestsException $e) {
					    die('Too many requests');
					}


		}



}















?>