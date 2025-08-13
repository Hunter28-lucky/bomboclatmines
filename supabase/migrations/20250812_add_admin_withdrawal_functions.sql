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

-- Drop existing policy if it exists
DROP POLICY IF EXISTS "Only admins can view admin_users" ON public.admin_users;

-- Only allow admins to view the admin_users table
CREATE POLICY "Only admins can view admin_users"
    ON public.admin_users
    FOR SELECT
    USING (auth.uid() IN (SELECT admin_users.user_id FROM public.admin_users));

-- Function for admins to get all withdrawals with user details
CREATE OR REPLACE FUNCTION public.get_all_withdrawals()
RETURNS SETOF JSONB
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
DECLARE
    v_withdrawals JSONB;
BEGIN
    -- Verify admin status
    IF NOT EXISTS (
        SELECT 1 FROM public.admin_users a
        WHERE a.user_id = auth.uid()
    ) THEN
        RAISE EXCEPTION 'Unauthorized';
    END IF;

    WITH withdrawal_data AS (
        SELECT 
            w.id,
            w.user_id,
            u.email,
            COALESCE(u.raw_user_meta_data->>'full_name', '') as full_name,
            w.amount::text,
            w.mobile_number,
            w.upi_id,
            w.status,
            w.admin_note,
            w.created_at,
            w.processed_at
        FROM public.withdrawals w
        LEFT JOIN auth.users u ON u.id = w.user_id
        ORDER BY w.created_at DESC
    )
    SELECT jsonb_agg(jsonb_build_object(
            'id', wd.id,
            'user_id', wd.user_id,
            'email', wd.email,
            'full_name', wd.full_name,
            'amount', wd.amount,
            'mobile_number', wd.mobile_number,
            'upi_id', wd.upi_id,
            'status', wd.status,
            'admin_note', wd.admin_note,
            'created_at', wd.created_at,
            'requested_at', wd.created_at,
            'processed_at', wd.processed_at
        ))
        INTO v_withdrawals
        FROM withdrawal_data wd;

    -- Handle case where there are no withdrawals
    IF v_withdrawals IS NULL THEN
        RETURN NEXT '[]'::jsonb;
    ELSE
        RETURN NEXT v_withdrawals;
    END IF;
END;
$$;

-- Grant execute permission to authenticated users (the function's SECURITY DEFINER will handle admin check)
GRANT EXECUTE ON FUNCTION public.get_all_withdrawals TO authenticated;
