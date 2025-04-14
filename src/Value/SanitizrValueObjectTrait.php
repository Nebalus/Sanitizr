<?php

namespace Nebalus\Sanitizr\Value;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

trait SanitizrValueObjectTrait
{
    private static AbstractSanitizrSchema $schemaCache;

    public static function getSchema(): AbstractSanitizrSchema
    {
        if (isset(self::$schemaCache)) {
            return self::$schemaCache;
        }
        self::$schemaCache = self::defineSchema();
        return self::$schemaCache;
    }

    abstract protected static function defineSchema(): AbstractSanitizrSchema;
}
