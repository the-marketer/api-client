<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

use Symfony\Component\Validator\Validation;
use TheMarketer\ApiClient\Exception\ValidationException;

abstract class Data
{
    public static function validateAndCreate(array $data): static
    {
        $instance = new static(...$data);
        $instance->validate();

        return $instance;
    }

    protected function validate(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($this);
        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }

            throw new ValidationException(implode(', ', $messages));
        }
    }

    public function toArray(): array
    {
        $result = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($value instanceof self) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $result[$key] = array_map(
                    fn($item) => $item instanceof self ? $item->toArray() : $item,
                    $value
                );
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}