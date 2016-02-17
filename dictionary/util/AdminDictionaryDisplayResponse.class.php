<?php
/*##################################################
 *                               AdminDictionaryDisplayResponse.class.php
 *                            -------------------
 *   begin                : February 17, 2016
 *   copyright            : (C) 2016 Julien BRISWALTER
 *   email                : j1.seth@phpboost.com
 *
 *
 ###################################################
 *
 * This program is a free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

 /**
 * @author Julien BRISWALTER <j1.seth@phpboost.com>
 */

class AdminDictionaryDisplayResponse extends AdminMenuDisplayResponse
{
	public function __construct($view, $title_page)
	{
		parent::__construct($view);
		
		global $LANG;
		load_module_lang('dictionary'); //Chargement de la langue du module.
		
		$lang = LangLoader::get('common', 'dictionary');
		$this->set_title($lang['module_title']);
		
		$this->add_link($LANG['admin.categories.manage'], new Url('/dictionary/admin_dictionary_cats.php'));
		$this->add_link($LANG['dictionary_cats_add'], new Url('/dictionary/admin_dictionary_cats.php?add=1'));
		$this->add_link($LANG['admin.words.manage'], new Url('/dictionary/admin_dictionary_list.php'));
		$this->add_link($LANG['create_dictionary'], new Url('/dictionary/dictionary.php?add=1'));
		$this->add_link(LangLoader::get_message('configuration', 'admin-common'), DictionaryUrlBuilder::configuration());
		
		$env = $this->get_graphical_environment();
		$env->set_page_title($title_page, $lang['module_title']);
	}
}
?>
