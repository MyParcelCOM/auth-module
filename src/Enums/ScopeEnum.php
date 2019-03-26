<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ScopeEnum BROKER_MEMBER()
 * @method static ScopeEnum ORGANIZATIONS_MANAGE()
 * @method static ScopeEnum SHIPMENTS_MANAGE()
 * @method static ScopeEnum SYSTEM_MANAGE()
 * @method static ScopeEnum EXPERIMENTAL()
 */
class ScopeEnum extends Enum
{
    public const BROKER_MEMBER = 'broker.member';
    public const ORGANIZATIONS_MANAGE = 'organizations.manage';
    public const SHIPMENTS_MANAGE = 'shipments.manage';
    public const SYSTEM_MANAGE = 'system.manage';
    public const EXPERIMENTAL = 'experimental';
}
