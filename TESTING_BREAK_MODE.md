# Manual Test Cases for Persistent Break Mode Functionality

## Pre-conditions:
- Ensure `userstart.html` is loaded in a web browser.
- Ensure JavaScript is enabled and `API.php` is operational with the new database columns and stored procedures.
- It's helpful to have the browser's developer console open to observe API calls, errors, and to manually inspect element states.
- For some tests, you might need a way to check the `werkuren` table in the database to confirm values are saved correctly (e.g., `current_break_start_timestamp`, `current_break_accumulated_seconds_before_pause`, `final_break_total_seconds`).

## Test Case 1: Initial State Verification
1.  **Action:** Load `userstart.html`. (Assume no active work session and no prior break state from a previous unfinished session).
2.  **Expected Outcome:**
    *   The 'Hello message' is visible.
    *   The 'Start' work button is visible.
    *   The 'Break' button is visible.
    *   The main 'Stop' work button is hidden.
    *   The timer display, 'Play', 'Pause', and 'Exit Break Screen' (formerly 'Stop Break') buttons are hidden.
    *   *JS Console Check (optional):* `serverBreakStartTime` should be null, `clientSideCalculatedAccumulatedSeconds` should be 0.

## Test Case 2: Starting a New Break
1.  **Action:** Click the 'Break' button.
2.  **Expected Outcome (UI):**
    *   'Hello message', 'Start' work, and main 'Break' button become hidden. Main 'Stop' work button (if visible) also hides.
    *   Timer display becomes visible, showing '00:00'.
    *   'Play' button is visible and DISABLED.
    *   'Pause' button is visible and ENABLED.
    *   'Exit Break Screen' button is visible.
    *   The timer starts incrementing automatically.
3.  **Expected Outcome (Console/Network):**
    *   An API call to `API.php` with `action: 'start_break'` is made. `client_timestamp_ms` is sent.
    *   Server should respond with success.
    *   *DB Check (Conceptual):* For the active `pk_werkuren`, `current_break_start_timestamp` should be set to the sent timestamp, `current_break_accumulated_seconds_before_pause` should be 0.

## Test Case 3: Pausing a Break
1.  **Action (starting from Test Case 2, let timer run to ~00:05):** Click the 'Pause' button.
2.  **Expected Outcome (UI):**
    *   Timer display stops at the current time (e.g., '00:05').
    *   'Play' button becomes ENABLED.
    *   'Pause' button becomes DISABLED.
3.  **Expected Outcome (Console/Network):**
    *   An API call to `API.php` with `action: 'pause_break'` is made. `accumulated_seconds` (e.g., 5) is sent.
    *   Server should respond with success.
    *   *DB Check (Conceptual):* `current_break_start_timestamp` should be NULL. `current_break_accumulated_seconds_before_pause` should be updated (e.g., to 5).

## Test Case 4: Resuming a Break (Play)
1.  **Action (starting from Test Case 3):** Click the 'Play' button.
2.  **Expected Outcome (UI):**
    *   Timer display resumes incrementing from the paused time (e.g., from '00:05' upwards).
    *   'Play' button becomes DISABLED.
    *   'Pause' button becomes ENABLED.
3.  **Expected Outcome (Console/Network):**
    *   An API call to `API.php` with `action: 'resume_break'` is made. `client_timestamp_ms` is sent.
    *   Server should respond with success.
    *   *DB Check (Conceptual):* `current_break_start_timestamp` should be set to the new resume timestamp. `current_break_accumulated_seconds_before_pause` remains as it was (e.g., 5).

## Test Case 5: Exiting Break Screen (Timer Running)
1.  **Action (starting from Test Case 4, let timer run to ~00:10):** Click 'Exit Break Screen' button.
2.  **Expected Outcome (UI):**
    *   Break UI (timer, Play/Pause, Exit Break Screen buttons) becomes hidden.
    *   Main UI ('Hello message', 'Start' work, 'Break' button) becomes visible. (State of Start/Stop work buttons depends on `ajaxcall` refresh).
    *   The break timer is NOT reset or logically stopped.
3.  **Expected Outcome (Console/Network):**
    *   No API call is made for this action.
    *   *DB Check (Conceptual):* `current_break_start_timestamp` should still be set (break is running). `current_break_accumulated_seconds_before_pause` unchanged (e.g., 5).

## Test Case 6: Re-entering Break Screen (Timer Was Running)
1.  **Action (starting from Test Case 5):** Click the main 'Break' button again.
2.  **Expected Outcome (UI):**
    *   Break UI becomes visible.
    *   Timer display shows a value GREATER than 00:10 (it has been running in the background based on server start time).
    *   'Play' button is DISABLED.
    *   'Pause' button is ENABLED.
    *   Timer continues to increment.
3.  **Expected Outcome (Console/Network):**
    *   An API call to `API.php` with `action: 'start_break'` is made. (This re-initializes a "fresh" break from the client's UI perspective, but the page load logic should have already synced the *actual* current break time if the page were reloaded. This test assumes same session, same page, user clicked "Exit Break Screen" then "Break" again. The plan has 'Break' button resetting client `currentBreakAccumulatedSecondsBeforePause` to 0 and calling `start_break` API. This means a *new* break period starts. The old one is implicitly discarded or should have been finalized if that was the flow).
    *   *Correction for this test case based on implemented JS for 'Break' button:* Clicking main 'Break' button *always* starts a *new* break segment from 00:00 visually, sending `start_break` to API. The previous running segment is effectively abandoned *by this client action*. The server state for `current_break_start_timestamp` will be updated. This is simpler than trying to differentiate.
    *   **Revised Expected Outcome for 6.2 & 6.3:** Timer displays '00:00' and starts incrementing. API call `start_break` is made.

## Test Case 7: Page Reload (Timer Was Running)
1.  **Action (Setup):**
    *   Click main 'Break' button. Timer starts.
    *   Let timer run to ~00:03.
    *   **Reload the page (`userstart.html`)**.
2.  **Expected Outcome (After Reload):**
    *   Main UI is shown. Break UI is hidden.
    *   *JS Console Check (optional):* `ajaxcall()` on load fetches `get_start_info`. `serverBreakStartTime` should be the timestamp from before reload. `clientSideCalculatedAccumulatedSeconds` should be near 0. `breakCurrentTimeInSeconds` should be calculated to be > 3 seconds. `isBreakTimerRunning` (logical) true.
3.  **Action:** Click the main 'Break' button.
4.  **Expected Outcome (UI):**
    *   Break UI shown. Timer display shows a value reflecting the time elapsed since the break *originally* started (e.g., if 15 seconds passed during reload and interaction, it might show ~00:18).
    *   'Play' button DISABLED, 'Pause' button ENABLED. Timer is running.
    *   *(This case depends on `ajaxcall()` correctly initializing `breakCurrentTimeInSeconds` and `toggleBreakMode(true)` using this value when displaying the break UI)*

## Test Case 8: Page Reload (Timer Was Paused)
1.  **Action (Setup):**
    *   Click main 'Break' button. Timer starts.
    *   Let timer run to ~00:05. Click 'Pause' button.
    *   **Reload the page (`userstart.html`)**.
2.  **Expected Outcome (After Reload):**
    *   Main UI is shown.
    *   *JS Console Check (optional):* `ajaxcall()` on load. `serverBreakStartTime` should be NULL. `clientSideCalculatedAccumulatedSeconds` should be ~5. `breakCurrentTimeInSeconds` should be ~5. `isBreakTimerRunning` (logical) false.
3.  **Action:** Click the main 'Break' button.
4.  **Expected Outcome (UI):**
    *   Break UI shown. Timer display shows the paused time (e.g., '00:05').
    *   'Play' button ENABLED, 'Pause' button DISABLED. Timer is NOT running.

## Test Case 9: Stopping Work (Timer Was Running)
1.  **Action (Setup):**
    *   Ensure a work session is active (e.g., click main 'Start' button if not already started).
    *   Click main 'Break' button. Timer starts. Let it run to ~00:10.
2.  **Action:** Click the main 'Stop Work' button.
3.  **Expected Outcome (Console/Network):**
    *   AJAX call to `API.php` with `action: 'stop_werkuur'`.
    *   `total_final_break_seconds` sent in the POST data should be approximately 10 (plus any prior accumulated time if the 'Break' button was clicked multiple times in complex ways, but per current logic, it's ~10 for this test).
    *   Server responds with success.
4.  **Expected Outcome (UI):**
    *   Main UI reflects work stopped (e.g., 'Start' button visible).
    *   *DB Check (Conceptual):* For the `pk_werkuren` of the session just stopped: `final_break_total_seconds` should be ~10. `current_break_start_timestamp` should be NULL, `current_break_accumulated_seconds_before_pause` should be 0.

## Test Case 10: Stopping Work (Timer Was Paused)
1.  **Action (Setup):**
    *   Ensure work session active.
    *   Click main 'Break' button. Timer starts. Let run to ~00:07. Click 'Pause'.
2.  **Action:** Click the main 'Stop Work' button.
3.  **Expected Outcome (Console/Network):**
    *   AJAX call to `action: 'stop_werkuur'`.
    *   `total_final_break_seconds` sent should be ~7.
4.  **Expected Outcome (UI):**
    *   Main UI reflects work stopped.
    *   *DB Check (Conceptual):* `final_break_total_seconds` should be ~7. `current_break_start_timestamp` NULL, `current_break_accumulated_seconds_before_pause` 0.

## Test Case 11: Complex Interaction
1. Start work.
2. Click 'Break'. Timer runs (e.g., to 00:05). Click 'Pause'. (Accumulated: 5s)
3. Click 'Play'. Timer resumes (e.g., to 00:08). Click 'Pause'. (Accumulated: 8s)
4. Click 'Exit Break Screen'.
5. Reload page.
6. Click 'Break' button.
    * Expected: Timer shows ~00:08 (value from when it was last paused, fetched from server). Play is enabled, Pause disabled.
7. Click 'Play'. Timer resumes. Let run to ~00:12.
8. Click 'Stop Work'.
    * Expected: `total_final_break_seconds` sent to server should be ~12.
    * DB: `final_break_total_seconds` ~12.
