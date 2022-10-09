<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */

class controller
{
   
    public function processRequest(string $method, string $module, ?string $id, ?array $extra): void
    {
        if ($id) {
            $this->processResourceRequest($method, $module, $id);
            
        } else {
            $this->processCollectionRequest($method, $module, $extra);
            
        }    
    }

    private function processResourceRequest(string $method, string $module, string $id): void
    {
        switch ($method) {
            case "GET":
                echo json_encode( $module::instance()->getOne( $id ) );
                break;
        }

    }
    
    private function processCollectionRequest(string $method, string $module, array $extra): void
    {
        switch ($method) {
            case "GET":
                echo json_encode( $module::instance()->getAll( $extra ) );
                break;
        }

    }
}