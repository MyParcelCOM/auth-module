<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static PermissionEnum BROKERS_READ()
 * @method static PermissionEnum CLIENTS_CREATE()
 * @method static PermissionEnum CLIENTS_READ()
 * @method static PermissionEnum CLIENTS_DELETE()
 * @method static PermissionEnum COMBINED_FILES_CREATE()
 * @method static PermissionEnum COMBINED_FILES_READ()
 * @method static PermissionEnum CONTRACTS_CREATE()
 * @method static PermissionEnum CONTRACTS_READ()
 * @method static PermissionEnum CONTRACTS_UPDATE()
 * @method static PermissionEnum CONTRACTS_DELETE()
 * @method static PermissionEnum FILES_READ()
 * @method static PermissionEnum HOOKS_CREATE()
 * @method static PermissionEnum HOOKS_READ()
 * @method static PermissionEnum HOOKS_UPDATE()
 * @method static PermissionEnum HOOKS_DELETE()
 * @method static PermissionEnum SHIPMENTS_CREATE()
 * @method static PermissionEnum SHIPMENTS_READ()
 * @method static PermissionEnum SHIPMENTS_UPDATE()
 * @method static PermissionEnum SHIPMENTS_DELETE()
 * @method static PermissionEnum SHOPS_CREATE()
 * @method static PermissionEnum SHOPS_READ()
 * @method static PermissionEnum SHOPS_UPDATE()
 * @method static PermissionEnum SYSTEM_MESSAGES_CREATE()
 * @method static PermissionEnum SYSTEM_MESSAGES_READ()
 * @method static PermissionEnum SYSTEM_MESSAGES_UPDATE()
 * @method static PermissionEnum SYSTEM_MESSAGES_DELETE()
 * @method static PermissionEnum ORGANIZATIONS_CREATE()
 * @method static PermissionEnum ORGANIZATIONS_READ()
 * @method static PermissionEnum ORGANIZATIONS_UPDATE()
 * @method static PermissionEnum ORGANIZATIONS_ESTIMATED_SHIPMENT_VOLUME_READ()
 * @method static PermissionEnum ORGANIZATIONS_ESTIMATED_SHIPMENT_VOLUME_UPDATE()
 * @method static PermissionEnum ORGANIZATIONS_CHARGE_LABEL_FEES_READ()
 * @method static PermissionEnum ORGANIZATIONS_CHARGE_LABEL_FEES_UPDATE()
 */
class PermissionEnum extends Enum
{
    public const BROKERS_READ = 'brokers:read';

    public const CLIENTS_CREATE = 'clients:create';
    public const CLIENTS_READ = 'clients:read';
    public const CLIENTS_DELETE = 'clients:delete';

    public const COMBINED_FILES_CREATE = 'combined-files:create';
    public const COMBINED_FILES_READ = 'combined-files:read';

    public const CONTRACTS_CREATE = 'contracts:create';
    public const CONTRACTS_READ = 'contracts:read';
    public const CONTRACTS_UPDATE = 'contracts:update';
    public const CONTRACTS_DELETE = 'contracts:delete';

    public const FILES_READ = 'files:read';

    public const HOOKS_CREATE = 'hooks:create';
    public const HOOKS_READ = 'hooks:read';
    public const HOOKS_UPDATE = 'hooks:update';
    public const HOOKS_DELETE = 'hooks:delete';

    public const SHIPMENTS_CREATE = 'shipments:create';
    public const SHIPMENTS_READ = 'shipments:read';
    public const SHIPMENTS_UPDATE = 'shipments:update';
    public const SHIPMENTS_DELETE = 'shipments:delete';

    public const SHOPS_CREATE = 'shops:create';
    public const SHOPS_READ = 'shops:read';
    public const SHOPS_UPDATE = 'shops:update';

    public const SYSTEM_MESSAGES_CREATE = 'system-messages:create';
    public const SYSTEM_MESSAGES_READ = 'system-messages:read';
    public const SYSTEM_MESSAGES_UPDATE = 'system-messages:update';
    public const SYSTEM_MESSAGES_DELETE = 'system-messages:delete';

    public const ORGANIZATIONS_CREATE = 'organizations:create';
    public const ORGANIZATIONS_READ = 'organizations:read';
    public const ORGANIZATIONS_UPDATE = 'organizations:update';

    public const ORGANIZATIONS_ESTIMATED_SHIPMENT_VOLUME_READ = 'organizations.estimated-shipment-volume:read';
    public const ORGANIZATIONS_ESTIMATED_SHIPMENT_VOLUME_UPDATE = 'organizations.estimated-shipment-volume:update';
    public const ORGANIZATIONS_CHARGE_LABEL_FEES_READ = 'organizations.charge-label-fees:read';
    public const ORGANIZATIONS_CHARGE_LABEL_FEES_UPDATE = 'organizations.charge-label-fees:update';
}
