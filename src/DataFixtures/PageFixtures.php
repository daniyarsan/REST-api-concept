<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $author = new Author();
        $author->setSlug('admin');
        $author->setFirstName('Admin');
        $author->setLastname('Admin');
        $manager->persist($author);

        foreach ($this->getCategories() as $categoryItem) {
            $category = new Category();
            $category->setAlias($categoryItem['slug']);
            $category->setName($categoryItem['title']);
            $manager->persist($category);
            $manager->flush();

            foreach ($this->getSubCategories() as $categoryItem) {
                $subcategory = new Category();
                $subcategory->setAlias($category->getAlias() . '-' . $categoryItem['slug']);
                $subcategory->setName($category->getName() . ' ' . $categoryItem['title']);
                $subcategory->setParentId($category->getId());
                $manager->persist($subcategory);
            }
        }

        $manager->flush();


        for($i = 0; $i < 20; $i++) {
            $page = new Page();
            $page->setTitle('Page '.$i);
            $page->setIsActive(true);
            $page->setBody('Test Test Test Test Test Test Test Test ');
            $page->setAuthor($author);
            $page->setCategory($category);
            $manager->persist($page);
            $manager->flush();
        }
    }


    public function getCategories()
    {
        return [
            ['slug' => 'faq', 'title' => 'FAQ'],
            ['slug' => 'news', 'title' => 'News'],
            ['slug' => 'import', 'title' => 'Important'],
            ['slug' => 'notes', 'title' => 'Notes'],
        ];
    }

    public function getSubCategories()
    {
        return [
            ['slug' => 'sub1', 'title' => 'Sub1'],
            ['slug' => 'sub2', 'title' => 'Sub2'],
            ['slug' => 'sub3', 'title' => 'Sub3'],
            ['slug' => 'sub4', 'title' => 'Sub4'],
        ];
    }
}
