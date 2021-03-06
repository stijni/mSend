<?php
/**
 * Class that handles all the actions and functions that can be applied to
 * files categories.
 *
 * @package		ProjectSend
 * @subpackage	Classes
 */

class CategoriesActions
{

	var $ategory = '';

	function __construct() {
		global $dbh;
		$this->dbh = $dbh;
	}

	/**
	 * Validate the information from the form.
	 */
	function validate_category($arguments)
	{
		require(ROOT_DIR.'/includes/vars.php');

		//global $valid_me;
		$this->state = array();

		$this->name			= $arguments['name'];
		$this->parent_id	= $arguments['parent'];
		$this->description	= $arguments['description'];
		$this->parent_status=NULL;
		if($this->parent_id==0){
			$editing= $this->dbh->prepare("SELECT * FROM " . TABLE_CATEGORIES . " WHERE parent IS NULL AND name=:name");
		}else{
			$editing= $this->dbh->prepare("SELECT * FROM " . TABLE_CATEGORIES . " WHERE parent=:parent AND name=:name");
			$editing->bindParam(':parent', $this->parent_id, PDO::PARAM_INT);
		}
		$editing->bindParam(':name', $this->name, PDO::PARAM_INT);
		$editing->execute();
		$editing->setFetchMode(PDO::FETCH_ASSOC);
		$this->duplicate=$editing->fetch();
		if($this->duplicate==false){
			if (!empty($this->name)) {
				return 1;
			}
			else {
				return 0;
			}
		}else{
			return 2;
		}



	}


	/**
	 * Save or create, according the the ACTION parameter
	 */
	function save_category($arguments)
	{
		$this->state = array();

		/** Define the information to use */
		$this->action		= $arguments['action'];
		$this->name			= $arguments['name'];
		$this->parent_id	= $arguments['parent'];
		$this->description	= $arguments['description'];
		
		switch ( $this->action ) {
			case 'add':

				/** Who is creating the category? */
				$this->created_by = CURRENT_USER_USERNAME;
		
				/** Insert the category information into the database */
				$this->sql_query = $this->dbh->prepare("INSERT INTO " . TABLE_CATEGORIES . " (name,parent,description,created_by)"
													."VALUES (:name, :parent, :description, :created_by)");
				$this->sql_query->bindParam(':name', $this->name);
				
				if ( $this->parent_id == '0' ) {
					$this->parent_id == null;
					$this->sql_query->bindValue(':parent', $this->parent_id, PDO::PARAM_NULL);
				}
				else {
					$this->sql_query->bindValue(':parent', $this->parent_id, PDO::PARAM_INT);
				}

				
				$this->sql_query->bindParam(':description', $this->description);
				$this->sql_query->bindParam(':created_by', $this->created_by);
		
				$this->sql_query->execute();
		
				if ($this->sql_query) {
					$this->state['query']	= 1;
					$this->state['new_id']	= $this->dbh->lastInsertId();


					/** Record the action log */
					$new_log_action = new LogActions();
					$log_action_args = array(
											'action'				=> 34,
											'owner_id'				=> CURRENT_USER_ID,
											'affected_account'		=> $this->state['new_id'],
											'affected_account_name'	=> $this->name
										);
					$new_record_action = $new_log_action->log_action_save($log_action_args);
				}
				else {
					/** Query couldn't be executed */
					$this->state['query'] = 0;
				}
				break;

			case 'edit':
				$this->id = $arguments['id'];
				/** SQL query */
				$this->edit_category_query = "UPDATE " . TABLE_CATEGORIES . " SET 
											name = :name,
											parent = :parent,
											description = :description
											WHERE id = :id
											";
	
	
				$this->sql_query = $this->dbh->prepare( $this->edit_category_query );
				$this->sql_query->bindParam(':name', $this->name);
				if ( $this->parent_id == '0' ) {
					$this->parent_id == null;
					$this->sql_query->bindValue(':parent', $this->parent_id, PDO::PARAM_NULL);
				}
				else {
					$this->sql_query->bindValue(':parent', $this->parent_id, PDO::PARAM_INT);
				}
				$this->sql_query->bindParam(':description', $this->description);
				$this->sql_query->bindParam(':id', $this->id, PDO::PARAM_INT);
	
				$this->sql_query->execute();
	
		
				if ($this->sql_query) {
					$this->state['query'] = 1;

					/** Record the action log */
					$new_log_action = new LogActions();
					$log_action_args = array(
											'action'				=> 35,
											'owner_id'				=> CURRENT_USER_ID,
											'affected_account'		=> $arguments['id'],
											'affected_account_name'	=> $this->name
										);
					$new_record_action = $new_log_action->log_action_save($log_action_args);
				}
				else {
					$this->state['query'] = 0;
				}
				break;
		}


		return $this->state;
	}

	/**
	 * Delete an existing category.
	 */
	function delete_category($cat_id) {
		$this->check_level = array(9,8,7);
		if (isset($cat_id)) {
			/** Do a permissions check */
			if (isset($this->check_level) && in_session_or_cookies($this->check_level)) {
				$this->sql = $this->dbh->prepare('DELETE FROM ' . TABLE_CATEGORIES . ' WHERE id=:id');
				$this->sql->bindParam(':id', $cat_id, PDO::PARAM_INT);
				$this->sql->execute();
			}
		}
	}

	/**
	 * Validate the information from the category edit form.
	 */
	function validate_categoryedit($arguments)
	{
		require(ROOT_DIR.'/includes/vars.php');

		//global $valid_me;
		$this->state = array();

		$this->name			= $arguments['name'];
		$this->parent_id	= $arguments['parent'];
		$this->description	= $arguments['description'];
		$this->id	= $arguments['id'];
		// var_dump($arguments);die();
		if($this->parent_id==0){
			$editing= $this->dbh->prepare("SELECT * FROM " . TABLE_CATEGORIES . " WHERE name=:name AND id!=:id");
			$editing->bindParam(':id', $this->id, PDO::PARAM_INT);
		}else{
			$editing= $this->dbh->prepare("SELECT * FROM " . TABLE_CATEGORIES . " WHERE parent=:parent AND name=:name AND id!=:id");
			$editing->bindParam(':parent', $this->parent_id, PDO::PARAM_INT);
			$editing->bindParam(':id', $this->id, PDO::PARAM_INT);
		}
		$editing->bindParam(':name', $this->name, PDO::PARAM_INT);
		$editing->execute();
		$editing->setFetchMode(PDO::FETCH_ASSOC);
		$this->duplicate=$editing->fetch();
		if($this->duplicate==false){
			if (!empty($this->name)) {
				return 1;
			}
			else {
				return 0;
			}
		}else{
			return 2;
		}



	}

		/**
	 * Delete an existing category.check_category
	 */
	function check_category($cat_id) {
		$this->check_level = array(9,8,7);
		if (isset($cat_id)) {
				$siblings='';
				$sep=',';
			/** Do a permissions check */
				$checking= $this->dbh->prepare("SELECT name FROM " . TABLE_CATEGORIES . " WHERE name IN(SELECT name FROM " . TABLE_CATEGORIES . " WHERE parent =:id) AND parent IS NULL");
				$checking->bindParam(':id', $cat_id, PDO::PARAM_INT);
				$checking->execute();
				$checking->setFetchMode(PDO::FETCH_ASSOC);
				$this->duplicate=$checking->fetchAll();
				if($this->duplicate==false){
					return array('status'=>true,'msg'=>'The selected categories were deleted.');
				}else{
					$len = count($this->duplicate);
					foreach ($this->duplicate as  $key => $val ) {
						$siblings.=$val['name'];
						if ($key != $len - 1) {
							$siblings.=$sep;
						}
					}
					return array('status'=>false,'msg'=>'"'.$siblings.'" Already Exist.');
				}
		}
	}


}

?>