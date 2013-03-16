=== ShortAndBuffer ===
Contributors: twostairs
Tags: CloudApp, buffer, URL, shorten, share, social
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically shorten your post's URL via CloudApp and share on buffer, when published.

== Description ==

A WordPress plugin that shortens a post's URL on publish through CloudApp and sends an update containing the post's title + short-url to buffer.

This plugin was built on-top of Francis Yaconiello's great WordPress Plugin Template (https://github.com/fyaconiello/wp_plugin_template).

This plugin uses CloudApp Wrapper by Matthias Plappert (https://github.com/matthiasplappert/CloudApp-API-PHP-wrapper).

This plugin uses buffer class by Kevin Khandjian (https://github.com/thewebguy/bufferapp-php).

This plugin does not have super-cow powers.

== Installation ==

Just place it into your WordPress plugins-directory and activate it from the plugins-menu.

Go to Settings -> ShortAndBuffer and configure your CloudApp & buffer account.

For buffer, you'll need to create an own buffer app (https://bufferapp.com/developers/apps/create) using your blog's URL + path-to-settings-page as callback. Just open the ShortAndBuffer settings on your admin panel and copy the URL to use it as callback. The same URL you'll need to configure as "buffer Callback URL" within the ShortAndBuffer settings.

After you've entered all the right data save and write your first new post. As soon as you'll click "Publish", ShortAndBuffer will wait for WordPress to save your post, it will retrieve the post's permalink and send it to CloudApp, from which it'll receive the shortened URL. This URL is being send to buffer then - although without the "now"-API-parameter. This means, that it ONLY BUFFERS the update. It does not share it.

However, an buffer update for a post called "Me and my cow Bennita" will look like this:

Me and my cow Bennita http://short.url/AbcdEfg

On Facebook and LinkedIn you won't see the URL within the status text, since buffer attaches it correctly.

== Frequently asked questions ==

= A question that someone might have =

An answer to that question.

== Screenshots ==

[Settings](https://raw.github.com/twostairs/ShortAndBuffer/master/shortandbuffer.png)

== Changelog ==

1.1
* Enhancements & new features.

1.0
* Initial commit

== Upgrade notice ==

Nothing to notice. :)
