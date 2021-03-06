<?php
class ControllerReportNewreport extends Controller {
	public function index() {  
		$this->load->language('report/newreport');
		$this->document->setTitle($this->language->get('heading_title'));
		
		date_default_timezone_set ('Europe/Paris');
		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-7, date("Y") ));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}	
				
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
						
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('report/newreport', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);		
		
		$this->load->model('report/newreport');

		$this->data['store_name'] = $this->config->get('config_name');
		
		$this->data['customers'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		$order_total1 = $this->model_report_newreport->getTotalOrders($data);
		$results = $this->model_report_newreport->getOrders($data);
	    //echo "<pre>";print_r($results);echo "</pre>";exit;
		$order_total=0;

		$shopAddress = $this->model_report_newreport->getShopAddress();

	    $this->data['shop_address'] = $shopAddress[0]['value'];
		
		foreach ($results as $result) {
			$order_total += 1;
			$action = array();
		
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
			
			// To Include the option value
			$a = explode(',', $result['options']);
			$b = explode(',', $result['ordprdid']);
			$c = explode(',', $result['optprdid']);
			$d = explode(',', $result['opquantity']);

			$i=0;
			$optionvalue='';
			if($result['options']<>''){
			foreach ($b as $option) {
				$optionvalue .= $a[$i] . '(' . $d[array_search($b[$i], $c)] . '), ' ;
				$i += 1;
			}
			}
			$optionvalue = trim($optionvalue,', ');		

			switch ($result['status']) {
				case '30mins':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+30 minutes"));
					break;

				case '45mins':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+45 minutes"));
					break;

				case '60mins':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+60 minutes"));
					break;

				case '75mins':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+75 minutes"));
					break;

				case '90mins':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+90 minutes"));
					break;

				case 'Nouveau':
					$timeArrive = date("d/m/Y H:i:s", strtotime(date("Y/m/d H:i:s", strtotime($result['date_modified']))."+30 minutes"));
					break;
				
				default:
					# code...
					break;
			}


			
						
			$this->data['customers'][] = array(
			    
				'order_id'          => $result['order_id'],		
				'customer'          => $result['customer'],
				'address'           => $result['address'],
				'city'              => $result['city'],
				'postcode'          => $result['postcode'],
				'zone'              => $result['zone'],
				'email'             => $result['email'],
				'telephone'         => $result['telephone'],
				'status'            => $result['status'] ,
				'pdtname'           => $result['pdtname'],
				'quantity'          => $result['quantity'],
				'text' => $this->language->get('text_view'),
				'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL'),
				'options'           => $optionvalue,
				'action'            => $action,
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'   => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'total'          => number_format($result['total'],2),
				'shipping_method'          => $result['shipping_method'],
				'liv'          => $result['liv']=intval($result['liv'])? "Livraison" : "Emporter",
				'couv'          => $result['couv'],
				'comment'          => $result['comment'],
				'prompt' =>$result['prompt'],
				'result'			=>$result,
				'timeArrive'		=>$timeArrive,
			);
		}
		//echo "<pre>";print_r($this->data['customers']);echo "</pre>";die;
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['store_url'] = HTTPS_CATALOG;
		} else {
			$this->data['store_url'] = HTTP_CATALOG;
		}

        if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $this->data['store_url'] . 'image/' . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';
		}	
		 
 		$this->data['heading_title'] = $this->language->get('heading_title');
		 
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_all_status'] = $this->language->get('text_all_status');
		
		$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_address'] = $this->language->get('column_address');
		$this->data['column_city'] = $this->language->get('column_city');
		$this->data['column_postcode'] = $this->language->get('column_postcode');
		$this->data['column_zone'] = $this->language->get('column_zone');
		$this->data['column_orderid'] = $this->language->get('column_orderid');
		$this->data['column_email'] = $this->language->get('column_email');
		$this->data['column_phone'] = $this->language->get('column_phone');
		$this->data['column_customer_group'] = $this->language->get('column_customer_group');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_orders'] = $this->language->get('column_orders');
		$this->data['column_products'] = $this->language->get('column_products');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_pdtname'] = $this->language->get('column_pdtname');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_options'] = $this->language->get('column_options');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['column_shipping_method'] = $this->language->get('column_shipping_method');
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_status'] = $this->language->get('entry_status');

		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			
		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
				
		$pagination = new Pagination();
		$pagination->total = $order_total1;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/newreport', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_order_status_id'] = $filter_order_status_id;
				 
		$this->template = 'report/newreport.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
	}


}
?>