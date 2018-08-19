<?php
// src/AppBundle/Service/JobManager.php
namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use AppBundle\Exceptions\ApiException;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;

/**
 * Class JobManager
 *
 * @package AppBundle\Service
 */
class JobManager
{
    /** @var EntityManager */
    private $em;

    /**
     * JobManager constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Saves job for create and update
     *
     * @param Job $job
     *
     * @return void
     * @throws ApiException
     */
    public function handleJob(Job $job): void
    {
        $this->checkValidations($job);

        try {
            $this->em->persist($job);
            $this->em->flush();
        } catch (Exception $e) {
            throw new ApiException(ApiException::CANNOT_SAVE);
        }
    }

    /**
     * Lists all jobs in one category
     *
     * @param Category $category
     *
     * @return array
     * @throws ApiException
     */
    public function listJobs(Category $category): array
    {
        if (!$category || !$category) {
            throw new ApiException(ApiException::CATEGORY_NOT_FOUND);
        }

        $jobs = $this->em->getRepository(Job::class)->findBy(['category' => $category]);
        $jobList = $this->getJobListApiData($jobs);

        return $jobList;
    }

    /**
     * Deletes job
     *
     * @param Job $job
     *
     * @throws ApiException
     */
    public function deleteJob(Job $job): void
    {
        if ($job === null) {
            throw new ApiException(ApiException::JOB_NOT_FOUND);
        }

        try {
            $this->em->remove($job);
            $this->em->flush();
        } catch (Exception $e) {
            throw new ApiException(ApiException::JOB_CANNOT_DELETE);
        }
    }

    /**
     * Returns API data for one job
     *
     * @param Job $job
     *
     * @return array
     */
    public function getApiData(Job $job): array
    {
        return [
            'category' => $job->getCategory()->getId(),
            'title' => $job->getTitle(),
            'zip' => $job->getZip(),
            'description' => $job->getDescription(),
            'dueDate' => $job->getDueDate()->format('Y-m-d')
        ];
    }

    /**
     * Returns list of jobs with API data
     *
     * @param array $jobs
     *
     * @return array
     */
    public function getJobListApiData(array $jobs): array
    {
        $result = [];

        foreach ($jobs as $job) {
            $result[] = $this->getApiData($job);
        }

        return $result;
    }

    /**
     * Checks if given zip code is german
     *
     * @param $zipCode
     *
     * @return bool
     */
    private function checkValidZipCode($zipCode): bool
    {
        // TODO: Move username to parameters.yml
        if (is_numeric($zipCode) && strlen($zipCode) === 5) {
            $url = 'http://api.geonames.org/postalCodeLookupJSON?';
            $url .= http_build_query([
                'postalcode' => (string)$zipCode,
                'country' => 'DE',
                'username' => 'kata_riina'
            ]);

            $response = json_decode(file_get_contents($url), true);

            if (isset($response['postalcodes']) && count($response['postalcodes']) >= 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks validations for a job
     *
     * @param Job $job
     *
     * @throws ApiException
     */
    private function checkValidations(Job $job): void
    {
        if ($job->getCategory() === null) {
            throw new ApiException(ApiException::CATEGORY_NOT_FOUND);
        }

        if (!$this->checkValidZipCode($job->getZip())) {
            throw new ApiException(ApiException::WRONG_ZIP_CODE);
        }

        $title = $job->getTitle();
        if (strlen($title) < 5 || strlen($title) > 50) {
            throw new ApiException(ApiException::WRONG_TITLE_LENGTH);
        }

        if (strlen($job->getDescription()) > 255) {
            throw new ApiException(ApiException::DESCRIPTION_TOO_LONG);
        }

        if ($job->getDueDate() < new DateTime()) {
            throw new ApiException(ApiException::WRONG_DATE);
        }
    }
}
