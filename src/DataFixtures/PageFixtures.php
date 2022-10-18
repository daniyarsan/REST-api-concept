<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }
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
            if ($categoryItem['slug'] == 'rules') {
                foreach ($this->getSubCategories() as $subcategoryItem) {
                    $subcategory = new Category();
                    $subcategory->setAlias($subcategoryItem['slug']);
                    $subcategory->setName($subcategoryItem['title']);
                    $subcategory->setParentId($category->getId());
                    $manager->persist($subcategory);
                }
            }

        }
        $manager->flush();

        $statuses = $this->getStatuses();
        $categoriesInDb = $this->entityManager->getRepository(Category::class)->findAll();


        for($i = 0; $i < 20; $i++) {
            $page = new Page();
            $page->setTitle('Page '.$i);
            $page->setStatus($statuses[array_rand($statuses)]);
            $page->setBody('Test Test Test Test Test Test Test Test ');
            $page->setAuthor($author);
            $page->setCategory($categoriesInDb[array_rand($categoriesInDb)]);
            $manager->persist($page);
            $manager->flush();
        }
    }


    public function getCategories()
    {
        return [
            ['slug' => 'rules', 'title' => 'Правила Сайта'],
            ['slug' => 'help', 'title' => 'Помощь'],
            ['slug' => 'exchange', 'title' => 'Обмен'],
            ['slug' => 'faq', 'title' => 'FAQ'],
        ];
    }

    public function getSubCategories()
    {
        return [
            ['slug' => 'common', 'title' => 'Общие'],
            ['slug' => 'instructions', 'title' => 'Инструкции'],
            ['slug' => 'questions', 'title' => 'Вопросы']
        ];
    }

    public function getStatuses()
    {
        return [
            Page::STATUS_ACTIVE,
            Page::STATUS_MODERATION,
            Page::STATUS_HIDDEN,
        ];
    }
}
