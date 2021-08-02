<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CourseLevel extends Enum
{
    const BEGINNER =  "BEGINNER";
    const INTERMEDIATE =   "INTERMEDIATE";
    const ADVANCED = "ADVANCED";
}
