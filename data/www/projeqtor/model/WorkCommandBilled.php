<?php
/*
 *	@author: qCazelles 
 */

require_once('_securityCheck.php');
class WorkCommandBilled extends SqlElement {
	
  public $id;   
	public $idCommand;
	public $idWorkCommand;
	public $idBill;
	public $billedQuantity;
	
	private static $_databaseCriteria = array();
	/** ==========================================================================
	 * Constructor
	 * @param $id the id of the object in the database (null if not stored yet)
	 * @return void
	 */

	
	/** ========================================================================
	 * Return the specific database criteria
	 * @return the databaseTableName
	 */
	protected function getStaticDatabaseCriteria() {
	  return self::$_databaseCriteria;
	}
	
	/** ==========================================================================
	 * Construct
	 * @return void
	 */
	function __construct($id = NULL, $withoutDependentObjects=false) {
		parent::__construct($id,$withoutDependentObjects);
	}
	
	
	/** ==========================================================================
	 * Destructor
	 * @return void
	 */
	function __destruct() {
		parent::__destruct();
	}
	
	
	
}