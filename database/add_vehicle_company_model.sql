-- Add company and model columns to vehicles table for enhanced security and vehicle tracking
-- Run this migration to update your database

ALTER TABLE vehicles ADD COLUMN company VARCHAR(100) DEFAULT NULL AFTER vehicle_type;
ALTER TABLE vehicles ADD COLUMN model VARCHAR(100) DEFAULT NULL AFTER company;
ALTER TABLE vehicles ADD COLUMN is_custom_vehicle BOOLEAN DEFAULT 0 AFTER model;

-- Create an index on company and model for faster lookups
CREATE INDEX idx_vehicle_company_model ON vehicles(company, model);
CREATE INDEX idx_custom_vehicle ON vehicles(is_custom_vehicle);
