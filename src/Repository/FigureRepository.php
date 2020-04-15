<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Figure::class);
	}

	public function resetIndex()
	{
		$connection = $this->getEntityManager()->getConnection();
		$connection->exec("ALTER TABLE figure AUTO_INCREMENT = 1;");
	}

	/**
	 * @return Figure[]
	 */
	public function findItems(int $index = 1): array
	{
		return $this->getQueryDesc($index)
			->getQuery()
			->getResult();
	}

	public function findMoreItems(int $index): array
	{
		if ($index !== 1) {
			return $this->getQueryDesc($index)
				->getQuery()
				->getResult(Query::HYDRATE_ARRAY);
		}
	}

	public function countAll()
	{
		return intval($this->createQueryBuilder('p')
			->select('COUNT(p)')
			->getQuery()->getSingleScalarResult());
	}

	public function getLastId()
	{
		return $this->findOneBy([], ['id' => 'desc']);
	}


	private function getQueryDesc(int $index)
	{
		$total = $this->countAll();
		$nbGroups = round($total / 15);
		$start = 0;
		$intervals = [];
		for ($i = 1; $i <= $nbGroups; $i++) {
			$intervals[$i] = [
				'start' => $start
			];

			$start += 15;
		}

		$interval = $intervals[$index];

		return $this->createQueryBuilder('p')
			->orderBy('p.updated_at', 'DESC')
			->setFirstResult($interval['start'])
			->setMaxResults(15);
	}
}