<?php
namespace App\Query;

use App\Entity\Chargement;
use App\Entity\Impact;
use App\Entity\Risque;
use App\Entity\Structure;
use App\Entity\TypeProcessus;
use Doctrine\ORM\EntityManager;
use App\Entity\Utilisateur;
use Doctrine\DBAL\DBALException;
use App\Entity\Processus;

class RisqueMetierQuery extends BaseQuery {
	public function createTable($next_id) {
		$query = sprintf("DROP TABLE IF EXISTS `temp_risquemetier`;
				CREATE TABLE IF NOT EXISTS `temp_risquemetier`(
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `macro` varchar(255) DEFAULT NULL,
				  `macro_sans_carspec` varchar(255) DEFAULT NULL,
				  `processus` varchar(255) DEFAULT NULL,
				  `processus_sans_carspec` varchar(255) DEFAULT NULL,
				  `sous_processus` varchar(255) DEFAULT NULL,
				  `sous_processus_sans_carspec` varchar(255) DEFAULT NULL,
				  `sous_entite` varchar(255) DEFAULT NULL,
				  `sous_entite_sans_carspec` varchar(255) DEFAULT NULL,
				  `activite` varchar(255) DEFAULT NULL,
				  `activite_sans_carspec` varchar(255) DEFAULT NULL,
				  `menace` varchar(255) DEFAULT NULL,
				  `menace_sans_carspec` varchar(255) DEFAULT NULL,
				  `cause` varchar(255) DEFAULT NULL,
				  `cause_sans_carspec` varchar(255) DEFAULT NULL,
				  `desc_pa` longtext DEFAULT NULL,
				  `porteur` varchar(255) DEFAULT NULL,
				  `email_porteur` varchar(255) DEFAULT NULL,
				  `date_debut_pa` varchar(255) DEFAULT NULL,
				  `date_fin_pa` varchar(255) DEFAULT NULL,
				  `avancement` varchar(255) DEFAULT NULL,
				  `statut` varchar(255) DEFAULT NULL,
				  `desc_ctrl` longtext DEFAULT NULL,
				  `type_ctrl` varchar(255) DEFAULT NULL,
				  `methode_ctrl` varchar(255) DEFAULT NULL,
				  `probabilite` varchar(255) DEFAULT NULL,
				  `gravite` varchar(255) DEFAULT NULL,
				  `financier` varchar(255) DEFAULT NULL,
				  `RH` varchar(255) DEFAULT NULL,
				  `image` varchar(255) DEFAULT NULL,
				  `juridique` varchar(255) DEFAULT NULL,
				  `client` varchar(255) DEFAULT NULL,
				  `criticite` varchar(255) DEFAULT NULL,
				  `maturite` varchar(255) DEFAULT NULL,
				  `best_id` varchar(255) DEFAULT NULL,
				   PRIMARY KEY(`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;");
		try {
			$exec = $this->connection->prepare($query)->execute();
		} catch(DBALException $e) {
			throw $e->getMessage();
		}
	}

	/**
	 * @param Chargement $chargement
	 * @param string $fileName
	 * @param string $web_dir
	 * @param number $next_id
	 */
	public function loadTable($chargement, $fileName, $web_dir, $next_id) {
		$subSelect = $chargement->getActivite() ? '' : "`macro` , `processus`,`sous_processus`,`sous_entite`,`activite`,";
		$newPath = $this->loadDataFile($fileName, 'risque_metier', $web_dir);
		$query = "LOAD DATA LOCAL INFILE '$newPath' INTO TABLE temp_risquemetier
		CHARACTER SET latin1
		FIELDS TERMINATED BY  ';' ENCLOSED BY  '".'"'."'
		LINES TERMINATED BY  '\\r\\n'
		IGNORE 1 LINES
		($subSelect `menace`,
		`cause`,`desc_pa` ,`porteur`,`email_porteur`,`date_debut_pa` ,`date_fin_pa` ,
		`avancement` ,`statut`,`desc_ctrl`,`type_ctrl`, `methode_ctrl` ,`probabilite` ,`gravite`,
		`financier`, `RH`, `image`, `juridique`, `client`, `criticite`, `maturite`);";
		$this->connection->prepare($query)->execute();
	}

	/**
	 * @param Chargement $chargement
	 */
	public function updateTable($chargement, $ids, $em) {
		$erreurs = array();
		$query = "";
		// Mettre a jour les Ids des structures
		if($chargement->getActivite()==null) {
			$query .= 'UPDATE structure SET name_sans_spec_char = name;';
			$query .= 'UPDATE processus SET libelle_sans_carspecial = libelle;';
			$query .= 'UPDATE activite SET libelle_sans_carspecial = libelle ;';
		}
		$query .= 'UPDATE menace SET libelle_sans_carspecial = libelle ;';
		$query .= 'UPDATE cause SET libelle_sans_carspecial = libelle ;';
		
		if($chargement->getActivite()==null) {
			$query .= 'UPDATE temp_risquemetier SET sous_entite_sans_carspec = sous_entite;';
			$query .= 'UPDATE temp_risquemetier SET macro_sans_carspec  = macro;';
			$query .= 'UPDATE temp_risquemetier SET processus_sans_carspec  = processus ;';
			$query .= 'UPDATE temp_risquemetier SET sous_processus_sans_carspec  = sous_processus ;';
			$query .= 'UPDATE temp_risquemetier SET activite_sans_carspec  = activite ;';
		}
		$query .= 'UPDATE temp_risquemetier SET menace_sans_carspec  = menace ;';
		$query .= 'UPDATE temp_risquemetier SET cause_sans_carspec  = cause ;';

		for($i = 0; $i < count($this->special_char); $i ++) {
			if($chargement->getActivite()==null) {
				$query .= "UPDATE temp_risquemetier SET sous_entite_sans_carspec  = REPLACE(sous_entite_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
				$query .= "UPDATE temp_risquemetier SET macro_sans_carspec  = REPLACE(macro_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
				$query .= "UPDATE temp_risquemetier SET processus_sans_carspec  = REPLACE(processus_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
				$query .= "UPDATE temp_risquemetier SET sous_processus_sans_carspec  = REPLACE(sous_processus_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
				$query .= "UPDATE temp_risquemetier SET activite_sans_carspec  = REPLACE(activite_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			}
			$query .= "UPDATE temp_risquemetier SET menace_sans_carspec  = REPLACE(menace_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquemetier SET cause_sans_carspec  = REPLACE(cause_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";

			if($chargement->getActivite()==null) {
				$query .= "UPDATE structure SET name_sans_spec_char = REPLACE(name_sans_spec_char, '" . $this->special_char [$i] . "','".$this->replacement_char[$i]."');";
				$query .= "UPDATE processus SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','".$this->replacement_char[$i]."');";
				$query .= "UPDATE activite SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','".$this->replacement_char[$i]."');";
			}
			$query .= "UPDATE menace SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','".$this->replacement_char[$i]."');";
			$query .= "UPDATE cause  SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','".$this->replacement_char[$i]."');";
		}
		if($chargement->getActivite()==null) {
			$query .= "UPDATE temp_risquemetier t INNER JOIN structure s on lower(s.name_sans_spec_char) = lower(t.sous_entite_sans_carspec) SET t.sous_entite = s.id where s.root=" . $chargement->getDirection()->getId() . ";";
		} else {
			$query .= "UPDATE temp_risquemetier SET sous_entite = ".$chargement->getActivite()->getProcessus()->getStructure()->getId().";";
		}
		$this->connection->prepare($query)->execute();
		$results = $this->connection->fetchAll("SELECT distinct id, date_debut_pa, date_fin_pa, sous_entite from temp_risquemetier");
		
		// Formats des dates d'échéances incorrects
		for($i = 0; $i < count($results); $i ++) {
			$dd = $results [$i] ['date_debut_pa'];
			$df = $results [$i] ['date_fin_pa'];
			if(!preg_match("/([012]?[1-9]|[12]0|3[01])\/(0?[1-9]|1[012])\/([0-9]{4})/", $dd) && $dd != '') {
				$erreurs [] = sprintf("Le format de la date debut du PA a la ligne %s est incorrect ", $i + 2);
			} if(!preg_match("/([012]?[1-9]|[12]0|3[01])\/(0?[1-9]|1[012])\/([0-9]{4})/", $df) && $df != '') {
				$erreurs [] = sprintf("Le format de la date fin du PA a la ligne %s est incorrect ", $i + 2);
			}
		}
		// Sous-entités inexistants
		for($i = 0; $i < count($results); $i ++) {
			if(ctype_digit($results [$i] ['sous_entite']) == false && $chargement->getActivite()==null) {
				$erreurs [] = sprintf("La sous entite a la ligne %s n'existe pas ", $i + 2);
			}
		}
		$toString = serialize($erreurs);
		if(count($erreurs) > 0) {
			throw new \Exception($toString);
		}
		if($chargement->getActivite()==null) {
			//creer macro , proc et ssproc inexistant
			$this->createNewProcessus($ids, $chargement, $em);
				$query  = "UPDATE temp_risquemetier t INNER JOIN processus p on lower(p.libelle_sans_carspecial) = lower(t.macro_sans_carspec) and p.lvl=0  SET t.macro = p.id where p.structure_id=" . $chargement->getDirection()->getId() . ";";
				$query .= "UPDATE temp_risquemetier t INNER JOIN processus p on lower(p.libelle_sans_carspecial) = lower(t.processus_sans_carspec) and p.lvl=1  SET t.processus = p.id  where p.parent_id = t.macro and p.structure_id=t.sous_entite;";
				$query .= "UPDATE temp_risquemetier t INNER JOIN processus p on lower(p.libelle_sans_carspecial) = lower(t.sous_processus_sans_carspec) and p.lvl=2 SET t.sous_processus = p.id where p.structure_id=t.sous_entite and p.parent_id = t.processus;";
			//creer activite inexistant
				$query .= "INSERT INTO `activite`( `processus_id`, `libelle`, `description`, `libelle_sans_carspecial`)
					   select distinct t.sous_processus, t.activite, t.activite, t.activite_sans_carspec
					   from temp_risquemetier t
					   left join activite a on a.libelle_sans_carspecial =t.activite_sans_carspec and a.processus_id = t.sous_processus
					   where a.id is null;";
		}
		// creer cause inexistant et les rattacher au carto
		$query .= "INSERT INTO `cause`(`libelle`, `etat`, `description`, `libelle_sans_carspecial`)
				select distinct t.cause, 1, t.cause, t.cause_sans_carspec
				from temp_risquemetier t
				left join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
				where c.id is null;";

		$query .= "INSERT INTO `cartographie_has_causes`(`cause_id`, `carto_id`)
				select distinct c.id, ".$chargement->getCartographie()->getId()."
				from temp_risquemetier t
				inner join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
				left join cartographie_has_causes chc on c.id=chc.cause_id and chc.carto_id=".$chargement->getCartographie()->getId()."
				where chc.cause_id is null;";

		//  creer les menaces inexistants
		$query .= "INSERT INTO `menace`( `libelle`, `description`, `etat`, `libelle_sans_carspecial`)
				select distinct t.menace, t.menace, 1, t.menace_sans_carspec
				from temp_risquemetier t
				left join menace m on m.libelle_sans_carspecial =t.menace_sans_carspec
				where m.id is null;";
		if($chargement->getActivite()) {
			$query .= sprintf("UPDATE temp_risquemetier t SET t.activite = %s;", $chargement->getActivite()->getId());
		} else {
			$query .= "UPDATE temp_risquemetier t INNER JOIN activite  a on lower(a.libelle_sans_carspecial) = lower(t.activite_sans_carspec) and a.processus_id=t.sous_processus  SET t.activite = a.id;";
		}
		$query .= "UPDATE temp_risquemetier t INNER JOIN menace m on lower(m.libelle_sans_carspecial) = lower(t.menace_sans_carspec) SET t.menace = m.id;";
		$query .= "UPDATE temp_risquemetier t INNER JOIN cause c on lower(c.libelle_sans_carspecial) = lower(t.cause_sans_carspec) INNER JOIN cartographie_has_causes chs on chs.cause_id = c.id SET t.cause = c.id WHERE chs.carto_id = " . $chargement->getCartographie()->getId() . ";";
		$query .= "UPDATE temp_risquemetier t INNER JOIN utilisateur u on trim(lower(u.email)) = trim(lower(t.email_porteur)) SET t.email_porteur = u.id;";
		$query .= "UPDATE temp_risquemetier  SET email_porteur = null where email_porteur='' ;";
		$query .= "UPDATE temp_risquemetier t INNER JOIN statut  st on trim(lower(t.statut)) = trim(lower(st.libelle)) SET t.statut = st.id ;";
		$query .= "UPDATE temp_risquemetier SET statut = null where statut like '';";
		$query .= "UPDATE temp_risquemetier t INNER JOIN methode_controle m on trim(lower(t.methode_ctrl)) = trim(lower(m.libelle)) SET t.methode_ctrl = m.id;";
		$query .= "UPDATE temp_risquemetier SET methode_ctrl = null where methode_ctrl like '';";
		$query .= "UPDATE temp_risquemetier t INNER JOIN type_controle tc on trim(lower(t.type_ctrl)) = trim(lower(tc.libelle)) SET t.type_ctrl = tc.id;";
		$query .= "UPDATE temp_risquemetier SET type_ctrl = null where type_ctrl like '';";
		$this->connection->prepare($query)->execute();
		$erreurs = array();
		$results = $this->connection->fetchAll("SELECT distinct id, sous_entite , sous_processus, activite, menace, cause ,email_porteur,statut, `type_ctrl`, `methode_ctrl` from temp_risquemetier");
		for($i = 0; $i < count($results); $i ++) {
			if(ctype_digit($results [$i] ['sous_processus']) == false && is_null($results [$i] ['sous_processus']) == false) {
				$erreurs [] = sprintf("Le sous processus a la ligne %s n'existe pas ", $i + 2);
			}
			if(ctype_digit($results [$i] ['activite']) == false) {
				$erreurs [] = sprintf("L'activite à la ligne %s n'existe pas", $i + 2);
			}
			if(ctype_digit($results [$i] ['menace']) == false) {
				$erreurs [] = sprintf("La menace à la ligne %s n'existe pas", $i + 2);
			}
			if(ctype_digit($results [$i] ['email_porteur']) == false && is_null($results [$i] ['email_porteur']) == false) {
				$erreurs [] = sprintf("Le porteur à la ligne %s n'existe pas", $i + 2);
			}
			if(ctype_digit($results [$i] ['statut']) == false && is_null($results [$i] ['statut']) == false) {
				$erreurs [] = sprintf("Le statut a la ligne %s n'existe pas ", $i + 2);
			}
			if(ctype_digit($results [$i] ['type_ctrl']) == false && is_null($results [$i] ['type_ctrl']) == false) {
				$erreurs [] = sprintf("Le type de controle à la ligne %s est incorrecte ", $i + 2);
			}
			if(ctype_digit($results [$i] ['methode_ctrl']) == false && is_null($results [$i] ['methode_ctrl']) == false) {
				$erreurs [] = sprintf("Le methode de controle à la ligne %s est incorrecte ", $i + 2);
			}
			if(ctype_digit($results [$i] ['cause']) == false) {
				$erreurs [] = sprintf("La cause à la ligne %s n'existe pas ", $i + 2);
			}
		}

		$toString = serialize($erreurs);
		if(count($erreurs) > 0) {
			throw new DBALException($toString);
		}
	}

	/**
	 *
	 * @param Utilisateur $current_user
	 * @param EntityManager $em
	 */
	public function migrateData($current_user, $em, $chargement, $ids) {
		$nextId = $em->getRepository(Risque::class)->getNextId();
		// creer une table temporaire risque
		$query = sprintf("DROP TABLE IF EXISTS `temp_risque`;
		             CREATE TABLE `temp_risque`(`id` int(11) NOT NULL AUTO_INCREMENT,`menace_id` int(11) DEFAULT NULL,`processus_id` int(11) DEFAULT NULL,`activite_id` int(11) DEFAULT NULL,`probabilite` int(11) DEFAULT NULL,
		             PRIMARY KEY(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=%s;", $nextId);
		$this->connection->prepare($query)->execute();

		$this->migrateRisque($current_user, $chargement);
		$this->migrateCauseControleAndPlanAction($ids, $chargement,$current_user);
		$this->migrateEvaluation($current_user, $em, $chargement);
	}
	
	public function deleteTable() {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_risquemetier`;DROP TABLE IF EXISTS `temp_risque`;DROP TABLE IF EXISTS `temp_impact`;"));
		$statement->execute();
	}
	
	/**
	 * @param unknown $current_user
	 * @param Chargement $chargement
	 */
	public function migrateRisque($current_user, $chargement) {
		// remplir la table temporaire risque
		$query = "";
		if($chargement->getActivite()) {
            $query .= sprintf("UPDATE temp_risquemetier SET activite=%s, sous_processus=%s, sous_entite='%s';", $chargement->getActivite()->getId(), $chargement->getActivite()->getProcessus()->getId(), $chargement->getActivite()->getProcessus()->getStructure()->getId());
            if($chargement->getActivite()->getProcessus()->getTypeProcessus()->getId() == TypeProcessus::$ids['macro']) {
                $chargement->getActivite()->getProcessus()->getParent() ? $query .= sprintf("UPDATE temp_risquemetier SET macro='%s';", $chargement->getActivite()->getProcessus()->getParent()->getId()) : null;
            } else {
                $chargement->getActivite()->getProcessus()->getParent() ? $query .= sprintf("UPDATE temp_risquemetier SET processus='%s';", $chargement->getActivite()->getProcessus()->getParent()->getId()) : null;
                //if ($chargement->getActivite()->getProcessus()->getParent() && $chargement->getActivite()->getProcessus()->getParent()->getTypeProcessus()->getId() == TypeProcessus::$ids['macro'])
                //{
                //    $query .= sprintf("UPDATE temp_risquemetier SET macro='%s';", $chargement->getActivite()->getProcessus()->getParent()->getId());
                //}
            }

		}
		$query .= "INSERT INTO temp_risque(menace_id, processus_id,activite_id)
				  SELECT distinct menace, sous_processus, activite
				  from temp_risquemetier;";

		// inserer les risques dans la table risque
		$query .= "INSERT INTO `risque`(`id`, `menace_id`, `utilisateur_id`, `societe_id`, `validateur`,   `cartographie_id`,   `date_saisie`, `date_validation`,`etat`, `relanced`)
				   select t.id, t.menace_id," . $current_user->getId() . "," . $current_user->getSociete()->getId() . "," . $current_user->getId() . ", " . $chargement->getCartographie()->getId() . ", NOW(), NOW() ,1,0
				   from temp_risque t;";

		// renseigner dans la temp_risquemetier le bon id risque
		$query .= "update temp_risquemetier t , temp_risque tr
				   set t.best_id=tr.id
				   where tr.menace_id=t.menace and tr.processus_id=t.sous_processus and tr.activite_id=t.activite;";

		// inserer les risques dans la table risque_metier
		$query .= "INSERT INTO `risque_metier`( `risque_id`, `processus_id`, `activite_id`, `structure_id`)
				   SELECT distinct t.best_id, t.sous_processus, t.activite, t.sous_entite FROM temp_risquemetier t ;";
		$this->connection->prepare($query)->execute();
	}

	/**
	 *
	 * @param unknown $ids
	 * @param unknown $chargement
	 * @param EntityManager $em
	 */
	public function migrateCauseControleAndPlanAction($ids, $chargement,$current_user) {
		// ajouter les causes des risques dans risque_has_cause
		$query = "INSERT INTO `risque_has_cause`( `risque_id`, `cause_id`, `grille_id`,`transfered`)
				   select t.`best_id`, t.`cause`, g.id, 1
				   from temp_risquemetier t
				   left join type_grille tg on tg.type_evaluation_id =".$ids['type_evaluation']['cause']." and tg.cartographie_id=".$chargement->getCartographie()->getId()."
				   left join note n on tg.id  = n.type_grille_id and n.valeur = t.probabilite
				   left join grille g on tg.id  = g.type_grille_id and n.id = g.note_id
				   group by t.best_id, t.cause;";

		// ajouter les controles
		$query .= "INSERT INTO `controle`( `risque_cause_id`, `methode_controle_id`,  `type_controle_id`, `maturite_theorique_id`, `description`,  `date_creation` ,`transfered`)
				   	SELECT distinct rhc.id,  t.methode_ctrl, t.type_ctrl,
				   	CASE t.maturite WHEN \"\" THEN null ELSE t.maturite END,
				   	desc_ctrl, NOW(), 1
				   	from temp_risquemetier t
				   	inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   	where t.desc_ctrl!='';";

		// ajouter les PAs
		$query .= "INSERT INTO `plan_action`( `porteur`, `nom_porteur`, `statut_id`, `risque_cause_id`, `libelle`, `date_debut`, `date_fin`, `description`,`transfered`)
				   SELECT distinct t.email_porteur,t.porteur, t.statut, rhc.id, t.desc_pa, case when t.date_debut_pa='' then null else str_to_date(t.date_debut_pa,'%d/%m/%Y') end , case when t.date_fin_pa='' then null else str_to_date(t.date_fin_pa,'%d/%m/%Y') end, t.avancement,1
				    from temp_risquemetier t inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id where t.desc_pa!='';";
		// ajouter les avancement des PAs

        $query .= "INSERT INTO `avancement`(`acteur`, `plan_action_id`, `etat_avancement_id`, `date_action`, `description`, `etat`)
				   select distinct ".$current_user->getId().", pa.id,null,now(), pa.description, 1
				   from plan_action pa
				   inner join risque_has_cause rhc on pa.risque_cause_id=rhc.id
				   inner join temp_risquemetier t on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   where pa.description !='' ;";
		$this->connection->prepare($query)->execute();
	}


	public function  createNewProcessus($ids,$chargement,$em){
		// creer macro inexistant
		$query = "select distinct t.macro macro, t.macro_sans_carspec macro_sans_carspec
				  from  temp_risquemetier t
				  left join processus p on t.macro_sans_carspec = p.libelle_sans_carspecial and p.type_processus_id =".$ids['type_processus']['macro']." and p.structure_id=".$chargement->getDirection()->getId() ."
				  left join structure s on s.id = p.structure_id
				  where p.id is null";
		$results = $this->connection->fetchAll($query);
		for($i = 0; $i < count($results); $i ++) {
			$proc = new Processus();
			$proc->setLibelle($results[$i]['macro']);
			$proc->setDescription($results[$i]['macro']);
			$proc->setParent(null);
			$proc->setStructure($chargement->getDirection());
			$proc->setTypeProcessus($em->getReference(TypeProcessus::class,$ids['type_processus']['macro']));
			$proc->setLibelleSansCarSpecial($results[$i]['macro_sans_carspec']);
			$em->persist($proc);
		}
		$em->flush();
		// creer proc inexistants
		$query = "select distinct t.processus processus, t.processus_sans_carspec processus_sans_carspec , p1.id parent, t.sous_entite structure
				  from  temp_risquemetier t
				  left join processus p on t.processus_sans_carspec = p.libelle_sans_carspecial and p.type_processus_id =".$ids['type_processus']['normal']." and p.structure_id  = t.sous_entite
				  left join processus p1 on t.macro_sans_carspec=p1.libelle_sans_carspecial and p1.structure_id=".$chargement->getDirection()->getId() ."
				  where p.id is null";
		$results = $this->connection->fetchAll($query);
		for($i = 0; $i < count($results); $i ++) {
			$proc = new Processus();
			$proc->setLibelle($results[$i]['processus']);
			$proc->setDescription($results[$i]['processus']);
			$proc->setParent($em->getReference(Processus::class,$results[$i]['parent']));
			$proc->setStructure($em->getReference(Structure::class,$results[$i]['structure']));
			$proc->setTypeProcessus($em->getReference(TypeProcessus::class,$ids['type_processus']['normal']));
			$proc->setLibelleSansCarSpecial($results[$i]['processus_sans_carspec']);
			$em->persist($proc);
		}
		$em->flush();
		// creer sous proc inexistants
		$query = "select distinct t.sous_processus processus, p1.id parent, t.sous_entite structure, t.sous_processus_sans_carspec sous_processus_sans_carspec 
				  from  temp_risquemetier t
				  left join processus p on t.sous_processus_sans_carspec = p.libelle_sans_carspecial and p.type_processus_id =".$ids['type_processus']['sous']."  and p.structure_id  = t.sous_entite
				  left join processus p1 on t.processus_sans_carspec=p1.libelle_sans_carspecial and p1.structure_id=t.sous_entite
				  where p.id is null";
		$results = $this->connection->fetchAll($query);
		for($i = 0; $i < count($results); $i ++) {
			$proc = new Processus();
			$proc->setLibelle($results[$i]['processus']);
			$proc->setDescription($results[$i]['processus']);
			$proc->setParent($em->getReference(Processus::class,$results[$i]['parent']));
			$proc->setStructure($em->getReference(Structure::class,$results[$i]['structure']));
			$proc->setTypeProcessus($em->getReference(TypeProcessus::class,$ids['type_processus']['sous']));
			$proc->setLibelleSansCarSpecial($results[$i]['sous_processus_sans_carspec']);
			$em->persist($proc);
		}
		$em->flush();
	}

	/**
	 *
	 * @param Utilisateur $current_user
	 * @param Chargement $chargement
	 */
	public function migrateEvaluation($current_user, $em, $chargement) {
		$nextId = $em->getRepository(Impact::class)->getNextId();
		$annee = date('Y');
		// creation evaluation pour chaque risque
		$query = "INSERT INTO `evaluation`(`evaluateur`, `validateur`, `criticite_id`, `risque_id`, `date_evaluation`, `probabilite`, `gravite`,  `transfered`, `annee`)
				 SELECT " . $current_user->getId() . "," . $current_user->getId() . ", null, t.best_id,  NOW(), t.probabilite, t.gravite, 1," . $annee . " FROM temp_risquemetier t where t.gravite !='' group by t.best_id;";

		// remplir la table eval_has_cause
		$query .= "INSERT INTO `evaluation_has_cause`(`evaluation_id`, `cause_id`, `mode_fonctionnement_id`, `grille_id`, `maturite_id`)
				 select distinct e.id, rhc.cause_id, null, rhc.grille_id, null
				 from evaluation e
				 inner join  risque_has_cause rhc on e.risque_id = rhc.risque_id
				 inner join  temp_risquemetier t  on t.best_id   = rhc.risque_id ;";

		// creer une table temporaire temp_impact
		$query .= sprintf("DROP TABLE IF EXISTS `temp_impact`;CREATE TABLE `temp_impact`(
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `critere_id` int(11) DEFAULT NULL,
				  `origine` int(11) DEFAULT NULL,
				  `date_creation` datetime default NULL,
				  `etat` tinyint(1) DEFAULT NULL,
				  `risque_id` int(11) DEFAULT NULL,
				   PRIMARY KEY(`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=%s;", $nextId);

		// remplir la table temp_impact
		$query .= "INSERT INTO `temp_impact`(`critere_id`, `origine`, `date_creation`, `etat`,`risque_id`)
				  SELECT chs.critere_id, null, NOW(), 1, t.id
				  FROM chargement_has_critere chs
				  INNER JOIN temp_risque t on 1=1
				  where chs.chargement_id=" . $chargement->getId() . ";";

		// remplir impact
		$query .= "INSERT INTO `impact`(`id`, `critere_id`, `origine`, `date_creation`, `etat`)
				  SELECT t.id , t.critere_id, t.origine, t.date_creation , t.etat
				  FROM temp_impact t;";

		// remplir risque_has_impact
		$query .= "INSERT INTO `risque_has_impact`(`risque_id`, `impact_id`, `grille_id`)
				  select  distinct t.risque_id, t.id, null
				  from   temp_impact t;";

		// remplir evaluation_has_impact
		$query .= "INSERT INTO `evaluation_has_impact`(`evaluation_id`, `impact_id`, `grille_id`)
				  select e.id, t.id, null
				  from   temp_impact t
				  inner join evaluation e on e.risque_id=t.risque_id;";

		$this->connection->prepare($query)->execute();
	}
	/**
	 *
	 * @param Chargement $chargement
	 * @param unknown $ids
	 */
	public function calculFinal($chargement, $ids) {
		$domaines = array(
				'financier' => 'financier',
				'RH' => 'Ressources Humaines',
				'Image' => 'Images',
				'juridique' => 'Juridique',
				'client' => 'Commercial'
		);
		// renseigner probabilte
		$query = "update risque r,(select max(probabilite) proba , best_id id from temp_risquemetier group by best_id ) tm
				 set r.probabilite = tm.proba
				 where r.id=tm.id;";

		$query .= "update evaluation e
				inner join risque r on r.id=e.risque_id
				inner join temp_risque t on t.id=r.id
				set e.probabilite =  case when r.probabilite=null then 0 else r.probabilite end ;";

		// renseigner gravite risque et maturite
		$query .= "update risque r
				 inner join temp_risquemetier t on t.best_id = r.id
				 set r.gravite=(case  when t.gravite!='' then t.gravite else null end) ,
				 r.maturite_theorique_id=(case  when t.maturite!='' then t.maturite else null end);";
		// renseigner maturite du risque
		$query .= "update risque r
				   inner join temp_risquemetier t on t.best_id = r.id
				   inner join criticite c on t.criticite between c.valeur_minimum  and c.valeur_maximum
				   set r.criticite_id=c.id;";

		foreach($domaines as $key => $valeur)
			$query .= "update evaluation_has_impact ehi
					   inner join temp_impact i on i.id=ehi.impact_id
					   inner join temp_risquemetier tr on tr.best_id=i.risque_id and tr." . $key . "!=''
					   inner join note n on n.valeur=tr." . $key . "
					   inner join grille g on n.id=g.note_id
					   inner join grille_impact gi on g.id=gi.grille_id AND gi.critere_id = i.critere_id 
					   inner join critere c on c.id = i.critere_id
					   inner join domaine_impact d on d.id = c.domaine_id 
					   inner join domaine_impact d2 on d.root = d2.id and trim(lower(d2.libelle)) like '" . $valeur . "'
					   inner join type_grille tg on tg.id =g.type_grille_id and tg.cartographie_id=" . $chargement->getCartographie()->getId() . " and tg.type_evaluation_id=" . $ids ['type_evaluation'] ['impact'] . "
					   set ehi.grille_id=g.id;";
				
			// faire historique du chargement
			$query .= " INSERT INTO `chargement_has_risque`(`chargement_id`, `risque_id`)
					select c.id, t.id
					from chargement c
					inner join temp_risque t on 1=1
					where c.id=" . $chargement->getId() . ";";
			$query .= "update chargement set etat=1 where id=" . $chargement->getId() . ";";
			$this->connection->prepare($query)->execute();
	}
}
