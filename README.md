SurfStack Access Control in PHP [![Build Status](https://travis-ci.org/josephspurrier/surfstack-access-control.png?branch=master)](https://travis-ci.org/josephspurrier/surfstack-access-control)
===============================

Single class that determines if a user is allowed to access a class and method.

For this example, we'll assume you have a class called: SurfStack\Test\TestClass.
The class will have two methods called: foo and bar.

```php
<?php
namespace SurfStack\Test;

class TestClass
{
    function foo()
    {
        echo 'Hello world!';
    }
    
    function bar()
    {
        echo 'Hello universe!';
    }
}
```

You would like to protect these methods so only certain users can access them.
In your main application file like index.php, follow these instructions.

```php
<?php

// Create an instance of the Access Handler
$ah = new SurfStack\AccessControl\AccessHandler();

// Enforce the ACL.
// If this property is not set (NULL), then the class will never grant access.
// If this property is set to TRUE, then the class will be determine access
// from the Rules and Permissions.
// If this property is set to FALSE, then the class will always grant access.
$ah->isEnforced = true;

// Create an associative array for the Rules.
// The key should be the class name, colon, then the method name. The value
// should be an array of one or more group names that should be allowed access.
// Method Rules only grant access to a specific method.
// Class Rules grant access to all the public methods in a class.
$rules = array(
    // This is a Method Rule
    'SurfStack\Test\TestClass:foo' => array('anonymous'),
    // This is a Class Rule
    'SurfStack\Test\TestClass:*' => array('authenticated', 'administrators'),
);

// Create an associative array for the Permissions.
// The key should be the group name. The value should be a boolean value that
// represents the current user's permissions. You'll want to dynamically
// generate these values using functions to make this practical.
// This array means the current user is part of the "anonymous" group only.
// These groups can be whatever you want. They must be the same groups
// used in the structure array for access to be granted.
$permissions = array(
    'anonymous' => true,
    'authenticated' => false,
    'administrators' => false,
    'blacklisted' => false,
);

// Create an indexed array for groups that should always have access.
$allowAll = array(
    'administrators',
);

// Create an indexed array for groups that should never have access.
$denyAll = array(
    'blacklisted',
);

// Pass the arrays
$ah->setRules($rules);
$ah->setPermissions($permissions);
$ah->setAllowAll($allowAll);
$ah->setDenyAll($denyAll);

// Pass the route (class and method) the user requested.
// You'll also need to dynamically generate these values.
// We'll use our class and method names in this example.
$ah->setRoute('SurfStack\Test\TestClass', 'foo');

// At this point, we've provide all the information the class needs to
// determine if the user has access to the requested route.

// You can now determine if the user is allowed access
$isAllowed = $ah->isAllowed();
```

To install using composer, use the code from the Wiki page [Composer Wiki page](../../wiki/Composer).
