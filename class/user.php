<?php
	class user extends database{
		function __construct(){
			$this->table = 'users';
			database::__construct();
		}

		public function addUserId($data,$is_die=false){
			return $this->addData($data,$is_die);
		}
		public function getUserId(){
			$args = array(
					'fields' => array('id','username','email','password'),
					'where' => array(
						'and' => array(
							'username' => 'value',
						)
						// 'or' => array(
						// 	columnname => value,
						// 	columnname => value,
						// )
					),
					'order' => 'ASC',
					'limit' => array(
								'offset' => 6,
								'no_of_data' => 7
								)
			);
			return $this->getData($args,true);
		}

		public function updateUserByEmail($data,$email,$is_die=false){
			$args = array(
				'where' => array(
						'or' => array(
							'email' => $email,
						)
 				)
			);
			return $this -> updateData($data,$args,$is_die);
		}

		public function deleteUserByEmail($email,$is_die=false){
			$args = array(
				'where' => array(
						'or' => array(
							'email' => $email,
						)
 				)
			);
			return $this -> deleteData($args,$is_die);
		}
		
	}

// public function getUser(){
			// $args = array(
			// 		'fields' => array('id','username','email','password');
			// 		'where' => array(
			// 			'and' => array(
			// 				columnname => value,
			// 				columnname => value,
			// 			),
			// 			'or' => array(
			// 				columnname => value,
			// 				columnname => value,
			// 			)
			// 		)
			// 		'order' => 'ASC|DESC',
			// 		'limit' => array(
			// 					'offset' => 6,
			// 					'no_of_data' => 7
			// 					)
			// );
		// }
?>