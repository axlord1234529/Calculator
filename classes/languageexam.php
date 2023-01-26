<?php
abstract class LanguageExam
{
    public const TYPE_C1 = 'C1';
    public const TYPE_B2 = 'B2';
    private string $language;

    public function __construct(string $name = "")
    {
        $this->language = $name;
    }

    abstract public function getPoints();

    public function equals(LanguageExam $languageExam) : bool
    {
        if($this->language == $languageExam->getLanguage())
        {
            return true;
        }
        return false;
    }

    public function getLanguage() : string
    {
        return $this->language;
    }
}