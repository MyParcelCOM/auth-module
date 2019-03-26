<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ResourceTypeEnum ADDRESSES()
 * @method static ResourceTypeEnum BILLING_LINES()
 * @method static ResourceTypeEnum BROKERS()
 * @method static ResourceTypeEnum CARRIERS()
 * @method static ResourceTypeEnum CONTRACTS()
 * @method static ResourceTypeEnum CLIENTS()
 * @method static ResourceTypeEnum COMBINED_FILES()
 * @method static ResourceTypeEnum CONTACTS()
 * @method static ResourceTypeEnum ENTERPRISES()
 * @method static ResourceTypeEnum FILES()
 * @method static ResourceTypeEnum INVITATIONS()
 * @method static ResourceTypeEnum INVOICES()
 * @method static ResourceTypeEnum LABEL_FEES()
 * @method static ResourceTypeEnum ORGANIZATIONS()
 * @method static ResourceTypeEnum PASSWORD_RESETS()
 * @method static ResourceTypeEnum PAYMENT_IDENTITIES()
 * @method static ResourceTypeEnum PICKUP_DROPOFF_LOCATIONS()
 * @method static ResourceTypeEnum REGIONS()
 * @method static ResourceTypeEnum SERVICE_OPTIONS()
 * @method static ResourceTypeEnum SERVICE_RATES()
 * @method static ResourceTypeEnum SERVICES()
 * @method static ResourceTypeEnum SHIPMENTS()
 * @method static ResourceTypeEnum SHIPMENT_STATUSES()
 * @method static ResourceTypeEnum SHOPS()
 * @method static ResourceTypeEnum STATUSES()
 * @method static ResourceTypeEnum SYSTEM_MESSAGES()
 * @method static ResourceTypeEnum USERS()
 */
class ResourceTypeEnum extends Enum
{
    public const ADDRESSES = 'addresses';
    public const BILLING_LINES = 'billing-lines';
    public const BROKERS = 'brokers';
    public const CARRIERS = 'carriers';
    public const CONTRACTS = 'contracts';
    public const CLIENTS = 'clients';
    public const CONTACTS = 'contacts';
    public const COMBINED_FILES = 'combined-files';
    public const ENTERPRISES = 'enterprises';
    public const FILES = 'files';
    public const HOOKS = 'hooks';
    public const INVITATIONS = 'invitations';
    public const INVOICES = 'invoices';
    public const LABEL_FEES = 'label-fees';
    public const ORGANIZATIONS = 'organizations';
    public const PASSWORD_RESETS = 'password-resets';
    public const PAYMENT_IDENTITIES = 'payment-identities';
    public const PAYMENTS = 'payments';
    public const PICKUP_DROPOFF_LOCATIONS = 'pickup-dropoff-locations';
    public const REGIONS = 'regions';
    public const SERVICE_OPTIONS = 'service-options';
    public const SERVICE_RATES = 'service-rates';
    public const SERVICES = 'services';
    public const SHIPMENTS = 'shipments';
    public const SHIPMENT_STATUSES = 'shipment-statuses';
    public const SHOPS = 'shops';
    public const STATUSES = 'statuses';
    public const SYSTEM_MESSAGES = 'system-messages';
    public const USERS = 'users';
}
