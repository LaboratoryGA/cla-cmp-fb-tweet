Facebook and Twitter stream component
==========

Pull in posts from your companies Facebook and/or Twitter stream with this handy component!

[![](https://raw.github.com/Claromentis/cla-cmp-fb-tweet/master/screenshot.png)](https://raw.github.com/Claromentis/FBTweetComponent/master/screenshot.png)

## Install
Simply copy the files to the `$CLA_ROOT/social' (preserving the folder structure).

Run the following command in `$CLA_ROOT`
phing -Dapp=social install

Drop the following component tag in the desired location:

```html
    <component class="SocialComponent">
```

You can filter the source (Facebook (fb) or Twitter (Twitter) with the source_filter attribute.
```html
    <component class="SocialComponent" source_filter="Twitter,fb">
```
To limit the number of posts to show use the limit attribute.
```html
    <component class="SocialComponent" source_filter="Twitter,fb" limit="2">
```


Add the below required facebook application credentials and twitter consumer credentials to config.php
```php
$cfg_facebook_app_id = '';
$cfg_facebook_app_secret = '';

$cfg_twitter_consumer_key = '';
$cfg_twitter_consumer_secret = '';
```

### Configuring the streams
In `config.php` add below two arrays. The Facebook array should contain the URL's to trawl, and the twitter must contain the usernames. Here's an example:


```php
// URL's for Facebook pages
$cfg_social_stream_facebook_pages = array('/Claromentis');
// Username for Twitter accounts
$cfg_social_stream_twitter_streams = array('claromentis');
```
