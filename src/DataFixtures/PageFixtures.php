<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    protected const CAT_NUM = 10;

    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < self::CAT_NUM; $i++) {
            $category = new Category();
            $category->setName('Category '.$i);
            $category->setAlias('category'.$i);
            $manager->persist($category);
            $manager->flush();
        }

        for($i = 0; $i < 20; $i++) {
            $page = new Page();
            $page->setTitle('Page '.$i);
            $page->setStatus(1);
            $page->setAuthorId(rand(0, 10));
            $page->setBody('Test Test Test Test Test Test Test Test ');
            $category = $manager->getRepository(Category::class)->findOneById(rand(0, self::CAT_NUM));
            $page->setCategory($category);
            $manager->persist($page);
            $manager->flush();
        }


    }
}
