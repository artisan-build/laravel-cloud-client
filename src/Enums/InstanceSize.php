<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * The instance sizes Cloud offers, taken verbatim from the bundled schema at
 * `resources/api-spec/api.json` (`components.schemas.InstanceSize`).
 *
 * The managed-queue sizes appear in TWO id formats, and both are the API's:
 * a legacy hyphenated form (`mq-pro-1gb`, `mq-dedicated-1gb`) and the current
 * dotted form (`mq.flex.512mb`, `mq.dedicated.pro.1gb`). That is not drift on
 * our side to be tidied away — sending an id the API does not list is
 * rejected, so both stay, and `EnumsMatchApiSpecTest` pins them to the schema.
 */
enum InstanceSize: string
{
    case Flex512Mb = 'flex-512mb';
    case Flex1Gb = 'flex-1gb';
    case Flex2Gb = 'flex-2gb';
    case FlexC1Vcpu256Mb = 'flex.c-1vcpu-256mb';
    case FlexG1Vcpu512Mb = 'flex.g-1vcpu-512mb';
    case FlexM1Vcpu1Gb = 'flex.m-1vcpu-1gb';
    case FlexC2Vcpu512Mb = 'flex.c-2vcpu-512mb';
    case FlexG2Vcpu1Gb = 'flex.g-2vcpu-1gb';
    case FlexM2Vcpu2Gb = 'flex.m-2vcpu-2gb';
    case FlexC4Vcpu1Gb = 'flex.c-4vcpu-1gb';
    case FlexG4Vcpu2Gb = 'flex.g-4vcpu-2gb';
    case FlexM4Vcpu4Gb = 'flex.m-4vcpu-4gb';
    case FlexC8Vcpu2Gb = 'flex.c-8vcpu-2gb';
    case FlexG8Vcpu4Gb = 'flex.g-8vcpu-4gb';
    case FlexM8Vcpu8Gb = 'flex.m-8vcpu-8gb';
    case ProC1Vcpu1Gb = 'pro.c-1vcpu-1gb';
    case ProG1Vcpu2Gb = 'pro.g-1vcpu-2gb';
    case ProM1Vcpu4Gb = 'pro.m-1vcpu-4gb';
    case ProC2Vcpu2Gb = 'pro.c-2vcpu-2gb';
    case ProG2Vcpu4Gb = 'pro.g-2vcpu-4gb';
    case ProM2Vcpu8Gb = 'pro.m-2vcpu-8gb';
    case ProC4Vcpu4Gb = 'pro.c-4vcpu-4gb';
    case ProG4Vcpu8Gb = 'pro.g-4vcpu-8gb';
    case ProM4Vcpu16Gb = 'pro.m-4vcpu-16gb';
    case ProC8Vcpu8Gb = 'pro.c-8vcpu-8gb';
    case ProG8Vcpu16Gb = 'pro.g-8vcpu-16gb';
    case ProM8Vcpu32Gb = 'pro.m-8vcpu-32gb';
    case DedicatedC1Vcpu2Gb = 'dedicated.c-1vcpu-2gb';
    case DedicatedG1Vcpu4Gb = 'dedicated.g-1vcpu-4gb';
    case DedicatedM1Vcpu8Gb = 'dedicated.m-1vcpu-8gb';
    case DedicatedC2Vcpu4Gb = 'dedicated.c-2vcpu-4gb';
    case DedicatedG2Vcpu8Gb = 'dedicated.g-2vcpu-8gb';
    case DedicatedM2Vcpu16Gb = 'dedicated.m-2vcpu-16gb';
    case DedicatedC4Vcpu8Gb = 'dedicated.c-4vcpu-8gb';
    case DedicatedG4Vcpu16Gb = 'dedicated.g-4vcpu-16gb';
    case DedicatedM4Vcpu32Gb = 'dedicated.m-4vcpu-32gb';
    case DedicatedC8Vcpu16Gb = 'dedicated.c-8vcpu-16gb';
    case DedicatedG8Vcpu32Gb = 'dedicated.g-8vcpu-32gb';
    case DedicatedM8Vcpu64Gb = 'dedicated.m-8vcpu-64gb';
    case MqPro256Mb = 'mq-pro-256mb';
    case MqPro512Mb = 'mq-pro-512mb';
    case MqPro1Gb = 'mq-pro-1gb';
    case MqPro2Gb = 'mq-pro-2gb';
    case MqPro4Gb = 'mq-pro-4gb';
    case MqPro8Gb = 'mq-pro-8gb';
    case MqDedicated256Mb = 'mq-dedicated-256mb';
    case MqDedicated512Mb = 'mq-dedicated-512mb';
    case MqDedicated1Gb = 'mq-dedicated-1gb';
    case MqDedicated2Gb = 'mq-dedicated-2gb';
    case MqDedicated4Gb = 'mq-dedicated-4gb';
    case MqDedicated8Gb = 'mq-dedicated-8gb';
    case MqDedicated16Gb = 'mq-dedicated-16gb';
    case MqFlex256Mb = 'mq.flex.256mb';
    case MqFlex512Mb = 'mq.flex.512mb';
    case MqFlex1Gb = 'mq.flex.1gb';
    case MqFlex2Gb = 'mq.flex.2gb';
    case MqProDot256Mb = 'mq.pro.256mb';
    case MqProDot512Mb = 'mq.pro.512mb';
    case MqProDot1Gb = 'mq.pro.1gb';
    case MqProDot2Gb = 'mq.pro.2gb';
    case MqProDot4Gb = 'mq.pro.4gb';
    case MqProDot8Gb = 'mq.pro.8gb';
    case MqDedicatedFlex256Mb = 'mq.dedicated.flex.256mb';
    case MqDedicatedFlex512Mb = 'mq.dedicated.flex.512mb';
    case MqDedicatedFlex1Gb = 'mq.dedicated.flex.1gb';
    case MqDedicatedFlex2Gb = 'mq.dedicated.flex.2gb';
    case MqDedicatedPro256Mb = 'mq.dedicated.pro.256mb';
    case MqDedicatedPro512Mb = 'mq.dedicated.pro.512mb';
    case MqDedicatedPro1Gb = 'mq.dedicated.pro.1gb';
    case MqDedicatedPro2Gb = 'mq.dedicated.pro.2gb';
    case MqDedicatedPro4Gb = 'mq.dedicated.pro.4gb';
    case MqDedicatedPro8Gb = 'mq.dedicated.pro.8gb';
    case MqDedicatedPro16Gb = 'mq.dedicated.pro.16gb';

    /**
     * The human label for this size.
     *
     * MANAGED-QUEUE SIZES ONLY produce something better than the raw id:
     * `mq.flex.512mb` reads as "Flex 512 MB". Nothing else here is rendered in
     * a select — the authoring form offers queue sizes and never a general
     * instance size — so for those the id is returned unchanged rather than
     * invented.
     */
    public function label(): string
    {
        return self::labelFor($this->value) ?? $this->value;
    }

    /**
     * Build the label for a managed-queue size id, live or bundled.
     *
     * The API's own `label` on `/instances/sizes` is the CATEGORY ALONE —
     * every `mq.flex.*` entry is labelled "Flex" — so a select built from it
     * shows "Flex, Flex, Flex, Pro, Pro" and the author cannot tell the sizes
     * apart. The size is in the id, and on the live endpoint also in
     * `memory_mib`, so the label is rebuilt here from tier + size instead.
     *
     * Takes the id rather than a case because the whole point of reading the
     * list live is that Cloud offers sizes this enum has never heard of; a
     * size added since the last release must label as well as a bundled one.
     *
     * Returns NULL — not a guess — for an id this cannot read as a
     * managed-queue size, leaving the caller to fall back to whatever the API
     * did send.
     *
     * @param  int|null  $memoryMib  `memory_mib` from the live endpoint, preferred over the id's own size token.
     */
    public static function labelFor(string $name, ?int $memoryMib = null): ?string
    {
        $tokens = preg_split('/[.\-]/', $name) ?: [];

        if (count($tokens) < 3 || $tokens[0] !== 'mq') {
            return null;
        }

        $spelled = self::parseMemory($tokens[count($tokens) - 1]);
        $size = $memoryMib !== null ? self::formatMemory($memoryMib) : $spelled;

        if ($size === null) {
            return null;
        }

        // Everything after `mq` is the tier — one word for `mq.pro.1gb`, two
        // for `mq.dedicated.pro.1gb` — minus the trailing size token, and only
        // if that token really is a size. An id ending in something else keeps
        // it: dropping a token we did not recognise would quietly retitle a
        // size Cloud has just introduced.
        $tier = implode(' ', array_map(ucfirst(...), array_slice($tokens, 1, $spelled === null ? null : -1)));

        return $tier.' '.$size;
    }

    /**
     * `512mb` → `512 MB`, `1gb` → `1 GB`. Null for a token that is not a size,
     * which is how an unrecognised id shape reaches the caller as a null.
     */
    private static function parseMemory(string $token): ?string
    {
        if (preg_match('/^(\d+)(mb|gb|tb)$/i', $token, $matches) !== 1) {
            return null;
        }

        return $matches[1].' '.mb_strtoupper($matches[2]);
    }

    /**
     * Cloud names these sizes in MB/GB (`mq.flex.512mb`) while the endpoint
     * reports MiB, so 1024 MiB is rendered "1 GB" to match the id the author
     * is choosing rather than being pedantically correct and disagreeing
     * with it.
     */
    private static function formatMemory(int $mib): ?string
    {
        if ($mib <= 0) {
            return null;
        }

        if ($mib < 1024) {
            return $mib.' MB';
        }

        $gb = $mib / 1024;

        return ($gb === floor($gb) ? (string) (int) $gb : rtrim(rtrim(number_format($gb, 1, '.', ''), '0'), '.')).' GB';
    }
}
