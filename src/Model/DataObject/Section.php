<?php

namespace App\Votee\Model\DataObject;

class Section extends AbstractDataObject {

    private string $login;
    private int $idProposition;
    private int $idSection;
    private string $titreSection;
    private string $texte;
    private int $idQuestion;

    public function __construct(string $login, int $idProposition, int $idSection, string $titreSection, string $texte, int $idQuestion) {
        $this->login = $login;
        $this->idProposition = $idProposition;
        $this->idSection = $idSection;
        $this->titreSection = $titreSection;
        $this->texte = $texte;
        $this->idQuestion = $idQuestion;
    }

    public function formatTableau(): array {
        return array(
            "LOGIN" => $this->getLogin(),
            "IDPROPOSITION" => $this->getIdProposition(),
            "IDSECTION" => $this->getIdSection(),
            "TITRESECTION" => $this->getTitreSection(),
            "TEXTE" => $this->getTexte(),
            "IDQUESTION" => $this->getIdQuestion(),
        );
    }

    public function getLogin(): string { return $this->login; }

    public function setLogin(string $login): void { $this->login = $login; }

    public function getIdProposition(): int { return $this->idProposition; }

    public function setIdProposition(int $idProposition): void { $this->idProposition = $idProposition; }

    public function getIdSection(): int { return $this->idSection; }

    public function setIdSection(int $idSection): void { $this->idSection = $idSection; }

    public function getTitreSection(): string { return $this->titreSection; }

    public function setTitreSection(string $titreSection): void { $this->titreSection = $titreSection; }

    public function getTexte(): string { return $this->texte; }

    public function setTexte(string $texte): void { $this->texte = $texte; }

    public function getIdQuestion(): int { return $this->idQuestion; }

    public function setIdQuestion(int $idQuestion): void { $this->idQuestion = $idQuestion; }
}