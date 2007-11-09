<?PHP

	/**
	* Session management class
	*
  * @author Nicolas Perrenoud, mail@dysign.ch
	*/	
	class Session
	{
		private $userName;     //Username given on sign-up
		private $userId;       //Random value generated on current login
		private $time;         //Time user was last active (page loaded)
		private $logged_in;    //True if user is logged in, false otherwise
		public $userInfo = array();  //The array holding all user info
		private $errorKey;
		private $hasError;
		private $infoFields = array("name"=>"user_name","email"=>"user_email");
		
		/**
		* Class constructor 
		*/
		public function Session()
		{
		   $this->time = time();
		   $this->hasError=false;
		   $this->startSession();
		}
		
		/**
		* startSession
		* 
		* Performs all the actions necessary to 
		* initialize this session object. Tries to determine if the
		* the user has logged in already, and sets the variables 
		* accordingly. Also takes advantage of this page load to
		* update the active visitors tables.
		*/
		private function startSession()
		{
		   global $db;  //The database connection
		   session_start();   //Tell PHP to start the session
		
		   // Determine if user is logged in
		   $this->logged_in = $this->checkLogin();
		
			// Add session set if user is logged in
		   if($this->logged_in)
		   {     	
		   	$this->updateActiveUser();
      }
      
   }	
   
   	/**
    * checkLogin
    *
    * Checks if the user has already previously
    * logged in, and a session with the user has already been
    * established. Also checks to see if user has been remembered.
    * If so, the database is queried to make sure of the user's 
    * authenticity. Returns true if the user has logged in.
    */
		private function checkLogin()
		{
		   global $db;  //The database connection
		   
		   // Username and userid have been set
		   if(isset($_SESSION['username']) && isset($_SESSION['userid']))
		   {
		      // Confirm that username and userid are valid 
		      if(!$this->confirmActiveUser())
		      {
		         // Variables are incorrect, user not logged in
		         $this->setError("wrongSession");
		         unset($_SESSION['username']);
		         unset($_SESSION['userid']);
		         return false;
		      }
		
		      // User is logged in, set class variables
		      $this->userName  = $_SESSION['username'];
		      $this->userId    = $_SESSION['userid'];
					$this->start		= $_SESSION['start'];
					
		      return true;
		   }
		   // User not logged in
		   else
		   {
		      return false;
		   }
		}     
   
		/**
		 * login
		 *
		 * The user has submitted his username and password
		 * through the login form, this function checks the authenticity
		 * of that information in the database and creates the session.
		 * Effectively logging in the user if all goes well.
		 *
		 * @param $subuser Submitted username
		 * @param $subpass Submitted password
		 */
		public function login($subuser, $subpass)
		{
			// Let's do some clean up
			$this->cleanUp();			
			
			// Set login status temporarily
			$this->logged_in = false;

		   // Check if username entered
		  if(!$subuser || strlen($subuser = trim($subuser)) == 0)
		  {
		  	$this->setError("noUsername");
			}
		   
			// Check username valid
			if(!eregi(REGEXP_NICK, $subuser))
			{
				$this->setError("illegalUserName");
				return false;
			}
			
			// Check password entered
		  if(!$subpass)
		  {
		  	$this->setError("noPassword");
				return false;
		 	}
		
			// Checks that username is in database and password is correct
			$userid = $this->confirmUserPass($subuser, $subpass);
		
		  // Return if no user is found
		  if($userid<0)
			{
		  	$this->setError("userNotFound");				
		   	return false;
		  }   
		
		 	// If Username and password correct, register session variables
		 	$this->userName	= $_SESSION['username'] = $subuser;
		 	$this->userId  	= $_SESSION['userid']   = $userid;
		 	$this->start		= $_SESSION['start'] = $this->time;
		 	
		 	// Insert userid into database and update active users table
		 	$this->addActiveUser();
		
		  // Login completed successfully 
			$this->logged_in = true;
		}   
   
   
	  /**
	  * Returns an error message based on the current error key
	  */
	  public function getError()
	  {
	   	if ($this->hasError && $this->errorKey!="")	
	   	{
		   	switch ($this->errorKey)
		   	{
		   		case "noUsername":
		   			return "No username submitted";
		   		case "noPassword":
		   			return "No password submitted";
		   		case "illegalUserName":
		   			return "Illegal character in username";
		   		case "userNotFound":
		   			return "User can not be found or password is incorrect";
		   		case "wrongSession":
		   			return "Session is no longer valid";
		   		case "timeout":
		   			return "Session has timed out (More than ".USER_TIMEOUT." minutes of inactivity)!";
		   		default:
		   			return "General session error";
		   	}	
			}
		}
		
	  /**
	  * Returns an error message based on the current error key
	  */
	  public function getErrorKey()
	  {
	   	if ($this->hasError && $this->errorKey!="")	
	   	{
		   	return $this->errorKey;
			}
			return '';
		}		
   
   	/**
    * logout
    *
    * Gets called when the user wants to be logged out of the
    * website. It unsets all session variables
    */
		public function logout()
		{
		   // Remove from active users table
		   $this->removeActiveUser('logout');

		   // Unset PHP session variables
		   unset($_SESSION['username']);
		   unset($_SESSION['userid']);
		   unset($_SESSION['start']);
		   		
		   // Reflect fact that user has logged out
		   $this->logged_in = false;
		}   
   
		/**
    * Confirms an active session
    *
    * Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns false
    */
		private function confirmActiveUser()
   	{
   		global $db;
   			
			// Verify that user is in database
			$sql = "SELECT 
				session_time"; 
				foreach ($this->infoFields as $if)
				{
					$sql.= ",\n$if";
				}			
				$sql.= " FROM 
				".TBL_USERS." 
			INNER JOIN
				".TBL_USER_SESSIONS."
				ON user_id=session_user_id
				AND session_ip='".$_SERVER['REMOTE_ADDR']."'
				AND session_start='".$_SESSION['start']."'
			WHERE 
				user_nick = '".$_SESSION['username']."'
				AND user_id = ".intval($_SESSION['userid'])."
			;";
			$res = $db->query($sql);
			if(!$res || ($db->numRows($res) < 1))
			{
			  return false;
			}
			
			// Verify that session not has timed out
			$arr=mysql_fetch_array($res);
			if ($arr['session_time'] < $this->time-(USER_TIMEOUT*60))
			{
				// This is only for the remove active user function
		    $this->userName  = $_SESSION['username'];
		    $this->userId    = $_SESSION['userid'];
				$this->start		= $_SESSION['start'];				
				$this->logged_in = true;				
				
        $this->removeActiveUser('timeout');

				$this->logged_in = false;				
				$this->setError("timeout");
				return false;
			}
			
			// Load additional info
			foreach ($this->infoFields as $if=>$iv)
			{
				$this->userInfo[$if]=$arr[$iv];
			}			
			
			return true;
		}    
	   
   	/**
    * Confirm user password
    *
    * Checks whether or not the given username is in the 
    * database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns false
    *
    * @param $username	Username to be checked
    * @param $password Passwort to be validated
    */
		private function confirmUserPass($username, $password)
		{
			global $db;
			$username = stripslashes($username);
				
			/* Add slashes if necessary (for query) */
			if(!get_magic_quotes_gpc()) 
			{
			  $username = addslashes($username);
			}
			
			/* Verify that user is in database */
			$sql = "
			SELECT 
				user_id,
				user_password";
				foreach ($this->infoFields as $if)
				{
					$sql.= ",\n$if";
				}				
				$sql.=" FROM 
				".TBL_USERS." 
			WHERE 
				user_nick='$username'
			;";
			$res = $db->query($sql);
			if(!$res || ($db->numRows($res) < 1))
			{
	   		return -1; //Indicates username failure
			}
			$arr=mysql_fetch_array($res);
			if ($arr['user_password']==md5($password))
			{
				// Load additional info
				foreach ($this->infoFields as $if=>$iv)
				{
					$this->userInfo[$if]=$arr[$iv];
				}		

				return $arr['user_id'];
			}
			else
			{
				$db->query("
				INSERT INTO
					".TBL_USER_FAILED_LOGINS."
				(
					failure_user_id,
					failure_password,
					failure_ip,
					failure_host,
					failure_client
				)
				VALUES 
				(
					".$arr['user_id'].",
					'".$password."',
					'".$_SERVER['REMOTE_ADDR']."',
					'".gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
					'".$_SERVER['HTTP_USER_AGENT']."'				
				);");
				return -1;
			}
   	}	   
   	
		/**
		* Adds the active user to the session table
		*
		* Updates username's last active timestamp
		* in the database, and also adds him to the table of
		* active users, or updates timestamp if already there.
		*/
	  private function addActiveUser()
	  {
			global $db;   	
	  	$sql = "
	  	REPLACE INTO 
	  		".TBL_USER_SESSIONS." 
	  	(
	  		session_user_id,
	  		session_start,
	  		session_time,
	  		session_ip
	  	) 
	  	VALUES
	  	( 
	  		".$this->userId.",
	  		".$this->time.",
	  		".$this->time.",
	  		'".$_SERVER['REMOTE_ADDR']."'
	  	);";  	
	  	$db->query($sql);
		}	 
		
		/**
		* Updates an active user
		*/		
		private function updateActiveUser()
	  {
			global $db;   	
	  	$sql = "
	  	UPDATE
	  		".TBL_USER_SESSIONS." 
	  	SET
	  		session_time=".$this->time."
	  	WHERE
	  		session_user_id=".$this->userId."
	  	;";  	
	  	$db->query($sql);
		}	
				
	  /**
	  * Removes the active user
	  *
	  * @param $reason Logout reason
	  */
   	private function removeActiveUser($reason='logout')
   	{
   		global $db;
   		if ($this->logged_in)
   		{
	      $sql = "
	     	DELETE FROM 
	      	".TBL_USER_SESSIONS." 
	      WHERE 
	      	session_user_id=".$this->userId."
	      ;";
	      $db->query($sql);
	 		}
	 		$this->addToSessionLog($this->userId,$this->start,$reason);
		}
		
		/**
		* Adds the active user's session to log table
		*
		* @param $userId	User Id
		* @param $start		Session start time
		*	@param $reason	Logout reason keyword
		*/
		private function addToSessionLog($userId, $start, $reason='')
		{
			global $db;
   		$db->query("
   		INSERT INTO
   			".TBL_USER_SESSION_LOG."
   		(
   			log_user_id,
   			log_start,
   			log_end,
   			log_ip,
   			log_host,
   			log_client,
   			log_logout_reason
   		)
   		VALUES
   		(
   			".$userId.",
   			'".$start."',
   			'".$this->time."',
   			'".$_SERVER['REMOTE_ADDR']."',
   			'".gethostbyname($_SERVER['REMOTE_ADDR'])."',
   			'".$_SERVER['HTTP_USER_AGENT']."',
   			'".$reason."'
   		);");
		}
		
		/**
		* Sets an error message
		*
		* @param $key Error key
		*/
		private function setError($key)
		{
			if (!$this->hasError)
			{
				$this->errorKey=$key;
				$this->hasError=true;
			}
		}				
		
		/**
		* Cleans up old sessions
		*/
		private function cleanUp()
		{
			global $db;
			
			// Select inactive sessions
			$res=$db->query("
			SELECT
				session_user_id,
				session_start
			FROM
				".TBL_USER_SESSIONS."
			WHERE 
				session_time < ".($this->time-(USER_TIMEOUT*60))."
			;");
			if ($db->numRows($res))
			{		
				// Delete every old session and add it to session log
				while ($arr=$db->fetch($res))
				{
		      $sql = "
		     	DELETE FROM 
		      	".TBL_USER_SESSIONS." 
		      WHERE 
		      	session_user_id=".$arr['session_user_id']."
		      ;";
		      $db->query($sql);
		 			$this->addToSessionLog($arr['session_user_id'],$arr['session_start'],'cleanup');
				}			
			}
		}
		
		/**
		* Return active user id
		*/ 
		public function userId()
		{
			return $this->userId;
		}

		/**
		* Return active user name
		*/ 
		public function userName()
		{
			return $this->userName;
		}
		
		/**
		* Return session duration
		*/ 
		public function getDuration()
		{
			return $this->time-$this->start;	
		}
		
		/**
		* Returns if session has an error
		*/
		public function hasError()
		{
			return $this->hasError;
		}
		
		/**
		* Returns if user is logged in, that is to say 
		* return true if the session is valid, else return false
		*/
		public function loggedIn()
		{
			return $this->logged_in;
		}
		
		/**
		* Returns the value of a field of additional user data
		* 
		* @param $field	Name of the field
		*/
		public function info($field='')
		{
			if ($field!='')
			{
				if (isset($this->userInfo[$field]))
				{
					return $this->userInfo[$field];
				}
				return '';
			}
			return $this->userInfo;			
		}
		
	}
	

?>