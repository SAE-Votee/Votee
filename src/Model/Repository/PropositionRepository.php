<?php

namespace App\Votee\Model\Repository;

use App\Votee\Model\DataObject\Proposition;
use PDOException;

class PropositionRepository extends AbstractRepository {

    protected function getNomsColonnes(): array {
        return array(
            'IDPROPOSITION',
            'IDQUESTION',
            'VISIBILITEPROPOSITION',
        );

    }
    function getNomTable(): string {
        return "overviewProposition";
    }

    function getNomClePrimaire(): string {
        return "IDPROPOSITION";
    }

    function getProcedureInsert(): string { return ""; }

    function getProcedureUpdate(): string { return ""; }

    function getProcedureDelete(): string {
        return "SupprimerPropositions";
    }

    public function construire(array $propositionFormatTableau) : Proposition {
        return new Proposition(
            $propositionFormatTableau['IDPROPOSITION'],
            $propositionFormatTableau['IDQUESTION'],
            $propositionFormatTableau['VISIBILITEPROPOSITION'],
        );
    }

    public function modifierProposition(string $idProposition, string $visibilite) {
        $sql = "CALL ModifierPropositions(:idPropositionTag, :visibiliteTag)";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $values = array("idPropositionTag" => $idProposition, "visibiliteTag" => $visibilite);
        $pdoStatement->execute($values);
    }

    public function ajouterProposition(string $visibite):int {
        $sql = "CALL AjouterPropositions(:visibiliteTag)";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $value = array("visibiliteTag" => $visibite);
        $pdoStatement->execute($value);

        $pdoLastInsert = DatabaseConnection::getPdo()->prepare("SELECT propositions_seq.CURRVAL AS lastInsertId FROM DUAL");
        $pdoLastInsert->execute();
        $lastInserId = $pdoLastInsert->fetch();
        return intval($lastInserId[0]);
    }

    public function ajouterRepresentant(string $login, int $idProposition, int $idQuestion):bool {
        $sql = "CALL AjouterRedigerR(:loginTag, :idPropositionTag, :idQuestionTag)";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $values = array(":loginTag"=>$login, "idPropositionTag"=>$idProposition, "idQuestionTag"=>$idQuestion);
        try {
            $pdoStatement->execute($values);
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function ajouterCoauteur(string $login, int $idProposition): bool {
        $sql = "CALL AjouterRedigerCA(:login, :idProposition)";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        try {
            $pdoStatement->execute(array(":login"=>$login, "idProposition"=>$idProposition));
            return true;
        } catch (PDOException) {
            return false;
        }
    }

}