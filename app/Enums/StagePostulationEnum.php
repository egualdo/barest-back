<?php

namespace App\Enums;

enum StagePostulationEnum: string {
    case pendant = 'pendant';
    case viewed = 'viewed';
    case contact = 'contact';
    case completed = 'completed';


    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}


