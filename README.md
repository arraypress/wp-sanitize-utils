# WordPress Clean Utils

A lean WordPress library for sanitization and validation utilities. Provides essential tools for cleaning user input and validating data with WordPress integration.

## Installation

```bash
composer require arraypress/wp-clean-utils
```

## Quick Start

```php
use ArrayPress\CleanUtils\Sanitize;
use ArrayPress\CleanUtils\Validate;

// Sanitization
$clean_email  = Sanitize::email( ' USER@EXAMPLE.COM ' );    // "user@example.com"
$clean_amount = Sanitize::amount( '$1,234.56' );           // "1234.56"
$safe_html    = Sanitize::html( '<p>Safe content</p>' );      // Allows safe HTML

// Validation
$is_valid_email = Validate::email( 'user@example.com' );   // true
$is_in_range    = Validate::range( 15, 10, 20 );             // true
$missing_fields = Validate::required_fields( $data, [ 'name', 'email' ] ); // []
```

## Sanitize Class

### Core Sanitization
```php
// Basic text cleaning
Sanitize::text( '  Hello World!  ' );          // "Hello World!"
Sanitize::clean( $dirty_data );                // Recursively clean arrays
Sanitize::email( ' USER@EXAMPLE.COM ' );       // "user@example.com"
Sanitize::url( 'https://example.com/path' );   // Clean URL
Sanitize::html( '<p>Content</p><script>' );    // Safe HTML only

// WordPress-specific
Sanitize::slug( 'Product Name Here' );         // "product-name-here"
Sanitize::username( 'User.Name!' );            // "user.name"
Sanitize::key( 'my-option_key' );              // "my-option_key"
```

### Specialized Sanitization
```php
// Numbers and amounts
Sanitize::int( '123.45' );                     // 123
Sanitize::float( '123.45' );                   // 123.45
Sanitize::amount( '$1,234.56' );               // "1234.56"
Sanitize::percentage( '125%' );                // 100.0 (clamped to 0-100)

// Lists and arrays
Sanitize::comma_list( 'item1, item2, item3' ); // ['item1', 'item2', 'item3']
Sanitize::emails( 'user1@ex.com,user2@ex.com' ); // ['user1@ex.com', 'user2@ex.com']
Sanitize::object_ids( [ 1, '2', 3, 'invalid' ] ); // [1, 2, 3]

// Ranges and constraints
Sanitize::range( 150, 0, 100 );                // 100.0 (clamped to range)
Sanitize::rating( '7', 1, 5 );                 // 5 (clamped to 1-5)
```

### Business-Specific
```php
// E-commerce
Sanitize::discount_type( 'Percentage' );       // "percentage"
Sanitize::status( 'Active' );                  // "active"

// Dates and times
Sanitize::date( '2024-01-15 10:30' );          // "2024-01-15 10:30:00"
Sanitize::time( '25:70' );                     // "" (invalid)
Sanitize::timezone( 'America/New_York' );      // "America/New_York"

// Technical
Sanitize::hex_color( '#FF0000' );              // "#ff0000"
Sanitize::phone( '+1 (555) 123-4567' );        // "+1 (555) 123-4567"
Sanitize::ip( '192.168.1.1' );                 // "192.168.1.1"
```

## Validate Class

### Basic Validation
```php
// Required fields
Validate::required( '' );                      // false
Validate::required( '0' );                     // true
Validate::required( [] );                      // false

// Data types
Validate::email( 'user@example.com' );         // true
Validate::url( 'https://example.com' );        // true
Validate::numeric( '123.45' );                // true
Validate::integer( '123' );                    // true
```

### Range and Constraints
```php
// Numeric ranges
Validate::min( 15, 10 );                       // true (15 >= 10)
Validate::max( 15, 20 );                       // true (15 <= 20)
Validate::range( 15, 10, 20 );                 // true (10 <= 15 <= 20)
Validate::percentage( 85 );                    // true (0-100)

// String length
Validate::length( 'hello', 10, 3 );            // true (3 <= 5 <= 10)

// Options validation
Validate::in( 'active', [ 'active', 'inactive' ] ); // true
```

### Specialized Validation
```php
// Dates and times
Validate::date( '2024-01-15' );                // true
Validate::time( '14:30' );                     // true
Validate::timezone( 'America/New_York' );      // true

// Technical formats
Validate::hex_color( '#FF0000' );              // true
Validate::uuid( '550e8400-e29b-41d4-a716-446655440000' ); // true
Validate::json( '{"valid": true}' );           // true
Validate::phone( '+1-555-123-4567' );          // true

// WordPress-specific
Validate::username( 'valid_user' );            // true
Validate::slug( 'valid-slug' );                // true
```

### Advanced Validation
```php
// Password strength
Validate::strong_password( 'MyPass123!', 8, true, true, true, true ); // true

// Credit card (Luhn algorithm)
Validate::credit_card( '4532015112830366' );   // true

// Multiple required fields
$data    = [ 'name' => 'John', 'email' => 'john@example.com' ];
$missing = Validate::required_fields( $data, [ 'name', 'email', 'phone' ] );
// Returns: ['phone']

// Pattern matching
Validate::matches_pattern( 'ABC123', '/^[A-Z]{3}\d{3}$/' ); // true
```

## Real-World Examples

### Form Processing
```php
// Clean and validate contact form
$data = [
	'name'    => $_POST['name'] ?? '',
	'email'   => $_POST['email'] ?? '',
	'phone'   => $_POST['phone'] ?? '',
	'message' => $_POST['message'] ?? ''
];

// Sanitize inputs
$clean_data = [
	'name'    => Sanitize::text( $data['name'] ),
	'email'   => Sanitize::email( $data['email'] ),
	'phone'   => Sanitize::phone( $data['phone'] ),
	'message' => Sanitize::textarea( $data['message'] )
];

// Validate required fields
$missing = Validate::required_fields( $clean_data, [ 'name', 'email' ] );
if ( ! empty( $missing ) ) {
	wp_die( 'Missing required fields: ' . implode( ', ', $missing ) );
}

// Validate email format
if ( ! Validate::email( $clean_data['email'] ) ) {
	wp_die( 'Invalid email address' );
}
```

### E-commerce Product Management
```php
// Clean product data
$product_data = [
	'name'           => Sanitize::text( $_POST['product_name'] ),
	'slug'           => Sanitize::slug( $_POST['product_slug'] ),
	'price'          => Sanitize::amount( $_POST['price'] ),
	'discount_type'  => Sanitize::discount_type( $_POST['discount_type'] ),
	'discount_value' => Sanitize::percentage( $_POST['discount_value'] ),
	'status'         => Sanitize::status( $_POST['status'] ),
	'categories'     => Sanitize::object_ids( $_POST['categories'] )
];

// Validate business rules
if ( ! Validate::min( $product_data['price'], 0.01 ) ) {
	wp_die( 'Price must be greater than $0.00' );
}

if ( $product_data['discount_type'] === 'percentage' &&
     ! Validate::percentage( $product_data['discount_value'] ) ) {
	wp_die( 'Discount percentage must be between 0-100%' );
}
```

### User Registration
```php
// Process user registration
$user_data = [
	'username'   => Sanitize::username( $_POST['username'] ),
	'email'      => Sanitize::email( $_POST['email'] ),
	'password'   => $_POST['password'], // Don't sanitize passwords
	'first_name' => Sanitize::text( $_POST['first_name'] ),
	'last_name'  => Sanitize::text( $_POST['last_name'] )
];

// Validate username
if ( ! Validate::username( $user_data['username'] ) ) {
	wp_die( 'Invalid username format' );
}

// Validate email
if ( ! Validate::email( $user_data['email'] ) ) {
	wp_die( 'Invalid email address' );
}

// Validate password strength
if ( ! Validate::strong_password( $user_data['password'], 8 ) ) {
	wp_die( 'Password must be at least 8 characters with uppercase, lowercase, number, and special character' );
}

// Check if username/email already exists
if ( username_exists( $user_data['username'] ) ) {
	wp_die( 'Username already exists' );
}

if ( email_exists( $user_data['email'] ) ) {
	wp_die( 'Email already registered' );
}
```

### Settings Page
```php
// Clean and validate settings
$settings = [
	'site_email'         => Sanitize::email( $_POST['site_email'] ),
	'items_per_page'     => Sanitize::int_range( $_POST['items_per_page'], 1, 100 ),
	'currency_symbol'    => Sanitize::text( $_POST['currency_symbol'] ),
	'allowed_file_types' => Sanitize::comma_list( $_POST['allowed_file_types'] ),
	'primary_color'      => Sanitize::hex_color( $_POST['primary_color'] ),
	'enable_feature'     => Sanitize::bool( $_POST['enable_feature'] )
];

// Validate critical settings
if ( ! Validate::email( $settings['site_email'] ) ) {
	add_settings_error( 'settings', 'invalid_email', 'Invalid email address' );
}

if ( ! Validate::range( $settings['items_per_page'], 1, 100 ) ) {
	add_settings_error( 'settings', 'invalid_range', 'Items per page must be between 1-100' );
}

// Save if valid
if ( empty( get_settings_errors() ) ) {
	update_option( 'my_plugin_settings', $settings );
}
```

### API Data Processing
```php
// Clean incoming API data
$api_data = json_decode( file_get_contents( 'php://input' ), true );

$clean_api_data = [
	'id'       => Sanitize::absint( $api_data['id'] ?? 0 ),
	'title'    => Sanitize::string_length( $api_data['title'] ?? '', 100 ),
	'content'  => Sanitize::html( $api_data['content'] ?? '' ),
	'status'   => Sanitize::option( $api_data['status'] ?? '', [ 'draft', 'published' ], 'draft' ),
	'tags'     => Sanitize::comma_list( $api_data['tags'] ?? '' ),
	'metadata' => Sanitize::json( $api_data['metadata'] ?? '{}' )
];

// Validate required fields
$required = [ 'id', 'title' ];
$missing  = Validate::required_fields( $clean_api_data, $required );

if ( ! empty( $missing ) ) {
	wp_send_json_error( [ 'message' => 'Missing required fields', 'fields' => $missing ] );
}

// Additional validation
if ( ! Validate::length( $clean_api_data['title'], 100, 1 ) ) {
	wp_send_json_error( [ 'message' => 'Title must be 1-100 characters' ] );
}
```

## Security Best Practices

```php
// ✅ Always sanitize user input
$clean_input = Sanitize::text( $_POST['user_input'] );

// ✅ Validate after sanitizing
if ( ! Validate::required( $clean_input ) ) {
	wp_die( 'Input is required' );
}

// ✅ Use appropriate sanitization for data type
$email  = Sanitize::email( $_POST['email'] );
$amount = Sanitize::amount( $_POST['price'] );
$html   = Sanitize::html( $_POST['content'] );

// ✅ Validate business rules
if ( ! Validate::min( $amount, 0 ) ) {
	wp_die( 'Amount must be positive' );
}

// ❌ Never trust user input without sanitization
// $unsafe = $_POST['data']; // Don't do this

// ❌ Don't sanitize passwords (just validate)
// $password = Sanitize::text($_POST['password']); // Wrong!
```

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-clean-utils)
- [Issue Tracker](https://github.com/arraypress/wp-clean-utils/issues)