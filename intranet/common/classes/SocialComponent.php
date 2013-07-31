<?php
class SocialComponent implements TemplaterComponent {

	public function Show($attributes)
	{
		global $BASE_LOCATION;
		if(!file_exists("$BASE_LOCATION/interface_default/social/output.html"))
			return;

		print(file_get_contents("$BASE_LOCATION/interface_default/social/output.html"));
	}
}