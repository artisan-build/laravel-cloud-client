<?php

declare(strict_types=1);

/**
 * @return array<string, string>
 */
function responseFixtureSchemas(): array
{
    return [
        'Applications/create.json' => 'ApplicationResource',
        'Applications/list.json' => 'ApplicationResource',
        'Applications/show.json' => 'ApplicationResource',
        'Applications/update.json' => 'ApplicationResource',
        'BackgroundProcesses/list.json' => 'BackgroundProcessResource',
        'BackgroundProcesses/show.json' => 'BackgroundProcessResource',
        'Caches/list.json' => 'CacheResource',
        'Caches/show.json' => 'CacheResource',
        'Commands/list.json' => 'CommandResource',
        'Commands/show.json' => 'CommandResource',
        'DatabaseClusters/databases.json' => 'DatabaseSchemaResource',
        'DatabaseClusters/list.json' => 'DatabaseResource',
        'DatabaseClusters/show.json' => 'DatabaseResource',
        // Both domain operations return data.type="domains". The fixture path is the operation evidence:
        // environment domain listing returns SimplifiedDomainResource, direct domain lookup returns DomainResource.
        'Domains/list.json' => 'SimplifiedDomainResource',
        'Domains/show.json' => 'DomainResource',
        'Deployments/list.json' => 'DeploymentResource',
        'Deployments/show.json' => 'DeploymentResource',
        'Deployments/trigger.json' => 'DeploymentResource',
        'Environments/list.json' => 'EnvironmentResource',
        'Environments/show.json' => 'EnvironmentResource',
        'Environments/show-with-resources.json' => 'EnvironmentResource',
        'Instances/list.json' => 'InstanceResource',
        'Instances/show.json' => 'InstanceResource',
        'Meta/organization.json' => 'OrganizationResource',
        'ObjectStorage/access-keys.json' => 'FilesystemKeyResource',
        'ObjectStorage/create-access-key.json' => 'FilesystemKeyResource',
        'ObjectStorage/list.json' => 'FilesystemResource',
        'ObjectStorage/show.json' => 'FilesystemResource',
    ];
}

/**
 * @return list<string>
 */
function flatResponseFixtures(): array
{
    return [
        // Legacy command output payload, not a documented JSON:API resource response.
        'Commands/output.json',
        // Undocumented database-user compatibility surface, not a documented JSON:API resource response.
        'DatabaseClusters/users.json',
        // Plain deployment log payload, not a JSON:API resource response.
        'Deployments/logs.json',
        // Undocumented SSL status compatibility surface, not a documented JSON:API resource response.
        'Domains/ssl-status.json',
        // Undocumented environment-variable compatibility surface, not a documented JSON:API resource response.
        'Environments/variables.json',
        // Error payloads use Laravel's error shape, not JSON:API resource objects.
        'Errors/401.json',
        'Errors/404.json',
        'Errors/422.json',
        'Errors/429.json',
        'Errors/500.json',
        // Published regions endpoint is plain application/json, not JSON:API.
        'Meta/regions.json',
    ];
}

/**
 * @return array<string, mixed>
 */
function laravelCloudApiSpec(): array
{
    $path = __DIR__.'/../../resources/api-spec/api.json';

    expect(file_exists($path))->toBeTrue("Missing vendored Laravel Cloud OpenAPI document at {$path}.");

    /** @var array<string, mixed> $spec */
    $spec = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

    expect(data_get($spec, 'components.schemas'))->toBeArray('Vendored Laravel Cloud OpenAPI document is missing components.schemas.');

    return $spec;
}

/**
 * @return list<string>
 */
function discoveredResponseFixtures(): array
{
    $root = __DIR__.'/../Fixtures/Responses';
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'json') {
            continue;
        }

        $files[] = substr($file->getPathname(), strlen($root) + 1);
    }

    sort($files);

    return $files;
}

/**
 * @return array<string, array{0: string, 1: int, 2: mixed, 3: mixed, 4: string}>
 */
function jsonApiResponseFixtureObjects(): array
{
    $root = __DIR__.'/../Fixtures/Responses';
    $schemas = responseFixtureSchemas();
    $flatFixtures = flatResponseFixtures();
    $cases = [];

    foreach (discoveredResponseFixtures() as $fixture) {
        if (in_array($fixture, $flatFixtures, true)) {
            continue;
        }

        $raw = (string) file_get_contents($root.'/'.$fixture);

        /** @var array<string, mixed> $payload */
        $payload = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        $jsonPayload = json_decode($raw, false, flags: JSON_THROW_ON_ERROR);

        if (! array_key_exists('data', $payload)) {
            continue;
        }

        $data = $payload['data'];
        $resources = is_array($data) && array_is_list($data) ? $data : [$data];
        $jsonData = is_object($jsonPayload) && property_exists($jsonPayload, 'data') ? $jsonPayload->data : null;
        $jsonResources = is_array($jsonData) ? $jsonData : [$jsonData];

        foreach ($resources as $index => $resource) {
            $cases["{$fixture} [{$index}]"] = [$fixture, $index, $resource, $jsonResources[$index] ?? null, $schemas[$fixture] ?? ''];
        }
    }

    return $cases;
}

/**
 * @return array<string, array{0: string}>
 */
function mappedResponseFixtures(): array
{
    return array_map(
        fn (string $fixture): array => [$fixture],
        array_combine(array_keys(responseFixtureSchemas()), array_keys(responseFixtureSchemas())),
    );
}

/**
 * @param  array<string, mixed>  $schemas
 * @return array<string, mixed>
 */
function resourceSchema(array $schemas, string $schemaName): array
{
    /** @var array<string, mixed> $schema */
    $schema = $schemas[$schemaName] ?? [];

    if ($schemaName !== 'EnvironmentResource') {
        return normalizeEmptyObjectPropertyDefects($schema);
    }

    /** @var array<string, mixed> $attributes */
    $attributes = data_get($schema, 'properties.attributes.properties', []);

    if (array_key_exists('', $attributes)) {
        // Published-spec defect: Laravel emits an empty attribute key/null required entry here.
        // The real optional response attribute is environment_variables; validate it against the inner value schema.
        $schema['properties']['attributes']['properties']['environment_variables'] = data_get($attributes[''], 'anyOf.0.properties.environment_variables', []);
        unset($schema['properties']['attributes']['properties']['']);
    }

    return normalizeEmptyObjectPropertyDefects($schema);
}

/**
 * @param  array<string, mixed>  $schema
 * @return array<string, mixed>
 */
function normalizeEmptyObjectPropertyDefects(array $schema): array
{
    if (! isset($schema['properties']) || ! is_array($schema['properties'])) {
        return $schema;
    }

    foreach ($schema['properties'] as $name => $propertySchema) {
        if (is_array($propertySchema)) {
            $schema['properties'][$name] = normalizeEmptyObjectPropertyDefects($propertySchema);
        }
    }

    if (! array_key_exists('', $schema['properties'])) {
        return $schema;
    }

    $innerProperties = data_get($schema['properties'][''], 'anyOf.0.properties', []);

    if (is_array($innerProperties)) {
        foreach ($innerProperties as $name => $innerSchema) {
            $schema['properties'][$name] = $innerSchema;
        }
    }

    unset($schema['properties']['']);

    return $schema;
}

/**
 * @param  array<string, mixed>  $schemas
 * @return array<string, mixed>
 */
function resourceAttributeProperties(array $schemas, string $schemaName): array
{
    /** @var array<string, mixed> $properties */
    $properties = data_get(resourceSchema($schemas, $schemaName), 'properties.attributes.properties', []);

    return $properties;
}

/**
 * @param  array<string, mixed>  $schemas
 * @return list<string>
 */
function resourceRequiredAttributes(array $schemas, string $schemaName): array
{
    /** @var list<string|null> $required */
    $required = data_get(resourceSchema($schemas, $schemaName), 'properties.attributes.required', []);

    return array_values(array_filter(
        $required,
        fn (?string $field): bool => $field !== null && $field !== '',
    ));
}

/**
 * @param  array<string, mixed>  $schemas
 * @param  array<string, mixed>  $schema
 * @return array<string, mixed>
 */
function resolvedSchema(array $schemas, array $schema): array
{
    $ref = $schema['$ref'] ?? null;

    if (! is_string($ref)) {
        return $schema;
    }

    /** @var array<string, mixed> $resolved */
    $resolved = data_get($schemas, basename(str_replace('/', DIRECTORY_SEPARATOR, $ref)), []);

    return $resolved;
}

/**
 * Enforces reachable JSON Schema contracts used by the vendored Laravel Cloud response schemas: $ref,
 * anyOf/oneOf/allOf, required object properties, documented object properties, array items, primitive
 * types, enum values, and date-time/uri formats. It intentionally does not implement numeric bounds,
 * minItems/maxItems, formats other than date-time/uri, or true additionalProperties semantics; instead,
 * it rejects keys absent from properties, which is stricter and safe for these fixtures.
 *
 * @param  array<string, mixed>  $schemas
 * @param  array<string, mixed>  $schema
 * @return list<string>
 */
function validateSchemaValue(array $schemas, array $schema, mixed $value, string $path, mixed $jsonValue = null): array
{
    $schema = resolvedSchema($schemas, $schema);
    $errors = [];

    foreach (['anyOf', 'oneOf'] as $keyword) {
        if (! isset($schema[$keyword]) || ! is_array($schema[$keyword])) {
            continue;
        }

        $matches = 0;
        $branchErrors = [];

        foreach ($schema[$keyword] as $candidate) {
            if (! is_array($candidate)) {
                continue;
            }

            $candidateErrors = validateSchemaValue($schemas, $candidate, $value, $path, $jsonValue);
            $branchErrors[] = $candidateErrors;

            if ($candidateErrors === []) {
                $matches++;
            }
        }

        if ($keyword === 'anyOf' && $matches > 0) {
            return [];
        }

        if ($keyword === 'oneOf' && $matches === 1) {
            return [];
        }

        $errors[] = "{$path} does not satisfy {$keyword}; branch errors: ".json_encode($branchErrors, JSON_THROW_ON_ERROR);

        return $errors;
    }

    if (isset($schema['allOf']) && is_array($schema['allOf'])) {
        foreach ($schema['allOf'] as $candidate) {
            if (is_array($candidate)) {
                $errors = [...$errors, ...validateSchemaValue($schemas, $candidate, $value, $path, $jsonValue)];
            }
        }
    }

    $enum = $schema['enum'] ?? null;

    if (is_array($enum) && $value !== null && ! in_array($value, $enum, true)) {
        $errors[] = "{$path} is not a documented enum member.";
    }

    $format = $schema['format'] ?? null;

    if (is_string($format) && is_string($value)) {
        $formatError = validateReachableFormat($value, $format, $path);

        if ($formatError !== null) {
            $errors[] = $formatError;
        }
    }

    $type = $schema['type'] ?? null;

    if (is_string($type) || is_array($type)) {
        $types = is_array($type) ? $type : [$type];

        if (! valueMatchesJsonSchemaType($value, $types, $jsonValue)) {
            $errors[] = "{$path} does not match documented type ".json_encode($types, JSON_THROW_ON_ERROR).'.';

            return $errors;
        }
    }

    if (($type === 'object' || isset($schema['properties'])) && is_array($value) && valueIsJsonObject($value, $jsonValue)) {
        /** @var array<string, mixed> $properties */
        $properties = is_array($schema['properties'] ?? null) ? $schema['properties'] : [];
        /** @var list<string|null> $required */
        $required = is_array($schema['required'] ?? null) ? $schema['required'] : [];

        foreach ($required as $field) {
            if ($field === null || $field === '') {
                continue;
            }

            if (! array_key_exists($field, $value)) {
                $errors[] = "{$path}.{$field} is missing required property.";
            }
        }

        foreach ($properties as $field => $propertySchema) {
            if (! array_key_exists($field, $value) || ! is_array($propertySchema)) {
                continue;
            }

            $errors = [...$errors, ...validateSchemaValue($schemas, $propertySchema, $value[$field], "{$path}.{$field}", jsonObjectProperty($jsonValue, $field))];
        }

        foreach (array_keys($value) as $field) {
            if (! array_key_exists($field, $properties)) {
                $errors[] = "{$path}.{$field} is not documented by the schema.";
            }
        }
    }

    if (($type === 'array' || isset($schema['items'])) && is_array($value) && valueIsJsonArray($value, $jsonValue) && is_array($schema['items'] ?? null)) {
        foreach ($value as $index => $item) {
            /** @var array<string, mixed> $items */
            $items = $schema['items'];
            $errors = [...$errors, ...validateSchemaValue($schemas, $items, $item, "{$path}[{$index}]", jsonArrayItem($jsonValue, $index))];
        }
    }

    return $errors;
}

function jsonObjectProperty(mixed $jsonValue, string|int $field): mixed
{
    if (is_object($jsonValue) && property_exists($jsonValue, (string) $field)) {
        return $jsonValue->{$field};
    }

    return null;
}

function jsonArrayItem(mixed $jsonValue, int $index): mixed
{
    return is_array($jsonValue) ? ($jsonValue[$index] ?? null) : null;
}

function validateReachableFormat(string $value, string $format, string $path): ?string
{
    if ($format === 'date-time') {
        return isValidRfc3339DateTime($value) ? null : "{$path} is not a documented RFC 3339 date-time.";
    }

    if ($format === 'uri') {
        return filter_var($value, FILTER_VALIDATE_URL) !== false
            ? null
            : "{$path} is not a documented URI.";
    }

    return null;
}

function isValidRfc3339DateTime(string $value): bool
{
    if (! preg_match('/^(?<date>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})(?:\.(?<fraction>\d+))?(?<offset>Z|[+-]\d{2}:\d{2})$/', $value, $matches)) {
        return false;
    }

    $parseValue = $matches['date'].$matches['offset'];
    $parseFormat = '!Y-m-d\TH:i:s'.($matches['offset'] === 'Z' ? '\Z' : 'P');

    $date = DateTimeImmutable::createFromFormat($parseFormat, $parseValue);
    $errors = DateTimeImmutable::getLastErrors();

    if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        return false;
    }

    $roundTrip = $date->format('Y-m-d\TH:i:s'.($matches['offset'] === 'Z' ? '\Z' : 'P'));

    if (isset($matches['fraction']) && $matches['fraction'] !== '') {
        $roundTrip = substr_replace($roundTrip, '.'.$matches['fraction'], 19, 0);
    }

    return $roundTrip === $value;
}

function valueIsJsonArray(mixed $value, mixed $jsonValue = null): bool
{
    return is_array($value) && array_is_list($value) && ($jsonValue === null || is_array($jsonValue));
}

function valueIsJsonObject(mixed $value, mixed $jsonValue = null): bool
{
    return is_array($value) && ($jsonValue === null ? ! array_is_list($value) : is_object($jsonValue));
}

/**
 * @param  list<mixed>  $types
 */
function valueMatchesJsonSchemaType(mixed $value, array $types, mixed $jsonValue = null): bool
{
    foreach ($types as $type) {
        if ($type === 'null' && $value === null) {
            return true;
        }

        if ($type === 'string' && is_string($value)) {
            return true;
        }

        if ($type === 'integer' && is_int($value)) {
            return true;
        }

        if ($type === 'number' && (is_int($value) || is_float($value))) {
            return true;
        }

        if ($type === 'boolean' && is_bool($value)) {
            return true;
        }

        if ($type === 'array' && valueIsJsonArray($value, $jsonValue)) {
            return true;
        }

        if ($type === 'object' && valueIsJsonObject($value, $jsonValue)) {
            return true;
        }
    }

    return false;
}

it('keeps every JSON API response fixture object conformant with the vendored OpenAPI spec', function (string $fixture, int $index, mixed $resource, mixed $jsonResource, string $schemaName): void {
    $spec = laravelCloudApiSpec();
    /** @var array<string, mixed> $schemas */
    $schemas = data_get($spec, 'components.schemas');

    expect($schemaName)->not->toBe('', "No explicit OpenAPI schema mapping exists for {$fixture}.");
    expect(array_key_exists($schemaName, $schemas))->toBeTrue("OpenAPI schema {$schemaName} mapped for {$fixture} does not exist.");
    expect($resource)->toBeArray("{$fixture} [{$index}] must be a JSON:API resource object.");

    /** @var array<string, mixed> $resource */
    $schema = resourceSchema($schemas, $schemaName);

    $documentedType = data_get($schema, 'properties.type.enum.0');

    expect($documentedType)->toBeString("OpenAPI schema {$schemaName} does not document a JSON:API data.type.");
    expect($resource['type'] ?? null)->toBe($documentedType, "{$fixture} [{$index}] data.type does not match {$schemaName}.");
    expect($resource['attributes'] ?? null)->toBeArray("{$fixture} [{$index}] must contain a JSON:API attributes object.");
    expect(validateSchemaValue($schemas, $schema, $resource, "{$fixture} [{$index}] {$schemaName}", $jsonResource))->toBe([]);

    /** @var array<string, mixed> $attributes */
    $attributes = $resource['attributes'];
    $documentedAttributes = resourceAttributeProperties($schemas, $schemaName);

    foreach (resourceRequiredAttributes($schemas, $schemaName) as $field) {
        expect(array_key_exists($field, $attributes))->toBeTrue("{$fixture} [{$index}] {$schemaName} is missing required attribute {$field}.");
    }

    foreach (array_keys($attributes) as $field) {
        expect(array_key_exists($field, $documentedAttributes))->toBeTrue("{$fixture} [{$index}] {$schemaName} has undocumented attribute {$field}.");
    }

    foreach ($attributes as $field => $value) {
        $attributeSchema = $documentedAttributes[$field] ?? null;

        if (! is_array($attributeSchema)) {
            continue;
        }

        expect(validateSchemaValue($schemas, $attributeSchema, $value, "{$fixture} [{$index}] {$schemaName}.{$field}", jsonObjectProperty(jsonObjectProperty($jsonResource, 'attributes'), $field)))->toBe([]);
    }
})->with(jsonApiResponseFixtureObjects());

it('keeps every mapped response fixture structurally present', function (string $fixture): void {
    $root = __DIR__.'/../Fixtures/Responses';

    /** @var array<string, mixed> $payload */
    $payload = json_decode((string) file_get_contents($root.'/'.$fixture), true, flags: JSON_THROW_ON_ERROR);

    expect(array_key_exists('data', $payload))->toBeTrue("Mapped response fixture {$fixture} must contain a data member.");

    $data = $payload['data'];
    $resources = is_array($data) && array_is_list($data) ? $data : [$data];
    $resourceObjects = array_filter($resources, fn (mixed $resource): bool => is_array($resource));

    if (is_array($data) && array_is_list($data)) {
        foreach ($data as $index => $resource) {
            expect($resource)->toBeArray("Mapped response fixture {$fixture} data[{$index}] must be a JSON:API resource object.");
        }
    }

    expect($resourceObjects)->not->toBe([], "Mapped response fixture {$fixture} must yield at least one JSON:API resource object.");
})->with(mappedResponseFixtures());

it('keeps flat response fixture exemptions from hiding JSON API resources', function (): void {
    $root = __DIR__.'/../Fixtures/Responses';

    foreach (flatResponseFixtures() as $fixture) {
        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) file_get_contents($root.'/'.$fixture), true, flags: JSON_THROW_ON_ERROR);
        $data = $payload['data'] ?? null;
        $items = is_array($data) && array_is_list($data) ? $data : [$data];

        foreach ($items as $index => $item) {
            $isJsonApiResource = is_array($item) && array_key_exists('type', $item) && array_key_exists('attributes', $item);

            expect($isJsonApiResource)->toBeFalse("Flat response fixture {$fixture} data[{$index}] looks like a JSON:API resource and must be schema-mapped instead of exempted.");
        }
    }
});

it('keeps every response fixture explicitly classified', function (): void {
    $schemaFixtures = array_keys(responseFixtureSchemas());
    $flatFixtures = flatResponseFixtures();
    $classified = [...$schemaFixtures, ...$flatFixtures];
    $discovered = discoveredResponseFixtures();

    sort($schemaFixtures);
    sort($flatFixtures);
    sort($classified);

    expect(array_intersect($schemaFixtures, $flatFixtures))->toBe([], 'Response fixture schema mappings and flat exemptions must be disjoint.');
    expect($classified)->toBe($discovered, 'Every discovered response fixture must be mapped to a schema or explicitly exempted as flat, and every classified fixture must exist.');
});
