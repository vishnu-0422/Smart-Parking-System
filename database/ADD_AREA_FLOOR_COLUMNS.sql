USE smart_parking;

-- Add columns for area and floor
ALTER TABLE slots
    ADD COLUMN area ENUM('open','closed') NOT NULL DEFAULT 'closed',
    ADD COLUMN floor VARCHAR(10) NULL;

-- Populate area/floor based on slot_number prefixes
UPDATE slots SET area='closed', floor='1' WHERE slot_number LIKE 'A-%' OR slot_number LIKE 'B-%' OR slot_number LIKE 'C-%' OR slot_number LIKE 'V-%' OR slot_number LIKE 'T-%' OR slot_number LIKE 'EV-%';
UPDATE slots SET area='closed', floor='2' WHERE slot_number LIKE 'M-%';

-- Any others treat as open
UPDATE slots SET area='open', floor=NULL WHERE slot_number NOT LIKE 'A-%' AND slot_number NOT LIKE 'B-%' AND slot_number NOT LIKE 'C-%' AND slot_number NOT LIKE 'V-%' AND slot_number NOT LIKE 'T-%' AND slot_number NOT LIKE 'EV-%' AND slot_number NOT LIKE 'M-%';

-- Verify
SELECT COUNT(*) as total_slots, SUM(area='open') as open_count, SUM(area='closed') as closed_count FROM slots;
SELECT slot_number, area, floor FROM slots ORDER BY slot_number LIMIT 20;