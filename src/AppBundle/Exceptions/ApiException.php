<?php

namespace AppBundle\Exceptions;


use Exception;

class ApiException extends Exception
{
    const CATEGORY_NOT_FOUND = 110;
    const WRONG_ZIP_CODE = 111;
    const WRONG_TITLE_LENGTH = 112;
    const DESCRIPTION_TOO_LONG = 113;
    const WRONG_DATE = 114;
    const NO_CONTENT = 115;
    const CANNOT_SAVE = 116;
    const JOB_NOT_FOUND = 117;
    const JOB_CANNOT_DELETE = 118;
    const FORM_NOT_VALID = 119;

    /**
     * ApiException constructor.
     * @param int $errorCode
     */
    public function __construct(int $errorCode)
    {
        $errorMessage = $this->getErrorMessage($errorCode);

        parent::__construct($errorMessage, $errorCode);
    }

    /**
     * @return array
     */
    public function getApiErrorData()
    {
        return [
            'errorCode' => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }

    /**
     * @param int $errorCode
     * @return string
     */
    private function getErrorMessage(int $errorCode)
    {
        switch ($errorCode) {
            case self::CATEGORY_NOT_FOUND:
                return "Category not found";
            case self::WRONG_ZIP_CODE:
                return "Only germans, please";
            case self::WRONG_TITLE_LENGTH:
                return "Title has to be between 5 and 50 characters";
            case self::DESCRIPTION_TOO_LONG:
                return "Description is too long";
            case self::WRONG_DATE:
                return "Due date cannot be in the past";
            case self::NO_CONTENT:
                return "No content";
            case self::CANNOT_SAVE:
                return "Cannot save job";
            case self::JOB_NOT_FOUND:
                return "Job not found";
            case self::JOB_CANNOT_DELETE:
                return "Job cannot be deleted";
            case self::FORM_NOT_VALID:
                return "Form is not valid";
            default:
                return "Something went wrong";
        }
    }
}
