# Manual Test Cases for Break Mode Functionality

## Pre-conditions:
- Ensure `userstart.html` is loaded in a web browser.
- Ensure JavaScript is enabled.
- It's helpful to have the browser's developer console open to observe any errors and to manually inspect element states if needed.

## Test Case 1: Initial State Verification
1.  **Action:** Load `userstart.html`.
2.  **Expected Outcome:**
    *   The 'Hello message' (e.g., "Hey, [User Name]") is visible.
    *   The 'Start' work button is visible.
    *   The 'Break' button is visible next to the 'Start' work button.
    *   The main 'Stop' work button's visibility depends on `ajaxcall()` (it might be hidden if no work is active, or visible if work is active - this is existing functionality).
    *   The timer display ('00:00') is hidden.
    *   The 'Play', 'Pause', and 'Stop Break' buttons are hidden.

## Test Case 2: Entering Break Mode
1.  **Action:** Click the 'Break' button.
2.  **Expected Outcome:**
    *   The 'Hello message' becomes hidden.
    *   The 'Start' work button becomes hidden.
    *   The 'Break' button (the one you just clicked) becomes hidden.
    *   The main 'Stop' work button becomes hidden.
    *   The timer display becomes visible, showing '00:00'.
    *   The 'Play' button becomes visible and is enabled.
    *   The 'Pause' button becomes visible and is disabled.
    *   The 'Stop Break' button becomes visible.

## Test Case 3: Timer Functionality - Play and Pause
1.  **Action (starting from state after Test Case 2):** Click the 'Play' button.
2.  **Expected Outcome:**
    *   The timer display starts incrementing every second (e.g., 00:01, 00:02, ...).
    *   The 'Play' button becomes disabled.
    *   The 'Pause' button becomes enabled.
3.  **Action:** Wait for the timer to reach '00:05' (or any few seconds). Click the 'Pause' button.
4.  **Expected Outcome:**
    *   The timer display stops incrementing and holds the current time (e.g., '00:05').
    *   The 'Play' button becomes enabled.
    *   The 'Pause' button becomes disabled.
5.  **Action:** Click the 'Play' button again.
6.  **Expected Outcome:**
    *   The timer display resumes incrementing from where it left off (e.g., from '00:05' to '00:06', ...).
    *   The 'Play' button becomes disabled.
    *   The 'Pause' button becomes enabled.

## Test Case 4: Exiting Break Mode
1.  **Action (starting from state after Test Case 3, timer can be running or paused):** Click the 'Stop Break' button.
2.  **Expected Outcome:**
    *   The 'Hello message' becomes visible again.
    *   The timer display becomes hidden.
    *   The 'Play', 'Pause', and 'Stop Break' buttons become hidden.
    *   The 'Start' work button becomes visible.
    *   The 'Break' button becomes visible again.
    *   The main 'Stop' work button's visibility is correctly restored by `ajaxcall()` based on whether a work session was active before the break. (This requires knowing the state prior to break or observing `ajaxcall`'s behavior).
    *   The break timer value should be reset internally (next time break mode is entered, it starts from 00:00).

## Test Case 5: Interaction with Main Work Flow (Conceptual)
1.  **Scenario A: No active work session.**
    *   **Action:** Start with no work session active (main 'Start' work button is visible).
    *   **Action:** Click 'Break', let timer run, then click 'Stop Break'.
    *   **Expected Outcome:** Page returns to initial state, main 'Start' work button is visible, main 'Stop' work button is hidden.
2.  **Scenario B: Active work session.**
    *   **Action:** Click main 'Start' work button (assuming geolocation allows and it starts). Main 'Stop' work button should become visible.
    *   **Action:** Click 'Break', let timer run, then click 'Stop Break'.
    *   **Expected Outcome:** Page returns, 'Hello message', 'Start' work (hidden), 'Break' (visible) buttons are in their correct post-break state. Crucially, the main 'Stop' work button should be visible (restored by `ajaxcall` because work was active).
    *   **Action:** Click the main 'Stop' work button.
    *   **Expected Outcome:** Work session stops as per existing functionality.
