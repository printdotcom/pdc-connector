# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).



## [2.0.1-beta]

### Added

- Search products on sku

### Changed

- Removed unused public.css

## [2.0.0-beta]

### Changed

- Prevent initializing the autocompletions when products or presets are not available.
- Removed token based authentication.
- Removed option keys pw and user.
- Removed base_url option key, replaced by env option key.
- Removed input field from product page
- Mark variation as dirty when pdf has been uploaded
- Removed hidden input for pdf url

### Added

- API Key support.


## [1.3.0-beta]

### Changed
- Rename meta keys
    - pdf_url
    - product_sku
    - product_title
    - preset_id
    - preset_title
    - order_item_number
    - order_item_grand_total
    - purchase_date
    - image_url
    - order_item_status
    - order_item_tnt_url
- Resolved issue with saving variants

### Added
- Centralized formatting for meta keys. Everything will now be prefixed with _ to mark it as private and have a plug-in prefix name.

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
