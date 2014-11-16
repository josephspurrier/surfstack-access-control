<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

use SurfStack\AccessControl\AccessHandler;

/**
 * Access Control Test
 * 
 * Ensures the class returns access as expected
 *
 */
class AccessControlTest extends PHPUnit_Framework_TestCase
{
    public function testCannotAccessByDefault()
    {
        // Create an instance
        $a = new AccessHandler();
        
        // Get bool
        $result = $a->isAllowed();
        
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
    
    public function testCannotAccessIfEnforced()
    {
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
        
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
    
    public function testCanAccessIfEnforced()
    {
        // Create an instance
        $a = new AccessHandler();
    
        // Don't enforce
        $a->isEnforced = false;
    
        // Get bool
        $result = $a->isAllowed();
    
        // Allow access
        $this->assertTrue($a->isAllowed());
    }
    
    public function testCanAccessIfGroupTrue()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method = 'index';
        $isGroupMember = true;
        
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
        
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
        
        // Set the rules
        $rules = array();
        $rules[$class.':'.$method] = array($group);
        $a->setRules($rules);
        
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method);
        
        // Get bool
        $result = $a->isAllowed();
    
        // Should allow access
        $this->assertTrue($a->isAllowed());
    }
    
    public function testCannotAccessIfGroupFalse()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method = 'index';
        $isGroupMember = false;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class.':'.$method] = array($group);
        $a->setRules($rules);
    
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
    
    public function testCanAccessIfAllow()
    {
        // Set the variables
        $group = 'anonymous';
        $class1 = 'SurfStack\Test\TestClass1';
        $class2 = 'SurfStack\Test\TestClass2';
        $method = 'index';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class1.':'.$method] = array($group);
        $a->setRules($rules);
        
        // Add to the allow all array
        $a->setAllowAll(array($group));
    
        // Set the routes (class and method called)
        $a->setRouteClass($class2, $method);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should allow access
        $this->assertTrue($a->isAllowed());
    }
    
    public function testCannotAccessIfDeny()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method = 'index';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class.':'.$method] = array($group);
        $a->setRules($rules);
    
        // Add to the deny all array
        $a->setDenyAll(array($group));
    
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }

    public function testCannotAccessIfDenyAndAllow()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method = 'index';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class.':'.$method] = array($group);
        $a->setRules($rules);
    
        // Add to the deny all array
        $a->setDenyAll(array($group));
        
        // Add to the allow all array
        $a->setAllowAll(array($group));
    
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
    
    public function testCanAccessIfClassRule()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method = 'index';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class.':*'] = array($group);
        $a->setRules($rules);
    
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should allow access
        $this->assertTrue($a->isAllowed());
    }
    
    public function testCanAccessIfFunctionRule()
    {
        // Set the variables
        $group = 'anonymous';
        $function = 'testFunction';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$function] = array($group);
        $a->setRules($rules);
    
        // Set the routes (class and method called)
        $a->setRouteFunction($function);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should allow access
        $this->assertTrue($a->isAllowed());
    }
    
    public function testCannotAccessIfMethodRuleDifferent()
    {
        // Set the variables
        $group = 'anonymous';
        $class = 'SurfStack\Test\TestClass';
        $method1 = 'index1';
        $method2 = 'index2';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$class.':'.$method1] = array($group);
        $a->setRules($rules);
    
        // Set the routes (class and method called)
        $a->setRouteClass($class, $method2);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
    
    public function testCannotAccessIfFunctionRuleDifferent()
    {
        // Set the variables
        $group = 'anonymous';
        $func1 = 'testFunc1';
        $func2 = 'testFunc2';
        $isGroupMember = true;
    
        // Create an instance
        $a = new AccessHandler();
    
        // Enforce
        $a->isEnforced = true;
    
        // Set the permissions
        $permissions = array(
            $group => $isGroupMember,
        );
        $a->setPermissions($permissions);
    
        // Set the rules
        $rules = array();
        $rules[$func1] = array($group);
        $a->setRules($rules);
    
        // Set the routes (class and method called)
        $a->setRouteFunction($func2);
    
        // Get bool
        $result = $a->isAllowed();
    
        // Should not allow access
        $this->assertFalse($a->isAllowed());
    }
}