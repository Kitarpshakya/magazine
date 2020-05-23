<?php
	class database{
		protected $conn;
		protected $stmt;
		protected $sql;
		protected $table;

		function __construct(){
			try{
				$this->conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASS);
				$this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$this->conn->exec('SET NAMES UTF8');
				return true;
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (DB Connection) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}
		}

		function runQuery($sql){
			try{
				$this->sql = $sql;
				$this->stmt = $this->conn->prepare($this->sql);
				$this->stmt->execute();
				return true;
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (run Query) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}
		}

		function getDataFromQuery($sql){
			try{
				$this->sql = $sql;
				$this->stmt = $this->conn->prepare($this->sql);
				$this->stmt->execute();
				$data = $this->stmt->fetchAll(PDO::FETCH_OBJ);
				return $data;
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (getDataFromQuery) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}
		}

		protected function addData($data,$is_die = false){
			// INSERT INTO TABLE SET
			// columnname = :cilumnname,
			// ...
			try{
			$this->sql = "INSERT INTO ";

			// table name start
			if(isset($this->table) && !empty($this->table)){
				$this->sql.=$this->table;
				$this->sql.=" SET ";
			}else{
				throw new Exception("Data cannot be inserted without table name");
			}
			// table name ends

			if(isset($data) && !empty($data)){
				if(is_array($data)){
					$col = array();
					foreach ($data as $columnname => $value) {
						$col[] = $columnname." = :".$columnname;
					}
					$this->sql.=implode(', ', $col);
				}else{
					$this->sql.=$data;
				}
			}else{
				throw new Exception("Data cannot be inserted without data");
			}

			if($is_die){
				echo $this->sql;
				exit();
			}
			$this->stmt=$this->conn->prepare($this->sql);

			// value bind

			if(isset($data) && !empty($data)){
				if(is_array($data)){
					$col = array();
					foreach ($data as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
					}
				}
			}else{
				throw new Exception("Data cannot be inserted without data");
			}
			// value bind ends
			$success = $this->stmt->execute();
			return $success;
			
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}catch(Exception $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}

		}

		protected function getData($data,$is_die=false){
			// format
			// SELECT fields(*) FROM tablename
			// WHERE
			// 	columnname = columnname,
			// 	columnname = columnname
			// ORDER by columnname ASC|DESC 
			// LIMIT offset, no_of_data
			try{
			$this->sql = "SELECT";

			// fields start
			if (isset($args['fields']) && !empty($args['fields'])){
				if(is_array($args['fields'])){
					$this->sql.= implode(', ', $args['fields']);
				}else{
					$this->sql.=$args['fields']." ";
				}

			}else{
				$this->sql.=" * ";
			}

			// fields end
			$this->sql.=" FROM ";


			// table name start
			if(isset($this->table) && !empty($this->table)){
				$this->sql.=$this->table;
			}else{
				throw new Exception("Data cannot be inserted without table name");
			}
			// table name ends

			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					$this->sql.="WHERE ";
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						$col_and = array(); 
						foreach ($$args['where']['and'] as $columnname => $value) {
							$col_and[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('and ', $col_and);
					}

					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						$col_or = array(); 
						foreach ($$args['where']['or'] as $columnname => $value) {
							$col_or[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('or ', $col_or);
					}
				}else{
					$this->sql.=$args['where'];
				}
			}
			// ordering
			if (isset($args['order']) && !empty($args['order'])){
				if ($args['order'] == 'DESC'){
					$this->sql.=" order by id DESC ";
				}else{
					$this->sql.=" order by id ASC ";
				}
			}else{
				$this->sql.=" order by id DESC ";
			}
			// ordering ends

			if (isset($args['limit']) && !empty($args['limit'])) {
				$this->sql.=" LIMIT ".$args['limit']['offset'].", ".$args['limit']['no_of_data'];
			}
			
			if($is_die){
				echo $this->sql;
				exit();
			}
			$this->stmt=$this->conn->prepare($this->sql);

			// value bind
			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						foreach ($args['where']['and'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				
					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						foreach ($args['where']['or'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				}
			}

			// value bind ends
			$this->stmt->execute();
			$data = $this->stmt->fetchAll(PDO::FETCH_OBJ);
			return $data;
			
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}catch(Exception $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}

		}

		protected function updateData($data,$is_die=false){
			try{
			$this->sql = "UPDATE ";

			// table name start
			if(isset($this->table) && !empty($this->table)){
				$this->sql.=$this->table;
				$this->sql.=" SET ";
			}else{
				throw new Exception("Data cannot be inserted without table name");
			}
			// table name ends

			if(isset($data) && !empty($data)){
				if(is_array($data)){
					$col = array();
					foreach ($data as $columnname => $value) {
						$col[] = $columnname." = :".$columnname."s";
					}
					$this->sql.=implode(', ', $col);
				}else{
					$this->sql.=$data;
				}
			}else{
				throw new Exception("Data cannot be inserted without data");
			}

			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					$this->sql.="WHERE ";
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						$col_and = array(); 
						foreach ($$args['where']['and'] as $columnname => $value) {
							$col_and[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('and ', $col_and);
					}

					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						$col_or = array(); 
						foreach ($$args['where']['or'] as $columnname => $value) {
							$col_or[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('or ', $col_or);
					}
				}else{
					$this->sql.=$args['where'];
				}
			}


			if($is_die){
				echo $this->sql;
				exit();
			}

			$this->stmt=$this->conn->prepare($this->sql);

			// value bind

			if(isset($data) && !empty($data)){
				if(is_array($data)){
					$col = array();
					foreach ($data as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname."s",$value,$param);
					}
				}
			}else{
				throw new Exception("Data cannot be inserted without data");
			}

			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						foreach ($args['where']['and'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				
					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						foreach ($args['where']['or'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				}
			}
			// value bind ends
			$success = $this->stmt->execute();
			return $success;
			
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}catch(Exception $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}

		}

		protected function deleteData($args,$is_die=false){
			try{
			$this->sql = "DELETE FROM";

			// table name start
			if(isset($this->table) && !empty($this->table)){
				$this->sql.=$this->table;
			}else{
				throw new Exception("Data cannot be inserted without table name");
			}
			// table name ends

			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					$this->sql.="WHERE ";
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						$col_and = array(); 
						foreach ($$args['where']['and'] as $columnname => $value) {
							$col_and[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('and ', $col_and);
					}

					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						$col_or = array(); 
						foreach ($$args['where']['or'] as $columnname => $value) {
							$col_or[] = $columnname." = :".$columnname;
						}
						$this->sql.=implode('or ', $col_or);
					}
				}else{
					$this->sql.=$args['where'];
				}
			}
			
			if($is_die){
				echo $this->sql;
				exit();
			}
			$this->stmt=$this->conn->prepare($this->sql);

			// value bind
			if (isset($args['where']) && !empty($args['where'])) {
				if (is_array($args['where'])){
					if(isset($args['where']['and']) && !empty($args['where']['and'])){
						foreach ($args['where']['and'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				
					if(isset($args['where']['or']) && !empty($args['where']['or'])){
						foreach ($args['where']['or'] as $columnname => $value) {
						if (is_int($value)){
							$param = PDO::PARAM_INT;
						}elseif(is_bool($value)){
							$param = PDO::PARAM_BOOL;
						}else{
							$param = PDO::PARAM_STR;
						}
						$this->stmt->bindValue(":".$columnname,$value,$param);
						}
					}
				}
			}

			// value bind ends
			$this->stmt->execute();
			return true;
			}catch(PDOException $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}catch(Exception $e){
				error_log(Date("M d,Y h:i:s a").' : (addData) : '.$e->getMessage()."\r\n",3,ERROR_PATH.'error.log');
				return false;
			}
		}

}


?>