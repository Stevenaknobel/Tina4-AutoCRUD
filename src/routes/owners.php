<?php
$api = 
\Tina4\Get::add("/owners/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/owners/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Owners Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/owners", new Owners(), function ($action, Owners $owners, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Owners";
                $savePath =  TINA4_SUB_FOLDER . "/owners";
                $content = \Tina4\renderTemplate("/owners/form.twig", []);
            } else {
                $title = "Edit Owners";
                $savePath =  TINA4_SUB_FOLDER . "/owners/".$owners->id;
                $content = \Tina4\renderTemplate("/owners/form.twig", ["data" => $owners]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#ownersForm').valid() ) { saveForm('ownersForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $owners->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>ownersGrid.ajax.reload(null, false); showMessage ('Owners Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>ownersGrid.ajax.reload(null, false); showMessage ('Owners Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>ownersGrid.ajax.reload(null, false); showMessage ('Owners Deleted');</script>"];
        break;
    }
});