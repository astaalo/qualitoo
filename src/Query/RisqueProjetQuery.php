<?php

namespace App\Query;

use App\Entity\Chargement;
use App\Entity\Impact;
use Doctrine\ORM\EntityManager;
use App\Entity\Utilisateur;
use Doctrine\DBAL\DBALException;
use App\Entity\Processus;
use App\Entity\Risque;

class RisqueProjetQuery extends BaseQuery {
	
	public function createTable($next_id) {
		$query = sprintf("DROP TABLE IF EXISTS `temp_risqueprojet`;
				CREATE TABLE IF NOT EXISTS `temp_risqueprojet`(
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `structure` varchar(255) DEFAULT NULL,
				  `processus` varchar(255) DEFAULT NULL,
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

	public function loadTable($fileName, $web_dir, $next_id) {
		$newPath = $this->loadDataFile($fileName, 'risque_projet', $web_dir);
		$query = "LOAD DATA LOCAL INFILE '$newPath' INTO TABLE temp_risqueprojet
		CHARACTER SET latin1
		FIELDS TERMINATED BY  ';' ENCLOSED BY  '".'"'."'
		LINES TERMINATED BY  '\\r\\n'
		IGNORE 1 LINES
		(`menace`, `cause`,`desc_pa` ,`porteur`,`email_porteur`,`date_debut_pa` ,`date_fin_pa` ,
		`avancement` ,`statut`,`desc_ctrl`,`type_ctrl`, `methode_ctrl` ,`probabilite` ,`gravite`,
		`financier` , `RH`,`image` ,`juridique`, `client` ,`criticite`,`maturite`);";
		$this->connection->prepare($query)->execute();
	}

	/**
	 * @param Chargement $chargement
	 */
	public function updateTable($chargement, $ids, $em) {
		$erreurs = array();
		$query = "";
		// Mettre a jour les Ids des structures
		$query .= 'UPDATE menace SET libelle_sans_carspecial  = libelle ;';
		$query .= 'UPDATE cause SET libelle_sans_carspecial  = libelle ;';

		$query .= 'UPDATE temp_risqueprojet SET menace_sans_carspec  = menace ;';
		$query .= 'UPDATE temp_risqueprojet SET cause_sans_carspec  = cause ;';

		for($i = 0; $i < count($this->special_char); $i ++) {
			$query .= "UPDATE temp_risqueprojet SET menace_sans_carspec  = REPLACE(menace_sans_carspec, '".$this->special_char [$i]."', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risqueprojet SET cause_sans_carspec  = REPLACE(cause_sans_carspec, '".$this->special_char [$i]."', '{$this->replacement_char[$i]}');";
				
			$query .= "UPDATE menace SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '".$this->special_char [$i]."','{$this->replacement_char[$i]}');";
			//$query .= "UPDATE cause  SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '".$this->special_char [$i]."','{$this->replacement_char[$i]}');";
		}
		$this->connection->prepare($query)->execute();

		$toString = serialize($erreurs);
		if(count($erreurs) > 0) {
			throw new DBALException($toString);
		}
		// creer cause inexistant et les rattacher a au carto
		$query .= "INSERT INTO `cause`(`libelle`, `etat`, `description`, `libelle_sans_carspecial`)
			   select distinct t.cause, 1, t.cause, t.cause_sans_carspec
			   from temp_risqueprojet t
			   left join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
			   where c.id is null;";

		$query .= "INSERT INTO `cartographie_has_causes`(`cause_id`, `carto_id`)
			   select distinct c.id, ".$chargement->getCartographie()->getId()."
			   from temp_risqueprojet t
			   inner join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
			   left join cartographie_has_causes chc on c.id=chc.cause_id and chc.carto_id=".$chargement->getCartographie()->getId()."
			   where chc.cause_id is null;";

		//  creer les menaces inexistants
		$query .= "INSERT INTO `menace`(`libelle`, `description`, `etat`, `libelle_sans_carspecial`)
			   select distinct t.menace, t.menace, 1, t.menace_sans_carspec
			   from temp_risqueprojet t
			   left join menace m on m.libelle_sans_carspecial =t.menace_sans_carspec
			   where m.id is null;";

		$query .= sprintf("UPDATE temp_risqueprojet t SET t.processus = %s;", $chargement->getProjet()->getProcessus()->getId());
		$query .= sprintf("UPDATE temp_risqueprojet t SET t.structure = %s;", $chargement->getProjet()->getProcessus()->getStructure()->getId());
		$query .= "UPDATE temp_risqueprojet t INNER JOIN menace m on lower(m.libelle_sans_carspecial) = lower(t.menace_sans_carspec) SET t.menace = m.id;";
		$query .= "UPDATE temp_risqueprojet t INNER JOIN cause c on lower(c.libelle_sans_carspecial) = lower(t.cause_sans_carspec) INNER JOIN cartographie_has_causes chs on chs.cause_id = c.id SET t.cause = c.id WHERE chs.carto_id = ".$chargement->getCartographie()->getId().";";
		$query .= "UPDATE temp_risqueprojet t INNER JOIN utilisateur u on trim(lower(u.email)) = trim(lower(t.email_porteur)) SET t.email_porteur = u.id;";
		$query .= "UPDATE temp_risqueprojet  SET email_porteur = null where email_porteur='' ;";
		$query .= "UPDATE temp_risqueprojet t INNER JOIN statut  st on trim(lower(t.statut)) = trim(lower(st.libelle)) SET t.statut = st.id ;";
		$query .= "UPDATE temp_risqueprojet SET statut = null where statut like '';";
		$query .= "UPDATE temp_risqueprojet t INNER JOIN methode_controle m on trim(lower(t.methode_ctrl)) = trim(lower(m.libelle)) SET t.methode_ctrl = m.id;";
		$query .= "UPDATE temp_risqueprojet SET methode_ctrl = null where methode_ctrl like '';";
		$query .= "UPDATE temp_risqueprojet t INNER JOIN type_controle tc on trim(lower(t.type_ctrl)) = trim(lower(tc.libelle)) SET t.type_ctrl = tc.id;";
		$query .= "UPDATE temp_risqueprojet SET type_ctrl = null where type_ctrl like '';";
		$query .= "UPDATE temp_risqueprojet SET maturite = null where maturite like '';";
		$this->connection->prepare($query)->execute();
		$erreurs = array();
		$results = $this->connection->fetchAll("SELECT distinct id, menace, cause ,email_porteur,statut, `type_ctrl`, `methode_ctrl`, `probabilite`, `gravite` from temp_risqueprojet");
		for($i = 0; $i < count($results); $i ++) {
			if(ctype_digit($results [$i] ['menace']) == false) {
				$erreurs [] = sprintf("La menace à la ligne %s n'existe pas", $i + 2);
			}
			if(ctype_digit($results [$i] ['email_porteur']) == false && is_null($results [$i] ['email_porteur']) == false) {
				$erreurs [] = sprintf("Le  porteur a la %s n'existe pas", $i + 2);
			}
			if(ctype_digit($results [$i] ['statut']) == false && is_null($results [$i] ['statut']) == false) {
				$erreurs [] = sprintf("Le statut a la ligne %s n'existe pas ", $i + 2);
			}
			if(ctype_digit($results [$i] ['type_ctrl']) == false && is_null($results [$i] ['type_ctrl']) == false) {
				$erreurs [] = sprintf("Le type de controle à la ligne %s est incorrecte ", $i + 2);
			}
			if(ctype_digit($results [$i] ['methode_ctrl']) == false && is_null($results [$i] ['methode_ctrl']) == false) {
				$erreurs [] = sprintf("Le methode de controle %s est incorrecte ", $i + 2);
			}
			if(ctype_digit($results [$i] ['cause']) == false) {
				$erreurs [] = sprintf("La cause  a la ligne %s n'existe pas ", $i + 2);
			}
			if(ctype_digit($results [$i] ['probabilite']) == false) {
				$erreurs [] = sprintf("La probabilté à la ligne %s est invalide ", $i + 2);
			}
			if(ctype_digit($results [$i] ['gravite']) == false) {
				$erreurs [] = sprintf("La gravité à la ligne %s est invalide ", $i + 2);
			}
		}
		$toString = serialize($erreurs);
		if(count($erreurs) > 0) {
			throw new DBALException($toString);
		}
	}

	/**
	 * @param Utilisateur $current_user
	 * @param EntityManager $em
	 */
	public function migrateData($current_user, $em, $chargement, $ids) {
		$nextId = $em->getRepository(Risque::class)->getNextId();
		// creer une table temporaire risque
		$query = sprintf("DROP TABLE IF EXISTS `temp_risque`;
				             CREATE TABLE `temp_risque`(`id` int(11) NOT NULL AUTO_INCREMENT,`menace_id` int(11) DEFAULT NULL,`processus_id` int(11) DEFAULT NULL,`projet_id` int(11) DEFAULT NULL,`probabilite` int(11) DEFAULT NULL,
				             PRIMARY KEY(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=%s;", $nextId);
		$this->connection->prepare($query)->execute();

		$this->migrateRisque($current_user, $chargement);
		$this->migrateCauseControleAndPlanAction($ids, $chargement,$current_user);
		$this->migrateEvaluation($current_user, $em, $chargement);
	}
	
	/**
	 * 
	 */
	public function deleteTable() {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_risqueprojet`;DROP TABLE IF EXISTS `temp_risque`;DROP TABLE IF EXISTS `temp_impact`;"));
		$statement->execute();
	}
	
	/**
	 * @param Utilisateur $current_user
	 * @param Chargement $chargement
	 */
	public function migrateRisque($current_user, $chargement) {
		// remplir la table temporaire risque
		$query = sprintf("INSERT INTO temp_risque (menace_id, processus_id, projet_id)
				  SELECT distinct menace, processus, %s from temp_risqueprojet;", $chargement->getProjet()->getId());
		// inserer les risques dans la table risque
		$query .= "INSERT INTO `risque`(`id`, `menace_id`, `utilisateur_id`, `societe_id`, `validateur`,   `cartographie_id`,   `date_saisie`, `date_validation`,`etat`, `relanced`)
				   select t.id, t.menace_id,".$current_user->getId().",".$current_user->getSociete()->getId().",".$current_user->getId().", ".$chargement->getCartographie()->getId().", NOW(), NOW() ,1,0
				   from temp_risque t;";

		// renseigner dans la temp_risqueprojet le bon id risque
		$query .= "update temp_risqueprojet t , temp_risque tr set t.best_id=tr.id where tr.menace_id=t.menace;";
		// inserer les risques dans la table risque_metier
		$query .= sprintf("INSERT INTO `risque_projet`(`risque_id`, `processus_id`, `projet_id`, `structure_id`)
				   SELECT distinct best_id, processus, %s, structure FROM temp_risqueprojet;", $chargement->getProjet()->getId());
		$this->connection->prepare($query)->execute();
	}

	/**
	 * @param array $ids
	 * @param Chargement $chargement
	 * @param EntityManager $em
	 */
	public function migrateCauseControleAndPlanAction($ids, $chargement,$current_user) {
		// ajouter les causes des risques dans risque_has_cause
		$query = " INSERT INTO `risque_has_cause`(`risque_id`, `cause_id`, `grille_id`,`transfered`)
				   select t.`best_id`, t.`cause`, g.id, 1
				   from temp_risqueprojet t
				   left join type_grille tg on tg.type_evaluation_id =".$ids['type_evaluation']['cause']." and tg.cartographie_id=".$chargement->getCartographie()->getId()."
				   left join note n on tg.id  = n.type_grille_id and n.valeur = t.probabilite
				   left join grille g on tg.id  = g.type_grille_id and n.id   = g.note_id
				   group by t.best_id, t.cause;";

		// ajouter les controles
		$query .= "INSERT INTO `controle`(`risque_cause_id`, `methode_controle_id`,  `type_controle_id`, `maturite_theorique_id`, `description`,  `date_creation` ,`transfered`)
				   SELECT distinct rhc.id,  t.methode_ctrl, t.type_ctrl,t.maturite,desc_ctrl, NOW(), 1
				   from temp_risqueprojet t
				   inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   where t.desc_ctrl!='';";

		// ajouter les PAs
		$query .= "INSERT INTO `plan_action`(`porteur`, `nom_porteur`, `statut_id`, `risque_cause_id`, `libelle`, `date_debut`, `date_fin`, `description`,`transfered`)
				   SELECT distinct t.email_porteur,t.porteur, t.statut, rhc.id, t.desc_pa, case when t.date_debut_pa='' then null else str_to_date(t.date_debut_pa,'%d/%m/%Y') end , case when t.date_fin_pa='' then null else str_to_date(t.date_fin_pa,'%d/%m/%Y') end, t.avancement,1
				    from temp_risqueprojet t inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id where t.desc_pa!='';";
		// ajouter les avancement des PAs
		$query .= "INSERT INTO `avancement`(`acteur`, `plan_action_id`, `etat_avancement_id`, `date_action`, `description`, `etat`)
				   select distinct ".$current_user->getId().", pa.id,null,now(), pa.description, 1
				   from plan_action pa
				   inner join risque_has_cause rhc on pa.risque_cause_id=rhc.id
				   inner join temp_risqueprojet t on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   where pa.description !='' ;";
		$this->connection->prepare($query)->execute();
	}

	/**
	 * @param Utilisateur $current_user
	 * @param Chargement $chargement
	 */
	public function migrateEvaluation($current_user, $em, $chargement) {
		$nextId = $em->getRepository(Impact::class)->getNextId();
		$annee = date('Y');
		// creation evaluation pour chaque risque
        //$query = "INSERT INTO `evaluation`(`evaluateur`, `validateur`, `criticite_id`, `risque_id`, `date_evaluation`, `probabilite`, `gravite`,  `transfered`, `annee`)
		//		 SELECT ".$current_user->getId().",".$current_user->getId().", null, t.best_id,  NOW(), null, t.gravite, 1,".$annee." FROM temp_risqueprojet t where t.gravite !='' group by t.best_id;";
        $query = "INSERT INTO `evaluation`(`evaluateur`, `validateur`, `criticite_id`, `risque_id`, `date_evaluation`, `probabilite`, `gravite`,  `transfered`, `annee`)
				 SELECT ".$current_user->getId().",".$current_user->getId().", t.criticite, t.best_id,  NOW(), t.probabilite, t.gravite, 1,".$annee." FROM temp_risqueprojet t where t.gravite !='' group by t.best_id;";

        // remplir la table eval_has_cause
		$query .= "INSERT INTO `evaluation_has_cause`(`evaluation_id`, `cause_id`, `mode_fonctionnement_id`, `grille_id`, `maturite_id`)
				 select distinct e.id, rhc.cause_id, null, rhc.grille_id, null
				 from evaluation e
				 inner join  risque_has_cause rhc on e.risque_id = rhc.risque_id
				 inner join  temp_risqueprojet t  on t.best_id   = rhc.risque_id ;";

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
				  FROM chargement_has_critere chs INNER JOIN temp_risque t on 1=1
				  where chs.chargement_id=".$chargement->getId().";";

		// remplir impact
		$query .= "INSERT INTO `impact`(`id`, `critere_id`, `origine`, `date_creation`, `etat`)
				  SELECT t.id , t.critere_id, t.origine, t.date_creation , t.etat FROM temp_impact t;";

		// remplir risque_has_impact
		$query .= "INSERT INTO `risque_has_impact`(`risque_id`, `impact_id`, `grille_id`)
				  select  distinct t.risque_id, t.id, null from temp_impact t;";

		// remplir evaluation_has_impact
		$query .= "INSERT INTO `evaluation_has_impact`(`evaluation_id`, `impact_id`, `grille_id`)
					select e.id, t.id, null from temp_impact t
					inner join evaluation e on e.risque_id=t.risque_id;";

		$this->connection->prepare($query)->execute();
	}
	
	/**
	 * @param Chargement $chargement
	 * @param unknown $ids
	 */
	public function calculFinal($chargement, $ids) {
		$domaines = array(
				'financier' => 'Financier',
				'RH' => 'Ressources Humaines',
				'image' => 'Images',
				'juridique' => 'Juridique',
				'client' => 'Commercial'
		);
		// renseigner probabilte
		$query = "update risque r,(select max(probabilite) proba , best_id id from temp_risqueprojet group by best_id ) tm
				 set r.probabilite = tm.proba
				 where r.id=tm.id;";

		// renseigner gravite risque et maturite
		$query .= "update risque r
				 inner join temp_risqueprojet t on t.best_id = r.id
				 set r.gravite=(case  when t.gravite!='' then t.gravite else null end) ,
				 r.maturite_theorique_id=(case  when t.maturite!='' then t.maturite else null end);";
		// renseigner maturite du risque
		$query .= "update risque r
				   inner join temp_risqueprojet t on t.best_id = r.id
				   inner join criticite c on (r.gravite * r.probabilite) between c.valeur_minimum  and c.valeur_maximum
				   set r.criticite_id=c.id;";

		$query .= "update evaluation e
				inner join risque r on r.id=e.risque_id
				inner join temp_risque t on t.id=r.id
				set e.probabilite = r.probabilite, e.criticite_id = r.criticite_id;";

		foreach($domaines as $key => $valeur) {
			$query .= "update evaluation_has_impact ehi
				inner join temp_impact i on i.id=ehi.impact_id
			   	inner join temp_risqueprojet tr on tr.best_id=i.risque_id and tr.".$key."!=''
			   	inner join note n on n.valeur=tr.".$key."
			   	inner join grille g on n.id=g.note_id
			   	inner join grille_impact gi on g.id=gi.grille_id AND gi.critere_id = i.critere_id
			   	inner join critere c on c.id = i.critere_id
			   	inner join domaine_impact d on d.id = c.domaine_id
			   	inner join domaine_impact d2 on d.root = d2.id and d2.libelle like '".$valeur."'
			   	inner join type_grille tg on tg.id = g.type_grille_id and tg.cartographie_id=".$chargement->getCartographie()->getId()." and tg.type_evaluation_id=".$ids ['type_evaluation'] ['impact']."
			   	set ehi.grille_id=g.id;";
		}

		$query .= "UPDATE risque_has_impact rhi 
				inner join risque r on rhi.risque_id=r.id
				inner join evaluation e on e.risque_id=r.id 
				inner join evaluation_has_impact ehi on ehi.evaluation_id=e.id 
				inner join impact ri on ri.id=rhi.impact_id
				inner join impact ei on ei.id=ehi.impact_id 
				inner join temp_risque t on t.id=r.id 
				set rhi.grille_id=ehi.grille_id	where ei.critere_id=ri.critere_id;";
		$query .= "delete ehi.*, rhi.* from evaluation e inner join risque r on r.id=e.risque_id
				inner join evaluation_has_impact ehi on ehi.evaluation_id=e.id
				inner join risque_has_impact rhi on rhi.risque_id=r.id inner join temp_risque t on t.id=r.id 
				where ehi.grille_id is null and rhi.grille_id is null;";
		// faire historique du chargement
		$query .= " INSERT INTO `chargement_has_risque`(`chargement_id`, `risque_id`)
				select c.id, t.id
				from chargement c
				inner join temp_risque t on 1=1
				where c.id=".$chargement->getId().";";
		$query .= "update chargement set etat=1 where id=".$chargement->getId().";";
		$this->connection->prepare($query)->execute();
	}
}
