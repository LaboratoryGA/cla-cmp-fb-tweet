<?php
class SocialComponent implements TemplaterComponent {

	public function Show($attributes)
	{
		global $APPDATA;
		$path = "{$APPDATA}/people/social_component.html";
		if(file_exists($path))
			return file_get_contents($path);
		else
			return;
	}
}
