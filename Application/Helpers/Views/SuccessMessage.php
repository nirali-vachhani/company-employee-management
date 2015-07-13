<?php


/**
 * Description of ActiveShortCut
 *
 * @author Penuel
 */
class Helpers_Views_SuccessMessage extends LMVC_ViewHelper {

    //put your code here    


    final public function init() {
        $this->helperName = 'success_message';
    }

    final public function helperFunc($params) {
        

        if (!empty($params['message'])) {
            return '<div class="alert alert-success">'. $params['message'] . '</div>';
        }
    }

}

?>
