<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user->setFirstName('lala')
            ->setLastName('lala')
            ->setPassword('password')
            ->setEmail('lala@gmail.com')
            ->setRoles(["ROLE_ADMIN"])
            ->setActive(false);

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'password'
        ));


        $manager->persist($user);


        $faker = Factory::create('fr_FR');


        for ($i = 0; $i < 10; $i++) {


            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setActive(false);

            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'password'
            ));


            $manager->persist($user);
        }

        $manager->flush();
    }
}
