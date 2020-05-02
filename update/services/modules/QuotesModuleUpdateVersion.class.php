<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 05 02
 * @since       PHPBoost 5.3 - 2019 11 03
 * @contributor xela <xela@phpboost.com>
*/

class QuotesModuleUpdateVersion extends ModuleUpdateVersion
{
	public function __construct()
	{
		parent::__construct('quotes');

		$this->content_tables = array(array('name' => PREFIX . 'quotes', 'content_field' => 'quote'));
		$this->delete_old_files_list = array(
			'/controllers/AdminQuotesManageController.class.php',
			'/lang/english/config.php',
			'/lang/french/config.php',
			'/phpboost/QuotesHomePageExtensionPoint.class.php',
			'/phpboost/QuotesSitemapExtensionPoint.class.php',
			'/phpboost/QuotesTreeLinks.class.php',
			'/services/QuotesAuthorizationsService.class.php',
			'/util/AdminQuotesDisplayResponse.class.php'
		);
		$this->delete_old_folders_list = array(
			'/controllers/categories'
		);
	}
}
?>
