<?php


require_once("ApiHandler.php");
require_once("Scoreboard.php");
require_once(SERVER_PATH ."/libs/ApiException.php");

class OmiReport extends ApiHandler 
{
    private $contest;
    
    protected function RegisterValidatorsToRequest() 
    {        
        
        ValidatorFactory::stringNotEmptyValidator()->validate(RequestContext::get("contest_alias"), "contest_alias");
        
        //ValidatorFactory::stringNotEmptyValidator()->validate(RequestContext::get("estado"), "estado");

	try 
        {
            $this->contest = ContestsDAO::getByAlias(RequestContext::get("contest_alias"));
	}
        catch(Exception $e) 
        {
            // Operation failed in the data layer
            throw new ApiException(ApiHttpErrors::invalidDatabaseOperation(), $e);
	}
        
        if (!Authorization::IsContestAdmin($this->_user_id, $this->contest))
        {   
            throw new ApiException(ApiHttpErrors::forbiddenSite("Unauthorized."));
        }        
    }
    
    protected function GenerateResponse() 
    {
        // Create scoreboard
	$scoreboard = new Scoreboard(
		$this->contest->getContestId(),
		true //Show only relevant runs
	);
                 
        // Push ultra full scoreboard data in response
        $this->addResponse('report', $scoreboard->generate(true, true, (is_null(RequestContext::get("estado")) ? null : RequestContext::get("estado") )  )); //true == with super full run details, and sorted by name 
    }
}

?>
