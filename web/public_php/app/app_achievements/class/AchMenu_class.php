<?php
	class AchMenu extends Parentum {
		/*---------------------------
			This class is the dispatcher for actual MenuNodes.
			Since every MenuNode will only keep a list of it's children,
			we have to handle the main nodes which have no parent this way.
		---------------------------*/
		protected $open;

		function AchMenu($open = false) {
			global $DBc,$_USER;

			parent::__construct();

			$this->open = $open;
			
			// the summary page is autogenerated and has no database entry. We add it manually here.
			$tmp = array();
			$tmp['ac_id'] = 0;
			$tmp['ac_parent'] = null;
			$tmp['acl_name'] = get_translation('ach_summary',$_USER->getLang());
			$tmp['ac_image'] = "summary.png";
			$tmp['ac_order'] = -1;
			$tmp['open'] = $open;
			$this->addChild(new AchMenuNode($tmp,$this));

			$res = $DBc->sqlQuery("SELECT * FROM ach_category LEFT JOIN (ach_category_lang) ON (acl_lang='".$_USER->getLang()."' AND acl_category=ac_id) WHERE ac_parent IS NULL ORDER by ac_order ASC, acl_name ASC");

			$sz = sizeof($res);
			for($i=0;$i<$sz;$i++) {
				$res[$i]['open'] = $open;
				$this->addChild($this->makeChild($res[$i]));
			}

		}

		function getOpen() { // just returns the previously set ID of the currently open MenuNode
			return $this->open;
		}

		function getOpenCat() { // finds the currently open MenuNode and returns it's ID. If not found the result will be 0 instead.
			$iter = $this->getIterator();
			while($iter->hasNext()) {
				$curr = $iter->getNext();
				$res = $curr->hasOpenCat();
				if($res != 0) {
					return $res;
				}
			}
			return 0;
		}

		#@override Parentum::makeChild()
		protected function makeChild($a) {
			return new AchMenuNode($a,$this);
		}
	}
?>