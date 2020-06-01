<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminProducts extends pjAdmin
{
	private $imageFillColor = array(255, 255, 255);
	
	private $imageSizes = array(163, 115);
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP05");
			}
			if (isset($_POST['product_create']))
			{
				$pjProductModel = pjProductModel::factory();
				
				$data = array();
				$category = pjCategoryModel::factory()->find($_POST['category_id'])->getData();
				if(!empty($category['parent_id']) )
				{
					$data['parent_category_id'] = $category['parent_id'];
				}
				$order = 1;
				$_arr = $pjProductModel
					->where('t1.category_id', $_POST['category_id'])
					->orderBy("`order` DESC")
					->limit(1)
					->findAll()
					->getData();
				if(!empty($_arr))
				{
					$order = $_arr[0]['order'] + 1;
				}
				$data['order'] = $order;
				$id = $pjProductModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AP03';
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjProductPriceModel = pjProductPriceModel::factory();
					if (isset($_POST['i18n']))
					{
						$pjMultiLangModel->saveMultiLang($_POST['i18n'], $id, 'pjProduct', 'data');
						
						if($_POST['set_different_sizes'] == 'T')
						{
							if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
							{
								$index_arr = explode("|", $_POST['index_arr']);
								foreach($index_arr as $k => $v)
								{
									if(strpos($v, 'fd') !== false)
									{
										$p_data = array();
										$p_data['product_id'] = $id;
										$p_data['price'] = $_POST['product_price'][$v];
										$price_id = $pjProductPriceModel->reset()->setAttributes($p_data)->insert()->getInsertId();
										if ($price_id !== false && (int) $price_id > 0)
										{
											foreach ($_POST['i18n'] as $locale => $locale_arr)
											{
												foreach ($locale_arr as $field => $content)
												{
													if(is_array($content))
													{
														$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
															'foreign_id' => $price_id,
															'model' => 'pjProductPrice',
															'locale' => $locale,
															'field' => $field,
															'content' => $content[$v],
															'source' => 'data'
														))->insert()->getInsertId();
													}
												}
											}
										}
									}
								}
							}
						}
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
											$image_path = PJ_UPLOAD_PATH . 'products/' . $id . '_' . $hash . '.' . $Image->getExtension();
											
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
											$Image->saveImage($image_path);
												
											$pjProductModel->reset()->where('id', $id)->limit(1)->modifyAll(array('image'=>$image_path));
											
										}
									}
								}
							}else{
								$err = 'AP09';
							}
						}else if($_FILES['image']['error'] != 4){
							$err = 'AP09';
						}
					}
				
					if($err != 'AP03')
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminProducts&action=pjActionUpdate&id=$id&err=AP09");
					}
				} else {
					$err = 'AP04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminProducts&action=pjActionIndex&err=$err");
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
		
				$category_arr = pjCategoryModel::factory()
					->select("t1.id, t1.parent_id, t2.content as name,
							(CASE
							    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.id
							    WHEN t1.parent_id<>t1.id THEN t1.parent_id
							END) AS 'the_parent'")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->orderBy("the_parent ASC, t1.parent_id ASC, `name` ASC")
					->findAll()
					->getData();
				$this->set('category_arr', $category_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('pjAdminProducts.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjProductModel = pjProductModel::factory();
			$arr = $pjProductModel->find($_GET['id'])->getData();
			
			if ($pjProductModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image'])) {
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				
				pjMultiLangModel::factory()->where('model', 'pjProduct')->where('foreign_id', $_GET['id'])->eraseAll();
				pjProductPriceModel::factory()->where('product_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteProductBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjProductModel = pjProductModel::factory();
				$arr = $pjProductModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
				}
				
				$pjProductModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjProduct')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				pjProductPriceModel::factory()->whereIn('product_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjProductModel = pjProductModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.category_id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left');
			
			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjProductModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjProductModel->where("(t2.content LIKE '%$q%')");
			}
			if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
			{
				$pjProductModel->where("t1.category_id", $_GET['category_id']);
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
			{
				$column = '`order`';
				$direction = 'ASC';
			}
			$total = $pjProductModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjProductModel
				->select("t1.*, t2.content AS name, t3.content AS categories")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			$order_arr = array();
			if(isset($_GET['category_id']))
			{
				foreach($data as $k => $v)
				{
					$order_arr[] = $v['order'];
				}	
			}
			
			$pjProductPriceModel = pjProductPriceModel::factory();
			foreach($data as $k => $v)
			{
				if($v['set_different_sizes'] == 'T')
				{
					$_arr = $pjProductPriceModel
						->reset()
						->select('t1.*, t2.content as price_name')
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
						->where('product_id', $v['id'])
						->findAll()
						->getData();
					$price_arr = array();
					foreach($_arr as $price)
					{
						$price_arr[] = $price['price_name'] . ': ' . pjUtil::formatCurrencySign($price['price'], $this->option_arr['o_currency']);
					}
					$v['price'] = join("<br/>", $price_arr);
				}else{
					$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']);
				}
				if(isset($_GET['category_id']))
				{
					$v['up'] = 1;
					$v['down'] = 1;
					
					if($v['order'] == min($order_arr))
					{
						$v['up'] = 0;
					}
					if($v['order'] == max($order_arr))
					{
						$v['down'] = 0;
					}
					if(count($order_arr) == 1)
					{
						$v['up'] = 0;
						$v['down'] = 0;
					}
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
			$category_arr = pjCategoryModel::factory()
				->select("t1.id, t1.parent_id, t2.content as name, t1.order,
						(CASE
						    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.`order`
						    WHEN t1.parent_id<>t1.id THEN (SELECT t4.`order` FROM `".pjCategoryModel::factory()->getTable()."` AS `t4` WHERE t4.id=t1.parent_id)
						END) AS 'the_order'")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->orderBy("the_order ASC, t1.order ASC")
				->findAll()
				->getData();
			$this->set('category_arr', $category_arr);
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminProducts.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjProductModel = pjProductModel::factory();
			if (!in_array($_POST['column'], $pjProductModel->i18n))
			{
				$value = $_POST['value'];
				
				$pjProductModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjProduct', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP06");
			}
			if (isset($_POST['product_update']))
			{
				$pjProductModel = pjProductModel::factory();
				
				$err = 'AP01';
				
				$arr = $pjProductModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminProducts&action=pjActionIndex&err=AP08");
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
										$image_path = PJ_UPLOAD_PATH . 'products/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
										$Image->saveImage($image_path);
										$data['image'] = $image_path;
									}
								}
							}
						}else{
							$err = 'AP10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'AP10';
					}
				}
				
				if(isset($_POST['is_featured']))
				{
					$data['is_featured'] = 1;
					unset($_POST['is_featured']);
				}else{
					$data['is_featured'] = 0;
				}
				$category = pjCategoryModel::factory()->find($_POST['category_id'])->getData();
				$data['parent_category_id'] = !empty($category['parent_id']) ? $category['parent_id'] : ':NULL';
				$pjProductModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjProduct', 'data');
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjProductPriceModel = pjProductPriceModel::factory();
					
					if($_POST['set_different_sizes'] == 'T')
					{
						if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
						{
							$index_arr = explode("|", $_POST['index_arr']);
							foreach($index_arr as $k => $v)
							{
								if(strpos($v, 'fd') !== false)
								{
									$p_data = array();
									$p_data['product_id'] = $_POST['id'];
									$p_data['price'] = $_POST['product_price'][$v];
									$price_id = $pjProductPriceModel->reset()->setAttributes($p_data)->insert()->getInsertId();
									if ($price_id !== false && (int) $price_id > 0)
									{
										foreach ($_POST['i18n'] as $locale => $locale_arr)
										{
											foreach ($locale_arr as $field => $content)
											{
												if(is_array($content))
												{
													$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
														'foreign_id' => $price_id,
														'model' => 'pjProductPrice',
														'locale' => $locale,
														'field' => $field,
														'content' => $content[$v],
														'source' => 'data'
													))->insert()->getInsertId();
												}
											}
										}
									}
								}else{
									$p_data = array();
									$p_data['price'] = $_POST['product_price'][$v];
									$pjProductPriceModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
									foreach ($_POST['i18n'] as $locale => $locale_arr)
									{
										foreach ($locale_arr as $field => $content)
										{
											if(is_array($content))
											{
												$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
													VALUES (NULL, :foreign_id, :model, :locale, :field, :update_content, :source)
													ON DUPLICATE KEY UPDATE `content` = :update_content, `source` = :source;",
													$pjMultiLangModel->getTable()
												);
												$foreign_id = $v;
												$model = 'pjProductPrice';
												$source = 'data';
												$update_content = $content[$v];
												$modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'update_content', 'source'));
												if ($modelObj->getAffectedRows() > 0 || $modelObj->getInsertId() > 0)
												{
													
												}
											}
										}
									}
								}
							}
						}
						
						if(isset($_POST['remove_arr']) && $_POST['remove_arr'] != '')
						{
							$remove_arr = explode("|", $_POST['remove_arr']);
							$pjMultiLangModel->reset()->where('model', 'pjProductPrice')->whereIn('foreign_id', $remove_arr)->eraseAll();
							$pjProductPriceModel->reset()->whereIn('id', $remove_arr)->eraseAll();
						}
						$pjProductModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('price' => ':NULL'));
					}else{
						$id_arr = $pjProductPriceModel->where('product_id', $_POST['id'])->findAll()->getDataPair("id", "id");
						$pjMultiLangModel->reset()->where('model', 'pjProductPrice')->whereIn('foreign_id', $id_arr);
						$pjProductPriceModel->reset()->where('product_id', $_POST['id'])->eraseAll();
					}
				}
				
				if($err == 'AP01')
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP01");
				}else{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminProducts&action=pjActionUpdate&id=".$_POST['id']."&err=AP10");
				}
			} else {
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = pjProductModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminProducts&action=pjActionIndex&err=AP08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjProduct');
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
				
				$category_arr = pjCategoryModel::factory()
					->select("t1.id, t1.parent_id, t2.content as name,
							(CASE
							    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.id
							    WHEN t1.parent_id<>t1.id THEN t1.parent_id
							END) AS 'the_parent'")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->orderBy("the_parent ASC, t1.parent_id ASC, `name` ASC")
					->findAll()
					->getData();
				$this->set('category_arr', $category_arr);
					
				if($arr['set_different_sizes'] == 'T')
				{
					$size_arr = pjProductPriceModel::factory()->where('product_id', $_GET['id'])->findAll()->getData();
					foreach($size_arr as $k => $v)
					{
						$size_arr[$k]['i18n'] = pjMultiLangModel::factory()->getMultiLang($v['id'], 'pjProductPrice');
					}
					$this->set('size_arr', $size_arr);	
				}
					
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('pjAdminProducts.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
			{
				pjAppController::setFields($_GET['locale']);
				$category_arr = pjCategoryModel::factory()
					->select("t1.id, t1.parent_id, t2.content as name,
							(CASE
							    WHEN t1.parent_id=t1.id OR t1.parent_id IS NULL  THEN t1.id
							    WHEN t1.parent_id<>t1.id THEN t1.parent_id
							END) AS 'the_parent'")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".pjObject::escapeString($_GET['locale'])."' AND t2.field = 'name'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCategory' AND t3.locale = '".pjObject::escapeString($_GET['locale'])."' AND t3.field = 'description'", 'left')
					->orderBy("the_parent ASC, t1.parent_id ASC, `name` ASC")
					->findAll()
					->getData();
				$this->set('category_arr', $category_arr);
			}
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjProductModel = pjProductModel::factory();
			$arr = $pjProductModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				if(!empty($arr['image']))
				{					
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				
				$data = array();
				$data['image'] = ':NULL';
				$pjProductModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
	}
	
	public function pjActionSetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
	
			$pjProductModel = pjProductModel::factory();
			$arr = $pjProductModel->find($_POST['id'])->getData();
			switch ($_POST['direction'])
			{
				case 'up':
					$prev_arr = $pjProductModel->reset()->where('t1.order', $arr['order'] - 1)->where('t1.category_id', $arr['category_id'])->findAll()->getData();
					if(count($prev_arr) > 0)
					{
						$prev_arr = $prev_arr[0];
						$pjProductModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $prev_arr['order']));
						$pjProductModel->reset()->where(array('id' => $prev_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
					}
					break;
				case 'down':
					$next_arr = $pjProductModel->reset()->where('t1.order', $arr['order'] + 1)->where('t1.category_id', $arr['category_id'])->findAll()->getData();
					if(count($next_arr) > 0)
					{
						$next_arr = $next_arr[0];
						$pjProductModel->reset()->where(array('id' => $arr['id']))->limit(1)->modifyAll(array('order' => $next_arr['order']));
						$pjProductModel->reset()->where(array('id' => $next_arr['id']))->limit(1)->modifyAll(array('order' => $arr['order']));
					}
					break;
			}
		}
		exit;
	}
}
?>