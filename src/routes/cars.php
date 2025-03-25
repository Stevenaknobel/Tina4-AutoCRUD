<?php
$api = 
\Tina4\Get::add("/cars/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/cars/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Car Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/cars", new Car(), function ($action, Car $car, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Car";
                $savePath =  TINA4_SUB_FOLDER . "/cars";
                $content = \Tina4\renderTemplate("/cars/form.twig", []);
            } else {
                $title = "Edit Car";
                $savePath =  TINA4_SUB_FOLDER . "/cars/".$car->id;
                $content = \Tina4\renderTemplate("/cars/form.twig", ["data" => $car]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#carForm').valid() ) { saveForm('carForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $car->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>carGrid.ajax.reload(null, false); showMessage ('Car Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>carGrid.ajax.reload(null, false); showMessage ('Car Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>carGrid.ajax.reload(null, false); showMessage ('Car Deleted');</script>"];
        break;
    }
});