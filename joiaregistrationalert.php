<?php

/**
 * copyright Joia Software Solutions [https://www.joiasoftware.it]
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Commercial Licence Copyright
 * You can modifify this and use only on the site declared when you bought it.
 *
 *    @author    Joia Software Solutions <ticket@joiasoftware.it>
 *    @copyright Joia Software Solutions - Italy
 *    @license   Commercial Licence
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Joiaregistrationalert extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'joiaregistrationalert';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Joia Software Solutions';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Notify me on customer registration');
        $this->description = $this->l('Send email notify on customer registration');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionCustomerAccountAdd');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $custInfo = $params['newCustomer'];		
		if (empty($custInfo))
			return false;		
		if (version_compare(_PS_VERSION_, '1.6', '>=')) 
		{
			$country_name  = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		} else {
            return false;
        }
   
        $emailVars = array(
            '{firstname}' => $custInfo->firstname
            ,'{lastname}' => $custInfo->lastname
            ,'{email}' => $custInfo->email
            ,'{shop_name}' => Configuration::get('PS_SHOP_NAME')
            ,'{country}' => $country_name
            ,'{company}' => $custInfo->company
            ,'{birthday}' => $custInfo->months.'-'.$custInfo->days.'-'.$custInfo->years
        );
        
        $template = 'joiaregistrationalert';

        Mail::Send((int)(Configuration::get('PS_LANG_DEFAULT')),$template,Mail::l('New Customer Account Registration'),$emailVars,
            strval(Configuration::get('PS_SHOP_EMAIL')),NULL,strval(Configuration::get('PS_SHOP_EMAIL')),strval(Configuration::get('PS_SHOP_NAME')),NULL,NULL,dirname(__FILE__).'/mails/');
			return;
    }
}