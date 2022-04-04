<?php

namespace App\model;

use Aura\SqlQuery\QueryFactory;

use PDO;
// создаем модель построителя запросов в базу данных и работы с ней
class QueryBuilder{

	private $pdo;

	private $queryFactory;

	public function __construct(PDO $pdo, QueryFactory $queryFactory)
	{
		$this->pdo = $pdo;

		$this->queryFactory = $queryFactory;
	}
	public function getAll($table)

	{

		$select = $this->queryFactory->newSelect();

		$select->cols(["*"])
			->from($table);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetchALL(PDO::FETCH_ASSOC);

		return $result;
	}

// выборка данных по столбцу

	public function getOneCols($table, $cols,$data)
	{

		$select = $this->queryFactory->newSelect();

		$select->cols([$cols])
			->from($table)
			->where("$cols = :data")
			->bindValue('data', $data);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetchALL(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getOne($id, $table)
	{
		$select = $this->queryFactory->newSelect();
		$select->cols(["*"])
				->from($table)
				->where('id = :id')
				->bindValue('id', $id);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function insert($data, $table)
	{
		$insert = $this->queryFactory->newInsert();

		$insert ->into($table)
		    	->cols($data);

		$sth = $this->pdo->prepare($insert->getStatement());

		$sth->execute($insert->getBindValues());

	}

	public function update($data, $id, $table)
	{
		$update = $this->queryFactory->newUpdate();

		$update
		    ->table($table)                  // update this table
		    ->cols($data)
		    ->where('id = :id')
		    ->bindValue('id', $id);


		$sth = $this->pdo->prepare($update->getStatement());

		$sth->execute($update->getBindValues());
	}

	public function delete($id, $table)
	{
		$delete = $this->queryFactory->newDelete();

		$delete
		    ->from($table)
		    ->where('id = :id')
		    ->bindValue('id', $id);

		$sth = $this->pdo->prepare($delete->getStatement());

		$sth->execute($delete->getBindValues());

	}

	public function paging($table,$paging,$page)
	{
		$select = $this->queryFactory->newSelect();
		$select->cols(["*"])
				->from($table)
				->setPaging($paging)
				->page($page);


		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetchALL(PDO::FETCH_ASSOC);
		return $result;
	}


}






?>