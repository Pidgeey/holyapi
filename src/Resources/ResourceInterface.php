<?php

namespace Src\Resources;

/**
 * Interface ResourceInterface
 *
 * @package Src\Resources
 */
interface ResourceInterface
{
    /** Caste la resource courante  en tableau */
    public function toArray(): array;

    /** Créer une réponse au format json de la resource courante */
    public function toJsonResponse();
}
