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

Add the below required facebook application credentials and twitter consumer credentials to config.php
```php
$cfg_facebook_app_id = '';
$cfg_facebook_app_secret = '';

$cfg_twitter_consumer_key = '';
$cfg_twitter_consumer_secret = '';
```

The webserver MUST have write permissions to `$CLA_ROOT/app_data/people/social_component.html` This can be achieved with:

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
### Configuring the streams
In `config.php` add below two arrays. The Facebook array should contain the URL's to trawl, and the twitter must contain the usernames. Here's an example:


```php
// URL's for Facebook pages
$cfg_social_stream_facebook_pages = array('/Claromentis');
// Username for Twitter accounts
$cfg_social_stream_twitter_streams = array('claromentis');
```
