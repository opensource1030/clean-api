<?php


namespace WA\Repositories\HelpDesk;


abstract class HelpDeskDecorator implements HelpDeskInterface {

    protected $helpDesk;

    public function __construct(HelpDeskInterface $helpDesk){
        $this->helpDesk = $helpDesk;
    }

    /**
     * Get asset by identification
     *
     * @param string $identification
     */
    public function assetByIdentification($identification){
        $this->helpDesk->assetByIdentification($identification);
    }

    /**
     * Get device by identification
     *
     * @param string $identification
     */
    public function deviceByIdentification($identification){
        $this->helpDesk->deviceByIdentification($identification);
    }

    /**
     * Get an employee by name
     *
     * @param string $name
     * @return array name information
     */
    public function employeeByName($name){
        $this->helpDesk->employeeByName($name);
    }

    /**
     * Get all validators by their companyID
     *
     * @param int $companyId
     * @return Object object of validators
     */
    public function getValidators($companyId){
        $this->helpDesk->getValidators($companyId);
    }

    /**
     * Get all supervisors but their companyId (Sups?)
     *
     * @oaram int $companyId
     * @return Object object of supervisors/managers
     */
    public function getSupervisors($companyId){
        $this->helpDesk->getSupervisors($companyId);
    }

    /**
     * Get an employee by the identification
     *
     * @param string $identification
     * @return string of identification
     */
    public function employeeByIdentification($identification){
        $this->helpDesk->employeeByIdentification($identification);
    }

    /**
     * Get the departmental path
     *
     * @param string $path
     * @return int id of the path
     */
    public function getDepartmentId($path){
        $this->helpDesk->getDepartmentId($path);
    }




}