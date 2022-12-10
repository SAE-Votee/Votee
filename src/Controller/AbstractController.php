<?php

namespace App\Votee\Controller;

class AbstractController {

    protected static function afficheVue(string $cheminVue, array $parametres = []): void {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . "/../view/$cheminVue"; // Charge la vue
    }

    public static function error(string $errorMessage = "") {
        self::afficheVue("view.php",
            [
                "pagetitle" => "Erreur",
                "cheminVueBody" => "question/error.php",
                "title" => "Un problème est survenu",
                "subtitle" => $errorMessage
            ]);
    }

    public static function pageIntrouvable(): void {
        http_response_code(404);
        self::afficheVue('view.php',
                        ["pagetitle" => "Page introuvable",
                            "cheminVueBody" => "404.php",
                            "title" => "Page introuvable",
                        ]);
    }

    public static function redirection($url): void {
        header("Location: $url");
        exit();
    }
}