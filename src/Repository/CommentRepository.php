<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Comment;
use Doctrine\ORM\Query;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Comment::class);
	}

	public function resetIndex()
	{
		$connection = $this->getEntityManager()->getConnection();
		$connection->exec("ALTER TABLE comment AUTO_INCREMENT = 1;");
	}

	/**
	 * @return Comment[]
	 */
	public function findItems(int $index = 1, $idFigure): array
	{
		return $this->getQueryDesc($index, $idFigure)
			->getQuery()
			->getResult();
	}

	public function findMoreItems(int $index, $idFigure): array
	{
		if ($index !== 1) {
			return $this->getQueryDesc($index, $idFigure)
				->getQuery()
				->getResult(Query::HYDRATE_ARRAY);
		}
	}

	public function countAll($idFigure)
	{
		return intval($this->createQueryBuilder('p')
			->select('COUNT(p)')
			->where('p.figure = :id_figure')
			->setParameter('id_figure', $idFigure)
			->getQuery()->getSingleScalarResult());
	}

	private function getQueryDesc(int $index, $idFigure)
	{
		$total = $this->countAll($idFigure);
		$nbResultsPerPage = 10;


		$nbGroups = round($total / $nbResultsPerPage);
		$start = 0;
		$intervals = [];

		if (!$total || !$nbGroups) {
			return $this->createQueryBuilder('p')
				->orderBy('p.created_at', 'DESC')
				->where('p.figure = :id_figure')
				->setParameter('id_figure', $idFigure)
				->setMaxResults($nbResultsPerPage);
		}

		for ($i = 1; $i <= $nbGroups; $i++) {
			$intervals[$i] = [
				'start' => $start
			];

			$start += $nbResultsPerPage;
		}

		$interval = $intervals[$index];

		return $this->createQueryBuilder('p')
			->orderBy('p.created_at', 'DESC')
			->where('p.figure = :id_figure')
			->setParameter('id_figure', $idFigure)
			->setFirstResult($interval['start'])
			->setMaxResults($nbResultsPerPage);
	}
}