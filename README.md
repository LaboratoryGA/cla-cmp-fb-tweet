Facebook and Twitter stream component
==========

Pull in posts from your companies Facebook and/or Twitter stream with this handy component!

[![](https://raw.github.com/Claromentis/FBTweetComponent/master/screenshot.png)](https://raw.github.com/Claromentis/FBTweetComponent/master/screenshot.png)

Install
---
Simply copy the files to the destination server (preserving the folder structure).

Drop the following component tag in the desired location:

```html
    <component class="SocialComponent">
```

To have it update periodically (it's pretty useless unless it does this), create a file named `background_custom.php` in `$CLA_ROOT/intranet/common` and add the following:

```php
<?php
if ($bg->IsTimePass("Update social stream", 20)) {
	$social = new SocialStream();
	$social->Go();
}
```