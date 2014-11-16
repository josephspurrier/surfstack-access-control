<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

namespace SurfStack\AccessControl;

/**
 * Access Handler
 *
 * Determines if a user is allowed to access a class method or function based on rules.
 * Method Rules override Class Rules.
 *
 * Method Rule allows access for one method:
 * $rules['Name\Spaced\Class:method1'] = array('anonymous');
 *
 * Class Rule specifies access for an entire class;
 * $rules['Name\Spaced\Class:*'] = array('authenticated');
 * 
 * Rules can accept multiple groups;
 * $rules['Name\Spaced\Class:method1'] = array('anonymous', 'authenticated');
 *
 * Group permissions array should use group names as keys and TRUE or FALSE as
 * values. Logic should look similar to:
 * $permissions = array(
 *     'anonymous' => ($session->get('id') ? false : true),
 *     'authenticated' => ($session->get('id') ? true : false),
 *     'administrators' => isAdmin(),
 *     'blacklisted' => isBlacklisted(),
 * ); 
 */
class AccessHandler
{
    /**
     * Is the ACL enforced
     * @var boolean
     */
    public $isEnforced = NULL;
        
    /**
     * Class name
     * @var string
     */
    private $strClass = '';
    
    /**
     * Method name
     * @var string
     */
    private $strMethod = '';
    
    /**
     * Function name
     * @var mixed
     */
    private $strFunction = '';
    
    /**
     * Associative array of access per class and method
     * @var array
     */
    private $arrRules = array();
    
    /**
     * Array of permissions
     * @var array
     */
    private $arrPermissions = array();
    
    /**
     * Array of always allow groups
     * @var array
     */
    private $arrAllow = array();
    
    /**
     * Array of always deny groups
     * @var array
     */
    private $arrDeny = array();
    
    /**
     * Set the associative array outlining rules
     * @param array $arr
     */
    function setRules(array $arr = array())
    {
        $this->arrRules = $arr;
    }
    
    /**
     * Set the permissions array
     * @param array $permissions
     */
    function setPermissions(array $arr = array())
    {
        $this->arrPermissions = $arr;
    }
    
    /**
     * Set the groups that are always allowed access
     * @param array $arr
     */
    function setAllowAll(array $arr = array())
    {
        $this->arrAllow = $arr;
    }
    
    /**
     * Set the groups that are always denied access
     * @param array $arr
     */
    function setDenyAll(array $arr = array())
    {
        $this->arrDeny = $arr;
    }
    
    /**
     * Set the class and method names
     * @param string $strClass
     * @param string $strMethod
     */
    public function setRouteClass($strClass, $strMethod)
    {
        $this->strClass = $strClass;
        $this->strMethod = $strMethod;
    }
    
    /**
     * Set the function
     * @param mixed $strFunction
     */
    public function setRouteFunction($strFunction)
    {
        $this->strFunction = $strFunction;
    }
    
    /**
     * Determines if the current user has permission to view the requested page
     * @return boolean
     */
    function isAllowed()
    {    
        // If the key is not set, don't allow any access
        if ($this->isEnforced === NULL)
        {
            return false;
        }
    
        // If the key is set and is set to FALSE, allow any access
        if ($this->isEnforced === false)
        {
            return true;
        }
        
        // Actual groups the user is in
        $groups = array_keys($this->arrPermissions, true);
        
        // If the current user is in Deny group, deny
        if (array_intersect($groups, $this->arrDeny))
        {
            return false;
        }
        // If the current user is in Allow group, deny
        else if (array_intersect($groups, $this->arrAllow))
        {
            return true;
        }
    
        // If a function
        if ($this->strFunction)
        {
            // Routes
            $arrRoute = array(
                // Function
                $this->strFunction,
            );
        }
        else
        {
            // Routes
            $arrRoute = array(
                // Exact route
                $this->strClass.':'.$this->strMethod,
                // Wildcard route
                $this->strClass.':*',
            );
        }
        
        // For each type of route
        foreach($arrRoute as $request)
        {
            // If the route is set and the route is an array
            if (isset($this->arrRules[$request]) && is_array($this->arrRules[$request]))
            {
                // If the route is set and it contains the current user's group, allow it
                if (array_intersect($groups, $this->arrRules[$request]))
                {
                    return true;
                }
            }
        }
    
        // Return false for everything else
        return false;
    }
}