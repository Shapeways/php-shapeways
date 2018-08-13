PHP Shapeways
=============

[![Build Status](https://travis-ci.org/Shapeways/php-shapeways.png?branch=master)](https://travis-ci.org/Shapeways/php-shapeways)
[![Coverage Status](https://coveralls.io/repos/Shapeways/php-shapeways/badge.png?branch=master)](https://coveralls.io/r/Shapeways/php-shapeways?branch=master)
[![PHP version](https://poser.pugx.org/shapeways/shapeways/v/stable.png)](https://packagist.org/packages/shapeways/shapeways)
[![Shapeways API Version](http://b.repl.ca/v1/shapeways--api-v1-brightgreen.png)](https://developers.shapeways.com/docs)

PHP module for accessing the [Shapeways](http://www.shapeways.com) api [http://developers.shapeways.com](http://developers.shapeways.com).

## Installation
### Composer
```bash
composer require shapeways/shapeways
```

```php
<?php
require './vendor/autoload.php';

$client = new Shapeways\Oauth2Client( ... );
```

### Git
```bash
git clone git://github.com/Shapeways/php-shapeways.git
```

```php
<?php

require './php-shapeways/src/Oauth2Client.php';

$client = new \Shapeways\Oauth2Client( ... );
```

## Documentation
https://shapeways.github.io/php-shapeways


## Examples
See `examples` directory.

## License
```
The MIT License (MIT) Copyright (c) 2014 Shapeways <api@shapeways.com> (http://developers.shapeways.com)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```
