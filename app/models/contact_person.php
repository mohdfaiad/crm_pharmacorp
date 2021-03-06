<?php
class ContactPerson extends AppModel {
	var $name = 'ContactPerson';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Purchaser');
	
	var $hasOne = array(
		'Address' => array(
			'dependent' => true
		)
	);
	
	var $hasMany = array(
		'Anniversary' => array(
			'dependent' => true
		),
		'BusinessSessionsContactPerson', // kontaktni osoba prizvana na jednani,
		'Contract' // dohody se vystavuji na kontaktni osoby
	);
	
	var $validate = array(
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Příjmení musí být vyplněno'
			)
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Telefon musí být zadán'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Zadejte platnou emailovou adresu'
			)
		),
		'purchaser_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není vybrán odběratel'
		)
	);
	
	var $virtualFields = array(
		'name' => 'TRIM(CONCAT(ContactPerson.degree_before, " ", ContactPerson.first_name, " ", ContactPerson.last_name, " ", ContactPerson.degree_after))'
	);
	
	var $export_file = 'contact_people.csv';
	
	function setExportFields() {
		$this->export_fields = array(
			array('field' => 'Purchaser.id', 'position' => '["Purchaser"]["id"]', 'alias' => 'Purchaser.id'),
			array('field' => $this->Purchaser->User->full_name . ' AS full_name', 'position' => '[0]["full_name"]', 'alias' => 'User.fullname'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => $this->Purchaser->virtualFields['name'] . ' AS purchaser_name', 'position' => '[0]["purchaser_name"]', 'alias' => 'Purchaser.name'),
			array('field' => 'ContactPerson.first_name', 'position' => '["ContactPerson"]["first_name"]', 'alias' => 'ContactPerson.first_name'),
			array('field' => 'ContactPerson.last_name', 'position' => '["ContactPerson"]["last_name"]', 'alias' => 'ContactPerson.last_name'),
			array('field' => 'ContactPerson.degree_before', 'position' => '["ContactPerson"]["degree_before"]', 'alias' => 'ContactPerson.degree_before'),
			array('field' => 'ContactPerson.phone', 'position' => '["ContactPerson"]["phone"]', 'alias' => 'ContactPerson.phone'),
			array('field' => 'ContactPerson.cellular', 'position' => '["ContactPerson"]["cellular"]', 'alias' => 'ContactPerson.cellular'),
			array('field' => 'ContactPerson.email', 'position' => '["ContactPerson"]["email"]', 'alias' => 'ContactPerson.email'),
			array('field' => 'ContactPerson.note', 'position' => '["ContactPerson"]["note"]', 'alias' => 'ContactPerson.note'),
		);
	}
	
	function beforeSave() {
		if (array_key_exists('birthday', $this->data['ContactPerson'])) {
			if (preg_match('/^\d{1,2}\.\d{1,2}\.\d{1,4}$/', $this->data['ContactPerson']['birthday'])) {
				$date = explode('.', $this->data['ContactPerson']['birthday']);
				$this->data['ContactPerson']['birthday'] = $date[2] . '-' . $date[1] . '-' . $date[0];
			} elseif (empty($this->data['ContactPerson']['birthday'])) {
				$this->data['ContactPerson']['birthday'] = null;
			}
		}
		return true;
	}
	
	function delete($id) {
		$save = array(
			'ContactPerson' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->save($save)) {
			$this->Anniversary->deleteAll(array(
				'contact_person_id' => $id
			));
			$this->BusinessSessionsContactPerson->deleteAll(array(
				'contact_person_id' => $id
			));
			return true;
		}
		
		return false;
	}
	
	function afterFind($results) {
		if (isset($results['id'])) {
			$salutation = '';
			if (!empty($results['last_name'])) {
				$salutation = $results['last_name'];
			}
			if (!empty($results['first_name'])) {
				$salutation = $results['first_name'] . ' ' . $salutation;
			}
			if (!empty($results['prefix'])) {
				$salutation = $results['prefix'] . ' ' . $salutation;
			}
			$results['salutation'] = $salutation;
		} else {
			if (is_array($results)) {
				foreach ($results as $index => $result) {
					if (!isset($result['ContactPerson'])) {
						break;
					}
					$salutation = '';
					if (!empty($result['ContactPerson']['last_name'])) {
						$salutation = $result['ContactPerson']['last_name'];					
					}
					if (!empty($result['ContactPerson']['first_name'])) {
						$salutation = $result['ContactPerson']['first_name'] . ' ' . $salutation;
					}
					if (!empty($result['ContactPerson']['prefix'])) {
						$salutation = $result['ContactPerson']['prefix'] . ' ' . $salutation;
					}
					$results[$index]['ContactPerson']['salutation'] = $salutation;
				}
			}
		}
		return $results;
	}
	
	function autocomplete_list($user, $term = null, $business_partner_id = null, $purchaser_id = null) {
		$conditions = array(
			'ContactPerson.active' => true,
			'Purchaser.active' => true,
			'BusinessPartner.active' => true
		);
		if ($user['User']['user_type_id'] == 3) {
			$conditions = array('Purchaser.user_id' => $user['User']['id']);
		}
		if ($term) {
			$conditions['ContactPerson.name LIKE'] = '%' . $term . '%';
		}
		if ($business_partner_id) {
			$conditions['Purchaser.business_partner_id'] = $business_partner_id;
		}
		if ($purchaser_id) {
			$conditions['ContactPerson.purchaser_id'] = $purchaser_id;
		}

		$contact_people = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'purchasers',
					'alias' => 'Purchaser',
					'type' => 'LEFT',
					'conditions' => array('Purchaser.id = ContactPerson.purchaser_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array('BusinessPartner.id = Purchaser.business_partner_id')
				)
			),
			'order' => array('ContactPerson.name' => 'asc'),
			'fields' => array('ContactPerson.id', 'ContactPerson.name', 'Purchaser.*', 'ContactPerson.bank_account')
		));

		$autocomplete_contact_people = array();
		foreach ($contact_people as $contact_person) {
			$autocomplete_contact_people[] = array(
				'label' => $this->autocomplete_field_info($contact_person['ContactPerson']['id']),
				'value' => $contact_person['ContactPerson']['id'],
				'bank_account' => $contact_person['ContactPerson']['bank_account']
			);
		}
		return json_encode($autocomplete_contact_people);
	}
	
	function autocomplete_field_info($id) {
		$contact_person = $this->find('first', array(
			'conditions' => array('ContactPerson.id' => $id),
			'contain' => array(
				'Address' => array(
					'fields' => array(
						'Address.id',
						'Address.street',
						'Address.number',
						'Address.city',
						'Address.zip',
					)
				),
				'Purchaser'
			),
			'fields' => array(
				'ContactPerson.id',
				'ContactPerson.name',
			)
		));
	
		if (empty($contact_person)) {
			return false;
		}
	
		return $contact_person['ContactPerson']['name'] . ', ' . $contact_person['Address']['street'] . ' ' . $contact_person['Address']['number'] . ', ' . $contact_person['Address']['city'] . ', ' . $contact_person['Address']['zip'];
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['Purchaser']['name'])) {
			$conditions[] = $this->Purchaser->virtualFields['name'] . ' LIKE \'%%' . $data['Purchaser']['name'] . '%%\'';
		}
		if (!empty($data['Purchaser']['icz'])) {
			$conditions[] = 'Purchaser.icz LIKE \'%%' . $data['Purchaser']['icz'] . '%%\'';
		}
		if (!empty($data['Purchaser']['user_id'])) {
			$conditions[] = array('Purchaser.user_id' => $data['Purchaser']['user_id']);
		}
		if (!empty($data['PurchaserAddress']['street'])) {
			$conditions[] = 'PurchaserAddress.street LIKE \'%%' . $data['PurchaserAddress']['street'] . '%%\'';
		}
		if (!empty($data['PurchaserAddress']['city'])) {
			$conditions[] = 'PurchaserAddress.city LIKE \'%%' . $data['PurchaserAddress']['city'] . '%%\'';
		}
		if (!empty($data['PurchaserAddress']['region'])) {
			$conditions[] = 'PurchaserAddress.region LIKE \'%%' . $data['PurchaserAddress']['region'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['first_name'])) {
			$conditions[] = 'ContactPerson.first_name LIKE \'%%' . $data['ContactPerson']['first_name'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['last_name'])) {
			$conditions[] = 'ContactPerson.last_name LIKE \'%%' . $data['ContactPerson']['last_name'] . '%%\'';
		}
		if (!empty($data['ContactPersonAddress']['street'])) {
			$conditions[] = 'ContactPersonAddress.street LIKE \'%%' . $data['ContactPersonAddress']['street'] . '%%\'';
		}
		if (!empty($data['ContactPersonAddress']['city'])) {
			$conditions[] = 'ContactPersonAddress.city LIKE \'%%' . $data['ContactPersonAddress']['city'] . '%%\'';
		}
		if (!empty($data['ContactPersonAddress']['region'])) {
			$conditions[] = 'ContactPersonAddress.region LIKE \'%%' . $data['ContactPersonAddress']['region'] . '%%\'';
		}
		
		return $conditions;
	}
	
	function get_business_partner($id) {
		$business_partner = $this->find('first', array(
			'conditions' => array('ContactPerson.id' => $id),
			'contain' => array(),
			'fields' => array('BusinessPartner.*'),
			'joins' => array(
				array(
					'table' => 'purchasers',
					'alias' => 'Purchaser',
					'type' => 'inner',
					'conditions' => array('Purchaser.id = ContactPerson.purchaser_id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'inner',
					'conditions' => array('BusinessPartner.id = Purchaser.business_partner_id')
				)
			)
		));
		
		return $business_partner;
	}
	
	function get_purchaser($id) {
		$purchaser = $this->find('first', array(
			'conditions' => array('ContactPerson.id' => $id),
			'contain' => array(),
			'fields' => array('Purchaser.*'),
			'joins' => array(
				array(
					'table' => 'purchasers',
					'alias' => 'Purchaser',
					'type' => 'inner',
					'conditions' => array('Purchaser.id = ContactPerson.purchaser_id')
				)
			)
		));
	
		return $purchaser;
	}
}
