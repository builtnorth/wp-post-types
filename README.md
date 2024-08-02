# WP Post Types

Composer package containing core functionality that is used across Built North WordPress sites. This package is meant to dropped in to either a theme or plugin.

## Requirements

-   PHP >= 8.1
-   WordPress >= 6.4

## Installation

This library is meant to be dropped into a theme or plugin via composer.

1. In your WordPress project directory, run: `composer require builtnorth/wp-post-types`.
2. In your main plugin file or theme's functions.php, add:

```php
use BuiltNorth\Baseline\Init;

if (class_exists('BuiltNorth\Baseline\Init')) {
    Init::boot();
}
```

## Features

-   Cleanup of unnessary WordPress functionality.
-   Enhanced security measures.
-   Option to disable comments (see below).

## Warning

This package has not been fully tested at this point, and is subject to breaking changes. Use at your own discretion.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
