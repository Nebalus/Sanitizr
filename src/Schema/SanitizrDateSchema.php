<?php

namespace Nebalus\Sanitizr\Schema;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrDateSchema extends AbstractSanitizrSchema
{
    /**
     * @throws DateMalformedStringException
     * @throws SanitizrValidationException
     */
    /*
    public function after(int|DateTimeInterface $value, string $message = 'Cannot be before %s'): static
    {
        $this->addCheck(function (int|DateTimeInterface $input) use ($value, $message)
            if (is_int($input)) {
                $input = new DateTime($input);
            }

            if ($input != $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }
*/
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be an DATE', string $path = ''): mixed
    {
        // TODO: Implement parseValue() method.
    }
}
