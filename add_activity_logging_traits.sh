#!/bin/bash

# Script to add ActivityLoggingTrait to controllers that need it
# Based on the audit results

echo "=== Adding ActivityLoggingTrait to Controllers ==="

CONTROLLERS_DIR="/opt/lampp/htdocs/optima1/app/Controllers"

# List of controllers that need ActivityLoggingTrait
CONTROLLERS_TO_UPDATE=(
    "RentalManagement.php"
    "UnitRolling.php" 
    "Settings.php"
    "UnitAssetController.php"
    "Profile.php"
    "WarehousePO.php"
    "Warehouse.php"
    "Reports.php"
    "Notifications.php"
)

for controller in "${CONTROLLERS_TO_UPDATE[@]}"; do
    CONTROLLER_PATH="$CONTROLLERS_DIR/$controller"
    
    if [ -f "$CONTROLLER_PATH" ]; then
        echo "Processing $controller..."
        
        # Check if it already has the trait
        if grep -q "ActivityLoggingTrait" "$CONTROLLER_PATH"; then
            echo "  ✓ Already has ActivityLoggingTrait"
        else
            echo "  + Adding ActivityLoggingTrait"
            
            # Add use statement after other use statements
            sed -i '/^use App\\Controllers\\BaseController;/a use App\\Traits\\ActivityLoggingTrait;' "$CONTROLLER_PATH"
            
            # Add trait usage in class (find class declaration and add trait)
            sed -i '/^class.*extends.*Controller/,/^{/{
                /^{/a\    use ActivityLoggingTrait;
            }' "$CONTROLLER_PATH"
            
            echo "  ✓ Added ActivityLoggingTrait to $controller"
        fi
    else
        echo "  ❌ $controller not found"
    fi
done

echo ""
echo "=== Trait Addition Complete ==="
echo "Note: You still need to manually add logCreate(), logUpdate(), logDelete() calls"
echo "to the appropriate methods in each controller."
