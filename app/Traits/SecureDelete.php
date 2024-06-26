<?php
namespace App\Traits;

trait SecureDelete{
    /**
     * Delete only when there is no reference to other models.
     *
     * @param array $relations
     * @return response
     */
    public function secureDelete(String ...$relations)
    {
        $hasRelation = false;
        foreach ($relations as $relation) {
            if ($this->$relation()->count() > 0) {
                $hasRelation = true;
                break;
            }
        }
        return $hasRelation;
    } 
}
?>