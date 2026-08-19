<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Regions Laravel Cloud can create resources in.
 *
 * Values are taken verbatim from the bundled schema at
 * `resources/api-spec/api.json` (`components.schemas.CloudRegion`), and
 * `EnumsMatchApiSpecTest` fails when the two drift.
 *
 * `GET /meta/regions` is the LIVE list and is what an interface should offer;
 * this enum exists so there is still something to offer when that call cannot
 * be made — an unreachable API, a missing credential, a test suite. It is a
 * fallback and a source of labels, not the authority.
 */
enum Region: string
{
    case UsEast1 = 'us-east-1';
    case UsEast2 = 'us-east-2';
    case CaCentral1 = 'ca-central-1';
    case EuCentral1 = 'eu-central-1';
    case EuWest1 = 'eu-west-1';
    case EuWest2 = 'eu-west-2';
    case MeCentral1 = 'me-central-1';
    case ApSoutheast1 = 'ap-southeast-1';
    case ApSoutheast2 = 'ap-southeast-2';
    case ApNortheast1 = 'ap-northeast-1';

    public function label(): string
    {
        return match ($this) {
            self::UsEast1 => 'N. Virginia (us-east-1)',
            self::UsEast2 => 'Ohio (us-east-2)',
            self::CaCentral1 => 'Canada (ca-central-1)',
            self::EuCentral1 => 'Frankfurt (eu-central-1)',
            self::EuWest1 => 'Ireland (eu-west-1)',
            self::EuWest2 => 'London (eu-west-2)',
            self::MeCentral1 => 'UAE (me-central-1)',
            self::ApSoutheast1 => 'Singapore (ap-southeast-1)',
            self::ApSoutheast2 => 'Sydney (ap-southeast-2)',
            self::ApNortheast1 => 'Tokyo (ap-northeast-1)',
        };
    }
}
