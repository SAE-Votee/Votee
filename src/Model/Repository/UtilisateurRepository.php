<?php

namespace App\Votee\Model\Repository;

use App\Votee\Model\DataObject\Utilisateur;
use PDOException;

class UtilisateurRepository extends AbstractRepository {


    function getNomSequence(): string { return ""; }
    function getNomTable(): string { return "Utilisateurs"; }
    function getNomClePrimaire(): string { return "LOGIN"; }

    function getProcedureInsert(): array { return array('procedure' => 'AjouterUtilisateur', 'LOGIN', 'MOTDEPASSE', 'NOM', 'PRENOM'); }
    function getProcedureUpdate(): array { return []; }
    function getProcedureDelete(): string { return ""; }

    public function construire(array $utilisateurFormatTableau) : Utilisateur {
        return new Utilisateur(
            $utilisateurFormatTableau['LOGIN'],
            $utilisateurFormatTableau['MOTDEPASSE'],
            $utilisateurFormatTableau['NOM'],
            $utilisateurFormatTableau['PRENOM'],
            $utilisateurFormatTableau['NBQUESTRESTANT']
        );
    }

    /** Retourne l'ensemble des roles pour une question et un utilisateur donné */
    public function getRolesQuestion($login, $idQuestion): array {
        $roles = [];
        $procedures = ["Responsable", "Organisateur", "CoAuteur", "Votant", "Specialiste"];
        foreach ($procedures as $procedure) {
            $sql = "SELECT :procedureTag(:loginTag, :idQuestionTag) FROM DUAL";
            $sql = str_replace(":procedureTag", 'est' . $procedure, $sql);
            $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
            $pdoStatement->execute(array("loginTag" => $login, "idQuestionTag" => $idQuestion));
            $result = $pdoStatement->fetch();
            if ($result[0] == null) continue;
            $roles[] = $procedure;
        }
        return $roles;
    }

    /** Retourne l'ensemble des roles pour une proposition et un utilisateur donné */
    public function getRolesProposition($login, $idProposition): array {
        $roles = [];
        $procedures = ["Responsable", "CoAuteur"];
        foreach ($procedures as $procedure) {
            $sql = "SELECT :procedureTag(:loginTag, :idPropositionTag) FROM DUAL";
            $sql = str_replace(":procedureTag", 'est' . $procedure . 'Prop', $sql);
            $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
            $pdoStatement->execute(array("loginTag" => $login, "idPropositionTag" => $idProposition));
            $result = $pdoStatement->fetch();
            if ($result[0] === null) continue;
            $roles[] = $procedure;
        }
        return $roles;
    }

    /** Rajoute 1 point au score s'il y a eu une erreur dans l'insertion d'une question */
    public function ajouterScoreQuestion($login): void {
        $sql = "UPDATE Utilisateurs SET NBQUESTRESTANT = NBQUESTRESTANT + 1 WHERE login = :loginTag";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute(array("loginTag" => $login));
    }

    // TODO La remplacer a terme par une verification de la table existe
    public function selectAllActorQuestion($idQuestion): array {
        $sql = "SELECT U.* FROM (
    SELECT DISTINCT LOGIN_ORGANISATEUR FROM (
        SELECT Q.LOGIN_ORGANISATEUR, Q.IDQUESTION FROM EXISTE E
        JOIN QUESTIONS Q on E.LOGIN = Q.LOGIN_ORGANISATEUR

        UNION

        SELECT LOGIN, IDQUESTION FROM (
            SELECT RC.LOGIN, R.IDQUESTION FROM REDIGERCA RC
            JOIN RECEVOIR R ON RC.IDPROPOSITION = R.IDPROPOSITION

            UNION

            SELECT RR.LOGIN, R.IDQUESTION FROM REDIGERR RR
            JOIN RECEVOIR R ON RR.IDPROPOSITION = R.IDPROPOSITION
        )
    ) WHERE IDQUESTION =:idQuestionTag
) JOIN UTILISATEURS U ON LOGIN_ORGANISATEUR = U.LOGIN";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute(array("idQuestionTag" => $idQuestion));
        foreach ($pdoStatement as $utilisateur) {
            $utilisateurs[] = $this->construire($utilisateur);
        }
        return $utilisateurs;
    }

    public static function getAdmins() : array {
        $sql = "SELECT * FROM Administrateurs";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute();
        $admins = [];
        $result = $pdoStatement->fetchAll();
        foreach ($result as $admin) {
            $admins[] = $admin['LOGIN'];
        }
        return $admins;
    }

    public function selectAllAdmins() : array {
        $sql = "SELECT * FROM UTILISATEURS WHERE LOGIN IN (SELECT LOGIN FROM ADMINISTRATEURS)";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute();
        foreach ($pdoStatement as $utilisateur) {
            $utilisateurs[] = $this->construire($utilisateur);
        }
        return $utilisateurs;
    }

    public function selectCoAuteur($idProposition): array {
        $coAuteurs = [];
        $sql = "SELECT u.* FROM RedigerCA r
                JOIN Coauteurs c ON r.login = c.login
                JOIN roles ro ON c.login = ro.login
                JOIN Utilisateurs u ON ro.login = u.login
                WHERE IDPROPOSITION = :idProposition";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);;
        $pdoStatement->execute(array("idProposition" => $idProposition));
        foreach ($pdoStatement as $utilisateur) {
            $coAuteurs[] = $this->construire($utilisateur);
        }
        return $coAuteurs;
    }

    public function selectResp($idProposition): ?Utilisateur {
        $sql = "SELECT u.* FROM RedigerR r
                JOIN responsables re ON r.login = re.login
                JOIN roles ro ON re.login = ro.login
                JOIN Utilisateurs u ON ro.login = u.login
                WHERE IDPROPOSITION = :idProposition";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute(array("idProposition" => $idProposition));
        $responsable = $pdoStatement->fetch();
        return $responsable ? $this->construire($responsable): null;
    }

    public function selectAdministrateur($login) {
        $sql = "SELECT * FROM Administrateurs WHERE login = :loginTag";
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);;
        $pdoStatement->execute(array("loginTag" => $login));
        $isAdmin = $pdoStatement->fetch();
        return (bool)$isAdmin;
    }
}