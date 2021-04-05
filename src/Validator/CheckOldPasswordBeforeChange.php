<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class CheckOldPasswordBeforeChange extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'A megadott jelszó helytelen!';

    public $anonymousMessage = 'Nem változtathatsz jelszót autentikáció nélkül!';
}
