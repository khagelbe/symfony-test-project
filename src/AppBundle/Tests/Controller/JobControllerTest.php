<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Job;
use AppBundle\Tests\TestTools;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class JobControllerTest extends WebTestCase
{
    /** @var Client */
    private $client;

    /** @var TestTools */
    private $tools;

    /**
     * Setup function
     */
    public function setUp()
    {
        $this->client = $client = static::createClient();
        $this->tools = new TestTools($this->getContainer());
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        return static::$kernel->getContainer();
    }

    /**
     * Lists all categories in db which is 5
     */
    public function testListAllCategories()
    {
        $this->client->request('GET', sprintf('/job/list-categories'));

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($response);
    }

    /**
     * Creates a job successfully
     */
    public function testCreateJobSuccess()
    {
        $requestBody = [
            "category" => 108140,
            "title" => "Umzug",
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
            "dueDate" => "2018-12-12"
        ];

        $expectedResponse = [
            "category" => 108140,
            "title" => "Umzug",
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
            "dueDate" =>  "2018-12-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response, $expectedResponse);
    }

    /**
     * Lists all job from one category
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testListAllJobs()
    {
        $category = $this->tools->createCategory();
        $categoryId = $category->getId();

        $jobs = [];
        for ($i = 0; $i < 3; $i++) {
            $jobs[] = $this->tools->createJob($category);
        }

        $this->client->request('GET', sprintf('/job/list/%s', $categoryId));

        $expectedResponse = [
            [
                'category' => $category->getId(),
                'title' => $jobs[0]->getTitle(),
                'zip' => $jobs[0]->getZip(),
                'description' => $jobs[0]->getDescription(),
                'dueDate' => $jobs[0]->getDueDate()->format('Y-m-d')
            ]
        ];

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($response);
        $this->assertEquals($expectedResponse[0], $response[0]);
    }

    /**
     * Tries to create a job with wrong zip code
     */
    public function testCreateJobWithNonGermanZipCode()
    {
        $requestBody = [
            "category" => 108140,
            "title" => "muutto",
            "zip" => "012334",
            "description" => "Lorem ipsum",
            "dueDate" => "2018-12-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to create a job with too long title
     */
    public function testCreateJobWithTooLongTitle()
    {
        $requestBody = [
            "category" => 108140,
            "title" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
            "zip" => "10711",
            "description" => "Lorem ipsum",
            "dueDate" => "2018-12-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to create a job with too long description
     */
    public function testCreateJobWithTooLongDescription()
    {
        $requestBody = [
            "category" => 108140,
            "title" => "Umzug",
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                              ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
                              ullamco laboris nisi ut aliquip ex ea commodo consequat.",
            "dueDate" => "2018-12-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to create a job with date in past
     */
    public function testCreateJobWithDueDateInPast()
    {
        $requestBody = [
            "category" => 108140,
            "title" => "Umzug",
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet.",
            "dueDate" => "2018-06-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to create a job with non existing category
     */
    public function testCreateJobWithWrongCategory()
    {
        $requestBody = [
            "category" => 123,
            "title" => "Umzug",
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet.",
            "dueDate" => "2018-12-12"
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Updates a job successfully
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testUpdateJobSuccess()
    {
        $job = $this->tools->createJob($this->tools->createCategory());
        $jobId = $job->getId();

        $requestBody = [
            "title" => "Umzug in Halensee",
            "dueDate" => "2018-09-09"
        ];

        $expectedResponse = [
            "category" => $job->getCategory()->getId(),
            "title" => "Umzug in Halensee",
            "zip" => $job->getZip(),
            "description" => $job->getDescription(),
            "dueDate" =>  "2018-09-09"
        ];

        $this->client->request('PUT', sprintf('/job/update/%s', $jobId), $requestBody);

        $this->assertEquals(
            Response::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Deletes a job
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testDeleteJob()
    {
        $jobId = $this->tools->createJob($this->tools->createCategory())->getId();
        $this->client->request('DELETE', sprintf('/job/delete/%s', $jobId));

        $this->assertEquals(
            Response::HTTP_NO_CONTENT,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $job = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository(Job::class)
            ->findBy(['id' => $jobId]);

        $this->assertEmpty($job);
    }

    /**
     * Tries to delete a job with non existing id
     */
    public function testDeleteJobWrongId()
    {
        $this->client->request('DELETE','/job/delete/1234567');

        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Treis to create a job with an invalid form
     */
    public function testCreateJobFormInvalid()
    {
        $requestBody = [
            "category" => 108140,
            "title" => 1234,
            "zip" => "10711",
            "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
            "dueDate" => 1234
        ];

        $this->client->request('POST', '/job/create', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to update a job with an invalid form
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testUpdateJobFormInvalid()
    {
        $job = $this->tools->createJob($this->tools->createCategory());
        $jobId = $job->getId();

        $requestBody = [
            "title" => "Umzug in Halensee",
            "dueDate" => 12345
        ];

        $this->client->request('PUT',sprintf('/job/update/%s', $jobId), $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Tries to update a job with non existing id
     */
    public function testUpdateJobWrongId()
    {
        $requestBody = [
            "title" => "Umzug in Halensee"
        ];

        $this->client->request('PUT','/job/update/12345678', $requestBody);

        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }
}
