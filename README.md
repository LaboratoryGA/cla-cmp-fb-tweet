# Social Media Stream Component
This module provides a templater component for streaming in your company's
social media stream, such as Facebook and Twitter.

It is a much more open-ended design that previous implementation, allowing
support for additional social media stream support, but remains downward
compatible, and should be usable as a "drop-in" replacement for the outdated
versions.

## Installation
### Pre-requisites
These installations steps make the following assumptions:
* you are running on a Linux server
* you have `git` installed

### Get Files
You must be in the `/web` subdirectory of your Claromentis installation:
```shell
cd /Claromentis/web
```

Clone the module repository:
```shell
git clone https://github.com/LaboratoryGA/cla-cmp-fb-tweet.git intranet/social
```

Now execute `phing` to perform any additional steps automatically. If this is
a new (clean) installation, invoke the following:
```shell
phing -Dapp=social install
```

If this is actually an upgrade from a previous version, invoke the following:
```shell
phing -Dapp=social upgrade
```

If everything has completed without issue, you are ready to move on to
configuration.

### Configuration
#### New (advanced)
Create a new configuration file for your specific installation:
```shell
nano intranet/social/config_social.php
```

In the editor which opens, enter the following lines (note that these are just
examples, your actual values should be different):
```php
$cfg_social_stream = [
	[
		'provider'			=> 'facebook',
		'appID'				=> '210301132424295',
		'appSecret'			=> '75407e2280fd24eb147c3d08cd7e340c',
		'resource'			=> 'laboratory.ga'
	],
	[
		'provider'			=> 'twitter',
		'consumerKey'		=> '3rVGexalmyASGnADj3osotm1u',
		'consumerSecret'	=> '9Fi1oJ9H6fZOZ3xYhwsbZ3LP1icn0eHKSmOIbqKijhRfDCl5o0',
		'screenName'		=> 'LaboratoryGa'
	]
];
```

When complete, press `Ctrl+X` and when asked if you wish to save, press `Y`.

See the section below on "Advanced Configuration/Usage Recipes" for more options.

#### Legacy Support
This version of the module has been specifically designed to use the
configuration options used in previous versions.

As such, the following configurations may appear in the
`/intranet/common/config.php` file, and will be used as expected:
```php
$cfg_facebook_app_id = '';
$cfg_facebook_app_secret = '';

$cfg_twitter_consumer_key = '';
$cfg_twitter_consumer_secret = '';

// URL's for Facebook pages
$cfg_social_stream_facebook_pages = array('/Claromentis');
// Username for Twitter accounts
$cfg_social_stream_twitter_streams = array('claromentis');
```

**It is critical that you be aware that, by default, the presence of these
legacy configuration options will override any other configurations**

## Usage
Place the following into any template:
```html
<component class="SocialComponent">
```

If you wish to limit which stream is display, use the `social_filter` parameter, and provide a comma-separated list of providers (such as Facebook [facebook/fb - *available only when using legacy configuration*] or Twitter [twitter]):
```html
<component class="SocialComponent" social_filter="fb,twitter">
```

If you wish to limit the *total* number of posts (i.e. all social streams combined), use the `limit` parameter:
```html
<component class="SocialComponent" social_filter="fb,twitter" limit="10">
```

If you wish to limit the number of posts for each individual stream, use the `limit_per` parameter:
```html
<component class="SocialComponent" social_filter="fb,twitter" limit="10" limit_per="5">
```

If you wish to limit the length of the text content (currently only implemented for Facebook, since Twitter already has a very restrictive length limit), use the `post_length` parameter:
```html
<component class="SocialComponent" social_filter="fb,twitter" limit="10" limit_per="5" post_length="200">
```

See the section below on "Advanced Configuration/Usage Recipes" for more options.

## Advanced Configuration/Usage Recipes
### Configuring multiple streams for the same provider
The configuration variable `$cfg_social_stream` has an additional parameter which may be passed: `name`. This is used to
uniquely identify a single stream instance, even if it refer to the same social media service. If not explicitly defined,
this parameter it automcatically set to `default`.

Following is an example of multiple Facebook streams:
```php
$cfg_social_stream = [
	[
		'provider'			=> 'facebook',
		'name'				=> 'laboratory',
		'appID'				=> '210301132424295',
		'appSecret'			=> '75407e2280fd24eb147c3d08cd7e340c',
		'resource'			=> 'laboratory.ga'
	],
	[
		'provider'			=> 'facebook',
		'name'				=> 'claromentis',
		'appID'				=> '210301132424295',
		'appSecret'			=> '75407e2280fd24eb147c3d08cd7e340c',
		'resource'			=> 'Claromentis'
	]
]
```

### Filtering by instance
In the previous section we saw how you can have multiple streams from the same source. When using the templater component, supplying a value of `facebook` to the `social_filter` parameter will automatically feed all the instances for a given provider. **However**, if you wish to limit the output to only a single instance, use the syntax `provider::name`, such as can be seen in the following example:
```html
<component class="SocialComponent" social_filter="facebook::claromentis">
```

This feature allows use-cases such as each department within a company has their own Facebook page or Twitter stream.

## Q&A
Q: *Why did you use "git clone" instead of simply copying the files?*

A: If there are minor upgrades in the future, upgrading the module's source is
simply a case of running `git pull origin master` and everything is
automagicaly upgraded. Any modifications you have made (assuming they do not
explicitly conflict with the new source) will be preserved.
