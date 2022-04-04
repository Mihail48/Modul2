<?php
// указываем namespace чтобы получать доступ к этой странице
namespace app\controllers;

if (!session_id()) {
    @session_start();
}

use League\Plates\Engine;

use Aura\SqlQuery\QueryFactory;

use PDO;

use App\model\QueryBuilder;

use Tamtamchik\SimpleFlash\Flash;

use Delight\Auth\Auth;

use JasonGrimes\Paginator;

// создаем контроллер видов для отображения страниц
class ViewController
{
	private $templates;
	private $auth;
	private $flash;
	private $qb;
	private $pdo;
	private $paginator;

	public function __construct(QueryBuilder $qb, Flash $flash, Engine $engine, Auth $auth, Paginator $paginator)
	{
		$this->pdo = $pdo;
		$this->auth = $auth;
		$this->templates = $engine;
		$this->qb = $qb;
		$this->flash = $flash;
		$this->paginator = $paginator;
	}


	public function show_register_page()
	{

		// Render a template
		echo $this->templates->render('page_register', ['flash' => $this->flash]);


	}

	public function show_login_page()
	{
		// Render a template
		echo $this->templates->render('page_login', ['flash' =>$this->flash]);
	}

	public function show_users_page()
	{

		// Render a template
		echo $this->templates->render('page_users', ['auth' => $this->auth,'flash' =>$this->flash,'users' => $this->qb->getAll('users_information'),'paging' => $this->qb->paging('users_information',6,$_GET['page'] ?? 1), 'paginator' => $this->paginator]);
	}

	public function show_profile_page()
	{
		$id = $_GET['id'];
		echo $this->templates->render('page_profile', ['auth' => $this->auth,'flash' =>$this->flash,'user' => $this->qb->getOne($id,'users_information')]);
	}

	public function show_edit_page()
	{
		$id = $_GET['id'];
		echo $this->templates->render('page_edit', ['auth' => $this->auth,'flash' =>$this->flash,'user' => $this->qb->getOne($id,'users_information')]);
	}

	public function show_security_page()
	{
		$id = $_GET['id'];
		echo $this->templates->render('page_security', ['auth' => $this->auth,'flash' =>$this->flash,'user' => $this->qb->getOne($id,'users')]);

	}
	public function show_status_page()
	{
		$id = $_GET['id'];
		echo $this->templates->render('page_status', ['auth' => $this->auth,'flash' =>$this->flash,'user' => $this->qb->getOne($id,'users_information')]);

	}

	public function show_media_page()
	{
		$id = $_GET['id'];
		echo $this->templates->render('page_media', ['auth' => $this->auth,'flash' =>$this->flash,'user' => $this->qb->getOne($id,'users_information')]);

	}

	public function show_create_user_page()
	{
		echo $this->templates->render('page_create_user', ['auth' => $this->auth,'flash' =>$this->flash]);

	}




}














?>