<?php
class SocialComponent implements TemplaterComponent {

	public function Show($attributes)
	{
		global $APPDATA;
		$path = "{$APPDATA}/people/social_component.html";
		if(file_exists($path))
			readfile($path);
		else
			return;
	}
}
