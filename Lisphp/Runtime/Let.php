<?php
require_once 'Lisphp/Applicable.php';
require_once 'Lisphp/List.php';
require_once 'Lisphp/Scope.php';

final class Lisphp_Runtime_Let implements Lisphp_Applicable {
    function apply(Lisphp_Scope $scope, Lisphp_List $arguments) {
        $vars = $arguments->car();
        $scope = new Lisphp_Scope($scope);
        foreach ($vars as $var) {
            list($var, $value) = $var;
            $scope->let($var, $value->evaluate($scope->superscope));
        }
        foreach ($arguments->cdr() as $form) {
            $retval = $form->evaluate($scope);
        }
        return $retval;
    }
}

