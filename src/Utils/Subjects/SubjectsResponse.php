<?php

namespace BiteIT\Utils;

class SubjectsResponse
{
    protected $isValid = false;

    /** @var Subject */
    protected $subject = null;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid && $this->getSubject() instanceof Subject;
    }

    /**
     * @param bool $isValid
     */
    public function setIsValid(bool $isValid): void
    {
        $this->isValid = $isValid;
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject
    {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     */
    public function setSubject(Subject $subject): void
    {
        $this->subject = $subject;
    }
}