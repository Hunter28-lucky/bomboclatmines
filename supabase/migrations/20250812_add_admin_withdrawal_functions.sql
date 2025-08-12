-- Drop existing function if it exists
DROP FUNCTION IF EXISTS public.get_all_withdrawals();

-- Create admin_users table if it doesn't exist
CREATE TABLE IF NOT EXISTS public.admin_users (
    user_id UUID PRIMARY KEY REFERENCES auth.users(id),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert the admin user
INSERT INTO public.admin_users (user_id)
SELECT id FROM auth.users WHERE email = 'krrishyogi18@gmail.com'
ON CONFLICT (user_id) DO NOTHING;

-- Enable RLS on admin_users
ALTER TABLE public.admin_users ENABLE ROW LEVEL SECURITY;

-- Only allow admins to view the admin_users table
CREATE POLICY "Only admins can view admin_users"
    ON public.admin_users
    FOR SELECT
    USING (auth.uid() IN (SELECT admin_users.user_id FROM public.admin_users));

-- Function for admins to get all withdrawals with user details
CREATE OR REPLACE FUNCTION public.get_all_withdrawals()
RETURNS TABLE (
    id UUID,
    user_id UUID,
    email TEXT,
    full_name TEXT,
    amount DECIMAL,
    upi_id TEXT,
    status TEXT,
    created_at TIMESTAMPTZ,
    processed_at TIMESTAMPTZ,
    processed_by UUID,
    notes TEXT
)
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
BEGIN
    -- Verify admin status
    IF NOT EXISTS (
        SELECT 1 FROM public.admin_users a
        WHERE a.user_id = auth.uid()
    ) THEN
        RAISE EXCEPTION 'Unauthorized';
    END IF;

    RETURN QUERY
    SELECT 
        w.id,
        w.user_id,
        u.email,
        u.raw_user_meta_data->>'full_name' as full_name,
        w.amount,
        w.upi_id,
        w.status,
        w.created_at,
        w.processed_at,
        w.processed_by,
        w.notes
    FROM public.withdrawals w
    LEFT JOIN auth.users u ON u.id = w.user_id
    ORDER BY w.created_at DESC;
END;
$$;

-- Grant execute permission to authenticated users (the function's SECURITY DEFINER will handle admin check)
GRANT EXECUTE ON FUNCTION public.get_all_withdrawals TO authenticated;
