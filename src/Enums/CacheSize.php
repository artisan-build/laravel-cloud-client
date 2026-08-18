<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum CacheSize: string
{
    case Upstash250Mb = '250mb';
    case Upstash1Gb = '1gb';
    case Upstash2Point5Gb = '2.5gb';
    case Upstash5Gb = '5gb';
    case Upstash12Gb = '12gb';
    case Upstash50Gb = '50gb';
    case Upstash100Gb = '100gb';
    case Upstash500Gb = '500gb';
    case ValkeyFlex250Mb = 'valkey-flex-250mb';
    case ValkeyFlex1Gb = 'valkey-flex-1gb';
    case ValkeyFlex2Point5Gb = 'valkey-flex-2.5gb';
    case ValkeyPro250Mb = 'valkey-pro.250mb';
    case ValkeyPro1Gb = 'valkey-pro.1gb';
    case ValkeyPro2Point5Gb = 'valkey-pro.2.5gb';
    case ValkeyPro5Gb = 'valkey-pro.5gb';
    case ValkeyPro12Gb = 'valkey-pro.12gb';
    case ValkeyPro25Gb = 'valkey-pro.25gb';
    case ValkeyPro50Gb = 'valkey-pro.50gb';
    case ElastiCacheR7GLarge = 'elasticache.cache.r7g.large';
    case ElastiCacheR7GXlarge = 'elasticache.cache.r7g.xlarge';
    case ElastiCacheR7G2Xlarge = 'elasticache.cache.r7g.2xlarge';
    case ElastiCacheR7G4Xlarge = 'elasticache.cache.r7g.4xlarge';
    case ElastiCacheR7G8Xlarge = 'elasticache.cache.r7g.8xlarge';
    case ElastiCacheR7G12Xlarge = 'elasticache.cache.r7g.12xlarge';
    case ElastiCacheR7G16Xlarge = 'elasticache.cache.r7g.16xlarge';
    case ElastiCacheC7GnLarge = 'elasticache.cache.c7gn.large';
    case ElastiCacheC7GnXlarge = 'elasticache.cache.c7gn.xlarge';
    case ElastiCacheC7Gn2Xlarge = 'elasticache.cache.c7gn.2xlarge';
    case ElastiCacheC7Gn4Xlarge = 'elasticache.cache.c7gn.4xlarge';
    case ElastiCacheC7Gn8Xlarge = 'elasticache.cache.c7gn.8xlarge';
    case ElastiCacheC7Gn12Xlarge = 'elasticache.cache.c7gn.12xlarge';
    case ElastiCacheC7Gn16Xlarge = 'elasticache.cache.c7gn.16xlarge';
    case ElastiCacheT4GMicro = 'elasticache.cache.t4g.micro';
    case ElastiCacheT4GSmall = 'elasticache.cache.t4g.small';
    case ElastiCacheT4GMedium = 'elasticache.cache.t4g.medium';
    case ElastiCacheM7GLarge = 'elasticache.cache.m7g.large';
    case ElastiCacheM7GXlarge = 'elasticache.cache.m7g.xlarge';
    case ElastiCacheM7G2Xlarge = 'elasticache.cache.m7g.2xlarge';
    case ElastiCacheM7G4Xlarge = 'elasticache.cache.m7g.4xlarge';
    case ElastiCacheM7G8Xlarge = 'elasticache.cache.m7g.8xlarge';
    case ElastiCacheM7G12Xlarge = 'elasticache.cache.m7g.12xlarge';
    case ElastiCacheM7G16Xlarge = 'elasticache.cache.m7g.16xlarge';
    case ElastiCacheM8GLarge = 'elasticache.cache.m8g.large';
    case ElastiCacheM8GXlarge = 'elasticache.cache.m8g.xlarge';
    case ElastiCacheM8G2Xlarge = 'elasticache.cache.m8g.2xlarge';
    case ElastiCacheM8G4Xlarge = 'elasticache.cache.m8g.4xlarge';
    case ElastiCacheM8G8Xlarge = 'elasticache.cache.m8g.8xlarge';
    case ElastiCacheM8G12Xlarge = 'elasticache.cache.m8g.12xlarge';
    case ElastiCacheM8G16Xlarge = 'elasticache.cache.m8g.16xlarge';
    case ElastiCacheR8GLarge = 'elasticache.cache.r8g.large';
    case ElastiCacheR8GXlarge = 'elasticache.cache.r8g.xlarge';
    case ElastiCacheR8G2Xlarge = 'elasticache.cache.r8g.2xlarge';
    case ElastiCacheR8G4Xlarge = 'elasticache.cache.r8g.4xlarge';
    case ElastiCacheR8G8Xlarge = 'elasticache.cache.r8g.8xlarge';
    case ElastiCacheR8G12Xlarge = 'elasticache.cache.r8g.12xlarge';
    case ElastiCacheR8G16Xlarge = 'elasticache.cache.r8g.16xlarge';
    case ElastiCacheC8GnLarge = 'elasticache.cache.c8gn.large';
    case ElastiCacheC8GnXlarge = 'elasticache.cache.c8gn.xlarge';
    case ElastiCacheC8Gn2Xlarge = 'elasticache.cache.c8gn.2xlarge';
    case ElastiCacheC8Gn4Xlarge = 'elasticache.cache.c8gn.4xlarge';
    case ElastiCacheC8Gn8Xlarge = 'elasticache.cache.c8gn.8xlarge';
    case ElastiCacheC8Gn12Xlarge = 'elasticache.cache.c8gn.12xlarge';
    case ElastiCacheC8Gn16Xlarge = 'elasticache.cache.c8gn.16xlarge';
}
