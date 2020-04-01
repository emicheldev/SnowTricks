<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('FR-fr');

        $users = [];
        $categories = [];
        $genders = ['male', 'female'];
        $categoriesDemoName = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        $tricksDemoName = ['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air'];

        // 20 User
        for ($i=0; $i<20; $i++)
        {
            $user = new User();

            $gender = $faker->randomElement($genders);

            $user->setUsername($faker->userName)
                 ->setEmail($faker->safeEmail)
                 ->setPassword($this->encoder->encodePassword($user, 'password'))
                 ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                 ->setActivated(true)
                 ->setImagePath('https://randomuser.me')
                 ->setImageName('api/portraits/' . ($gender == 'male' ? 'men/' : 'women/') . $faker->numberBetween(1,99) . '.jpg')
                 ->setToken(md5(random_bytes(10)));
            $manager->persist($user);
            $users[] = $user;
        }

        // 7 Category
        foreach ($categoriesDemoName as $categoryName)
        {
            $category = new Category();
            $category->setName($categoryName);

            $manager->persist($category);
            $categories[] = $category;
        }

        // 9 Tricks
        foreach ($tricksDemoName as $trickName)
        {
            $trick = new Trick();
            $trick->setName($trickName)
                ->setDescription($faker->paragraph(5))
                ->setCreatedAt(new \Datetime)
                ->setUpdatedAt(new \Datetime)
                ->setUser($faker->randomElement($users))
                ->setCategory($faker->randomElement($categories))
                ->setSlug($faker->slug());

            // 3 Image by Trick
            for ($k=1; $k<4; $k++)
            {
                $image = new Image();
                $image->setName($trick->getName() . ' ' . $k . '.jpg')
                      ->setCaption('Image du trick ' . $trick->getName())
                      ->setTrick($trick)
                      ->setPath('img/tricks');
                
                $manager->persist($image);
                
                if($k === 3)
                {
                    // Last Image become the main one
                    $trick->setMainImage($image);
                    $manager->persist($trick);
                }

            }

            // 1 to 2 Video by Trick
            for ($l=0; $l<mt_rand(1, 2); $l++)
            {
                $video = new Video();
                $video->setUrl('https://www.youtube.com/watch?v=tHHxTHZwFUw')
                      ->setTrick($trick);
                
                $manager->persist($video);
            }

            // 0 to 30 Comment by Trick
            for ($m=0; $m<mt_rand(0, 30); $m++)
            {
                $comment = new Comment();
                $comment->setContent($faker->sentence(mt_rand(1, 5)))
                        ->setCreatedAt(new \Datetime)
                        ->setUser($faker->randomElement($users))
                        ->setTrick($trick);
                
                $manager->persist($comment);
            }                
        }

        $manager->flush();
    }
}