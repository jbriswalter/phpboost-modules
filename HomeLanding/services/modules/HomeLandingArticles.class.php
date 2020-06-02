<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 06 02
 * @since       PHPBoost 5.2 - 2020 03 06
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class HomeLandingArticles
{
	public static function get_articles_cat_view()
	{
		return self::build_view(HomeLandingConfig::MODULE_ARTICLES_CATEGORY);
	}

	public static function get_articles_view()
	{
		return self::build_view(HomeLandingConfig::MODULE_ARTICLES);
	}

	private static function build_view($page_type)
	{
		$now = new Date();

		$module_config = ArticlesConfig::load();
		$home_config   = HomeLandingConfig::load();
		$modules       = HomeLandingModulesList::load();
		$module_name   = HomeLandingConfig::MODULE_ARTICLES;
		$module_cat    = HomeLandingConfig::MODULE_ARTICLES_CATEGORY;

		$theme_id = AppContext::get_current_user()->get_theme();
		if (file_exists(PATH_TO_ROOT . '/HomeLanding/templates/pagecontent/' . $page_type . '.tpl'))
			$view = new FileTemplate('/HomeLanding/templates/pagecontent/' . $page_type . '.tpl');
		elseif (file_exists(PATH_TO_ROOT . '/templates/' . $theme_id . '/modules/HomeLanding/pagecontent/' . $page_type . '.tpl'))
			$view = new FileTemplate('/templates/' . $theme_id . '/modules/HomeLanding/pagecontent/' . $page_type . '.tpl');
		else
			$view = new FileTemplate('HomeLanding/pagecontent/items.tpl');

		$view->add_lang(array_merge(LangLoader::get('common', 'HomeLanding'), LangLoader::get('common', $module_name)));

		$sql_condition = 'WHERE id_category IN :categories
			AND (published = ' . Item::PUBLISHED . ' OR (published = ' . Item::DEFERRED_PUBLICATION . ' AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))';
		
		$sql_parameters = array(
			'timestamp_now' => $now->get_timestamp()
		);
		
		if ($page_type == $module_name)
			$sql_parameters['categories'] = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, $module_config->get_summary_displayed_to_guests(), $module_name);
		else
			$sql_parameters['categories'] = $modules[$module_cat]->is_subcategories_content_displayed() ? CategoriesService::get_authorized_categories($modules[$module_cat]->get_id_category(), $module_config->get_summary_displayed_to_guests(), $module_name) : array($modules[$module_cat]->get_id_category());
		
		$items = ItemsService::get_items_manager($module_name)->get_items($sql_condition, $sql_parameters, $modules[$page_type]->get_elements_number_displayed(), 0, 'creation_date', Item::DESC);
		
		$view->put_all(array(
			'C_NO_ITEM'          => count($items) > 0,
			'C_VIEWS_NUMBER'     => $module_config->get_views_number_enabled(),
			'C_GRID_VIEW'        => $module_config->get_display_type() == DefaultRichModuleConfig::GRID_VIEW,
			'C_AUTHOR_DISPLAYED' => $module_config->get_author_displayed(),
			'ITEMS_PER_ROW'      => $module_config->get_items_per_row(),
			'MODULE_NAME'        => $module_name,
			'MODULE_POSITION'    => $home_config->get_module_position_by_id($page_type),
			'L_SEE_ALL_ITEMS'    => LangLoader::get_message('link.to.' . $module_name, 'common', 'HomeLanding')
		));
		
		if ($page_type == $module_name)
		{
			$view->put_all(array(
				'L_MODULE_TITLE' => LangLoader::get_message('last.'.$module_name, 'common', 'HomeLanding'),
			));
		}
		else
		{
			$category = CategoriesService::get_categories_manager($module_name)->get_categories_cache()->get_category($modules[$module_cat]->get_id_category());
			$view->put_all(array(
				'C_CATEGORY'     => true,
				'L_MODULE_TITLE' => LangLoader::get_message('last.'.$module_name.'.cat', 'common', 'HomeLanding') . ': ' . $category->get_name()
			));
		}

		foreach ($items as $item)
		{
			$view->assign_block_vars('item', $item->get_template_vars());
		}
		
		return $view;
	}
}
?>
