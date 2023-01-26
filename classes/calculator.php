<?php
include_once 'includes/auto_loader.inc.php';
class Calculator
{
    private const MANDATORY_SUBJECTS = ['magyar nyelv és irodalom','történelem','matematika'];
    private array $subjects;
    private Major $major;
    private string $error;
    private array $languageExams;


    public function __construct(array $input)
    {

        $results = $input['erettsegi-eredmenyek'];
        foreach ($results as $result)
        {

            $subject = new Subject($result['nev'],$result['tipus'],$result['eredmeny']);
            if($subject->isFailed())
            {
                $this->error = "hiba, nem lehetséges a pontszámítás a ".$subject->getName()."tárgyból elért 20% alatti eredmény miatt";
            }

            $this->subjects[] = $subject;
        }

        $majorName = $input['valasztott-szak']['szak'];
        switch ($majorName)
        {
            case 'Programtervező informatikus':

                $mandatorySubject = new Subject('matematika');
                $freeSubjects = array(new Subject('fizika'),new Subject('biológia'),new Subject('informatika'), new Subject('kémia'));

                $this->major = new Major($input['valasztott-szak']['egyetem'], $input['valasztott-szak']['kar'], $input['valasztott-szak']['szak'], $mandatorySubject, $freeSubjects);
                break;
            case 'Anglisztika':

                $mandatorySubject = new Subject('angol','emelt');
                $freeSubjects = array(new Subject('francia'),new Subject('német'),new Subject('olasz'), new Subject('orosz'), new Subject('spanyol'));

                $this->major = new Major($input['valasztott-szak']['egyetem'], $input['valasztott-szak']['kar'], $input['valasztott-szak']['szak'], $mandatorySubject, $freeSubjects);
                break;
        }


        $exams = $input['tobbletpontok'];

        foreach ($exams as $exam)
        {

            $language = $exam['nyelv'];
            if(in_array(LanguageExam::TYPE_C1,$exam)) $this->languageExams [] = new C1($language);
            if(in_array(LanguageExam::TYPE_B2,$exam)) $this->languageExams [] = new B2($language);


        }


    }

    private function hasAllSubjects() : bool
    {
        foreach (self::MANDATORY_SUBJECTS as $MANDATORY_SUBJECT)
        {
            $mandatorySubject = new Subject($MANDATORY_SUBJECT);
            if(Subject::findInArray($this->subjects, $mandatorySubject) == null)
            {
                return false;
            }
        }

        if(!$this->major->canApply($this->subjects))
        {
            return false;
        }

        return true;
    }

    public function calculate() : string
    {
        $excessPoints = 0;
        if(isset($this->error)) return $this->error;

        if (!$this->hasAllSubjects())
        {
            return "hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt";
        }

        $mandatorySubject = Subject::findInArray($this->subjects, $this->major->getMandatorySubject());
        $mandatorySubjectResult = $mandatorySubject->getResult();

        if($mandatorySubject->getType() == Subject::TYPE_ADVANCED)
        {
            $excessPoints += 50;
        }

        $freeSubjectResults = array();
        foreach ($this->major->getFreeSubjects() as $majorFreeSubject)
        {
            $freeSubject = Subject::findInArray($this->subjects,$majorFreeSubject);
            if($freeSubject != null)
            {
                $freeSubjectResults [] = $freeSubject->getResult();
                if($freeSubject->getType() == Subject::TYPE_ADVANCED && $excessPoints < 100)
                {
                    $excessPoints += 50;
                }
            }
        }

        asort($freeSubjectResults);
        $bestFreeSubjectResult = array_shift($freeSubjectResults);

        $basePoints = ($mandatorySubjectResult + $bestFreeSubjectResult) * 2;


        $pointsFromLanguageExams = 0;
        /*
        if($excessPoints < 100)
        {
            for ($i = 0; $i < count($this->languageExams) ; $i++ )
            {
                if (get_class($this->languageExams[$i]) == LanguageExam::TYPE_B2)
                {
                    $exam_c2 = 0;
                    for ($j = $i+1; $j < count($this->languageExams) ; $j++)
                    {
                        if($this->languageExams[$i]->equals($this->languageExams[$j]))
                        {
                            if ($this->languageExams[$i]->getPoints() < $this->languageExams[j]->getPoints())
                            {
                                $exam_c2 += $this->languageExams[j]->getPoints();
                            }
                        }
                    }
                    for ($j = $i-1; $j >= 0 ; $j--)
                    {
                        if($this->languageExams[$i]->equals($this->languageExams[$j]))
                        {
                            if ($this->languageExams[$i]->getPoints() < $this->languageExams[j]->getPoints())
                            {
                                $exam_c2 += $this->languageExams[j]->getPoints();
                            }
                        }
                    }
                    if($exam_c2 > 0)
                    {
                        $pointsFromLanguageExams += $exam_c2;
                    }else{
                        $pointsFromLanguageExams += $this->languageExams[$i]->getPoints();
                    }

                }
                else
                {
                    $pointsFromLanguageExams += $this->languageExams[$i]->getPoints();
                }

            }
        }
        */

        $excessPoints += $this->getPointsFromLanguageExams($excessPoints);
        if($excessPoints > 100) $excessPoints = 100;

        $allPoints = strval($excessPoints + $basePoints);

        return strval($allPoints." (". $basePoints." alappont +".$excessPoints." töbletpont)");
    }

    private function getPointsFromLanguageExams($excessPoints) : int
    {
        $pointsFromLanguageExams = 0;
        if($excessPoints < 100)
        {
            for ($i = 0; $i < count($this->languageExams) ; $i++ )
            {
                if (get_class($this->languageExams[$i]) == LanguageExam::TYPE_B2)
                {
                    $exam_c2 = 0;
                    for ($j = $i+1; $j < count($this->languageExams) ; $j++)
                    {
                        if($this->languageExams[$i]->equals($this->languageExams[$j]))
                        {
                            if ($this->languageExams[$i]->getPoints() < $this->languageExams[j]->getPoints())
                            {
                                $exam_c2 += $this->languageExams[j]->getPoints();
                            }
                        }
                    }
                    for ($j = $i-1; $j >= 0 ; $j--)
                    {
                        if($this->languageExams[$i]->equals($this->languageExams[$j]))
                        {
                            if ($this->languageExams[$i]->getPoints() < $this->languageExams[j]->getPoints())
                            {
                                $exam_c2 += $this->languageExams[j]->getPoints();
                            }
                        }
                    }
                    if($exam_c2 > 0)
                    {
                        $pointsFromLanguageExams += $exam_c2;
                    }else{
                        $pointsFromLanguageExams += $this->languageExams[$i]->getPoints();
                    }

                }
                else
                {
                    $pointsFromLanguageExams += $this->languageExams[$i]->getPoints();
                }

            }
        }
        return $pointsFromLanguageExams;
    }
}