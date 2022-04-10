<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserLessonStatus extends Enum
{
    const ON_PROGRESS =   "ON PROGRES";
    const DONE =   "DONE";
}
