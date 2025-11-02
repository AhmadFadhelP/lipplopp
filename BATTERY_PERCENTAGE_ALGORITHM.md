# Battery Percentage Algorithm - Technical Documentation

## Overview
Improved battery percentage calculation that uses both **voltage** and **current** to provide accurate State of Charge (SOC) readings for 12V lead-acid battery systems.

## Problem with Old Method

The previous calculation used a simple linear interpolation:
```javascript
level = ((voltage - 10.5) / (12.6 - 10.5)) × 100
```

**Issues:**
1. ❌ Ignores current flow (charging/discharging)
2. ❌ Linear interpolation doesn't match battery discharge curve
3. ❌ Inaccurate during high current scenarios
4. ❌ No compensation for voltage sag/rise

## New Algorithm

### 1. Voltage Compensation
Accounts for internal resistance during current flow:

```javascript
Compensated Voltage = Measured Voltage - (Current × Internal Resistance)
Internal Resistance = 0.015Ω
```

**Why this matters:**
- During **charging** (positive current): Measured voltage is artificially high
- During **discharging** (negative current): Measured voltage sags below actual SOC
- Compensation removes this error for accurate reading

### 2. Non-Linear SOC Curve
Uses realistic lead-acid battery discharge curve:

| Voltage Range | SOC Range | Characteristics |
|---------------|-----------|-----------------|
| ≥12.65V | 100% | Fully charged |
| 12.45V - 12.65V | 90% - 100% | Top 10% (slow voltage drop) |
| 12.24V - 12.45V | 70% - 90% | Upper range (moderate drop) |
| 12.06V - 12.24V | 40% - 70% | Mid range (linear region) |
| 11.88V - 12.06V | 20% - 40% | Lower range (faster drop) |
| 11.31V - 11.88V | 5% - 20% | Low battery (rapid drop) |
| 10.50V - 11.31V | 0% - 5% | Critical (damage risk) |
| <10.50V | 0% | Battery protection cutoff |

## Example Calculations

### Scenario 1: Fully Charged + Charging
```
Measured: 13.00V, Current: 4.00A (charging)
Compensation: 4.00 × 0.015 = 0.06V
Compensated: 13.00 - 0.06 = 12.94V
Result: 100% ✓

Old method would show: 100% (coincidentally correct)
New method shows: 100% (correctly compensated)
```

### Scenario 2: Medium Charge + Heavy Discharge
```
Measured: 12.10V, Current: -3.00A (discharging)
Compensation: -3.00 × 0.015 = -0.045V
Compensated: 12.10 - (-0.045) = 12.145V
Result: 55% ✓

Old method would show: 76% ❌ (overestimated due to voltage sag)
New method shows: 55% ✓ (correctly compensated)
```

### Scenario 3: Low Battery + Heavy Discharge
```
Measured: 11.90V, Current: -4.50A (heavy discharge)
Compensation: -4.50 × 0.015 = -0.0675V
Compensated: 11.90 - (-0.0675) = 11.9675V ≈ 11.97V
Result: 26% ✓

Old method would show: 67% ❌ (dangerously overestimated!)
New method shows: 26% ✓ (accurate warning of low battery)
```

## Enhanced Status Messages

The dashboard now shows detailed battery status:

| Condition | Display |
|-----------|---------|
| SOC ≥95% + Charging | "Fully charged ⚡ X.XA" |
| SOC ≥95% + Standby | "Full battery 🔋" |
| Charging + Current >2A | "Fast charging ⚡ X.XA" |
| Charging | "Charging ⚡ X.XA" |
| SOC ≤20% + Discharging | "Low battery (discharging) ⚠ X.XA" |
| SOC ≤20% | "Low battery ⚠" |
| Discharging >1A | "Discharging X.XA" |
| Otherwise | "Standby" |

## Visual Indicators

Battery color changes based on SOC:
- **Green** (>80%): Healthy
- **Yellow** (40-80%): Normal
- **Orange** (20-40%): Low
- **Red** (<20%): Critical

## Files Modified

1. **frontend/dashboard.php** (lines 457-534)
   - Updated `initBattery()` function with new algorithm
   - Enhanced status messages with current display

## Testing

### Test Files Created:
1. **test_battery_calculation.html** - Interactive calculator
2. **test_battery_scenarios.php** - Comparison visualization

### To Test:
```bash
# Open in browser:
http://localhost:8080/test_battery_calculation.html
http://localhost:8080/test_battery_scenarios.php

# Or view live dashboard:
http://localhost:8080/frontend/dashboard.php
```

## Benefits

✅ **Accurate readings** during charging and discharging
✅ **Better battery protection** with accurate low battery warnings
✅ **Realistic SOC curve** matches actual battery behavior
✅ **Current flow visibility** shows charging/discharging rate
✅ **Improved user experience** with detailed status messages

## Technical References

- Lead-acid battery voltage curve: Non-linear discharge characteristic
- Internal resistance: Typical 12V battery ~0.01-0.02Ω
- Peukert's law: Not implemented (requires capacity knowledge)
- Temperature compensation: Not implemented (would require temp sensor)

## Future Improvements

1. **Temperature compensation**: Voltage changes ~-0.003V/°C
2. **Coulomb counting**: Track amp-hours for capacity estimation
3. **State of Health (SOH)**: Monitor battery aging
4. **Adaptive resistance**: Learn actual battery resistance over time
5. **Historical SOC tracking**: Graph battery usage patterns

---

**Last Updated:** 2025-11-02
**Version:** 2.0
**Author:** Battery Management System
