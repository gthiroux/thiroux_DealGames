<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'firstname'  => 'Alice',
                'lastname'   => 'Martin',
                'email'      => 'alice.martin@email.com',
                'password'   => 'password123',
                'roles'      => ['ROLE_USER'],
                'isVerified' => true,
            ],
            [
                'firstname'  => 'Bob',
                'lastname'   => 'Dupont',
                'email'      => 'bob.dupont@email.com',
                'password'   => 'password123',
                'roles'      => ['ROLE_USER'],
                'isVerified' => true,
            ],
            [
                'firstname'  => 'Clara',
                'lastname'   => 'Leroy',
                'email'      => 'clara.leroy@email.com',
                'password'   => 'password123',
                'roles'      => ['ROLE_USER'],
                'isVerified' => false,
            ],
            [
                'firstname'  => 'Admin',
                'lastname'   => 'Super',
                'email'      => 'admin@email.com',
                'password'   => 'admin1234',
                'roles'      => ['ROLE_ADMIN','ROLE_USER'],
                'isVerified' => true,
            ],
        ];

        foreach ($users as $key => $data) {
            $user = new User();
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setIsVerified($data['isVerified']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));

            $manager->persist($user);

            // Référence pour AdFixture
            $this->addReference('user_' . $key, $user);
        }

        $manager->flush();
    }
}
