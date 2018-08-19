<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use AppBundle\Exceptions\ApiException;
use AppBundle\Form\CreateJobType;
use AppBundle\Form\UpdateJobType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    /**
     * Creates job
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $jobManager = $this->container->get('job_manager');

        /** @var Job $job */
        $job = new Job();

        $form = $this->createForm(CreateJobType::class, $job);

        $form->handleRequest($request);

        // TODO Handle form errors more specific
        if (!$form->isValid()) {
            $exception = new ApiException(ApiException::FORM_NOT_VALID);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $job = $form->getData();

        try {
            $jobManager->handleJob($job);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $jobManager->getApiData($job);
        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Lists all available jobs based on category
     *
     * @param Category $category
     *
     * @return JsonResponse
     */
    public function listAction(Category $category): JsonResponse
    {
        $jobManager = $this->container->get('job_manager');

        try {
            $jobList = $jobManager->listJobs($category);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($jobList, Response::HTTP_OK);
    }

    /**
     * Updates job
     *
     * @param Request $request
     * @param Job     $job
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, ?Job $job): JsonResponse
    {
        if ($job === null) {
            $exception = new ApiException(ApiException::JOB_NOT_FOUND);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_NOT_FOUND);
        }

        $jobManager = $this->container->get('job_manager');

        $form = $this->createForm(new UpdateJobType(), $job);
        $form->submit($request, false);

        // TODO Handle form errors more specific
        if (!$form->isValid()) {
            $exception = new ApiException(ApiException::FORM_NOT_VALID);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $job = $form->getData();

        try {
            $jobManager->handleJob($job);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $jobManager->getApiData($job);

        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Deletes job
     *
     * @param Job $job
     *
     * @return JsonResponse
     */
    public function deleteAction(?Job $job): JsonResponse
    {
        if ($job === null) {
            $exception = new ApiException(ApiException::JOB_NOT_FOUND);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_NOT_FOUND);
        }

        $jobManager = $this->container->get('job_manager');

        try {
            $jobManager->deleteJob($job);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(['Job deleted'], Response::HTTP_NO_CONTENT);
    }
}
