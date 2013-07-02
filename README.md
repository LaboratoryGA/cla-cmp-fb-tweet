Facebook and Twitter stream component
==========

Pull in posts from your companies Facebook and/or Twitter stream with this handy component!

[![](https://raw.github.com/Claromentis/cla-cmp-fb-tweet/master/screenshot.png)](https://raw.github.com/Claromentis/FBTweetComponent/master/screenshot.png)

## Install
Simply copy the files to the destination server (preserving the folder structure).

Drop the following component tag in the desired location:

```html
    <component class="SocialComponent">
```

The webserver MUST have write permissions to `$CLA_ROOT/interface_default/social/output.html` This can be achieved with:

	$ chmod 777 $CLA_ROOT/interface_default/social/output.html

### Periodic updates
To have it update periodically (it's pretty useless unless it does this), create a file named `background_custom.php` in `$CLA_ROOT/intranet/common` and add the following:

```php
<?php
if ($bg->IsTimePass("Update social stream", 20)) {
	$social = new SocialStream();
	$social->Go();
}
```
### Configuring the strams
In `SocialStream.php` there are two arrays named `$twitter_streams` and `$facebook_pages`. The Facebook array should contain the URL's to trawl, and the twitter must contain the URL's **and** Username. Here's an example:

```php
// URL's for Facebook pages
protected $facebook_pages = array(
	'http://facebook.com/a/valid/page/url'
);

// Username for Twitter accounts
protected $twitter_streams = array(
	'userAccount1', 'userAccount2'
);
```