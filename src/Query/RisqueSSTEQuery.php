<?php

namespace App\Query;

use App\Entity\Chargement;
use App\Entity\Impact;
use App\Entity\Risque;
use Doctrine\ORM\EntityManager;
use App\Entity\Utilisateur;
use Doctrine\DBAL\DBALException;
use App\Entity\Processus;

class RisqueSSTEQuery extends BaseQuery {
	public function createTable($next_id) {
		$query = sprintf ( "DROP TABLE IF EXISTS `temp_risquesste`;
				CREATE TABLE IF NOT EXISTS `temp_risquesste` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `site` varchar(255) DEFAULT NULL,
				  `site_sans_carspec` varchar(255) DEFAULT NULL,
				  `domaine_activite` varchar(255) DEFAULT NULL,
				  `domaine_activite_sans_carspec` varchar(255) DEFAULT NULL,
				  `activite_equipement` varchar(255) DEFAULT NULL,
				  `activite_equipement_sans_carspec` varchar(255) DEFAULT NULL,
				  `type_equipement_activite` varchar(255) DEFAULT NULL,
				  `proprietaire` varchar(255) DEFAULT NULL,
				  `proprietaire_sans_carspec` varchar(255) DEFAULT NULL,
				  `cause` varchar(255) DEFAULT NULL,
				  `cause_sans_carspec` varchar(255) DEFAULT NULL,
				  `mode_fonctionnement` varchar(255) DEFAULT NULL,
				  `menace` varchar(255) DEFAULT NULL,
				  `menace_sans_carspec` varchar(255) DEFAULT NULL,
				  `lieu` varchar(255) DEFAULT NULL,
				  `lieu_sans_carspec` varchar(255) DEFAULT NULL,
				  `manifestation` text DEFAULT NULL,
				  `manifestation_sans_carspec` varchar(255) DEFAULT NULL,
				  `desc_ctrl` longtext DEFAULT NULL,
				  `type_ctrl` varchar(255) DEFAULT NULL,
				  `methode_ctrl` varchar(255) DEFAULT NULL,
				  `desc_pa` longtext DEFAULT NULL,
				  `email_porteur` varchar(255) DEFAULT NULL,
				  `date_debut_pa` varchar(255) DEFAULT NULL,
				  `date_fin_pa` varchar(255) DEFAULT NULL,
				  `avancement` varchar(255) DEFAULT NULL,
				  `statut` varchar(255) DEFAULT NULL,
				  `probabilite` varchar(255) DEFAULT NULL,
				  `gravite` varchar(255) DEFAULT NULL,
				  `criticite` varchar(255) DEFAULT NULL,
				  `maturite` varchar(255) DEFAULT NULL,
				  `best_id` varchar(255) DEFAULT NULL,
				   PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;" );
		try {
			$exec = $this->connection->prepare ( $query )->execute ();
		} catch ( DBALException $e ) {
			throw $e->getMessage ();
		}
	}

	public function loadTable($fileName, $web_dir, $next_id) {
		$newPath = $this->loadDataFile ( $fileName, 'risque_sste', $web_dir );
		$query = "LOAD DATA LOCAL INFILE '$newPath' INTO TABLE temp_risquesste
		CHARACTER SET latin1 
		FIELDS TERMINATED BY  ';' ENCLOSED BY  '".'"'."'
		LINES TERMINATED BY  '\\r\\n'
		IGNORE 1 LINES
		(`site`, `domaine_activite`, `activite_equipement`, `type_equipement_activite`,
		`proprietaire`,`cause` , `mode_fonctionnement`,`menace`,
		`lieu` ,`manifestation` ,`desc_ctrl` ,`type_ctrl` ,`methode_ctrl` ,`desc_pa` ,
		`email_porteur` , `date_debut_pa` , `date_fin_pa` , `avancement` , `statut` ,`probabilite` ,
		`gravite` ,`criticite` , `maturite`);";
		$this->connection->prepare ( $query )->execute ();
	}

	/**
	 * @param Chargement $chargement
	 */
	public function updateTable($chargement,$ids,$em,$current_user) {
		$erreurs = array ();
		$query = "";
		// Mettre a jour les Ids des sites
        $query .= 'UPDATE site SET libelle_sans_carspecial  = libelle;';
        $query .= 'UPDATE domaine_activite SET libelle_sans_carspecial  = libelle ;';
        $query .= 'UPDATE lieu SET libelle_sans_carspecial  = libelle;';
        $query .= 'UPDATE manifestation SET libelle_sans_carspecial  = libelle ;';
        $query .= 'UPDATE equipement SET libelle_sans_carspecial  = libelle ;';
        $query .= 'UPDATE menace SET libelle_sans_carspecial  = libelle ;';
        $query .= 'UPDATE cause SET libelle_sans_carspecial  = libelle ;';

		$query .= 'UPDATE temp_risquesste SET site_sans_carspec = site;';
		$query .= 'UPDATE temp_risquesste SET domaine_activite_sans_carspec = domaine_activite;';
		$query .= 'UPDATE temp_risquesste SET lieu_sans_carspec  = lieu;';
		$query .= 'UPDATE temp_risquesste SET manifestation_sans_carspec  = manifestation;';
		$query .= 'UPDATE temp_risquesste SET activite_equipement_sans_carspec  = activite_equipement;';
		$query .= 'UPDATE temp_risquesste SET menace_sans_carspec  = menace;';
		$query .= 'UPDATE temp_risquesste SET cause_sans_carspec  = cause;';

		for($i = 0; $i < count ( $this->special_char ); $i ++) {
			$query .= "UPDATE temp_risquesste SET site_sans_carspec  = REPLACE(site_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET domaine_activite_sans_carspec  = REPLACE(domaine_activite_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET lieu_sans_carspec  = REPLACE(lieu_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET manifestation_sans_carspec  = REPLACE(manifestation_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET activite_equipement_sans_carspec  = REPLACE(activite_equipement_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET menace_sans_carspec  = REPLACE(menace_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";
			$query .= "UPDATE temp_risquesste SET cause_sans_carspec  = REPLACE(cause_sans_carspec, '" . $this->special_char [$i] . "', '{$this->replacement_char[$i]}');";

			$query .= "UPDATE site SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE domaine_activite SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE lieu SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE manifestation SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE equipement SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE menace SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
			$query .= "UPDATE cause SET libelle_sans_carspecial  = REPLACE(libelle_sans_carspecial, '" . $this->special_char [$i] . "','{$this->replacement_char[$i]}');";
		}
		$query .= "UPDATE temp_risquesste SET type_equipement_activite = null where type_equipement_activite like '';";
		$this->connection->prepare ( $query )->execute ();
        $query = "";
			//creer site inexistant
			$query .=  "INSERT INTO `site`(`libelle`, `etat`, `responsable_id`, `societe_id`, `code`, `libelle_sans_carspecial`)
					    select distinct t.site, 1, null,".$current_user->getSociete ()->getId ().",null, t.site_sans_carspec
				        from temp_risquesste t
				        left join site s on s.libelle_sans_carspecial = t.site_sans_carspec
				        where s.id is null;";
			
			//creer domaine activite inexistant
			$query .=  "INSERT INTO `domaine_activite`( `libelle`, `etat`, `libelle_sans_carspecial`) 
					    select distinct  t.domaine_activite , 1, t.domaine_activite_sans_carspec
				        from temp_risquesste t
				        left join domaine_activite d on d.libelle_sans_carspecial = t.domaine_activite_sans_carspec
				        where d.id is null;";
			
			//creer equipement inexistant
			$query .=  "INSERT INTO `equipement`( `societe_id`, `libelle`, `etat`, `type`, `libelle_sans_carspecial`)
					    select distinct ".$current_user->getSociete ()->getId ()." , t.activite_equipement , 1, t.type_equipement_activite, t.site_sans_carspec
				        from temp_risquesste t
				        left join equipement e on e.libelle_sans_carspecial = t.activite_equipement_sans_carspec and e.type = t.type_equipement_activite
				        where e.id is null;";
			
			//creer lieu inexistant
			$query .=  "INSERT INTO `lieu`( `libelle`, `cartographie_id`, `libelle_sans_carspecial`)
					    select distinct t.lieu, ".$chargement->getCartographie()->getId().", t.lieu_sans_carspec
				        from temp_risquesste t
				        left join lieu l on l.libelle_sans_carspecial = t.lieu_sans_carspec
				        where l.id is null;";
				
			//creer manifestation inexistant
			$query .=  "INSERT INTO `manifestation`( `libelle`, `libelle_sans_carspecial`)
					    select distinct t.manifestation,  t.manifestation_sans_carspec
				        from temp_risquesste t
				        left join manifestation m on m.libelle_sans_carspecial = t.manifestation_sans_carspec
				        where m.id is null;";

			// creer cause inexistant et les rattacher a au carto
			$query .= "INSERT INTO `cause`(`libelle`, `etat`, `description`, `libelle_sans_carspecial`)
				       select distinct t.cause, 1, t.cause, t.cause_sans_carspec
				       from temp_risquesste t
				       left join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
				       where c.id is null;";

			$query .= "INSERT INTO `cartographie_has_causes`(`cause_id`, `carto_id`)
				       select distinct c.id, ".$chargement->getCartographie()->getId()."
				       from temp_risquesste t
				       inner join cause c on c.libelle_sans_carspecial =t.cause_sans_carspec
				       left join cartographie_has_causes chc on c.id=chc.cause_id and chc.carto_id=".$chargement->getCartographie()->getId()."
				       where chc.cause_id is null;";

			//  creer les menaces inexistants
			$query .= "INSERT INTO `menace`( `libelle`, `description`, `etat`, `libelle_sans_carspecial`)
				       select distinct t.menace, t.menace, 1, t.menace_sans_carspec
				       from temp_risquesste t
				       left join menace m on m.libelle_sans_carspecial = t.menace_sans_carspec
				       where m.id is null;";
			
			$query .= "UPDATE temp_risquesste t INNER JOIN site  s on lower(s.libelle_sans_carspecial) = lower(t.site_sans_carspec) set t.site = s.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN domaine_activite  d on lower(d.libelle_sans_carspecial) = lower(t.domaine_activite_sans_carspec) set t.domaine_activite=d.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN lieu  l on lower(l.libelle_sans_carspecial) = lower(t.lieu_sans_carspec) set t.lieu=l.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN manifestation  m on lower(m.libelle_sans_carspecial) = lower(t.manifestation_sans_carspec) set t.manifestation=m.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN equipement  e on lower(e.libelle_sans_carspecial) = lower(t.activite_equipement_sans_carspec) and e.type = t.type_equipement_activite  set t.activite_equipement=e.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN menace m on lower(m.libelle_sans_carspecial) = lower(t.menace_sans_carspec) SET t.menace = m.id;";
			$query .= "UPDATE temp_risquesste t INNER JOIN cause c on lower(c.libelle_sans_carspecial) = lower(t.cause_sans_carspec) INNER JOIN cartographie_has_causes chs on chs.cause_id = c.id SET t.cause = c.id WHERE chs.carto_id = " . $chargement->getCartographie ()->getId () . ";";
			$query .= "UPDATE temp_risquesste t INNER JOIN utilisateur u on trim(lower(u.email)) = trim(lower(t.email_porteur)) SET t.email_porteur = u.id;";
			$query .= "UPDATE temp_risquesste  SET email_porteur = null where email_porteur='' ;";
			$query .= "UPDATE temp_risquesste t INNER JOIN statut  st on trim(lower(t.statut)) = trim(lower(st.libelle)) SET t.statut = st.id ;";
			$query .= "UPDATE temp_risquesste SET statut = null where statut like '';";
			$query .= "UPDATE temp_risquesste t INNER JOIN methode_controle m on trim(lower(t.methode_ctrl)) = trim(lower(m.libelle)) SET t.methode_ctrl = m.id;";
			$query .= "UPDATE temp_risquesste SET methode_ctrl = null where methode_ctrl like '';";
			$query .= "UPDATE temp_risquesste t INNER JOIN type_controle tc on trim(lower(t.type_ctrl)) = trim(lower(tc.libelle)) SET t.type_ctrl = tc.id;";
			$query .= "UPDATE temp_risquesste SET type_ctrl = null where type_ctrl like '';";
			$query .= "UPDATE temp_risquesste t INNER JOIN mode_fonctionnement  mf on trim(lower(mf.code)) = trim(lower(t.mode_fonctionnement)) SET t.mode_fonctionnement = mf.id ;";

			$this->connection->prepare ( $query )->execute ();
			$erreurs = array ();
			$results = $this->connection->fetchAll ( "SELECT distinct id,type_equipement_activite, site , mode_fonctionnement,domaine_activite, lieu, manifestation, activite_equipement ,cause,email_porteur,statut, `type_ctrl`, `methode_ctrl` from temp_risquesste" );
			for($i = 0; $i < count ( $results ); $i ++) {
				if (ctype_digit ( $results [$i] ['site'] ) == false) {
					$erreurs [] = sprintf ( "Le site a la ligne %s n'existe pas ", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['domaine_activite'] ) == false) {
					$erreurs [] = sprintf ( "Le domaine d\'activité à la ligne %s n'existe pas", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['lieu'] ) == false) {
					$erreurs [] = sprintf ( "Le lieu à la ligne %s n'existe pas", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['manifestation'] ) == false) {
					$erreurs [] = sprintf ( "La  manifestation a la %s n'existe pas", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['activite_equipement'] ) == false) {
					$erreurs [] = sprintf ( "L\' activite/equipement a la %s n'existe pas", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['type_equipement_activite'] ) == false) {
					$erreurs [] = sprintf ( "Le type d\' activite/equipement a la %s est incorrecte", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['email_porteur'] ) == false && is_null ( $results [$i] ['email_porteur'] ) == false) {
					$erreurs [] = sprintf ( "Le porteur a la %s n'existe pas", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['statut'] ) == false && is_null ( $results [$i] ['statut'] ) == false) {
					$erreurs [] = sprintf ( "Le statut a la ligne %s n'existe pas ", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['type_ctrl'] ) == false && is_null ( $results [$i] ['type_ctrl'] ) == false) {
					$erreurs [] = sprintf ( "Le type de controle à la ligne %s est incorrecte ", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['methode_ctrl'] ) == false && is_null ( $results [$i] ['methode_ctrl'] ) == false) {
					$erreurs [] = sprintf ( "Le methode de controle %s est incorrecte ", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['cause'] ) == false) {
					$erreurs [] = sprintf ( "La cause  a la ligne %s n'existe pas ", $i + 2 );
				}
				if (ctype_digit ( $results [$i] ['mode_fonctionnement'] ) == false) {
					$erreurs [] = sprintf ( "Le mode fonctionnement  a la ligne %s est incorrect ", $i + 2 );
				}
			}
			$toString = serialize($erreurs);
			if (count ( $erreurs ) > 0)
				throw new DBALException($toString);
	}

	/**
	 *
	 * @param Utilisateur $current_user
	 * @param EntityManager $em
	 */
	public function migrateData($current_user, $em, $chargement, $ids) {
		$nextId = $em->getRepository ( Risque::class )->getNextId ();
		// creer une table temporaire risque
		$query = sprintf ("DROP TABLE IF EXISTS `temp_risque`;
                                    CREATE TABLE `temp_risque` (  `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `site_id` int(11) DEFAULT NULL,`domaine_activite_id` int(11) DEFAULT NULL,
                                    `equipement_id` int(11) DEFAULT NULL,`menace_id` int(11) DEFAULT NULL,
                                    `lieu_id` int(11) DEFAULT NULL,`manifestation_id` int(11) DEFAULT NULL,
                                    `proprietaire` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,`risque_id_doublon` int(11) DEFAULT NULL,
                                    PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=%s;", $nextId);
		$this->connection->prepare ( $query )->execute ();

		$this->migrateRisque ( $current_user, $chargement,$ids );
		$this->migrateCauseControleAndPlanAction ( $ids, $chargement,$current_user );
		$this->migrateEvaluation ( $current_user, $em, $chargement );
	}
	
	public function migrateRisque($current_user, $chargement,$ids) {
		// remplir la table temporaire risque
		$query = "INSERT INTO temp_risque ( `site_id`,`domaine_activite_id`,`equipement_id`,`menace_id` ,`lieu_id`,`manifestation_id`)
				  SELECT distinct `site`,`domaine_activite`,`activite_equipement`,`menace` ,`lieu`,`manifestation`
				  from temp_risquesste;";

        $query .= "update temp_risquesste t , temp_risque tr
				   set t.best_id=tr.id
				   where tr.menace_id=t.menace and tr.site_id=t.site and tr.domaine_activite_id=t.domaine_activite and tr.equipement_id=t.activite_equipement
				   and tr.lieu_id=t.lieu and tr.manifestation_id=t.manifestation;";

		$this->connection->prepare($query)->execute();

        $table  = $chargement->getCartographie ()->getId ()==$ids['carto']['sst'] ? 'risque_sst' : 'risque_environnemental';
        $this->checkDoublonSSTE($table);
        // inserer les risques dans la table risque
		$query = "INSERT INTO `risque`(`id`, `menace_id`, `utilisateur_id`, `societe_id`, `validateur`,   `cartographie_id`,   `date_saisie`, `date_validation`,`etat`, `relanced`)
				   select t.id, t.menace_id," . $current_user->getId () . "," . $current_user->getSociete ()->getId () . "," . $current_user->getId () . ", " . $chargement->getCartographie ()->getId () . ", NOW(), NOW() ,1,0
				   from temp_risque t
				   WHERE t.risque_id_doublon IS NULL;";

		// inserer les risques dans la table risque_sst
		$query .= "INSERT INTO {$table} (`risque_id`, `site_id`, `domaine_activite_id`, `equipement_id`, `lieu_id`, `manifestation_id`, `proprietaire`) 
				   SELECT distinct t.best_id, t.site, t.domaine_activite, t.activite_equipement, t.lieu,t.manifestation,t.proprietaire 
		           FROM temp_risquesste t
                   INNER JOIN temp_risque tr on tr.id=t.best_id
                   WHERE tr.risque_id_doublon IS NULL;";
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
		$query = " INSERT INTO `risque_has_cause`( `risque_id`, `cause_id`, `grille_id`,`transfered`,`mode_fonctionnement_id`)
				   select t.`best_id`, t.`cause`, g.id, 1,t.mode_fonctionnement
				   from temp_risquesste t
				   left join type_grille tg on t.mode_fonctionnement = tg.mode_fonctionnement_id and tg.type_evaluation_id =".$ids['type_evaluation']['cause']." and tg.cartographie_id=".$chargement->getCartographie()->getId()."
				   left join note n on tg.id = n.type_grille_id and n.valeur = t.probabilite
				   left join grille g on tg.id = g.type_grille_id and n.id = g.note_id
				   group by t.best_id, t.cause;";

		// ajouter les controles
		$query .= "INSERT INTO `controle`( `risque_cause_id`, `methode_controle_id`,  `type_controle_id`, `maturite_theorique_id`, `description`,  `date_creation` ,`transfered`,`grille_id`)
				   SELECT distinct rhc.id,  t.methode_ctrl, t.type_ctrl,t.maturite,desc_ctrl, NOW(), 1, null
				   from temp_risquesste t
				   inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   where t.desc_ctrl!='';";

		// ajouter les PAs
		$query .= "INSERT INTO `plan_action`( `porteur`,  `statut_id`, `risque_cause_id`, `libelle`, `date_debut`, `date_fin`, `description`,`transfered`)
				   SELECT distinct t.email_porteur, t.statut, rhc.id, t.desc_pa, case when t.date_debut_pa='' then null else str_to_date(t.date_debut_pa,'%d/%m/%Y') end , case when t.date_fin_pa='' then null else str_to_date(t.date_fin_pa,'%d/%m/%Y') end, t.avancement,1
				    from temp_risquesste t inner join risque_has_cause rhc on rhc.cause_id = t.cause and rhc.risque_id=t.best_id where t.desc_pa!='';";
		// ajouter les avancement des PAs
		$query .= "INSERT INTO `avancement`(`acteur`, `plan_action_id`, `etat_avancement_id`, `date_action`, `description`, `etat`)
				   select distinct ".$current_user->getId().", pa.id,null,now(), pa.description, 1
				   from plan_action pa
				   inner join risque_has_cause rhc on pa.risque_cause_id=rhc.id
				   inner join temp_risquesste t on rhc.cause_id = t.cause and rhc.risque_id=t.best_id
				   where pa.description !='' ;";
		$this->connection->prepare ( $query )->execute ();
	}


	/**
	 *
	 * @param Utilisateur $current_user
	 * @param Chargement $chargement
	 */
	public function migrateEvaluation($current_user, $em, $chargement) {
		$nextId = $em->getRepository ( Impact::class )->getNextId ();
		$annee = date ( 'Y' );
		// creation evaluation pour chaque risque
		$query = "INSERT INTO `evaluation`(`evaluateur`, `validateur`, `criticite_id`, `risque_id`, `date_evaluation`, `probabilite`, `gravite`,  `transfered`, `annee`)
				 SELECT " . $current_user->getId () . "," . $current_user->getId () . ", null, t.best_id,  NOW(), t.probabilite, t.gravite, 1," . $annee . " FROM temp_risquesste t where t.gravite !='' group by t.best_id;";

		// remplir la table eval_has_cause
		$query .= "INSERT INTO `evaluation_has_cause`(`evaluation_id`, `cause_id`, `mode_fonctionnement_id`, `grille_id`, `maturite_id`)
				 select distinct e.id, rhc.cause_id, null, rhc.grille_id, null
				 from evaluation e
				 inner join  risque_has_cause rhc on e.risque_id = rhc.risque_id
				 inner join  temp_risquesste t  on t.best_id   = rhc.risque_id ;";

		// creer une table temporaire temp_impact
		$query .= sprintf ( "DROP TABLE IF EXISTS `temp_impact`;CREATE TABLE `temp_impact` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `critere_id` int(11) DEFAULT NULL,
				  `origine` int(11) DEFAULT NULL,
				  `date_creation` datetime default NULL,
				  `etat` tinyint(1) DEFAULT NULL,
				  `risque_id` int(11) DEFAULT NULL,
				   PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=%s;", $nextId );

		// remplir la table temp_impact
		$query .= "INSERT INTO `temp_impact`(`critere_id`, `origine`, `date_creation`, `etat`,`risque_id`)
				  SELECT distinct chs.critere_id, null, NOW(), 1, t.id
				  FROM chargement_has_critere chs
				  INNER JOIN temp_risque t on 1=1
				  where chs.chargement_id=" . $chargement->getId () . ";";

		// remplir impact
		$query .= "INSERT INTO `impact`(`id`, `critere_id`, `origine`, `date_creation`, `etat`)
				  SELECT t.id , t.critere_id, t.origine, t.date_creation , t.etat
				  FROM temp_impact t;";

		// remplir risque_has_impact
		/*$query .= "delete rhi.*
				   from risque_has_impact rhi
				   left join temp_impact t on t.risque_id=rhi.risque_id
				   where rhi.id is not null;";*/
		$query .= "INSERT INTO `risque_has_impact`(`risque_id`, `impact_id`, `grille_id`)
				  select distinct t.risque_id, t.id, null
				  from temp_impact t;";
		
		// remplir evaluation_has_impact
		$query .= "INSERT INTO `evaluation_has_impact`(`evaluation_id`, `impact_id`, `grille_id`)
				  select e.id, t.id, null
				  from   temp_impact t
				  inner join evaluation e on e.risque_id=t.risque_id;";

		$this->connection->prepare ( $query )->execute ();
		
	}
	/**
	 *
	 * @param Chargement $chargement
	 * @param unknown $ids
	 */
	public function calculFinal($chargement, $ids) {
		// renseigner probabilte
		$query = "update risque r, (select max(probabilite) proba , best_id id from temp_risquesste group by best_id  ) tm
				 set r.probabilite = tm.proba
				 where r.id=tm.id;";

		$query .= "update evaluation e
				inner join risque r on r.id=e.risque_id
				inner join temp_risque t on t.id=r.id
				set e.probabilite =  case when r.probabilite=null then 0 else r.probabilite end ;";

		// renseigner gravite risque et maturite
		$query .= "update risque r
				 inner join temp_risquesste t on t.best_id = r.id
				 set r.gravite= (case  when t.gravite!='' then t.gravite else null end) ,
				 r.maturite_theorique_id= (case  when t.maturite!='' then t.maturite else null end);";
		// renseigner maturite du risque
		$query .= "update risque r
				   inner join temp_risquesste t on t.best_id = r.id
				   inner join criticite c on t.criticite between c.valeur_minimum  and c.valeur_maximum
				   set r.criticite_id=c.id;";

		$query .= "update evaluation_has_impact ehi
				   inner join temp_impact i on i.id=ehi.impact_id
				   inner join temp_risquesste tr on tr.best_id=i.risque_id and tr.gravite!=''
				   inner join note n on n.valeur=tr.gravite
				   inner join grille g on n.id=g.note_id
				   inner join grille_impact gi on g.id=gi.grille_id AND gi.critere_id = i.critere_id 
				   inner join critere c on c.id = i.critere_id
				   inner join domaine_impact d on d.id = c.domaine_id 
				   inner join type_grille tg on tg.id =g.type_grille_id and tg.cartographie_id=" . $chargement->getCartographie ()->getId () . " and tg.type_evaluation_id=" . $ids ['type_evaluation'] ['impact'] . "
				   set ehi.grille_id=g.id;";
		
		if($chargement->getCartographie()->getId()==$ids['carto']['sst']) {
			$query .= "update controle ctrl
					   inner join  risque_has_cause rhc on ctrl.risque_cause_id = rhc.id
					   inner join  temp_risquesste t  on t.best_id   = rhc.risque_id 
					   inner join note n on n.valeur=t.gravite
					   inner join grille g on n.id=g.note_id
					   inner join type_grille tg on tg.id =g.type_grille_id and tg.cartographie_id=" . $chargement->getCartographie ()->getId () . " and tg.type_evaluation_id=" . $ids ['type_evaluation'] ['maturite'] . "
					   set ctrl.grille_id=g.id;";
		} else {
			$query .= "update controle ctrl
					   inner join  risque_has_cause rhc on ctrl.risque_cause_id = rhc.id
					   inner join  temp_risquesste t  on t.best_id   = rhc.risque_id
					   inner join note n on n.valeur=t.gravite
					   inner join grille g on n.id=g.note_id
					   inner join type_grille tg on tg.id = g.type_grille_id and t.mode_fonctionnement = tg.mode_fonctionnement_id and tg.cartographie_id=" . $chargement->getCartographie ()->getId () . " and tg.type_evaluation_id=" . $ids ['type_evaluation'] ['maturite'] . "
					   set ctrl.grille_id=g.id;";
		}
		// faire historique du chargement
		$query .= " INSERT INTO `chargement_has_risque`(`chargement_id`, `risque_id`)
				    select distinct {$chargement->getId ()}, t.id
				    from temp_risque t;";
		$query .= "update chargement set etat=1 where id=" . $chargement->getId () . ";";
		$this->connection->prepare ( $query )->execute ();
	}
	
	public function deleteTable() {
		$statement = $this->connection->prepare ( sprintf ( "DROP TABLE IF EXISTS `temp_risquesste`;DROP TABLE IF EXISTS `temp_risque`;DROP TABLE IF EXISTS `temp_impact`;" ) );
		$statement->execute ();
	}

    public function checkDoublonSSTE($table)
    {
        $temp_risque = $this->connection->fetchAll("SELECT * FROM temp_risque;");
        foreach ($temp_risque as $risk){
            $req = "SELECT r.id FROM {$table} ta
                INNER JOIN risque r ON r.id = ta.risque_id
                INNER JOIN menace m ON m.id = r.menace_id
                INNER JOIN site si ON si.id = ta.site_id
                INNER JOIN domaine_activite da ON da.id = ta.domaine_activite_id
                INNER JOIN equipement e ON e.id = ta.equipement_id
                INNER JOIN lieu l ON l.id = ta.lieu_id
                INNER JOIN manifestation ma ON ma.id = ta.manifestation_id
                INNER JOIN temp_risque tr ON m.id = tr.menace_id
                WHERE m.id=".$risk['menace_id']."
                AND si.id=".$risk['site_id']." 
                AND da.id=".$risk['domaine_activite_id']."
                AND e.id=".$risk['equipement_id']."
                AND l.id=".$risk['lieu_id']."
                AND ma.id=".$risk['manifestation_id'].";";
            $idRiskDoublon = $this->connection->fetchOne($req);

            if ($idRiskDoublon) {
                $req = "UPDATE temp_risque SET risque_id_doublon=".$idRiskDoublon.", id=".$idRiskDoublon." 
                        WHERE menace_id=".$risk['menace_id']."
                        AND site_id=".$risk['site_id']."
                        AND domaine_activite_id=".$risk['domaine_activite_id']."
                        AND equipement_id=".$risk['equipement_id']."
                        AND lieu_id=".$risk['lieu_id']."
                        AND manifestation_id=".$risk['manifestation_id'].";";

                $req .= "update temp_risquesste r , temp_risque tr
                        set r.best_id=".$idRiskDoublon."
                        WHERE menace_id=".$risk['menace_id']."
                        AND site_id=".$risk['site_id']."
                        AND domaine_activite_id=".$risk['domaine_activite_id']."
                        AND equipement_id=".$risk['equipement_id']."
                        AND lieu_id=".$risk['lieu_id']."
                        AND manifestation_id=".$risk['manifestation_id'].";";

                $this->connection->prepare($req)->execute();
            }
        }
    }
}
