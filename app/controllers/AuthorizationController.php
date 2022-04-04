<?php
namespace App\controllers;

if (!session_id()) {
    @session_start();
}

use League\Plates\Engine;

use Aura\SqlQuery\QueryFactory;

use Delight\Auth\Auth;

use PDO;

use App\model\QueryBuilder;

use Mail\SimpleMail;

use Tamtamchik\SimpleFlash\Flash;

// создаем контроллер для авторизации пользователей
class AuthorizationController
{

	private $auth;
	private $templates;
	private $mail;
	private $qb;
	private $flash;

	public function __construct(QueryBuilder $qb, Flash $flash, Engine $engine, Auth $auth)
	{
		$this->auth = $auth;
		$this->templates = $engine;
		$this->mail = new SimpleMail;
		$this->qb = $qb;
		$this->flash = $flash;
	}


	public function register()
	{

		try {
    $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
    	SimpleMail::make()
		->setTo($_POST['email'], $_POST['username'])
		->setMessage("<a href='https://blog/verification?selector=$selector&token=$token'>нажмите на ссылку для подтверждения почты</a>")
		->setHtml()
		->send();


    });

	    echo 'We have signed up a new user with the ID ' . $userId;
	    $this->qb->insert(['id'=>$userId,'email'=>$_POST['email']],'users_information');
	    header('location: /login');
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    $this->flash->error('<strong>Уведомление!</strong> Не верный формат почтового адреса');
		    header('location: /register');
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    $this->flash->error('<strong>Уведомление!</strong> Не верный формат пароля');
		    header('location: /register');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    $this->flash->error('<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
		    header('location: /register');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		   $this->flash->error('<strong>Уведомление!</strong> Слишком много запросов');
		    header('location: /register');
		}
	}
	public function verification()
		{
			try {
	    $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
	    $this->flash->success('Регистрация успешна');
		    header("Location: /login");
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
		    die('Invalid token');
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
		    die('Token expired');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    die('Email address already exists');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    die('Too many requests');
		}
		}


	public function login()
	{
		try {
    $this->auth->login($_POST['email'], $_POST['password']);


    $this->flash->success('Профиль успешно обновлен');
    header("Location: /users");
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    die('Wrong email address');
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    die('Wrong password');
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
		    die('Email not verified');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    die('Too many requests');
		}
	}


	public function logout()
	{
		$this->auth->logOut();
		header("Location: /login");
	}



}



















?>