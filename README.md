Rootd Framework for WordPress
=============================

Rootd is a framework for WordPress that seeks to provide a better development toolkit.

Follow updates here and on my blog:

http://blog.rickbuczynski.com/tag/rootd-wordpress/


Installation
============

Setup the package in your project `composer.json`

```json
{
    "repositories": 
    [
        {
            "type": "vcs",
            "url": "git@github.com:vbuck/rootd-wordpress.git"
        }

    ],
    "require": {
        "vbuck/rootd-wordpress": "dev-master"
    }
}
```

Then run `composer update` to inject the new dependency.

Please note that this package will also install the required core framework `vbuck/root-wordpress-core`.


License
=======

The MIT License (MIT)

Copyright (c) 2014 Rick Buczynski

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.