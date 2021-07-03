<?php
namespace App\Query;

class BaseQuery {
	
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection;
	
	/**
	 * @var array
	 */
	protected $special_char;
	
	/**
	 * @var array
	 */
	protected $replacement_char;
	
	/**
	 * 
	 * @var array
	 */
	protected $ignore_char;
	
	public function __construct($connection) {
		$this->connection = $connection;

		$this->special_char = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î',
				'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß',
				'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î',
				'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A',
				'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd',
				'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G',
				'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i',
				'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L',
				'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O',
				'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's',
				'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U',
				'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z',
				'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o',
				'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?',
				'?', '_', '-', ':', '/', '!', '|', '=', '[', ']', '~', '{', '}', '(', ')',
				'\\\\',' ', '"',',',"\'"
			);
		
		$this->replacement_char = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I',
				'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's',
				'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i',
				'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a',
				'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd',
				'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g',
				'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
				'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l',
				'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R',
				'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't',
				'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y',
				'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I',
				'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o',
				' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','','','',''
			);
	}
	
	/**
	 * load impact of risk
	 */
	public function loadImpactOfRisk() {
		/*
		 $query="INSERT INTO risque_has_impact
		 SELECT null, s.risque_id, s.impact_id, s.grille_id
		 FROM (
		 SELECT e.risque_id, ehi.impact_id, ehi.grille_id, e.id as evaluation_id
		 FROM `evaluation_has_impact` ehi
		 INNER JOIN `evaluation` e ON e.id = ehi.evaluation_id
		 INNER JOIN `impact` i ON i.id = ehi.impact_id
		 WHERE e.risque_id NOT IN (SELECT risque_id FROM risque_has_impact)
		 GROUP BY e.risque_id, i.critere_id
		 HAVING MAX(evaluation_id)=evaluation_id) s";
		*/
		$query = "UPDATE `risque_has_impact` rhi
				INNER JOIN evaluation_has_impact ehi ON ehi.impact_id = rhi.impact_id
				INNER JOIN (SELECT MAX(e.id) ev_id FROM evaluation e GROUP BY e.risque_id, e.criticite_id) t ON t.ev_id = ehi.evaluation_id
				INNER JOIN `impact` i ON i.id = ehi.impact_id
				SET rhi.grille_id = ehi.grille_id;";
		$query .= "DELETE FROM `evaluation_has_impact` WHERE `grille_id` IS NULL;";
		$query .= "DELETE FROM `risque_has_impact` WHERE `impact_id` NOT IN (SELECT `impact_id` FROM evaluation_has_impact) AND `grille_id` IS NULL;";
		$this->connection->prepare($query)->execute();
	}

	public function loadDataFile($fileName, $type, $web_dir) {
		$nomdufichier = ($type.'-'.date('YmdHis'));
		$newPath =  addslashes($web_dir.'upload/import/'.$type.'/'.$nomdufichier.'.csv');
		$rs=move_uploaded_file($fileName, $newPath);
		return $newPath;
	}
	
	/**
	 * @param string $str
	 */
	public function trim($str) {
		for($i=0;$i<count($this->special_char);$i++) {
			$str = str_replace($this->special_char[$i], $this->replacement_char[$i], $str);
		}
		$str = str_replace('\'', '', $str);
		return strtoupper($str);
	}
	
}