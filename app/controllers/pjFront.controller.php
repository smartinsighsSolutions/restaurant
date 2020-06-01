<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjMenuMaker_Captcha';
	
	public $defaultLocale = 'pjMenuMaker_LocaleId';
	
	public $defaultLangMenu = 'pjMenuMaker_LangMenu';
	
	public $defaultStore = 'pjMenuMaker_Store';
	
	public $defaultForm = 'pjMenuMaker_Form';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		self::allowCORS();
	}

	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
	
	private function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$key];
		}
		return false;
	}
	
	private function _is($key)
	{
		return isset($_SESSION[$this->defaultStore]) && isset($_SESSION[$this->defaultStore][$key]);
	}
	
	private function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$key] = $value;
		return $this;
	}
	
	public function afterFilter()
	{		
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionMenu', 'pjActionOffers', 'pjActionProducts', 'pjActionOfferDetails')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();			
			$this->set('locale_arr', $locale_arr);
		}
		$category_arr = pjCategoryModel::factory()
			->select("t1.id, t1.parent_id, t1.image, t2.content as name, t3.content as description")
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
			->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
			->where("t1.parent_id IS NULL")
			->orderBy("`order` ASC")
			->findAll()
			->getData();
		$this->set('category_arr', $category_arr);
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
		}
			
		if ($this->pjActionGetLocale() === FALSE)
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
		if (!in_array($_GET['action'], array('pjActionLoadCss')))
		{
			$this->loadSetFields();
		}
	}
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				$this->loadSetFields(true);
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		header("Cache-Control: max-age=3600, private");
		$Captcha = new pjCaptcha(PJ_WEB_PATH.'obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage(PJ_IMG_PATH.'button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
		exit;
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		$theme = isset($_GET['theme']) ? $_GET['theme'] : $this->option_arr['o_theme'];
		if((int) $theme > 0)
		{
			$theme = 'theme' . $theme;
		}
		$arr = array(
			array('file' => 'font-awesome.min.css', 'path' => $dm->getPath('font_awesome') . 'css/'),
			array('file' => 'pjMenuBuilder.css', 'path' => PJ_CSS_PATH),
			array('file' => "$theme.css", 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '../fonts/fontawesome', '../fonts/', 'images/', "pjWrapper"),
					array(
						PJ_INSTALL_URL . PJ_IMG_PATH,
						PJ_INSTALL_URL . $dm->getPath('font_awesome') . 'fonts/fontawesome',
						PJ_INSTALL_URL . 'app/web/fonts/',
						PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/',
						"pjWrapperMenuBuilder_" . $theme
					),
					$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		if(isset($_GET['locale']) && $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
			$this->loadSetFields(true);
		}
	}
	
	public function pjActionMenu()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			
		}
	}
	
	public function pjActionOffers()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$arr = pjOfferModel::factory()
				->select('t1.*, t2.content as name')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjOffer' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->where('t1.status', 'T')
				->orderBy("`name` ASC")
				->findAll()
				->getData();

			$this->set('arr', $arr);
		}
	}
	
	public function pjActionProducts()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if(isset($_GET['cid']) && (int) $_GET['cid'] > 0)
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$pjProductModel = pjProductModel::factory();
				
				$category = $pjCategoryModel
					->select('t1.*, t2.content as name, t3.content as description')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->find($_GET['cid'])
					->getData();
				
				$sub_categories = $pjCategoryModel
					->reset()
					->select('t1.*, t2.content as name')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->where('t1.parent_id', $_GET['cid'])
					->orderBy("t1.order ASC")
					->findAll()
					->getData();
				
				$tpp_table = pjProductPriceModel::factory()->getTable();
				$tm_table = pjMultiLangModel::factory()->getTable();
				
				$sub_arr = array();
				$main_arr = $pjProductModel
					->select("t1.*, t2.content as name, t3.content as description, 
							(SELECT GROUP_CONCAT(concat(TPP.price,'~|~',TM.content) SEPARATOR '~:~') FROM `".$tpp_table."` AS `TPP` LEFT OUTER JOIN `".$tm_table."` AS TM ON (TM.foreign_id = TPP.id AND TM.model = 'pjProductPrice' AND TM.locale = '".$this->getLocaleId()."' AND TM.field = 'price_name') WHERE TPP.product_id=t1.id) AS prices")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjProduct' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->where('t1.status', 'T')
					->where("t1.category_id", $_GET['cid'])
					->where("t1.parent_category_id IS NULL")
					->orderBy("`order` ASC")
					->findAll()
					->toArray("prices", "~:~")
					->getData();
				
				if(count($sub_categories) > 0)
				{
					
					$_arr = $pjProductModel
						->reset()
						->select("t1.*, t2.content as name, t3.content as description,
							(SELECT GROUP_CONCAT(concat(TPP.price,'~|~',TM.content) SEPARATOR '~:~') FROM `".$tpp_table."` AS `TPP` LEFT OUTER JOIN `".$tm_table."` AS TM ON (TM.foreign_id = TPP.id AND TM.model = 'pjProductPrice' AND TM.locale = '".$this->getLocaleId()."' AND TM.field = 'price_name') WHERE TPP.product_id=t1.id) AS prices")
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
						->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjProduct' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
						->where('t1.status', 'T')
						->where("t1.parent_category_id", $_GET['cid'])
						->orderBy("`order` ASC")
						->findAll()
						->toArray("prices", "~:~")
						->getData();
					
					foreach($_arr as $v)
					{
						if(!empty($v['category_id']))
						{
							$sub_arr[$v['category_id']][] = $v;
						}
					}
				}
				
				$this->set('main_arr', $main_arr);
				$this->set('sub_arr', $sub_arr);
				$this->set('category', $category);
				$this->set('sub_categories', $sub_categories);
			}
		}
	}
	
	public function pjActionOfferDetails()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if(isset($_GET['offer_id']) && (int) $_GET['offer_id'] > 0)
			{
				$arr = pjOfferModel::factory()
					->select('t1.*, t2.content as name, t3.content as description')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjOffer' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjOffer' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->find($_GET['offer_id'])
					->getData();
				
				$product_arr = pjProductModel::factory()
					->select("t1.*, t2.content as name, t3.content as description, t4.content as category")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjProduct' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->join('pjMultiLang', "t4.foreign_id = t1.category_id AND t4.model = 'pjCategory' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
					->where("t1.id IN (SELECT t4.product_id FROM `".pjOfferProductModel::factory()->getTable()."` AS t4 WHERE t4.offer_id = '".$_GET['offer_id']."')")
					->where('t1.status', 'T')
					->orderBy("`name` ASC")
					->findAll()
					->getData();
				
				$this->set('arr', $arr);
				$this->set('product_arr', $product_arr);
			}
		}
	}
}
?>