<?php
namespace Olorin\Mgmt;

use Exception;

class MgmtException extends Exception
{
    /**
     * Render an appropriate response to the Exception Handler.  Redirects to mgmt.index
     * route while flashing a message to the session which will be handled by SweetAlert.
     * Accepts different codes for different message types:
     * 1 = error
     * 2 = warning
     * 3 = info
     * 4 = success
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render(){
        switch($this->code){
            case 4:
                flash()->success($this->message);
                break;
            case 3:
                flash()->message($this->message);
                break;
            case 2:
                flash()->warning($this->message);
                break;
            case 1:
            default:
                flash()->error($this->message);
                break;
        }

        return redirect()->route('mgmt.index');
    }
}
