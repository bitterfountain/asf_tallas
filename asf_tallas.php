<?php

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

//use PrestaShop\PrestaShop\Core\Module\WidgetInterface;


class Asf_Tallas extends Module 
{
    private $templateFile;

	public function __construct()
	{
		$this->name = 'asf_tallas';
		$this->version = '1.0';
		$this->author = 'Antonio Sánchez';
		$this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Tallas', array(), 'Modules.Tallas.Admin');
        $this->description = $this->trans('Displays sizes in miniatures.', array(), 'Modules.Tallas.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile = $this->_path . $this->name . '.tpl';
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayProductPriceBlock') &&
            $this->registerHook('header') &&
           //$this->installFixtures() &&
            $this->disableDevice(Context::DEVICE_MOBILE));
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
        $this->context->controller->addJS($this->_path . $this->name . '.js');
    }


    public function hookDisplayProductPriceBlock($params)
    {

        if (isset($params['product']) && $params['type']=='tallas') 
        {

            include_once('classes/Combinations.php');

            $comb = new Combinations();

            $this->smarty->assign( Array(
                'combinations'  => $comb->getProductAttributeCombinations($params['product']['id_product']),
                'id_product'    => $params['product']['id_product']
            ));

            return $this->display(__FILE__, 'asf_tallas.tpl');
        }

    }
/*
    protected function installFixtures()
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $this->installFixture((int)$lang['id_lang'], 'sale70.png');
        }

        return true;
    }

    protected function installFixture($id_lang, $image = null)
    {
        $values['BANNER_IMG'][(int)$id_lang] = $image;
        $values['BANNER_LINK'][(int)$id_lang] = '';
        $values['BANNER_DESC'][(int)$id_lang] = '';

        Configuration::updateValue('BANNER_IMG', $values['BANNER_IMG']);
        Configuration::updateValue('BANNER_LINK', $values['BANNER_LINK']);
        Configuration::updateValue('BANNER_DESC', $values['BANNER_DESC']);
    }
*/
    public function uninstall()
    {
//        Configuration::deleteByName('BANNER_IMG');
  //      Configuration::deleteByName('BANNER_LINK');
    //    Configuration::deleteByName('BANNER_DESC');

        return parent::uninstall();
    }

/*
    public function renderWidget($hookName, array $params)
    {
      // if (!$this->isCached($this->templateFile, $this->getCacheId('asf_tallas'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $params));
       // }

        return $this->fetch($this->templateFile, $this->getCacheId('asf_tallas'));
    }

    public function getWidgetVariables($hookName, array $params)
    {

        //include_once('classes/Combinations.php');

        //$comb = new Combinations();

        return array(
            'combinations' => 'mierda!!!' //$comb->getProductAttributeCombinations($params['product']['id_product'])
        );
    }

*/
}
