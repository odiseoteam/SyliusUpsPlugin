<?php

declare(strict_types=1);

namespace Odiseo\SyliusUpsPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OdiseoSyliusUpsPlugin extends Bundle
{
    use SyliusPluginTrait;
}
