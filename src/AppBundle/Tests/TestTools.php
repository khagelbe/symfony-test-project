<?php

namespace AppBundle\Tests;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use DateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestTools
{
    /** @var ContainerInterface */
    private $container;

    /**
     * TestTools constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates category for tests
     *
     * @param string $name | null
     *
     * @return Category
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCategory(string $name = null): Category
    {
        if ($name === null) {
            $name = sprintf('TestCategory%s', rand());
        }

        $category = new Category();
        $category->setName($name);

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $em->persist($category);
        $em->flush();

        return $category;
    }

    /**
     * Creates job for tests
     *
     * @param Category $category
     * @param string   $title
     * @param string   $description
     * @param string   $zip
     * @param string   $dueDate
     *
     * @return Job
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createJob(
        Category $category,
        string $title = 'Wohnungssanierung',
        string $description = 'Lorem ipsum',
        string $zip = '10711',
        string $dueDate = "2018-09-12"
    ): Job
    {
        $job = new Job();

        $job->setCategory($category);
        $job->setTitle($title);
        $job->setDescription($description);
        $job->setZip($zip);
        $job->setDueDate(new DateTime($dueDate));

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $em->persist($job);
        $em->flush();

        return $job;
    }
}
