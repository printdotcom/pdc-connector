#!/usr/bin/env bash

# Seed script to configure WooCommerce and create products using raw SQL
set -e

CONFIG_FILE="config/wp-version.conf"

WORDPRESS_VERSION_ENV="${1:-$WORDPRESS_VERSION}"
PHP_VERSION_ENV="${2:-$PHP_VERSION}"

# Use defaults if no arguments provided
if [[ -z "$WORDPRESS_VERSION_ENV" ]]; then
    WORDPRESS_VERSION_ENV="67"
fi

if [[ -z "$PHP_VERSION_ENV" ]]; then
    PHP_VERSION_ENV="82"
fi

WP_IMAGE_KEY="${WORDPRESS_VERSION_ENV}_${PHP_VERSION_ENV}"
WP_IMAGE=$(grep "^${WP_IMAGE_KEY}=" "$CONFIG_FILE" | cut -d'=' -f2)
WORDPRESS_PORT="80${WORDPRESS_VERSION_ENV}"

if [ -z "$WP_IMAGE" ]; then
  echo "❌ Unsupported version combination: $WP_IMAGE_KEY"
  echo "Available combinations in $CONFIG_FILE:"
  grep "^[0-9]" "$CONFIG_FILE" || echo "No valid combinations found"
  exit 1
fi

# Parse WordPress version parameter
if [[ "$WORDPRESS_VERSION_ENV" =~ ^[0-9]{2}$ ]]; then
  WORDPRESS_VERSION="${WORDPRESS_VERSION_ENV:0:1}.${WORDPRESS_VERSION_ENV:1:1}"
else
  WORDPRESS_VERSION="$WORDPRESS_VERSION_ENV"
fi

# Parse PHP version parameter  
if [[ "$PHP_VERSION_ENV" =~ ^[0-9]{2}$ ]]; then
  PHP_VERSION="${PHP_VERSION_ENV:0:1}.${PHP_VERSION_ENV:1:1}"
else
  PHP_VERSION="$PHP_VERSION_ENV"
fi

export WORDPRESS_VERSION 
export WORDPRESS_VERSION_ENV
export WP_IMAGE
export PHP_VERSION
export PHP_VERSION_ENV
export WORDPRESS_PORT
export COMPOSE_PROJECT_NAME="pdc_${WORDPRESS_VERSION_ENV}_${PHP_VERSION_ENV}"

echo "🚀 Starting WooCommerce configuration with SQL..."
echo "Using WordPress $WORDPRESS_VERSION and PHP $PHP_VERSION"
echo "Project: $COMPOSE_PROJECT_NAME"

# Ensure WooCommerce is installed and activated
echo "📦 Installing and activating WooCommerce..."
docker compose -f config/docker-compose.yml run --rm wpcli wp plugin install woocommerce --activate --allow-root

# Execute SQL commands via WP-CLI
execute_sql() {
    local SQL="$1"
    echo "Executing SQL: ${SQL:0:100}..."
    docker compose -f config/docker-compose.yml run --rm wpcli wp db query "$SQL" --allow-root
}

# Configure WooCommerce settings via SQL
echo "⚙️ Configuring WooCommerce..."
execute_sql "
INSERT INTO wp_options (option_name, option_value, autoload) VALUES
('woocommerce_store_address', '123 Example Street', 'yes'),
('woocommerce_store_city', 'Example City', 'yes'),
('woocommerce_default_country', 'US:CA', 'yes'),
('woocommerce_store_postcode', '90210', 'yes'),
('woocommerce_currency', 'USD', 'yes'),
('woocommerce_product_type', 'both', 'yes'),
('woocommerce_allow_tracking', 'no', 'yes'),
('woocommerce_enable_guest_checkout', 'yes', 'yes'),
('woocommerce_enable_checkout_login_reminder', 'yes', 'yes'),
('woocommerce_enable_signup_and_login_from_checkout', 'yes', 'yes'),
('woocommerce_enable_myaccount_registration', 'yes', 'yes'),
('woocommerce_registration_generate_username', 'yes', 'yes'),
('woocommerce_registration_generate_password', 'yes', 'yes')
ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);"

# Create WooCommerce pages
echo "📄 Creating WooCommerce pages..."
execute_sql "
-- Create Shop page
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order, post_type, post_mime_type, comment_count)
VALUES (1, NOW(), UTC_TIMESTAMP(), '[woocommerce_shop]', 'Shop', '', 'publish', 'closed', 'closed', '', 'shop', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0, 'page', '', 0);

SET @shop_page_id = LAST_INSERT_ID();

-- Create Cart page
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order, post_type, post_mime_type, comment_count)
VALUES (1, NOW(), UTC_TIMESTAMP(), '[woocommerce_cart]', 'Cart', '', 'publish', 'closed', 'closed', '', 'cart', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0, 'page', '', 0);

SET @cart_page_id = LAST_INSERT_ID();

-- Create Checkout page
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order, post_type, post_mime_type, comment_count)
VALUES (1, NOW(), UTC_TIMESTAMP(), '[woocommerce_checkout]', 'Checkout', '', 'publish', 'closed', 'closed', '', 'checkout', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0, 'page', '', 0);

SET @checkout_page_id = LAST_INSERT_ID();

-- Create My Account page
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order, post_type, post_mime_type, comment_count)
VALUES (1, NOW(), UTC_TIMESTAMP(), '[woocommerce_my_account]', 'My account', '', 'publish', 'closed', 'closed', '', 'my-account', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0, 'page', '', 0);

SET @account_page_id = LAST_INSERT_ID();

-- Set WooCommerce page options
INSERT INTO wp_options (option_name, option_value, autoload) VALUES
('woocommerce_shop_page_id', @shop_page_id, 'yes'),
('woocommerce_cart_page_id', @cart_page_id, 'yes'),
('woocommerce_checkout_page_id', @checkout_page_id, 'yes'),
('woocommerce_myaccount_page_id', @account_page_id, 'yes')
ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);"

# Create product categories
echo "🏷️ Creating product categories..."
execute_sql "
-- Create Print Products category
INSERT INTO wp_terms (name, slug, term_group) VALUES ('Print Products', 'print-products', 0);
SET @print_products_term_id = LAST_INSERT_ID();

INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) 
VALUES (@print_products_term_id, 'product_cat', 'Custom print products from Print.com', 0, 0);
SET @print_products_taxonomy_id = LAST_INSERT_ID();

-- Create T-Shirts subcategory
INSERT INTO wp_terms (name, slug, term_group) VALUES ('T-Shirts', 't-shirts', 0);
SET @tshirts_term_id = LAST_INSERT_ID();

INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) 
VALUES (@tshirts_term_id, 'product_cat', 'Custom printed t-shirts', @print_products_taxonomy_id, 0);
SET @tshirts_taxonomy_id = LAST_INSERT_ID();

-- Create Posters subcategory
INSERT INTO wp_terms (name, slug, term_group) VALUES ('Posters', 'posters', 0);
SET @posters_term_id = LAST_INSERT_ID();

INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) 
VALUES (@posters_term_id, 'product_cat', 'Custom printed posters', @print_products_taxonomy_id, 0);
SET @posters_taxonomy_id = LAST_INSERT_ID();"

# Function to create a product using SQL
create_product_sql() {
    local SKU="$1"
    local TITLE="$2"
    local PRICE="$3"
    local DESCRIPTION="$4"
    local CATEGORY_SLUG="$5"
    
    echo "🛍️ Creating product: $TITLE (SKU: $SKU)"
    
    execute_sql "
    -- Create the product post
    INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order, post_type, post_mime_type, comment_count)
    VALUES (1, NOW(), UTC_TIMESTAMP(), '$DESCRIPTION', '$TITLE', '', 'publish', 'closed', 'closed', '', '$(echo "$TITLE" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9]/-/g' | sed 's/--*/-/g' | sed 's/^-\|-$//g')', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0, 'product', '', 0);
    
    SET @product_id = LAST_INSERT_ID();
    
    -- Add product meta data
    INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
    (@product_id, '_sku', '$SKU'),
    (@product_id, '_price', '$PRICE'),
    (@product_id, '_regular_price', '$PRICE'),
    (@product_id, '_sale_price', ''),
    (@product_id, '_visibility', 'visible'),
    (@product_id, '_stock_status', 'instock'),
    (@product_id, '_manage_stock', 'no'),
    (@product_id, '_sold_individually', 'no'),
    (@product_id, '_virtual', 'no'),
    (@product_id, '_downloadable', 'no'),
    (@product_id, '_product_attributes', ''),
    (@product_id, '_default_attributes', ''),
    (@product_id, '_featured', 'no'),
    (@product_id, '_weight', ''),
    (@product_id, '_length', ''),
    (@product_id, '_width', ''),
    (@product_id, '_height', ''),
    (@product_id, '_tax_status', 'taxable'),
    (@product_id, '_tax_class', ''),
    (@product_id, '_purchase_note', ''),
    (@product_id, '_product_version', '8.5.0');
    
    -- Assign to category
    INSERT INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
    SELECT @product_id, tt.term_taxonomy_id, 0
    FROM wp_term_taxonomy tt
    JOIN wp_terms t ON tt.term_id = t.term_id
    WHERE t.slug = '$CATEGORY_SLUG' AND tt.taxonomy = 'product_cat';
    
    -- Update category count
    UPDATE wp_term_taxonomy tt
    JOIN wp_terms t ON tt.term_id = t.term_id
    SET tt.count = tt.count + 1
    WHERE t.slug = '$CATEGORY_SLUG' AND tt.taxonomy = 'product_cat';
    "
}

# Create sample products
echo "🎨 Creating sample products..."

create_product_sql "PDC-TSHIRT-001" "Custom T-Shirt - Basic" "29.99" "High-quality custom printed t-shirt. Perfect for personal use or promotional purposes. Available in multiple sizes and colors." "t-shirts"

create_product_sql "PDC-TSHIRT-002" "Custom T-Shirt - Premium" "39.99" "Premium quality custom printed t-shirt with enhanced fabric and printing. Ideal for professional events and gifts." "t-shirts"

create_product_sql "PDC-POSTER-001" "Custom Poster - A3" "19.99" "High-resolution custom printed poster in A3 size. Perfect for home decoration, office display, or promotional materials." "posters"

create_product_sql "PDC-POSTER-002" "Custom Poster - A2" "34.99" "Large format custom printed poster in A2 size. Premium paper quality with vibrant colors for professional displays." "posters"

create_product_sql "PDC-MUG-001" "Custom Mug - Ceramic" "14.99" "Personalized ceramic mug with your custom design. Dishwasher and microwave safe. Perfect for gifts or promotional items." "print-products"

# Update WooCommerce version and setup completion
echo "🔄 Finalizing WooCommerce setup..."
execute_sql "
INSERT INTO wp_options (option_name, option_value, autoload) VALUES
('woocommerce_version', '8.5.0', 'yes'),
('woocommerce_db_version', '8.5.0', 'yes'),
('woocommerce_onboarding_profile', '{\"completed\": true}', 'yes')
ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);"

# Flush rewrite rules
echo "🔄 Flushing rewrite rules..."
docker compose -f config/docker-compose.yml run --rm wpcli wp rewrite flush --allow-root

# Final verification
echo "🔍 Verifying WooCommerce setup..."
execute_sql "SELECT COUNT(*) as product_count FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish';"
execute_sql "SELECT p.post_title, pm1.meta_value as sku, pm2.meta_value as price FROM wp_posts p LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sku' LEFT JOIN wp_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_price' WHERE p.post_type = 'product' AND p.post_status = 'publish' ORDER BY p.post_title;"

echo "🎉 WooCommerce seeding completed successfully!"
echo "📊 You can now visit your WooCommerce shop and admin area to see the products."
echo "🛒 Shop URL: http://localhost:\${WORDPRESS_PORT:-8000}/shop/"
echo "⚙️ Admin URL: http://localhost:\${WORDPRESS_PORT:-8000}/wp-admin/"
