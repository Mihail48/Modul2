<?php
namespace App\controllers;

if (!session_id()) {
    @session_start();
}

use League\Plates\Engine;

use Aura\SqlQuery\QueryFactory;

use PDO;

use Delight\Auth\Auth;

use App\model\QueryBuilder;

use \Tamtamchik\SimpleFlash\Flash;

use App\model\User;

// создаем контроллер для работы с пользовательскими функциями
class UserController
{
		private $auth;
		private $templates;
		private $qb;
		private $flash;
		private $user;

		public function __construct(QueryBuilder $qb, Flash $flash, Engine $engine, Auth $auth, User $User)
		{
			$this->auth = $auth;
			$this->templates = $engine;
			$this->qb = $qb;
			$this->flash = $flash;
			$this->user = $User;
		}


		public function edit_make()
		{
			$this->user->edit($_GET['id']);
			$this->flash->success('Профиль успешно обновлен');
			header("location: /users");
		}

		public function security_make()
		{


			$email = $this->qb->getOneCols('users','email',$_POST['email']);

			if(empty($_POST['email']))
			{
				$this->flash->warning('Поле email не может быть пустым');
				header('location: /security?id=<?php echo $user["id"];?>');
				exit;
			}
			elseif(empty($email)==FALSE)
			{
				$this->flash->warning('Такой email уже зарегистрирован');
				header('location: /security?id=<?php echo $user["id"];?>');
				exit;
			}
			elseif(empty($_POST['password']))
			{
				$this->flash->warning('Поле password не может быть пустым');
				header('location: /security?id=<?php echo $user["id"];?>');
				exit;
			}
			elseif(empty($_POST['password_confirm']))
			{
				$this->flash->warning('Поле подтверждения пароля не может быть пустым');
				header('location: /security?id=<?php echo $user["id"];?>');
				exit;
			}
			elseif($this->auth->hasRole(\Delight\Auth\Role::ADMIN))
			{
				$this->qb->update(['email' => $_POST['email']], $_GET['id'], 'users');
				$this->qb->update(['password' => password_hash($_POST['password'],PASSWORD_DEFAULT)], $_GET['id'], 'users');
				$this->qb->update(['email' => $_POST['email']], $_GET['id'], 'users_information');
				$this->flash->success('Настройки безопастности обновлены');
				header("location: /users");
				exit;
			}
			else
			{
				$this->user->security($_GET['id']);
				$this->flash->success('Настройки безопастности обновлены');
				header("location: /users");
			}

		}

		public function status_make()
		{
			$this->user->status($_GET['id']);
			$this->flash->success('статус изменен');

			header("location: /");
		}

		public function images_make()
		{
			$this->user->delete_images($_GET['id']);

			if($this->user->images($_GET['id'])==TRUE)
				{
					$this->flash->success('аватар изменен');
				}

			else
				{
					$this->flash->warning('аватар не загружен');
				}


			header("location: /");
		}

		public function create_user_make()
		{


			$userId = $this->user->create_user();

			$this->user->images($userId);

			$this->flash->success('Новый пользователь создан');

			header("location: /");

		}

		public function delete_user()
		{
			$this->user->delete();

		    if ($this->auth->hasRole(\Delight\Auth\Role::ADMIN)==FALSE)
		    	{
		    		$this->auth->logOut();
		    	}

			header("location: /");

		}

}























?>