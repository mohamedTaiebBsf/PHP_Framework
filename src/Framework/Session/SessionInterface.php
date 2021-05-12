<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * Récupérer une information de la Session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Ajouter une information en Session
     *
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void;

    /**
     * Supprimer une clef de la Session
     *
     * @param string $key
     */
    public function delete(string $key): void;
}
