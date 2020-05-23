<?php
	class category extends database{
		function __construct(){
			$this->table = 'categories';
			database::__construct();
		}

		public function addcategoryId($data,$is_die=false){
			return $this->addData($data,$is_die);
		}
		public function getcategoryId(){
			$args = array(
					'fields' => array('id','categoryname','email','password'),
					'where' => array(
						'and' => array(
							'categoryname' => 'value',
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

		public function updatecategoryById($data,$email,$is_die=false){
			$args = array(
				'where' => array(
						'or' => array(
							'id' => $id,
						)
 				)
			);
			return $this -> updateData($data,$args,$is_die);
		}

		public function deletecategoryById($email,$is_die=false){
			$args = array(
				'where' => array(
						'or' => array(
							'id' => $id,
						)
 				)
			);
			return $this -> deleteData($args,$is_die);
		}
		
	}

?>