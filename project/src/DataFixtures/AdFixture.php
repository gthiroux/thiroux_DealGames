<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $ads = [
            [
                'title'       => 'PlayStation 5 comme neuve',
                'description' => 'PS5 en parfait état, achetée il y a 6 mois. Vendue avec deux manettes.',
                'category'    => 'category_0', // Console
                'user'        => 'user_0',
            ],
            [
                'title'       => 'Xbox Series X',
                'description' => 'Xbox Series X avec 3 jeux inclus. Très peu utilisée.',
                'category'    => 'category_0', // Console
                'user'        => 'user_1',
            ],
            [
                'title'       => 'The Legend of Zelda : Tears of the Kingdom',
                'description' => 'Jeu Nintendo Switch en très bon état. Boîte et notice incluses.',
                'category'    => 'category_1', // Jeux
                'user'        => 'user_0',
            ],
            [
                'title'       => 'FIFA 25 PS5',
                'description' => 'FIFA 25 pour PS5, jamais utilisé, encore sous blister.',
                'category'    => 'category_1', // Jeux
                'user'        => 'user_2',
            ],
            [
                'title'       => 'Manette PS5 DualSense',
                'description' => 'Manette DualSense blanche en excellent état. Chargeur inclus.',
                'category'    => 'category_2', // Accessoires
                'user'        => 'user_1',
            ],
            [
                'title'       => 'Casque gaming sans fil',
                'description' => 'Casque gaming compatible PS5 et PC. Son surround 7.1, autonomie 20h.',
                'category'    => 'category_2', // Accessoires
                'user'        => 'user_2',
            ],
        ];

        foreach ($ads as $data) {
            $ad = new Ad();
            $ad->setTitle($data['title']);
            $ad->setDescription($data['description']);
            $ad->setCategory($this->getReference($data['category'],Category::class));
            $ad->setUser($this->getReference($data['user'],User::class));

            $manager->persist($ad);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
            UserFixture::class,
        ];
    }
}
