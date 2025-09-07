<?php

// Test if plugin class can be loaded
require '../vendor/autoload.php';

echo "<h1>Plugin Test</h1>";

try {
    $pluginClass = 'mycompany\\menumanager\\Plugin';
    echo "<p>Testing plugin class: $pluginClass</p>";
    
    if (class_exists($pluginClass)) {
        echo "<p>✅ Plugin class exists</p>";
    } else {
        echo "<p>❌ Plugin class NOT found</p>";
    }
    
    // Test controller class
    $controllerClass = 'mycompany\\menumanager\\controllers\\MenusController';
    echo "<p>Testing controller class: $controllerClass</p>";
    
    if (class_exists($controllerClass)) {
        echo "<p>✅ Controller class exists</p>";
    } else {
        echo "<p>❌ Controller class NOT found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/admin'>Back to Admin</a></p>";
?>