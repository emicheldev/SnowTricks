<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Picture;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\PictureRepository;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FigureFixtures extends Fixture
{
	public function __construct(CategoryRepository $categoryRepository, PictureRepository $pictureRepository, VideoRepository $videoRepository, CommentRepository $commentRepository, FigureRepository $figureRepository, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
	{
		$this->categoryRepository = $categoryRepository;
		$this->pictureRepository = $pictureRepository;
		$this->videoRepository = $videoRepository;
		$this->commentRepository = $commentRepository;
		$this->figureRepository = $figureRepository;
		$this->userRepository = $userRepository;
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
	{
		$faker = Factory::create('fr_FR');

		$categories = ['Grabs', 'Rotations', 'Flips', 'Slides'];
		$categoriesObj = [];

		$figures = ['Melancholie', 'Mute', 'Style week', '540 rotation', 'Indy', 'Stalefish', 'Japan Air', 'Nose grab', '180 rotation', 'Sad', 'Tail grab', '900 rotation', 'Seat Belt', '360 rotation', 'Japan', '720 rotation', 'Backside Air', 'Truck driver', 'Big foot', 'Slide', 'Rocket Air', 'Flip', 'Method Air'];

		$videos = [
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/AzJPhQdTRQQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/IlL-PGqZqVY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/qsd8uaex-Is" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/5mtNg2mf-Hg" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/1TJ08caetkw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
			'<iframe width="560" height="315" src="https://www.youtube.com/embed/UGdif-dwu-8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
		];

		// reset autoincrement à 1
		$this->categoryRepository->resetIndex();
		$this->pictureRepository->resetIndex();
		$this->videoRepository->resetIndex();
		$this->commentRepository->resetIndex();
		$this->figureRepository->resetIndex();
		$this->userRepository->resetIndex();

		$user = new User();
		$user->setUsername('root');
		$user->setEmail('root@root.fr');
		$user->setPassword($this->encoder->encodePassword($user, 'root'));
		$user->setActif(1);
		$manager->persist($user);

		foreach ($categories as $key => $item) {
			$category = new Category();
			$category->setName($item);
			$manager->persist($category);
			array_push($categoriesObj, $category);
		}

		foreach ($figures as $key => $item) {
			$figure = new Figure();

			$date = new \DateTime();

			$figure->setName($item);
			$figure->setCreatedAt($date);
			$figure->setUpdatedAt($date);
			$figure->setDescription($faker->sentences(15, true));
			$figure->addCategory($categoriesObj[random_int(0, 3)]);
			$figure->setMainImage('image-' . random_int(1, 6) . '.jpg');
			$manager->persist($figure);

			for ($i = 1; $i <= 3; $i++) {
				$picture = new Picture();
				$picture->setFigure($figure);
				$picture->setFilename('image-' . random_int(1, 6) . '.jpg');
				$picture->setUpdatedAt($date);
				$manager->persist($picture);

				$video = new Video();
				$video->setFigure($figure);
				$video->setUrl($videos[random_int(0, 5)]);
				$manager->persist($video);
			}

			for ($i = 1; $i <= 30; $i++) {
				$comment = new Comment();
				$comment->setContent($faker->sentences(random_int(1, 4), true));
				$comment->setCreatedAt($date);
				$comment->setFigure($figure);
				$comment->setUser($user);
				$manager->persist($comment);
			}

			// for ()
		}

		// for ($i = 0; $i < 100; $i++) {
		// 	$figure = new Figures();

		// 	$date = new \DateTime();

		// 	$figure->setName($faker->words(4, true));
		// 	$figure->setCreatedAt($date);
		// 	$figure->setUpdatedAt($date);
		// 	$figure->setDescription($faker->sentences(10, true));
		// 	$manager->persist($figure);
		// }

		$manager->flush();
	}
}