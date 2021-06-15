<?php

class ItemBean {
	var $itemId;
	var $uid;
	var $item;
	var $itemTime;
	var $itemAmPm;
	var $itemDate;
	var $itemDesc;
	
	function ItemBean() {
	}
	
	function setItemId($iid) {
		$this->itemId = $iid;
	}
	
	function getItemId() {
		return $this->itemId;
	}
	
	function setUid($id) {
		$this->uid=$id;
	}
	
	function getUid() {
		return $this->uid;
	}
	function setItem($itm) {
		$this->item=$itm;
	}
	
	function getItem() {
		return $this->item;
	}
	function setItemTime($itime) {
		$this->itemTime=$itime;
	}
	
	function getItemTime() {
		return $this->item;
	}
	function setItemAmPm($am) {
		$this->itemAmPm=$am;
	}
	
	function getItemAmPm() {
		return $this->itemAmPm;
	}
	
	function setItemDate($dt) {
		$this->itemDate=$dt;
	}
	
	function getItemDate() {
		return $this->itemDate;
	}
 	
	function setItemDesc($desc) {
		$this->itemDesc=$desc;
	}
	
 	function getItemDesc() {
		return $this->itemDesc;
	}
}
?>