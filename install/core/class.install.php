<?php
//Refuses direct access
if (!defined("SSC")){ exit("You don't have access to this file"); }

class Install extends Database{
	
	public $dbhost;
	public $dbuser;
	public $dbpass;
	public $dbname;
	public $server_name;
	public $server_slogan;
	public $server_user;
	public $server_password;
	public $server_core;
	public $server_expansion;
	public $server_players;
	public $server_slider;
	public $error;
	
	public function getExpansion($expansion,$core){
		if ($core == "arcemu"){
			switch ($expansion) {
				case "0":
					$expansion_number = 0;
					break;
				case "1":
					$expansion_number = 8;
					break;
				case "2":
					$expansion_number = 24;
					break;
				case "3":
					$expansion_number = 32;
					break;
				default:
					$expansion_number = 8;
					break;
			}	
		}
		else{
			$expansion_number = $expansion;
		}
		
		return $expansion_number;
	}
	
	public function getCoreDatabases($core){
		switch ($core) {
			case "arcemu":
				$core_db['accounts'] = "accounts";
				$core_db['characters'] = "characters";
				$core_db['world'] = "world";
				break;
			case "mangos":
				$core_db['accounts'] = "auth";
				$core_db['characters'] = "characters";
				$core_db['world'] = "world";
				break;
			case "trinity":
				$core_db['accounts'] = "auth";
				$core_db['characters'] = "characters";
				$core_db['world'] = "world";
				break;
			case "trinity_v6":
				$core_db['accounts'] = "auth";
				$core_db['characters'] = "characters";
				$core_db['world'] = "world";
				break;
			default:
				$core_db['accounts'] = "auth";
				$core_db['characters'] = "characters";
				$core_db['world'] = "world";
				break;
		}
		
		return $core_db;
	}
	
	public function insertAdmin($core,$core_db){
		if ($core == "arcemu"){
			$this->SimpleQuery("INSERT INTO account () VALUES ();");
		}
		else{
			$this->SimpleQuery("INSERT INTO accounts () VALUES ();");
			$this->SimpleQuery("INSERT INTO accounts () VALUES ();");
		}
	}
	
	public function addInfo() {
		//Config File
		$config_file = "core/config.php";
	
		$config_data = '//DB constants
				define("DBHOST", "'.$this->dbhost.'");
				define("DBUSER", "'.$this->dbuser.'");
				define("DBPASS", "'.$this->dbpass.'");
				define("DBNAME", "'.$this->dbname.'");
				define("DBFORUM", "forum");
				define("DBPORT", "3306");';
	
		//Scans Webpath to get all web applications
		$apps = array_diff(scandir(WEB_PATH), array(".","..","index.php","README",".git","core","LICENSE"));
	
		//!TODO This is probably stupid... (Make only 1 config file at the main core location instead)
		//Opens the config file of every web application and appends the data
		foreach ($apps as $value){
			$handle = fopen(WEB_PATH ."/". $value ."/". $config_file, 'a') or die("Cannot open file:  ". WEB_PATH ."/". $value ."/". $config_file);
			fwrite($handle, $config_data);
			fclose($handle);
		}
	
		//Main querys to create the necessary databases and tables
		$create_database_website = "CREATE DATABASE IF NOT EXISTS `". $this->dbname ."`;";
	
		$con = @mysqli_connect($this->dbhost,$this->dbuser,$this->dbpass);
	
		//Creates database for the Website
		mysqli_query($con,$create_database_website);
	}
	
	public function addDb() {
		
		//Gets the expansion number and the database names
		$expansion = $this->getExpansion($this->server_expansion, $this->server_core);
		$core = $this->getCoreDatabases($this->server_core);
		
		//Main querys to create the necessary tables
		$create_table_chat = "CREATE TABLE IF NOT EXISTS `chat` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user` varchar(50) DEFAULT NULL,
				  `msg` text,
				  `posttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
				
		$create_table_info = "CREATE TABLE IF NOT EXISTS `info` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` varchar(50) NOT NULL DEFAULT '". $this->server_name ."',
				  `slogan` varchar(100) NOT NULL DEFAULT '". $this->server_slogan ."',
				  `core` varchar(50) NOT NULL DEFAULT '". $this->server_core ."',
				  `expansion` int(11) NOT NULL DEFAULT '". $expansion ."',
				  `acc_db` varchar(50) NOT NULL DEFAULT '". $core['accounts'] ."',
				  `char_db` varchar(50) NOT NULL DEFAULT '". $core['characters'] ."',
				  `world_db` varchar(50) NOT NULL DEFAULT '". $core['world'] ."',
				  `style` varchar(50) NOT NULL DEFAULT 'default',
				  `onplayers` int(11) NOT NULL DEFAULT '". $this->server_players ."',
				  `slider` varchar(50) NOT NULL DEFAULT '". $this->server_slider ."',
				  `realmlist` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
				
		$create_table_menu = "CREATE TABLE IF NOT EXISTS `menu` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(50) NOT NULL DEFAULT 'link name',
				  `link` varchar(50) NOT NULL DEFAULT '?page=',
				  `link_order` int(11) DEFAULT NULL,
				  `logged` int(11) NOT NULL DEFAULT '0',
				  `position` varchar(50) NOT NULL DEFAULT 'left',
				  `icon` varchar(50) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
				
		$create_table_news = "CREATE TABLE IF NOT EXISTS `news` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` varchar(50) NOT NULL DEFAULT 'Announcement',
				  `user` varchar(50) DEFAULT NULL,
				  `content` text NOT NULL,
				  `media` varchar(50) NOT NULL DEFAULT 'news.jpg',
				  `posttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			
		$create_table_voted_cooldown = "CREATE TABLE IF NOT EXISTS `voted_cooldown` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `username` varchar(50) NOT NULL DEFAULT '0',
				  `vote_link_id` int(11) NOT NULL DEFAULT '0',
				  `voted` int(11) NOT NULL DEFAULT '0',
				  `voted_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			
		$create_table_vote_links = "CREATE TABLE IF NOT EXISTS `vote_links` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(50) DEFAULT NULL,
				  `vote_link` varchar(50) DEFAULT NULL,
				  `vote_img` varchar(50) DEFAULT NULL,
				  `value` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		
		$create_table_statistics = "CREATE TABLE IF NOT EXISTS `statistics` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`session` char(100) DEFAULT NULL,
			`ip` char(20) DEFAULT NULL,
			`country` char(20) DEFAULT NULL,
			`state` char(20) DEFAULT NULL,
			`town` char(20) DEFAULT NULL,
			`last_seen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
				
		$insert_data_menu = "INSERT INTO `menu` (`id`, `name`, `link`, `link_order`, `logged`, `position`) VALUES
					(1, 'Account P', '?page=account', 2, 1, 'left'),
					(2, 'Forum', '?page=forum', 4, 0, 'left'),
					(3, 'Store', '?page=store', 6, 1, 'right'),
					(4, 'Armory', '?page=armory', 7, 0, 'right'),
					(5, 'Media', '?page=media', 8, 0, 'right'),
					(6, 'Home', 'index.php', 1, 0, 'left'),
					(7, 'Login', '?page=login', 3, 0, 'left'),
					(8, 'Register', '?page=register', 5, 0, 'right');";
		
		$insert_data_info = "INSERT INTO `info` (`title`, `slogan`, `core`, `expansion`, `acc_db`, `char_db`, `world_db`, `style`, `onplayers`, `slider`) VALUES ('". $this->server_name ."', '". $this->server_slogan ."', '". $this->server_core ."', '". $expansion ."', '". $core['accounts'] ."', '". $core['characters'] ."', '". $core['world'] ."', 'default', '". $this->server_players ."', '". $this->server_slider ."');";
				
		$create_database_forum = "CREATE DATABASE IF NOT EXISTS `forum`;";
			
		$create_table1_forum = "";
		
		$create_table2_forum = "";
		
		$create_table3_forum = "";
		
		$create_table4_forum = "";
		
		$create_table5_forum = "";

		//Creates tables for the Website
		$this->SelectDb(DBNAME);
		
		/***********************************************
		 * 
		 * The SimpleUpdateQuery function was changed
		 * before it returned and error to display in 
		 * the end of the installation, now it clears 
		 * the page and specifies the error.
		 * 
		 ***********************************************/
		/*$this->error['chat'] =*/ $this->SimpleUpdateQuery($create_table_chat);
		/*$this->error['info'] =*/ $this->SimpleUpdateQuery($create_table_info);
		/*$this->error['menu'] =*/ $this->SimpleUpdateQuery($create_table_menu);
		/*$this->error['news'] =*/ $this->SimpleUpdateQuery($create_table_news);
		/*$this->error['vote_cooldown'] =*/ $this->SimpleUpdateQuery($create_table_voted_cooldown);
		/*$this->error['vote_links'] =*/ $this->SimpleUpdateQuery($create_table_vote_links);
		/*$this->error['statistics'] =*/ $this->SimpleUpdateQuery($create_table_statistics);
		/*$this->error['data_in_menu'] =*/ $this->SimpleUpdateQuery($insert_data_menu);
		/*$this->error['data_in_info'] =*/ $this->SimpleUpdateQuery($insert_data_info);
		
		
		//!TODO Make the rest of the forum database and tables (already in a sql file)
		
		//Creates database and tables for the Forum
		//$error['create_database_forum'] = $this->SimpleQuery($create_database_forum);
		//$this->SelectDb("forum");
		/*$error['create_table1_forum'] = $this->SimpleUpdateQuery($create_table1_forum);
		$error['create_table2_forum'] = $this->SimpleUpdateQuery($create_table2_forum);
		$error['create_table3_forum'] = $this->SimpleUpdateQuery($create_table3_forum);
		$error['create_table4_forum'] = $this->SimpleUpdateQuery($create_table4_forum);
		$error['create_table5_forum'] = $this->SimpleUpdateQuery($create_table5_forum);*/
	}
	
	public function finish(){
		//Renames the install folder to trash
		rename(WEB_PATH. "/install", WEB_PATH ."/trash");
		
		//Unsets the language session variable
		unset($_SESSION['lang']);
	}
}
