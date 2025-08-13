-- Migration: Create payments table for admin panel
CREATE TABLE IF NOT EXISTS public.payments (
    id SERIAL PRIMARY KEY,
    site_id UUID NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    utr_number VARCHAR(50),
    screenshot_url TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    -- Add other fields as needed
    CONSTRAINT fk_site_user FOREIGN KEY (site_id) REFERENCES auth.users(id)
);

-- Optional: Add index for created_at for faster sorting
CREATE INDEX IF NOT EXISTS idx_payments_created_at ON public.payments(created_at);
