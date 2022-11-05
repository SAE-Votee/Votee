<?php

namespace App\Votee\Model\DataObject;

class Texte extends AbstractDataObject {

    private int $idSection;
    private ?int $idProposition;
    private string $texte;

    public function __construct( int $idSection, ?int $idProposition, string $texte) {
        $this->idSection = $idSection;
        $this->idProposition = $idProposition;
        $this->texte = $texte;
    }

    public function formatTableau(): array {
        return array(
            "IDSECTION" => $this->getIdSection(),
            "IDPROPOSITION" => $this->getIdProposition(),
            "TEXTE" => $this->getTexte(),
        );
    }

    public function getIdProposition(): ?int { return $this->idProposition; }

    public function setIdProposition(int $idProposition): void { $this->idProposition = $idProposition; }

    public function getIdSection(): int { return $this->idSection; }

    public function setIdSection(int $idSection): void { $this->idSection = $idSection; }

    public function getTexte(): string { return $this->texte; }

    public function setTexte(string $texte): void { $this->texte = $texte; }


}