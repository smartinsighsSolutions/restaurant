<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOffers extends pjAdmin
{
	private $imageFillColor = array(255, 255, 255);
	
	private $imageSizes = array(575, 228);
	
	public function pjActionCheckOffer()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_POST['locale']))
		{
			$locale = $_POST['locale'];
			
			$value = $_POST['i18n'][$locale]['name'];
			
			$pjOfferModel = pjOfferModel::factory();
			
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjOfferModel->where('t1.id !=', $_POST['id']);
			}
			$pjOfferModel->where("t1.id IN(SELECT TL.foreign_id FROM `".pjMultiLangModel::factory()->getTable()."` AS TL WHERE TL.model='pjOffer' AND TL.field='name' AND TL.content = '".$value."' AND TL.locale='$locale')");
			echo $pjOfferModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminOffers&action=pjActionIndex&err=ASO05");
			}
			if (isset($_POST['offer_create']))
			{
				$pjOfferModel = pjOfferModel::factory();
				
				$id = $pjOfferModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ASO03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjOffer', 'data');
					}
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							if(getimagesize($_FILES['image']["tmp_name"]) != false)
							{
								$Image = new pjImage();
								if ($Image->getErrorCode() !== 200)
								{
									$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
									if ($Image->load($_FILES['image']))
									{
										$resp = $Image->isConvertPossible();
										if ($resp['status'] === true)
										{
											$hash = md5(uniqid(rand(), true));
											$image_path = PJ_UPLOAD_PATH . 'offers/' . $id . '_' . $hash . '.' . $Image->getExtension();
																							
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
											$Image->saveImage($image_path);
					
											$pjOfferModel->reset()->where('id', $id)->limit(1)->modifyAll(array('image'=>$image_path));
												
										}
									}
								}
							}else{
								$err = 'ASO09';
							}
						}else if($_FILES['image']['error'] != 4){
							$err = 'ASO09';
						}
					}
					
				} else {
					$err = 'ASO04';
				}
				
				$pjOfferProductModel = pjOfferProductModel::factory();
				if (isset($_POST['product_id']) && is_array($_POST['product_id']) && count($_POST['product_id']) > 0)
				{
					$pjOfferProductModel->begin();
					foreach ($_POST['product_id'] as $index => $product_id)
					{
						$size_id = ":NULL";
						if(isset($_POST['size_id'][$index]) && $_POST['size_id'][$index] != '')
						{
							$size_id = $_POST['size_id'][$index];
						}
						$pjOfferProductModel
							->reset()
							->set('offer_id', $id)
							->set('product_id', $product_id)
							->set('size_id', $size_id)
							->insert();
					}
					$pjOfferProductModel->commit();
				}
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOffers&action=pjActionIndex&err=$err");
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$order_arr = array();
				$product_arr = array();
				$category_arr = pjCategoryModel::factory()
					->select("t1.id, t1.parent_id, t1.order,
						(CASE
						    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.`order`
						    WHEN t1.parent_id<>t1.id THEN (SELECT t4.`order` FROM `".pjCategoryModel::factory()->getTable()."` AS `t4` WHERE t4.id=t1.parent_id)
						END) AS 'the_order'")
					->orderBy("the_order ASC, t1.order ASC")
					->findAll()
					->getDataPair(null, 'id');
				
				foreach($category_arr as $k => $v)
				{
					$order_arr[$v] = $k;
				}
				$_arr = pjProductModel::factory()
					->select('t1.*, t2.content as name, t3.content as category, t4.content as parent_category')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.category_id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
					->join('pjMultiLang', "t4.foreign_id = t1.parent_category_id AND t4.model = 'pjCategory' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
					->where('t1.status', 'T')
					->orderBy("name ASC")
					->findAll()
					->getData();
				foreach($_arr as $k => $v)
				{
					$product_arr[$order_arr[$v['category_id']]][] = $v;
				}
				ksort($product_arr);
				$this->set('product_arr', $product_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminOffers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteOffer()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjOfferModel = pjOfferModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$arr = $pjOfferModel->find($_GET['id'])->getData();
				
			if ($pjOfferModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image'])) {
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				$pjMultiLangModel->where('model', 'pjOffer')->where('foreign_id', $_GET['id'])->eraseAll();
				pjOfferProductModel::factory()->where('offer_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteOfferBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjOfferModel = pjOfferModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = $pjOfferModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
				}
				$pjOfferModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				$pjMultiLangModel->reset()->where('model', 'pjOffer')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjOfferProductModel::factory()->whereIn('offer_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetOffer()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOfferModel = pjOfferModel::factory()
				->select("t1.*, t2.content as name, (SELECT COUNT(*) FROM `".pjOfferProductModel::factory()->getTable()."` AS TOP WHERE TOP.offer_id=t1.id) AS cnt_products")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjOffer' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjOfferModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjOfferModel->where('t1.status', $_GET['status']);
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
						
			$total = $pjOfferModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjOfferModel
				->limit($rowCount, $offset)
				->orderBy("`$column` $direction")
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				if($v['price'] != '')
				{
					$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']);
				}
				$data[$k] = $v;
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminOffers.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveOffer()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOfferModel = pjOfferModel::factory();
			if (!in_array($_POST['column'], $pjOfferModel->getI18n()))
			{
				$pjOfferModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjOffer', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		$post_max_size = pjUtil::getPostMaxSize();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminOffers&action=pjActionIndex&err=ASO06");
		}
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['offer_update']))
			{
				$pjOfferModel = pjOfferModel::factory();
				
				$err = 'ASO01';
				
				$arr = $pjOfferModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOffers&action=pjActionIndex&err=ASO08");
				}
				
				$data = array();
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						if(getimagesize($_FILES['image']["tmp_name"]) != false)
						{
							if(!empty($arr['image']))
							{
								@unlink(PJ_INSTALL_PATH . $arr['image']);
							}
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$image_path = PJ_UPLOAD_PATH . 'offers/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
				
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
										$Image->saveImage($image_path);
										$data['image'] = $image_path;
									}
								}
							}
						}else{
							$err = 'ASO10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'ASO10';
					}
				}
				
				$pjOfferModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjOffer', 'data');
				}
				
				$pjOfferProductModel = pjOfferProductModel::factory();
				$pjOfferProductModel->where('offer_id', $_POST['id'])->eraseAll();
				if (isset($_POST['product_id']) && is_array($_POST['product_id']) && count($_POST['product_id']) > 0)
				{
					$pjOfferProductModel->reset()->begin();
					foreach ($_POST['product_id'] as $index => $product_id)
					{
						$size_id = ":NULL";
						if(isset($_POST['size_id'][$index]) && $_POST['size_id'][$index] != '')
						{
							$size_id = $_POST['size_id'][$index];
						}
						$pjOfferProductModel
							->reset()
							->set('offer_id', $_POST['id'])
							->set('product_id', $product_id)
							->set('size_id', $size_id)
							->insert();
					}
					$pjOfferProductModel->commit();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminOffers&action=pjActionIndex&err=" . $err);
				
			} else {
				$arr = pjOfferModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOffers&action=pjActionIndex&err=ASO08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjOffer');
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$order_arr = array();
				$product_arr = array();
				$category_arr = pjCategoryModel::factory()
					->select("t1.id, t1.parent_id, t1.order,
						(CASE
						    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.`order`
						    WHEN t1.parent_id<>t1.id THEN (SELECT t4.`order` FROM `".pjCategoryModel::factory()->getTable()."` AS `t4` WHERE t4.id=t1.parent_id)
						END) AS 'the_order'")
					->orderBy("the_order ASC, t1.order ASC")
					->findAll()
					->getDataPair(null, 'id');
				
				foreach($category_arr as $k => $v)
				{
					$order_arr[$v] = $k;
				}
				$_arr = pjProductModel::factory()
					->select('t1.*, t2.content as name, t3.content as category, t4.content as parent_category')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.category_id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
					->join('pjMultiLang', "t4.foreign_id = t1.parent_category_id AND t4.model = 'pjCategory' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
					->where('t1.status', 'T')
					->orderBy("name ASC")
					->findAll()
					->getData();
				foreach($_arr as $k => $v)
				{
					$product_arr[$order_arr[$v['category_id']]][] = $v;
				}
				ksort($product_arr);
				$this->set('product_arr', $product_arr);
				
				$product_id_arr = pjOfferProductModel::factory()
					->where("offer_id", $_GET['id'])
					->findAll()
					->getDataPair("product_id", "size_id");
				
				$size_arr = array();
				$pjProductPriceModel = pjProductPriceModel::factory();
				foreach($product_id_arr as $product_id => $size_id)
				{
					if(!empty($size_id))
					{
						$size_arr[$size_id] = $pjProductPriceModel
							->reset()
							->select('t1.*, t2.content as size')
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
							->where('t1.product_id', $product_id)
							->findAll()
							->getData();
					}
				}
				
				$this->set('product_id_arr', $product_id_arr);
				$this->set('size_arr', $size_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminOffers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
				
			$pjOfferModel = pjOfferModel::factory();
			$arr = $pjOfferModel->find($_GET['id'])->getData();
				
			if(!empty($arr))
			{
				if(!empty($arr['image']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
	
				$data = array();
				$data['image'] = ':NULL';
				$pjOfferModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
	
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
				
			pjAppController::jsonResponse($response);
		}
	}
	
	public function pjActionGetSizes()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$size_arr = pjProductPriceModel::factory()
				->select("t1.*, t2.content as size")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
				->where('t1.product_id', $_GET['product_id'])
				->findAll()
				->getData();
			$this->set('size_arr', $size_arr);
		}
	}
}
?>