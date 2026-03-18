# ENT Clinic Predictive Forecasting System

## Overview

The predictive forecasting system in the ENT Clinic application uses **advanced time-series analysis** combined with **weekday seasonality modeling** to forecast future visit volumes. The system is implemented in the `AnalyticsController.php` file and provides:

- Historical trend analysis
- Smoothed data processing  
- Automatic trend detection (rising, falling, or stable)
- Weekday-aware predictions
- Intelligent fallback mechanisms for data gaps

---

## System Architecture

### Data Input
- **Source:** `patient_visits` and `appointments` tables
- **Time Range:** User-selected (Today, This Week, This Month, All Time)
- **Metrics Tracked:** Visit counts per day, appointment completions, cancellations, ENT types

### Output
- Daily visit predictions for the next 7-30 days (based on input range)
- Forecast summary (increasing, decreasing, or stable trend)
- Actionable suggestions for resource planning

---

## Forecasting Algorithm

### Step 1: Data Preparation

The system extracts historical visit data grouped by date:

```
Values = [v₁, v₂, v₃, ..., vₙ]  where vᵢ = visits on day i
Labels = [d₁, d₂, d₃, ..., dₙ]  where dᵢ = date string (YYYY-MM-DD)
n = total number of days in selected range
```

### Step 2: Lookback Window Selection

The system determines how much recent history to analyze for trend estimation:

$$\text{lookback} = \min(28, \max(1, n))$$

This ensures:
- **Minimum:** At least 1 day of data is used
- **Maximum:** At most 28 days (prevents old patterns from skewing forecasts)
- For large date ranges (>28 days), only the most recent 28 days drive the trend

**Example:** If user selects "All Time" (500 days), only the last 28 days determine the forecasting trend.

### Step 3: Global Mean Calculation

$$\text{globalMean} = \frac{\sum_{i=1}^{n} v_i}{n}$$

This is the average number of visits across the entire selected period and serves as the baseline for seasonality adjustments.

### Step 4: Weekday Seasonality Model

The system captures **day-of-week patterns** (e.g., busier Mondays, quieter Sundays):

For each weekday (w = 0 to 6, where 0 = Sunday, 6 = Saturday):

$$\text{weekdaySums}[w] = \sum_{i: \text{dow}(d_i)=w} v_i$$

$$\text{weekdayCounts}[w] = \text{count of occurrences of weekday } w$$

$$\text{weekdayMean}[w] = \begin{cases} 
\frac{\text{weekdaySums}[w]}{\text{weekdayCounts}[w]} & \text{if } \text{weekdayCounts}[w] > 0 \\
\text{globalMean} & \text{otherwise}
\end{cases}$$

**Example Interpretation:**
- Monday mean = 25 visits, Global mean = 20 → Mondays are +25% busier
- Saturday mean = 15 visits, Global mean = 20 → Saturdays are -25% quieter

### Step 5: Data Smoothing (Moving Average)

Raw daily visit counts are noisy. The system applies a **simple moving average** to reduce noise:

$$w = \min(7, \max(1, \lfloor \text{lookback} / 4 \rfloor))$$

This determines the smoothing window size (1 to 7 days).

For each day i:

$$\text{smoothed}[i] = \frac{1}{m} \sum_{j=start}^{end} v_j$$

where:
- $start = \max(0, i - \lfloor w/2 \rfloor)$
- $end = \min(n-1, i + \lfloor w/2 \rfloor)$  
- $m = end - start + 1$ (number of points in the window)

**Effect:** Smoothing reduces day-to-day noise while preserving the underlying trend.

### Step 6: Trend Estimation (Ordinary Least Squares)

The system uses **OLS linear regression** to detect if visits are increasing, decreasing, or stable:

$$len = \min(30, \max(2, \lfloor n / 2 \rfloor))$$

Only the last `len` days of smoothed data are used for regression.

**OLS Regression Model:**
$$y = a + b \cdot x$$

where:
- $x$ = day index within the regression window (0, 1, 2, ...)
- $y$ = smoothed visit count
- $a$ = intercept (not used in forecast)
- $b$ = **slope** (trend direction and magnitude)

**Slope Calculation:**

$$\sum_x = \sum_{i=start}^{n-1} i \quad \text{(sum of x values)}$$

$$\sum_y = \sum_{i=start}^{n-1} y_i \quad \text{(sum of smoothed values)}$$

$$\sum_{xx} = \sum_{i=start}^{n-1} i^2$$

$$\sum_{xy} = \sum_{i=start}^{n-1} i \cdot y_i$$

$$\text{count} = n - start$$

$$\text{denominator} = \text{count} \cdot \sum_{xx} - (\sum_x)^2$$

$$\text{slope} = \begin{cases}
\frac{\text{count} \cdot \sum_{xy} - \sum_x \cdot \sum_y}{\text{denominator}} & \text{if } |\text{denominator}| > 10^{-9} \\
0 & \text{otherwise}
\end{cases}$$

**Slope Interpretation:**
- Slope = +2 → 2 more visits expected per day
- Slope = -1 → 1 fewer visits expected per day
- Slope ≈ 0 → Relatively stable demand

**Slope Clamping (Safety Mechanism):**

To prevent unrealistic runaway forecasts, the slope is bounded:

$$\text{maxSlopePerDay} = \max(1, 0.5 \times \text{globalMean})$$

$$\text{slope} = \begin{cases}
\text{maxSlopePerDay} & \text{if } \text{slope} > \text{maxSlopePerDay} \\
-\text{maxSlopePerDay} & \text{if } \text{slope} < -\text{maxSlopePerDay} \\
\text{slope} & \text{otherwise}
\end{cases}$$

**Example:** If globalMean = 20 visits/day, max slope = 10 visits/day (prevents forecast from claiming 100+ visits in a few days).

### Step 7: Forecast Prediction

For each future day $i$ (where $i = 1, 2, ..., \text{forecastDays}$):

**Step 1:** Calculate the future date:
$$\text{future}_\text{date} = \text{endDate} + i \text{ days}$$

**Step 2:** Get weekday of future date:
$$wd = \text{dayOfWeek}(\text{future}_\text{date}) \in \{0, 1, 2, 3, 4, 5, 6\}$$

**Step 3:** Calculate seasonality adjustment:
$$\text{seasonAdj} = \text{weekdayMean}[wd] - \text{globalMean}$$

**Step 4:** Compute prediction:
$$\text{prediction}[i] = \text{lastLevel} + \text{slope} \times i + \text{seasonAdj}$$

where:
- $\text{lastLevel}$ = most recent smoothed value (or raw value if insufficient data)
- $\text{slope}$ = trend magnitude per day
- $\text{seasonAdj}$ = weekday offset from global mean

**Step 5:** Ensure non-negative:
$$\text{forecast}[i] = \max(0, \text{round}(\text{prediction}[i]))$$

---

## Forecast Range Determination

The system selects the forecast horizon based on the selected date range:

$$\text{rangeDays} = \text{endDate} - \text{startDate} + 1$$

$$\text{forecastDays} = \begin{cases}
30 & \text{if } \text{rangeDays} = \text{all time OR } > 30 \\
7 & \text{if } \text{rangeDays} \leq 7 \text{ (Today/This Week)} \\
30 & \text{if } \text{rangeDays} \leq 30 \text{ (This Month)} \\
\end{cases}$$

---

## Fallback Mechanism (Low Data)

If insufficient data exists (< 3 days), the system **falls back to simple averaging**:

For each future day $i$:

$$\text{forecast}[i] = \max(0, \text{round}(\text{avgLast}))$$

where:
$$\text{avgLast} = \frac{\sum_{j=\max(0,n-\text{lookback})}^{n-1} v_j}{\text{lookback}}$$

This ensures reasonable predictions even with minimal historical data.

---

## Forecast Summary Generation

The system compares the **first half** and **second half** of the forecast period:

$$n_\text{forecast} = \text{length of forecast array}$$

$$\text{first}_\text{half} = \frac{1}{k_1} \sum_{i=0}^{k_1-1} \text{forecast}[i]$$

$$\text{last}_\text{half} = \frac{1}{k_2} \sum_{i=k_1}^{n_\text{forecast}-1} \text{forecast}[i]$$

where:
- $k_1 = \lfloor n_\text{forecast} / 2 \rfloor$
- $k_2 = \lceil n_\text{forecast} / 2 \rceil$

**Percentage Change:**

$$\text{pct} = \begin{cases}
\frac{\text{last}_\text{half} - \text{first}_\text{half}}{\text{first}_\text{half}} \times 100 & \text{if } \text{first}_\text{half} > 0 \\
(\text{last}_\text{half} - \text{first}_\text{half}) \times 100 & \text{otherwise}
\end{cases}$$

**Summary Classification:**

$$\text{summary} = \begin{cases}
\text{"Forecast indicates a likely increase..."} & \text{if } \text{pct} > 5\% \\
\text{"Forecast indicates a likely decrease..."} & \text{if } \text{pct} < -5\% \\
\text{"Forecast is relatively stable..."} & \text{otherwise}
\end{cases}$$

---

## ENT Type Classification (Inference System)

The system automatically categorizes visits into ENT specialties:

### Categories
1. **Ears**
2. **Nose**
3. **Throat**
4. **Head & Neck Tumors** (inferred from keywords)
5. **Lifestyle Medicine** (inferred from keywords)
6. **Misc / Others**

### Inference Patterns

**Head & Neck Pattern:**
```regex
(head|neck|lump|mass|tumor|tumour|swelling|thyroid|tonsil)
```

**Lifestyle Medicine Pattern:**
```regex
(lifestyle|diet|smoking|exercise|obesity|weight|alcohol|sedentary|dietary)
```

The system searches `chief_complaint`, `diagnosis`, and `notes` fields using two methods:

1. **SQL-based (RLIKE):** For large datasets
2. **PHP-based (preg_match):** Fallback for small samples or missing data

If a visit has a blank `ent_type`, the text patterns are applied to intelligently classify it.

---

## Average Visits Per Day

$$\text{avgVisits} = \begin{cases}
\text{round}(\frac{\sum_{i=1}^{n} v_i}{\text{rangeDays}}, 2) & \text{if } \text{rangeDays} > 0 \\
0 & \text{otherwise}
\end{cases}$$

---

## Clinical Suggestions

The system generates actionable suggestions based on forecast metrics:

1. **High Volume Alert** (max forecast > 40 visits):
   - "Increase available clinicians on peak days"

2. **Moderate Load** (avg last 28 days > 25):
   - "Consider adding follow-up slots for coming week"

3. **Cancellation Review** (cancellations > 0):
   - "Review cancellation reasons and confirm appointment reminders"

---

## Performance Characteristics

| Metric | Value |
|--------|-------|
| **Historical lookback (max)** | 28 days |
| **Forecast horizon (max)** | 30 days |
| **Smoothing window** | 1–7 days |
| **OLS regression window** | 2–30 days |
| **Slope clamp** | ±50% of global mean per day |
| **Cache TTL** | 30 seconds |

---

## Error Handling & Fallbacks

The system includes **three layers of fallback**:

### Layer 1: SQL Inference Fallback
If RLIKE pattern matching fails, switch to PHP-side `preg_match`.

### Layer 2: Data Availability Fallback  
If some metrics are unavailable (e.g., no cancellation_reason column), return "Unknown" bucket instead of failing.

### Layer 3: Database Error Fallback
If the entire database is unavailable:
- Return deterministic pseudo-random data based on CRC32 hash
- Prevents completely broken analytics UI
- Allows users to navigate while database is recovering

**Fallback Formula:**
$$\text{value} = 10 + (\text{CRC32}(\text{dateRange}) \mod 23)$$

---

## Caching

The API response is cached with an **ETag** mechanism:

$$\text{ETag} = \text{SHA1}(\text{JSON}(\text{payload}))$$

- **Cache Duration:** 30 seconds
- **Client Revalidation:** If client sends matching ETag, server returns `304 Not Modified`
- **Benefit:** Reduces database load for rapid repeated requests

---

## Example Forecast Calculation

**Scenario:**
- Date range: 2025-01-01 to 2025-02-28 (59 days)
- Selected: "This Month" → forecastDays = 30
- Historical data:
  - Total visits: 590 → globalMean = 10 visits/day
  - Last 28 days trend: increasing from 8 → 12 visits/day
  - Slope (OLS): +0.15 visits/day
  - Monday mean: 12, Sunday mean: 6

**Forecast for Day 1 (March 1, a Friday):**

1. Friday weekday index = 5
2. Seasonality: weekdayMean[5] = 10 → seasonAdj = 10 - 10 = 0
3. lastLevel = 12 (smoothed value from 2025-02-28)
4. Prediction = 12 + (0.15 × 1) + 0 = 12.15 → **rounds to 12 visits**

**Forecast for Day 8 (March 8, a Friday):**

1. Friday weekday index = 5
2. Seasonality: seasonAdj = 0
3. Prediction = 12 + (0.15 × 8) + 0 = 13.2 → **rounds to 13 visits**

**Forecast for Day 2 (March 2, a Saturday):**

1. Saturday weekday index = 6
2. Seasonality: weekdayMean[6] = 8 → seasonAdj = 8 - 10 = -2
3. Prediction = 12 + (0.15 × 2) - 2 = 10.3 → **rounds to 10 visits**

---

## Code Location

- **Main Implementation:** [public/api/AnalyticsController.php](public/api/AnalyticsController.php)
- **Forecasting Logic:** Lines 181–520
- **Seasonality Calculation:** Lines 405–420
- **OLS Regression:** Lines 425–445
- **Forecast Loop:** Lines 447–466

---

## Monitoring & Debugging

### Debug Logs
Enable debugging with query parameter: `?debug=1`

**Log Files:**
- `logs/analytics_debug.log` – Detailed algorithm traces
- `logs/api_exception.log` – Database errors and exceptions

### Sample Debug Output
```
[Analytics API Request] time=2025-03-18T10:30:00+00:00 start=2025-02-01 end=2025-03-18
[Analytics parsed dates] sdt=2025-02-01 10:30:00 edt=2025-03-18 10:30:00
[entMapComputed] array('head_neck_tumor' => 15, 'lifestyle_medicine' => 8, 'ears' => 42, ...)
[inferred_head_neck] matched=3 for range=2025-02-01:2025-03-18
[Analytics API Response] summary=array('total_patients' => 150, 'appointments_completed' => 89, ...)
```

---

## Future Enhancements

- [ ] ARIMA (AutoRegressive Integrated Moving Average) for non-stationary data
- [ ] Support for external regressors (holidays, events)
- [ ] Prophet library integration for multi-seasonality (weekly + yearly)
- [ ] Confidence intervals (80%, 95%) for each forecast point
- [ ] Anomaly detection for unusual days
- [ ] A/B testing on forecast accuracy
