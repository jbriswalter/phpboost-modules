<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 05 02
 * @since       PHPBoost 3.0 - 2012 12 20
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class GoogleAnalyticsModuleMiniMenu extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__BOTTOM_CENTRAL;
	}

	public function default_is_enabled() { return true; }

	public function display($tpl = false)
	{
		$tpl = new FileTemplate('GoogleAnalytics/GoogleAnalyticsModuleMiniMenu.tpl');
		MenuService::assign_positions_conditions($tpl, $this->get_block());

		$config = GoogleAnalyticsConfig::load();
		$cookiebar_config = CookieBarConfig::load();

		if (!$config->get_identifier() && AppContext::get_current_user()->check_level(User::ADMINISTRATOR_LEVEL))
		{
			$message = StringVars::replace_vars(LangLoader::get_message('identifier_required','common', 'GoogleAnalytics'), array(
				'link' => Url::to_absolute('/GoogleAnalytics/' . url('index.php?url=/admin', 'admin/'))
			));
			return MessageHelper::display($message, MessageHelper::WARNING)->render();
		}

		$tpl->put_all(array(
			'C_DISPLAY' => $config->get_identifier() && $cookiebar_config->is_cookiebar_enabled() && $cookiebar_config->get_cookiebar_tracking_mode() == CookieBarConfig::TRACKING_COOKIE && AppContext::get_request()->get_cookie('pbt-cookiebar-choice', 0) == 1,
			'IDENTIFIER' => $config->get_identifier()
		));

		return $tpl->render();
	}
}
?>
