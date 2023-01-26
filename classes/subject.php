<?php

class Subject
{
    public const TYPE_DEFAULT = 'közép';
    public const TYPE_ADVANCED = 'emelt';
    private string $name;
    private string $type;
    private int $result;

    public function __construct(string $name, string $type = self::TYPE_DEFAULT, string $result = "")
    {
        $this->name = $name;
        $this->type = $type;
        if($result != "")
        {
            $this->result = intval(trim($result,"%"));
        }else{
            $this->result = -1;
        }
    }

    public function isFailed()
    {
        if($this->result < 20) return true;
        return false;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : string{
        return $this->type;
    }

    public function getResult() : int
    {
        return $this->result;
    }

    public function setResult(int $result)
    {
        $this->result = $result;
    }

    private function equals(Subject $other) : bool
    {
        if($this->name == $other->getName())
        {
            return true;
        }
        return false;
    }

    public static function findInArray(array $array, Subject $subject) : ?Subject
    {
        foreach ($array as $item)
        {
            if($item instanceof Subject)
            {
                if($subject->equals($item))
                {
                    return $item;
                }
            }
        }
        return null;
    }


}