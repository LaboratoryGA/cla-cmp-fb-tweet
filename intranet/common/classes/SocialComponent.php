<?php
class SocialComponent extends TemplaterComponentTmpl {

	public function Show($attributes)
	{
		global $BASE_LOCATION;
		if(!file_exists("$BASE_LOCATION/interface_default/social/output.html"))
			return;
		return $this->CallTemplater('social/output.html', array(), array());
	}
}