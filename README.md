# HTTP/2 Server Push Twig plugin for Craft CMS 3.x

This plugin provides a convenient way to utilize Server Push via a twig filter

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require raeder/craft-http2-server-push-twig

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for HTTP/2 Server Push Twig.

## HTTP/2 Server Push Twig Overview

This plugin adds a twig filter that allows you to send a "Link" header to your web-server to enable HTTP/2 Server Push.
You will still have to configure your web-server to interpret the Link header and push the assets.

## Using HTTP/2 Server Push Twig

You can add any asset that should be included in the Link header like this:
```
<link rel="stylesheet" href="{{ '/css/blog.css' | h2push }}">
```

CSS, JS and image files are supported out of the box. If you want to push another asset type you can pass the type like this:
```
<link rel="stylesheet" href="{{ '/uploads/test.mp3' | h2push('audio') }}">
```

A full list of supported types can be found here: https://fetch.spec.whatwg.org/#concept-request-destination
It's also possible to mark an asset as crossorigin:
```
<link rel="stylesheet" href="{{ '/uploads/test.mp3' | h2push('audio', true) }}">
```

After your site template was rendered by craft a hook will generate the Header.
If you're using Caddy you can find an example on how to configure your server in my blog-post: https://www.raeder.technology/post/practical-php-implementation-for-http-2-server-push

## Using HTTP/2 Server Push Twig modulepreload

Module preload headers are an experimental browser feature so tread carefully.
To push your module file use the h2module filter:
```
<script type="module" src="{{ 'app.js' | h2module }}"></script>
```

Please note that you must add 'type="module"' to your script tag, otherwise Google Chrome will not support import and export features.
