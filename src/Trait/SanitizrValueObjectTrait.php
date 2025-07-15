<?php

namespace Nebalus\Sanitizr\Trait;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

trait SanitizrValueObjectTrait
{
    private static AbstractSanitizrSchema $schemaCache;

    public static function getSchema(): AbstractSanitizrSchema
    {
        if (isset(self::$schemaCache)) {
            return clone self::$schemaCache;
        }
        self::$schemaCache = self::defineSchema();
        return clone self::$schemaCache;
    }

    abstract protected static function defineSchema(): AbstractSanitizrSchema;
}
