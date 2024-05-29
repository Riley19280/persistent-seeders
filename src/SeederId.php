<?php

namespace PersistentSeeders;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SeederId
{
    public string $functionName;

    /**
     * @param string      $id   The uuid of the seeder
     * @param null|string $name The custom name for the seeder, defaults to the function name
     */
    public function __construct(public string $id, public ?string $name = null)
    {
    }
}
