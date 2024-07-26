# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0-beta]

### Changed

- Remove using the order billing address and rely on Print.com configured billing address. Customers will be required to have set up a billing address in their Print.com account.

## [1.1.0-beta]

### Added

- Add pdc-request-source to place order headers of API requests to Print.com
- Use WooCommerce order invoice address as billing address when placing the order to Print.com

### Changed

- Added possibility to also do PUT and PATCH requests to Print.com
- Added possibility to add custom headers to Print.com API requests

## [1.0.0-beta]

Beta version of the plugin. Breaking changes will not be communicated in advance as this will slow down development.
