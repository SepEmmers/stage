-- SQL Changes for Break Timer Functionality

-- 1. Modify 'werkuren' table
-- Ensure you have a backup before running these ALTER statements.

ALTER TABLE werkuren
ADD COLUMN current_break_start_timestamp DATETIME NULL DEFAULT NULL COMMENT 'Timestamp when the current break segment started or resumed (UTC recommended)',
ADD COLUMN current_break_accumulated_seconds_before_pause INT NOT NULL DEFAULT 0 COMMENT 'Accumulated seconds for the current break segment before it was last paused',
ADD COLUMN final_break_total_seconds INT NOT NULL DEFAULT 0 COMMENT 'Total seconds of all breaks for this work session, finalized on work stop';

-- Note: Adjust DATETIME/TIMESTAMP type according to your specific SQL dialect.
-- For example, PostgreSQL uses TIMESTAMP WITH TIME ZONE or TIMESTAMP WITHOUT TIME ZONE.
-- MySQL uses DATETIME or TIMESTAMP.

-- 2. Stored Procedure Skeletons
-- Adjust syntax for your specific SQL dialect (e.g., MySQL, PostgreSQL, SQL Server).

-- Procedure to set/reset the start timestamp for a break segment
-- (Called when a break starts or resumes)
-- For MySQL:
DELIMITER //
CREATE PROCEDURE SP_SetBreakStartTimestamp(
    IN IN_pk_werkuren INT,  -- Assuming pk_werkuren is INT
    IN IN_timestamp DATETIME
)
BEGIN
    UPDATE werkuren
    SET current_break_start_timestamp = IN_timestamp
    WHERE pk_werkuren = IN_pk_werkuren;
END //
DELIMITER ;

/*
-- For PostgreSQL:
CREATE OR REPLACE FUNCTION SP_SetBreakStartTimestamp(
    IN_pk_werkuren INT,
    IN_timestamp TIMESTAMP
) RETURNS VOID AS $$
BEGIN
    UPDATE werkuren
    SET current_break_start_timestamp = IN_timestamp
    WHERE pk_werkuren = IN_pk_werkuren;
END;
$$ LANGUAGE plpgsql;
*/

-- Procedure to handle pausing a break segment
-- (Called when a break is paused)
-- For MySQL:
DELIMITER //
CREATE PROCEDURE SP_PauseBreak(
    IN IN_pk_werkuren INT,
    IN IN_accumulated_seconds INT
)
BEGIN
    UPDATE werkuren
    SET 
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = IN_accumulated_seconds
    WHERE pk_werkuren = IN_pk_werkuren;
END //
DELIMITER ;

/*
-- For PostgreSQL:
CREATE OR REPLACE FUNCTION SP_PauseBreak(
    IN_pk_werkuren INT,
    IN_accumulated_seconds INT
) RETURNS VOID AS $$
BEGIN
    UPDATE werkuren
    SET 
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = IN_accumulated_seconds
    WHERE pk_werkuren = IN_pk_werkuren;
END;
$$ LANGUAGE plpgsql;
*/

-- Procedure to finalize break times when a work session ends
-- (Called when the main 'Stop Work' action occurs)
-- This might be integrated into your existing stored procedure for stopping a work session.
-- For MySQL:
DELIMITER //
CREATE PROCEDURE SP_FinalizeWorkSessionBreaks(
    IN IN_pk_werkuren INT,
    IN IN_total_final_break_seconds INT
)
BEGIN
    UPDATE werkuren
    SET 
        final_break_total_seconds = IN_total_final_break_seconds,
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = 0
    WHERE pk_werkuren = IN_pk_werkuren;
    -- Add other logic for stopping a work session here if this SP handles it all,
    -- or call this SP from your main work session stopping SP.
END //
DELIMITER ;

/*
-- For PostgreSQL:
CREATE OR REPLACE FUNCTION SP_FinalizeWorkSessionBreaks(
    IN_pk_werkuren INT,
    IN_total_final_break_seconds INT
) RETURNS VOID AS $$
BEGIN
    UPDATE werkuren
    SET 
        final_break_total_seconds = IN_total_final_break_seconds,
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = 0
    WHERE pk_werkuren = IN_pk_werkuren;
END;
$$ LANGUAGE plpgsql;
*/

-- Reminder:
-- - `SP_ResumeBreak` is functionally the same as `SP_SetBreakStartTimestamp` as per the plan.
-- - Ensure the data types (INT, DATETIME/TIMESTAMP) match your `werkuren` table's `pk_werkuren` column and your timestamping preferences.
-- - Test these procedures thoroughly in your database environment.
