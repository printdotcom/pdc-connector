# Print.com Print on Demand - WordPress Plugin

[![WordPress](https://img.shields.io/badge/WordPress-6.0+-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-8.5+-purple.svg)](https://woocommerce.com/)
[![PHP](https://img.shields.io/badge/PHP-7.0+-777BB4.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0+-red.svg)](LICENSE.txt)

A WordPress plugin that integrates with the Print.com API to enable custom print-on-demand products in WooCommerce stores. Configure, edit, and sell custom printed products seamlessly through your WordPress/WooCommerce site.

## ✨ Features

- 🖨️ **Print.com API Integration** - Direct connection to Print.com's print-on-demand services
- 🛒 **WooCommerce Integration** - Seamless product management within WooCommerce
- 🎨 **Custom Product Configuration** - Allow customers to customize products before purchase
- 📦 **Order Management** - Track and manage print orders from WordPress admin
- 🔧 **Easy Setup** - Docker-based development environment with automated seeding
- 🚀 **Production Ready** - Built with WordPress coding standards and best practices

## 🚀 Quick Start

### Prerequisites

- [Docker](https://www.docker.com/get-started) and Docker Compose
- [Git](https://git-scm.com/downloads)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/printdotcom/pdc-connector.git
   cd pdc-connector
   ```

2. **Start the development environment:**
   ```bash
   # Start WordPress with PHP 8.2 and WordPress 6.7
   bin/run-wordpress 67 82
   ```

3. **Seed WooCommerce with sample data:**
   ```bash
   # Configure WooCommerce and create sample products
   bin/seed-woocommerce 67 82
   ```

4. **Access your site:**
   - **Frontend:** http://localhost:8067
   - **Admin:** http://localhost:8067/wp-admin (admin/password)
   - **Shop:** http://localhost:8067/shop

## 🐳 Local Environment

### Available Commands

| Command | Description | Example |
|---------|-------------|----------|
| `bin/run-wordpress [WP_VER] [PHP_VER]` | Start WordPress environment | `bin/run-wordpress 67 82` |
| `bin/stop-wordpress [WP_VER] [PHP_VER]` | Stop WordPress environment | `bin/stop-wordpress 67 82` |
| `bin/seed-woocommerce [WP_VER] [PHP_VER]` | Seed WooCommerce with sample data | `bin/seed-woocommerce 67 82` |
| `bin/run-mock-api {start\|stop\|status}` | Manage Print.com Mock API | `bin/run-mock-api start` |

### Supported Versions

Check `config/wp-version.conf` for available WordPress/PHP combinations:

```bash
# WordPress 6.7 with PHP 7.0
bin/run-wordpress 67 70

# WordPress 6.8 with PHP 8.2 (if available)
bin/run-wordpress 68 82
```

### Port Mapping

The port format is `80[WORDPRESS_VERSION]`:
- WordPress 6.7: `http://localhost:8067`
- WordPress 6.8: `http://localhost:8068`

## 🛍️ Sample Products

The seed script creates these sample products:

| Product | SKU | Price | Category |
|---------|-----|-------|-----------|
| Custom T-Shirt - Basic | PDC-TSHIRT-001 | $29.99 | T-Shirts |
| Custom T-Shirt - Premium | PDC-TSHIRT-002 | $39.99 | T-Shirts |
| Custom Poster - A3 | PDC-POSTER-001 | $19.99 | Posters |
| Custom Poster - A2 | PDC-POSTER-002 | $34.99 | Posters |
| Custom Mug - Ceramic | PDC-MUG-001 | $14.99 | Print Products |

## 🏗️ Project Structure

```
pdc-connector/
├── admin/                  # Admin-specific functionality
│   ├── AdminCore.php      # Main admin class
│   ├── PrintDotCom/       # Print.com API integration
│   │   ├── APIClient.php  # API client
│   │   ├── Product.php    # Product model
│   │   └── Preset.php     # Preset model
│   ├── css/               # Admin stylesheets
│   ├── js/                # Admin JavaScript
│   └── partials/          # Admin template partials
├── bin/                   # Development scripts
│   ├── run-wordpress      # Start WordPress environment
│   ├── stop-wordpress     # Stop WordPress environment
│   └── seed-woocommerce   # Seed WooCommerce data
├── config/                # Configuration files
│   ├── docker-compose.yml # Docker Compose configuration
│   └── wp-version.conf    # WordPress/PHP version mapping
├── includes/              # Core plugin files
│   ├── Core.php          # Main plugin class
│   ├── Activator.php     # Plugin activation
│   ├── Deactivator.php   # Plugin deactivation
│   └── Loader.php        # Hook loader
├── front/                 # Public-facing functionality
│   └── FrontCore.php      # Main public class
└── vendor/                # Composer dependencies
```

## 🔧 Configuration

### Print.com API Credentials

1. Navigate to **WordPress Admin → PDC Connector → Settings**
2. Enter your Print.com API credentials:
   - API Key
   - API Secret
   - Environment (Sandbox/Production)

### WooCommerce Settings

The plugin automatically configures WooCommerce with:
- Store address and currency (USD)
- Guest checkout enabled
- User registration options
- Required WooCommerce pages (Shop, Cart, Checkout, My Account)

## 🛠️ Development

### Code Standards

This project follows:
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress Documentation Standards](https://developer.wordpress.org/coding-standards/inline-documentation-standards/)
- [Conventional Commits](https://www.conventionalcommits.org/) for commit messages

### Autoloading

The project uses PSR-4 autoloading via Composer:

```json
{
  "autoload": {
    "psr-4": {
      "PdcConnector\\Admin\\": "admin/",
      "PdcConnector\\Front\\": "front/",
      "PdcConnector\\Includes\\": "includes/"
    }
  }
}
```

### Adding New Features

1. **Create feature branch:**
   ```bash
   git checkout -b feat/new-feature
   ```

2. **Follow namespace conventions:**
   ```php
   <?php
   namespace PdcConnector\Admin\PrintDotCom;
   
   class NewFeature {
       // Implementation
   }
   ```

3. **Add proper DocBlocks:**
   ```php
   /**
    * Brief description of the method.
    *
    * Longer description if needed.
    *
    * @since 1.0.0
    * @param string $param Description of parameter.
    * @return bool Description of return value.
    */
   public function methodName($param) {
       // Implementation
   }
   ```

## 🧪 Testing

### Manual Testing

1. **Start environment and seed data:**
   ```bash
   bin/run-wordpress 67 82
   bin/seed-woocommerce 67 82
   ```

2. **Test WooCommerce integration:**
   - Visit shop page: http://localhost:8067/shop
   - Add products to cart
   - Test checkout process

3. **Test admin functionality:**
   - Visit admin: http://localhost:8067/wp-admin
   - Check PDC Connector settings
   - Verify product configurations

### Mock API Testing

Test API integration without real Print.com credentials:

```bash
# Start the mock API
bin/run-mock-api start

# Test API endpoints
curl -H "X-API-Key: test_key_12345" http://localhost:8001/products

# Configure plugin to use mock API
# Set API base URL to: http://localhost:8001
# Use API key: test_key_12345
```

**Mock API Features:**
- 🎯 Based on actual Print.com API documentation
- 🔑 Realistic authentication with test API keys
- 📊 Dynamic responses with templating
- 🛠️ Admin interface at http://localhost:8001/__admin

See [`test/wiremock/README.md`](test/wiremock/README.md) for detailed documentation.

## 📝 API Documentation

### Print.com API Client

```php
use PdcConnector\Admin\PrintDotCom\APIClient;

$client = new APIClient();
$products = $client->getProducts();
```

### Product Model

```php
use PdcConnector\Admin\PrintDotCom\Product;

$product = new Product('PDC-TSHIRT-001', 'Custom T-Shirt');
echo $product->sku;   // PDC-TSHIRT-001
echo $product->title; // Custom T-Shirt
```

## 🚀 Deployment

### Production Checklist

- [ ] Update version number in `pdc-connector.php`
- [ ] Test with production Print.com API credentials
- [ ] Verify WooCommerce compatibility
- [ ] Test on target WordPress/PHP versions
- [ ] Create deployment package (exclude development files)

### Creating Release Package

```bash
# Or use .distignore file for WordPress.org
wp dist-archive .
```

## 📄 License

This project is licensed under the GPL-2.0+ License - see the [LICENSE.txt](LICENSE.txt) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feat/amazing-feature`)
3. Commit your changes using [Conventional Commits](https://www.conventionalcommits.org/)
4. Push to the branch (`git push origin feat/amazing-feature`)
5. Open a Pull Request

## 📞 Support

- **Documentation:** [Print.com Developer Docs](https://developer.print.com)
- **Issues:** [GitHub Issues](https://github.com/printdotcom/pdc-connector/issues)
- **Email:** [devops@print.com](mailto:devops@print.com)

## 📊 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed history of changes.

---

**Made with ❤️ by the Print.com team**
