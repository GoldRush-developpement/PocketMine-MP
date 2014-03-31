<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace PocketMine\Plugin;

use PocketMine\Permission\Permission;

class PluginDescription{
	private $name;
	private $main;
	private $api;
	private $depend = array();
	private $softDepend = array();
	private $loadBefore = array();
	private $version;
	private $commands = array();
	private $description = null;
	private $authors = array();
	private $website = null;
	private $order = PluginLoadOrder::POSTWORLD;

	/**
	 * @var Permission[]
	 */
	private $permissions = array();

	/**
	 * @param string $yamlString
	 */
	public function __construct($yamlString){
		$this->loadMap(\yaml_parse($yamlString));
	}

	private function loadMap(array $plugin){
		$this->name = preg_replace("[^A-Za-z0-9 _.-]", "", $plugin["name"]);
		if($this->name === ""){
			trigger_error("Invalid PluginDescription name", E_USER_WARNING);

			return;
		}
		$this->name = str_replace(" ", "_", $this->name);
		$this->version = $plugin["version"];
		$this->main = $plugin["main"];
		$this->api = !is_array($plugin["api"]) ? array($plugin["api"]) : $plugin["api"];
		if(stripos($this->main, "PocketMine\\") === 0){
			trigger_error("Invalid PluginDescription main, cannot start within the PocketMine namespace", E_USER_ERROR);

			return;
		}

		if(isset($plugin["commands"]) and is_array($plugin["commands"])){
			$this->commands = $plugin["commands"];
		}

		if(isset($plugin["depend"])){
			$this->depend = (array) $plugin["depend"];
		}
		if(isset($plugin["softdepend"])){
			$this->softDepend = (array) $plugin["softdepend"];
		}
		if(isset($plugin["loadbefore"])){
			$this->loadBefore = (array) $plugin["loadbefore"];
		}

		if(isset($plugin["website"])){
			$this->website = $plugin["website"];
		}
		if(isset($plugin["description"])){
			$this->description = $plugin["description"];
		}
		if(isset($plugin["load"])){
			$order = strtoupper($plugin["load"]);
			if(!defined("PocketMine\\Plugin\\PluginLoadOrder::" . $order)){
				trigger_error("Invalid PluginDescription load", E_USER_ERROR);

				return;
			}else{
				$this->order = constant("PocketMine\\Plugin\\PluginLoadOrder::" . $order);
			}
		}

		if(isset($plugin["authors"])){
			$this->authors = $plugin["authors"];
		}elseif(isset($plugin["author"])){
			$this->authors = array($plugin["author"]);
		}else{
			$this->authors = array();
		}

		if(isset($plugin["permissions"])){
			$this->permissions = Permission::loadPermissions($plugin["permissions"]);
		}
	}

	/**
	 * @return string
	 */
	public function getFullName(){
		return $this->name . " v" . $this->version;
	}

	/**
	 * @return array
	 */
	public function getCompatibleApis(){
		return $this->api;
	}

	/**
	 * @return array
	 */
	public function getAuthors(){
		return $this->authors;
	}

	/**
	 * @return array
	 */
	public function getCommands(){
		return $this->commands;
	}

	/**
	 * @return array
	 */
	public function getDepend(){
		return $this->depend;
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}

	/**
	 * @return array
	 */
	public function getLoadBefore(){
		return $this->loadBefore;
	}

	/**
	 * @return string
	 */
	public function getMain(){
		return $this->main;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getOrder(){
		return $this->order;
	}

	/**
	 * @return Permission[]
	 */
	public function getPermissions(){
		return $this->permissions;
	}

	/**
	 * @return array
	 */
	public function getSoftDepend(){
		return $this->softDepend;
	}

	/**
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getWebsite(){
		return $this->website;
	}
}