<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories=['Console','Jeux','Accessoires'];

        foreach ($categories as $key=>$name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
           $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}