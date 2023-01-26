<?php

class Major
{
    private string $university;
    private string $faculty;
    private  string $name;
    private Subject $mandatorySubject;
    private array $freeSubjects;


    public function __construct(string $university, string $faculty, string $name, Subject $mandatorySubject, array $freeSubjects)
    {
        $this->university = $university;
        $this->faculty = $faculty;
        $this->name = $name;
        $this->freeSubjects = $freeSubjects;
        $this->mandatorySubject = $mandatorySubject;
    }

    public function canApply(array $subjects) : bool
    {
        $inputMandatorySubject = Subject::findInArray($subjects,$this->mandatorySubject);
        if($inputMandatorySubject == null) return false;

        if ($inputMandatorySubject->getType() != $this->mandatorySubject->getType() && $inputMandatorySubject->getType() != Subject::TYPE_ADVANCED)
        {
            return false;
        }

        foreach ($this->freeSubjects as $freeSubject)
        {
            $inputFreeSubject = Subject::findInArray($subjects,$freeSubject);
            if($inputFreeSubject != null)
            {
                if ( $inputFreeSubject->getType() == $freeSubject->getType() || $inputFreeSubject->getType() == Subject::TYPE_ADVANCED) return true;
            }
        }

        return false;
    }

    public function getMandatorySubject() :Subject
    {
        return $this->mandatorySubject;
    }

    public function getFreeSubjects() :array
    {
        return $this->freeSubjects;
    }
}