# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-01-21

### Added

- Initial release
- Complete API coverage for Laravel Cloud:
  - Applications (5 endpoints)
  - Environments (10 endpoints)
  - Deployments (5 endpoints)
  - Instances (6 endpoints)
  - Domains (7 endpoints)
  - Database Clusters (11 endpoints)
  - Background Processes (6 endpoints)
  - Commands (5 endpoints)
  - Object Storage (8 endpoints)
  - Caches (6 endpoints)
- Type-safe enums for DatabaseType, InstanceType, DeploymentStatus, EnvironmentType, PhpVersion
- Exception hierarchy with proper error handling
- Laravel service provider with auto-discovery
- Facade for convenient access
- 7 Artisan commands for CLI usage
- Automatic retry logic for rate limits
- Comprehensive test suite with 90%+ coverage
- PHPStan level 9 compliance
